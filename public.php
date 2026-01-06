<?php

declare(strict_types=1);

use App\Service\TemplateRenderer;
use Ubnt\UcrmPluginSdk\Security\PermissionNames;
use Ubnt\UcrmPluginSdk\Service\UcrmApi;
use Ubnt\UcrmPluginSdk\Service\UcrmOptionsManager;
use Ubnt\UcrmPluginSdk\Service\UcrmSecurity;

chdir(__DIR__);

require __DIR__ . '/vendor/autoload.php';

// Retrieve API connection.
$api = UcrmApi::create();

// Ensure that user is logged in and has permission to view payments.
$security = UcrmSecurity::create();
$user = $security->getUser();
if (
    ! $user
    || $user->isClient
    || ! $user->hasViewPermission(PermissionNames::BILLING_PAYMENTS)
    || ! $user->hasViewPermission(PermissionNames::CLIENTS_CLIENTS)
) {
    \App\Http::forbidden();
}

// Retrieve renderer.
$renderer = new TemplateRenderer();

// Load plugin options
$optionsManager = UcrmOptionsManager::create();
$ucrmOptions = $optionsManager->loadOptions();

// Load plugin configuration from data/config.json
$pluginConfig = [];
$configPath = __DIR__ . '/data/config.json';
if (file_exists($configPath)) {
    $configContent = file_get_contents($configPath);
    if ($configContent) {
        $pluginConfig = json_decode($configContent, true) ?? [];
    }
}
$currency = $pluginConfig['currency'] ?? 'PHP';

// Function to get expenses from Expense Manager plugin
function getExpenses(string $customPath = ''): array {
    // If custom path is provided and valid, use it
    if ($customPath && $customPath !== 'auto' && file_exists($customPath)) {
        $content = file_get_contents($customPath);
        if ($content) {
            $expenses = json_decode($content, true);
            if (is_array($expenses)) {
                return $expenses;
            }
        }
    }
    
    // Try multiple paths for expense data (auto-detection)
    // UISP stores plugins in /data/ucrm/data/plugins/[plugin-name]/
    $expensePaths = [
        // Standard UISP paths
        '/data/ucrm/data/plugins/expense-manager/data/expenses.json',
        '/data/ucrm/data/plugins/Expense Manager V 1.0.0/data/expenses.json',
        // Alternative paths with different naming
        dirname(__DIR__) . '/expense-manager/data/expenses.json',
        dirname(__DIR__) . '/Expense Manager V 1.0.0/data/expenses.json',
        // Home directory paths
        '/home/unms/data/ucrm/ucrm/data/plugins/expense-manager/data/expenses.json',
        '/home/unms/data/ucrm/ucrm/data/plugins/Expense Manager V 1.0.0/data/expenses.json',
        // Relative paths
        __DIR__ . '/../expense-manager/data/expenses.json',
        __DIR__ . '/../Expense Manager V 1.0.0/data/expenses.json',
    ];
    
    foreach ($expensePaths as $path) {
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if ($content) {
                $expenses = json_decode($content, true);
                if (is_array($expenses)) {
                    return $expenses;
                }
            }
        }
    }
    
    return [];
}

// Function to group expenses by month
function groupExpensesByMonth(array $expenses): array {
    $monthlyExpenses = [];
    
    foreach ($expenses as $expense) {
        $expenseDate = new DateTimeImmutable($expense['date']);
        $monthYear = $expenseDate->format('Y-m');
        
        if (!isset($monthlyExpenses[$monthYear])) {
            $monthlyExpenses[$monthYear] = [
                'month' => $expenseDate->format('F Y'),
                'total' => 0,
                'count' => 0,
                'categories' => [],
                'payment_methods' => [],
                'month_key' => $monthYear,
            ];
        }
        
        $amount = floatval($expense['amount'] ?? 0);
        $monthlyExpenses[$monthYear]['total'] += $amount;
        $monthlyExpenses[$monthYear]['count']++;
        
        // Track by category
        $category = $expense['category'] ?? 'General';
        if (!isset($monthlyExpenses[$monthYear]['categories'][$category])) {
            $monthlyExpenses[$monthYear]['categories'][$category] = [
                'total' => 0,
                'count' => 0,
            ];
        }
        $monthlyExpenses[$monthYear]['categories'][$category]['total'] += $amount;
        $monthlyExpenses[$monthYear]['categories'][$category]['count']++;
        
        // Track by payment method
        $paymentMethod = $expense['payment_method'] ?? 'Unknown';
        if (!isset($monthlyExpenses[$monthYear]['payment_methods'][$paymentMethod])) {
            $monthlyExpenses[$monthYear]['payment_methods'][$paymentMethod] = [
                'total' => 0,
                'count' => 0,
            ];
        }
        $monthlyExpenses[$monthYear]['payment_methods'][$paymentMethod]['total'] += $amount;
        $monthlyExpenses[$monthYear]['payment_methods'][$paymentMethod]['count']++;
    }
    
    return $monthlyExpenses;
}

$result = null;

// Process submitted form.
if (
    array_key_exists('since', $_GET)
    && is_string($_GET['since'])
    && array_key_exists('until', $_GET)
    && is_string($_GET['until'])
) {
    $trimNonEmpty = static function (string $value): ?string {
        $value = trim($value);
        return $value === '' ? null : $value;
    };

    $parameters = [
        'createdDateFrom' => $trimNonEmpty((string) $_GET['since']),
        'createdDateTo' => $trimNonEmpty((string) $_GET['until']),
        'status' => [1, 2, 3],
    ];
    $parameters = array_filter($parameters);

    // Make sure the dates are in YYYY-MM-DD format
    if (($parameters['createdDateFrom'] ?? null) !== null) {
        $parameters['createdDateFrom'] = new \DateTimeImmutable($parameters['createdDateFrom']);
        $parameters['createdDateFrom'] = $parameters['createdDateFrom']->format('Y-m-d');
    }
    if (($parameters['createdDateTo'] ?? null) !== null) {
        $parameters['createdDateTo'] = new \DateTimeImmutable($parameters['createdDateTo']);
        $parameters['createdDateTo'] = $parameters['createdDateTo']->format('Y-m-d');
    }

    // Fetch payment methods for name mapping
    $paymentMethods = [];
    try {
        $methods = $api->get('payment-methods');
        foreach ($methods as $method) {
            $paymentMethods[$method['id']] = $method['name'];
        }
    } catch (Exception $e) {
        error_log('Error fetching payment methods: ' . $e->getMessage());
    }

    // ==================== FETCH PAYMENTS ====================
    $monthlyPayments = [];
    $page = 1;
    $limit = 1000;
    $hasMore = true;

    while ($hasMore) {
        try {
            $payments = $api->get('payments', [
                'createdDateFrom' => $parameters['createdDateFrom'] ?? null,
                'createdDateTo' => $parameters['createdDateTo'] ?? null,
                'limit' => $limit,
                'offset' => ($page - 1) * $limit,
                'order' => 'createdDate',
            ]);

            if (empty($payments)) {
                $hasMore = false;
                break;
            }

            foreach ($payments as $payment) {
                $paymentDate = new DateTimeImmutable($payment['createdDate']);
                $monthYear = $paymentDate->format('Y-m');

                if (!isset($monthlyPayments[$monthYear])) {
                    $monthlyPayments[$monthYear] = [
                        'month' => $paymentDate->format('F Y'),
                        'total' => 0,
                        'count' => 0,
                        'methods' => [],
                        'month_key' => $monthYear,
                    ];
                }

                $monthlyPayments[$monthYear]['total'] += $payment['amount'];
                $monthlyPayments[$monthYear]['count']++;

                $methodId = $payment['methodId'] ?? null;
                $methodName = $paymentMethods[$methodId] ?? 'Unknown';
                if (!isset($monthlyPayments[$monthYear]['methods'][$methodName])) {
                    $monthlyPayments[$monthYear]['methods'][$methodName] = [
                        'total' => 0,
                        'count' => 0,
                    ];
                }
                $monthlyPayments[$monthYear]['methods'][$methodName]['total'] += $payment['amount'];
                $monthlyPayments[$monthYear]['methods'][$methodName]['count']++;
            }

            if (count($payments) < $limit) {
                $hasMore = false;
            } else {
                $page++;
                usleep(20000);
            }
        } catch (Exception $e) {
            error_log('Error fetching payments: ' . $e->getMessage());
            $hasMore = false;
        }
    }

    // ==================== FETCH EXPENSES ====================
    $expenseDataPath = $pluginConfig['expense_data_path'] ?? 'auto';
    $allExpenses = getExpenses($expenseDataPath);
    
    // Filter expenses by date range
    $filteredExpenses = array_filter($allExpenses, function($expense) use ($parameters) {
        $expenseDate = $expense['date'] ?? null;
        if (!$expenseDate) return false;
        
        if (isset($parameters['createdDateFrom']) && $expenseDate < $parameters['createdDateFrom']) {
            return false;
        }
        if (isset($parameters['createdDateTo']) && $expenseDate > $parameters['createdDateTo']) {
            return false;
        }
        return true;
    });
    
    $monthlyExpenses = groupExpensesByMonth($filteredExpenses);

    // ==================== COMBINE DATA ====================
    // Get all unique months from both payments and expenses
    $allMonths = array_unique(array_merge(
        array_keys($monthlyPayments),
        array_keys($monthlyExpenses)
    ));
    sort($allMonths);

    $combinedMonthly = [];
    foreach ($allMonths as $monthYear) {
        $paymentData = $monthlyPayments[$monthYear] ?? null;
        $expenseData = $monthlyExpenses[$monthYear] ?? null;
        
        $paymentTotal = $paymentData['total'] ?? 0;
        $expenseTotal = $expenseData['total'] ?? 0;
        $netTotal = $paymentTotal - $expenseTotal;
        
        // Determine month display name
        if ($paymentData) {
            $monthDisplay = $paymentData['month'];
        } elseif ($expenseData) {
            $monthDisplay = $expenseData['month'];
        } else {
            $date = new DateTimeImmutable($monthYear . '-01');
            $monthDisplay = $date->format('F Y');
        }
        
        $combinedMonthly[$monthYear] = [
            'month_key' => $monthYear,
            'month' => $monthDisplay,
            'payment_total' => $paymentTotal,
            'payment_count' => $paymentData['count'] ?? 0,
            'payment_methods' => $paymentData['methods'] ?? [],
            'expense_total' => $expenseTotal,
            'expense_count' => $expenseData['count'] ?? 0,
            'expense_categories' => $expenseData['categories'] ?? [],
            'expense_payment_methods' => $expenseData['payment_methods'] ?? [],
            'net_total' => $netTotal,
        ];
    }

    ksort($combinedMonthly);

    $result = [
        'monthlyData' => array_values($combinedMonthly),
        'currency' => $currency,
        'since' => $parameters['createdDateFrom'] ?? null,
        'until' => $parameters['createdDateTo'] ?? null,
    ];
}

// Render form.
$renderer->render(
    __DIR__ . '/templates/form.php',
    [
        'ucrmPublicUrl' => $ucrmOptions->ucrmPublicUrl,
        'result' => $result ?? [],
        'currency' => $currency,
    ]
);

<?php
// Calculate grand totals
$grandPaymentTotal = 0;
$grandExpenseTotal = 0;
$grandNetTotal = 0;
$totalPaymentCount = 0;
$totalExpenseCount = 0;
$monthCount = count($result['monthlyData']);

foreach ($result['monthlyData'] as $monthData) {
    $grandPaymentTotal += $monthData['payment_total'];
    $grandExpenseTotal += $monthData['expense_total'];
    $grandNetTotal += $monthData['net_total'];
    $totalPaymentCount += $monthData['payment_count'];
    $totalExpenseCount += $monthData['expense_count'];
}

$profitMargin = $grandPaymentTotal > 0 ? (($grandPaymentTotal - $grandExpenseTotal) / $grandPaymentTotal) * 100 : 0;
?>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="card summary-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff;">
            <div class="card-body">
                <div class="card-title" style="color: #fff;"><i class="fas fa-arrow-up mr-1"></i> Total Payments</div>
                <div class="card-value" style="color: #fff;"><?php echo htmlspecialchars($result['currency']); ?> <?php echo number_format($grandPaymentTotal, 2); ?></div>
                <small style="color: rgba(255,255,255,0.9);"><?php echo number_format($totalPaymentCount); ?> transactions</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="card summary-card" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: #fff;">
            <div class="card-body">
                <div class="card-title" style="color: #fff;"><i class="fas fa-arrow-down mr-1"></i> Total Expenses</div>
                <div class="card-value" style="color: #fff;"><?php echo htmlspecialchars($result['currency']); ?> <?php echo number_format($grandExpenseTotal, 2); ?></div>
                <small style="color: rgba(255,255,255,0.9);"><?php echo number_format($totalExpenseCount); ?> entries</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="card summary-card" style="background: linear-gradient(135deg, <?php echo $grandNetTotal >= 0 ? '#4facfe' : '#6c757d'; ?> 0%, <?php echo $grandNetTotal >= 0 ? '#00f2fe' : '#495057'; ?> 100%); color: #fff;">
            <div class="card-body">
                <div class="card-title" style="color: #fff;"><i class="fas fa-calculator mr-1"></i> Net Total</div>
                <div class="card-value" style="color: #fff;"><?php echo htmlspecialchars($result['currency']); ?> <?php echo number_format($grandNetTotal, 2); ?></div>
                <small style="color: rgba(255,255,255,0.9);">Payment - Expense</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="card summary-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff;">
            <div class="card-body">
                <div class="card-title" style="color: #fff;"><i class="fas fa-percentage mr-1"></i> Profit Margin</div>
                <div class="card-value" style="color: #fff;">
                    <?php echo $grandPaymentTotal > 0 ? number_format($profitMargin, 1) . '%' : 'N/A'; ?>
                </div>
                <small style="color: rgba(255,255,255,0.9);">(Net / Payment) × 100</small>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Data Table -->
<div class="card main-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="mb-0"><i class="fas fa-table mr-2"></i>Monthly Payment vs Expense Report</h5>
        <span class="text-muted"><?php echo $monthCount; ?> month<?php echo $monthCount !== 1 ? 's' : ''; ?> of data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-center align-middle table-header-month">Month</th>
                        <th colspan="2" class="text-center table-header-payment">Payments</th>
                        <th colspan="2" class="text-center table-header-expense">Expenses</th>
                        <th rowspan="2" class="text-center align-middle table-header-net">Net Total</th>
                    </tr>
                    <tr>
                        <th class="text-right table-header-payment">Amount</th>
                        <th class="text-center table-header-payment">Count</th>
                        <th class="text-right table-header-expense">Amount</th>
                        <th class="text-center table-header-expense">Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($result['monthlyData'])): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                                <span class="text-muted">No data found for the selected date range</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($result['monthlyData'] as $monthData): ?>
                            <tr class="<?php echo $monthData['net_total'] >= 0 ? 'net-positive' : 'net-negative'; ?>">
                                <td class="text-center font-weight-bold"><?php echo htmlspecialchars($monthData['month']); ?></td>
                                <td class="text-right positive"><?php echo number_format($monthData['payment_total'], 2); ?></td>
                                <td class="text-center">
                                    <span class="badge badge-pill" style="background: #11998e; color: white; padding: 5px 12px;">
                                        <?php echo number_format($monthData['payment_count']); ?>
                                    </span>
                                </td>
                                <td class="text-right negative"><?php echo number_format($monthData['expense_total'], 2); ?></td>
                                <td class="text-center">
                                    <span class="badge badge-pill" style="background: #e74c3c; color: white; padding: 5px 12px;">
                                        <?php echo number_format($monthData['expense_count']); ?>
                                    </span>
                                </td>
                                <td class="text-right font-weight-bold <?php echo $monthData['net_total'] >= 0 ? 'positive' : 'negative'; ?>">
                                    <?php echo $monthData['net_total'] >= 0 ? '' : '-'; ?><?php echo number_format(abs($monthData['net_total']), 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($result['monthlyData'])): ?>
                <tfoot>
                    <tr>
                        <td class="text-center"><i class="fas fa-sigma mr-1"></i> GRAND TOTAL</td>
                        <td class="text-right" style="color: #90EE90; font-size: 1.1rem;"><?php echo number_format($grandPaymentTotal, 2); ?></td>
                        <td class="text-center"><?php echo number_format($totalPaymentCount); ?></td>
                        <td class="text-right" style="color: #FFB6C1; font-size: 1.1rem;"><?php echo number_format($grandExpenseTotal, 2); ?></td>
                        <td class="text-center"><?php echo number_format($totalExpenseCount); ?></td>
                        <td class="text-right" style="color: <?php echo $grandNetTotal >= 0 ? '#90EE90' : '#FFB6C1'; ?>; font-size: 1.1rem;">
                            <?php echo $grandNetTotal >= 0 ? '' : '-'; ?><?php echo number_format(abs($grandNetTotal), 2); ?>
                        </td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<!-- Detailed Breakdown (Collapsible) -->
<div class="card details-card">
    <div class="card-header">
        <h5 class="mb-0">
            <button class="btn btn-link p-0" type="button" data-toggle="collapse" data-target="#detailsCollapse" aria-expanded="false">
                <i class="fas fa-chevron-down mr-2"></i>Detailed Breakdown (Click to expand)
            </button>
        </h5>
    </div>
    <div id="detailsCollapse" class="collapse">
        <div class="card-body">
            <?php foreach ($result['monthlyData'] as $monthData): ?>
                <div class="row mb-4 pb-3" style="border-bottom: 1px solid #eee;">
                    <div class="col-12 mb-2">
                        <h6 class="mb-0" style="color: #667eea; font-weight: 700;">
                            <i class="fas fa-calendar-alt mr-2"></i><?php echo htmlspecialchars($monthData['month']); ?>
                        </h6>
                    </div>
                    <!-- Payment Methods (Income) -->
                    <div class="col-md-6 col-lg-3 mt-2">
                        <div class="p-3" style="background: rgba(17, 153, 142, 0.08); border-radius: 10px; height: 100%;">
                            <strong style="color: #11998e;"><i class="fas fa-wallet mr-1"></i> Payment Methods:</strong>
                            <?php if (!empty($monthData['payment_methods'])): ?>
                                <ul class="method-list mt-2">
                                    <?php foreach ($monthData['payment_methods'] as $methodName => $methodData): ?>
                                        <li>
                                            <span><?php echo htmlspecialchars($methodName); ?></span>
                                            <span style="color: #11998e; font-weight: 600;"><?php echo number_format($methodData['total'], 2); ?> <small class="text-muted">(<?php echo (int)$methodData['count']; ?>)</small></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted small mb-0 mt-2">No payments</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Expense Categories -->
                    <div class="col-md-6 col-lg-3 mt-2">
                        <div class="p-3" style="background: rgba(231, 76, 60, 0.08); border-radius: 10px; height: 100%;">
                            <strong style="color: #e74c3c;"><i class="fas fa-tags mr-1"></i> Expense Categories:</strong>
                            <?php if (!empty($monthData['expense_categories'])): ?>
                                <ul class="category-list mt-2">
                                    <?php foreach ($monthData['expense_categories'] as $catName => $catData): ?>
                                        <li>
                                            <span><?php echo htmlspecialchars($catName); ?></span>
                                            <span style="color: #e74c3c; font-weight: 600;"><?php echo number_format($catData['total'], 2); ?> <small class="text-muted">(<?php echo (int)$catData['count']; ?>)</small></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted small mb-0 mt-2">No expenses</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Expense Payment Methods -->
                    <div class="col-md-6 col-lg-3 mt-2">
                        <div class="p-3" style="background: rgba(255, 152, 0, 0.08); border-radius: 10px; height: 100%;">
                            <strong style="color: #f57c00;"><i class="fas fa-credit-card mr-1"></i> Expense Payment Methods:</strong>
                            <?php if (!empty($monthData['expense_payment_methods'])): ?>
                                <ul class="method-list mt-2">
                                    <?php foreach ($monthData['expense_payment_methods'] as $methodName => $methodData): ?>
                                        <li>
                                            <span><?php echo htmlspecialchars($methodName); ?></span>
                                            <span style="color: #f57c00; font-weight: 600;"><?php echo number_format($methodData['total'], 2); ?> <small class="text-muted">(<?php echo (int)$methodData['count']; ?>)</small></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted small mb-0 mt-2">No expense payments</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Summary for this month -->
                    <div class="col-md-6 col-lg-3 mt-2">
                        <div class="p-3" style="background: rgba(102, 126, 234, 0.08); border-radius: 10px; height: 100%;">
                            <strong style="color: #667eea;"><i class="fas fa-chart-pie mr-1"></i> Month Summary:</strong>
                            <ul class="method-list mt-2">
                                <li>
                                    <span>Payments</span>
                                    <span style="color: #11998e; font-weight: 600;"><?php echo number_format($monthData['payment_total'], 2); ?></span>
                                </li>
                                <li>
                                    <span>Expenses</span>
                                    <span style="color: #e74c3c; font-weight: 600;"><?php echo number_format($monthData['expense_total'], 2); ?></span>
                                </li>
                                <li style="border-top: 2px solid #667eea; padding-top: 8px; margin-top: 4px;">
                                    <span><strong>Net Total</strong></span>
                                    <span style="color: <?php echo $monthData['net_total'] >= 0 ? '#11998e' : '#e74c3c'; ?>; font-weight: 700;"><?php echo number_format($monthData['net_total'], 2); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="card chart-card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Monthly Comparison Chart</h5>
    </div>
    <div class="card-body">
        <div id="chart-comparison" style="height: 400px;"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Month');
        data.addColumn('number', 'Payments');
        data.addColumn('number', 'Expenses');
        data.addColumn('number', 'Net Total');

        data.addRows([
            <?php 
            foreach ($result['monthlyData'] as $monthData): 
                echo "['" . addslashes($monthData['month']) . "', " . 
                     $monthData['payment_total'] . ", " . 
                     $monthData['expense_total'] . ", " . 
                     $monthData['net_total'] . "],";
            endforeach; 
            ?>
        ]);

        var options = {
            chartArea: {width: '80%', height: '75%'},
            hAxis: {
                title: 'Month',
                textStyle: { fontSize: 11 },
                slantedText: true,
                slantedTextAngle: 45
            },
            vAxis: {
                title: 'Amount (<?php echo htmlspecialchars($result['currency']); ?>)',
                format: 'short',
                textStyle: { fontSize: 11 },
                gridlines: { color: '#f0f0f0' }
            },
            seriesType: 'bars',
            series: {
                0: { color: '#11998e' },
                1: { color: '#e74c3c' },
                2: { type: 'line', color: '#667eea', lineWidth: 3 }
            },
            legend: { position: 'top', maxLines: 1 },
            bar: { groupWidth: '70%' },
            animation: {
                startup: true,
                duration: 1000,
                easing: 'out'
            },
            tooltip: { isHtml: true }
        };

        var chart = new google.visualization.ComboChart(document.getElementById('chart-comparison'));
        chart.draw(data, options);
    }

    window.addEventListener('resize', function() {
        drawCharts();
    });

    // CSV Export functionality
    function exportToCSV() {
        var csvContent = "data:text/csv;charset=utf-8,";
        
        // Header row
        csvContent += "Month,Payment Amount,Payment Count,Expense Amount,Expense Count,Net Total\n";
        
        // Data rows
        <?php foreach ($result['monthlyData'] as $monthData): ?>
        csvContent += "<?php echo addslashes($monthData['month']); ?>,";
        csvContent += "<?php echo $monthData['payment_total']; ?>,";
        csvContent += "<?php echo $monthData['payment_count']; ?>,";
        csvContent += "<?php echo $monthData['expense_total']; ?>,";
        csvContent += "<?php echo $monthData['expense_count']; ?>,";
        csvContent += "<?php echo $monthData['net_total']; ?>\n";
        <?php endforeach; ?>
        
        // Totals row
        csvContent += "GRAND TOTAL,<?php echo $grandPaymentTotal; ?>,<?php echo $totalPaymentCount; ?>,<?php echo $grandExpenseTotal; ?>,<?php echo $totalExpenseCount; ?>,<?php echo $grandNetTotal; ?>\n";
        
        // Add blank line and breakdown header
        csvContent += "\n";
        csvContent += "DETAILED BREAKDOWN\n";
        csvContent += "\n";
        
        // Payment Methods breakdown
        csvContent += "PAYMENT METHODS BY MONTH\n";
        csvContent += "Month,Payment Method,Amount,Count\n";
        <?php foreach ($result['monthlyData'] as $monthData): ?>
            <?php if (!empty($monthData['payment_methods'])): ?>
                <?php foreach ($monthData['payment_methods'] as $methodName => $methodData): ?>
        csvContent += "<?php echo addslashes($monthData['month']); ?>,<?php echo addslashes($methodName); ?>,<?php echo $methodData['total']; ?>,<?php echo $methodData['count']; ?>\n";
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        
        csvContent += "\n";
        csvContent += "EXPENSE CATEGORIES BY MONTH\n";
        csvContent += "Month,Category,Amount,Count\n";
        <?php foreach ($result['monthlyData'] as $monthData): ?>
            <?php if (!empty($monthData['expense_categories'])): ?>
                <?php foreach ($monthData['expense_categories'] as $catName => $catData): ?>
        csvContent += "<?php echo addslashes($monthData['month']); ?>,<?php echo addslashes($catName); ?>,<?php echo $catData['total']; ?>,<?php echo $catData['count']; ?>\n";
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        
        csvContent += "\n";
        csvContent += "EXPENSE PAYMENT METHODS BY MONTH\n";
        csvContent += "Month,Payment Method,Amount,Count\n";
        <?php foreach ($result['monthlyData'] as $monthData): ?>
            <?php if (!empty($monthData['expense_payment_methods'])): ?>
                <?php foreach ($monthData['expense_payment_methods'] as $methodName => $methodData): ?>
        csvContent += "<?php echo addslashes($monthData['month']); ?>,<?php echo addslashes($methodName); ?>,<?php echo $methodData['total']; ?>,<?php echo $methodData['count']; ?>\n";
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        
        // Create download link
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "payment_vs_expense_report_<?php echo date('Y-m-d'); ?>.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment vs Expense Report</title>
    <link rel="stylesheet" href="<?php echo rtrim(htmlspecialchars($ucrmPublicUrl, ENT_QUOTES), '/'); ?>/assets/fonts/lato/lato.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Lato', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 25px;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        }
        .page-header h1 {
            font-weight: 700;
            margin: 0;
            font-size: 2rem;
        }
        .page-header p {
            opacity: 0.9;
            margin: 5px 0 0 0;
        }
        .filter-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        .filter-card .card-body {
            padding: 25px;
        }
        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn-generate {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }
        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-generate:disabled {
            background: linear-gradient(135deg, #95a5a6 0%, #bdc3c7 100%);
            transform: none;
            box-shadow: none;
        }
        .summary-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .summary-card .card-body {
            padding: 25px;
            text-align: center;
        }
        .summary-card .card-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        .summary-card .card-value {
            font-size: 1.8rem;
            font-weight: 700;
        }
        .summary-card small {
            opacity: 0.8;
        }
        .main-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 25px;
        }
        .main-card .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 20px 25px;
        }
        .main-card .card-header h5 {
            margin: 0;
            font-weight: 700;
            color: #333;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 15px;
            border: none;
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }
        .table tbody tr:hover {
            background-color: #f8f9ff;
        }
        .table tfoot {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
        }
        .table tfoot td {
            padding: 15px;
            font-weight: 700;
            border: none;
        }
        .table-header-payment { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important; color: white !important; }
        .table-header-expense { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%) !important; color: white !important; }
        .table-header-net { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; color: white !important; }
        .table-header-month { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; color: white !important; }
        .positive { color: #11998e; font-weight: bold; }
        .negative { color: #e74c3c; font-weight: bold; }
        .net-positive { background-color: rgba(17, 153, 142, 0.08); }
        .net-negative { background-color: rgba(231, 76, 60, 0.08); }
        .chart-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 25px;
        }
        .chart-card .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 20px 25px;
        }
        .chart-card .card-body {
            padding: 25px;
        }
        .details-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 25px;
        }
        .details-card .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 15px 25px;
        }
        .details-card .btn-link {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }
        .details-card .btn-link:hover {
            color: #764ba2;
        }
        .method-list, .category-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .method-list li, .category-list li {
            padding: 6px 0;
            font-size: 0.9rem;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #eee;
        }
        .method-list li:last-child, .category-list li:last-child {
            border-bottom: none;
        }
        .spinner-border {
            width: 1.2rem;
            height: 1.2rem;
        }
        @media (max-width: 768px) {
            .page-header { padding: 20px; }
            .page-header h1 { font-size: 1.5rem; }
            .summary-card .card-value { font-size: 1.4rem; }
            .filter-card .card-body { padding: 15px; }
            .table thead th, .table tbody td { padding: 10px; font-size: 0.85rem; }
        }
        @media (max-width: 576px) {
            body { padding: 10px; }
            .page-header { padding: 15px; border-radius: 12px; }
            .page-header h1 { font-size: 1.3rem; }
            .summary-card .card-value { font-size: 1.2rem; }
            .summary-card .card-body { padding: 15px; }
            .btn-generate { padding: 10px 20px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1><i class="fas fa-balance-scale mr-2"></i>Payment vs Expense Report</h1>
                    <p class="mb-0">Monthly view of Payments, Expenses, and Net Total</p>
                </div>
                <?php if (!empty($result['monthlyData'])): ?>
                <div class="mt-2 mt-md-0">
                    <button type="button" class="btn btn-light" onclick="exportToCSV()" style="border-radius: 10px; font-weight: 600;">
                        <i class="fas fa-download mr-2"></i>Export CSV
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card filter-card">
            <div class="card-body">
                <form id="report-form">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label" for="frm-since"><i class="fas fa-calendar-alt mr-1"></i> From Date</label>
                            <input type="date" name="since" id="frm-since" class="form-control" value="<?php echo htmlspecialchars($result['since'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label" for="frm-until"><i class="fas fa-calendar-alt mr-1"></i> To Date</label>
                            <input type="date" name="until" id="frm-until" class="form-control" value="<?php echo htmlspecialchars($result['until'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="col-md-4 text-md-right">
                            <button id="btn-submit" type="submit" class="btn btn-generate">
                                <i class="fas fa-sync-alt mr-2"></i>Generate Report
                            </button>
                            <button id="btn-loading" type="button" class="btn btn-generate d-none" disabled>
                                <span class="spinner-border spinner-border-sm mr-2" role="status"></span>Generating...
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php
            if ($result) {
                require_once(__DIR__ . '/result.php');
            }
        ?>
    </div>

    <script>
        document.querySelector("#report-form").addEventListener("submit", function(e){
            document.querySelector("#btn-submit").classList.add('d-none');
            document.querySelector("#btn-loading").classList.remove('d-none');
        });
    </script>
</body>
</html>

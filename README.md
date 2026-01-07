# Payment vs Expense Report Plugin

A UISP/UCRM plugin that generates monthly reports showing **Payments**, **Expenses**, and **Net Total (Payment - Expense)**.

## Features

- **Monthly View Table**: Displays payments, expenses, and net totals grouped by month
- **Summary Cards**: Quick overview of total payments, expenses, net total, and profit margin
- **Interactive Chart**: Visual comparison of payments vs expenses with a net total trend line
- **Detailed Breakdown**: Expandable section showing payment methods and expense categories per month
- **Date Range Filter**: Filter reports by custom date ranges
- **Color-Coded Results**: Green for positive net, red for negative net

## Requirements

- UISP/UCRM 2.14.0 or higher
- **Expense Manager Plugin** (V 1.0.0) installed for expense data
- PHP 7.4 or higher

## Installation

1. Download or clone this plugin folder
2. Run `composer install` in the plugin directory to install dependencies
3. Zip the plugin folder
4. Upload to UISP: **System > Plugins > Add Plugin**
5. Enable the plugin after upload

## Configuration

After installation, configure the plugin in **System > Plugins > Payment vs Expense**:

- **Currency Symbol**: Set your preferred currency (default: PHP)
- **Expense Data Path**: Leave empty for default path, or specify custom path to expense data

## Usage

1. Navigate to **Reports > Payment vs Expense**
2. Select a date range (Since/Until)
3. Click **Generate Report**
4. View the monthly comparison table with summary

## Data Sources

- **Payments**: Fetched from UCRM API (`/api/v1.0/payments`)
- **Expenses**: Read from Expense Manager plugin data file (`expenses.json`)

## Plugin Structure

```
Payment-Expense-Report-V1.0.0/
├── classes/
│   ├── Http.php
│   └── Service/
│       └── TemplateRenderer.php
├── data/
├── public/
│   └── main.css
├── templates/
│   ├── form.php
│   └── result.php
├── composer.json
├── hook_*.php
├── main.php
├── manifest.json
├── public.php
└── README.md
```

## Report Output

The generated report includes:

| Column | Description |
|--------|-------------|
| Month | Calendar month (e.g., "January 2024") |
| Payment Amount | Total payments received |
| Payment Count | Number of payment transactions |
| Expense Amount | Total expenses recorded |
| Expense Count | Number of expense entries |
| Net Total | Payment - Expense (positive = profit) |

## License

MIT License

## Author

**xingginto**

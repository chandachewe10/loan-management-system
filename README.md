### Loan Management Software.

## Requirements

- PHP >= 8.2

![sample1](https://github.com/user-attachments/assets/f57b4435-24a4-467d-9518-d8397b2e81ce)
![sample2](https://github.com/user-attachments/assets/84e6a945-e47b-49a9-a950-660ecba8ced5)
![sample3](https://github.com/user-attachments/assets/0e3bbcb2-d370-481d-b47b-e22b7c0addd1)
![sample4](https://github.com/user-attachments/assets/3bd6032a-a62e-4469-94ff-74a7bfc1058d)

## How it works

The Loan Management software is a web-based application that helps manage and track loans efficiently. It provides features to streamline the loan application process, automate loan approvals, and monitor loan repayments. This README file provides an overview of the software and instructions for installation, configuration, and usage.

## Features

1. Beautiful, User-friendly interface for managing loans, borrowers, and lenders
2. Add different types of loans, their percentage and interest cycle
3. Add different types of wallets accounts such as expense wallet including expense categories for business expenses and loan wallet for loans payouts.
4. Transfer funds from one wallet to the other e.g from loans wallet to the expense wallet
5. Show all transactions history
6. Loan application form with customizable fields to collect borrowers information and add borrower files.
7. Automated loan approval process with customizable criteria and decision workflows.
8. Loan repayment tracking and reminders for borrowers.
9. Detailed loan reports and analytics for monitoring loan portfolios.
10. Role-based access control to manage user permissions.
11. Integration with external systems such as payment gateways and credit scoring services.
12. Receive loan agreement forms via email once the loan is approved automatically and compile the loan settlement form once the loan is settled
13. View and Export Active Loans, Denied Loans, Pending Loans and Defaulted Loans
14. Update Payments for loans
15. Assign roles, assign permissions, revoke roles Etc
16. **Double-Entry Accounting** with Chart of Accounts, Journal Entries, General Ledger, Trial Balance, Statement of Financial Position, Statement of Comprehensive Income, and Cash Flow Statement
17. **Bank / Cash Account management** linked to the Chart of Accounts — every wallet transaction auto-posts balanced journal entries
18. **Granular permission control** via FilamentShield — every module including accounting reports is role/permission-gated

## Demo Credentials

[Website Demo Link](https://lendfy-development.macroit.org)

1. EMAIL: demo@demo.com
2. PASSWORD: qwertyuiop

## Installation (Local Development)

### Clone the repository

Please follow carefully the installation and use of this web framework of the Loan Management System for better utilization of it. Do not skip any stage.

```bash
git clone https://github.com/chandachewe10/loan-management-system.git
cd loan-management-system
composer install
cp .env.example .env        # Windows: copy .env.example .env
```

Set your database credentials and app settings in `.env`, then:

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed --class=ChartOfAccountsSeeder
```

Install Shield for roles and permissions:

```bash
php artisan shield:install
php artisan shield:generate --all
```

Create a super-admin user (follow the prompts):

```bash
php artisan shield:super-admin
```

Link existing wallets to Chart of Accounts (run once after seeding):

```bash
php artisan db:seed --class=LinkExistingWalletsSeeder
```

Start your Application:

```bash
php artisan serve
```

Set up email notifications using [Mailtrap](https://mailtrap.io). Copy the mail credentials for your Laravel app from Mailtrap and update the corresponding settings in your `.env` file.

---

## 🚀 Production Deployment

Run these commands **in order** every time you deploy to the production server:

```bash
# 1. Pull latest code
git pull origin main

# 2. Install / update PHP dependencies (no dev packages)
composer install --no-dev --optimize-autoloader

# 3. Run any new database migrations
php artisan migrate --force

# 4. Seed the Chart of Accounts (safe to run multiple times — skips existing records)
php artisan db:seed --class=ChartOfAccountsSeeder --force

# 5. Link any unlinked wallets to Chart of Accounts
php artisan db:seed --class=LinkExistingWalletsSeeder --force

# 6. Regenerate Shield permissions for any new pages/resources added
php artisan shield:generate --all

# 7. Seed custom page permissions (accounting reports not auto-detected by Shield)
php artisan db:seed --class=PagePermissionsSeeder --force

# 8. Clear and re-cache everything for performance
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 9. Reset the permission cache
php artisan permission:cache-reset
```

### First-time production setup only

Run these **once** when setting up a brand-new production server:

```bash
php artisan key:generate
php artisan storage:link
php artisan shield:install
php artisan shield:super-admin
```

### Queue Workers (if using queues)

If you run queues (e.g. for notifications/emails), restart the worker after every deployment:

```bash
php artisan queue:restart
```

> **Note:** Always back up your database before running `php artisan migrate --force` in production.

---

## Contributions

Contributions to the Loan Management software are welcome! If you have suggestions, bug reports, or feature requests, please submit an issue or a pull request on the GitHub repository.

## License

The Loan Management software source code is publicly available but is licensed under the Proprietary License Agreement attached in this repo. Modifications, enhancements and improvements are allowed, but redistribution and commercial use require written consent from MACROIT INFORMATION TECHNOLOGY

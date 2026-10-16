💰 Where’s My Money?

Where’s My Money? is a personal finance dashboard designed to answer one simple question:

Where did my money go?

The project brings accounts, transactions, bills, budgets, cash, crypto, investments, and financial reporting into one clear interface.

Instead of forcing users to piece together information from multiple banking apps, wallets, spreadsheets, and statements, Where’s My Money? is being built as a centralized view of an individual’s complete financial picture.

⸻

🚧 Project Status

Current Version: 0.1.0

Where’s My Money? is currently under active development.

The repository contains the application frontend, dashboard structure, financial views, responsive interface, and the foundation for the application’s backend financial system.

Some screens currently use development/demo data while live database and financial-account integrations are being completed.

This project should currently be considered development software, not production financial software.

⸻

✨ Core Features

📊 Financial Dashboard

The dashboard provides a high-level view of a user’s financial position, including:

* Total available cash
* Monthly income
* Monthly spending
* Upcoming bills
* Safe-to-spend calculations
* Net worth overview
* Recent transactions
* Spending by category

The goal is to make the most important financial information understandable immediately.

⸻

🏦 Accounts

Track financial accounts from a single interface.

Account support is being designed for:

* Checking accounts
* Savings accounts
* Credit cards
* Cash
* Digital wallets
* Loans
* Investment accounts
* Other manually tracked assets

Accounts can display information such as:

* Institution
* Account type
* Balance
* Available balance
* Account source
* Masked account number

⸻

💳 Transactions

The transaction system is designed to turn raw transaction data into something understandable.

Transactions can include:

* Merchant or transaction name
* Date
* Amount
* Account
* Category
* Transaction type
* Pending or posted status

Transaction types include:

* Income
* Expenses
* Transfers

A major goal of the project is improving raw banking descriptions so users can quickly understand who received their money and why.

⸻

💵 Cash Tracking

Physical cash is easy to lose track of because it exists outside normal banking transaction feeds.

Where’s My Money? is being designed to support manual cash accounts so users can track:

* Cash on hand
* Cash spending
* Cash deposits
* Cash withdrawals
* Manual cash transactions

This allows cash to remain part of the user’s complete financial picture.

⸻

🧾 Bills & Recurring Expenses

The bills section provides a centralized place to track recurring financial obligations.

Bills can include:

* Bill name
* Category
* Amount
* Due date
* Frequency
* Payment account
* Auto-pay status
* Payment status

Statuses can identify bills as:

* Paid
* Due soon
* Upcoming

The long-term goal is to help users see upcoming obligations before calculating what money is actually available to spend.

⸻

🎯 Budgets

Category-based budgeting helps users compare actual spending against planned spending.

Current budget views support concepts such as:

* Category
* Budget amount
* Amount spent
* Remaining amount
* Percentage used

Example categories include:

* Groceries
* Fuel
* Utilities
* Shopping
* Entertainment
* Other customizable spending groups

⸻

📈 Reports

The reporting system is designed to make historical financial activity easier to understand.

Reports can present:

* Monthly income
* Monthly spending
* Bill totals
* Net cash flow
* Savings rate
* Category breakdowns
* Spending percentages
* Historical comparisons

The focus is not simply storing transactions—it is turning those transactions into useful financial information.

⸻

₿ Crypto Tracking

Where’s My Money? includes a dedicated crypto portfolio interface.

The current design supports concepts such as:

* Asset symbol
* Asset name
* Quantity
* Market price
* Portfolio value
* Percentage change
* Exchange or wallet source
* Blockchain network

Potential sources include centralized exchanges and self-custody wallets.

⸻

📈 Investments & Net Worth

The long-term application design extends beyond checking-account spending.

Where’s My Money? is intended to provide a broader financial picture containing assets such as:

* Bank balances
* Cash
* Cryptocurrency
* Brokerage accounts
* Retirement accounts
* Other investments

These assets can eventually contribute toward a unified net worth calculation.

⸻

🔄 How It Works

The project follows four primary concepts:

1. Connect

Bring financial accounts or manually tracked assets into the application.

2. Normalize

Convert inconsistent transaction descriptions into cleaner, more useful financial records.

3. Understand

Categorize income, expenses, transfers, bills, and other financial activity.

4. See Everything

Present the user’s financial activity through dashboards, budgets, reports, and account views.

⸻

🔐 Security Philosophy

Financial software should have the smallest amount of financial authority necessary.

Where’s My Money? is being designed around a read-focused financial model.

The application is intended to:

* Read financial information
* Track balances
* Import transaction activity
* Analyze spending
* Display financial information

It is not intended to move money between bank accounts.

Security goals include:

* No money-moving permissions
* No plaintext bank-password storage
* Protected financial credentials and tokens
* Minimal financial API permissions
* Separation of application data and credentials
* Server-side validation
* Prepared database queries
* Secure session handling
* Principle-of-least-privilege access

⸻

🛠️ Technology

Where’s My Money? is primarily built with:

* PHP
* MySQL
* HTML5
* CSS3
* JavaScript
* PDO
* Git
* GitHub

The application uses a traditional server-rendered PHP architecture rather than depending on a large frontend framework.

This keeps the application relatively lightweight and makes the entire request lifecycle easy to inspect and control.

⸻

📁 Project Structure

WheresMyMoney/
│
├── assets/
│   └── Application CSS, JavaScript, and frontend assets
│
├── data/
│   └── Application data directory
│
├── includes/
│   ├── app-nav.php
│   ├── bootstrap.php
│   ├── footer.php
│   ├── functions.php
│   ├── header.php
│   └── public-nav.php
│
├── pages/
│   ├── app/
│   │   ├── accounts.php
│   │   ├── bills.php
│   │   ├── budgets.php
│   │   ├── crypto.php
│   │   ├── dashboard.php
│   │   ├── reports.php
│   │   ├── settings.php
│   │   └── transactions.php
│   │
│   └── home.php
│
├── accounts.php
├── bills.php
├── budgets.php
├── crypto.php
├── dashboard.php
├── index.php
├── login.php
├── register.php
├── reports.php
├── settings.php
├── transactions.php
└── README.md

⸻

⚙️ Development Setup

The project is currently being developed in a local PHP environment.

Requirements

Recommended development environment:

* PHP 8.x+
* MySQL 8.x+
* Apache or another PHP-capable web server
* PDO MySQL extension
* Git

A local environment such as XAMPP, WAMP, Laragon, or a manually configured Apache/PHP installation can be used.

⸻

Clone the Repository

git clone https://github.com/CraftEarth/WheresMyMoney.git

Move into the project:

cd WheresMyMoney

Place the project inside your local web root if required by your PHP installation.

Example:

D:\Website\htdocs\WheresMyMoney

The application can then be accessed through the configured local web server.

Example:

http://localhost/WheresMyMoney/

⸻

🗄️ Database

The active backend is being developed around MySQL using PHP PDO.

Financial records are being structured around entities such as:

* Users
* Financial institutions
* Accounts
* Transactions
* Categories
* Bills
* Budgets
* Account connections
* Synchronization state

Database credentials and third-party API secrets should never be committed to this repository.

⸻

🔌 Financial Account Integration

Automatic bank synchronization is part of the active development roadmap.

The integration architecture is intended to support securely importing:

* Financial institutions
* Accounts
* Balances
* Transactions
* Pending transactions
* Posted transactions
* Account synchronization state

External financial connections should remain read-only whenever possible.

Manual accounts will remain useful for financial information that cannot be obtained automatically, including physical cash and unsupported assets.

⸻

🧠 Transaction Intelligence

One of the central goals of Where’s My Money? is to make transactions easier to understand.

Future transaction processing is intended to support:

* Merchant normalization
* Category detection
* Transfer identification
* Recurring payment detection
* Duplicate prevention
* Pending-to-posted reconciliation
* Income recognition
* Spending analysis
* User-defined transaction corrections

The application should learn from structured financial data without hiding the original transaction information from the user.

⸻

📱 Responsive Design

Where’s My Money? is intended to work across:

* Desktop computers
* Laptops
* Tablets
* Mobile phones

The interface is being refined so financial information remains usable on smaller screens instead of simply shrinking a desktop layout.

⸻

🗺️ Roadmap

Major development areas include:

* [x]	Public landing page
* [x]	Application navigation
* [x]	Dashboard UI
* [x]	Account interface
* [x]	Transaction interface
* [x]	Bill interface
* [x]	Budget interface
* [x]	Report interface
* [x]	Crypto interface
* [x]	Settings interface
* [x]	Responsive frontend foundation
* [ ]	Complete authentication system
* [ ]	Complete MySQL persistence layer
* [ ]	Live financial account connections
* [ ]	Automatic transaction synchronization
* [ ]	Pending-to-posted transaction reconciliation
* [ ]	Manual account management
* [ ]	Manual cash tracking
* [ ]	Transaction categorization engine
* [ ]	Merchant normalization
* [ ]	Recurring bill detection
* [ ]	Automated budget calculations
* [ ]	Investment account integration
* [ ]	Live cryptocurrency pricing
* [ ]	Net worth history
* [ ]	Advanced reports
* [ ]	Data export
* [ ]	Security hardening
* [ ]	Production deployment

⸻

🎯 Project Philosophy

Personal finance applications often show users balances without answering the question they actually care about:

What happened to my money?

Where’s My Money? is being built around visibility rather than judgment.

The application should help a user understand:

* What came in
* What went out
* Where it went
* What bills are coming
* What remains available
* What they own
* What they owe
* How their financial position is changing over time

The goal is a financial dashboard that is understandable without requiring the user to become an accountant.

⸻

⚠️ Development Disclaimer

Where’s My Money? is currently under active development.

Do not use the development version as the sole source of truth for financial balances, bill payments, tax information, investment decisions, or other important financial decisions.

Financial information should be verified against the appropriate bank, financial institution, exchange, lender, or official statement.

⸻

👨‍💻 Developer

William Murphy — CraftEarth

GitHub: CraftEarth

Repository: CraftEarth/WheresMyMoney

⸻

📄 License

A formal project license has not yet been added.

Until a license is provided, all rights remain with the project author.

⸻

Where’s My Money?

Your money. One clear picture.

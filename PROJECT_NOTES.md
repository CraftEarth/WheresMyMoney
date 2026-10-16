# Where's My Money? — Project Notes

## Local Development

- Project: `D:\Website\htdocs\WheresMyMoney`
- URL: `http://localhost/WheresMyMoney/`
- Web root: `D:\Website\htdocs`
- PHP: `D:\Website\php\php.exe`
- XAMPP manages Apache/PHP.
- MySQL database: `wheres_my_money`
- Host: `127.0.0.1`
- Port: `3306`
- DB user: `root`
- Root-level PHP application; no `/public` directory.
- GitHub: `CraftEarth/WheresMyMoney`

## Development Rules

- Inspect actual current code and database schema before editing.
- Localhost/live MySQL wins if GitHub is behind.
- Ask questions before coding when behavior or data design is unclear.
- Design the page and workflow first.
- Then implement real database, PHP, validation, calculations, and UI front to back.
- No fake values, placeholder math, fake transactions, or guessed financial data.
- Unknown financial information must stay Unknown rather than becoming zero.
- Prefer full-page edits instead of repeatedly stacking small patches.
- Back up risky files before edits.
- Lint changed PHP files.
- Run `git diff --check` before committing.
- Prefer PowerShell blocks over manual editing.
- Write generated PHP as UTF-8 without BOM.
- Do not commit `plaid.local.php` or `alchemy.local.php`.
- Never store bank credentials, seed phrases, private keys, tokens, or passwords in Git.

## Account Colors

- Source of truth: `accounts.account_color`
- Each active account should have a unique color per user.
- Account colors should remain consistent across:
  - Accounts
  - Transactions
  - Dashboard
  - Reports/charts later
- Use a real color picker rather than a limited dropdown.
- Transactions remain chronological; color identifies the account visually.

## Crypto

Crypto must use real accounting data.

- Manual holdings already support average cost.
- Wallet-synced assets must never assume cost basis is zero.
- Unknown basis displays as `Unknown` or `Not Set`.
- Gain/loss is calculated only when basis is actually known.
- Current market value may come from live pricing.
- Cost basis must come from real acquisition data or verified transaction history.
- Do not manufacture basis from current price.

Future crypto/Web3 scope may include:

- wallet transaction history
- acquisition lots
- realized gains/losses
- NFTs
- DeFi
- LP positions
- cross-chain assets

## Cash

Cash eventually means actual cash accounts only.

Dashboard Cash should use active accounts where:

`account_type = 'cash'`

Checking, savings, Venmo, and credit accounts should not be counted as physical cash.

Known current state:

- Personal Cash = `cash`
- Vault currently = `retirement`

Do not silently change Vault. Resolve it intentionally during the Cash work.

## Current Focus

1. Account colors are being implemented.
2. Next: rebuild Crypto cost basis and gain/loss with real data.
3. Then: correct Cash semantics.
4. Bills currently appear acceptable unless live data shows otherwise.

## Principle

Get the design right first, then build the real feature once.

Avoid temporary business logic that looks finished.

Prefer one source of truth, real persistence, real calculations, real validation, and end-to-end testing.
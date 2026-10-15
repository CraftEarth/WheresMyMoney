<div class="app-layout">

    <main class="app-main">

        <header class="settings-header">

            <div>

                <p class="app-greeting">
                    Personal preferences
                </p>

                <h1>
                    Settings
                </h1>

                <p class="settings-subtitle">
                    Control how accounts, reports,
                    privacy, and financial totals behave.
                </p>

            </div>

        </header>


        <section class="settings-layout">

            <div class="settings-main">

                <article class="settings-card">

                    <div class="settings-card-heading">

                        <div>

                            <small>
                                Profile
                            </small>

                            <h2>
                                Personal Information
                            </h2>

                        </div>

                    </div>


                    <div class="settings-form-grid">

                        <label>

                            <span>
                                Display Name
                            </span>

                            <input
                                type="text"
                                value="Murph"
                            >

                        </label>


                        <label>

                            <span>
                                Email
                            </span>

                            <input
                                type="email"
                                value="dev@localhost"
                            >

                        </label>


                        <label>

                            <span>
                                Currency
                            </span>

                            <select>

                                <option selected>
                                    USD
                                </option>

                                <option>
                                    CAD
                                </option>

                                <option>
                                    EUR
                                </option>

                                <option>
                                    GBP
                                </option>

                            </select>

                        </label>


                        <label>

                            <span>
                                Starting Page
                            </span>

                            <select>

                                <option selected>
                                    Dashboard
                                </option>

                                <option>
                                    Accounts
                                </option>

                                <option>
                                    Transactions
                                </option>

                                <option>
                                    Reports
                                </option>

                            </select>

                        </label>

                    </div>

                </article>


                <article class="settings-card">

                    <div class="settings-card-heading">

                        <div>

                            <small>
                                Money calculations
                            </small>

                            <h2>
                                Financial Preferences
                            </h2>

                        </div>

                    </div>


                    <div class="settings-toggle-list">

                        <label class="settings-toggle-row">

                            <div>

                                <strong>
                                    Include crypto in net worth
                                </strong>

                                <small>
                                    Count tracked crypto holdings
                                    as part of total net worth.
                                </small>

                            </div>

                            <input
                                type="checkbox"
                                checked
                            >

                        </label>


                        <label class="settings-toggle-row">

                            <div>

                                <strong>
                                    Include investments in net worth
                                </strong>

                                <small>
                                    Count retirement and brokerage
                                    accounts toward net worth.
                                </small>

                            </div>

                            <input
                                type="checkbox"
                                checked
                            >

                        </label>


                        <label class="settings-toggle-row">

                            <div>

                                <strong>
                                    Include crypto in safe to spend
                                </strong>

                                <small>
                                    Normally disabled because crypto
                                    is not treated as spendable cash.
                                </small>

                            </div>

                            <input
                                type="checkbox"
                            >

                        </label>


                        <label class="settings-toggle-row">

                            <div>

                                <strong>
                                    Show habit spending
                                </strong>

                                <small>
                                    Display user-selected categories
                                    on dashboard and reports.
                                </small>

                            </div>

                            <input
                                type="checkbox"
                                checked
                            >

                        </label>

                    </div>

                </article>


                <article class="settings-card">

                    <div class="settings-card-heading">

                        <div>

                            <small>
                                Data
                            </small>

                            <h2>
                                Import & Connections
                            </h2>

                        </div>

                    </div>


                    <div class="settings-action-list">

                        <div>

                            <div>

                                <strong>
                                    Financial Connections
                                </strong>

                                <small>
                                    Manage linked banks, cards,
                                    exchanges, and investment accounts.
                                </small>

                            </div>

                            <button
                                class="secondary-button"
                                type="button"
                            >
                                Manage
                            </button>

                        </div>


                        <div>

                            <div>

                                <strong>
                                    CSV Imports
                                </strong>

                                <small>
                                    Import transactions from unsupported
                                    institutions or old statements.
                                </small>

                            </div>

                            <button
                                class="secondary-button"
                                type="button"
                            >
                                Import
                            </button>

                        </div>


                        <div>

                            <div>

                                <strong>
                                    Manual Accounts
                                </strong>

                                <small>
                                    Manage physical cash or accounts
                                    that do not support syncing.
                                </small>

                            </div>

                            <button
                                class="secondary-button"
                                type="button"
                            >
                                Manage
                            </button>

                        </div>

                    </div>

                </article>

            </div>


            <aside class="settings-side">

                <article class="settings-card">

                    <div class="settings-card-heading">

                        <div>

                            <small>
                                Security
                            </small>

                            <h2>
                                Privacy
                            </h2>

                        </div>

                    </div>


                    <div class="settings-info-list">

                        <div>

                            <strong>
                                Read-only finance access
                            </strong>

                            <p>
                                Linked financial services should
                                only provide balances and transaction
                                information.
                            </p>

                        </div>


                        <div>

                            <strong>
                                No bank passwords stored
                            </strong>

                            <p>
                                Institution authentication belongs
                                to the financial provider connection flow.
                            </p>

                        </div>


                        <div>

                            <strong>
                                No money movement
                            </strong>

                            <p>
                                This application tracks finances.
                                It does not send payments or withdrawals.
                            </p>

                        </div>

                    </div>

                </article>


                <article class="settings-card">

                    <div class="settings-card-heading">

                        <div>

                            <small>
                                Appearance
                            </small>

                            <h2>
                                Interface
                            </h2>

                        </div>

                    </div>


                    <label class="settings-toggle-row">

                        <div>

                            <strong>
                                Dark Mode
                            </strong>

                            <small>
                                Alternate application appearance.
                            </small>

                        </div>

                        <input
                            type="checkbox"
                        >

                    </label>

                </article>


                <button
                    class="settings-save-button"
                    type="button"
                >
                    Save Settings
                </button>

            </aside>

        </section>

    </main>

</div>

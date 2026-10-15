<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

require_login();

$pageTitle = 'Link Account';
$pageType = 'app';
$activePage = 'accounts';

require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/app-nav.php';
?>

<div class="app-layout">

    <main class="app-main">

        <header class="transactions-header">

            <div>

                <p class="app-greeting">
                    Secure account connection
                </p>

                <h1>
                    Link Financial Account
                </h1>

                <p class="transactions-subtitle">
                    Connect through Plaid without sharing
                    bank credentials with this app.
                </p>

            </div>

        </header>


        <section class="import-card">

            <div class="import-heading">

                <small>
                    Plaid Sandbox
                </small>

                <h2>
                    Connect an Institution
                </h2>

                <p>
                    This is currently using Plaid's test environment.
                </p>

            </div>


            <button
                type="button"
                class="primary-button"
                id="launch-plaid"
            >
                Open Plaid
            </button>


            <div
                id="plaid-status"
                style="margin-top: 16px;"
            ></div>

        </section>

    </main>

</div>


<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>

<script>
const launchButton =
    document.getElementById(
        'launch-plaid'
    );

const statusBox =
    document.getElementById(
        'plaid-status'
    );


async function getLinkToken() {
    const response = await fetch(
        <?= json_encode(
            url('plaid-link-token.php')
        ); ?>,
        {
            credentials: 'same-origin'
        }
    );

    const data = await response.json();

    if (!data.success) {
        throw new Error(
            data.error
            || 'Could not create Link token.'
        );
    }

    return data.link_token;
}


async function exchangePublicToken(
    publicToken
) {
    const response = await fetch(
        <?= json_encode(
            url('plaid-exchange-token.php')
        ); ?>,
        {
            method: 'POST',

            headers: {
                'Content-Type':
                    'application/json'
            },

            credentials: 'same-origin',

            body: JSON.stringify({
                public_token:
                    publicToken
            })
        }
    );

    const data = await response.json();

    if (!data.success) {
        throw new Error(
            data.error
            || 'Token exchange failed.'
        );
    }

    return data;
}


launchButton.addEventListener(
    'click',
    async () => {
        launchButton.disabled = true;

        statusBox.textContent =
            'Preparing secure connection...';

        try {
            const linkToken =
                await getLinkToken();

            const handler =
                Plaid.create({
                    token: linkToken,

                    onSuccess: async (
                        publicToken,
                        metadata
                    ) => {
                        statusBox.textContent =
                            'Finishing connection...';

                        try {
                            await exchangePublicToken(
                                publicToken
                            );

                            statusBox.textContent =
                                'Account linked successfully.';

                            window.location.href =
                                <?= json_encode(
                                    url(
                                        'plaid-accounts.php'
                                    )
                                ); ?>;
                        } catch (error) {
                            statusBox.textContent =
                                error.message;

                            launchButton.disabled =
                                false;
                        }
                    },

                    onExit: (
                        error,
                        metadata
                    ) => {
                        if (error) {
                            statusBox.textContent =
                                error.display_message
                                || error.error_message
                                || 'Plaid Link closed with an error.';
                        } else {
                            statusBox.textContent =
                                'Connection cancelled.';
                        }

                        launchButton.disabled =
                            false;
                    }
                });

            handler.open();
        } catch (error) {
            statusBox.textContent =
                error.message;

            launchButton.disabled =
                false;
        }
    }
);
</script>

<?php

require_once APP_ROOT . '/includes/footer.php';

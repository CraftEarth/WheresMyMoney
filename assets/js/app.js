"use strict";


/* BEGIN WMM DASHBOARD SVG ICONS */

document.addEventListener('DOMContentLoaded', function () {

    const dashboard = document.querySelector('.dashboard-v2');

    if (!dashboard) {
        return;
    }


    function icon(name) {

        const icons = {

            wallet: `
                <path d="M4 7h15a2 2 0 0 1 2 2v10H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h12"/>
                <path d="M16 11h5v5h-5a2.5 2.5 0 0 1 0-5Z"/>
            `,

            income: `
                <path d="M12 20V5"/>
                <path d="m6 11 6-6 6 6"/>
            `,

            expense: `
                <path d="M12 4v15"/>
                <path d="m18 13-6 6-6-6"/>
            `,

            calendar: `
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M16 3v4M8 3v4M3 10h18"/>
            `,

            shield: `
                <path d="M12 3 20 6v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/>
                <path d="m8.5 12 2.2 2.2 4.8-5"/>
            `,

            trend: `
                <path d="m4 17 5-5 4 3 7-8"/>
                <path d="M15 7h5v5"/>
            `,

            pie: `
                <path d="M11 3a9 9 0 1 0 9 9h-9z"/>
                <path d="M14 3.5V9h5.5A8 8 0 0 0 14 3.5Z"/>
            `,

            bitcoin: `
                <path d="M9 3v18M13 3v18"/>
                <path d="M7 6h7a3 3 0 0 1 0 6H7"/>
                <path d="M7 12h8a3 3 0 0 1 0 6H7"/>
            `,

            card: `
                <rect x="3" y="5" width="18" height="14" rx="2"/>
                <path d="M3 10h18"/>
                <path d="M7 15h4"/>
            `,

            receipt: `
                <path d="M6 3h12v18l-2-1.4L14 21l-2-1.4L10 21l-2-1.4L6 21z"/>
                <path d="M9 8h6M9 12h6M9 16h4"/>
            `,

            transfer: `
                <path d="M4 8h13"/>
                <path d="m14 5 3 3-3 3"/>
                <path d="M20 16H7"/>
                <path d="m10 13-3 3 3 3"/>
            `,

            person: `
                <circle cx="12" cy="8" r="3"/>
                <path d="M5 20c.7-4 3-6 7-6s6.3 2 7 6"/>
            `,

            fuel: `
                <path d="M5 21V4h10v17"/>
                <path d="M4 21h12"/>
                <path d="M8 8h4"/>
                <path d="M15 7h2l3 3v7a2 2 0 0 1-4 0v-4"/>
            `,

            chart: `
                <path d="M5 20V11"/>
                <path d="M12 20V5"/>
                <path d="M19 20v-8"/>
            `,

            spark: `
                <path d="m12 3 1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5z"/>
                <path d="m19 15 .8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z"/>
            `,

            sync: `
                <path d="M20 7v5h-5"/>
                <path d="M4 17v-5h5"/>
                <path d="M6.5 8a7 7 0 0 1 11.7-2L20 8"/>
                <path d="M17.5 16a7 7 0 0 1-11.7 2L4 16"/>
            `

        };

        return `
            <svg
                class="wm-svg-icon"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                ${icons[name] || ''}
            </svg>
        `;
    }


    function setIcon(selector, iconName) {

        const element =
            document.querySelector(selector);

        if (element) {
            element.innerHTML =
                icon(iconName);
        }
    }


    /*
     * Top summary cards.
     */

    setIcon(
        '[data-dashboard-metric="cash"] .wm-summary-icon',
        'wallet'
    );

    setIcon(
        '[data-dashboard-metric="income"] .wm-summary-icon',
        'income'
    );

    setIcon(
        '[data-dashboard-metric="spending"] .wm-summary-icon',
        'expense'
    );

    setIcon(
        '[data-dashboard-metric="bills"] .wm-summary-icon',
        'calendar'
    );

    setIcon(
        '[data-dashboard-metric="safe"] .wm-summary-icon',
        'shield'
    );

    setIcon(
        '[data-dashboard-metric="net-worth"] .wm-summary-icon',
        'trend'
    );


    /*
     * Money allocation.
     */

    setIcon(
        '.wm-allocation-cash .wm-allocation-icon',
        'wallet'
    );

    setIcon(
        '.wm-allocation-crypto .wm-allocation-icon',
        'bitcoin'
    );

    setIcon(
        '.wm-allocation-debt .wm-allocation-icon',
        'card'
    );

    setIcon(
        '.wm-allocation-bills .wm-allocation-icon',
        'receipt'
    );

    setIcon(
        '.wm-allocation-worth .wm-allocation-icon',
        'trend'
    );


    /*
     * Section heading helper.
     */

    function headingIcon(element, iconName) {

        if (!element) {
            return;
        }

        let label =
            element.textContent
                .replace(/^[?\s]+/, '')
                .trim();

        element.innerHTML =
            icon(iconName);

        const span =
            document.createElement('span');

        span.textContent =
            label;

        element.appendChild(span);
    }


    document
        .querySelectorAll(
            '.wm-section-heading h2, ' +
            '.wm-panel-heading h2, ' +
            '.wm-crypto-head h2'
        )
        .forEach(function (heading) {

            const text =
                heading.textContent
                    .replace(/^[?\s]+/, '')
                    .trim()
                    .toLowerCase();

            if (
                text.includes(
                    'money allocation'
                )
            ) {
                headingIcon(
                    heading,
                    'pie'
                );
            }

            else if (
                text.includes(
                    'upcoming bills'
                )
            ) {
                headingIcon(
                    heading,
                    'calendar'
                );
            }

            else if (
                text.includes(
                    'recent transactions'
                )
            ) {
                headingIcon(
                    heading,
                    'transfer'
                );
            }

            else if (
                text === 'gains'
            ) {
                headingIcon(
                    heading,
                    'chart'
                );
            }

            else if (
                text.includes(
                    'crypto snapshot'
                )
            ) {
                headingIcon(
                    heading,
                    'bitcoin'
                );
            }
        });


    /*
     * Bill rows.
     */

    document
        .querySelectorAll(
            '.wm-row-icon-warning'
        )
        .forEach(function (element) {
            element.innerHTML =
                icon('receipt');
        });


    /*
     * Recent transaction rows.
     */

    document
        .querySelectorAll(
            '.wm-transactions-panel .wm-list-row'
        )
        .forEach(function (row) {

            const iconBox =
                row.querySelector(
                    '.wm-row-icon'
                );

            if (!iconBox) {
                return;
            }

            const description =
                (
                    row.querySelector(
                        '.wm-row-copy strong'
                    )?.textContent
                    || ''
                ).toLowerCase();

            const subtext =
                (
                    row.querySelector(
                        '.wm-row-copy small'
                    )?.textContent
                    || ''
                ).toLowerCase();

            const amount =
                (
                    row.querySelector(
                        '.wm-transaction-amount'
                    )?.textContent
                    || ''
                ).trim();


            let iconName =
                'card';


            if (
                description.includes(
                    'fuel'
                ) ||
                description.includes(
                    "huck's"
                )
            ) {
                iconName =
                    'fuel';
            }

            else if (
                subtext.includes(
                    'transfer'
                )
            ) {
                iconName =
                    'transfer';
            }

            else if (
                amount.startsWith('+')
            ) {
                iconName =
                    'income';
            }


            iconBox.innerHTML =
                icon(iconName);
        });


    /*
     * Gains rows.
     */

    document
        .querySelectorAll(
            '.wm-gains-panel .wm-gain-row'
        )
        .forEach(function (row) {

            const iconBox =
                row.querySelector(
                    '.wm-row-icon'
                );

            if (!iconBox) {
                return;
            }

            const description =
                (
                    row.querySelector(
                        '.wm-row-copy strong'
                    )?.textContent
                    || ''
                ).toLowerCase();

            iconBox.innerHTML =
                icon(
                    description.includes(
                        'patricia'
                    )
                        ? 'person'
                        : 'transfer'
                );
        });


    /*
     * Bottom status icon.
     */

    const statusIcon =
        document.querySelector(
            '.wm-status-icon'
        );

    if (statusIcon) {
        statusIcon.innerHTML =
            icon('spark');
    }


    /*
     * Date icon.
     */

    const dateIcon =
        document.querySelector(
            '.wm-date span'
        );

    if (dateIcon) {
        dateIcon.innerHTML =
            icon('calendar');
    }


    /*
     * Sync button.
     */

    const syncButton =
        document.getElementById(
            'dashboard-plaid-sync'
        );

    if (syncButton) {

        const text =
            syncButton.textContent
                .replace(/^[?\s]+/, '')
                .trim();

        syncButton.innerHTML =
            icon('sync')
            + '<span>'
            + text
            + '</span>';
    }


    /*
     * Clean leftover broken icon question marks
     * from dashboard links/buttons.
     */

    dashboard
        .querySelectorAll(
            'a, button'
        )
        .forEach(function (element) {

            if (
                element.querySelector(
                    'svg'
                )
            ) {
                return;
            }

            const original =
                element.textContent;

            const cleaned =
                original
                    .replace(
                        /^[?\s]+/,
                        ''
                    )
                    .replace(
                        /\s+\?$/,
                        ''
                    )
                    .trim();

            if (
                original.trim()
                !== cleaned
            ) {
                element.textContent =
                    cleaned;
            }
        });

});

/* END WMM DASHBOARD SVG ICONS */


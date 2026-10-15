<header class="public-header">

    <div class="container public-header-inner">

        <a
            class="brand"
            href="<?= e(url()); ?>"
        >
            <span class="brand-mark">$</span>

            <span>
                <strong>Where's My Money?</strong>
                <small>Know where every dollar goes.</small>
            </span>
        </a>

        <nav class="public-nav">

            <a href="<?= e(url('#features')); ?>">
                Features
            </a>

            <a href="<?= e(url('#how-it-works')); ?>">
                How It Works
            </a>

            <a href="<?= e(url('#privacy')); ?>">
                Privacy
            </a>

            <a
                class="button button-light"
                href="<?= e(url('login.php')); ?>"
            >
                Login
            </a>

            <a
                class="button button-primary"
                href="<?= e(url('register.php')); ?>"
            >
                Get Started
            </a>

        </nav>

    </div>

</header>

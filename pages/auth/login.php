<main class="auth-page">

    <section class="auth-card">

        <div class="auth-brand">

            <span>$</span>

            <div>
                <strong>
                    Where's My Money?
                </strong>

                <small>
                    Welcome back
                </small>
            </div>

        </div>

        <h1>
            Login
        </h1>

        <p class="auth-intro">
            Open your personal financial dashboard.
        </p>


        <?php if ($error !== null): ?>

            <div class="auth-error">

                <p>
                    <?= e($error); ?>
                </p>

            </div>

        <?php endif; ?>


        <form
            method="post"
            class="auth-form"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrf_token()); ?>"
            >


            <label>

                <span>
                    Email
                </span>

                <input
                    type="email"
                    name="email"
                    value="<?= e($email); ?>"
                    required
                    autocomplete="email"
                >

            </label>


            <label>

                <span>
                    Password
                </span>

                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >

            </label>


            <button type="submit">
                Login
            </button>

        </form>


        <p class="auth-switch">
            Need an account?

            <a href="<?= e(url('register.php')); ?>">
                Register
            </a>
        </p>

    </section>

</main>

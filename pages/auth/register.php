<main class="auth-page">

    <section class="auth-card">

        <div class="auth-brand">
            <span>$</span>

            <div>
                <strong>
                    Where's My Money?
                </strong>

                <small>
                    Create your account
                </small>
            </div>
        </div>

        <h1>
            Start tracking your money.
        </h1>

        <p class="auth-intro">
            Your financial data stays separated
            under your own account.
        </p>


        <?php if ($errors !== []): ?>

            <div class="auth-error">

                <?php foreach ($errors as $error): ?>

                    <p>
                        <?= e($error); ?>
                    </p>

                <?php endforeach; ?>

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
                    Name
                </span>

                <input
                    type="text"
                    name="display_name"
                    value="<?= e($displayName); ?>"
                    required
                    autocomplete="name"
                >

            </label>


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
                    minlength="8"
                    autocomplete="new-password"
                >

            </label>


            <label>

                <span>
                    Confirm Password
                </span>

                <input
                    type="password"
                    name="confirm_password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                >

            </label>


            <button type="submit">
                Create Account
            </button>

        </form>


        <p class="auth-switch">
            Already have an account?

            <a href="<?= e(url('login.php')); ?>">
                Login
            </a>
        </p>

    </section>

</main>

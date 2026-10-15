<?php

$pageTitle = $pageTitle ?? APP_NAME;
$pageType = $pageType ?? 'public';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        <?= e($pageTitle); ?> | <?= e(APP_NAME); ?>
    </title>

    <link
        rel="stylesheet"
        href="<?= e(url('assets/css/app.css')); ?>"
    >

    <?php if ($pageType === 'landing'): ?>
        <link
            rel="stylesheet"
            href="<?= e(url('assets/css/landing.css')); ?>"
        >
    <?php endif; ?>

    <?php if ($pageType === 'app'): ?>
        <link
            rel="stylesheet"
            href="<?= e(url('assets/css/dashboard.css')); ?>"
        >
    <?php endif; ?>
</head>

<body class="<?= e($pageType); ?>">

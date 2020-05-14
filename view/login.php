<?php

/** @var $user \model\LoginForm */

$err = $user->getErrors();
?>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= 'Authorization' ?></title>
    <link href="<?= TEST_BASE_URL ?>assets/css.css" rel="stylesheet">
</head>
<body>
<div class="container" id="login-wrapper">
    <div class="row">
        <div class="panel-wrapper">
            <div class="panel-heading">
                <h3 class="panel-title"><?= 'Authorization' ?></h3>
            </div>
            <div class="panel-body">
                <div class="row summary-error"
                     style="<?= isset($err['summary']) ? '' : 'display:none;' ?>"><?= $err['summary'] ?? '' ?></div>
                <form id="login-form" method="post" autocomplete="off">
                    <div class="form-group field-loginform-login required">
                        <input onchange="helpers.validateAttribute('login')" type="text" id="loginform-login"
                               class="form-control <?= isset($err['login']) ? 'has-error' : '' ?>"
                               name="LoginForm[login]" placeholder="<?= 'Login' ?>" autocomplete="off"
                               value="<?= $user->getAttribute('login') ?>">
                        <p class="help-block help-block-error"><?= $err['login'] ?? '' ?></p>
                    </div>
                    <div class="form-group field-loginform-password required">
                        <input onchange="helpers.validateAttribute('password')" type="password" id="loginform-password"
                               class="form-control <?= isset($err['password']) ? 'has-error' : '' ?>"
                               name="LoginForm[password]" placeholder="<?= 'Password' ?>" autocomplete="off"
                               value="<?= $user->getAttribute('password') ?>">
                        <p class="help-block help-block-error"><?= $err['password'] ?? '' ?></p>
                    </div>
                    <button type="submit" onclick="return helpers.validateForm();"
                            class="btn btn-primary btn-block"><?= 'Sign in' ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?= TEST_BASE_URL ?>assets/js.js"></script>
<script>
    window.onload = function (event) {
        helpers.init({rules:<?= json_encode($user->getRules()) ?>, formID: 'login-form'});
    };
</script>
</body>
</html>

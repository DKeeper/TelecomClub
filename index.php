<?php
if ('cli' === PHP_SAPI) {
    echo 'Command should run in WEB mode' . PHP_EOL;
    exit();
}

session_start();

/**
 * Login = testadmin
 * Password = testadmin
 */
$user = null;

require_once(__DIR__ . '/helpers/autoload.php');
require_once(__DIR__ . '/helpers/helpers.php');
$config = require_once(__DIR__ . '/config/config.php');

define('TEST_BASE_URL', $config['baseUrl']);
define('TEST_APP_URL', $config['appUrl']);

$db = new \components\DBwrapper();
$db->init($config['db']);

if (isset($_SESSION['user'])) {
    $user = new \model\User($db);
    $user->load($_SESSION['user']);
}

$q = get('q', '');

switch ($q) {
    case 'login':
        if (isset($user)) {
            redirectMain();
        }

        $user = new \model\LoginForm($db);
        $loginData = post('LoginForm');

        if (isset($loginData)) {
            $user->load($loginData);

            if ($user->validate()) {
                if ($user->find(['login LIKE :login', 'password LIKE :password'],
                    [':login' => $user->getAttribute('login'), ':password' => md5($user->getAttribute('password'))])) {
                    $_pk = $user->getAttribute($user->getPk());
                    $user = new \model\User($db);
                    $user->findByPk($_pk);
                    $_SESSION['user'] = $user->getAttributes();
                    redirectMain();
                } else {
                    $user->addError('summary', 'Login or Password invalid');
                }
            }
        }

        renderLogin($user);
        break;
    case 'logout':
        if (isset($user)) {
            unset($_SESSION['user']);
        }

        redirectMain();
        break;
    default:
        $postModel = new \model\News($db);
        $postData = post("News");

        if (isset($postData)) {
            $postModel->load($postData);

            if ($postModel->validate() && $postModel->save()) {
                $_SESSION['addPostSuccess'] = "Ваше сообщение было добавлено. После модерации оно будет доступно для просмотра.";
                $postModel = new \model\News($db);
            }
        }

        renderNews($postModel, $postModel->getNews(), $user);
        break;
}

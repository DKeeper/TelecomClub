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
        $postData = post("filters");
        $conditions = [];
        $filterValues = [
            'sort' => [
                'category ASC',
                'category DESC',
                'created_at ASC',
                'created_at DESC',
                'category ASC, created_at ASC',
                'category DESC, created_at DESC',
                'created_at ASC, category ASC',
                'created_at DESC, category DESC',
            ],
            'limit' => [
                10 => 10,
                25 => 25,
                50 => 50,
                100 => 100,
            ],
            'page' => [
                1 => 1,
                5 => 5,
                10 => 10,
                25 => 25,
                50 => 50,
                75 => 75,
                100 => 100,
            ],
        ];

        if (false === isset($postData)) {
            $l = [10, 25, 50, 100];
            $p = [1, 5, 10, 25, 50, 75, 100];
            $postData = [
                'sort' => random_int(0, 7),
                'limit' => $l[array_rand($l)],
                'page' => $p[array_rand($p)],
            ];
        }

        switch ((int) $postData['sort']) {
            case 0:
            case 1:
                $conditions['sort'] = 'category ' . ((int) $postData['sort'] === 0 ? 'ASC' : 'DESC');

                break;
            case 2:
            case 3:
                $conditions['sort'] = 'created_at ' . ((int) $postData['sort'] === 2 ? 'ASC' : 'DESC');

                break;
            case 4:
            case 5:
                $conditions['sort'] = [];

                foreach (['category', 'created_at'] as $value) {
                    $conditions['sort'][] = $value . ' ' . ((int)$postData['sort'] === 4 ? 'ASC' : 'DESC');
                }

                break;
            case 6:
            case 7:
                $conditions['sort'] = [];

                foreach (['created_at', 'category'] as $value) {
                    $conditions['sort'][] = $value . ' ' . ((int)$postData['sort'] === 6 ? 'ASC' : 'DESC');
                }

                break;
            default:
        }

        $conditions['limit'] = (int) $postData['limit'];
        $conditions['offset'] = ((int) $postData['page'] - 1) * (int) $postData['limit'];

        renderNews($postData, $filterValues, $postModel->getList($conditions, true), $user);
        break;
}

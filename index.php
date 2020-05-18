<?php
if ('cli' === PHP_SAPI) {
    echo 'Command should run in WEB mode' . PHP_EOL;
    exit();
}

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '');

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

$scriptPath = [TEST_BASE_URL, TEST_BASE_URL . 'index.php'];

if (false === in_array($uri['path'], $scriptPath, true)) {
    exit();
}

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
    case 'news':
        if (null === $id = get('id')) {
            redirectMain();
        }

        $model = new \model\News($db);

        if (false === $model->findByPk($id)) {
            echo 'Model couldn\'t found by ID ' . $id;
            exit();
        }

        echo viewPhpFile(getViewPath() . 'news_single.php', [
            'user' => $user,
            'model' => $model,
        ]);

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
        ];

        if (false === isset($postData)) {
            $l = [10, 25, 50, 100];
            $postData = [
                'sort' => random_int(0, 7),
                'limit' => $l[array_rand($l)],
                'page' => random_int(1, 100),
            ];
        } else {
            $postData = array_map('intval', $postData);
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

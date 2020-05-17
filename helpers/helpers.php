<?php

/**
 * @param string $name
 * @param null|mixed $default
 *
 * @return null|mixed
 */
function post($name, $default = null)
{
    return $_POST[$name] ?? $default;
}

/**
 * @param string $name
 * @param null|mixed $default
 *
 * @return null|mixed
 */
function get($name, $default = null)
{
    return $_GET[$name] ?? $default;
}

/**
 * @param $str
 * @param $config
 *
 * @return null|string
 */
function validateRequire($str, $config)
{
    return !empty($str) ? null : $config['message'];
}

/**
 * @param $path
 * @param $config
 *
 * @return null|string
 */
function validateFile($path, $config)
{
    $valid = true;

    if (!is_file($path)) {
        $valid = false;
        $config['message'] = 'Wrong file';
    } else {
        try {
            $img = new Imagick($path);
            $identity = $img->identifyImage();
            $type = str_replace('image/', '', $identity['mimetype']);

            if (!in_array($type, $config['allowedType'], true)) {
                $valid = false;
            }
        } catch (ImagickException $e) {
            $config['message'] = 'File is not image';
            $valid = false;
        }
    }

    return $valid ? null : $config['message'];
}

/**
 * @param $param
 * @param $config
 *
 * @return null|string
 */
function validate($param, $config)
{
    $valid = true;

    switch ($config[0]) {
        case 'min':
            if (strlen($param) < $config[1]) {
                $valid = false;
            }
            break;
        case 'max':
            if (strlen($param) > $config[1]) {
                $valid = false;
            }
            break;
        default:
    }

    return $valid ? null : $config['message'] . $config[1];
}

/**
 * @param string $str
 * @param array $config
 *
 * @return null|string
 */
function validateRegExp($str, $config)
{
    $valid = true;
    if (!preg_match($config['pattern'], $str, $matches)) {
        $valid = false;
    }

    return $valid ? null : $config['message'];
}

/**
 * @param string $_file_
 * @param array $_params_
 *
 * @return string
 */
function viewPhpFile($_file_, $_params_ = [])
{
    ob_start();
    ob_implicit_flush(false);
    extract($_params_, EXTR_OVERWRITE);
    require($_file_);

    return ob_get_clean();
}

function getViewPath()
{
    return (dirname(__DIR__) . '/view/');
}

function renderNews($model, $data = [], $user = null)
{
    echo viewPhpFile(getViewPath() . 'news_list.php', [
        'user' => $user,
        'dataProvider' => $data,
        'model' => $model,
    ]);
}

/**
 * @param string $image
 *
 * @return string
 */
function getImageLink(string $image): string
{
    return TEST_BASE_URL . 'upload'
        . DIRECTORY_SEPARATOR . substr(md5($image), 0, 2)
        . DIRECTORY_SEPARATOR . $image
        ;
}

function uuid4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

        // 32 bits for "time_low"
        random_int(0, 0xffff), random_int(0, 0xffff),

        // 16 bits for "time_mid"
        random_int(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        random_int(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        random_int(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
    );
}

function renderLogin($user)
{
    echo viewPhpFile(getViewPath() . 'login.php', [
        'user' => $user,
    ]);
}

function redirect($url)
{
    header("Refresh: 0; url=$url");
}

function redirectMain()
{
    redirect(TEST_APP_URL);
}

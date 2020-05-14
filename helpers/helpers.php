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
    echo viewPhpFile(getViewPath() . 'news.php', [
        'user' => $user,
        'news' => $data,
        'model' => $model,
    ]);
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

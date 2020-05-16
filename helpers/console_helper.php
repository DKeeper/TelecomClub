<?php
/**
 * @return string|null
 */
function getCommand(): ?string
{
    return $_SERVER['argv'][1] ?? null;
}

/**
 * @return array
 */
function getParameters(): array
{
    $p = [];

    foreach (array_slice($_SERVER['argv'], 2) as $line) {
        $line = explode('=', $line);
        $name = $line[0];
        $value = $line[1] ?? null;
        $p[ltrim($name, '-')] = trim($value);
    }

    return $p;
}

/**
 * @param int $length

 * @return string
 *
 * @throws Exception
 */
function generateRandomString($length = 10): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ -!?,.:';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

/**
 * @param string $fileName
 *
 * @return string
 */
function generateRandomUploadPath(string $fileName): string
{
    return TEST_BASE_PATH
        . DIRECTORY_SEPARATOR . 'upload'
        . DIRECTORY_SEPARATOR . substr(md5($fileName), 0, 2);
}

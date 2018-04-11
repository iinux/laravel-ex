<?php
/**
 * Created by PhpStorm.
 * User: nalux
 * Date: 2017/11/5
 * Time: 18:33
 */

$PASSWORD = '9f29b97a0ee6f8e6ba4783fc9bc08ce2c8c82bae';

session_start();
if (PHP_SAPI !== "cli" && !isset($_SESSION['_sfm_allowed'])) {
    // sha1, and random bytes to thwart timing attacks.  Not meant as secure hashing.
    // zhangqun modify
    // $t = bin2hex(openssl_random_pseudo_bytes(10));
    // if($_POST['p'] && sha1($t.$_POST['p']) === sha1($t.$PASSWORD)) {
    if (isset($_POST['p']) && sha1($_POST['p'].'love') === $PASSWORD) {
        $_SESSION['_sfm_allowed'] = true;
        header('Location: ?');
    }
    echo '<html><body><form action=? method=post>PASSWORD:<input type=password name=p /></form></body></html>';
    exit;
}

function dd($var, $exit = true)
{
    var_dump($var);
    if ($exit) {
        die(0);
    }
}

function my_echo($str, $exit = false)
{
    echo "<p>$str</p>";
    if ($exit) {
        die(0);
    }
}

function parse_file_name($fileName, $reverse = false)
{
    // 如果是Windows, 将文件名和路径转成Windows系统默认的GB2312编码, 否则将会读取不到
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        if ($reverse) {
            $fileName = iconv("gb2312", "utf-8", $fileName);
        } else {
            $fileName = iconv("utf-8", "gb2312", $fileName);
        }
    }
    return $fileName;
}

set_exception_handler(function ($e) {
    dd($e);
});
set_error_handler(function ($e) {
    dd($e);
});

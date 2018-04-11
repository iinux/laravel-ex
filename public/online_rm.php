<?php

include 'online_base.php';

header("Content-type:text/html;charset=utf-8");

if (!isset($_GET['fileName'])) {
    my_echo("参数 fileName 不存在！", true);
}

// 先判断待解压的文件是否存在
$fileName = $_GET['fileName'];
if (!file_exists(parse_file_name($fileName)) && !is_dir(parse_file_name($fileName))) {
    my_echo("文件 $fileName 不存在！", true);
}

// 开始的时间
$startTime = explode(' ', microtime());

function rm_dir($dir)
{
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        $realPath = $file->getRealPath();
        if ($file->isDir()){
            my_echo("delete dir $realPath");
            rmdir($realPath);
        } else {
            my_echo("delete file $realPath");
            unlink($realPath);
        }
    }
    rmdir($dir);
}

rm_dir($fileName);

// 结束的时间
$endTime = explode(' ', microtime());

$elapseTime = $endTime[0] + $endTime[1] - ($startTime[0] + $startTime[1]);
my_echo("完毕！, 本次花费：$elapseTime 秒。");


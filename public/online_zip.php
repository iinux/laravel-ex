<?php

include 'online_base.php';

header("Content-type:text/html;charset=utf-8");

if (!isset($_GET['fileName'])) {
    my_echo("参数 fileName 不存在！", true);
}

// 先判断文件是否存在
$fileName = $_GET['fileName'];
if (!file_exists(parse_file_name($fileName)) && !is_dir(parse_file_name($fileName))) {
    my_echo("文件 $fileName 不存在！", true);
}

// 开始的时间
$startTime = explode(' ', microtime());

function zip_dir($dir)
{
    // 使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释
    $zip = new ZipArchive();
    if ($zip->open("$dir.zip", ZipArchive::CREATE) !== true) {
        my_echo('无法打开文件，或者文件创建失败', true);
    }

    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        /**
         * @var SplFileInfo $file
         */
        $realPath = $file->getRealPath();
        if ($file->isDir()){
        } else {
            my_echo("add file $realPath");
            $zip->addFile($realPath, $file->getPathname());
        }
    }

    $zip->close();
}

zip_dir($fileName);

// 结束的时间
$endTime = explode(' ', microtime());

$elapseTime = $endTime[0] + $endTime[1] - ($startTime[0] + $startTime[1]);
my_echo("完毕！, 本次花费：$elapseTime 秒。", true);


<?php

include 'online_base.php';

$tryUnzipLargeFile = true;
$largeFileThresholdValue = 1024 * 1024 * 6; // 6MB
$skipCount = 1;
$pathPrefix = '';
// $pathPrefix = '../';
$overwrite = true;


// 需开启配置 php_zip.dll
// phpinfo();
header("Content-type:text/html;charset=utf-8");

if (!isset($_GET['fileName'])) {
    my_echo("参数 fileName 不存在！", true);
}

// 先判断待解压的文件是否存在
$fileName = $_GET['fileName'];
if (!file_exists(parse_file_name($fileName))) {
    my_echo("文件 $fileName 不存在！", true);
}

// 解压开始的时间
$startTime = explode(' ', microtime());

// 打开压缩包
$resource = zip_open($fileName);

if (!is_resource($resource) && $resource >= ZipArchive::ER_MULTIDISK && $resource <= ZipArchive::ER_DELETED) {
    echo '
#define ZIP_ER_OK             0  /* N No error */<br />
#define ZIP_ER_MULTIDISK      1  /* N Multi-disk zip archives not supported */<br />
#define ZIP_ER_RENAME         2  /* S Renaming temporary file failed */<br />
#define ZIP_ER_CLOSE          3  /* S Closing zip archive failed */<br />
#define ZIP_ER_SEEK           4  /* S Seek error */<br />
#define ZIP_ER_READ           5  /* S Read error */<br />
#define ZIP_ER_WRITE          6  /* S Write error */<br />
#define ZIP_ER_CRC            7  /* N CRC error */<br />
#define ZIP_ER_ZIPCLOSED      8  /* N Containing zip archive was closed */<br />
#define ZIP_ER_NOENT          9  /* N No such file */<br />
#define ZIP_ER_EXISTS        10  /* N File already exists */<br />
#define ZIP_ER_OPEN          11  /* S Can\'t open file */<br />
#define ZIP_ER_TMPOPEN       12  /* S Failure to create temporary file */<br />
#define ZIP_ER_ZLIB          13  /* Z Zlib error */<br />
#define ZIP_ER_MEMORY        14  /* N Malloc failure */<br />
#define ZIP_ER_CHANGED       15  /* N Entry has been changed */<br />
#define ZIP_ER_COMPNOTSUPP   16  /* N Compression method not supported */<br />
#define ZIP_ER_EOF           17  /* N Premature EOF */<br />
#define ZIP_ER_INVAL         18  /* N Invalid argumen */<br />
#define ZIP_ER_NOZIP         19  /* N Not a zip archive */<br />
#define ZIP_ER_INTERNAL      20  /* N Internal error */<br />
#define ZIP_ER_INCONS        21  /* N Zip archive inconsistent */<br />
#define ZIP_ER_REMOVE        22  /* S Can\'t remove file */<br />
#define ZIP_ER_DELETED       23  /* N Entry has been deleted */<br />
    ';
    my_echo("打开文件 $fileName 失败, error code $resource", true);
}

// 遍历读取压缩包里面的一个个文件
while ($entryResource = zip_read($resource)) {
    // 如果能打开则继续
    if (zip_entry_open($resource, $entryResource)) {
        // 获取当前项目的名称,即压缩包里面当前对应的文件名
        $fileName = zip_entry_name($entryResource);
        /*$encode=mb_detect_encoding($fileName,array(
            "ASCII",'UTF8','GB2312',"GBK",'BIG5'
        ));
        my_echo($encode);
        $fileName = iconv("cp1252", "utf-8", $fileName);*/

        if (isset($_GET['view'])) {
            my_echo($fileName);
            // 关闭当前
            zip_entry_close($entryResource);
            continue;
        }

        // 以最后一个 "/" 分割,再用字符串截取出路径部分
        $filePath = substr($fileName, 0, strrpos($fileName, "/"));
        $filePath = $pathPrefix . $filePath;
        $fileName = $pathPrefix . $fileName;

        // 如果路径不存在, 则创建一个目录, true表示可以创建多级目录
        if ($filePath && !is_dir($filePath)) {
            mkdir($filePath, 0777, true);
            my_echo("mkdir $filePath");
        }
        // 如果不是目录, 则写入文件
        if (!is_dir($fileName) && ($overwrite || !file_exists($fileName))) {
            // 读取这个文件
            $fileSize = zip_entry_filesize($entryResource);
            // 最大读取6M, 如果文件过大, 跳过解压, 继续下一个
            if ($tryUnzipLargeFile || $fileSize < $largeFileThresholdValue) {
                $fileContent = zip_entry_read($entryResource, $fileSize);
                file_put_contents($fileName, $fileContent);
                my_echo("file_put_contents $fileName");
            } else {
                my_echo("<$skipCount> $fileName 文件过大, 跳过");
                $skipCount++;
            }
        }
        // 关闭当前
        zip_entry_close($entryResource);
    }
}
// 关闭压缩包
zip_close($resource);

// 解压结束的时间
$endTime = explode(' ', microtime());

$elapseTime = $endTime[0] + $endTime[1] - ($startTime[0] + $startTime[1]);
my_echo("解压完毕！, 本次解压花费：$elapseTime 秒。跳过 $skipCount 个大文件");


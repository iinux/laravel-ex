<?php

include 'online_base.php';

header("Content-type:text/html;charset=utf-8");

if (!isset($_GET['wget'])) {
    my_echo("参数 wget 不存在！", true);
}

// 开始的时间
$startTime = explode(' ', microtime());

function wget($url)
{
    $headers = array(
        "Accept-Language: zh-CN,zh;q=0.8"
    );
    $curlHandleResource = curl_init();
    curl_setopt($curlHandleResource, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curlHandleResource, CURLOPT_URL, $url);
    // 设置是将结果保存到字符串中还是输出到屏幕上, 1表示将结果保存到字符串
    curl_setopt($curlHandleResource, CURLOPT_RETURNTRANSFER, 1);
    // 显示返回的Header区域内容
    curl_setopt($curlHandleResource, CURLOPT_HEADER, 0);
    curl_setopt($curlHandleResource, CURLOPT_BINARYTRANSFER, true);
    // curl_setopt($curlHandleResource, CURLOPT_ENCODING, 'gzip,deflate');
    // 使用自动跳转
    curl_setopt($curlHandleResource, CURLOPT_FOLLOWLOCATION, true);
    // 对认证证书来源的检查
    curl_setopt($curlHandleResource, CURLOPT_SSL_VERIFYPEER, 0);
    //curl_setopt($curlHandleResource, CURLOPT_SSL_VERIFYHOST, 1);
    // 模拟用户使用的浏览器
    curl_setopt($curlHandleResource, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    if (isset($_GET['referer'])) {
        curl_setopt($curlHandleResource, CURLOPT_REFERER, $_GET['referer']);
    }
    // 读取上面所储存的Cookie信息
    //curl_setopt($curlHandleResource, CURLOPT_COOKIEFILE,$GLOBALS['cookie_file']);
    // 存放Cookie信息的文件名称
    //curl_setopt($curlHandleResource, CURLOPT_COOKIEJAR, $GLOBALS['cookie_file']);
    curl_setopt($curlHandleResource, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环

    //$postData = array ("username" => "bob","key" => "12345");
    // post数据
    //curl_setopt($curlHandleResource, CURLOPT_POST, 1);
    // post的变量
    //curl_setopt($curlHandleResource, CURLOPT_POSTFIELDS, $postData);

    if (curl_errno($curlHandleResource)) {
        my_echo('error ' . curl_error($curlHandleResource), true);
    }

    $output = curl_exec($curlHandleResource);
    //$info = curl_getinfo($curlHandleResource);
    curl_close($curlHandleResource);

    file_put_contents(basename($url), $output);
    //$str = gzdecode($output);
}

wget($_GET['wget']);

// 结束的时间
$endTime = explode(' ', microtime());

$elapseTime = $endTime[0] + $endTime[1] - ($startTime[0] + $startTime[1]);
my_echo("完毕！, 本次花费：$elapseTime 秒。", true);

<?php
header('Content-Type:text/json;charset=UTF-8');
$id = isset($_GET['id']) ? $_GET['id'] : 'cctv1';
$n = [
    'cctv4k' => '2000266303',  // cccv4k-1080P
];
if (empty($n[$id])) {
    $id = 'cctv1';
}

$cnlid = $n[$id];
$guid = rand_str(6);
$salt = '0f$IVHi9Qno?G';
$platform = "5910204";
$key = hex2bin("48e5918a74ae21c972b90cce8af6c8be");
$iv = hex2bin("9a7e7d23610266b1d9fbf98581384d92");
$ts = time();
$el = "|{$cnlid}|{$ts}|mg3c3b04ba|V1.0.0|{$guid}|{$platform}|[url]https://www.yangshipin.c[/url]|mozilla/5.0 (windows nt ||Mozilla|Netscape|Win32|";

$len = strlen($el);
$xl = 0;
for ($i = 0; $i < $len; $i++) {
    $xl = ($xl << 5) - $xl + ord($el[$i]);
    $xl &= $xl & 0xFFFFFFFF;
}

$xl = ($xl > 2147483648) ? $xl - 4294967296 : $xl;

$el = '|' . $xl . $el;
$ckey = "--01" . strtoupper(bin2hex(openssl_encrypt($el, "AES-128-CBC", $key, 1, $iv)));

$params = [
    "adjust" => 1,
    "appVer" => "V1.0.0",
    "app_version" => "V1.0.0",
    "cKey" => $ckey,
    "channel" => "ysp_tx",
    "cmd" => "2",
    "cnlid" => "{$cnlid}",
    "defn" => "fhd",
    "devid" => "devid",
    "dtype" => "1",
    "guid" => $guid,
    "id" => $id,
    "ip" => "",
    "isp" => "",
    "os" => "",
    "p2p" => "",
    "platform" => $platform,
    "salt" => $salt,
    "ts" => $ts,
    "type" => "1",
    "v" => "1.0",
    "ver" => "1.0",
    "xl" => $xl,
];

$url = "http://ysp_tx.liveplay.myqcloud.com/redirect.php?" . http_build_query($params);

echo json_encode([
    'url' => $url,
]);

function isIpValid($ip)
{
    return preg_match('/\b(?:\d{1,3}\.){3}\d{1,3}\b/', $ip);
}

function rand_str($length)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    $max = strlen($chars) - 1;

    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[rand(0, $max)];
    }

    return $str;
}

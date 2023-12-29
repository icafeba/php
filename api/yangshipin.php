<?php
header('Content-Type:text/json;charset=UTF-8');
$id = $_GET['id'] ?? 'cctv1';
$n = [
    'cctv4k' => '2000266303',  // cccv4k-1080P
];
if(empty($n[$id])){
    $id = 'cctv1'; 
}
$ipFile = 'ysp_ip_collection.json';

if (file_exists($ipFile)) {
    $ipCollection = json_decode(file_get_contents($ipFile), true);
}else{
        $ipCollection = [
        'back' => [],
        'pk' => 0,
        'tech' => [],
    ];
}
// 此处进行IP添加,PHP自动更新。
$newIpCollection = [
    '182.242.215.102',
    '182.242.215.123',
    '182.242.215.138',
    '182.242.215.200',
    '182.242.215.223',
    '111.123.50.188',
    '111.123.56.52',
    '111.123.56.62',
    '111.123.56.76',
    '111.123.56.96',
    '111.123.56.97',
    '111.31.107.178',
];

$ipdiff = array_diff($newIpCollection, $ipCollection['back']);
if (count($ipdiff) > 0) {
    $ipCollection['back'] = array_unique(array_merge($ipCollection['back'], $newIpCollection), SORT_REGULAR);
    $ipCollection['tech'] = array_merge($ipCollection['tech'], $ipdiff);
    $change = 1;   
}
   
if (!empty($ipCollection['tech'])) {
    $ip = $ipCollection['tech'][array_rand($ipCollection['tech'])];
    $m3u8 = "http://{$ip}/tlivecloud-ipv6.ysp.cctv.cn/ysp/{$n[$id]}.m3u8";
    if (isIpValid($m3u8)) {
        header('Location: ' . $m3u8);
        if($change == 1){
            file_put_contents($ipFile, json_encode($ipCollection));
        }
        exit; 
    } else {
        unset($ipCollection['tech'][array_search($ip, $ipCollection['tech'])]);
        $ipCollection['tech'] = array_values($ipCollection['tech']);
        file_put_contents($ipFile, json_encode($ipCollection));
    }
}        
//无有效缓存IP时，动态代理输出
//以下代码来至guoma的分享    
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
for($i=0;$i<$len;$i++){
    $xl = ($xl << 5) - $xl + ord($el[$i]);
    $xl &= $xl & 0xFFFFFFFF;
    }

$xl = ($xl > 2147483648) ? $xl - 4294967296 : $xl; 

$el = '|'.$xl.$el;
$ckey = "--01".strtoupper(bin2hex(openssl_encrypt($el,"AES-128-CBC",$key,OPENSSL_RAW_DATA,$iv)));

$params = [
        "adjust"=>1,
        "appVer"=>"V1.0.0",
        "app_version"=>"V1.0.0",
        "cKey"=>$ckey,
        "channel"=>"ysp_tx",
        "cmd"=>"2",
        "cnlid"=>"{$cnlid}",
		"defn"=>"fhd",
        "devid"=>"devid",
        "dtype"=>"1",
        "encryptVer"=>"8.1",
        "guid"=>$guid,
        "otype"=>"ojson",
        "platform"=>$platform,
        "rand_str"=>"{$ts}",
        "sphttps"=>"1",
        "stream"=>"2"
        ];

$sign = md5(http_build_query($params).$salt);
$params["signature"] = $sign;

$bstrURL = "https://player-api.yangshipin.cn/v1/player/get_live_info";
$headers = [
        "Content-Type: application/json",
        "Referer:https://www.yangshipin.cn/",
        "Cookie: guid={$guid};vplatform=109",
        "Yspappid: 519748109",
        "user-agent:".$_SERVER['HTTP_USER_AGENT'],
        ];
$json = json_decode(get_data($bstrURL,$headers,$params));
$live = $json->data->playurl;
$host = parse_url($live)['host'];
$cdns = array(
    'hlslive-hs-cdn.ysp.cctv.cn',
    'hlslive-ty-cdn.ysp.cctv.cn',
);
$cdn = $cdns[array_rand($cdns)];
$m3u8 = preg_replace("/{$host}/",$cdn,$live);
$m3u8 = trim(preg_replace("/https/","http",$m3u8));
$burl = dirname($m3u8)."/";

header('Content-Type: application/vnd.apple.mpegurl');
print_r(preg_replace("/(.*?.ts)/i", $burl."$1",get_data($m3u8,$headers)));
exit; 
function get_data($url,$header,$post=null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
    curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
    if(!empty($post)){
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($post));
    }
    $data = curl_exec($ch);
    curl_close($ch);    
    return $data;   
} 
function isIpValid($ip) {
    $timeout = 3;
    $ch = curl_init($ip);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ($httpCode >= 200 && $httpCode <= 302);
}
function rand_str($k) {
    $e = "ABCDEFGHIJKlMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $i = 0;
    $str = "";
    while($i < $k) {
        $str.= $e[mt_rand(0,61)];
        $i++;
    }
    return $str;
}
?>	

<?php
header('Content-Type:text/json;charset=UTF-8');
$id = isset($_GET['id'])?$_GET['id']:'cctv1';
$n = [
    'cctv4k' => '2000266303',  // cccv4k-1080P
    'cctv8k' => '2020603421',  //cctv8k-1080P
    'cctv1' => '2000210103',  // cccv1
    'cctv2' => '2000203603',  // cccv2
    'cctv3' => '2000203803',  // cccv3(vip)
    'cctv4' => '2000204803',  // cccv4
    'cctv5' => '2000205103',  // cccv5
    'cctv5p' =>'2000204503',  // cccv5+
    'cctv6' => '2000203303',  // cccv6(vip)
    'cctv7' => '2000510003',  // cccv7
    'cctv8' => '2000203903',  // cccv8(vip)
    'cctv9' => '2000499403',  // cccv9
    'cctv10' => '2000203503',  // CCTV10
    'cctv11' => '2000204103',  // CCTV11
    'cctv12' => '2000202603',  // CCTV12
    'cctv13' => '2000204603',  // CCTV13
    'cctv14' => '2000204403',  // CCTV14
    'cctv15' => '2000205003',  // CCTV15
    'cctv16' => '2012375003',  // CCTV16
    'cctv16-4k' => '2012375003',  // CCTV16-4k(vip)//*ID可能不对*
    'cctv17' => '2000204203',  // CCTV17
    // 央视数字
    'bqkj' => '2012513403',  // CCTV兵器科技(vip)
    'dyjc' => '2012514403',  // CCTV第一剧场(vip)
    'hjjc' => '2012511203',  // CCTV怀旧剧场(vip)
    'fyjc' => '2012513603',  // CCTV风云剧场(vip)
    'fyyy' => '2012514103',  // CCTV风云音乐(vip)
    'fyzq' => '2012514203',  // CCTV风云足球(vip)
    'dszn' => '2012514003',  // CCTV电视指南(vip)
    'nxss' => '2012513903',  // CCTV女性时尚(vip)
    'whjp' => '2012513803',  // CCTV央视文化精品(vip)
    'sjdl' => '2012513303',  // CCTV世界地理(vip)
    'gefwq' => '2012512503',  // CCTV高尔夫网球(vip)
    'ystq' => '2012513703',  // CCTV央视台球(vip)
    'wsjk' => '2012513503',  // CCTV卫生健康(vip)
    //央视国际
    'cgtn' => 2001656803,//CGTN
    'cgtnjl' => 2010155403,//CGTN纪录
    'cgtne' => 2010152503,//CGTN西语
    'cgtnf' => 2010153503,//CGTN法语
    'cgtna' => 2010155203,//CGTN阿语
    'cgtnr' => 2010152603,//CGTN俄语
    //卫视
    'bjws' => 2000272103,//北京卫视
    'dfws' => 2000292403,//东方卫视
    'tjws' => 2019927003, //天津卫视
    'cqws' => 2000297803,//重庆卫视
    'hljws' => 2000293903,//黑龙江卫视
    'lnws' => 2000281303,//辽宁卫视
    'hbws' => 2000293403,//河北卫视
    'sdws' => 2000294803,//山东卫视
    'ahws' => 2000298003,//安徽卫视
    'hnws' => 2000296103,//河南卫视
    'hubws' => 2000294503,//湖北卫视
    'hunws' => 2000296203,//湖南卫视
    'jxws' => 2000294103,//江西卫视
    'jsws' => 2000295603,//江苏卫视
    'zjws' => 2000295503,//浙江卫视
    'dnws' => 2000292503,//东南卫视
    'gdws' => 2000292703,//广东卫视
    'szws' => 2000292203,//深圳卫视
    'gxws' => 2000294203,//广西卫视
    'gzws' => 2000293303,//贵州卫视
    'scws' => 2000295003,//四川卫视
    'xjws' => 2019927403, //新疆卫视
    'hinws' => 2000291503,//海南卫视
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
$ckey = "--01".strtoupper(bin2hex(openssl_encrypt($el,"AES-128-CBC",$key,1,$iv)));

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
$cdn = $cdns[array_rand($cdns, 1)];
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
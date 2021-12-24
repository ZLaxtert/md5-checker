<?php

error_reporting(0);
ini_set('memory_limit', '-1');

//INPUT LISTS
system("clear");
echo banner();
enterlist:
echo PHP_EOL."[+] Enter your list (eg: list.txt) >> ";
$listname = trim(fgets(STDIN));
if(empty($listname) || !file_exists($listname)) {
	echo PHP_EOL."[!] list not found [!]".PHP_EOL;
	goto enterlist;
}
$lists = array_unique(explode("\n",str_replace("\r","",file_get_contents($listname))));

//COUNT
$total = count($lists);
$s     = 0;
$d     = 0;

echo PHP_EOL."[!] TOTAL $total LISTS [!]".PHP_EOL.PHP_EOL;
global $jam, $result;

foreach($lists as $list){
    jam();
    
    //EXPLODE
    if(strpos($list, "|") !== false) list($email, $pwd) = explode("|", $list);
    else if(strpos($list, ":") !== false) list($email, $pwd) = explode(":", $list);
    else $email = $list;
    if(empty($email)) continue;
    $email = str_replace(" ", "", $email);
    
    //CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.banditcoding.xyz/md5/?md5=$list&type=md5");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $res = curl_exec($ch);
    $json = json_decode($res, TRUE);
    $result      = $json['data']['info']['result'];
    curl_close($ch);

    //RESPONSE
    if(strpos($res, '"status":"success"')){
        $s++;
        file_put_contents("result/success.txt", "[$jam] SUCCESS => ".$email."|".$result." @Zlaxtert".PHP_EOL, FILE_APPEND);
        echo "[$jam] SUCCESS => $email|$result @Zlaxtert".PHP_EOL;
    }elseif(strpos($res, '"status":"failed"')){
        echo "[!] INVALID PARAMETERS [!]".PHP_EOL;
        exit();
    }else{
        $d++;
        file_put_contents("result/failed.txt", "[$jam] FAILED => ".$list." @Zlaxtert".PHP_EOL, FILE_APPEND);
        echo "[$jam] FAILED => $list @Zlaxtert".PHP_EOL;
    }
}

echo PHP_EOL;
echo "=================[SUCCES]=================".PHP_EOL;
echo " INFO :".PHP_EOL;
echo "    - TOTAL $total".PHP_EOL;
echo "    - SUCCESS $s".PHP_EOL;
echo "    - FAILED $d".PHP_EOL;
echo "=================[THANKS]=================".PHP_EOL;
echo "   FILE RESULT SAVED IN FOLDER 'result' ".PHP_EOL;
echo PHP_EOL;

function banner(){
    date_default_timezone_set("Asia/Jakarta");
    $date = date("l, d-m-Y (H:m:s)");

    // CURL 
    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.banditcoding.xyz/dev/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $ok = curl_exec($ch);
    $js = json_decode($ok, TRUE);
    $ip      = $js['data']['info']['connection']['ip'];
    $isp     = $js['data']['info']['connection']['isp'];
    $country = $js['data']['info']['connection']['country'];
    
    // BANNER

    $banner = "
    
  .___  ___.  _______   _____    ________________________
  |   \/   | |       \ | ____|  |       MD5 CHECKER      |    
  |  \  /  | |  .--.  || |__    |     CODE BY ZLAXTERT   |
  |  |\/|  | |  |  |  ||___ \   |         V.1.1.0        |
  |  |  |  | |  '--'  | ___) |  |       BANDITCODING     |
  |__|  |__| |_______/ |____/   |________________________|

             $date
============================================================
   YOUR IP      : $ip
   YOUR ISP     : $isp
   YOUR COUNTRY : $country
============================================================
";
    return $banner;
}

function jam(){
    global $jam;
    $date = new DateTime();
    $jam = $date->format("H:m:s");
    return $jam;
}
function getStr($source, $start, $end) {
    $a = explode($start, $source);
    $b = explode($end, $a[1]);
    return $b[0];
}

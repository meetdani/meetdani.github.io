	<?php
	$time_start = time(true);
	$playlist = "joox.m3u";
	if (file_exists($playlist)) {
		unlink($playlist);
	}
	error_reporting(0);
    function ngecurl($url , $post=null , $header=null){
$ch = curl_init($url);
if($post != null) {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
}
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; U; CPU iPhone OS 8_3_3 like Mac OS X; en-SG) AppleWebKit/537.25 (KHTML, like Gecko) Version/7.0 Mobile/8C3 Safari/6533.18.1");
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd()."cookies.txt");
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd()."cookies.txt");
curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
if($header != null) {
    curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
}
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
return curl_exec($ch);
curl_close($ch);
	}
function bersih($song){return preg_replace('/\s+/', '', $song);}
    $data = ngecurl('http://www.joox.com/ranking?lang=id_id');
  preg_match_all('/<span class=\"songname-text\">(.*?)<\/span>/s',$data,$matchesid);
 foreach($matchesid[1] as $idx=>$valx){
 $detail1 = array();
$ganti = str_replace("/", "", $valx);
 preg_match_all('/<a href=.*/',$ganti,$detail1);
 $pec = explode("'", str_replace("style='display:inline;'", "", $detail1[0][0]));
$final = str_replace("single?id=", "", "$pec[1]");
//download broh
 $song = ngecurl("http://api.joox.com/web-fcgi-bin/web_get_songinfo?songid=".$final."&lang=id&country=id&from_type=-1&channel_id=-1");
$mp4a = '/"m4aUrl":"(.*?)"/';
$mp3  = '/"mp3Url":"(.*?)"/';
$title  = '/"msong":"(.*?)"/';
       $artis  = '/"msinger":"(.*?)"/';
preg_match_all($mp4a, $song, $matchesmp4a);
preg_match_all($mp3, $song, $matchesmp3);
preg_match_all($title, $song, $matchestitle);
preg_match_all($artis, $song, $matchesartis);
// replace \ on fputs contains error
if($matchesmp4a[1][0] != null){
$source = $matchesmp4a[1][0];
$donlot = curl_init();
curl_setopt($donlot, CURLOPT_URL, $source);
curl_setopt($donlot, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($donlot, CURLOPT_COOKIEFILE, "cookies.txt");
curl_setopt($donlot, CURLOPT_FOLLOWLOCATION,1);
$titlesave = str_replace('\\', '', $matchestitle[1][0]);
$destination = ''.$matchesartis[1][0].' - '.$titlesave.'.m4a';
$destinations = ''.$matchesartis[1][0].' - '.$titlesave.'.m4a'.PHP_EOL;
$filez = fopen($playlist, "a+");
fwrite($filez, $destinations);
fclose($filez);
if (file_exists($destination)) {
echo "Lagu sudah ada\n";
}else{
$files = curl_exec ($donlot);
$error = curl_error($donlot); 
curl_close ($donlot);
$file = fopen($destination, "w+");
fputs($file, $files);
fclose($file);
echo "[M4A] ".$destination." SELESAI\n";
}
}else if($matchesmp3[1][0] != null){
$source = $matchesmp3[1][0];
$mp3 = curl_init();
curl_setopt($mp3, CURLOPT_URL, $source);
curl_setopt($mp3, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($mp3, CURLOPT_COOKIEFILE, "cookies.txt");
curl_setopt($mp3, CURLOPT_FOLLOWLOCATION,1);
$titlesave = str_replace('\\', '', $matchestitle[1][0]);
$destination = "".$matchesartis[1][0]." - ".$matchestitle[1][0].".mp3";
if (file_exists($destination)) {
	echo "Lagu sudah ada\n";
}else{
$filess = curl_exec ($mp3);
$errorr = curl_error($mp3); 
curl_close ($mp3);
$filez = fopen($destination, "w+");
fputs($filez, $filess);
fclose($filez);
echo "[MP3] ".$destination." SELESAI\n";
}       
}else{
	$time_end = time(true);
	$execution_time = ($time_end - $time_start);
    echo 'Lagu Terdownload Semua Dalam Waktu : '.$execution_time.' Detik'.PHP_EOL;
    echo "Playlist baru : $playlist\n".PHP_EOL;
    echo "SELAMAT MENDENGARKAN!";
}
}
?>
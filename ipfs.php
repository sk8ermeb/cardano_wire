<?php
function getipfsfile($ipfshash, $maxsize, $filefinal)
{
	$maxbytes = strval(intval($maxsize*1024*1024)+10);
	//$curfilsize = 0;
	//$ext = $article['ext'];
	//$filetmp = "/var/www/html/nftfiles/$ipfshash.$ext.tmp";
	//$filefinal = "/var/www/html/nftfiles/$ipfshash.$ext";
	$filetmp = $filefinal."tmp";
	$myfile = fopen($filetmp, "w");
	$batch = 1024*100;
	$iteration = 0;
	$toobig = true;
	while((($iteration+1)*$batch) < $maxbytes)
	{
		$offset = $iteration * $batch;
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, "http://127.0.0.1/api/v0/cat?arg=$ipfshash&offset=$offset&length=$batch");
		curl_setopt($tuCurl, CURLOPT_PORT , 5001);
		curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT, 5); // 5 seconds timeout
		curl_setopt($tuCurl, CURLOPT_POST, true); // 5 seconds timeout

		$tuData = curl_exec($tuCurl);
		$dl = strlen($tuData);
		//print("dl = $dl. iteration = $iteration batch = $batch maxbytes = $maxbytes\n");
		curl_close($tuCurl);
		fwrite($myfile, $tuData);
		if($dl < $batch)
		{
			$toobig = false;
			break;
		}
		$iteration += 1;
      //$curfilsize = filesize($filetmp);
	}
	fclose($myfile);
	if(!$toobig){
		rename($filetmp, $filefinal);
		return true;
	}
	else{
		unlink($filetmp);
		return false;
	}
}
?>

<?php
function getipfsfilefromnftstoragelink($ipfshash, $maxsize, $filefinal)
{
	if(strlen($ipfshash)!= 46)
	{
		return false;
	}
	$filetmp = $filefinal.".tmp";
	$myfile = fopen($filetmp, "w");
	if($myfile ===false)
	{
		//print("unable to open file $filetmp \n");
		return false;
	}
	$toobig = true;

	$tuCurl = curl_init();
	curl_setopt($tuCurl, CURLOPT_URL, "https://nftstorage.link/ipfs/$ipfshash");
	curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT, 5); // 5 seconds timeout
	curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, true); // 5 seconds timeout
	curl_setopt($tuCurl, CURLOPT_COOKIEJAR, 'amazoncookie.txt');
	curl_setopt($tuCurl, CURLOPT_COOKIEFILE, 'amazoncookie.txt');
 	$tuData = curl_exec($tuCurl);		
	//print("data = $tuData");
	$dl = strlen($tuData);
	$written = fwrite($myfile, $tuData);
	$closed = fclose($myfile);
	if($dl < $maxsize)
	{
		$toobig = false;
	}
	if(!$toobig){
		rename($filetmp, $filefinal);
		if(file_exists($filefinal))
		{
			return true;
		}
		else{
			return false;
		}
	}
	else{
		unlink($filetmp);
		return false;
	}
}
function getipfsfile($ipfshash, $maxsize, $filefinal)
{
	if(strlen($ipfshash)!= 46)
	{
		return false;
	}
	$maxbytes = strval(intval($maxsize*1024*1024)+10);
	$filetmp = $filefinal.".tmp";
	$myfile = fopen($filetmp, "w");
	if($myfile ===false)
	{
		//print("unable to open file $filetmp \n");
		return false;
	}
	$batch = 1024*100;
	$iteration = 0;
	$toobig = true;

	//print("\n\n\n\n--------------IPFS Start-------------\n");
	//print("hash = $ipfshash, batch = $batch, maxbytes = $maxbytes\nFilename = $filefinal\nfile temp=$filetmp");
	while((($iteration+1)*$batch) < $maxbytes)
	{
		$offset = $iteration * $batch;
		//print("iteration = $iteration, offset = $offset\n");
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, "http://127.0.0.1/api/v0/cat?arg=$ipfshash&offset=$offset&length=$batch");
		curl_setopt($tuCurl, CURLOPT_PORT , 5001);
		curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT, 5); // 5 seconds timeout
		curl_setopt($tuCurl, CURLOPT_POST, true); // 5 seconds timeout
 		$tuData = curl_exec($tuCurl);		
		$dl = strlen($tuData);
		curl_close($tuCurl);
		$written = fwrite($myfile, $tuData);
		//print("size returned from server = $dl, Size written to file = $written\n");
		if($dl < $batch)
		{
			$toobig = false;
			break;
		}
		$iteration += 1;
      //$curfilsize = filesize($filetmp);
	}
	$closed = fclose($myfile);
	//print("File closed? ".strval($closed)."\n");
	if(!$toobig){
		rename($filetmp, $filefinal);
		if(file_exists($filefinal))
		{
			return true;
		}
		else{
			return false;
		}
	}
	else{
		unlink($filetmp);
		return false;
	}
}
//getipfsfile('QmcKrWGGM6NRWadNZ7ciRb4MaUAV7D2sQMUetmHAjxHiDk', 999999, 'test.zip');
//getipfsfilefromnftstoragelink('QmSAeSLzyxGiQAdk2VShYkq1KUhor4xhCmGyQEr8bMKP34', 20, 'data.txt');
?>

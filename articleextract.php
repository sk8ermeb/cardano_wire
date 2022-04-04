<?php
require __DIR__ . '/blockfrost.php';
define( 'SHORTINIT', true );
require( '../../../wp-load.php' );

function blockarticleextract($oldest=null, $minada=10, $maxsize=10, $tags=[], $address=null)
{
	$articles = [];
	$apikey = getapi();
	//if(is_null($address))
	//{
	$articles = ArticleScan($apikey, 1);
	//}
	//else{
	//	$articles = AddressArticleScan($apikey, $address, 1);
	//	return;
	//}
	foreach($articles as $article)
	{
		if(!is_null($address))
		{
			if($article['owner'] !== $address)
			{
				print("Skipped ".$article['name']." wrong owner");
				continue;				
			}
		}
		if(!is_null($oldest))
		{
			$mintdt = new DateTime($article['mintdate']);
			if($mintdt<$oldest)
			{
				print("Skipped ".$article['name']." article too old");
				continue;
			}
		}
		$lovelace = intval($article['lovelace']);
		if($lovelace < ($minada *1000000))
		{
			print("Skipped ".$article['name']."Not enough lovelace");
			continue;
		}
		
		if(count($tags)>0){
			$found = false;
			foreach($tags as $tag){
				foreach($article['tags'] as $articletag){
					if($tag === $articletag){
						$found = true;
						break;
					}
				}
				if($found){
					break;
				}
			}
			if(!$found)
			{
				continue;
			}
		}
		$ipfshash = substr($article['ipfs'], 7);
		$maxbytes = strval(intval($maxsize*1024*1024)+10);
		
		$tuCurl = curl_init(); 
		curl_setopt($tuCurl, CURLOPT_URL, "http://127.0.0.1/api/v0/cat?arg=$ipfshash&offset=0&length=$maxbytes"); 
		curl_setopt($tuCurl, CURLOPT_PORT , 5001); 
		curl_setopt($tuCurl, CURLOPT_VERBOSE, 0); 
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT, 5); // 5 seconds timeout
		curl_setopt($tuCurl, CURLOPT_POST, true); // 5 seconds timeout

		$tuData = curl_exec($tuCurl); 
		curl_close($tuCurl);
		$ext = $article['ext'];
		$myfile = fopen("/var/www/html/nftfiles/$ipfshash.$ext.tmp", "w");
		fwrite($myfile, $tuData);
		fclose($myfile);

		print("extracted ".$article['name']);
	}
	print_r($articles);
}

//$mintdate = date('Y-m-d H:i:s');
//$dt = new DateTime($mintdate);
$addr = "addr1vxqxgmytq4tzxthz6dlfwj0mn3f9j5mvqlw6vehfxt84wxsw8elfe";
$addr = "addr1vy7xr3vuj8vxr47c9lzfzrl8z5hwdaj5eflrs04hnt34fnq0grylw";
$old = new DateTime("2022-03-29 18:04:45");
blockarticleextract($old, 1.9, 4, ['cool'], $addr);
?>

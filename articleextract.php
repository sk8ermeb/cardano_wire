<?php
require __DIR__ . '/blockfrost.php';
require __DIR__ . '/ipfs.php';
define( 'SHORTINIT', true );
require( '../../../wp-load.php' );

function blockarticleextract($oldest=null, $minada=10, $maxsize=10, $tags=[], $address=null)
{
	global $wpdb;
	$pagecount = 1;
	$articles = [];
	$apikey = getapi();
	$totalextracted = 0;
	$articles = ArticleScan($apikey, $pagecount);
	while($articles!==false)
{
	
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
		$maxbytes = intval($maxsize*1024*1024);
		$ext = $article['ext'];
		$filefinal = "/var/www/html/nftfiles/$ipfshash.$ext";
		$good = getipfsfile($ipfshash, $maxbytes, $filefinal);
		if(!$good){
			print("Skipped ".$article['name']." too big");
			unlink($filetmp);
			continue;
		}
		$table_name_article = $wpdb->prefix . "cardanowire_articlecache";
		$table_name_articletags = $wpdb->prefix . "cardanowire_article_tags";
		$res = $wpdb->insert($table_name_article, array(
			'name' => $article['name'],
			'location' => $filefinal,
			'ipfs' => $ipfshash,
			'addressowner' => $article['owner'],
			'stackedlovelace' => $article['lovelace'],
			'mintdate' => $article['mintdate'],
			'policy' => $article['policy'],
			'hash' => $article['sha'],
			'asset' => $article['asset']
		));
		$lastid = $wpdb->insert_id;
		foreach($article['tags'] as $tag)
		{
			$res = $wpdb->insert($table_name_articletags, array(
				'tag' => $tag,
				'article' => $lastid
			));
		}
		$totalextracted ++;
		print("extracted ".$article['name']."\n");
	}
	//print_r($articles);
	$pagecount ++;
	$articles = ArticleScan($apikey, $pagecount);
}
return $totalextracted;
}

//$mintdate = date('Y-m-d H:i:s');
//$dt = new DateTime($mintdate);
$addr = "addr1vxqxgmytq4tzxthz6dlfwj0mn3f9j5mvqlw6vehfxt84wxsw8elfe";
$addr = "addr1vy7xr3vuj8vxr47c9lzfzrl8z5hwdaj5eflrs04hnt34fnq0grylw";
$old = new DateTime("2022-03-29 18:04:45");
$cnt = blockarticleextract($old, 1.9, 4, ['cool'], $addr);
print(strval($cnt). " Articles extracted\n");
?>

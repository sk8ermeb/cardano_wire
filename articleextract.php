<?php
require __DIR__ . '/blockfrost.php';
require __DIR__ . '/ipfs.php';
define( 'SHORTINIT', true );
require_once( __DIR__.'/../../../wp-load.php' );

//function blockarticleextract($oldest=null, $minada=4, $maxsize=10, $tags=[], $address=null)
function blockarticleextract($criteria)
{
	$oldest = new DateTime("2022-04-10 18:04:45");
	if(array_key_exists('oldest', $criteria)){
		$oldest = $criteria['oldest'];
	}
	$minada=5;
	if(array_key_exists('minada', $criteria)){
		$minada = $criteria['minada'];
	}
	$maxsize=10;
	if(array_key_exists('maxsize', $criteria)){
		$maxsize = $criteria['maxsize'];
	}
	$tags=[];
	if(array_key_exists('tags', $criteria)){
		$tags = $criteria['tags'];
	}
	$address=null;
	if(array_key_exists('address', $criteria)){
		$address = $criteria['address'];
	}
	//echo "minada = $minada, max size = $maxsize, addr = $address, ".print_r($tags, true).", dt = ".$oldest->format('Y-m-d H:i:s')."<br/><br/>";
	$mediadir = __DIR__.'/../../uploads/cardano_wire/';
	global $wpdb;
	$pagecount = 1;
	$articles = [];
	$apikey = getapi();
	$totalextracted = 0;
	$articles = ArticleScan($apikey, $pagecount);
	//echo (print_r($articles, true));
	//echo "here??".print_r($articles, true);
	while($articles!==false)
	{
		//echo strval(count($articles))." on page scan ";	
	foreach($articles as $article)
	{
		if(!is_null($address))
		{
			if($article['owner'] !== $address)
			{
				print("Skipped ".$article['name']." wrong owner. \n");
				continue;				
			}
		}
		if(!is_null($oldest))
		{
			$mintdt = new DateTime($article['mintdate']);
			if($mintdt<$oldest)
			{
				print("Skipped ".$article['name']." article too old. \n");
				continue;
			}
		}
		$lovelace = intval($article['lovelace']);
		if($lovelace < ($minada *1000000))
		{
			print("Skipped ".$article['name']."Not enough lovelace. \n");
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
				echo "Skipped ".$article['name']." not tagged. \n";
				continue;
			}
		}
		//print("\n article:\n".print_r($article, true));
		$ipfshash = substr($article['ipfs'], 7);
		$maxbytes = intval($maxsize*1024*1024);
		$ext = $article['ext'];
		$filefinal = "$mediadir$ipfshash.$ext";
		//print("\n-----------------\n".$filefinal."\n---------------------\n");
		$options = get_option('cardano_wire_settings');
		$ipfs_selection = $options['ipfs_selection'];
		$good = false;
		if($ipfs_selection == 'nftstorage.link')
		{
			$good = getipfsfilefromnftstoragelink($ipfshash, $maxbytes, $filefinal);
		}
		else
		{
			$good = getipfsfile($ipfshash, $maxbytes, $filefinal);
		}
		if(!$good){
			print("Skipped ".$article['name']." too big or missing ipfs hash or file\n");
			if(file_exists($filetmp))
			{
				unlink($filetmp);
			}
			continue;
		}

		$table_name_article = $wpdb->prefix . "cardanowire_articlecache";
		$table_name_articletags = $wpdb->prefix . "cardanowire_article_tags";
		
		$sql = $wpdb->prepare("SELECT * FROM $table_name_article WHERE asset = %s;", $article['asset']);
  	$result = $wpdb->get_results($sql);
		if(count($result) > 0)
		{
			print("Skipping, ".$article['name']." article already in database\n");
			//print(" current article: ".print_r($result[0], true)."\n\nBlockchain article:".print_r($article, true)."\n");
			if($article['owner'] != $result[0]->addressowner)
			{
				$updated = $wpdb->update( $table_name_article, array( 'addressowner' => $article['owner']), array( 'id' => $result[0]->id ));
				print("NFT has new owner, updating = ".strval($updated)."\n");
			}
			continue;
		}
				

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
//$addr = "addr1vxqxgmytq4tzxthz6dlfwj0mn3f9j5mvqlw6vehfxt84wxsw8elfe";
//$addr = "addr1vy7xr3vuj8vxr47c9lzfzrl8z5hwdaj5eflrs04hnt34fnq0grylw";
//$old = new DateTime("2022-04-10 18:04:45");
//$cnt = blockarticleextract($old, 1.9, 4, ['cool'], $addr);
//$cnt = blockarticleextract($old);
//print(strval($cnt). " Articles extracted\n");
?>

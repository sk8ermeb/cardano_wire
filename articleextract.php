<?php
require __DIR__ . '/blockfrost.php';
define( 'SHORTINIT', true );
require( '../../../wp-load.php' );

function blockarticleextract($oldest=null, $minada=10, $maxsize=10, $tags=[], $address=null)
{
	$apikey = getapi();
	if(is_null($address))
	{
		$articles = gettxmetas($apikey, 1);
		foreach($articles as $article)
		{
			if(!is_null($oldest))
			{
				$mintdt = new DateTime($article['mintdate']);
				if($mintdt<$oldest)
				{
					print("Skipped ".$article['name']);
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
			print("minted ".$article['name']);
		}
		print_r($articles);
	}
}
//$mintdate = date('Y-m-d H:i:s');
//$dt = new DateTime($mintdate);
$old = new DateTime("2022-03-29 18:04:45");
blockarticleextract($old, 1.9, 2, ['cool']);
?>

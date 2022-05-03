<?php
require_once( __DIR__.'/../../../wp-load.php' );
/*
This function crafts the web request to the blockfrost API. 
This is a basic GET web request. The header information is 
['https':['method':'GET', 'header': 'project_id: <blockfrost api key'']]
This should return a json response. 
*/
function getfrosted($url, $apikey)
{
  $headers = array('http'=> array(
    'method' => 'GET',
    'header' => "project_id: $apikey"
    )
  );
	$context = stream_context_create($headers);
	$json = @file_get_contents($url, false, $context);
	//$json = file_get_contents($url, false, $context);
	if($json === false)
	{
		return false;
	}
	$results = json_decode($json);
	return $results;
	
}
/*
Not used except for debugging purposes. Can be ignored
*/
function getutxos($addr, $apikey)
{
	$headers = array('http'=> array(
		'method' => 'GET',
		'header' => "project_id: $apikey"
		)
	);
	$url = "https://cardano-mainnet.blockfrost.io/api/v0/addresses/$addr/utxos";
	$context = stream_context_create($headers);
	$json = file_get_contents($url, false, $context);
	$utxos = json_decode($json);
	foreach ($utxos as $utxo)
	{
		$txhash = $utxo->tx_hash;
		$outputs = $utxo->amount;
	}
	//https://cardano-mainnet.blockfrost.io/api/v0/txs/{hash}/utxos
}
/*
This gets the meta data of a particular NFT. Asset is synonomous with NFT
*/
function getasset($asset, $apikey)
{
	$url = "https://cardano-mainnet.blockfrost.io/api/v0/assets/$asset";
	$assetinfo = getfrosted($url, $apikey);
	return $assetinfo;
}
/*
Not used. Can be ignored
*/
function teststuff($apikey)
{
	$url = "https://cardano-mainnet.blockfrost.io/api/v0/metadata/txs/labels/721?count=1&page=2";
	$metas =  getfrosted($url, $apikey);
	print_r($metas);
}
/*
Not used. All ipfs gateways are in ipfs.php can beignored
*/
function IPFSGateway($apikey, $ipfshash)
{
	$url = "https://ipfs.blockfrost.io/api/v0/ipfs/gateway/$ipfshash";
	$metas =  getfrosted($url, $apikey);
	print_r($metas);
}
/*
The main function that pulls all article based NFTs from blockfrost using article key
1985. Oldest is retrieved first. 100 per page. Page 1 will contain the oldest article 
NFTs minted, and the scan should always start there.  
*/
function ArticleScan($apikey, $page=1)
{
	$url = "https://cardano-mainnet.blockfrost.io/api/v0/metadata/txs/labels/1985?page=$page";
	$metas =  getfrosted($url, $apikey);
	$nftarr = [];
	//Make sure we got a responce from blockfrost.
	if($metas === false)
	{
		return false;
	}
	//If we have reached the end of our scan and there are no more articles on the blockchain we need
	//To indicate that to the user so they can update our pointer. we keep the pointer the same
	//and stop scanning if the page isn't filled
	$endreached= false;
	if(count($metas) < 100)
	{
		$endreached=true;
	}
	/*this is a list of dictionaries (php objects). We iterate through each meta and pull the relevant info out
	//of the dictionary
	[$policy_id=>
		[$article_name in ascii encoded hex string=>
			[
				“name”=>article_name,
				“tags”=>article_tags,
				“sha256″=>zip sha256_hash,
				“ipfs”=>”ipfs://”.ipfs_hash,
				“ext”=>”zip”
			]
		]
	]
*/
	foreach ($metas as $meta)
	{
		//First there is a top level "policy" key for the dictionary
		//we get this policy since it is the key. That value contains 
		//a nested dictionary 
		$policy = key($meta->json_metadata);
		$nft = $meta->json_metadata->$policy;
		//nft name is a hex string to be decoded to an ascii string. Needs to be converted
		//This is the key to another nested dictionary
		$nft_name = key($nft);
		$nft_meta = $nft->$nft_name;
		//extension name
		$ext = $nft_meta->ext;
		//sha256 hash of .zip
		$sha = $nft_meta->sha256;
		//tags, comma separated. Need to trim whitespace from each tag
		$tags = $nft_meta->tags;
		$tagr = explode(',', $tags);
		$size = count($tagr);
		$tago = [];
		for ($x = 0; $x < $size; $x++) {
			$tag =  trim($tagr[$x], $characters = " \n\r\t\v\x00");
			if(strlen($tag)>0)
			{
				array_push($tago, $tag);
			}
		}
		//ipfs locations in the first 7 characters "ipfs://" need to be rmoved to 
		//pull from an ipfs gateway	
		$ipfs = $nft_meta->ipfs;

		//The complete asset is the policy and article name in hex concatonated together. This is how its
		//identified on the blockchain
		$hexname = bin2hex($nft_name);
		$asset = "$policy$hexname";
		//Now we need to get some additional meta info on the NFT that isn't explicitly stored
		//like mint date and owner with the asset name
		$assettxurl = "https://cardano-mainnet.blockfrost.io/api/v0/assets/$asset/transactions";
		$assettxs = getfrosted($assettxurl, $apikey);
		//Each asset is unique so we can just check the 0'th index. The only way this will fail 
		//is if blockfrost goes downn because the asset was derrived from the blolchain anyway
		$minttime = $assettxs[0]->block_time;
		$dt = new DateTime("@$minttime");  // convert UNIX timestamp to PHP DateTime
		$mintdate =  $dt->format('Y-m-d H:i:s'); // output = 2017-01-01 00:00:00
		//We get the current owner of the NFT, not the original minter. To do then get the
		//most recent transaction with that NFT. That also tells us how much ada is part of that
		//utxo for the spam protection.
		$lasttx = end($assettxs);
		$curtxofasset = $lasttx->tx_hash;

		$txutxourls = "https://cardano-mainnet.blockfrost.io/api/v0/txs/$curtxofasset/utxos";
		$utxos = getfrosted($txutxourls, $apikey);
		//lovelace is the smallest unit of ada. 1 ada = 1000000 ada
		$lovelace = 0;
		$assetaddr = "";
		foreach($utxos->outputs as $utxo)
		{
			$lovelace = 0;
			$assetaddr = "";
			$amounts = $utxo->amount;
			//will filter all the different items in the utxo (there could be multiple assets in additiona
			//to our NFT and lovelace
			foreach($amounts as $amount)
			{
				if($amount->unit === "lovelace")
				{
					$lovelace = $amount->quantity;
				}
				if($amount->unit === "$asset")
				{
					$assetaddr = $utxo->address;
				}
			}
			if($lovelace > 0 && $assetaddr != "")
			{
				//once we find our asset and lovelace we are done with teh this amount. Could run to the end
				//sense they are unique all the same	
				break;
			}
		}
		//we compile all of our needed return data into a nice dictionary to return it
		$nftentry = ['name'=>$nft_name, 'ipfs'=>$ipfs, 'mintdate'=>$mintdate, 'tags'=>$tago, 'policy'=>$policy, 'asset'=>$asset, 'lovelace'=>$lovelace, 'owner'=>$assetaddr, 'ext'=>$ext, 'sha'=>$sha];
		array_push($nftarr, $nftentry);
		
	}
	//TODO: still need to update teh calling function so it returns the $res instead
	$res = ['end'=>$endreached, 'nfts'=>$nftarr];
	return $nftarr;
}
//Wordpress specific function. Should probably be moved. This gets the api key for blockfrost 
//from the wordpress settings
function getapi()
{
	$options = get_option('cardano_wire_settings');
	$apikey = $options['blockfrost_cardano_api_key'];
	return $apikey;
}

/*
These are just debugging functions I sed to testing. You know how it is. 
*/
//getutxos('addr1vy7xr3vuj8vxr47c9lzfzrl8z5hwdaj5eflrs04hnt34fnq0grylw', $apikey);
//teststuff($apikey)
//gettxmetas($apikey);
//$apikey = getapi();
//print($apikey);
//IPFSGateway($apikey, "QmXeq9APEF3deRP5ZQu69YatuBZ5gmHY4juwVCqyoZ6TBd");
//teststuff($apikey);
?>

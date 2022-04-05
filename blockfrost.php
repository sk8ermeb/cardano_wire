<?php
function getfrosted($url, $apikey)
{
  $headers = array('http'=> array(
    'method' => 'GET',
    'header' => "project_id: $apikey"
    )
  );
	$context = stream_context_create($headers);
	$json = @file_get_contents($url, false, $context);
	if($json === false)
	{
		return false;
	}
	$results = json_decode($json);
	return $results;
	
}
function getutxos($addr, $apikey)
{
	$headers = array('http'=> array(
		'method' => 'GET',
		'header' => "project_id: $apikey"
		)
	);
	$url = "https://cardano-mainnet.blockfrost.io/api/v0/addresses/$addr/utxos";
	print(" $url \n\n");
	$context = stream_context_create($headers);
	$json = file_get_contents($url, false, $context);
	$utxos = json_decode($json);
	foreach ($utxos as $utxo)
	{
		$txhash = $utxo->tx_hash;
		$outputs = $utxo->amount;
		print_r($outputs);
	}
	//https://cardano-mainnet.blockfrost.io/api/v0/txs/{hash}/utxos
}
function getasset($asset, $apikey)
{
	$url = "https://cardano-mainnet.blockfrost.io/api/v0/assets/$asset";
	$assetinfo = getfrosted($url, $apikey);
	return $assetinfo;
}
function teststuff($apikey)
{
	$url = "https://cardano-mainnet.blockfrost.io/api/v0/metadata/txs/labels/721?count=1&page=2";
	$metas =  getfrosted($url, $apikey);
	print_r($metas);
}
/*
function AddressArticleScan($apikey, $address, $page=1)
{
	$url = "https://cardano-mainnet.blockfrost.io/api/v0/addresses/$address/utxos?page=$page";
	$utxos = getfrosted($url, $apikey);
	print("utxso: \n");
	print_r($utxos);
	print("\n\n");
	return $utxos;
}
*/
function ArticleScan($apikey, $page=1)
{
	$url = "https://cardano-mainnet.blockfrost.io/api/v0/metadata/txs/labels/1985?page=$page";
	$metas =  getfrosted($url, $apikey);
	$nftarr = [];
	if($metas === false)
	{
		return false;
	}
	foreach ($metas as $meta)
	{
		//print_r($meta->json_metadata);
		$policy = key($meta->json_metadata);
		$nft = $meta->json_metadata->$policy;
		$nft_name = key($nft);
		$nft_meta = $nft->$nft_name;
		$ext = $nft_meta->ext;
		$tags = $nft_meta->tags;
		$sha = $nft_meta->sha256;
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
	
		$ipfs = $nft_meta->article;
		//$mintdate = $nft_meta->mintdate;
		$hexname = bin2hex($nft_name);
		$asset = "$policy$hexname";
		//$asseturl = "https://cardano-mainnet.blockfrost.io/api/v0/assets/$asset";
		//print($asseturl);
		//$assitblockinfo =  getfrosted($asseturl, $apikey);
		//print("Asset block info=\n");
		//print_r($assitblockinfo);
		//$mintxhash=$assitblockinfo->initial_mint_tx_hash;

		$assettxurl = "https://cardano-mainnet.blockfrost.io/api/v0/assets/$asset/transactions";
		$assettxs = getfrosted($assettxurl, $apikey);
		$minttime = $assettxs[0]->block_time;
		//$epoch = 1483228800;
		$dt = new DateTime("@$minttime");  // convert UNIX timestamp to PHP DateTime
		$mintdate =  $dt->format('Y-m-d H:i:s'); // output = 2017-01-01 00:00:00
		$lasttx = end($assettxs);
		$curtxofasset = $lasttx->tx_hash;

		//print("\nCurrebt tx of asset=  $curtxofasset \n asset txs=\n");
		//print_r($assettxs);
		$txutxourls = "https://cardano-mainnet.blockfrost.io/api/v0/txs/$curtxofasset/utxos";
		$utxos = getfrosted($txutxourls, $apikey);
		//print("utxos=\n");
		//print_r($utxos);
		$lovelace = 0;
		$assetaddr = "";
		foreach($utxos->outputs as $utxo)
		{
			$lovelace = 0;
			$assetaddr = "";
			$amounts = $utxo->amount;
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
				break;
			}
		}
		$nftentry = ['name'=>$nft_name, 'ipfs'=>$ipfs, 'mintdate'=>$mintdate, 'tags'=>$tago, 'policy'=>$policy, 'asset'=>$asset, 'lovelace'=>$lovelace, 'owner'=>$assetaddr, 'ext'=>$ext, 'sha'=>$sha];
		array_push($nftarr, $nftentry);
		//print("lovelace on utxo is $lovelace and the owner is $assetaddr on transaction $curtxofasset");
		
	}
	//print("final results=\n");
	return $nftarr;
	//print_r($nftarr);
}
function getapi()
{
	$dir = dirname(__FILE__);
	$dir .= "/pass.txt";
	$myfile = fopen($dir, "r") or die("Unable to open file!");
	$jsontxt = fread($myfile,filesize($dir));
	fclose($myfile);
	$data = json_decode($jsontxt, true);
  $apikey = $data["apikey"];
	return $apikey;
}
//getutxos('addr1vy7xr3vuj8vxr47c9lzfzrl8z5hwdaj5eflrs04hnt34fnq0grylw', $apikey);
//teststuff($apikey)
//gettxmetas($apikey);
$apikey = getapi();
//teststuff($apikey);
?>

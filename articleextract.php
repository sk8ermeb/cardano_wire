<?php
require __DIR__ . '/blockfrost.php';
function blockarticleextract($oldest=null, $minada=10, $maxsize=10, $tags=[], $address=null)
{
	$apikey = getapi();
	if(is_null($address))
	{
		gettxmetas($apikey);
	}
}
blockarticleextract();
?>

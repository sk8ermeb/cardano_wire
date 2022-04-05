<?php

require __DIR__ . '/ipfs.php';
$ipfshash = 'QmYUPHGUALpqzfbbmX4s3FsNb8W6bCQCCtjZeeRoRALYAW';
$maxsize = 1.101;
$filefinal = '/var/www/html/nftfiles/QmYUPHGUALpqzfbbmX4s3FsNb8W6bCQCCtjZeeRoRALYAW.zip';
$good = getipfsfile($ipfshash, $maxsize, $filefinal);
if($good)
{
	print("success\n");

}
else{
	print("no good\n");
}

?>

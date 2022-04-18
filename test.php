<?php

//require __DIR__ . '/ipfs.php';
//define( 'SHORTINIT', true );
//require( '../../../wp-load.php' );


//$size = filesize('/var/www/html/pressmint/wordpress/wp-content/plugins/cardano_wire/../../uploads/cardano_wire/.zip');

//print(strval($size));

//$val = "a.df234";
//print(strval(floatval($val)));
//$olddt = new DateTime("2022-04-10 18:04:45");
/*$olddt = new DateTime("2022-04-10 18:04:45");
try{
$olddt = new DateTime("20sadfgfhs22-04-10 18:04:45");
}catch (Exception $e) {
	print(get_class($e));
	print("failed\n");
}
$result = $olddt->format('Y-m-d H:i:s');
print("$result\n");
*/

$data = explode(',', "abc , def, hij,klm, ");
foreach($data as $item)
{
	$len = strlen($item);
	print("len = $len, $item \n");
}
//print_r($data);
?>

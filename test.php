<?php

require __DIR__ . '/ipfs.php';
define( 'SHORTINIT', true );
require( '../../../wp-load.php' );

$a = 1;
$a ++;
$b = false;
print($a);
if($a!==false)
{
	print(" a not false\n");
}
if(!$b)
{
	print("not b\n");
}
?>

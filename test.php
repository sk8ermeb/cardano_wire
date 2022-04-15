<?php

require __DIR__ . '/ipfs.php';
define( 'SHORTINIT', true );
require( '../../../wp-load.php' );


$size = filesize('/var/www/html/pressmint/wordpress/wp-content/plugins/cardano_wire/../../uploads/cardano_wire/.zip');

print(strval($size));
?>

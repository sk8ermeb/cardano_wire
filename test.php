<?php

require __DIR__ . '/ipfs.php';
define( 'SHORTINIT', true );
require( '../../../wp-load.php' );

$filefinal = "/var/www/html/nftfiles/$ipfshash.$ext";
$table_name_article = $wpdb->prefix . "cardanowire_articlecache";
    $table_name_articletags = $wpdb->prefix . "cardanowire_article_tags";
    $res = $wpdb->insert($table_name_article, array(
      'location' => $filefinal
      //'ipfs' => $ipfshash,
      //'addressowner' => $article['owner'],
      //'stackedlovelace' => $article['lovelace'],
      //'mintdate' => $article['mintdate'],
      //'policy' => $article['policy'],
      //'asset' => $article['asset']
    ));
    $lastid = $wpdb->insert_id;

?>

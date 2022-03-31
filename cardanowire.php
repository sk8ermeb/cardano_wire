<?php
/**
 *  *
 *   *  * @package cardanowire
 *    *
 *     *   */

/*
 *  *
 *   * Plugin Name: Pressmint's cardanowire plugin
 *    *
 *     *
 *      *
 *       * */


//require __DIR__ . '/node.php';

register_activation_hook( __FILE__, 'cardanowire_install' );
function cardanowire_install () {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_name_article = $wpdb->prefix . "cardanowire_articlecache";
	$sql = "CREATE TABLE $table_name_article (
		id bigint NOT NULL AUTO_INCREMENT,
		status smallint NOT NULL DEFAULT 0,
		location text NOT NULL DEFAULT '',
		text text NOT NULL DEFAULT '',
		ipfs text NOT NULL DEFAULT '',
		hash text NOT NULL DEFAULT '',
		addressowner text NOT NULL DEFAULT '',
		stackedlovelace text NOT NULL DEFAULT '',
		PRIMARY KEY  (id)
	) $charset_collate;";
	dbDelta( $sql ); 

	$table_name_tags = $wpdb->prefix . "cardanowire_tags";
	$sql = "CREATE TABLE $table_name_tags (
		id bigint NOT NULL AUTO_INCREMENT,
		tag varchar(55) DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";
	dbDelta( $sql ); 
	
	$table_name_articletags = $wpdb->prefix . "cardanowire_article_tags";
	$sql = "CREATE TABLE $table_name_articletags (
		tag bigint NOT NULL,
		article bigint NOT NULL,
		FOREIGN KEY (article) REFERENCES $table_name_article(id),
		FOREIGN KEY (tag) REFERENCES $table_name_tags(id),
		PRIMARY KEY (tag,article)
	) $charset_collate;";
	dbDelta( $sql ); 
/*
require __DIR__ . '/nftdisplay.php';
require __DIR__ . '/contractdiplay.php';
add_filter( 'wpforms_smart_tags', 'wpf_dev_register_smarttag' );
add_filter( 'wpforms_smart_tag_process', 'wpf_dev_process_smarttag', 10, 5 );
add_action( 'wpforms_process_complete', 'wpf_dev_process_complete', 10, 4 );
add_action( 'wpforms_process', 'wpf_dev_process', 10, 3 );
add_filter( 'wpforms_process_redirect_url', 'wpf_dev_process_redirect_url', 10, 5 );
add_shortcode( 'nft_status' , 'nft_status');

function prefix_register_example_routes() {
	//register_rest_route( 'ezmint/v1', '/ipfsfileloaded(?P<file>', array(
	register_rest_route( 'ezmint/v1', '/ipfsfileloaded/', array(
		//'methods'  => WP_REST_Server::READABLE,
		'methods'  => 'GET',
		'callback' => 'check_ipfs_file_loaded',
		));
}
add_action( 'rest_api_init', 'prefix_register_example_routes' );

function nft_status()
{
	$ret = runnodecmd("query tip --mainnet");
	echo print_r($ret, True);
}
add_shortcode( 'nft_display' , 'nft_display');
add_shortcode( 'contract_display' , 'contract_display');

//'0000-00-00 00:00:00' 
register_activation_hook( __FILE__, 'ezmint_install' );
function ezmint_install () {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_name = $wpdb->prefix . "contract";
	$sql = "CREATE TABLE $table_name (
		uuid varchar(55) DEFAULT '' NOT NULL,
		payementaddress varchar(200) DEFAULT '' NOT NULL,
		vkey varchar(200) DEFAULT '' NOT NULL,
		skey varchar(200) DEFAULT '' NOT NULL,
		nftaddress varchar(200) DEFAULT '' NOT NULL,
		nftname varchar(200) DEFAULT '' NOT NULL,
		nfdescription varchar(200) DEFAULT '' NOT NULL,
		personaldata varchar(200) DEFAULT '' NOT NULL,
		email varchar(200) DEFAULT '' NOT NULL,
		fileloc varchar(200) DEFAULT '' NOT NULL,
		contractdate datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		mindate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		sha varchar(64) DEFAULT '' NOT NULL,
		status smallint NOT NULL DEFAULT 0,
		payement bigint NOT NULL DEFAULT 4000000,
		policy text NOT NULL DEFAULT '',
		PRIMARY KEY  (uuid)
	) $charset_collate;";
	dbDelta( $sql ); 
	
	$table_name = $wpdb->prefix . "nft";
	$sql = "CREATE TABLE $table_name (
		nftid varchar(300) DEFAULT '' NOT NULL,
		meta text NOT NULL DEFAULT '',
		PRIMARY KEY  (nftid)
	) $charset_collate;";

	dbDelta( $sql ); 
	$table_name = $wpdb->prefix . "userwallet";
	$sql = "CREATE TABLE $table_name (
		id bigint NOT NULL AUTO_INCREMENT,
		userid bigint NOT NULL DEFAULT 0,
		payementaddress varchar(200) DEFAULT '' NOT NULL,
		vkey varchar(200) DEFAULT '' NOT NULL,
		skey varchar(200) DEFAULT '' NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";
	dbDelta( $sql );
*/
}

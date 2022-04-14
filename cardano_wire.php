<?php
/*
 *
 * @package cardano_wire
 * @author Marvin Byrd
 * @copyright Marvin Byrd
 * @license           GPL-3.0
 * @wordpress-plugin
 * Plugin Name: Cardano Wire
 * Description: A plugin that allows the user to search and pull articles off the cardano block chain and ipfs file storage for review and publishing on their own site
 * Version:           1.0.0 
 * Author:            Pressmint.io
 *      *
 *       * */


require __DIR__ . '/settings.php';

//add_action('admin_menu', 'cardano_wire_setup_menu');
//function cardano_wire_setup_menu(){
//	        add_menu_page( 'Cardano Wire Settings Page', 'Cardano Wire', 'manage_options', 'cardano_wire', 'cardano_wire_settings' );
//}
//add_action( 'wp_nav_menu_item_custom_fields', 'my_menu_item_field' );
//add_action( 'wp_nav_menu_item_custom_fields', 'pr_menu_item_sub', 10, 2 );
//add_action( 'wp_update_nav_menu_item', 'save_menu_item_sub', 10, 2 );
//add_filter( 'nav_menu_item_title', 'show_menu_item_sub', 10, 2 );

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
		name text NOT NULL DEFAULT '',
		ipfs text NOT NULL DEFAULT '',
		hash text NOT NULL DEFAULT '',
		addressowner text NOT NULL DEFAULT '',
		stackedlovelace text NOT NULL DEFAULT '',
		mintdate datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		policy text NOT NULL DEFAULT '',
		asset text NOT NULL DEFAULT '',
		PRIMARY KEY  (id)
	) $charset_collate;";
	dbDelta( $sql ); 

	$table_name_articletags = $wpdb->prefix . "cardanowire_article_tags";
	$sql = "CREATE TABLE $table_name_articletags (
		tag varchar(55) DEFAULT '' NOT NULL,
		article bigint NOT NULL,
		FOREIGN KEY (article) REFERENCES $table_name_article(id),
		PRIMARY KEY (tag,article)
	) $charset_collate;";
	dbDelta( $sql ); 
}

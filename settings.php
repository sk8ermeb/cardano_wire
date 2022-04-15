<?php

add_action('admin_menu', 'cardano_wire_admin_add_page');
function cardano_wire_admin_add_page() {
	//add_options_page('Custom Plugin Page', 'Custom Plugin Menu', 'manage_options', 'plugin', 'plugin_options_page');
	add_menu_page('Cardano Wire', 'Cardano Wire', 'manage_options', 'cardano_wire', 'cardano_wire_options_page');
} 

function cardano_wire_options_page() {
?>
<div>
<h2>Cardano Wire</h2>
Configuration to pull data from the blockchain and ipfs
<form action="options.php" method="post">
<?php settings_fields('cardano_wire_settings'); ?>
<?php do_settings_sections('cardano_wire'); ?>
 
<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form></div>
 
<?php
}

function blockfrost_cardano_api_setting_string() {
	$options = get_option('cardano_wire_settings');
	echo "<input id='cardano_wire_settings_id' name='cardano_wire_settings[blockfrost_cardano_api_key]' size='40' type='text' value='{$options['blockfrost_cardano_api_key']}' />";
} 

add_action('admin_init', 'cardano_wire_admin_init');
function cardano_wire_admin_init(){
	register_setting( 'cardano_wire_settings', 'cardano_wire_settings', 'cardano_wire_options_validate' );
	add_settings_section('cardano_wire_main', 'Blockfrost Settings', 'cardano_wire_section_text', 'cardano_wire');
	add_settings_field('cardano_wire_settings_id', 'Blockfrost Cardano API Key', 'blockfrost_cardano_api_setting_string', 'cardano_wire', 'cardano_wire_main');
}

function cardano_wire_section_text() {
	echo '<p>Blockfrost Settings</p>';
}

function cardano_wire_options_validate($input) {
	return $input;
}

?>

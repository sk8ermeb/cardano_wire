<?php

add_action('admin_menu', 'cardano_wire_setup_menu');
 
function cardano_wire_setup_menu(){
    add_menu_page( 'Cardano Wire Settings', 'Cardano Wire', 'manage_options', 'cardano_wire', 'cardano_wire_init' );
}
 
function cardano_wire_init(){
?>
<div>
<h2>Cardano Wire Settings</h2>
Configuration for how to connect and extract data out of the blockchain and IPFS
<form action="options.php" method="post">
<?php settings_fields('plugin_options'); ?>
<?php do_settings_sections('plugin'); ?>
 
<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form></div>
 
<?php
    echo "<h1>Hello World!</h1>";
}
add_action('admin_init', 'cardano_wire_admin_init');
function cardano_wire_admin_init(){
	register_setting( 'plugin_options', 'plugin_options', 'plugin_options_validate' );
	add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
	add_settings_field('plugin_text_string', 'Plugin Text Input', 'plugin_setting_string', 'plugin', 'plugin_main');
}
function plugin_section_text() {
	echo '<p>Main description of this section here.</p>';
}

function plugin_setting_string() {
	$options = get_option('plugin_options');
	echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
} 
function plugin_options_validate($input) {
	$newinput['text_string'] = trim($input['text_string']);
	if(!preg_match('/^[a-z0-9]{32}$/i', $newinput['text_string'])) {
		$newinput['text_string'] = '';
	}
	return $newinput;
}






?>

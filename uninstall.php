<?php
/*
 * WPGear. Plugin Notes Label
 * ajax_note.php
 */	

	if( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) )
		exit();
	 
	global $wpdb;
	
	$PluginNotesLabel_options_table = $wpdb->prefix .'options';
	
	// Удаляем Options Плагина
	$Query = "DELETE FROM $PluginNotesLabel_options_table WHERE option_name LIKE 'plugin-note-label_%'";		
	$wpdb->query($Query);
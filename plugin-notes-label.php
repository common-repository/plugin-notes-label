<?php
/*
Plugin Name: Plugin Notes Label
Plugin URI: wpgear.xyz/plugin-notes-label/
Description: Add your Notes to each plugin.
Version: 4.17
Author: WPGear
Author URI: http://wpgear.xyz
License: GPLv2
*/

	$PluginNotesLabel_plugin_url = plugin_dir_url( __FILE__); // со слэшем на конце	
	
	$Upload_Dir = wp_get_upload_dir();	
	$PluginNotesLabel_upload_dir_path = $Upload_Dir['basedir'];	// без слэша на конце. Папка Uploads
	$PluginNotesLabel_upload_url_path = $Upload_Dir['baseurl'];	// без слэша на конце. Папка Uploads
	$PluginNotesLabel_File_Export_Name = 'plugin_notes_label_export';
	
	$PluginNotesLabel_Options = get_option("plugin-note-label_options", array());

	$PluginNotesLabel_Setup_ShowAuthor 	= (isset($PluginNotesLabel_Options['show_author'])) ? intval($PluginNotesLabel_Options['show_author']) : 1;
	$PluginNotesLabel_Setup_ShowDate 	= (isset($PluginNotesLabel_Options['show_date'])) ? intval($PluginNotesLabel_Options['show_date']) : 1;	
	
	/* Admin Console - Styles.
	----------------------------------------------------------------- */	
	function PluginNotesLabel_admin_style ($hook) {
		$screen = get_current_screen();
		$screen_base = $screen->base;			
	
		if ($screen_base == 'plugins') {
			// Страница Плагины.
			global $PluginNotesLabel_plugin_url;
			global $PluginNotesLabel_Setup_ShowAuthor, $PluginNotesLabel_Setup_ShowDate;
			
			$current_user = wp_get_current_user();	
			$User_Name = $current_user->user_login;			
			
			wp_enqueue_style ('plugin_note_label_style', $PluginNotesLabel_plugin_url .'admin-style.css');			

			wp_enqueue_script ('plugin_note_label', $PluginNotesLabel_plugin_url .'includes/plugin_note_label.js');			
			wp_localize_script ('plugin_note_label', 'PluginNotesLabel_VarObject', array( 
				'user' => $User_Name,
				'show_author' => $PluginNotesLabel_Setup_ShowAuthor,
				'show_date' => $PluginNotesLabel_Setup_ShowDate
			));	
		}
		
		if ($screen_base == 'plugin-notes-label/options') {
			// Страница Настрока "Plugin Notes Label"
			global $PluginNotesLabel_plugin_url;
			
			wp_enqueue_style ('plugin_note_label_option_style', $PluginNotesLabel_plugin_url .'option-style.css');
		}

		if ($screen_base == 'update-core') {	
			// Страница Обновлений.			
			$NoAction = isset($_REQUEST['action']) ? false : true;	

			if ($NoAction) {				
				// Не запускаемся, если запущен процесс Обновления.
				global $PluginNotesLabel_plugin_url;
				global $PluginNotesLabel_Setup_ShowAuthor, $PluginNotesLabel_Setup_ShowDate;

				$current_user = wp_get_current_user();	
				$User_Name = $current_user->user_login;

				wp_enqueue_style ('plugin_note_label_style', $PluginNotesLabel_plugin_url .'admin-style.css');

				wp_enqueue_script ('plugin_note_label_updatecore', $PluginNotesLabel_plugin_url .'includes/plugin_note_label_updatecore.js');				
				wp_localize_script ('plugin_note_label_updatecore', 'PluginNotesLabel_VarObject', array( 
					'user' => $User_Name,
					'show_author' => $PluginNotesLabel_Setup_ShowAuthor,
					'show_date' => $PluginNotesLabel_Setup_ShowDate
				));	

				wp_enqueue_script ('plugin_note_label', $PluginNotesLabel_plugin_url .'includes/plugin_note_label.js');
				wp_localize_script ('plugin_note_label', 'PluginNotesLabel_VarObject', array( 
					'user' => $User_Name,
					'show_author' => $PluginNotesLabel_Setup_ShowAuthor,
					'show_date' => $PluginNotesLabel_Setup_ShowDate
				));
			}
		}
	}
	add_action ('admin_enqueue_scripts', 'PluginNotesLabel_admin_style' );	
		
	/* Admin Console - Plugins page.
	----------------------------------------------------------------- */		
	function PluginNotesLabel_after_plugin_row ($plugin_file, $plugin_data, $status) {
		global $PluginNotesLabel_Setup_ShowAuthor, $PluginNotesLabel_Setup_ShowDate;
		
		$PluginData_Name = $plugin_data['Name'];
		$PluginData_Slug = isset ($plugin_data['slug']) ? $plugin_data['slug'] : sanitize_title ($PluginData_Name);
		
		$Plugin_Note = get_option("plugin-note-label_$PluginData_Slug", '');
		
		$Plugin_Note_Title = "";		

		if (is_array($Plugin_Note)) {
			$Plugin_Note_Content 	= $Plugin_Note['content'];
			$Plugin_Note_User 		= $Plugin_Note['user'];
			$Plugin_Note_Date 		= $Plugin_Note['date'];
			
			$Plugin_Note_Label = "Note";
			
			if ($Plugin_Note_Content) {
				if ($PluginNotesLabel_Setup_ShowAuthor) {
					$Plugin_Note_Label .= " [$Plugin_Note_User]";
				}

				if ($PluginNotesLabel_Setup_ShowDate) {
					$Plugin_Note_Label .= " <span class='pluginnotelabel-label-date'>$Plugin_Note_Date</span>";
				}	
			}

			$Plugin_Note_Label .= ":";
			
			if (!$PluginNotesLabel_Setup_ShowAuthor && !$PluginNotesLabel_Setup_ShowDate) {
				$Plugin_Note_Title = "Note by [$Plugin_Note_User] $Plugin_Note_Date";
			}
		} else {
			// нет данных или самая ранняя версия.
			$Plugin_Note_Content = $Plugin_Note;
			
			$Plugin_Note_Label = "Note:";
		}		
		
		$is_active = intval(is_plugin_active($plugin_file));		
		
		if ($PluginData_Slug) {			
			if ($is_active) {
				$PluginNote2_Box_Class = "pluginnotelabel-box-active";
			} else {
				$PluginNote2_Box_Class = "pluginnotelabel-box-inactive";
			}
		
			ob_start();
				?>
				<tr id='pluginnotelabel-box_<?php echo $PluginData_Slug; ?>'>
					<td colspan='100%' class='pluginnotelabel-box <?php echo $PluginNote2_Box_Class; ?>'>
						<span id='pluginnotelabel_control_<?php echo $PluginData_Slug; ?>' class='pluginnotelabel-label' title='Click to Edit Note' onclick='plugin_note_label_edit("<?php echo $PluginData_Slug; ?>", "<?php echo $Plugin_Note_Content; ?>")'><?php echo $Plugin_Note_Label; ?></span>
						<span id='pluginnotelabel_<?php echo $PluginData_Slug; ?>' class='pluginnotelabel-content' title='<?php echo $Plugin_Note_Title; ?>'><?php echo $Plugin_Note_Content; ?></span>
					</td>
				</tr>
				<?php
			ob_end_flush();
		}
	}	
	add_action('after_plugin_row', 'PluginNotesLabel_after_plugin_row', 999999, 3);
	
	/* Create plugin SubMenu
	----------------------------------------------------------------- */		
	function PluginNotesLabel_create_menu() {
		add_options_page(
			'Plugin Notes Label',
			'Plugin Notes Label',
			'publish_posts',
			'plugin-notes-label/options.php',
			''
		);	
	}
	add_action('admin_menu', 'PluginNotesLabel_create_menu');	
	
	/* AJAX Processing
	----------------------------------------------------------------- */
    add_action( 'wp_ajax_plugin_note_label', 'PluginNotesLabel_Ajax' );
    function PluginNotesLabel_Ajax(){		
		include_once ('includes/ajax_note.php');
    }
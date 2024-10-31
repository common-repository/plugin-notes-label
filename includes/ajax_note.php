<?php
/*
 * WPGear. Plugin Notes Label
 * ajax_note.php
 */	 
 
	$current_user = wp_get_current_user();	
	$User_Name 	= $current_user->user_login;
		
	$Mode 			= isset($_REQUEST['mode']) ? sanitize_text_field($_REQUEST['mode']) : null;
	$Note_Slug 		= isset($_REQUEST['slug']) ? sanitize_text_field($_REQUEST['slug']) : null;	
	$Note_Content	= isset($_REQUEST['note']) ? sanitize_text_field($_REQUEST['note']) : null;	
	$Names			= isset($_REQUEST['names']) ? sanitize_text_field($_REQUEST['names']) : null;		
	
	$Date = current_time('d.m.Y');
	$TimeStamp = null;
	$Plugin_Notes = array();

	global $PluginNotesLabel_upload_dir_path, $PluginNotesLabel_File_Export_Name;	

	$Result = false; 
	
	// Save Note
	if ($Mode == 'save_note') {			
		$Note = array(
			'user' => $User_Name,
			'content' => $Note_Content,
			'date' => $Date
		);
		
		update_option("plugin-note-label_$Note_Slug", $Note);
	
		$Result = true;	
	}

	// Export all Notes.
	if ($Mode == 'export') {
		global $wpdb;
		
		$PluginNotesLabel_options_table = $wpdb->prefix .'options';
		
		$Query = "SELECT * FROM $PluginNotesLabel_options_table WHERE (option_name LIKE 'plugin-note-label_%' AND option_name != 'plugin-note-label_options')";
		$Records = $wpdb->get_results ($Query);
		
		if ($Records) {
			$Option_Name 	= null;
			$Option_Value 	= null;;
			$Note 			= array();
			$Export_Notes 	= '';
			
			foreach ($Records as $Record) {
				$Option_Name 	= $Record->option_name;
				$Option_Value 	= $Record->option_value;
				
				$Note_Slug = substr($Option_Name, 18);
				
				if (is_serialized ($Option_Value)) {
					$Note_Content_Array = unserialize (trim ($Option_Value));							
					
					$Note = array (
						'slug' => $Note_Slug,
						'content' => $Note_Content_Array['content'],
						'user' => $Note_Content_Array['user'],							
						'date' => $Note_Content_Array['date'],
					);						
					
					$Export_Notes .= json_encode($Note, JSON_UNESCAPED_UNICODE)."\r\n";	
				} else {
					// нет данных или самая ранняя версия.
					if ($Option_Value) {
						$Note = array (
							'slug' => $Note_Slug,
							'content' => $Option_Value,
							'user' => $User_Name,								
							'date' => $Date,
						);
						
						$Export_Notes .= json_encode($Note, JSON_UNESCAPED_UNICODE)."\r\n";
					}
				}		
			}

			// Create File
			$PluginNotesLabel_Options = get_option("plugin-note-label_options", array());
			
			$TimeStamp = $PluginNotesLabel_Options['export'];
			
			if ($TimeStamp) {
				// Удаляем предыдущий Файл.
				$Upload_Dir_Path = $PluginNotesLabel_upload_dir_path .'/' .$PluginNotesLabel_File_Export_Name .'_' .$TimeStamp .'.txt';
				
				if (file_exists($Upload_Dir_Path)) {
					unlink ($Upload_Dir_Path);
				}
			}
			
			$TimeStamp = date("Ymdhis");
			$Upload_Dir_Path = $PluginNotesLabel_upload_dir_path .'/' .$PluginNotesLabel_File_Export_Name .'_' .$TimeStamp .'.txt';

			file_put_contents($Upload_Dir_Path, $Export_Notes);
			
			$PluginNotesLabel_Options['export'] = $TimeStamp;
			update_option('plugin-note-label_options', $PluginNotesLabel_Options);
		}			
		
		$Result = true;	
	}
	
	// Delete ALL Notes
	if ($Mode == 'clear') {			
		global $wpdb;
		
		$PluginNotesLabel_options_table = $wpdb->prefix .'options';
		
		$Query = "DELETE FROM $PluginNotesLabel_options_table WHERE (option_name LIKE 'plugin-note-label_%' AND option_name != 'plugin-note-label_options')";
		$wpdb->query($Query);

		$Result = true;				
	}	
	
	// Get Notes (Страница Обновлений)
	if ($Mode == 'get_notes') {
		if ($Names) {
			$All_Plugins = get_plugins();
								
			$Names = explode(',', $Names);				

			foreach ($All_Plugins as $key => $value) {
				$Plugin_Slug = substr($key, 0, stripos($key, "/"));	
				$Plugin_Name = $value['Name'];
				
				$Translated_Names = __($Plugin_Name, $Plugin_Slug);	
				
				foreach($Names as $Name) {
				    // Преобразуем HTML Сущности обратно в Символы
                    $Name = html_entity_decode($Name);
					
					if ($Name == $Translated_Names) {
						$Plugin_Note = get_option("plugin-note-label_$Plugin_Slug", '');
						
						if (is_array($Plugin_Note)) {
							// Нормальный набор данных.
						} else {
							// нет данных или самая ранняя версия.
							$Plugin_Note_Content = '';
							if ($Plugin_Note) {
								$Plugin_Note_Content = $Plugin_Note;
							} 
							
							$Plugin_Note = array();
							
							$Plugin_Note['content'] = $Plugin_Note_Content;
							$Plugin_Note['user'] = 'UFO';
							$Plugin_Note['date'] = '';
						}
						
						$Plugin_Note['slug'] = $Plugin_Slug;
					
						$Plugin_Notes[] = $Plugin_Note;			
					}
				}
			}
		}
		
		$Result = true;	
	}
	
	$Obj_Request = new stdClass();
	$Obj_Request->status 	= 'OK';
	$Obj_Request->answer 	= $Result;
	$Obj_Request->timestamp = $TimeStamp;
	$Obj_Request->notes 	= $Plugin_Notes;

	wp_send_json($Obj_Request);    

	die; // Complete.
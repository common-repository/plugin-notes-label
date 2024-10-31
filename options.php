<?php
/*
 * WPGear. Plugin Notes Label
 * options.php
 */
	global $PluginNotesLabel_Setup_ShowAuthor, $PluginNotesLabel_Setup_ShowDate;
	global $PluginNotesLabel_upload_dir_path, $PluginNotesLabel_upload_url_path, $PluginNotesLabel_File_Export_Name;
	
	$PluginNotesLabel_Action = isset($_REQUEST['action']) ? sanitize_text_field ($_REQUEST['action']) : null;	
	
	if ($PluginNotesLabel_Action == 'update') {
		// Save Options.
		$PluginNotesLabel_Setup_AdminOnly 	= (isset($_REQUEST['pluginnotelabel_option_adminonly'])) ? 1 : 0;
		$PluginNotesLabel_Setup_ShowAuthor 	= (isset($_REQUEST['pluginnotelabel_option_show_author'])) ? 1 : 0;
		$PluginNotesLabel_Setup_ShowDate 	= (isset($_REQUEST['pluginnotelabel_option_show_date'])) ? 1 : 0;
		
		$PluginNotesLabel_Options = get_option("plugin-note-label_options", array());

		$TimeStamp = $PluginNotesLabel_Options['export'];
		
		$PluginNotesLabel_Options = array(
			'adminonly' => $PluginNotesLabel_Setup_AdminOnly,
			'show_author' => $PluginNotesLabel_Setup_ShowAuthor,
			'show_date' => $PluginNotesLabel_Setup_ShowDate
		);	

		if ($TimeStamp) {
			$PluginNotesLabel_Options['export'] = $TimeStamp;
		}
		
		update_option('plugin-note-label_options', $PluginNotesLabel_Options);
	} 
	
	if ($PluginNotesLabel_Action == 'upload') {
		// Import Notes.
		$PluginNotesLabel_Import_File_MaxSize = 102400; // Максимальный допустимый для загрузки, размер файла. 100K
		
		$Date_Created = current_time("Y-m-d H:m");
		
		$File = isset($_FILES['pluginnotelabel_upload_file']) ? $_FILES['pluginnotelabel_upload_file'] : null;		
		
		if ($File) {
			// $File_Name 	= $File['name'];
			$File_Size 	= $File['size'];
			$File_Type 	= $File['type'];
			$File_Error = $File['error'];
			$File_Tmp 	= $File['tmp_name'];
			
			$Notes_Count = 0;

			if ($File_Size > 0 && $File_Size <= $PluginNotesLabel_Import_File_MaxSize && $File_Type == 'text/plain') {
				switch ($File_Error) {
					case 0:					
						// OK						
						if (file_exists($File_Tmp)) {		
							$File_Content = file_get_contents($File_Tmp);

							$File_Content_Array = explode(PHP_EOL, $File_Content);							
							
							foreach ($File_Content_Array as $Line) {
								if ($Line) {
									$Note = json_decode($Line, true);
									
									$Note_Slug = sanitize_text_field($Note['slug']);
									$Note_Content = sanitize_text_field($Note['content']);
									$Note_User = sanitize_text_field($Note['user']);
									$Note_Date = sanitize_text_field($Note['date']);
									
									unset($Note['slug']);
									
									if ($Note_Slug && $Note_Content) {
										// Добавляем 'НЕ пустые' Note.
										update_option("plugin-note-label_$Note_Slug", $Note);

										$Notes_Count = $Notes_Count + 1;
									}
								}	
							}
						}					

						break;
					case 3:	
						// ERROR_UPLOADING
						break;
					default:
						// SYSTEM_ERROR_UPLOADING
				}
			} else {
				// Errors
			}
			
			unlink ($File_Tmp);
							
			?>
			<script>
				// Post Processing. Messaging.
				window.addEventListener ('load', function() {
					var File_Error = <?php echo $File_Error; ?>;
					var File_Size = <?php echo $File_Size; ?>;
					var File_Size_Max = <?php echo $PluginNotesLabel_Import_File_MaxSize; ?>;
					var File_Type = '<?php echo $File_Type; ?>'
					var Notes_Count = <?php echo $Notes_Count; ?>;				
					
					if (File_Error == 0) {
						// May be - Success.
						if (File_Size > 0) {
							if (File_Size <= File_Size_Max) {
								if (File_Type == 'text/plain') {
									if (Notes_Count > 0) {
										// All OK
										document.getElementById("pluginnotelabel_message_box").innerHTML = Notes_Count + ' Notes successfully imported.';
										document.getElementById("pluginnotelabel_message_box").classList.add("pluginnotelabel_message_success");
									} else {
										// 0 Notes.
										document.getElementById("pluginnotelabel_message_box").innerHTML = 'File content No Notes...';
										document.getElementById("pluginnotelabel_message_box").classList.add("pluginnotelabel_message_warning");											
									}
								} else {
									// File_Type incorrect
									document.getElementById("pluginnotelabel_message_box").innerHTML = 'Error Uploading. File Type - incorrect';
									document.getElementById("pluginnotelabel_message_box").classList.add("pluginnotelabel_message_error");									
								}
							} else {
								// File_Size too Large
								document.getElementById("pluginnotelabel_message_box").innerHTML = 'Error Uploading. File Size too Large! Size = ' + File_Size;
								document.getElementById("pluginnotelabel_message_box").classList.add("pluginnotelabel_message_error");								
							}
						} else {
							// File_Size = 0
							document.getElementById("pluginnotelabel_message_box").innerHTML = 'Error Uploading. File Size = 0';
							document.getElementById("pluginnotelabel_message_box").classList.add("pluginnotelabel_message_error");								
						}
					} else {
						// Error
						document.getElementById("pluginnotelabel_message_box").innerHTML = 'Error Uploading! Cod = ' + File_Error;
						document.getElementById("pluginnotelabel_message_box").classList.add("pluginnotelabel_message_error");						
					}
					document.getElementById("pluginnotelabel_message_box").style.display = 'block';
				});		
			</script>									
			<?php			
		}			
	} 
	
	$PluginNotesLabel_Options = get_option("plugin-note-label_options", array());

	$PluginNotesLabel_Setup_AdminOnly = (isset($PluginNotesLabel_Options['adminonly'])) ? intval($PluginNotesLabel_Options['adminonly']) : 1;	
	
	if ($PluginNotesLabel_Setup_AdminOnly) {
		if (!current_user_can('edit_dashboard')) {
			?>
			<div class="pluginnotelabel_warning" style="margin: 40px;">
				Sorry, you are not allowed to view this page.
			</div>
			<?php
			
			return;
		}		
	}	
	
	?>
	<div class="wrap">
		<h2>Plugin Notes Label.</h2>
		
		<div id="pluginnotelabel_message_box" class="pluginnotelabel_message_box"></div>
		<hr>		
		<div class="pluginnotelabel_options_box">			
			<form name="form_PluginNotesLabel_Options" method="post" style="margin-top: 20px;">
				<div style="margin-top: 10px;">
					<label for="pluginnotelabel_option_adminonly" title="On/Off">
						Enable this Page for Admin only
					</label>
					<input id="pluginnotelabel_option_adminonly" name="pluginnotelabel_option_adminonly" type="checkbox" <?php if($PluginNotesLabel_Setup_AdminOnly) {echo 'checked';} ?>>
				</div>	

				<div style="margin-top: 10px; margin-left: 79px;">
					<label for="pluginnotelabel_option_show_author" title="On/Off">
						Show note Author.
					</label>
					<input id="pluginnotelabel_option_show_author" name="pluginnotelabel_option_show_author" type="checkbox" <?php if($PluginNotesLabel_Setup_ShowAuthor) {echo 'checked';} ?>>
				</div>		

				<div style="margin-top: 10px; margin-left: 91px;">
					<label for="pluginnotelabel_option_show_date" title="On/Off">
						Show note Date.
					</label>
					<input id="pluginnotelabel_option_show_date" name="pluginnotelabel_option_show_date" type="checkbox" <?php if($PluginNotesLabel_Setup_ShowDate) {echo 'checked';} ?>>
				</div>				
				
				<div style="margin-top: 10px; margin-bottom: 5px; text-align: right;">
					<input id="pluginnotelabel_btn_options_save" type="submit" class="button button-primary" style="margin-right: 5px;" value="Save">
				</div>
				<input id="action" name="action" type="hidden" value="update">			
			</form>
			
			<hr>
			
			<h3>Export - Import</h3>

			<div id="pluginnotelabel_confirm_clear_box" class="pluginnotelabel_confirm_clear_box" style="display: none;">
				<div>
					<div class="pluginnotelabel_confirm_clear_box_title">All Notes will be deleted!</div>
				</div>
				<input id="pluginnotelabel_btn_clear_confirm" type="button" class="button" style="margin-right: 5px;" onclick="Do_PluginNotesLabel_clear()" value="Confirm">
				<input id="pluginnotelabel_btn_clear_cancel" type="button" class="button" style="margin-right: 5px;" onclick="Do_PluginNotesLabel_cancel()" value="Cancel">
				<span id="pluginnotelabel_indicator_processing_clear" class="pluginnotelabel_indicator_processing_clear" style="display: none;">...processing...</span>
			</div>
					
			<div style="float: right;">
				<div style="margin-top: 10px; margin-bottom: 5px;">
					<input id="pluginnotelabel_btn_clear" type="button" class="button" style="margin-right: 5px;" onclick="Do_Confirm_PluginNotesLabel_clear()" value="Clear All Notes">					
				</div>				
			</div>
			
			<div style="float: left;">
				<div style="margin-top: 10px; margin-bottom: 5px;">
					<input id="pluginnotelabel_btn_export" type="button" class="button" style="margin-right: 5px;" onclick="Do_PluginNotesLabel_export()" value="Export Notes">
					<span id="pluginnotelabel_indicator_processing_export" style="display: none;">...processing...</span>		
				</div>			

				<div style="margin-top: 10px; margin-bottom: 5px;">
					<input id="pluginnotelabel_btn_import" type="button" class="button" style="margin-right: 5px;" onclick="Enable_PluginNotesLabel_UploadForm()" value="Import Notes">	
					
					<form id="form_PluginNotesLabel_Upload" name="form_PluginNotesLabel_Upload" method="post" enctype="multipart/form-data" style="display: none;">
						<input type="hidden" name="action" value="upload"/>
						
						<div>
							<input id="pluginnotelabel_upload_file" type="file" onchange="Enable_PluginNotesLabel_UploadBtn()" name="pluginnotelabel_upload_file" value="">
						</div>
						
						<div id="pluginnotelabel_upload_btn" style="display: none;">
							<input type="submit" class="button button-primary" onclick="return Check_PluginNotesLabel_FormSaveFile ();" name="pluginnotelabel_upload_btn" value="Upload Notes">
						</div>
					</form>				
				</div>			
			</div>
		</div>			
	</div>
	
	<script>
		// Export all Notes.
		function Do_PluginNotesLabel_export() {
			var File_Upload_Path = '<?php echo $PluginNotesLabel_upload_url_path; ?>';
			var File_Name = '<?php echo $PluginNotesLabel_File_Export_Name; ?>';			
			var Download_Name = 'plugin_notes_label_export.txt';
			
			var Download_URL = File_Upload_Path + '/' + File_Name;
			
			Do_PluginNotesLabel_cancel();
			
			document.getElementById("pluginnotelabel_indicator_processing_export").style.display = 'inline-block';
			
			var PluginNote2_Ajax_URL = ajaxurl;
			var PluginNote2_Ajax_Data = 'action=plugin_note_label&mode=export';	
						
			jQuery.ajax({
				type:"POST",
				url: PluginNote2_Ajax_URL,
				dataType: 'json',
				data: PluginNote2_Ajax_Data,
				cache: false,
				success: function(jsondata) {
					var Obj_Request = jsondata;	
					
					var Status = Obj_Request.status;
					var Answer = Obj_Request.answer;
					var TimeStamp = Obj_Request.timestamp;

					document.getElementById("pluginnotelabel_indicator_processing_export").style.display = 'none';
					
					if (TimeStamp) {
						// Download
						Download_URL = Download_URL + '_' + TimeStamp + '.txt';

						var Download_Link = document.createElement("a");
						
						Download_Link.setAttribute('download', Download_Name);
						Download_Link.href = Download_URL;
						document.body.appendChild(Download_Link);
						
						Download_Link.click();
						Download_Link.remove();
					} else {
						// No Notes for Export
						document.getElementById("pluginnotelabel_message_box").innerHTML = 'No Notes for Export.';
						document.getElementById("pluginnotelabel_message_box").classList.add("pluginnotelabel_message_warning");
						document.getElementById("pluginnotelabel_message_box").style.display = 'block';						
					}
				}
			});							
		}
		
		// Import Notes.
		function Enable_PluginNotesLabel_UploadForm() {
			Do_PluginNotesLabel_cancel();

			document.getElementById("form_PluginNotesLabel_Upload").style.display = 'inline-block';
		}

		function Enable_PluginNotesLabel_UploadBtn() {
			document.getElementById("pluginnotelabel_upload_btn").style.display = 'block';
		}
		
		function Check_PluginNotesLabel_FormSaveFile () {
			var File_Name = document.getElementById('pluginnotelabel_upload_file').files[0].name;
	
			if (File_Name == "") {
				alert('please Select File.');
				
				return false;
			}

			return true;
		}

		// Confirmation Clear Notes.
		function Do_Confirm_PluginNotesLabel_clear() {
			Do_PluginNotesLabel_cancel();
			
			document.getElementById("pluginnotelabel_confirm_clear_box").style.display = 'block';			
			document.getElementById("pluginnotelabel_btn_clear_confirm").style.display = 'inline-block';
			document.getElementById("pluginnotelabel_btn_clear_cancel").style.display = 'inline-block';		
		}
		
		// Clear Notes.
		function Do_PluginNotesLabel_clear() {
			document.getElementById("pluginnotelabel_btn_clear_confirm").style.display = 'none';
			document.getElementById("pluginnotelabel_btn_clear_cancel").style.display = 'none';
			document.getElementById("pluginnotelabel_indicator_processing_clear").style.display = 'block';
			
			var PluginNote2_Ajax_URL = ajaxurl;
			var PluginNote2_Ajax_Data = 'action=plugin_note_label&mode=clear';	
						
			jQuery.ajax({
				type:"POST",
				url: PluginNote2_Ajax_URL,
				dataType: 'json',
				data: PluginNote2_Ajax_Data,
				cache: false,
				success: function(jsondata) {
					var Obj_Request = jsondata;	
					
					var Status = Obj_Request.status;
					var Answer = Obj_Request.answer;					
					
					if (Answer) {
						document.getElementById("pluginnotelabel_confirm_clear_box").style.display = 'none';
						
						document.getElementById("pluginnotelabel_message_box").innerHTML = 'All Notes successfully Deleted.';
						document.getElementById("pluginnotelabel_message_box").classList.add("pluginnotelabel_message_success");
						document.getElementById("pluginnotelabel_message_box").style.display = 'block';						
					}
				}
			});				
		}
		
		// Cancel Clear Notes.
		function Do_PluginNotesLabel_cancel() {
			document.getElementById("pluginnotelabel_message_box").style.display = 'none';
			document.getElementById("pluginnotelabel_confirm_clear_box").style.display = 'none';
			document.getElementById("form_PluginNotesLabel_Upload").style.display = 'none';			
			document.getElementById("pluginnotelabel_indicator_processing_clear").style.display = 'none';			
		}
	</script>

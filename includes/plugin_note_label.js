// WPGear. Plugin Notes Label
// plugin_note_label.js

var PluginNotesLabel_User_Name 			= PluginNotesLabel_VarObject.user;
var PluginNotesLabel_Setup_ShowAuthor 	= PluginNotesLabel_VarObject.show_author;
var PluginNotesLabel_Setup_ShowDate 	= PluginNotesLabel_VarObject.show_date;

	// Note Edit.	
	function plugin_note_label_edit (Plugin_Slug, Plugin_Note) {
		var Note_Box;
	
		Note_Box = '<div class="pluginnotelabel-box-edit"><textarea id="pluginnotelabel_box_edit_content" class="pluginnotelabel-box-edit-content">' + Plugin_Note + '</textarea>';
		Note_Box += '<input id="pluginnotelabel_box_edit_btn_save" type="button" class="button button-primary" value="Save Note">';
		Note_Box += '<input id="pluginnotelabel_box_edit_btn_cancel" type="button" class="button" style="margin-left: 10px;" value="Cancel"></div>';
		
		document.getElementById("pluginnotelabel_" + Plugin_Slug).innerHTML = Note_Box;
				
		var PluginNoteLabel_Btn_Save = document.getElementById("pluginnotelabel_box_edit_btn_save");
		var PluginNoteLabel_Btn_Cancel = document.getElementById("pluginnotelabel_box_edit_btn_cancel");

		// Save
		PluginNoteLabel_Btn_Save.addEventListener("click", function(e) {
			var PluginNoteLabel_Content = document.getElementById("pluginnotelabel_box_edit_content").value;
					
			document.getElementById("pluginnotelabel_" + Plugin_Slug).innerHTML = PluginNoteLabel_Content;
			document.getElementById("pluginnotelabel_" + Plugin_Slug).style.color = 'darkgrey';
			document.getElementById("pluginnotelabel_control_" + Plugin_Slug).setAttribute('onclick','plugin_note_label_edit("' + Plugin_Slug + '", "' + PluginNoteLabel_Content + '")');

			var PluginNoteLabel_Ajax_URL = ajaxurl;
			var PluginNoteLabel_Ajax_Data = 'action=plugin_note_label&mode=save_note&slug=' + Plugin_Slug + '&note=' + PluginNoteLabel_Content;	
			//Save Note
				jQuery.ajax({
					type:"POST",
					url: PluginNoteLabel_Ajax_URL,
					dataType: 'json',
					data: PluginNoteLabel_Ajax_Data,
					cache: false,
					success: function(jsondata) {
						var Obj_Request = jsondata;	
						
						var Status = Obj_Request.status;
						var Answer = Obj_Request.answer;
						
						console.log('Note saved.');
						
						var Plugin_Note_Label = "Note";
						
						if (PluginNotesLabel_Setup_ShowAuthor == '1') {
							Plugin_Note_Label += " [" + PluginNotesLabel_User_Name + "]";
						}
						
						if (PluginNotesLabel_Setup_ShowDate == '1') {
							var CurentDay = new Date();
							
							CurentDay = CurentDay.toLocaleDateString();
							
							Plugin_Note_Label += " <span class='pluginnotelabel-label-date'>" + CurentDay + "</span>";
						}

						Plugin_Note_Label += ":";
						
						document.getElementById("pluginnotelabel_control_" + Plugin_Slug).innerHTML = Plugin_Note_Label;
						document.getElementById("pluginnotelabel_" + Plugin_Slug).style.color = 'black';
					}
				});			
			}, false);
		
		// Cancel
		PluginNoteLabel_Btn_Cancel.addEventListener("click", function(e) {
			document.getElementById("pluginnotelabel_" + Plugin_Slug).innerHTML = Plugin_Note;
			}, false);		
	}	
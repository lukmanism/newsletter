
var PommoFileManager = {
	ckeditorCallback: 0,
	field: '',
	language: {},
	hostname: '',
	imageDirectory: 'uploadedimages/data/',
	load: function(ckeditorCallback, field, hostname, language) {
		this.ckeditorCallback = ckeditorCallback;
		this.field = field;
		this.language = language;
		this.hostname = hostname;
	}
}

$(document).ready(function() { 
	
	PommoDialog.init(false, {ajax: false});
	
	$('#column-left').tree({
		data: { 
			type: 'json',
			async: true, 
			opts: { 
				method: 'post', 
				url: 'ajax/filemanager.php?action=directory'
			} 
		},
		selected: 'top',
		ui: {		
			theme_name: 'classic',
			animation: 300
		},	
		types: { 
			'default': {
				clickable: true,
				creatable: false,
				renameable: false,
				deletable: false,
				draggable: false,
				max_children: -1,
				max_depth: -1,
				valid_children: 'all'
			}
		},
		callback: {
			beforedata: function(NODE, TREE_OBJ) { 
				if (NODE == false) {
					TREE_OBJ.settings.data.opts.static = [ 
						{
							data: 'images',
							attributes: { 
								'id': 'top',
								'directory': ''
							}, 
							state: 'closed'
						}
					];
					
					return { 'directory': '' } 
				} else {
					TREE_OBJ.settings.data.opts.static = false;  
					
					return { 'directory': $(NODE).attr('directory') } 
				}
			},		
			onselect: function (NODE, TREE_OBJ) {
				$.ajax({
					url: 'ajax/filemanager.php?action=files',
					type: 'post',
					data: 'directory=' + encodeURIComponent($(NODE).attr('directory')),
					dataType: 'json',
					success: function(json) {
						html = '<div>';
						
						if (json) {
							for (i = 0; i < json.length; i++) {
								name = '';
								
								filename = json[i]['filename'];
								
								for (j = 0; j < filename.length; j = j + 15) {
									name += filename.substr(j, 15) + '<br />';
								}
								
								name += json[i]['size'];
								
								html += '<a>' + name + '<input type="hidden" name="image" value="' + json[i]['file'] + '" /></a>';
							}
						}
						
						html += '</div>';
						
						$('#column-right').html(html);
						
						$('#column-right a').each(function(index, element) {
							$.ajax({
								url: 'ajax/filemanager.php?action=image&image=' + encodeURIComponent($(element).find('input[name=\'image\']').attr('value')),
								dataType: 'html',
								success: function(html) {
									$(element).prepend('<img src="' + html + '" title="" style="display: none;" /><br />');
									
									$(element).find('img').fadeIn();
								}
							});
						});
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}
	});	
	
	$('#column-right a').live('click', function() {
		if ($(this).attr('class') == 'selected') {
			$(this).removeAttr('class');
		} else {
			$('#column-right a').removeAttr('class');
			
			$(this).attr('class', 'selected');
		}
	});
	
	$('#column-right a').live('dblclick', function() {
		var fileRelativePath = encodeURIComponent($(this).find('input[name=\'image\']').attr('value'));
		fileRelativePath = fileRelativePath.replace(/^%2F/, ""); // Remove prepended slash
		fileRelativePath = fileRelativePath.replace(/%2F/g, "/"); // Decode back slashes
		
		if (PommoFileManager.ckeditorCallback) {
			window.opener.CKEDITOR.tools.callFunction(PommoFileManager.ckeditorCallback, PommoFileManager.hostname + PommoFileManager.imageDirectory + fileRelativePath);
			
			self.close();	
		} else {
			parent.$('#' + PommoFileManager.field).attr('value', PommoFileManager.hostname + PommoFileManager.imageDirectory + fileRelativePath);
			parent.$('#dialog').dialog('close');
			
			parent.$('#dialog').remove();	
		}
	});		
						
	$('#create').bind('click', function() {
		var tree = $.tree.focused();
		
		if (tree.selected) {
			$('#folderDialog').jqmShow();
			$('#folderDialog .textInput').val('');
		} else {
			alert(PommoFileManager.language.error_directory);	
		}
	});
	
	$('#folderDialog input[type=\'button\']').bind('click', function() {
		var tree = $.tree.focused();
		
		$.ajax({
			url: 'ajax/filemanager.php?action=create',
			type: 'post',
			data: 'directory=' + encodeURIComponent($(tree.selected).attr('directory')) + '&name=' + encodeURIComponent($('#folderDialog input[name=\'name\']').val()),
			dataType: 'json',
			success: function(json) {
				if (json.success) {
					$('#folderDialog').jqmHide();
					
					tree.refresh(tree.selected);
					
					alert(json.success);
				} else {
					alert(json.error);
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
	
	$('#delete').bind('click', function() {
		path = $('#column-right a.selected').find('input[name=\'image\']').attr('value');
							 
		if (path) {
			var fileName = path.substr(path.lastIndexOf('/') + 1);
			if (confirm("Are you sure you want to delete '" + fileName + "'?")) {
				$.ajax({
					url: 'ajax/filemanager.php?action=delete',
					type: 'post',
					data: 'path=' + encodeURIComponent(path),
					dataType: 'json',
					success: function(json) {
						if (json.success) {
							var tree = $.tree.focused();
						
							tree.select_branch(tree.selected);
							
							alert(json.success);
						}
						
						if (json.error) {
							alert(json.error);
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		} else {
			var tree = $.tree.focused();
			
			if (tree.selected) {
				var directoryName = $(tree.selected).attr('directory');
				directoryName = directoryName.substr(directoryName.lastIndexOf('/') + 1);
				
				if (confirm("Are you sure you want to delete the folder: '" + directoryName + "'?")) {
					$.ajax({
						url: 'ajax/filemanager.php?action=delete',
						type: 'post',
						data: 'path=' + encodeURIComponent($(tree.selected).attr('directory')),
						dataType: 'json',
						success: function(json) {
							if (json.success) {
								tree.select_branch(tree.parent(tree.selected));
								
								tree.refresh(tree.selected);
								
								alert(json.success);
							} 
							
							if (json.error) {
								alert(json.error);
							}
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});		
				}
			} else {
				alert(PommoFileManager.language.error_select);
			}			
		}
	});
	
	$('#move').bind('click', function() {
		
		$('#moveDialog').jqmShow();

		$('#moveDialog select[name=\'to\']').load('ajax/filemanager.php?action=folders');
	});
	
	$('#moveDialog input[type=\'button\']').bind('click', function() {
		path = $('#column-right a.selected').find('input[name=\'image\']').attr('value');
						 
		if (path) {																
			$.ajax({
				url: 'ajax/filemanager.php?action=move',
				type: 'post',
				data: 'from=' + encodeURIComponent(path) + '&to=' + encodeURIComponent($('#moveDialog select[name=\'to\']').val()),
				dataType: 'json',
				success: function(json) {
					if (json.success) {
						$('#moveDialog').jqmHide();
						
						var tree = $.tree.focused();
						
						tree.select_branch(tree.selected);
						
						alert(json.success);
					}
					
					if (json.error) {
						alert(json.error);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		} else {
			var tree = $.tree.focused();
			
			$.ajax({
				url: 'ajax/filemanager.php?action=move',
				type: 'post',
				data: 'from=' + encodeURIComponent($(tree.selected).attr('directory')) + '&to=' + encodeURIComponent($('#moveDialog select[name=\'to\']').val()),
				dataType: 'json',
				success: function(json) {
					if (json.success) {
						$('#moveDialog').jqmHide();
						
						tree.select_branch('#top');
							
						tree.refresh(tree.selected);
						
						alert(json.success);
					}						
					
					if (json.error) {
						alert(json.error);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});				
		}
	});

	$('#copy').bind('click', function() {
		$('#copyDialog').jqmShow();
		$('#copyDialog .textInput').val('');
	});
	
	$('#copyDialog input[type=\'button\']').bind('click', function() {
		path = $('#column-right a.selected').find('input[name=\'image\']').attr('value');
						 
		if (path) {																
			$.ajax({
				url: 'ajax/filemanager.php?action=copy',
				type: 'post',
				data: 'path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent($('#copyDialog input[name=\'name\']').val()),
				dataType: 'json',
				success: function(json) {
					if (json.success) {
						$('#copyDialog').jqmHide();
						
						var tree = $.tree.focused();
						
						tree.select_branch(tree.selected);
						
						alert(json.success);
					}						
					
					if (json.error) {
						alert(json.error);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		} else {
			var tree = $.tree.focused();
			
			$.ajax({
				url: 'ajax/filemanager.php?action=copy',
				type: 'post',
				data: 'path=' + encodeURIComponent($(tree.selected).attr('directory')) + '&name=' + encodeURIComponent($('#copyDialog input[name=\'name\']').val()),
				dataType: 'json',
				success: function(json) {
					if (json.success) {
						$('#copyDialog').jqmHide();
						
						tree.select_branch(tree.parent(tree.selected));
						
						tree.refresh(tree.selected);
						
						alert(json.success);
					} 						
					
					if (json.error) {
						alert(json.error);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});				
		}
	});	
	
	$('#rename').bind('click', function() {
		var fileName = $('#column-right a.selected').find('input[name=\'image\']').attr('value');
		if (fileName) {
			fileName = fileName.substr(fileName.lastIndexOf('/') + 1);
			fileName = fileName.substr(0, fileName.lastIndexOf('.'));
		} else {
			fileName = $($.tree.focused().selected).attr('directory');
			fileName = fileName.substr(fileName.lastIndexOf('/') + 1);
		}
		
		$('#renameDialog .textInput').val(fileName);
		$('#renameDialog').jqmShow();
	});
	
	$('#renameDialog input[type=\'button\']').bind('click', function() {
		path = $('#column-right a.selected').find('input[name=\'image\']').attr('value');
						 
		if (path) {		
			$.ajax({
				url: 'ajax/filemanager.php?action=rename',
				type: 'post',
				data: 'path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent($('#renameDialog input[name=\'name\']').val()),
				dataType: 'json',
				success: function(json) {
					if (json.success) {
						$('#renameDialog').jqmHide();
						
						var tree = $.tree.focused();
				
						tree.select_branch(tree.selected);
						
						alert(json.success);
					} 
					
					if (json.error) {
						alert(json.error);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});			
		} else {
			var tree = $.tree.focused();
			
			$.ajax({ 
				url: 'ajax/filemanager.php?action=rename',
				type: 'post',
				data: 'path=' + encodeURIComponent($(tree.selected).attr('directory')) + '&name=' + encodeURIComponent($('#renameDialog input[name=\'name\']').val()),
				dataType: 'json',
				success: function(json) {
					if (json.success) {
						$('#renameDialog').jqmHide();
							
						tree.select_branch(tree.parent(tree.selected));
						
						tree.refresh(tree.selected);
						
						alert(json.success);
					} 
					
					if (json.error) {
						alert(json.error);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
	
	new AjaxUpload('#upload', {
		action: 'ajax/filemanager.php?action=upload',
		name: 'image',
		autoSubmit: false,
		responseType: 'json',
		onChange: function(file, extension) {
			var tree = $.tree.focused();
			
			if (tree.selected) {
				this.setData({'directory': $(tree.selected).attr('directory')});
			} else {
				this.setData({'directory': ''});
			}
			
			this.submit();
		},
		onSubmit: function(file, extension) {
			$('#upload').append('<img src="themes/shared/images/loader.gif" class="loading" style="padding-left: 5px;" />');
		},
		onComplete: function(file, json) {
			if (json.success) {
				var tree = $.tree.focused();
					
				tree.select_branch(tree.selected);
				
				alert(json.success);
			}
			
			if (json.error) {
				alert(json.error);
			}
			
			$('.loading').remove();	
		}
	});
	
	$('#refresh').bind('click', function() {
		var tree = $.tree.focused();
		
		tree.refresh(tree.selected);
	});	
});
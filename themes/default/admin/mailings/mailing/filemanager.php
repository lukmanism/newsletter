<?php 
ob_start();
?>
<script type="text/javascript" src="<?php echo $this->url['theme']['shared']; ?>js/jq/jstree/jquery.tree.min.js"></script>
<script type="text/javascript" src="<?php echo $this->url['theme']['shared']; ?>js/ajaxupload.js"></script>
<script type="text/javascript" src="<?php echo $this->url['theme']['shared']; ?>js/filemanager.js"></script>
	
<link href="<?php echo $this->url['theme']['shared']; ?>css/default.filemanager.css" type="text/css" rel="stylesheet" />
<?php
include $this->template_dir.'/inc/ui.dialog.php';
$this->capturedHead = ob_get_clean();

// Works from Version 1.3.0 up to 1.8.3, however jQuery 1.8.x introduces a small 
// bug with jsTree where you have to click twice when expanding the first folder tree.
$this->jQueryVersion = '1.7.2';
$this->simpleTemplate = true;
include $this->template_dir.'/inc/admin.header.php';

?>

<div id="container">
	<div id="filemanager-menu">
		<a id="create" class="button" style="background-image: url('<?php echo $this->url['theme']['shared']; ?>images/filemanager/folder.png');">
			<?php echo _('New Folder'); ?></a>
		<a id="delete" class="button" style="background-image: url('<?php echo $this->url['theme']['shared']; ?>images/filemanager/edit-delete.png');">
			<?php echo _('Delete'); ?></a>
		<a id="move" class="button" style="background-image: url('<?php echo $this->url['theme']['shared']; ?>images/filemanager/edit-cut.png');">
			<?php echo _('Move'); ?></a>
		<a id="copy" class="button" style="background-image: url('<?php echo $this->url['theme']['shared']; ?>images/filemanager/edit-copy.png');">
			<?php echo _('Copy'); ?></a>
		<a id="rename" class="button" style="background-image: url('<?php echo $this->url['theme']['shared']; ?>images/filemanager/edit-rename.png');">
			<?php echo _('Rename'); ?></a>
		<a id="upload" class="button" style="background-image: url('<?php echo $this->url['theme']['shared']; ?>images/filemanager/upload.png');">
			<?php echo _('Upload'); ?></a>
		<a id="refresh" class="button" style="background-image: url('<?php echo $this->url['theme']['shared']; ?>images/filemanager/refresh.png');">
			<?php echo _('Refresh'); ?></a>
	</div>
	<div id="column-left"></div>
	<div id="column-right"></div>
</div>
	
	
<script type="text/javascript"><!--

	PommoFileManager.load(
		<?php echo $this->ckeditorCallback ?>,
		'<?php echo $this->field ?>',
		'<?php echo 'http://' . Pommo::$_hostname . Pommo::$_baseUrl ?>',
		{
			error_select: '<?php echo _('Warning: Please select a directory or file!') ?>',
			error_directory: '<?php echo _('Warning: Please select a directory!') ?>'
		}
	);


//-->
</script>
	
<?php
ob_start();

$this->dialogId = 'folderDialog';
$this->dialogTitle = _('New Folder');
$this->dialogContent = '<h2>' . _('New Folder') . '</h2>'
	. ' <input class="textInput" type="text" name="name" value="" /> <input type="button" value="' . _('Submit') . '" />';

include $this->template_dir.'/inc/dialog.php';

$this->dialogId = 'moveDialog';
$this->dialogTitle = _('Move');
$this->dialogContent = '<h2>' . _('Move') . '</h2>'
	. ' <select name="to"></select> <input type="button" value="' . _('Submit') . '" />';

include $this->template_dir.'/inc/dialog.php';

$this->dialogId = 'copyDialog';
$this->dialogTitle = _('Copy');
$this->dialogContent = '<h2>' . _('Copy') . '</h2>'
	. ' <input class="textInput" type="text" name="name" value="" /> <input type="button" value="' . _('Submit') . '" />';

include $this->template_dir.'/inc/dialog.php';

$this->dialogId = 'renameDialog';
$this->dialogTitle = _('Rename');
$this->dialogContent = '<h2>' . _('Rename') . '</h2>'
	. ' <input class="textInput" type="text" name="name" value="" /> <input type="button" value="' . _('Submit') . '" />';

include $this->template_dir.'/inc/dialog.php';

$this->capturedDialogs = ob_get_clean();

include $this->template_dir.'/inc/admin.footer.php';
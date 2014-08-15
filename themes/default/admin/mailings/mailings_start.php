<?php
ob_start();
?>
<link href="<?php echo $this->url['theme']['shared']; ?>css/default.mailings.css"
		type="text/css" rel="stylesheet" />
<link rel='stylesheet'  href="<?php echo $this->url['theme']['shared'];
		?>js/fileuploader/fileuploader.css" type='text/css' />
<script type="text/javascript" src="<?php echo $this->url['theme']['shared'];
		?>js/fileuploader/fileuploader.js"></script>
<?php

include $this->template_dir . '/inc/jquery.ui.php';
include $this->template_dir.'/inc/ui.form.php';
include $this->template_dir.'/inc/ui.dialog.php';
include $this->template_dir.'/inc/ui.tabs.php';

// Include the WYSIWYG Javascripts
foreach ($this->wysiwygJS as $js)
{
	echo '<script type="text/javascript" src="'.$this->url['theme']['shared']
			.'../wysiwyg/'.$js.'"></script>';
}

$this->capturedHead = ob_get_clean();

$this->sidebar = false;
include $this->template_dir.'/inc/admin.header.php';

?>

<ul class="inpage_menu">
	<li><a href="admin_mailings.php" title="<?php
			echo _('Return to Subscribers Page'); ?>"><?php
			echo _('Return to Mailings Page'); ?></a></li>
</ul>

<?php
	include $this->template_dir.'/inc/messages.php';
?>

<hr />

<div id="tabs">
	<ul>
	    <li><a href="ajax/setup.php"><span><?php echo _('Setup'); ?></span></a></li>
	    <li><a href="ajax/templates.php"><span><?php echo _('Templates'); ?></span></a></li>
	    <li><a href="ajax/compose.php"><span><?php echo _('Compose'); ?></span></a></li>
	    <li><a href="ajax/preview.php"><span><?php echo _('Preview'); ?></span></a></li>
	</ul>
</div>

<script type="text/javascript">
$().ready(function(){
	// initialize tabs
	poMMo.tabs = PommoTabs.init('#tabs');

	// initialize dialog(s)
	PommoDialog.init();
});
</script>

<?php

ob_start();

$this->dialogId = 'dialog';
$this->dialogWide = true;
$this->dialogTall = true;
include $this->template_dir.'/inc/dialog.php';

$this->capturedDialogs = ob_get_clean();

include $this->template_dir.'/inc/admin.footer.php';


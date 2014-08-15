<div class="alert">
<?php
	echo _('Templates allow you to re-use your crafted message bodies. The HTML and'
			.' text version is remembered.');
?>
</div>

<div class="output" style="font-size: 130%;">
<?php
	include $this->template_dir.'/inc/messages.php';
?>
</div>

<form class="ajax" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

	<p>
	<?php
		sprintf(_('Fields marked like %s this %s are required.'),
				'<span class="required">', '</span>');
	?>
	</p>

	<div>
		<label for="name"><span class="required"><?php echo _('Name:'); ?></span></label>
		<input type="text" name="name" value="<?php echo $this->escape($_POST['name']); ?>" />
		<span class="notes"><?php echo _('(maximum of 60 characters)'); ?></span>
	</div>

	<div>
		<label for="description"><?php echo _('Description:'); ?></span></label>
		<textarea name="description" style="height: 60px;"><?php
				echo $this->escape($_POST['description']); ?></textarea>
		<span class="notes"><?php echo _('(Brief Summary - 255 characters)'); ?></span>
	</div>

	<div class="buttons">
		<input type="submit" id="submit" name="submit" value="<?php echo _('Save'); ?>" />
		<input type="submit" class="jqmClose" value="<?php echo _('Cancel'); ?>" />
	</div>

</form>

<script type="text/javascript">
$().ready(function(){
	$('form .jqmClose',$('#dialog')[0]).click(function(){$('#dialog').jqmHide();});
});
</script>

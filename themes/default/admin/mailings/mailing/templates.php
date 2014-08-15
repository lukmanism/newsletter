<div class="output">
<?php
	include $this->template_dir.'/inc/messages.php';
?>
</div>

<form class="json" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

	<div class="alert">
		<?php echo _('Templates allow you to re-use your crafted message bodies.'
			.' The HTML and text version is remembered.'); ?>
	</div>

	<p>
	<?php
		echo sprintf(_('You may %sload%s or %sdelete%s templates from here.'),
			'<strong>', '</strong>');
			
	?>
	</p>

	<div>
		<label for="template"><?php echo _('Template'); ?>:</label>
		<select name="template">
			<option value=""><?php echo _('choose template'); ?></option>
			<?php
				foreach ($this->templates as $key => $name)
				{
					echo '<option value="'.$key.'">'.$name.'</option>';
				}
			?>
		</select>
	</div>

	<div class="t_description" style="color: green; margin: 5px 12px;">
		<strong><?php echo _('Description'); ?>:</strong>
		<div>
			<?php echo _('No template selected'); ?>
		</div>
	</div>

	<hr />

	<div class="buttons">
		<input type="submit" name="skip" value="<?php echo _('Skip'); ?>" />
		<input type="submit" name="load" value="<?php echo _('Load'); ?>" />
		<input type="submit" name="delete" value="<?php echo _('Delete'); ?>" />
	</div>

</form>

<script type="text/javascript">
$().ready(function() {
	var scope = $('form.json');
	
	$('select',scope).change(function(){
		var v = $(this).val();
		if(v == '') 
			$('div.t_description div',scope).html('<?php echo _('No template selected'); ?>');
		else
		$('div.t_description div',scope)
			.html('<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" />')
			.load('ajax/ajax.rpc.php?call=getTemplateDescription&id='+v);
	});

	// called as success callback from form submission
	poMMo.callback.deleteTemplate = function(p) {
		// remove the deleted option (p[0])
		$('option[@value='+p.id+']',scope).remove();
		
		// output the passed message (p[1])
		$('div.output').addClass('error').html(p.msg);
	}
	
});
</script>


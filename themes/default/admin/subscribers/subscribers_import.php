<?php

include $this->template_dir.'/inc/admin.header.php';

?>

<h2><?php echo _('Import Subscribers'); ?></h2>

<p>
	<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/cells.png"
			class="navimage right" alt="table cells icon"/>
	<?php
		echo sprintf(_('Welcome to Subscriber Import! You can import subscribers'
			.' from a list of email addresses or from a full fledged CSV file'
			.' containing subscriber field values as well as their email. CSV'
			.' files should have one subscriber(email) per line with field'
			.' information seperated by commas(%s,%s).'), '<tt>', '</tt>');
	?>
</p>

<p>
	<?php
		echo sprintf(_('Popular programs such as Microsoft Excel and %s Open'
			.' Office %s support saving files in CSV (Comma-Seperated-Value)'
			.' format.'), '<a href="http://www.openoffice.org/">', '</a>');
	?>
</p>

<p class="warn">
<?php
	echo _('Duplicate subscribers or invalid email addresses will be ignored.');
?>
</p>

<form method="post" enctype="multipart/form-data" action="">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php $this->maxSize; ?>" />

	<?php
		include $this->template_dir.'/inc/messages.php';
	?>

	<fieldset>
		<legend><?php echo _('Import'); ?></legend>

		<div>
			<label class="required" for="type"><?php echo _('Type'); ?></label>
			<select name="type" id="type">
				<option value="txt">
					<?php echo _('List of Email Addresses'); ?>
				</option>
				<option value="csv">
					<?php echo _('.CSV - All subscriber Data'); ?>
				</option>
			</select>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo _('Subscribers'); ?></legend>

		<div id="file" style="display: none;">
			<div>
				<a href="#" title="<?php echo _('Type subscribers into a box'); ?>">
					<?php echo _('From a box'); ?>
				</a>
			</div>
			<label for="csvfile"><?php echo _('CSV file:'); ?></label>
			<input type="file" accept="text/csv" name="csvfile" id="csvfile"
					class="file" />
		</div>

		<div id="box">
			<div>
				<a href="#" title="<?php echo _('Upload subscribers from a file');
						?>"><?php echo _('From a file'); ?></a>
			</div>
			<textarea name="box" cols="40" rows="8"><?php
					echo _('Type/Paste Contents...'); ?></textarea>
		</div>

		<input type="checkbox" name="excludeUnsubscribed" />
		<?php echo _('Allow unsubscribed emails to be re-subscribed.'); ?>
	</fieldset>

	<div class="buttons">
		<input type="submit" name="submit" value="<?php echo _('Import'); ?>" />
	</div>
</form>

<script type="text/javascript">
$().ready(function(){

	var box = $('#box textarea');
	var orig = box.val();

	box.focus(function() {
		if ($(this).val() == orig)
			$(this).val("");
	});

	box.blur(function() {
		var val = $(this).val();
		val.replace(/^\s*|\s*$/g,"");
		if (val == "")
			$(this).val(orig);
	});
	
	$('#box a').click(function() { 
		$('#box').hide().find('textarea').val(orig);
		$('#file').show();
		return false;
	});
	
	$('#file a').click(function() { 
		$('#file').hide().find('input').val("");
		$('#box').show();
		return false;
	});
	
	$('#type').change(function() {
		if($(this).val() != 'csv')
			return;
		$('#box').hide().find('textarea').val(orig);
		$('#file').show();
	});

});
</script>

<?php

include $this->template_dir.'/inc/admin.footer.php';


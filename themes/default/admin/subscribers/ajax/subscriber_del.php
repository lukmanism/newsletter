<p>
<?php
	echo _('Enter email addresses of subscribers in the box below. Seperate emails with commas, spaces, or line breaks.');
?>
</p>

<form class="json" action="ajax/manage.rpc.php?call=delSubscriber" method="post">

	<div class="output alert"></div>

	<fieldset>
		<legend><?php echo _('Remove Subscribers'); ?></legend>

		<div>
			<label for="emails">
				<strong class="required"><?php echo _('Email Addresses:');
						?></strong>
			</label>
			<textarea name="emails" cols="40" rows="8"><?php
					echo _('Enter Emails...'); ?></textarea>
		</div>
	</fieldset>

	<div class="buttons">
		<input type="submit" value="<?php echo _('Remove Subscribers'); ?>" />
		<input type="hidden" name="status" value="<?php echo $this->status; ?>" />
		<input type="reset" value="<?php echo _('Reset'); ?>" />
	</div>

</form>

<script type="text/javascript">
$().ready(function(){

	var box = $('textarea[name=emails]');
	var orig = box.val();

	poMMo.callback.delSubscriber = function(ids) {
		poMMo.grid.delRow(ids);
		box.val("");
	};

	box.focus(function() {
		if ($(this).val() == orig)
			$(this).val("");
	});

	box.blur(function() {
		if($.trim($(this).val()) == '')
			$(this).val(orig);
	});

	var rows = poMMo.grid.getRowIDs();
	if(rows) {
		var emails = new Array();
		var row = null;
		for (i=0; i<rows.length; i++) {
			row = poMMo.grid.getRow(rows[i]);
			emails.push(row.email);
		}
		box.val(emails.join("\n"));
	}
});
</script>

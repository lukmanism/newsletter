<?php

ob_start();
include $this->template_dir.'/inc/ui.form.php';
include $this->template_dir.'/inc/ui.dialog.php';
include $this->template_dir.'/inc/ui.cssTable.php';
include $this->template_dir . '/inc/jquery.ui.php';
include $this->template_dir.'/inc/ui.sort.php';
$this->capturedHead = ob_get_clean();

include $this->template_dir.'/inc/admin.header.php';

?>

<h2><?php echo _('Fields Page'); ?></h2>

<p>
	<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/fields.png"
			alt="fields icon" class="navimage right" />
	<?php
		printf(_('Subscriber fields allow you to collect information on list'
			.' members. They are typically displayed on the subscription form,'
			.' although "hidden" ones can be used for administrative purposes.'
			.' %sGroups%s are based on subscriber field values.'),
			'<a href="'.$this->url['base'].'subscribers_groups.php">', '</a>');
	?>
</p>

<form method="post" action="">
<?php
	include $this->template_dir.'/inc/messages.php';
?>
	<fieldset>
		<legend><?php echo _('Fields'); ?></legend>
		<div>
			<label for="field_name"><?php echo _('New field name:'); ?></label>
			<input type="text" title="<?php echo _('type new field name'); ?>"
					maxlength="60" size="30" name="field_name" id="field_name" />
		</div>

		<div>
			<label for="field_type"><?php echo _('Field type:'); ?></label>
			<select name="field_type" id="field_type">
				<option value="text"><?php echo _('Text'); ?></option>
				<option value="number"><?php echo _('Number'); ?></option>
				<option value="checkbox"><?php echo _('Checkbox'); ?></option>
				<option value="multiple"><?php echo _('Multiple Choice'); ?></option>
				<option value="date"><?php echo _('Date'); ?></option>
				<option value="comment"><?php echo _('Comment'); ?></option>
			</select>
		</div>

		<div class="buttons">
			<input type="submit" value="<?php echo _('Add'); ?>" />
		</div>
	</fieldset>
</form>

<h3><?php echo _('Field Ordering'); ?></h3>
<ul>
	<li>
		<?php echo _('Change the ordering of fields on the subscription form by'
			.' dragging and dropping the order icon'); ?>
	</li>
</ul>

<div id="grid">
	<div class="header">
		<span><?php echo _('ID'); ?></span>
		<span><?php echo _('Delete'); ?></span>
		<span><?php echo _('Edit'); ?></span>
		<span><?php echo _('Order'); ?></span>
		<span><?php echo _('Field Name'); ?></span>
	</div>

	<?php
	if ($this->fields)
	{
		$row = 0;
		foreach ($this->fields as $key => $field)
		{
			$row++;
			if (4 == $row)
			{
				$row = 1;
			}
			?>
			<div class="r<?php echo $row; ?> sortable" id="id<?php echo $key; ?>">
				<span>
					<?php echo $field['id']; ?>
				</span>

				<span>
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?field_id=<?php
							echo $key; ?>&delete=TRUE&field_name=<?php
							echo $field['name']; ?>">
						<img alt="delete icon"
								src="<?php echo $this->url['theme']['shared'];
								?>images/icons/delete.png" />
					</a>
				</span>

				<span>
					<a href="ajax/field_edit.php?field_id=<?php echo $key; ?>"
							class="editTrigger">
						<img src="<?php echo $this->url['theme']['shared'];
								?>images/icons/edit.png" alt="edit icon" />
					</a>
				</span>

				<span>
					<img src="<?php echo $this->url['theme']['shared'];
							?>images/icons/order.png" alt="order icon" class="handle" />
				</span>

				<span class="name
				<?php
					if ('on' == $field['active'])
					{
						echo 'green';
					}
				?>">
					<?php
						if ('on' == $field['required'])
						{
							echo '<strong>'.$field['name'].'</strong>';
						}
						else
						{
							echo $field['name'];
						}
					?>
				</span>
			</div>
		<?php
		}
	}
	?>
</div>

<p>
<?php
	printf(_('Fields marked like %s this %s are required. '),
				'<span class="required">', '</span>');
	printf(_('%sGreen%s fields are active.'),
				'<span class="green">', '</span>');
?>
</p>

<?php
if ($this->added)
{
	echo '<a href="ajax/field_edit.php?field_id='.$this->added.'" id="added"
			class="hidden"></a>';
}
?>

<script type="text/javascript">
$().ready(function(){

	// setup dialogs
	PommoDialog.init('#dialog',{modal: true, trigger: '.editTrigger'});

	// setup sorting
	PommoSort.init('#grid',{updateURL: 'ajax/fields.rpc.php?call=updateOrdering'});

	// trigger editing of recently added field
	var a = $('#added')[0];
	if (a)
	{
		$('#dialog').jqmShow(a);
	}
});

poMMo.callback.updateField = function(f) {
	var name = f.name;
	if(f.required == 'on')
		name = '<strong>'+name+'</strong';

	var e = $('#id'+f.id+' span.name').html(name);

	e.removeClass('green');
	if(f.active == 'on')
		e.addClass('green');
};

poMMo.callback.updateOptions = function(json) {

	// remember #normally
	var normally = $('#normally').val();

	// remove existing options
	$('#delOptions option, #normally option:gt(0)').remove();


	// clear addOptions input
	$('#addOptions').val('');

	// populate options
	for(var i=0;i<json.length;i++)
		$('#delOptions, #normally').append('<option>'+json[i]+'</option>');

	// restore #normally
	$('#normally').val(normally);

};

</script>

<?php

ob_start();
$this->dialogId = 'dialog';
$this->dialogWide = 'true';
$this->dialogTall = 'true';
include $this->template_dir.'/inc/dialog.php';
$this->capturedDialogs = ob_get_clean();

include $this->template_dir.'/inc/admin.footer.php';

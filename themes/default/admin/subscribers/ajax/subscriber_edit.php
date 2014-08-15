<?php
    include $this->config['app']['path'] . 'themes/shared/datepicker/datepicker.php';
?>

<form class="json validate" action="ajax/manage.rpc.php?call=editSubscriber"
		method="post" id="edForm">

	<div class="output alert"></div>

	<fieldset>
		<legend><?php echo _('Edit Subscriber'); ?></legend>

		<div>
			<label for="email">
				<strong class="required"><?php echo _('Email:'); ?></strong>
			</label>
			<input type="text" class="pvEmail pvEmpty" size="32" maxlength="60"
					name="email" />
		</div>

		<?php
			foreach ($this->fields as $key => $field)
			{
			?>
			<div>
				<label for="field<?php echo $key; ?>">
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
				</label>

				<?php
					switch ($field['type'])
					{
						case 'checkbox':
							?>
							<input type="checkbox" name="d[<?php echo $key; ?>]"
							<?php
								if ('on' == $field['normally'])
								{
									echo ' checked="checked"';
								}
								if ('on' == $field['required'])
								{
									echo ' class="pvEmpty"';
								}
							?>
							/>
							<?php
							break;
						case 'multiple':
							?>
							<select name="d[<?php echo $key; ?>]">
							<?php
							foreach ($field['array'] as $option)
							{
							?>
								<option
								<?php
									if ($option == $field['normally'])
									{
										echo 'selected="selected"';
									}
								?>>
								<?php echo $option; ?>
								</option>
							<?php
							}
							?>
							</select>
							<?php
							break;
						case 'date':
							?>
							<input type="text" class="datepicker pvDate <?php
							if ('on' == $field['required'])
							{
								echo 'pvEmpty';
							}
							?>" size="12" name="d[<?php echo $key; ?>]"
							<?php
								if ($field['normally'])
								{
									echo 'value="'.$field['normally'].'"';
								}
								else
								{
									echo 'value="'
											.$this->config['app']['dateformat']
											.'"';
								}
							?> />
							<?php
							break;
						case 'number':
							?>
							<input type="text" class="pvNumber <?php
								if ('on' == $field['required'])
								{
									echo 'pvEmpty';
								}
							?>" size="12" name="d[<?php echo $key; ?>]"
							<?php
								if ($field['normally'])
								{
									echo 'value="'.$this->escape($field['normally'])
											.'"';
								}
							?> />
							<?php
							break;
						default:
							?>
							<input type="text" size="32"
							<?php
								if ('on' == $field['required'])
								{
									echo 'class="pvEmpty"';
								}
							?>
							name="d[<?php echo $key; ?>]"
							value="<?php
								if ($field['normally'])
								{
									echo $this->escape($field['normally']);
								}
							?>" />
							<?php
							break;
					}
				?>
			</div>
			<?php
			}
		?>
	</fieldset>

	<fieldset>
		<input type="checkbox" name="force" />
		<?php echo _('Force (bypasses validation)'); ?>
	</fieldset>

	<div class="buttons">
		<input type="hidden" name="id" value="0" />
		<input type="submit" value="<?php echo _('Update Subscriber'); ?>" />
	</div>

	<p>
	<?php
		echo sprintf(_('Fields marked like %s this %s are required.'),
				'<span class="required">', '</span>');
	?>
	</p>
</form>

<script type="text/javascript">
$().ready(function(){

	poMMo.callback.editSubscriber = function(p) {
		poMMo.grid.setRow(p);
	};

	// populate form with first selected row...
	// TODO; add support for multiple subscriber editing at a time.
	var data = poMMo.grid.getRow();
	var scope = $('#edForm')[0];

	for (var i in data)  {

		// skip empty values/data
		if($.trim(data[i]) == '')
			continue;

		// transform "d#" to "d[#]"
		var name = (i.match(/^d\d+$/)) ? 'd['+i.substr(1)+']' : i;
		$(':input[name="'+name+'"]',scope).each(function(){
			if($(this).attr('type') == 'checkbox')
				this.checked = (data[i] == 'on') ? true : false;
			else
				$(this).val(""+data[i]+"");
		});
	}

	$('input[name="force"]',scope).click(function(){
		if(this.checked)
			$(this).jqvDisable();
		else
			$(this).jqvEnable();
	});

});
</script>

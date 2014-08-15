<p>
<?php
    include $this->config['app']['path'] . 'themes/shared/datepicker/datepicker.php';


	echo sprintf(_('Welcome to adding subscribers! You can add subscribers'
			.' one-by-one here. If you would like to add subscribers in bulk,'
			.' visit the %sSubscriber Import%s page.'),
			'<a href="subscribers_import.php">', '</a>');
?>
</p>

<form class="json validate" action="ajax/manage.rpc.php?call=addSubscriber"
		method="post">
	<div class="output alert"></div>

	<fieldset>
		<legend><?php echo _('Add Subscriber'); ?></legend>

		<div>
			<label for="email">
				<strong class="required"><?php echo _('Email:'); ?></strong>
			</label>
			<input type="text" class="pvEmail pvEmpty" size="32" maxlength="60"
					name="Email" />
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
						echo '<strong>'.$field['name'].':</strong>';
					}
					else
					{
						echo $field['name'].':';
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
									echo 'checked="checked" ';
								}
								if ('on' == $field['required'])
								{
									echo 'class="pvEmpty"';
								}
							?> />
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
								<?php
									echo $option;
								?>
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
									echo ' pvEmpty';
								}
							?>" size="12" name="d[<?php echo $key; ?>]"
							<?php
								if ($field['normally'])
								{
									echo 'value="'.$this->escape($field['normally'])
											.'" ';
								}
								else
								{
									echo 'value="'.$this->config['app']['dateformat']
											.'" ';
								}
							?> />
							<?php
							break;
						case 'number':
							?>
							<input type="text" class="pvNumber <?php
								if ('on' == $field['required'])
								{
									echo ' pvEmpty';
								}
							?>" size="12" name="d[<?php echo $key; ?>]"
							<?php
								if ($field['normally'])
								{
									echo 'value="'.$this->escape($field['normally'])
											.'" ';
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
							?> name="d[<?php echo $key; ?>]"
							<?php
								if ($field['normally'])
								{
									echo 'value="'.$this->escape($field['normally'])
											.'" ';
								}
							?> />
							<?php
					}
				?>
			</div>
			<?php
			}
		?>
	</fieldset>

	<fieldset>
		<input type="checkbox" name="force" /><?php
				echo _('Force (bypasses validation)'); ?>
	</fieldset>

	<div class="buttons">
		<input type="submit" value="<?php echo _('Add Subscriber'); ?>" />
		<input type="reset" value="<?php echo _('Reset'); ?>" />
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

	poMMo.callback.addSubscriber = function(json) {
		if($('#grid').size() == 0)
        	history.go(0); // refresh the page if no grid exists, else add new subscriber to grid
        else
        	poMMo.grid.addRow(json.key,json);
	};

	$('input[name="force"]').click(function(){
		if(this.checked)
			$(this).jqvDisable();
		else
			$(this).jqvEnable();
	});

});
</script>

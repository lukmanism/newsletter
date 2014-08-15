<div class="alert">
<?php
	if ('date' == $this->field['type'])
	{
		echo _('value must be a date').'('.$this->config['app']['dateformat'].')';
	}
	elseif ('number' == $this->field['type'])
	{
		echo _('value must be a number');
	}
	elseif ('text' == $this->field['type'])
	{
		echo _('value must not be blank');
	}
?>
</div>

<div style="width: 100%; text-align: center; margin: 15px 0;">
	<form class="json validate" action="ajax/group.rpc.php?call=addRule" method="post">
		<input type="hidden" name="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="field" value="<?php echo $this->field['id']; ?>" />

		<?php echo _('Match subscribers where'); ?> <strong><?php echo $field['name']; ?></strong>

		<select name="logic">
		<?php
			foreach ($this->logic as $val => $desc)
			{
				echo '<option value="'.$val.'">'.$desc.'</option>';
			}
		?>
		</select>

		<?php
			if ('checkbox' != $this->field['type'])
			{
			?>
				<hr />
				<?php echo _('Value(s)'); ?>
				<div id="values">
					<div>
					<?php
						if ('multiple' == $this->field['type'])
						{
						?>
							<select name="match[]" class="pvEmpty">
							<?php
								foreach ($this->field['array'] as $option)
								{
									echo '<option ';
									if ($this->firstVal == $option)
									{
										echo 'selected="selected"';
									}
									echo '>'.$option.'</option>';
								}
							?>								
							</select>
						<?php
						}
						else
						{
						?>
							<input type="text" name="match[]" value="<?php
									echo $this->firstVal; ?>" class="pvEmpty <?php
									if ('number' == $this->field['type'])
									{
										echo 'pvNumber';
									}
									elseif ('date' == $this->field['type'])
									{
										echo 'pvDate';
									}
									?>" />
						<?php
						}
					?>
						<input type="submit" value="+" class="addMatch pvSkip" />
					</div>

					<?php
						// If we're editing, add another input populated w/ value
						foreach ($this->values as $val)
						{
						?>
						<div>
							<?php
								if ('multiple' == $this->field['type'])
								{
								?>
								<select name="match[]" class="pvEmpty">
									<?php
										foreach ($this->field['array'] as $option)
										{
											echo '<option ';
											if ($val == $option)
											{
												echo 'selected="selected"';
											}
											echo '>'.$option.'</option>';
										}
									?>
								</select>
								<?php
								}
								else
								{
								?>
									<input type="text" value="<?php echo $val; ?>"
											name="match[]" class="pvEmpty<?php
									if ('number' == $this->field['type'])
									{
										echo 'pvNumber';
									}
									elseif ('date' == $this->field['type'])
									{
										echo 'pvDate';
									}
									?>" />
								<?php
								}
							?>
							<input type="submit" value="-" class="delMatch pvSkip" />
						</div>
						<?php
						}
					?>
				</div>
				<hr />
			<?php
			}
		?>

		<div>
			<input type="submit"
					<?php
						if ($this->firstVal)
						{
							echo 'value="'._('Update').'"';
						}
						else
						{
							echo 'value="'._('Add').'"';
						}
					?> />
			<input type="submit" value="<?php echo _('Cancel'); ?>" class="jqmClose" />
		</div>
	</form>
</div>

<script type="text/javascript">

$().ready(function(){
	// stretch window
	$('#dialog div.jqmdBC').addClass('jqmdTall');
	
	$('#values input.addMatch').click(function() {
		var add = $(this).parent().clone().find(':input:last').val('-').end();
		$('#values').append(add).find(':input:last')
		.one('click', function() {
			$(this).parent().remove();
			return false;
		});
		$('#dialog form.validate').jqValidate();
		return false;
	});
	
	$('#values input.delMatch').one('click', function() {
		$(this).parent().remove();
		$('#dialog form.validate').jqValidate();
		return false;
	});

});
</script>


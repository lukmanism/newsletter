<!--{* Field Validation - see docs/template.txt documentation *}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="field_name"}
{fv validate="field_prompt"}
{fv validate="field_required"}
{fv validate="field_active"}-->

<p>
<?php echo $this->intro; ?>
</p>

<form class="json" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="output alert">
	<?php
		include $this->template_dir.'/inc/messages.php';
	?>
	</div>

	<input type="hidden" value="<?php echo $this->field['id']; ?>"
			name="field_id"/>

	<fieldset>
		<legend>'<?php echo $this->field['name']; ?>' parameters</legend>

		<div>
			<label for="field_name">
				<strong class="required"><?php echo _('Short Name:'); ?></strong><!--{fv message="field_name"}-->
			</label>
			<input value="<?php echo $this->escape($this->field['name']); ?>"
					type="text" name="field_name"/>
			<div class="notes">
			<?php
				echo _('Identifying name. NOT displayed on Subscription Form or'
					.' seen by users.');
			?>
			</div>
		</div>

		<div>
			<label for="field_prompt">
				<strong class="required"><?php echo _('Form Name:'); ?></strong><!--{fv message="field_prompt"}-->
			</label>
			<input value="<?php echo $this->escape($this->field['prompt']); ?>"
					type="text" name="field_prompt"/>
			<div class="notes">
			<?php
				printf(_('Prompt for field on the Subscription Form. e.g.'
					.' %sType your city%s'), '<tt>', '</tt>');
			?>
			</div>
		</div>

		<?php
		if ('comment' != $this->field['type'])
		{
		?>
		<div>
			<label for="field_required"><?php echo _('Required:'); ?><!--{fv message="field_required"}--></label>
			<input type="radio" name="field_required" value="on"
			<?php
				if ('on' == $this->field['required'])
				{
					echo 'checked="checked"';
				}
				echo '/> ';
				echo _('yes');
			?>
			<input type="radio" name="field_required" value="off"
			<?php
				if ('on' != $this->field['required'])
				{
					echo 'checked="checked"';
				}
				echo '/> ';
				echo _('no');
			?>
			<div class="notes">
			<?php
				printf(_('Toggle to require field on Subscription Form (user'
					.' cannot leave blank if %syes%s)'), '<tt>', '</tt>');
			?>
			</div>
		</div>
		<?php
		}
		else
		{
		?>
			<input type="hidden" name="field_required" value="off" />
		<?php
		}
		?>

		<div>
			<label for="field_active"><?php echo _('Active:'); ?><!--{fv message="field_active"}--></label>
			<input type="radio" name="field_active" value="on"
			<?php
			if ('on' == $this->field['active'])
			{
				echo 'checked="checked"';
			}
			echo '/> ';
			echo _('show');
			?>
			<input type="radio" name="field_active" value="off"
			<?php
			if ('on' != $this->field['active'])
			{
				echo 'checked="checked"';
			}
			echo '/> ';
			echo _('hide');
			?>
			<div class="notes">
				<?php echo _('Toggle display of field for Subscription Form'); ?>
			</div>
		</div>

		<?php
		if ('text' == $this->field['type']
				|| 'number' == $this->field['number']
				|| 'date' == $this->field['type'])
		{
		?>
		<div>
			<label for="field_normally"><?php echo _('Default:'); ?></label>
			<input type="text" name="field_normally" value="<?php
					echo $this->escape($this->field['normally']); ?>" />
			<div class="notes">
				<?php echo _('If provided, this value will appear pre-filled on'
					.' the subscription form'); ?>
			</div>
		</div>
		<?php
		}
		elseif ('checkbox' == $this->field['type'])
		{
		?>
		<div>
			<label for="field_normally"><?php echo _('Default:'); ?></label>
			<select name="field_normally">
				<option value="on"
				<?php
				if ('on' == $this->field['normally'])
				{
					echo 'selected="selected"';
				}
				echo '>';
				echo _('Checked');
				echo '</option>';
				?>
				<option value="off"
				<?php
				if ('on' != $this->field['normally'])
				{
					echo 'selected="selected"';
				}
				echo '>';
				echo _('Not Checked');
				echo '</option>';
				?>
			</select>
			<div class="notes">
				<?php echo _('If provided, this value will appear pre-filled on'
					.' the subscription form'); ?>
			</div>
		</div>
		<?php
		}
		elseif ('multiple' == $this->field['type'])
		{
		?>
		<div>
			<label for="field_normally"><?php echo _('Default:'); ?></label>
			<select name="field_normally" id="normally">
				<option value=""><?php echo _('Select default choice'); ?></option>
				<?php
				if ($this->field['array'])
				{
					foreach ($this->field['array'] as $option)
					{
						echo '<option ';
						if ($option == $this->field['normally'])
						{
							echo 'selected="selected"';
						}
						echo '>';
						echo $option;
						echo '</option>';
					}
				}
				?>
			</select>
			<div class="notes">
				<?php echo _('If provided, this value will appear pre-filled on'
					.' the subscription form'); ?>
			</div>
		</div>
		<?php
		}
		?>
	</fieldset>

	<input type="submit" value="<?php echo _('Update'); ?>" />
	<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
			name="loading" class="hidden" title="<?php echo _('loading...'); ?>"
			alt="<?php echo _('loading...'); ?>" />
</form>

<?php
if ('multiple' == $this->field['type'])
{
?>
<fieldset>
	<legend><?php echo _('Multiple Choices'); ?></legend>

	<form class="json" action="ajax/fields.rpc.php?call=addOption" method="post">
		<input type="hidden" name="field_id" value="<?php echo $this->field['id']; ?>" />
		<div>
			<label for="options"><?php echo _('Add Option(s)'); ?></label>
			<input type="text" name="options" id="addOptions" size="50"
					title="<?php echo _('type option(s)'); ?>" />
			<input type="submit" value="<?php echo _('Add'); ?>" />
			<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
					name="loading" class="hidden" title="<?php echo _('loading...'); ?>"
					alt="<?php echo _('loading...'); ?>" />
			<div class="notes">
				<?php echo _('Enter a multiple choice option. You can add more'
					.' than one choice at a time by separating each with a'
					.' comma.'); ?>
			</div>
			<div class="output"></div>
		</div>
	</form>

	<form class="json confirm" action="ajax/fields.rpc.php?call=delOption" method="post">
		<input type="hidden" value="<?php echo $this->field['id']; ?>"
				name="field_id"/>
		<div>
			<label for="options"><?php echo _('Delete Option(s)'); ?></label>
			<select name="options" id="delOptions">
			<?php
				if ($this->field['array'])
				{
					foreach ($this->field['array'] as $option)
					{
						echo '<option>'.$option.'</option>';
					}
				}
			?>
			</select>
			<input type="submit" value="<?php echo _('Delete'); ?>" />
			<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
					name="loading" class="hidden" title="<?php echo _('loading...'); ?>"
					alt="<?php echo _('loading...'); ?>" />
			<div class="output"></div>
		</div>
	</form>

</fieldset>
<?php
}

<?php

include $this->template_dir.'/inc/messages.php';

if ($this->sent)
{
	echo '<div class="alert">';
	echo sprintf(_('Mailing sent to %s'), '<strong>'.$this->sent.'</strong>');
	echo '</div>';
}

?>

<p>
	<?php
		echo 'Verify the appeareance of a mailing by sending a message to yourself.';
	?>
</p>

<form class="ajax" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<fieldset>
		<legend><?php echo _('Recipient'); ?></legend>

		<div>
			<label class="required" for="email"><?php echo _('Email:'); ?></label>
			<input type="text" size="32" maxlength="60" name="email"
					value="<?php echo $this->escape($this->email); ?>" />
			<input type="submit" value="<?php echo _('Send Mailing'); ?>"/>
		</div>
	</fieldset>

	<p>
	<?php
		echo sprintf(_('If your mailing includes personalizations, you can'
			.' %soptionally%s supply test values'), '<strong>', '</strong>');
	?>
	</p>

	<fieldset>
		<legend><?php echo _('Personalizations'); ?></legend>
		<?php
			foreach ($this->fields as $key => $field)
			{
			?>
			<div>
				<label <?php
					if ('on' === $field['required'])
					{
						echo 'class="required" ';
					}
					echo 'for="'.$field[$key].'" ';
				?>><?php echo $field['name']; ?>:</label>

				<?php
					if ('checkbox' === $field['type'])
					{
						echo '<input type="checkbox" name="d['.$key.']" ';
						if ('on' === $field['normally'])
						{
						 	echo 'checked="checked" ';
						}
						if ('on' === $field['required'])
						{
							echo ' class="pvEmpty" ';
						}
						echo '/>';
					}
					elseif ('multiple' === $field['type'])
					{
						echo '<select name="d['.$key.']">';
						foreach ($field['array'] as $option)
						{
							echo '<option ';
							if ($option === $field['normally'])
							{
								echo 'selected="selected" ';
							}
							echo '>'.$option.'</option>';
						}
						echo '</select>';
					}
					elseif ('date' === $field['type'])
					{
						echo '<input type="text" class="pvDate';
						if ('on' === $field['required'])
						{
							echo ' pvEmpty';
						}
						echo '" size="12" name="d['.$key.']" value="';
						if ($field['normally'])
						{
							echo $this->escape($field['normally']);
						}
						else
						{
							echo $this['config']['app']['dateformat'];
						}
						echo '" />';
					}
					elseif ('number' === $field['type'])
					{
						echo '<input type="text" class="pvNumber';
						if ('on' === $field['required'])
						{
							echo ' pvEmpty';
						}
						echo '" size="12" name="d['.$key.']" value="';
						if ($field['normally'])
						{
							echo $this->escape($field['normally']);
						}
						echo '" />';
					}
					else
					{
						echo '<input type="text" ';
						if ($field.required == 'on')
						{
							echo 'class="pvEmpty" ';
						}
						echo 'size="32" name="d['.$key.']" value="';
						if ($field.normally)
						{
							echo $this->escape($field['escape']);
						}
						echo '" />';
					}
				?>
			</div>
			<?php
			}
		?>
	</fieldset>
</form>

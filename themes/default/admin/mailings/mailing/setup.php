<form class="json mandatory" action="<?php echo $_SERVER['PHP_SELF']; ?>"
		method="post">
	<div class="output alert">
	<?php
		include $this->template_dir.'/inc/messages.php';
	?>
	</div>
	<p>
	<?php
		echo sprintf(_('Fields marked like %s this %s are required.'),
				'<span class="required">', '</span>');
	?>
	</p>

	<div>
		<label for="subject">
			<span class="required"><?php echo _('Subject:'); ?></span>
		</label>
		<input value="<?php echo $this->escape($this->subject); ?>"
				type="text" name="subject" maxlength="60" />
		<span class="notes"><?php echo _('(maximum of 60 characters)'); ?></span>
	</div>

	<div>
		<label for="mailgroup">
			<span class="required"><?php echo _('Send Mail To:'); ?></span>
		</label>
		<br />

		<select name="mailgroup[]" multiple=multiple>
			<option value="all"
			<?php
				if ('all' == $this->mailgroup)
				{
					echo 'selected="selected"';
				}
			?>><?php echo _('All subscribers'); ?></option>
			<?php
				foreach ($this->groups as $key => $group)
				{
				?>
					<option value="<?php echo $key; ?>"
					<?php
						if (in_array($key, $this->mailgroups))
						{
							echo 'selected="selected"';
						}
					?>><?php echo $group['name'].' '.$this->is_selected; ?></option>
				<?php
				}
			?>
		</select>

		<span class="notes"><?php echo _('(Select who should receive the mailing.'
			.' Control click ( or command click on the Mac ) to select multiple'
			.' groups. Note: If a subscriber is in more than one group they will'
			.' only receive one email and NOT one for each group that they\'re'
			.' in.)'); ?></span>
	</div>

	<div>
		<label for="fromname"><span class="required"><?php echo _('From Name:');
				?></span></label>
		<input type="text" name="fromname" value="<?php
				echo $this->escape($this->fromname); ?>" />
		<span class="notes"><?php echo _('(maximum of 60 characters)'); ?></span>
	</div>

	<div>
		<label for="fromemail"><span class="required"><?php echo _('From Email:');
				?></span></label>
		<input type="text" name="fromemail" value="<?php
				echo $this->escape($this->fromemail); ?>" />
		<span class="notes"><?php echo _('(maximum of 60 characters)'); ?></span>
	</div>

	<div>
		<label for="frombounce"><span class="required"><?php echo _('Return:');
				?></span></label>
		<input value="<?php echo $this->escape($this->frombounce); ?>"
				type="text" name="frombounce" />
		<span class="notes"><?php echo _('(maximum of 60 characters)'); ?></span>
	</div>

	<label for="list_charset">
		<span class="required"><?php echo _('Character Set:'); ?></span>
	</label>

	<select name="list_charset">
		<option value="UTF-8"
		<?php
			if ('UTF-8' == $this->list_charset)
			{
				echo 'selected="selected"';
			}
		?>><?php echo _('UTF-8 (recommended)'); ?></option>
		<option value="ISO-8859-1"
		<?php
			if ('ISO-8859-1' == $this->list_charset)
			{
				echo 'selected="selected"';
			}
		?>><?php echo _('Western (ISO-8859-1)'); ?></option>
		<option value="ISO-8859-15"
		<?php
			if ('ISO-8859-15' == $this->list_charset)
			{
				echo 'selected="selected"';
			}
		?> ><?php echo _('Western (ISO-8859-15)'); ?></option>
		<option value="ISO-8859-2"
		<?php
			if ('ISO-8859-2' == $this->list_charset)
			{
				echo 'selected="selected"';
			}
		?> ><?php echo _('Central/Eastern European (ISO-8859-2)'); ?></option>
		<option value="ISO-8859-7"
		<?php
			if ('ISO-8859-7' == $this->list_charset)
			{
				echo 'selected="selected"';
			}
		?>><?php echo _('Greek (ISO-8859-7)'); ?></option>
		<option value="ISO-2022-JP"
		<?php
			if ('ISO-2022-JP' == $this->list_charset)
			{
				echo 'selected="selected"';
			}
		?>><?php echo _('Japanese (ISO-2022-JP)'); ?></option>
		<option value="EUC-JP"
		<?php
			if ('EUC-JP' == $this->list_charset)
			{
				echo 'selected="selected"';
			}
		?>><?php echo _('Japanese (EUC-JP)'); ?></option>
		<option value="cp1251"
		<?php
			if ('cp1251' == $this->list_charset)
			{
				echo 'selected="selected"';
			}
		?>><?php echo _('cyrillic (Windows-1251)'); ?></option>
		<option value="KOI8-R"
		<?php
			if ('KOI8-R' == $this->list_charset)
			{
				echo 'selected="selected"';
			}
		?>><?php echo _('cyrillic (KOI8-R)'); ?></option>
		<option value="GB2312"
		<?php
			if ('GB2312' == $this->list_charset)
			{
				echo 'selected="selected"';
			}
		?>><?php echo _('Simplified Chinese (GB2312)'); ?></option>
	</select>
	<span class="notes">
		<?php echo _('(Select Character Set of Mailings)'); ?>
	</span>

	<div>
		<label for="track">
			<span><?php echo _('Track when people open this mailing:'); ?></span>
		</label>
		<select name="track">
			<option value='0'><?php echo _('No'); ?></option>
			<option value='1'
			<?php
				if ('1' == $this->track)
				{
					echo 'selected="selected"';
				}
			?>>
				<?php echo _('Yes'); ?>
			</option>
		</select>
	</div>

	<div class="buttons">
		<input type="submit" id="submit" name="submit"
				value="<?php echo _('Continue'); ?>" />
		<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
				name="loading" class="hidden" title="<?php echo _('loading...');
				?>" alt="<?php echo _('loading...'); ?>" />
	</div>

</form>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="json">
	<div class="output alert">
	<?php
		if ($this->output)
		{
			echo $this->output;
		}
	?>
	</div>

	<div>
		<label for="list_fromname">
			<strong class="required"><?php echo _('From Name:'); ?></strong>
		</label>
		<input value="<?php echo $this->escape($this->list_fromname); ?>"
				type="text" name="list_fromname"/>
		<span class="notes">
			<?php echo _('(Default name mails will be sent from)'); ?>
		</span>
	</div>

	<div>
		<label for="list_fromemail">
			<strong class="required"><?php echo _('From Email:'); ?></strong>
		</label>
		<input value="<?php echo $this->escape($this->list_fromemail); ?>"
				type="text" name="list_fromemail"/>
		<span class="notes">
			<?php echo _('(Default email mails will be sent from)'); ?>
		</span>
	</div>

	<div>
		<label for="list_frombounce">
			<strong class="required"><?php echo _('Bounce Address:'); ?></strong>
		</label>
		<input value="<?php echo $this->escape($this->list_frombounce); ?>"
				type="text" name="list_frombounce"/>
		<span class="notes">
			<?php echo _('(Returned emails will be sent to this address)'); ?>
		</span>
	</div>

	<div>
		<label for="demo_mode">
			<?php echo _('Demonstration Mode:'); ?>
		</label>
		<input type="radio" name="demo_mode" value="on"
		<?php
			if ('on' == $this->demo_mode)
			{
				echo 'checked="checked"';
			}
		?> /> <?php echo _('on'); ?>
		<input type="radio" name="demo_mode" value="off"
		<?php
			if ('on' != $this->demo_mode)
			{
				echo 'checked="checked"';
			}
		?> /> <?php echo _('off'); ?>
		<span class="notes"><?php echo _('(Toggle Demonstration Mode)'); ?></span>
	</div>

	<div>
		<label for="public_history"><?php echo _('Public Mailings'); ?></label>
		<input type="radio" name="public_history" value="on"
		<?php
			if ('on' == $this->public_history)
			{
				echo 'checked="checked"';
			}
		?> /> <?php echo _('on'); ?>
		<input type="radio" name="public_history" value="off"
		<?php
			if ('on' != $this->public_history)
			{
				echo 'checked="checked"';
			}
		?> /> <?php echo _('off'); ?>
		<span class="notes">
			<?php
				echo sprintf(_('(When on, the public can view past mailings at'
					.' this %sURL%s)'),
					'<a href="'.$this->url['base'].'mailings.php">', '</a>');
			?>
		</span>
	</div>

	<div>
		<a href="ajax/ajax.throttle.php" id="throttleTrigger">
			<img src="<?php echo $this->url['theme']['shared'];
					?>images/icons/right.png" alt="icon" class="navimage" />
			<?php echo _('Set mailing throttle values'); ?>
		</a>
		<span class="notes">
			<?php echo _('(controls mails per second, bytes per second, and'
				.' domain limits)'); ?>
		</span>
		<br clear="left" />
	</div>

	<div>
		<label for="list_charset">
			<strong class="required"><?php echo _('Character Set:'); ?></strong>
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
			?>><?php echo _('Western (ISO-8859-15)'); ?></option>
			<option value="ISO-8859-2"
			<?php
				if ('ISO-8859-2' == $this->list_charset)
				{
					echo 'selected="selected"';
				}
			?>><?php echo _('Central/Eastern European (ISO-8859-2)'); ?></option>
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
			<?php echo _('(Select Default Character Set of Mailings)'); ?>
		</span>
	</div>

	<div>
		<label for="maxRuntime">
			<strong class="required"><?php echo _('Runtime:'); ?></strong>
		</label>
		<input value="<?php echo $this->escape($this->maxRuntime); ?>" size="4"
				type="text" name="maxRuntime" maxlength="5" />
		<span class="notes">
			<?php echo _('(Seconds a processing script runs for. Default: 80,'
				.' Minimum: 15)'); ?>
		</span>
	</div>

	<input type="submit" value="<?php echo _('Update'); ?>" />
	<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
			alt="loading..." class="hidden" name="loading" />

</form>

<script type="text/javascript">
$().ready(function(){
	$('#throttleWindow').jqmAddTrigger($('#throttleTrigger'));
});
</script>

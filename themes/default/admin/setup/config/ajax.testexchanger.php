<p>
<?php
	echo sprintf(_('A test message will be sent to the supplied recipient. If'
			.' you receive it, poMMo can use the %s exchanger. Remember to check'
			.' your SPAM folder too.'), '<strong>'.$this->exchanger.'</strong>');
?>
</p>

<div id="scope">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

		<div class="output alert">
		<?php
			if ($this->output)
			{
				echo $this->output;
			}
		?>
		</div>

		<fieldset>
			<legend><?php echo _('Recipient'); ?></legend>
			<div>
				<label for="email">
					<strong class="required"><?php echo _('Email:'); ?></strong>
				</label>
				<input value="<?php echo $this->escape($this->email); ?>" type="text"
						name="email" />
				<span class="notes">
					<?php echo _('(address to send test message to)'); ?>
				</span>
			</div>

			<input type="submit" value="<?php echo _('Send Mailing'); ?>" />
			<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
					alt="loading..." class="hidden" name="loading" />
		</fieldset>
	</form>
</div>

<script type="text/javascript">
poMMo.form.init('#scope form',{type: 'json'});
</script>

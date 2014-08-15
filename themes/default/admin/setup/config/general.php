<form class="json" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="output alert">
	<?php
		if ($this->output)
		{
			echo $this->output;
		}
	?>
	</div>

	<div>
		<label for="list_name">
			<strong class="required">
				<?php echo _('List Name:'); ?>
			</strong>
		</label>
		<input value="<?php echo $this->escape($this->list_name); ?>"
				type="text" name="list_name" />
		<span class="notes"><?php echo _('(The name of your Mailing List)'); ?></span>
	</div>

	<div>
		<label for="site_name">
			<strong class="required">
				<?php echo _('Website Name:'); ?>
			</strong>
		</label>
		<input value="<?php echo $this->escape($this->site_name); ?>"
				type="text" name="site_name" />
		<span class="notes"><?php echo _('(The name of your Website)'); ?></span>
	</div>

	<div>
		<label for="site_url">
			<strong class="required">
				<?php echo _('Website URL:'); ?>
			</strong>
		</label>
		<input value="<?php echo $this->escape($this->site_url); ?>"
				type="text" name="site_url" />
		<span class="notes"><?php echo _('(Web address of your Website)'); ?></span>
	</div>

	<div>
		<label for="site_success">
			<?php echo _('Success URL:'); ?>
		</label>
		<input value="<?php echo $this->escape($this->site_success); ?>"
				type="text" name="site_success" />
		<span class="notes"><?php echo _('(Webpage users will see upon successfull'
			.' subscription. Leave blank to display default welcome page.)');
		?></span>
	</div>

	<div>
		<label for="site_confirm">
			<?php echo _('Confirm URL:'); ?>
		</label>
		<input value="<?php echo $this->escape($this->site_confirm); ?>"
				type="text" name="site_confirm"/>
		<span class="notes"><?php echo _('(Webpage users will see upon'
			.' subscription attempt. Leave blank to display default'
			.' confirmation page.)'); ?></span>
	</div>

	<div>
		<label for="list_confirm">
			<?php echo _('Email Confirmation:'); ?>
		</label>
		<input type="radio" name="list_confirm" value="on"
		<?php
			if ('on' == $this->list_confirm)
			{
				echo 'checked="checked"';
			}
		?>
		/><?php echo _('on'); ?>
		<input type="radio" name="list_confirm" value="off"
		<?php
			if ('on' != $this->list_confirm)
			{
				echo 'checked="checked"';
			}
		?>
		/><?php echo _('off'); ?>
		<span class="notes"><?php echo _('(Set to validate email upon'
			.' subscription attempt.)'); ?></span>
	</div>

	<div>
		<label for="list_exchanger">
			<strong class="required">
				<?php echo _('Mail Exchanger:'); ?>
			</strong>
		</label>
		<select name="list_exchanger">
			<option value="sendmail"
			<?php
				if ('sendmail' == $this->list_exchanger)
				{
					echo 'selected="selected"';
				}
			?>
			>Sendmail</option>
			<option value="mail"
			<?php
				if ('mail' == $this->list_exchanger)
				{
					echo 'selected="selected"';
				}
			?>
			><?php echo _('PHP Mail Function'); ?></option>
			<option value="smtp"
			<?php
				if ('smtp' == $this->list_exchanger)
				{
					echo 'selected="selected"';
				}
			?>
			>SMTP Relay</option>
		</select>
		&nbsp;&nbsp; - &nbsp;&nbsp;
		<a href="ajax/ajax.testexchanger.php" id="testTrigger">
			<?php echo _('Test Exchanger'); ?>
		</a>
		<span class="notes"><?php echo _('(Select Mail Exchanger)'); ?></span>
	</div>

	<div class="hidden" id="configSMTP">
		<br clear="both" />
		<a href="ajax/ajax.smtp.php" id="smtpTrigger">
			<img alt="icon" class="navimage"
					src="<?php echo $this->url['theme']['shared'];
					?>images/icons/right.png"  />
			<?php echo _('Setup your SMTP Servers'); ?>
		</a>
		<span class="notes"><?php echo _('(configure SMTP relays)'); ?></span>
		<br clear="both" />
	</div>


	<input type="submit" value="<?php echo _('Update'); ?>" />
	<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
			alt="loading..." class="hidden" name="loading" />
</form>

<script type="text/javascript">
var showSMTP = function() {
	if(exchanger.val() == 'smtp')
		$('#configSMTP').show();
	else
		$('#configSMTP').hide();
}
var exchanger = $('select[name=list_exchanger]');

$().ready(function(){
	$('#smtpWindow').jqmAddTrigger($('#smtpTrigger'));
	$('#testWindow').jqmAddTrigger($('#testTrigger'));

	exchanger.change(function(){
		$(this).parents('form:eq(0)').submit();
		showSMTP();
	});
	showSMTP();

});
</script>

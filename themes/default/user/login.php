<?php

include $this->template_dir.'/inc/user.header.php';

?>

<h2><?php echo _('Subscriber Login'); ?></h2>

<p><?php echo _('In order to check your subscription status, update your'
	.' information, or unsubscribe, you must enter your email address in the'
	.' field below.'); ?></p>

<?php

include $this->template_dir.'/inc/messages.php';

?>

<form method="post" action="">
	<fieldset>
		<legend><?php echo _('Login'); ?></legend>

		<div>
			<label for="email">
				<strong class="required"><?php echo _('Your Email:'); ?></strong>
			</label>
			<input type="text" name="Email" id="email" size="32" maxlength="60"
					value="<?php echo $this->escape($this->Email); ?>" />
		</div>
	</fieldset>

	<div class="buttons">
		<input type="submit" value="<?php echo _('Login'); ?>" />
	</div>
</form>

<?php

include $this->template_dir.'/inc/user.footer.php';

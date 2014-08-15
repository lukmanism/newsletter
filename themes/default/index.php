<?php

$this->sidebar = false;
include 'inc/configure.header.php';
include 'inc/messages.php';

if ($this->captcha)
{
?>
	<h2><?php echo _('Administrative Login'); ?></h2>

	<p>
	<?php
		echo sprintf(_('You have requested to reset your password. If you are '
				.'sure you want to do this, please fill in the captcha below. '
				.'Enter the text you see between the brackets (%s[]%s).'),
				'<tt>', '</tt>'); 
	?>
	</p>

	<p>
		<strong><?php echo _('Captcha'); ?></strong> -
		<tt>[ <?php echo $this->captcha; ?> ]</tt>
	</p>

	<form method="post" action="">
		<input type="hidden"  name="realdeal"
				value="<?php echo $this->captcha; ?>" />

		<p>
			<?php echo _('Captcha Text:'); ?>
			<input type="text" name="captcha" />
		</p>

		<input type="submit" name="resetPassword"
				value="<?php echo _('Reset Password'); ?>" />
	</form>
<?php
}
else
{
?>
	<h3><?php echo _('Administrative Login'); ?></h3>

	<form method="post" action="">
		<fieldset id="login">
			<legend><?php echo _('Login'); ?></legend>

			<input type="hidden" name="referer"
					value="<?php echo $this->referer; ?>" />

			<div>
				<label for="username"><?php echo _('Username'); ?></label>
				<input type="text" name="username" id="username" />
			</div>

			<div>
				<label for="password"><?php echo _('Password'); ?></label>
				<input type="password" name="password" id="password" />
			</div>

		</fieldset> 

		<div class="buttons">
			<input type="submit" name="submit"
					value="<?php echo _('Log In'); ?>" />
			<input type="submit" name="resetPassword" id="resetPassword"
					class="green" value="<?php echo _('Forgot your password?'); ?>" />
		</div>
	</form>
<?php
}

include 'inc/admin.footer.php';


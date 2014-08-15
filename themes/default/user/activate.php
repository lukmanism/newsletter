<?php

include $this->template_dir.'/inc/user.header.php';

?>

<h2><?php echo _('Update Activation'); ?></h2>

<p>
	<?php echo _('We require that you verify your email address before'
			.' unsubscribing or updating your records. This extra step is necessary'
			.' to maintain your privacy, and to protect you against fraudulent'
			.' activity.'); ?>
</p>
<?php

include $this->template_dir.'/inc/messages.php';

if (!$this->sent)
{
?>
	<p>
	<?php
		echo sprintf(_('An activation email has recently been sent to %s.'
			.' Please check your inbox.'), '<strong>'.$this->email.'</strong>');
	?>
	</p>
<?php
}

include $this->template_dir.'/inc/user.footer.php';


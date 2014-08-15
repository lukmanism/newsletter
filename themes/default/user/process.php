<?php

include $this->template_dir.'/inc/user.header.php';

?>

<h2><?php echo _('Subscription Review'); ?></h2>

<?php
if ($this->back)
{
?>
<p>
	<a href="<?php echo $this->referer; ?>" onClick="history.back(); return false;">
		<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/back.png"
				alt="back icon" class="navimage" />
		<?php echo _('Back to Subscription Form'); ?>
	</a>
</p>
<?php
}
else
{
?>
<p>
	<a href="<?php echo $this->config['site_url']; ?>">
		<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/back.png"
				alt="back icon" class="navimage" />
		<?php
			echo sprintf(_('Return to %s'), $this->config['site_name']);
		?>
	</a>
</p>
<?php
}

include $this->template_dir.'/inc/messages.php';

if ($this->dupe)
{
	echo
		'<p>'
			.sprintf(_('%sUpdate your records%s'), '<a href="login.php">', '</a>')
		.'</p>';
}

include $this->template_dir.'/inc/user.footer.php';


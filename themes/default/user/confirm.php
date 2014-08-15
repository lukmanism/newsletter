<?php

include $this->template_dir.'/inc/user.header.php';

?>

<h2><?php echo _('Subscriber Confirmation'); ?></h2>

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

include $this->template_dir.'/inc/messages.php';

include $this->template_dir.'/inc/user.footer.php';


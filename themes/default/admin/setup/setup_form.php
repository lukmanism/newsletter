<?php

include $this->template_dir.'/inc/admin.header.php';

?>

<h2><?php echo _('Subscription Forms'); ?></h2>

<div id="boxMenu">

<div class="advanced">
	<a href="<?php echo $this->url['base']; ?>subscribe.php">
		<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/form.png"
				alt="form icon" class="navimage" />
		<?php echo _('Default Subscription Form'); ?>
	</a>
	- <?php echo _('Preview the default subscription form. Its look and feel can'
			.' be adjusted through the theme template'
			.' ([theme]/user/subscribe.php).'); ?>
</div>

<div>
	<a href="<?php echo $this->url['base']; ?>form_embed.php">
		<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/embed.png"
				alt="embed icon" class="navimage" />
		<?php echo _('Embedded Subscription From'); ?>
	</a>
	- <?php echo _('Preview subscription forms that you can embed into an area'
			.' of an existing webpage.'); ?>
</div>

<div>
	<a href="<?php echo $this->url['base']; ?>form_generate.php">
		<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/plain.png"
				alt="plain icon" class="navimage" />
		<?php echo _('HTML Subscription Form'); ?>
	</a>
	- <?php echo _('Generate a plain HTML subscription form that you can'
			.' customize to fit your site.'); ?>
</div>

</div>

<?php

include $this->template_dir.'/inc/admin.footer.php';

<?php
ob_start();
include $this->template_dir.'/inc/ui.dialog.php';
$this->capturedHead = ob_get_clean();

include $this->template_dir.'/inc/admin.header.php';
?>

<h2><?php echo _('Embedded Subscription Forms'); ?></h2>

<ul class="inpage_menu">
	<li>
		<a href="setup_form.php">
			<?php echo _('Return to Subscription Forms'); ?>
		</a>
	</li>
</ul>

<p>
	<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/embed.png"
			alt="embed" class="navimage right" />
	<?php
		echo _('Subscription forms can easily be added to your website'
			.' using a line of code. You can use the PHP include listed'
			.' below, or embed the actual HTML. Remember, you can also'
			.' direct subscribers to the ');
	?>
	<a href="<?php echo $this->url['base']; ?>subscribe.php">
		<?php echo _('Default Subscribe Form'); ?>
	</a>.
</p>

<h3><?php echo _('Mini Subscription Form'); ?></h3>

<p>
	<?php
		echo _('This prints a form which prompts for a user\'s email address.'
				.' If the inputted email exists as a registered subscriber, it'
				.' redirects to the subscriber update page. If not, it redirects to'
				.' the the default subscription form.');
	?>
</p>

<ul>
	<li>
		<a href="#" class="miniPreview"><?php echo _('Preview'); ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url['base']; ?>embed.miniform.php">
			<?php echo _('URL'); ?>
		</a>
	</li>
</ul>

<p>
	PHP: <tt style="color: green;">include('<?php echo $this->config['app']['path'];
		?>embed.miniform.php');</tt>
</p>

<h3><?php echo _('Full Subscription Form'); ?></h3>

<p><?php echo _('This prints the entire subscription form.'); ?></p>

<ul>
	<li><a href="#" class="fullPreview"><?php echo _('Preview'); ?></a></li>
	<li>
		<a href="<?php echo $this->url['base']; ?>embed.form.php">
			<?php echo _('URL'); ?>
		</a>
	</li>
</ul>

<p>
	PHP: <tt style="color: green;">include('<?php echo $this->config['app']['path'];
		?>embed.form.php');</tt>
</p>

<script type="text/javascript">
$().ready(function(){
	$('#miniPreview').jqm({
		trigger: 'a.miniPreview'
	});
	
	$('#fullPreview').jqm({
		trigger: 'a.fullPreview'
	});
});
</script>

<?php
ob_start();
?>

<h4>
	<?php echo _('Mini Subscription Form'); ?>
	<?php echo _('Preview'); ?>
</h4>

<?php include $this->template_dir.'/subscribe/form.mini.php'; ?>

<hr />

<h4>HTML Source</h4>

<textarea cols="60" rows="11"><?php
		include $this->template_dir.'/subscribe/form.mini.php'; ?></textarea>

<br /><br />&nbsp;

<?php
$this->capturedMini = ob_get_clean();

ob_start();
?>

<h4><?php echo _('Subscription Form'); ?> <?php echo _('Preview'); ?></h4>

<?php include $this->template_dir.'/subscribe/form.subscribe.php'; ?>

<hr />

<h4>HTML Source</h4>

<textarea cols="60" rows="11"><?php
		include $this->template_dir.'/subscribe/form.subscribe.php'; ?></textarea>

<br /><br />&nbsp;

<?php
$this->capturedFull = ob_get_clean();

ob_start();

$this->dialogWide = true;
$this->dialogTall = true;

$this->dialogContent = $this->capturedMini;
$this->dialogId = 'miniPreview';
include $this->template_dir.'/inc/dialog.php';

$this->dialogContent = $this->capturedFull;
$this->dialogId = 'fullPreview';
include $this->template_dir.'/inc/dialog.php';

$this->capturedDialogs = ob_get_clean();

include $this->template_dir.'/inc/admin.footer.php';

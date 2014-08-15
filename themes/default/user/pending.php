<?php

include $this->template_dir.'/inc/user.header.php';

?>

<h2><?php echo _('Pending Changes'); ?></h2>

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


if (!$this->nodisplay)
{
?>
	<form method="post" action="">
		<fieldset>
			<legend>Pending user</legend>
			<div class="buttons">
				<input type="submit" name="reconfirm"
						value="<?php echo _('SEND another confirmation email'); ?>" />
				<input type="submit" name="cancel"
						value="<?php echo _('CANCEL your pending request'); ?>" />
			</div>
		</fieldset>
	</form>
<?php	
}

include $this->template_dir.'/inc/user.footer.php';


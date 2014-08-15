<?php 

include $this->template_dir.'/inc/admin.header.php';

?>

<h2><?php echo _('Mailings Page'); ?></h2>


<div id="boxMenu">

	<div>
		<a href="<?php echo $this->url['base']; ?>mailings_start.php">
			<img src="<?php echo $this->url['theme']['shared'];
					?>images/icons/typewritter.png" alt="typewritter icon"
					class="navimage" /><?php echo _('Send'); ?>
		</a>
		- <?php echo _('Create and send a mailing.'); ?>
	</div>

	<div>
		<a href="<?php echo $this->url['base']; ?>mailings_history.php">
			<img src="<?php echo $this->url['theme']['shared'];
					?>images/icons/history.png" alt="calendar icon"
					class="navimage" /><?php echo _('History'); ?>
		</a>
		- <?php echo _('View mailings that have already been sent.'); ?>
	</div>

</div>

<?php 

include($this->template_dir.'/inc/admin.footer.php');


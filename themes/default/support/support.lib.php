<?php

ob_start();
include $this->template_dir.'/inc/ui.dialog.php';
$this->capturedHead = ob_get_clean();
include $this->template_dir.'/inc/admin.header.php';
include $this->template_dir.'/inc/messages.php';

?>

<h2>
<?php
	echo _('poMMo support v0.02');
?>
</h2>

<ul>
	<li>
		<a href="<?php echo $this->url['base']; ?>file.clearWork.php"
				title="Clear Work Directory" class="modal"><?php
				echo _('Clear Work Directory'); ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url['base']; ?>mailing.test.php"
				onclick="return !window.open(this.href)"><?php
				echo _('Test Mailing Processor'); ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url['base']; ?>mailing.kill.php"
				title="Terminate Current Mailing" class="modal"><?php
				echo _('Terminate Current Mailing'); ?></a>
	</li>
	<li>
		<a href="<?php echo $this->url['base']; ?>mailing.runtime.php"
				onclick="return !window.open(this.href)"><?php
				echo _('Test Max Runtime (takes 90 seconds)'); ?></a>
	</li>
	<li>
		<a class="warn" href="<?php echo $this->url['base']; ?>db.clear.php"
				title="Reset Database"><?php
				echo _('Reset Database (clears all subscribers, groups, fields)');
				?></a>
	</li>
	<li>
		<a class="warn" href="<?php echo $this->url['base']; ?>db.subscriberClear.php"
				title="Reset Subscribers"><?php
				echo _('Reset Subscribers (clears all susbcribers)');
				?></a>
	</li>
	<li>
		<a class="warn" href="<?php echo $this->url['base']; ?>db.sample.php"
				title="Load Sample Data"><?php
				echo _('Load Sample Data (resets database, loads sample data)');
				?></a>
	</li>
</ul>

<script type="text/javascript">
$().ready(function() {
	$('a.warn').click(function() {
		var str = this.innerHTML;
		return confirm("<?php echo _('Are you sure you want to '); ?>" +
				str + "<?php echo _('?\nData will be lost permanently.'); ?>");
	});

	// Setup Modal Dialogs
	PommoDialog.init();
});
</script>

<?php

ob_start();
$this->dialogId = 'dialog';
include $this->template_dir.'/inc/dialog.php';
$this->capturedDialogs = ob_get_clean();
include $this->template_dir.'/inc/admin.footer.php';


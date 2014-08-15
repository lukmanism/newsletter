<?php

include $this->template_dir.'/inc/admin.header.php';

?>

<h2><?php echo _('Import Subscribers'); ?></h2>

<fieldset>
	<legend><?php echo _('Import'); ?></legend>

	<div>
	<?php
		echo sprintf(_('%s non-duplicate subscribers will be imported. %s'
			.' were ignored as duplicates.'), $this->tally, $this->dupes);

		if ($this->flag)
		{
		?>
			<p class="warn"><?php echo _('Notice: Imported subscribers will'
				.' be flagged to update their records'); ?></p>
		<?php
		}
	?>
	</div>

	<div class="buttons" id="buttons">
		<?php echo _('Are you sure?'); ?>
		<a href="#" id="import"><button><?php echo _('Yes'); ?></button></a>
		<a href="subscribers_import.php">
			<button><?php echo _('No'); ?></button>
		</a>
	</div>
</fieldset>

<div id="ajax" class="warn hidden">
	<img src="{$url.theme.shared}images/loader.gif" alt="Importing..." />
	... <?php echo _('Processing'); ?>
</div>

<script type="text/javascript">
$().ready(function(){
	$('#import').click(function() {
		
		$('#buttons').hide();
		
		$('#ajax').show().load('import_txt.php?continue=true',{}, function() {
			$('#ajax').removeClass('warn').addClass('error');
		});
		
		return false;
	
	});
});
</script>

<?php

include $this->template_dir.'/inc/admin.footer.php';


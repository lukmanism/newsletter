<?php
ob_start();
?>
<div id="warnmsg" class="warn">
<?php
	if ($this->confirm['msg'])
	{
		echo $this->confirm['msg'];
	}
?>
</div>

<p><strong><?php echo _('Confirm your action.'); ?></strong></p>

<div style="float: left; margin:10px 30px;">
	<a href="<?php echo $this->confirm['nourl']; ?>" class="jqmClose"><img src="<?php echo $this->url['theme']['shared']; ?>images/icons/undo.png" alt="undo icon" class="navimage" /> <?php echo _('No'); ?> <?php echo _('please return'); ?></a>
</div>

<div style="float: left; margin:10px 30px;">
	<a href="<?php echo $this->confirm['yesurl']; ?>" class="_yes"><img src="<?php echo $this->url['theme']['shared']; ?>images/icons/ok.png" alt="accept icon" class="navimage" /> <?php echo _('Yes'); ?> <?php echo _('I confirm'); ?></a>
</div>
<?php
$this->confirmMsg = ob_get_clean();
?>

<?php
if ($this->confirm['ajaxConfirm'])
{
?>
	<div id="confirmMsg" style="display: none;">
	<?php
		echo $this->confirmMsg;
	?>
	</div>

	<script type="text/javascript">
	$('#confirm')
		.find('.jqmdMSG')
			.html($('#confirmMsg').html())
			.end()
		.find('a,.jqmClose')
			.click(function(){
				if(this.className == '_yes') {
					$('#<?php echo $this->confirm['targetID']; ?> div.jqmdMSG').load(this.href,function(){$('#confirm').jqmHide(); if(assignForm)assignForm(this);});
					$('#confirm div.jqmdMSG').html('<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" /><?php echo _('Please Wait'); ?>...');
				}
				else
					$('#confirm').jqmHide();
				return false;
			})
			.end()
		.jqmShow();
	</script>
<?php
}
else
{
	include $this->template_dir.'/inc/admin.header.php';
	if ($this->confirm['title'])
	{
		echo '<h2>'.$this->confirm['title'].'</h2>';
	}
	else
	{
		echo '<h2>'._('Confirm').'</h2>';
	}

	echo $this->confirmMsg;
	include $this->template_dir.'/inc/admin.footer.php';
}


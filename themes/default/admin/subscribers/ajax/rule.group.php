<div style="width: 100%; text-align: center; margin: 40px 0; font-size: 130%;">

<form class="json" action="ajax/group.rpc.php?call=addRule" method="post">
<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
<input type="hidden" name="field" value="<?php echo $this->match_id; ?>" />

<select name="logic">
	<option value="is_in"><?php echo _('Include'); ?></option>
	<option value="not_in"><?php echo _('Exclude'); ?></option>
</select>

<?php
	echo sprintf(_('members in group %s.'), '<strong>'.$this->match_name.'</strong>');
?>

<div>
	<input type="submit" value="<?php echo _('Add'); ?>" />
	<input type="submit" value="<?php echo _('Cancel'); ?>" class="jqmClose" />
</div>

</form>
</div>

<script type="text/javascript">

$().ready(function(){
	// shrink window
	$('#dialog div.jqmdBC').removeClass('jqmdTall');
});

</script>


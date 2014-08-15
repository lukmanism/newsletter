<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" class="json">

<div class="output alert"><?php if ($output) {echo($output);} ?></div>

<script type="text/javascript">
$().ready(function()
{	
	var p =
	{	
		colNames:
		[
			'<?php echo _('User'); ?>'
		],
		colModel:
		[
			{name: 'username', width: 350}
		],
		url: 'ajax/users.list.php',
		rowList: [10,25,50]
	};

	poMMo.grid = PommoGrid.init('#grid',p);
});
</script>

<script type="text/javascript">
$(function()
{
	// Setup Modal Dialogs
	PommoDialog.init();
	
	$('#addUser').jqmAddTrigger('a.addUser');

	$('a.modal').click(function()
	{
		var rows = poMMo.grid.getRowIDs();
		if(rows)
		{
			// check for confirmation
			if($(this).hasClass('confirm') && !poMMo.confirm())
			{
				return false;
			}
				
			// serialize the data
			var data = $.param({'users[]': rows});
			
			// rewrite the HREF of the clicked element
			var oldHREF = this.href;
			this.href += (this.href.match(/\?/) ? "&" : "?") + data
			
			// trigger the modal dialog, or visit the URL
			if($(this).hasClass('visit'))
			{
				window.location = this.href;
			}
			else
			{
				$('#dialog').jqmShow(this);
			}
			
			// restore the original HREF
			this.href = oldHREF;
			
			poMMo.grid.reset();
		}
		return false;
	});
});

poMMo.callback.deleteUser = function(p)
{
	poMMo.grid.delRow(p.users);
	$('#dialog').jqmHide();
}

</script>

<table id="grid" class="scroll" cellpadding="0" cellspacing="0"></table>
<div id="gridPager" class="scroll" style="text-align:center;"></div>

<ul class="inpage_menu">
	<li>
		<a href="ajax/user_add.php" class='addUser'>
			<?php echo _('New'); ?>
		</a>
	</li>
	<li>
		<a href="ajax/users.rpc.php?call=delete" class="modal confirm">
			<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/delete.png"/>
			<?php echo _('Delete'); ?>
		</a>
	</li>
</ul>

<div>
    <label for="admin_email"><strong class="required"><?php echo _('Administrator Email:'); ?></strong></label>
    <input type="text" name="admin_email" value="<?php echo $this->admin_email; ?>" />
    <span class="notes"><?php echo _('(email address of administrator)'); ?></span>
</div>

<input type="submit" value="<?php echo _('Update'); ?>" />

<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif" alt="loading..." class="hidden" name="loading" />

</form>


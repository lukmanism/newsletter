<?php

ob_start();

include $this->template_dir.'/inc/ui.dialog.php';
include $this->template_dir.'/inc/ui.grid.php';

$this->capturedHead = ob_get_clean();

$this->sidebar = false;
include $this->template_dir.'/inc/admin.header.php';

?>

<ul class="inpage_menu">
	<li>
		<a href="admin_mailings.php">
		<?php
			echo sprintf(_('Return to %s'), $this->returnStr);
		?>
		</a>
	</li>
</ul>

<h2><?php echo _('Mailings History'); ?></h2>
<?php

include $this->template_dir.'/inc/messages.php';

if ($this->tally > 0)
{
?>
	<table id="grid" class="scroll" cellpadding="0" cellspacing="0"></table>
	<div id="gridPager" class="scroll" style="text-align:center;"></div>

	<ul class="inpage_menu">
		<li><a href="ajax/mailing_preview.php" class="modal visit"><img src="<?php echo $this->url['theme']['shared']; ?>images/icons/mailing_small.png"/><?php echo _('View Mailing'); ?></a></li>
		<li><a href="ajax/history.rpc.php?call=notice" class="modal"><img src="<?php echo $this->url['theme']['shared']; ?>images/icons/examine_small.png"/><?php echo _('View Last Notices'); ?></a></li>
		<li><a href="ajax/history.rpc.php?call=reload" class="modal visit"><img src="<?php echo $this->url['theme']['shared']; ?>images/icons/typewritter_small.png"/><?php echo _('Reload Checked Mailing'); ?></a></li>
		<li><a href="ajax/history.rpc.php?call=delete" class="modal confirm"><img src="<?php echo $this->url['theme']['shared']; ?>images/icons/delete.png"/><?php echo _('Delete Checked Mailings'); ?></a></li>
	</ul>
    <ul class="inpage_menu">
        <li>
            <a href="#" id="exportCsv">
                <?php echo _('Export Mailing Hits'); ?>
            </a>
        </li>
    </ul>

	<script type="text/javascript">
    // CSV export hits action
    $(function() {
        $('#exportCsv').click(function(e) {
            var rows = poMMo.grid.getRowIDs();

            if (Array !== rows.constructor) {
                alert(
                    "<?php echo _('You must select a mailing to export'); ?>"
                );
                return e.preventDefault();
            }

            if (1 < rows.length) {
                alert("<?php echo _('Select just one mailing to export'); ?>");
                return e.preventDefault();
            }

            $(this).attr('href', 'export_hits.php?mailing=' + rows[0]);
        });
    });

	$().ready(function() {

		var p = {
		colNames: [
			'ID',
			'<?php echo _('Subject'); ?>',
			'<?php echo _('Group (count)'); ?>',
			'<?php echo _('Sent'); ?>',
			'<?php echo _('Started'); ?>',
			'<?php echo _('Finished'); ?>',
			'<?php echo _('Status'); ?>',
			'<?php echo _('Hits'); ?>',
		],
		colModel: [
			{name: 'id', index: 'id', hidden: true, width: 1},
			{name: 'subject', width: 110},
			{name: 'group', width: 120},
			{name: 'sent', width: 40},
			{name: 'start', width: 130},
			{name: 'end', width: 100},
			{name: 'status', width: 70},
			{name: 'hits', width: 70}
		],
		url: 'ajax/history.list.php'
		};

		poMMo.grid = PommoGrid.init('#grid',p);
	});
	</script>

	<script type="text/javascript">
	$().ready(function(){

		// Setup Modal Dialogs
		PommoDialog.init();

		$('a.modal').click(function(){
			var rows = poMMo.grid.getRowIDs();
			if(rows) {

				// check for confirmation
				if($(this).hasClass('confirm') && !poMMo.confirm())
					return false;

				// serialize the data
				var data = $.param({'mailings[]': rows});

				// rewrite the HREF of the clicked element
				var oldHREF = this.href;
				this.href += (this.href.match(/\?/) ? "&" : "?") + data

				// trigger the modal dialog, or visit the URL
				if($(this).hasClass('visit'))
					window.location = this.href;
				else
					$('#dialog').jqmShow(this);

				// restore the original HREF
				this.href = oldHREF;

				poMMo.grid.reset();

			}
			return false;
		});
	});

	poMMo.callback.deleteMailing = function(p) {
		poMMo.grid.delRow(p.ids);
		$('#dialog').jqmHide();
	}

	</script>
<?php
}
else
{
?>
	<strong><?php echo _('No records returned.'); ?></strong>
<?php
}

ob_start();

$this->dialogId = 'dialog';
$this->dialogWide = true;
$this->dialogTall = true;
include $this->template_dir.'/inc/dialog.php';

$this->capturedDialogs = ob_get_clean();

include $this->template_dir.'/inc/admin.footer.php';

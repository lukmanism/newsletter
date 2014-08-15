<?php

ob_start();

?>
<script type="text/javascript" src="<?php echo $this->url['theme']['shared'];
		?>js/jq/jquery.js"></script>
<script type="text/javascript" src="<?php echo $this->url['theme']['shared'];
		?>js/pommo.js"></script>
<?php

include $this->template_dir.'/inc/ui.grid.php';

$this->capturedHead = ob_get_clean();

$this->sidebar = false;
include $this->template_dir.'/inc/user.header.php';
?>

<h2><?php echo _('Mailings History'); ?></h2>

<?php

include $this->template_dir.'/inc/messages.php';

if ($this->tally > 0)
{
?>
	<table id="grid" class="scroll" cellpadding="0" cellspacing="0"></table>
	<div id="gridPager" class="scroll" style="text-align:center;"></div>

    <ul class="inpage_menu">
        <li>
            <a href="mailings.php" class="visit">
                <img src="<?php echo $this->url['theme']['shared'];
                        ?>images/icons/mailing_small.png"/>
                <?php echo _('View Mailing'); ?>
            </a>
        </li>
    </ul>

	<script type="text/javascript">
	$().ready(function() {

		var p = {
		colNames: [
			'ID',
			'<?php echo _('Subject'); ?>',
			'<?php echo _('Sent'); ?>'
		],
		rowNum: <?php echo $this->state['limit']; ?>,
		rowList: [],
		colModel: [
			{name: 'id', index: 'id', hidden: true, width: 1},
			{name: 'subject', width: 150},
			{name: 'start', width: 130}
		],
		url: 'ajax/mailing.list.php'
		};

		poMMo.grid = PommoGrid.init('#grid',p);
	});
	</script>

	<script type="text/javascript">
	$().ready(function(){
		$('a.visit').click(function(){
			var rows = poMMo.grid.getRowIDs();
			if(rows) {
				// serialize the data
				var data = $.param({'mail_id': rows});

				// rewrite the HREF of the clicked element
				var oldHREF = this.href;
				this.href += (this.href.match(/\?/) ? "&" : "?") + data

				window.location = this.href;
			}
			return false;
		});
	});

	</script>
<?php
}
else
{
?>
	<strong><?php echo _('No records returned.'); ?></strong>
<?php
}

include $this->template_dir.'/inc/user.footer.php';


<?php
ob_start();
include $this->template_dir.'/inc/ui.dialog.php';
include $this->template_dir.'/inc/ui.grid.php';
include $this->template_dir.'/inc/ui.form.php';
$this->capturedHead = ob_get_clean();

$this->sidebar = false;
include $this->template_dir.'/inc/admin.header.php';
?>

<ul class="inpage_menu">
	<li>
		<a href="ajax/subscriber_add.php" class="addTrigger"
				title="<?php echo _('Add Subscribers'); ?>">
			<?php echo _('Add Subscribers'); ?>
		</a>
	</li>

	<li>
		<a href="ajax/subscriber_del.php?status=<?php echo $this->state['status'];
				?>" title="<?php echo _('Remove Subscribers'); ?>"
				class="delTrigger">
			<?php echo _('Remove Subscribers'); ?>
		</a>
	</li>

	<li>
		<a href="ajax/subscriber_export.php?type=csv"
				title="<?php echo _('Export Subscribers'); ?>"
				class="expTrigger">
			<?php echo _('Export Subscribers'); ?>
		</a>
	</li>

	<li>
		<a href="admin_subscribers.php"
				title="<?php echo _('Return to Subscribers Page'); ?>">
			<?php echo _('Return to Subscribers Page'); ?>
		</a>
	</li>
</ul>

<?php

include $this->template_dir.'/inc/messages.php';

?>

<form method="post" action="" id="orderForm">
	<fieldset class="click">
		<legend class="click"><?php echo _('View'); ?> &raquo;</legend>
		<ul class="inpage_menu view">
			<li>
				<label>
					<?php echo _('View'); ?>
					<select name="status">
						<option value="1"
						<?php
							if (1 == $this->state['status'])
							{
								echo 'selected="selected"';
							}
						?>><?php echo _('Active Subscribers'); ?></option>
						<option value="1">------------------</option>
						<option value="0"
						<?php
							if (0 == $this->state['status'])
							{
								echo 'selected="selected"';
							}
						?>><?php echo _('Unsubscribed'); ?></option>
						<option value="2"
						<?php
							if (2 == $this->state['status'])
							{
								echo 'selected="selected"';
							}
						?>><?php echo _('Pending'); ?></option>
					</select>
				</label>
			</li>
			<li>
				<label>
					<?php echo _('Belonging to Group'); ?>
					<select name="group">
						<option value="all"
						<?php
							if ('all' == $this->state['group'])
							{
								echo 'selected="selected"';
							}
						?>><?php echo _('All Subscribers'); ?></option>
						<option value="all">---------------</option>
						<?php
							foreach ($this->groups as $id => $g)
							{
							?>
								<option value="<?php echo $id; ?>"
								<?php
									if ($id == $this->state['group'])
									{
										echo 'selected="selected"';
									}
								?>><?php echo $g['name']; ?></option>
							<?php
							}
						?>
					</select>
				</label>
			</li>
		</ul>
	</fieldset>
</form>

<form method="post" action="" id="searchForm">
	<fieldset class="click">
		<legend class="click"><?php echo _('Search'); ?> &raquo;</legend>
		<ul class="inpage_menu search">
			<li>
				<label>
					<?php echo _('Find Subscribers where'); ?>
					<select name="searchField">
						<option value="email"
						<?php
							if ('email' == $this->state['search']['field'])
							{
								echo 'selected="selected"';
							}
						?>><?php echo _('email'); ?></option>
						<?php
							foreach ($this->fields as $id => $f)
							{
							?>
								<option value="<?php echo $id; ?>"
								<?php
									if ($id == $this->state['search']['field'])
									{
										echo 'selected="selected"';
									}
								?>><?php echo $f['name']; ?></option>
							<?php
							}
						?>
						<option value="time_registered"
						<?php
							if ('time_registered' == $this->state['search']['field'])
							{
								echo 'selected="selected"';
							}
						?>><?php echo _('time registered'); ?></option>
						<option value="time_touched"
						<?php
							if ('time_touched' == $this->state['search']['field'])
							{
								echo 'selected="selected"';
							}
						?>><?php echo _('time last updated'); ?></option>
						<option value="ip"
						<?php
							if ('ip' == $this->state['search']['field'])
							{
								echo 'selected="selected"';
							}
						?>><?php echo _('IP Address'); ?></option>
					</select>
				</label>
			</li>
		</ul>

		<ul class="inpage_menu search">
			<li>
				<label><?php echo _('is like'); ?>
					<input value="<?php echo $this->escape(
							$this->state['search']['string']); ?>" type="text"
							name="searchString" />
				</label>
			</li>
			<li>
				<input type="submit" name="submit"
						value="<?php echo _('Search'); ?>" />
			</li>

			<?php
				if (!empty($this->state['search']))
				{
				?>
				<li>
					<input type="submit" name="searchClear"
							value="<?php echo _('Reset'); ?>" />
				</li>
				<?php
				}
			?>
		</ul>
	</fieldset>
</form>

<?php
if ($this->tally > 0)
{
?>
	<table id="grid" class="scroll" cellpadding="0" cellspacing="0"></table>
	<div id="gridPager" class="scroll" style="text-align:center;"></div>

	<a href="ajax/subscriber_del.php?status=<?php echo $this->state['status']; ?>"
			class="delTrigger">
		<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/delete.png"
				alt="<?php echo _('Delete'); ?>" />
		<?php echo _('Delete Checked Subscribers'); ?>
	</a>
	<a href="ajax/subscriber_edit.php" class="editTrigger">
		<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/edit.png"
				alt="<?php echo _('Edit'); ?>" />
		<?php echo _('Edit Checked'); ?>
	</a>

	<script type="text/javascript">
	$().ready(function()
	{
		var p =
		{
			url: 'ajax/manage.list.php',
			colNames: [
				'ID',
				'Email',
				<?php
					foreach ($this->fields as $id => $f)
					{
						echo "'".$this->escape($f['name'])."',";
					}
				?>
				'<?php echo $this->escape(_('Registered')); ?>',
				'<?php echo $this->escape(_('Updated')); ?>',
				'<?php echo $this->escape(_('IP Address')); ?>'
			],
			colModel: [
				{name: 'id', index: 'id', hidden: true, width: 1},
				{name: 'email', width: 150},
				<?php
					foreach ($this->fields as $id => $f)
					{
						echo '{name: "d'.$id.'", width: 120},';
					}
				?>
				{name: 'registered', width: 130},
				{name: 'touched', width: 130},
				{name: 'ip', width: 90}
			]
		};

		poMMo.grid = PommoGrid.init('#grid', p);
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
?>

<script type="text/javascript">
$(function()
{
	// Setup Modal Dialogs
	PommoDialog.init();
	$('#add').jqmAddTrigger('a.addTrigger');
	$('#del').jqmAddTrigger('a.delTrigger');
	$('#exp').jqmAddTrigger('a.expTrigger');

	$('a.editTrigger').click(function()
	{
		// prevent edit window from appearing if no row is selected
		if(poMMo.grid.getRowID())
			$('#edit').jqmShow(this);
		return false;
	});


	$('#orderForm select').change(function()
	{
		$('#orderForm')[0].submit();
	});

	$('legend.click').click(function()
	{
		$(this).siblings('ul').slideToggle();
	});

	<?php
		if ($this->state['search'])
		{
		?>
		$('ul.search').slideDown();
		<?php
		}

		if ('all' != $this->state['group'] || 1 != $this->state['statue'])
		{
		?>
		$('ul.view').slideDown('slow');
		<?php
		}
	?>
});
</script>

<?php

ob_start();

$this->dialogId = 'add';
$this->dialogWide = true;
$this->dialogTall = true;
include $this->template_dir.'/inc/dialog.php';

$this->dialogId = 'edit';
include $this->template_dir.'/inc/dialog.php';

$this->dialogId = 'del';
include $this->template_dir.'/inc/dialog.php';

$this->dialogId = 'exp';
include $this->template_dir.'/inc/dialog.php';

$this->capturedDialogs = ob_get_clean();

include $this->template_dir.'/inc/admin.footer.php';

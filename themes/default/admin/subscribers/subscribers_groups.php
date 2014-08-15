<?php
ob_start();
include $this->template_dir.'/inc/ui.cssTable.php';
$this->capturedHead = ob_get_clean();

include $this->template_dir.'/inc/admin.header.php';
?>

<h2><?php echo _('Groups Page'); ?></h2>

<p>
	<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/groups.png"
			class="navimage right" alt="groups icon" />
	<?php
		echo sprintf(_('Subscriber Groups allow you to mail subsets of subscribers'
			.' instead of the entire list. Groups are defined by customizable'
			.' matching rules, and members are automatically assigned based on'
			.' their %ssubscriber field%s values.'),
			'<a href="'.$this->url['base'].'setup_fields.php">', '</a>');
	?>
</p>

<form method="post" action="">
	<?php
		include $this->template_dir.'/inc/messages.php';
	?>
	<fieldset>
		<legend><?php echo _('New group'); ?></legend>
		<div>
			<label for="group_name"><?php echo _('Group name'); ?></label>
			<input type="text" title="<?php echo _('type new group name'); ?>"
					name="group_name" id="group_name" maxlength="60" size="30" />
		</div>
		<div class="buttons">
			<input type="submit" value="<?php echo _('Add'); ?>">
		</div>
	</fieldset>
</form>


<fieldset>
	<legend><?php echo _('Groups'); ?></legend>

	<div id="grid">
		<div class="header">
			<span><?php echo _('Delete'); ?></span>
			<span><?php echo _('Edit'); ?></span>
			<span><?php echo _('Group Name'); ?></span>	
		</div>

		<?php
			$cycle = 0;
			foreach ($this->groups as $id => $name)
			{
				$cycle++;
				if (3 < $cycle)
				{
					$cycle = 1;
				}
			?>
			<div class="r<?php echo $cycle; ?> sortable" id="id<?php echo $id; ?>">
				<span>
					<button onclick="window.location.href='<?php
							echo $_SEVER['PHP_SELF']; ?>?group_id=<?php
							echo $id; ?>&amp;delete=TRUE'; return false;">
						<img src="<?php echo $this->url['theme']['shared'];
								?>images/icons/delete.png" alt="delete icon" />
					</button>
				</span>

				<span>
					<button onclick="window.location.href='groups_edit.php?group=<?php
							echo $id; ?>'; return false;">
						<img src="<?php echo $this->url['theme']['shared'];
								?>images/icons/edit.png" alt="edit icon" />
					</button>
				</span>

				<span><?php echo $name; ?></span>
			</div>
			<?php
			}
		?>
	</div>
</fieldset>
<?php

include $this->template_dir.'/inc/admin.footer.php';


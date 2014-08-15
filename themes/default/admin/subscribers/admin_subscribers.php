<?php

include $this->template_dir.'/inc/admin.header.php';

?>
	
<h2><?php echo _('Subscribers Page'); ?></h2>

<div id="boxMenu">
	<div>
		<a href="<?php echo $this->url['base']; ?>subscribers_manage.php">
			<img alt="manage icon" src="<?php echo $this->url['theme']['shared'];
					?>images/icons/examine.png" class="navimage" />
			<?php echo _('Manage'); ?>
		</a>
		- <?php echo _('subscribers. See an overview of your current and pending'
			.' subscribers. You can add, delete, and edit subscribers from'
			.' here.'); ?>
	</div>

	<div>
		<a href="<?php echo $this->url['base']; ?>subscribers_import.php">
			<img alt="user icon" src="<?php echo $this->url['theme']['shared'];
					?>images/icons/import.png" class="navimage" />
			<?php echo _('Import'); ?>
		</a>
		- <?php echo _('Subscribers. You can import large amounts of subscribers'
			.' using files stored on your computer.'); ?>
	</div>

	<div>
		<a href="<?php echo $this->url['base']; ?>subscribers_groups.php">
			<img alt="group icon" src="<?php echo $this->url['theme']['shared'];
					?>images/icons/groups.png" class="navimage" />
			<?php echo _('Groups'); ?>
		</a>
		- <?php echo _('Manage "mailing groups" from this area. Mailing groups'
			.' allow you to mail subsets of your subscribers, rather than just'
			.' the entire list.'); ?>
	</div>
</div>

<?php

include $this->template_dir.'/inc/admin.footer.php';


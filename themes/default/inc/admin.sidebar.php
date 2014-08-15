<div id="sidebar">

<ul id="nav">
<li><a href="<?php $this->url.base?>admin_mailings.php">
        <?php echo _('Mailings')?></a>
        <?php if ($this->section == "mailings") : ?>
            <ul>
            <li><a href="mailings_start.php"><?php echo _('Send')?></a></li>
            <li><a href="mailings_history.php"><?php echo _('History')?></a></li>
            </ul>
        <?php endif; ?>
</li>

<li><a href="<?php $this->url.base?>admin_subscribers.php">
        <?php echo _('Subscribers')?></a>
	<?php if ($this->section == "subscribers") : ?>
            <ul>
            <li><a href="subscribers_manage.php"><?php echo _('Manage')?></a></li>
            <li><a href="subscribers_import.php"><?php echo _('Import')?></a></li>
            <li><a href="subscribers_groups.php"><?php echo _('Groups')?></a></li>
            </ul>
        <?php endif; ?>
</li>

<li><a href="<?php $this->url.base?>admin_setup.php">
        <?php echo _('Setup')?></a>
	<?php if ($this->section == "setup") : ?>
            <ul>
            <li class="advanced"><a href="setup_configure.php"><?php echo _('Configure')?></a></li>
            <li><a href="setup_fields.php"><?php echo _('Fields')?></li>
            <li><a href="setup_form.php"><?php echo _('Setup Form')?></a></li>
            </ul>
        <?php endif; ?>
</li>
</ul>

<div class="extra">
    <?php if ($this->config['demo_mode'] == "on") { ?>
        <p><img src="<?php echo($this->url['theme']['shared']);?>images/icons/demo.png" alt="Key icon" class="sideimage" />
            <?php echo _('Demonstration mode is ON.')?></p>
        <?php } else { ?>
            <p><img src="<?php echo($this->url['theme']['shared']);?>images/icons/nodemo.png" alt="World icon" class="sideimage" />
            <?php echo _('Demonstration mode is OFF.')?></p>
    <?php }; ?>   
</div>

</div>

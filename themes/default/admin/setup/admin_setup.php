<?php include($this->template_dir.'/inc/admin.header.php'); ?>

<h2><?php echo _('Setup Page') ?></h2>

<div id="boxMenu">

<div class="advanced">
    <a href="setup_configure.php">
        <img src="<?php echo($this->url['theme']['shared']); ?>images/icons/settings.png" alt="settings icon" 
             class="navimage" />
        <?php echo _('Configure') ?>
    </a> - <?php echo _('Set your mailing list name, its default behaviour,'
			.' and the administrator\'s information.') ?>
</div>

<div>
    <a href="<?php echo($this->url_base) ?>setup_fields.php">
        <img src="<?php echo($this->url['theme']['shared']); ?>images/icons/fields.png" alt="subscriber icon" 
             class="navimage" />
        <?php echo _('Subscriber Fields') ?>
    </a> - <?php echo _('Choose the information you\'d like to collect from your subscribers.') ?>
</div>

<div>
    <a href="<?php echo($this->url_base) ?>setup_form.php">
        <img src="<?php echo($this->url['theme']['shared']); ?>images/icons/form.png" alt="form icon"
             class="navimage" />
        <?php echo _('Subscription Form') ?>
    </a> - <?php echo _('Preview and Generate the subscription form for your website.') ?>
</div>

</div>

<?php include($this->template_dir.'/inc/admin.footer.php'); ?>

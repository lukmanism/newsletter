<?php
ob_start();
include $this->template_dir . '/inc/jquery.ui.php';
include $this->template_dir.'/inc/ui.form.php';
include $this->template_dir.'/inc/ui.dialog.php';
include $this->template_dir.'/inc/ui.tabs.php';
include $this->template_dir.'/inc/ui.slider.php';
include $this->template_dir.'/inc/ui.grid.php';
$this->capturedHead = ob_get_clean();
$this->sidebar = false;
include $this->template_dir.'/inc/admin.header.php';
?>

<ul class="inpage_menu">
    <li><a href="admin_setup.php" title="<?php echo _('Return to Setup Page'); ?>">
            <?php echo _('Return to Setup Page'); ?></a></li>
</ul>

<h2><?php echo _('Configure'); ?></h2>

<p><img src="<?php echo($this->url['theme']['shared']); ?>images/icons/settings.png" alt="settings icon"
        class="navimage right" />
        <?php echo _('You can change the login information, set website and mailing list parameters, end enable demonstration mode. If you enable demonstration mode, no emails will be sent from the system.'); ?>
</p>

<?php include $this->template_dir.'/inc/messages.php'; ?>

<br class="clear">

<div id="tabs">
    <ul>
        <li><a href="ajax/users.php"><span><?php echo _('Users'); ?></span></a></li>
        <li><a href="ajax/general.php"><span><?php echo _('General'); ?></span></a></li>
        <li><a href="ajax/mailings.php"><span><?php echo _('Mailings'); ?></span></a></li>
        <li><a href="ajax/messages.php"><span><?php echo _('Messages'); ?></span></a></li>
        <?php if ('dev' === getenv('POMMO_ENV')) { ?>
        <li><a href="ajax/bounces.php"><span><?php echo _('Bounces'); ?></span></a></li>
        <?php } ?>
    </ul>
</div>

<br class="clear">
<br class="clear">&nbsp;

<script type="text/javascript">
    $().ready(function(){

        PommoDialog.init();

        poMMo.tabs = PommoTabs.init('#tabs');
        // override changeTab function
        PommoTabs.change = function() { return true; };

        <?php
        $selectedTab = $_GET['tab'];
        if (!is_Null($selectedTab))
        {
            echo('var hash = "#'.strtolower($selectedTab).'";');
        } else
        {
            echo('var hash = location.hash.toLowerCase();');
        }
        ?>

        switch(hash) {
        case '#users': $('#tabs li a:eq(0)').click();
        break;
        case '#general': $('#tabs li a:eq(1)').click();
        break;
        case '#mailings': $('#tabs li a:eq(2)').click();
        break;
        case '#messages': $('#tabs li a:eq(3)').click();
        break;
        }
    });
</script>

<?php
ob_start();

//Add User Dialog
$this->dialogId = 'addUser';
$this->dialogWide = true;
$this->dialogTall = true;
include $this->template_dir.'/inc/dialog.php';

//Throttle Dialog
$this->dialogId = 'throttleWindow';
$this->dialogTitle = $this->throttleTitle;
$this->dialogTall = 'true';
include $this->template_dir.'/inc/dialog.php';

//Smtp Dialog
$this->dialogId = 'smtpWindow';
$this->dialogTitle = $this->smtpTitle;
$this->dialogTall = 'true';
include $this->template_dir.'/inc/dialog.php';

//Test Dialog
$this->dialogId = 'testWindow';
$this->dialogTitle = $this->testTitle;
include $this->template_dir.'/inc/dialog.php';

//Dialog
$this->dialogId = 'dialog';
$this->dialogWide = 'true';
$this->dialogTall = 'true';
include $this->template_dir.'/inc/dialog.php';

//Now write them out
$this->capturedDialogs = ob_get_clean();

//Lastly add the footer
include $this->template_dir.'/inc/admin.footer.php';

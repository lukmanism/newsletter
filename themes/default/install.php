<?php
$this->sidebar = false;
include 'inc/configure.header.php';
?>

<h2><?php echo _('Installation'); ?></h2>

<h3><?php echo _('Online install'); ?></h3>

<ul class="inpage_menu">
    <li><?php echo $this->config['app']['weblink']; ?></li>
</ul>

<p>
    <?php echo _('Welcome to the online installation process. We have connected'
		.' to the database and set your language successfully. Fill in the values'
		.' below, and you\'ll be on your way!'); ?>
</p>

<?php
include 'inc/messages.php';
?>

<?php
if (!$this->installed)
{
    ?>
    <form method="post" action="">
        <fieldset>
            <legend><?php echo _('Configuration Options'); ?></legend>
            <div>
                <div class="error">
                    <?php
                    echo $this->formError['list_name'];
                    ?>
                </div>
                <label for="list_name">
                    <strong class="required">
                        <?php echo _('Name of Mailing List:'); ?>
                    </strong>
                </label>
                <input type="text" size="32" maxlength="60" name="list_name"
                       value="<?php echo $this->escape($this->list_name); ?>"
                       id="list_name" />
                <span class="notes">
                    <?php echo _('(ie. Pommos\'s Mailing List)'); ?>
                </span>
            </div>
            <div>
                <div class="error">
                    <?php
                    echo $this->formError['site_name'];
                    ?>
                </div>
                <label for="site_name">
                    <strong class="required">
                        <?php echo _('Name of Website:'); ?>
                    </strong>
                </label>
                <input type="text" size="32" maxlength="60" name="site_name"
                       value="<?php echo $this->escape($this->site_name); ?>" id="site_name" />
                <span class="notes">
                    <?php echo _('(ie. The poMMo Website)'); ?>
                </span>
            </div>
            <div>
                <div class="error">
                    <?php
                    echo $this->formError['site_url'];
                    ?>
                </div>
                <label for="site_url">
                    <strong class="required">
                        <?php echo _('Website URL:'); ?>
                    </strong>
                </label>
                <input type="text" size="32" maxlength="60" name="site_url"
                       value="<?php echo $this->escape($this->site_url); ?>" id="site_url" />
                <span class="notes">
                    <?php echo _('(ie. http://www.pommo-rocks.com/)'); ?>
                </span>
            </div>
            <div>
                <div class="error">
                    <?php
                    echo $this->formError['admin_password'];
                    ?>
                </div>
                <label for="admin_password">
                    <strong class="required">
                        <?php echo _('Administrator Password:'); ?>
                    </strong>
                </label>
                <input type="password" size="32" maxlength="60"
                       name="admin_password" id="admin_password"
                       value="<?php echo $this->escape($this->admin_password);
                        ?>" />
                <span class="notes">
                    <?php echo _('(you will use this to login)'); ?>
                </span>
            </div>
            <div>
                <div class="error">
                    <?php
                    echo $this->formError['admin_password2'];
                    ?>
                </div>
                <label for="admin_password2">
                    <strong class="required">
                        <?php echo _('Verify Password:'); ?>
                    </strong>
                </label>
                <input type="password" size="32" maxlength="60"
                       name="admin_password2" id="admin_password2"
                       value="<?php echo $this->escape($this->admin_password2);
                        ?>" />
                <span class="notes">
                    <?php echo _('(enter password again)'); ?>
                </span>
            </div>
            <div>
                <div class="error">
                    <?php
                    echo $this->formError['admin_email'];
                    ?>
                </div>
                <label for="admin_email">
                    <strong class="required">
                        <?php echo _('Administrator Email:'); ?>
                    </strong>
                </label>
                <input type="text" size="32" maxlength="60" name="admin_email"
                       value="<?php echo $this->escape($this->admin_email); ?>"
                       id="admin_email" />
                <span class="notes">
                    <?php echo _('(enter your valid email address)'); ?>
                </span>
            </div>
        </fieldset>
        <div class="buttons">
            <input type="submit" id="installerooni" name="installerooni"
                   value="<?php echo _('Install'); ?>" />
                   <?php
                   if ($this->debug)
                   {
                       ?>
                <input type="hidden" name="debugInstall" value="true" />
                <input type="submit" id="disableDebug" name="disableDebug"
                       value="<?php echo _('To disable debugging');
                       ?> <?php echo _('Click Here'); ?>" />
                       <?php
                   } else
                   {
                       ?>
                <input type="submit" id="debugInstall" name="debugInstall"
                       value="<?php echo _('To enable debugging'); ?> <?php echo _('Click Here'); ?>" />
                       <?php
                   }
                   ?>
        </div>
    </form>
    <?php
}
?>

<p>
    <a href="<?php echo $this->url['base']; ?>index.php">
        <img src="<?php echo $this->url['theme']['shared']; ?>images/icons/back.png"
             alt="back icon" class="navimage" />
<?php echo _('Continue to login page'); ?>
    </a>
</p>

<?php
include 'inc/admin.footer.php';
print_r($this->formError);


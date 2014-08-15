<?php

$this->sidebar = false;
include 'inc/configure.header.php';
include 'inc/messages.php';

?>

<h3><?php echo _('Configure'); ?> </h3>

<form method="post" action="">
<fieldset id="login">
<legend>
	<?php echo _('You need to configure pommo before you can use it'); ?>
</legend>

<div>
<label for="dbhost"><?php echo _('Database Host'); ?></label>
<input type="text" name="dbhost" id="dbhost" value="<?php echo $this->dbhost; ?>" />
</div>

<div>
<label for="dbname"><?php echo _('Database Name'); ?></label>
<input type="text" name="dbname" id="dbname" value="<?php echo $this->dbname; ?>" />
</div>

<div>
<label for="dbuser"><?php echo _('Database User'); ?></label>
<input type="text" name="dbuser" id="dbuser" value="<?php echo $this->dbuser; ?>" />
</div>

<div>
<label for="dbpass"><?php echo _('Database Password'); ?></label>
<input type="password" name="dbpass" id="dbpass" />
</div>

</fieldset> 

<div class="buttons">

<input type="hidden" name="configure" value="1">

<input type="submit" name="submit" value="<?php echo _('Continue'); ?>" />

</div>

</form>

<?php
	
include 'inc/admin.footer.php';

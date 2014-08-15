<?php

include $this->template_dir.'/inc/admin.header.php';

?>

<h2><?php echo _('Support Page'); ?></h2>

<p><a href="support.lib.php"><?php echo _('poMMo Support Library'); ?></a></p>

<p>
<?php
	sprintf(_('poMMo version: %s + %s'), $this->version, $this->revision);
?>
</p>

<p><i><?php echo _('Coming to a theatre near you'); ?></i></p>

<h3><?php echo _('MY NOTES:'); ?></h3>

<pre>
<?php
	echo
	_('+ Enhanced support library').PHP_EOL
	._('+ PHPInfo()  (or specifically mysql, php, gettext, safemode, webserver, versions, etc.)').PHP_EOL
	._('+ Database dump (allow selection of tables.. provide a dump of them)').PHP_EOL
	._('+ Link to README.HTML  +  local documentation').PHP_EOL
	._('+ Link to WIKI documentation').PHP_EOL
	._('	+ Make a user-contributed open WIKI documentation system').PHP_EOL
	._('	+ When support page is clicked, show specific support topics for that page').PHP_EOL
	._('+ Clear All Subscribers').PHP_EOL
	._('+ Reset Database').PHP_EOL
	._('+ Backup Database').PHP_EOL
	._('+ Ensure max run time is 30 seconds if safe mode is enabled').PHP_EOL;
?>
</pre>

<?php

include $this->template_dir.'/inc/admin.footer.php';

?>


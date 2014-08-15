<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>
		<?php echo $this->config['site_name']; ?>
		<?php echo _('Subscription'); ?>
	</title>
	<link href="<?php echo $this->url['theme']['shared']; ?>css/default.user.css"
			type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="<?php echo $this->url['theme']['shared']; ?>js/jq/jquery.js"></script>
	<?php
		if ($this->datePicker)
		{
			include $this->config['app']['path'].'themes/shared/datepicker/datepicker.php';
		}
	?>
</head>
<body>
	<h2>
		<?php echo $this->config['list_name']; ?>
		<?php echo _('Subscription'); ?>
	</h2>
	<?php
		include $this->template_dir.'/subscribe/form.subscribe.php';
	?>
</body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title><?php echo $this->title; ?></title>
	<link href="<?php echo $this->url['theme']['shared']; ?>css/default.user.css"
			type="text/css" rel="stylesheet" />
	<?php
		echo $this->capturedHead;
	?>
</head>
<body>
	<div id="header">
		<h1>
			<a href="<?php echo $this->config['site_url']; ?>">
			<?php
				echo $this->config['site_name'];
			?>
			</a>
		</h1>
	</div>
	<div id="content">

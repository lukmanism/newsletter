<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" /> 
	<title><?php echo $this->title; ?></title>

	<script src="<?php echo $this->url['theme']['shared'] ?>js/jq/jquery.js"
			type="text/javascript"></script>
	<script src="<?php echo $this->url['theme']['shared'] ?>js/pommo.js"
			type="text/javascript"></script>
	<script type="text/javascript">
		poMMo.confirmMsg = '<?php echo _('Are you sure?'); ?>';
	</script>
	<link href="<?php echo $this->url['theme']['shared'] ?>css/default.admin.css"
			type="text/css" rel="stylesheet"/>
</head>
<body>
	<div id="header">
		<h1>
			<a href="<?php echo $this->config['site_url']; ?>">
				<img src="<?php echo $this->url['theme']['shared'] ?>images/pommo.gif"
						alt="pommo logo" />
				<strong><?php echo $this->config['site_name']; ?></strong>
			</a>
		</h1>
	</div>
	<ul id="menu"></ul>
	<?php
		if (false !== $this->sidebar)
		{
			include 'admin.sidebar.php';
                ?>
			<div id="content">
		<?php
		}
		else
		{
		?>
			<div id="content" class="wide">
		<?php
		}

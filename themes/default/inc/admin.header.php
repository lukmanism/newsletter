<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" /> 
<title><?php echo $this->title; ?></title>

	<?php if (isset($this->jQueryVersion)) {
	?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/<?php echo $this->jQueryVersion ?>/jquery.min.js"
      	type="text/javascript"></script>
	<?php
	} else { ?>
<script src="<?php echo $this->url['theme']['shared'] ?>js/jq/jquery.js"
      type="text/javascript"></script>
	<?php } ?>
<script src="<?php echo $this->url['theme']['shared'] ?>js/pommo.js"
      type="text/javascript"></script>
<script type="text/javascript">
    poMMo.confirmMsg = '<?php echo _('Are you sure?'); ?>';
</script>

<link type="text/css" rel="stylesheet" 
      href="<?php echo $this->url['theme']['shared']?>css/default.admin.css" />
<?php

echo $this->capturedHead;

?>

</head>

<body>

<?php if (!isset($this->simpleTemplate)) { ?>

<div id="header">

<h1><a href="<?php echo($this->config['site_url']) ?>">
       <img src="<?php echo($this->url['theme']['shared']); ?>images/pommo.gif" 
         alt="pommo logo" /> <strong><?php echo($this->config['site_name']) ?></strong>
    </a></h1>
</div>

<ul id="menu">
<li><a href="<?php echo($this->url_base) ?>index.php?logout=TRUE">
        <?php echo _('Logout')?></a></li>
<li class="advanced"><a href="<?php echo($this->url_base) ?>support.php">
        <?php echo _('Support')?></a></li>
<li><a href="<?php echo($this->url_base) ?>admin.php">
        <?php echo _('Admin Page')?></a></li>
</ul>

<?php 
    if ($this->sidebar !== false)
    {
        include($this->template_dir.'/inc/admin.sidebar.php');
        echo('<div id="content">');
    } else 
    {
        echo('<div id="content" class="wide">');
    }
}
?>

        

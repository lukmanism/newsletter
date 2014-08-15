<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<?php
    if (empty($this->charset)) {
        $this->charset = $this->list_charset;
    }
?>
<meta content="text/html;charset=<?php echo $this->charset; ?>"
        http-equiv="Content-Type">
<title><?php echo $this->subject; ?></title>
</head>
<body>
<?php echo $this->body; ?>
</body>
</html>

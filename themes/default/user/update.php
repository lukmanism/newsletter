<script type="text/javascript" src="<?php echo $this->url['theme']['shared']; ?>js/jq/jquery.js"></script>
<?php
ob_start();

if ($this->datePicker)
{
	include $this->config['app']['path'].'themes/shared/datepicker/datepicker.php';
}

$this->capturedHead = ob_get_clean();

// include $this->template_dir.'/inc/user.header.php';

?>

<!-- <h3><?php echo _('Subscriber Update'); ?></h3> -->

<?php

// include $this->template_dir.'/inc/messages.php';

if (!$this->unsubscribe)
{
	// include $this->template_dir.'/subscribe/form.update.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>AYAM&#8482; e-newsletter</title>



<link href="images/AB-style.css" rel="stylesheet" type="text/css" />
<!-- <link href="hide.css" rel="stylesheet" type="text/css" /> -->

</head>

<body>
<table width="983" height="600" border="0" align="center" cellpadding="0" cellspacing="0">
  
  <tr>
    <td width="982" height="601" valign="top" background="images/Hi-grey-email.jpg" style="background-repeat:no-repeat;">
    <table width="776" height="505" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
          <td height="87" colspan="2" align="center">&nbsp;</td>
          <td class="Text-blue1">&nbsp;</td>
        </tr>
        <tr>
          <td width="20%" align="center"><span class="Text-blue1"><img src="taf/images/AB-round-logo.gif" width="135" height="138" /></span></td>
          <td width="80%" class="Text-blue1">
          <table height="505" border="0" align="left" cellpadding="5" cellspacing="0">
        <tr>
          <td>

          



<table width="513" border="0" cellspacing="0" cellpadding="5">
<tbody><tr>
<td colspan="3"><span class="Text-blue-cn">Unsubscribe</span></td>
</tr>
<tr>
<td width="178" class="Text-greyb4">Your email address</td>
<td width="5" class="Text-greyb4">:</td>
<td width="300" rowspan="2">





<form method="post" action="">
<table border="0" cellspacing="0" cellpadding="5">
<tbody><tr>
<td><input name="email" size="45" value="<?php echo $this->email; ?>" /></td>
</tr>
<tr>
<td align="right">
<button type="submit" name="unsubscribe" value="true" class="warn">Unsubscribe</button>
</td>
</tr>
<tr>
<td align="right"><a href="../../../../privacy-policy.html" target="_blank" class="blue-text">Please read our Privacy Policy</a></td>
</tr>
</tbody></table>
<input type="hidden" name="code" value="<?php echo $this->code; ?>" />
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan="3" align="left" class="Text-grey4">&nbsp;</td>
</tr>
</tbody>
</table>

</form>

          </td>
        </tr>

    </table></td>
        </tr>
    </table></td>
  </tr>
</table>
<div style="visibility:hidden">
<!--WEBBOT bot="HTMLMarkup" startspan ALT="Site Meter" -->
<a href="http://s14.sitemeter.com/stats.asp?site=s14ayamaustralia"
target="_top">
<img src="http://s14.sitemeter.com/meter.asp?site=s14ayamaustralia"
lt="Site Meter" border=0></a>
<!--WEBBOT bot="HTMLMarkup" Endspan -->
</div>
</body>
</html>



<?php
}

?>

<script type="text/javascript">
$().ready(function() {
	$('.warn').click(function() {
		var str = this.innerHTML;
		return confirm("<?php echo _('Really unsubscribe?'); ?>");
	});
});
</script>

<?php

// include $this->template_dir.'/inc/user.footer.php';


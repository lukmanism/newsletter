<?php 
include('taf/inc2/lang/en.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>AYAM&#8482; e-newsletter</title>



<link href="http://ayambrand-recipes.com/video-eng-recipes/CSS/AB-style.css" rel="stylesheet" type="text/css" />
<link href="taf/custom.css" rel="stylesheet" type="text/css" />
<script language="javascript">
function strTrim(str) {
var elem = document.getElementById(str).value;
document.getElementById(str).value = elem.replace(/^\s+|\s+$/g, '');
}

function strProperCase(str) {
var elem = document.getElementById(str).value;
document.getElementById(str).value = elem.toLowerCase().replace(/^(.)|\s(.)/g,
function($1) { return $1.toUpperCase(); });
}
</script>
</head>

<body>
<table id="maintable">
  
  <tr>
    <td width="982" height="601" valign="top" background="taf/images/Hi-grey-email.jpg">
    <table width="729" height="459" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
          <td height="111" colspan="2" align="center">&nbsp;</td>
          <td class="Text-blue1">&nbsp;</td>
        </tr>
        <tr>
          <td height="84" colspan="2" align="center"><img src="taf/images/logo.gif" width="200" height="84" /></td>
          <td width="505" class="Text-blue1">
          <?php 
		  if($_GET['action'] != "success"){
		  	echo _SUBSCRIBE_TITLE2;
		  }else{
			echo _SUBSCRIBE_TITLE1;
		  }
		  ?>  </td>
        </tr>
        
        <tr>
          <td colspan="2" align="center" valign="top"><table width="100" border="1" cellpadding="2" cellspacing="2" bordercolor="#FFFFFF">
            <tr>
              <td bordercolor="#999999"><img src="taf/images/recipe-1.jpg" width="85" height="85" /></td>
              <td bordercolor="#999999"><img src="taf/images/recipe-2.jpg" width="85" height="85" /></td>
            </tr>
            <tr>
              <td height="84" bordercolor="#999999"><img src="taf/images/recipe-3.jpg" width="85" height="85" /></td>
              <td height="84" bordercolor="#999999"><img src="taf/images/recipe-4.jpg" width="85" height="85" /></td>
            </tr>

          </table></td>
          <td valign="top" class="Text-blue1">

<?php //include("admin/linkstore.php"); ?>          
<?php 
switch($_GET['action']){
	case "success":
	echo "As a member of the AYAM&trade; Club, you are entitled to receive a great monthly newsletter which will introduce new AYAM&trade; products, cooking tips, authentic Asian recipes, the chance to win prizes and much more!";
	echo '<p><a href="http://www.ayam.com/newsletter/taf/index.php?action=newsletter&link
='.$linkurl.'">Click here</a> to share Club AYAM&trade; with your friends.</p>';
	break;
	
	case "campaign":
	include('taf/campaign/index.php');
	break;
	
	default:
	include('subscribe_form.php');
	break;	
}
?></td>
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

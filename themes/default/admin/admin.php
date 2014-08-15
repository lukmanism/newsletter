<?php 

include($this->template_dir.'/inc/admin.header.php');

?>

<h2><?php echo _('Admin Menu')?></h2>

<div id="language" class="right">
<form method="POST" action="" id="language">
<select name="lang" onChange="this.form.submit();">
<option value="en">English (en)</option>
<option value="en-uk" {if $lang == 'en-uk'}SELECTED{/if}>british english (en-uk)</option>
<option value="bg" {if $lang == 'bg'}SELECTED{/if}>български (bg)</option>
<option value="da" {if $lang == 'da'}SELECTED{/if}>dansk (da)</option>
<option value="de" {if $lang == 'de'}SELECTED{/if}>deutsch (de)</option>
<option value="es" {if $lang == 'es'}SELECTED{/if}>español (es)</option>
<option value="fr" {if $lang == 'fr'}SELECTED{/if}>français (fr)</option>
<option value="it" {if $lang == 'it'}SELECTED{/if}>italiano (it)</option>
<option value="nl" {if $lang == 'nl'}SELECTED{/if}>nederlands (nl)</option>
<option value="pl" {if $lang == 'pl'}SELECTED{/if}>polski (pl)</option>
<option value="pt" {if $lang == 'pt'}SELECTED{/if}>português (pt)</option>
<option value="pt-br" {if $lang == 'pt-br'}SELECTED{/if}>brasil português (pt-br)</option>
<option value="ro" {if $lang == 'ro'}SELECTED{/if}>română (ro)</option>
<option value="ru" {if $lang == 'ru'}SELECTED{/if}>русский язык (ru)</option>
</select>
</form>
</div>

<?php include($this->template_dir.'/inc/messages.php');?>

<div id="boxMenu">

<div><a href="<?php echo($this->url_base) ?>admin_mailings.php">
        <img src="<?php echo($this->url['theme']['shared']); ?>images/icons/mailing.png" alt="envelope icon" class="navimage" />
        <?php echo _('Mailings') ?>
    </a> - <?php echo _('Send mailings to the entire list or to a subset of subscribers. Mailing status and history can also be viewed from here.') ?>
</div>

<div><a href="<?php echo($this->url_base) ?>admin_subscribers.php">
        <img src="<?php echo($this->url['theme']['shared']); ?>images/icons/subscribers.png" alt="people icon" class="navimage" /> 
        <?php echo _('Subscribers') ?>
    </a> - <?php echo _('Here you can list, add, delete, import, export, and update your subscribers. You can also create groups (subsets) of your subsribers from here.') ?>
</div>

<div><a href="<?php echo($this->url_base) ?>admin_setup.php">
        <img src="<?php echo($this->url['theme']['shared']); ?>images/icons/settings.png" alt="hammer and screw icon" class="navimage" />
        <?php echo _('Setup') ?>
    </a> - <?php echo _('This area allows you to configure poMMo. Set mailing list parameters, choose the information you\'d like to collect from subscribers, and generate subscription forms from here.') ?>
</div>

<br />

</div>
<?php include($this->template_dir.'/inc/admin.footer.php');

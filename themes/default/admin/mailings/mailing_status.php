<?php
ob_start();
?>
<link type="text/css" rel="stylesheet" href="<?php echo $this->url['theme']['shared'];
		?>css/default.mailings.css" />
<?php

$this->capturedHead = ob_get_clean();

include $this->template_dir.'/inc/admin.header.php';

?>

<p>
	<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/alert.png"
			class="navimage right" alt="thunderbolt icon" />
	<?php echo sprintf(_('Mailings take place in the background so feel free to close'
			.' this page, visit other sites, or even turn off your computer. You'
			.' can always return to this status page by visiting the Mailings'
			.' section.  %sThrottle settings%s can also be adjusted -- although you'
			.' must pause and revive the mailing before changes take effect.'),
			'<a href="'.$this->url['base'].'setup_configure.php#mailings">',
			'</a>'); ?>
</p>

<div>
<?php
	echo sprintf(_('Sending message to %s subscribers.'), $this->mailing['tally']);
?>
</div>

<!-- Updates via AJAX: Processing Mailing, Mailing Finished, Mailing Frozen -->
<div class="warn">
	<?php echo _('Mailing Status'); ?> &raquo; <span id="status"></span>
</div>

<!-- Updates via AJAX: Pause/Resume (started), Resume/Cancel (stopped), DeThaw/Cancel (frozen) -->
<div id="commands">

	<div class="error uniq" id="init"><?php echo _('Initializing...'); ?></div>

	<div class="hidden uniq" id="started">
		<div class="first"><a class="cmd" href="#stop"><img src="<?php echo $this->url['theme']['shared']; ?>images/icons/pause-small.png" alt=" icon" /><?php echo _('Pause Mailing'); ?></a></div>
		<div class="second"><a class="cmd" href="#restart"><?php echo _('Resume Mailing'); ?> <img src="<?php echo $this->url['theme']['shared']; ?>images/icons/restart-small.png" alt="icon" /></a></div>
	</div>

	<div class="hidden uniq" id="stopped">
		<div class="first"><a class="cmd" href="#restart"><img src="<?php echo $this->url['theme']['shared']; ?>images/icons/restart-small.png" alt="icon" /> <?php echo _('Resume Mailing'); ?></a></div>
		<div class="second"><a class="cmd" href="#cancel"><?php echo _('Cancel Mailing'); ?>	<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/stopped-small.png" alt="icon" /></a></div>
	</div>

	<div class="hidden uniq" id="frozen">
		<div class="first"><a class="cmd" href="#restart"><img src="<?php echo $this->url['theme']['shared']; ?>images/icons/restart-small.png" alt="icon" /><?php echo _('Resume Mailing'); ?></a></div>
		<div class="second"><a class="cmd" href="#cancel"><?php echo _('Cancel Mailing'); ?>	<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/stopped-small.png" alt="icon" /></a></div>
	</div>

	<!-- Hidden until mailing is finished -->
	<div id="finished" class="hidden error uniq">
		<?php echo _('Mailing Finished'); ?> -- <a href="admin_mailings.php"><?php echo _('Return to'); ?> <?php echo _('Mailings Page'); ?></a>
	</div>

	<!-- Displayed when a command is clicked -->
	<div id="pause" class="hidden error uniq">
		<?php echo _('Command Recieved. Please wait...'); ?>
	</div>

</div>

<div id="barHead">
	<?php
		echo sprintf(_('%s mails sent'), '<span id="sent">0</span>');
	?>

	<img class="anim go" src="<?php echo $this->url['theme']['shared'];
			?>images/loader.gif" alt="Processing" />
	<img class="anim hidden stop" src="<?php echo $this->url['theme']['shared'];
			?>images/icons/stopped-small.png" alt="Stopped" />
</div>

<div id="barBox">
	<div id="barTrack">
		<div id="bar"></div>
	</div>
</div>

<div id="barFoot"></div>

<form>
	<fieldset>
		<legend><?php echo _('Last 50 notices'); ?></legend>

		<ul class="inpage_menu">
			<li><a href="ajax/status_download.php?type=sent"><?php echo _('View'); ?>
					<?php echo _('Sent Emails'); ?></a></li>
			<li><a href="ajax/status_download.php?type=unsent"><?php echo _('View'); ?>
					<?php echo _('Unsent Emails'); ?></a></li>
			<li><a href="ajax/status_download.php?type=error"><?php echo _('View'); ?>
					<?php echo _('Failed Emails'); ?></a></li>
		</ul>

		<div id="notices"></div>
	</fieldset>
</form>

<script type="text/javascript">

var pommo = {
	status: null,
	poll: function(get){get = get || '';  $.getJSON("ajax/status_poll.php?id=<?php echo $this->mailing['id']; ?>&"+get,pommo.process)},
	process: function(mailing) {
		$('#status').html(mailing.statusText);

		// status >> 1: Processing  2: Stopped  3: Frozen  4: Finished    5: command Sent
		$('#barHead img.go').css({display:((mailing.status == 1)?'inline':'none')});
		$('#barHead img.stop').css({display:((mailing.status == 1)?'none':'inline')});

		$('#sent').html(mailing.sent);
		$('#barFoot').html(mailing.percent+'%');
		$('#bar').width(mailing.percent+'%');

		if (mailing.status != pommo.status) {
			pommo.status = mailing.status;
			var id = null;
			switch(mailing.status) {
				case 1: id = 'started'; break;
				case 2: id = 'stopped'; break;
				case 3: id = 'frozen'; break;
				case 4: id = 'finished'; break;
			}
			$('#'+id).show().siblings('div.uniq').hide();
		}

		if (typeof(mailing.notices) == 'object')
			for (i in mailing.notices)
				if (mailing.notices[i] != '')
					$('#notices').prepend('<li>'+mailing.notices[i]+'</li>');

		// TODO --> make a nice XPATH selector out of this...
		if ($('#notices li').size() > 50) {
			$('#notices li').each(function(i){ if (i > 40) $(this).remove(); });
		}

	}
};


$().ready(function(){
	// assign command events
	$('#commands a.cmd').click(function() {
		if(pommo.status != 5) {
			pommo.status = 5;
			$('#pause').show().siblings('div.uniq').hide();
			var cmd = $(this).attr('href').replace(/.*\#/,'');
			$.getJSON(
				'ajax/status_cmd.php?cmd='+cmd,
				function(ret) { setTimeout('pommo.poll()',1500); }
			);
		}
		return false;
	});

	// init
	pommo.poll('resetNotices=true');

    // continually ("hearbeat") poll the mailing
    $(document).ajaxStop(function() {
        if (1 == pommo.status) {
            setTimeout('pommo.poll()', 4500);
        }
    });
});

</script>

<?php

include $this->template_dir.'/inc/admin.footer.php';


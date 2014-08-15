<h4><?php echo _('Preview Mailing'); ?></h4>

<form id="sendForm" class="json" action="<?php echo $_SERVER['PHP_SELF']; ?>"
		method="post">
	<div class="output">
		<?php include $this->template_dir.'/inc/messages.php'; ?>
	</div>
	<input type="hidden" name="sendaway" value="true">
</form>

<div class="msgpreview">
	<p>
		<strong><?php echo _('Subject:'); ?></strong>
		<tt><?php echo $this->subject; ?></tt>
	</p>

	<p>
		<strong><?php echo _('To:'); ?></strong>
		<span style="color: #EA2B2B; font-size: 120%;"><?php
				echo $this->group; ?></span>
		(<em><?php echo $this->tally; ?></em>
		<?php echo _('recipients'); ?>)
	</p>

	<p>
		<strong><?php echo _('From:'); ?></strong>
		<?php echo $this->fromname; ?>
		<tt>&lt;<?php echo $this->fromemail; ?>&gt;</tt>
	</p>

	<?php
		if ($this->fromemail != $this->frombounce)
		{
		?>
		<p>
			<strong><?php echo _('Bounces:'); ?></strong>
			<tt>&lt;<?php echo $this->frombounce; ?>&gt;</tt>
		</p>
		<?php
		}
	?>

	<p>
		<strong><?php echo _('Character Set:'); ?></strong>
		<tt><?php echo $this->list_charset; ?></tt>
	</p>
</div>


<ul class="inpage_menu">
	<li>
		<a href="ajax/ajax.mailingtest.php" id="e_test">
			<img src="<?php echo $this->url['theme']['shared'];
					?>images/icons/world_test.png" alt="icon" border="0"
					align="absmiddle" />
			<?php echo _('Send Test'); ?>
		</a>
	</li>
	<li>
		<a href="#" id="e_send">
			<img src="<?php echo $this->url['theme']['shared'];
					?>images/icons/world.png" alt="icon" border="0"
					align="absmiddle" />
			<?php echo _('Send Mailing'); ?>
		</a>
	</li>
</ul>

<h4><?php echo _('Message'); ?></h4>

<div class="msgpreview">
	<?php
		if ('on' == $this->ishtml)
		{
		?>
			<strong><?php echo _('HTML Message'); ?></strong> :
			<a href="ajax/mailing_preview.php"
					title="<?php echo _('Preview Message'); ?>"
					onclick="return !window.open(this.href)">
				<?php echo _('Click Here'); ?>
			</a>
			<hr />
		<?php
		}
	?>
	<strong><?php echo _('Text Version'); ?></strong>: <br /><br />
    <div>
    <?php
        // Replace line endings with <br> so preview looks good
        echo str_replace(
            array(
                "\r\n",
                "\n"
            ),
            '<br>',
            $this->altbody
        );
    ?>
    </div>
</div>

<script type="text/javascript">
$().ready(function() {

	$('#e_test').click(function() {
		$('#dialog').jqmShow(this);
		return false;
	});

	$('#e_send').click(function() {
		$('#sendForm').submit();
		return false;
	});
});
</script>

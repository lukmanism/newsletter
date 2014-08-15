<div class="helpToggle">
	<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/help.png"
			alt="help icon" style="float: left; margin: 0 10px; 0 5px;" />

	<p>
		<?php echo sprintf(_('Mailings may be personalized per subscriber. You can inject'
			.' %ssubscriber field%s values, clickable links, and tracking information.'),
			'<a href="'.$this->url['base'].'setup_fields.php">', '</a>'); ?>
	</p>

	<p>
	<?php
		echo _('Personalizations allow you to write a message like "Dear [[firstName|Loyal Subscriber]], Happy New Year! .... follow this link to unsubscribe or update your records; [[!unsubscribe]]".');
	?>
	</p>
</div>

<hr />

<div id="personal">
	<div style="float: left;" class="alert">
		<input type="radio" name="type" value="field" checked="yes"/><?php
				echo _('Personalization'); ?><br />
		<input type="radio" name="type" value="link" /><?php echo _('Link (URL)');
				?> <br />
		<input type="radio" name="type" value="track" /><?php echo _('Tracking (ID)');
				?><br />
	</div>

	<div style="float: left; margin-left: 25px;">
		<div class="pType" name="field">
			<select>
				<option value="email"><?php echo _('Email'); ?></option>
				<option value="ip"><?php echo _('IP Address'); ?></option>
				<option value="registered"><?php echo _('Registered'); ?></option>
				<?php
					foreach ($this->fields as $id => $field)
					{
						echo '<option value="'.$field['name'].'">&nbsp;&nbsp;'
								.$field['name'].'</option>';
					}
				?>
			</select>
			<p>
				<label for="default"><?php echo _('Default (optional; used if no'
					.' value exists)'); ?>:</label><br />
				<input type="text" name="default" style="width: 200px;" />
			</p>
		</div>

		<div class="pType hidden" name="link">
			<select>
			<option value="!unsubscribe"><?php echo _('Unsubscribe or Update Records');
					?></option>
			<option value="!weblink"><?php echo _('View on Web (public mailings must'
				.' be enabled)'); ?></option>
			</select>
		</div>

		<div class="pType hidden" name="track">
			<select >
			<option value="!subscriber_id"><?php echo _('Subscriber ID'); ?></option>
			<option value="!mailing_id"><?php echo _('Mailing ID'); ?></option>
			</select>
		</div>

		<div class="buttons">
			<button name="submit"><?php echo _('Insert'); ?></button>
			<button class="jqmClose"><?php echo _('Cancel'); ?></button>
		</div>
	</div>

</div>

<script type="text/javascript">
$().ready(function(){
	$('#personal input[type=radio]').change(function(){
		$('#personal div.pType').hide();
		$('#personal div.pType[name='+this.value+']').show();
	});

	$('#personal button[name=submit]').click(function(){

		// construct the value
		var vals = $('#personal div.pType:visible :input');
		var out = (vals.size()>1 && $(vals[1]).val() != '') ?
			'[['+$(vals[0]).val()+'|'+$(vals[1]).val()+']]' :
			'[['+$(vals[0]).val()+']]';

		// inject personalization into WYSIWYG
		wysiwyg.inject(out);

		// close the dialog
		$('#dialog').jqmHide();

	});
});
</script>


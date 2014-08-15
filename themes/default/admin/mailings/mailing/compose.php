<div class="output">
<?php
	include $this->template_dir.'/inc/messages.php';
?>
</div>

<form id="compose" class="json mandatory" action="<?php echo $_SERVER['PHP_SELF']; ?>"
		method="post">
	<input type="hidden" name="compose" value="true" />

	<div class="compose">
		<h4><?php echo _('HTML Message'); ?></h4>
			<ul class="inpage_menu">
				<li>
					<a href="#" class="e_altbody">
						<img src="<?php echo $this->url['theme']['shared'];
								?>images/icons/reload.png" alt="icon"
								border="0" align="absmiddle" />
						<?php echo _('Copy text from HTML Message'); ?>
					</a>
				</li>
				<li>
					<input type="submit" id="submit" name="submit"
							value="<?php echo _('Continue'); ?>" />
				</li>
			</ul>

		<textarea name="body" id="ck_mailing"><?php echo $this->body; ?></textarea>
		<span class="notes">(<?php echo _('Leave blank to send text only'); ?>)</span>
	</div>

    <?php
        // Add hidden inputs for each attachment already saved
        $attachments = array();
        if (!empty($this->attachments)) {
            $attachments = explode(',', $this->attachments);
        }
        foreach ($attachments as $attachment) {
        ?>
            <input type="hidden" name="attachment[]" class="attached_file"
                    value="<?php echo $attachment; ?>">
        <?php
        }
    ?>
	<div id="file-uploader-demo1" style='float: left; margin-left: 30px'>
		<noscript>
			<p><?php echo _('Please enable JavaScript to use file uploader.'); ?></p>
			<!-- or put a simple form for upload here -->
		</noscript>
	</div>

	<ul class="inpage_menu" style='float: left'>
		<li>
			<a href="#" id="e_toggle">
				<img src="<?php echo $this->url['theme']['shared'];
						?>images/icons/viewhtml.png" alt="icon"
						border="0" align="absmiddle" /><span id="toggleText">
				<?php echo _('Enable WYSIWYG'); ?></span>
			</a>
		</li>
		<li>
			<a href="ajax/ajax.personalize.php" id="e_personalize">
				<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/subscribers_tiny.png"
						alt="icon" border="0" align="absmiddle" />
				<?php echo _('Add Personalization'); ?>
			</a>
		</li>
		<li>
			<a href="ajax/ajax.addtemplate.php" id="e_template">
				<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/edit.png" alt="icon"
						border="0" align="absmiddle" /> <?php echo _('Save as Template'); ?>
			</a>
		</li>
	</ul>

	<div style='clear:both'></div>

	<div class="compose">
		<h4><?php echo _('Text Version'); ?></h4>
		<textarea name="altbody"><?php echo $this->altbody; ?></textarea>
		<span class="notes">(<?php echo _('Leave blank to send HTML only'); ?>)</span>
	</div>

	<ul class="inpage_menu">
		<li>
			<a href="#" class="e_altbody">
				<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/reload.png" alt="icon"
						border="0" align="absmiddle" />
				<?php echo _('Copy text from HTML Message'); ?>
			</a>
		</li>
		<li>
			<input type="submit" id="submit" name="submit"
					value="<?php echo _('Continue'); ?>" />
		</li>
	</ul>
</form>

<script type='text/javascript'>
	function createUploader()
	{
		var uploader = new qq.FileUploader(
	    {
			element: document.getElementById('file-uploader-demo1'),
	        action: 'ajax/process_upload.php',
	        template: '<div class="qq-uploader">' +
                '<div class="qq-upload-drop-area"><span><?php echo _("Drop files here to upload"); ?></span></div>' +
                '<div class="qq-upload-button"><?php echo _("Add attachment"); ?></div>' +
                '<ul class="qq-upload-list"></ul>' +
            '</div>',
      		onComplete: function(id, fileName, responseJSON)
            {
				attachment_id = responseJSON.attachment_id;
				$('.qq-upload-list').append('<input type="hidden" ' +
						'name="attachment[]"' + ' class="attached_file" ' +
						'value="' + attachment_id + '">');
            }
	    });
	}

	createUploader();
</script>

<script type="text/javascript">
var onText = '<?php echo _('Disable WYSIWYG'); ?>';
var offText = '<?php echo _('Enable WYSIWYG'); ?>';

$().ready(function()
{
	wysiwyg.init({
		language: '<?php echo $this->lang; ?>',
		baseURL: '<?php echo $this->url['theme']['shared']; ?>../wysiwyg/',
		t_weblink: '<?php echo _('View this Mailing on the Web'); ?>',
		t_unsubscribe: '<?php echo _('Unsubscribe or Update Records'); ?>',
		textarea: $('textarea[name=body]')
	});

	<?php
		if ('on' == $this->wysiwyg)
		{
		?>
			// Enable the WYSIWYG
			wysiwyg.enable();
			$('#toggleText').html(onText);
		<?php
		}
	?>

	// Command Buttons (toggle HTML, add personalization, save template, generate altbody)
	$('#e_toggle').bind('click', function() {
		if(wysiwyg.enabled) {
			if(wysiwyg.disable()) {
				$('#toggleText').html(offText)
				$.getJSON('ajax/ajax.rpc.php?call=wysiwyg&disable=true');
			}
		}
		else {
			if(wysiwyg.enable()) {
				$('#toggleText').html(onText);
				$.getJSON('ajax/ajax.rpc.php?call=wysiwyg&enable=true');
			}
		}
		return false;
	});

	$('#e_personalize').click(function() {
		$('#dialog').jqmShow(this);
		return false;
	});

	$('#e_template').click(function() {

		// submit the bodies
		var post = {
			body: wysiwyg.getBody(),
			altbody: $('textarea[name=altbody]').val()
		},trigger = this;

		poMMo.pause();

		$.post('ajax/ajax.rpc.php?call=savebody',post,function(){
			$('#dialog').jqmShow(trigger);
			poMMo.resume();
		});

		return false;
	});


	$('.e_altbody').click(function() {

		var post = {
			body: wysiwyg.getBody()
		};

		poMMo.pause();

		$.post('ajax/ajax.rpc.php?call=altbody',post,function(json){
			$('textarea[name=altbody]').val(json.altbody);
			poMMo.resume();
		},"json");

		return false;
	});


	$('#compose').submit(function()
	{
		// submit the bodies and attachments
		attachments = {};
		i = 0;
		$('.attached_file').each(function() {
			theName = 'attachment[' + i + ']';
			attachments[theName] = $(this).val();
			i++;
		});

		var post = $.extend
		(
			{
				body: wysiwyg.getBody(),
				altbody: $('textarea[name=altbody]').val()
			},
			attachments
		);

		poMMo.pause();

		$.post('ajax/ajax.rpc.php?call=savebody',post,function(){
			poMMo.resume();
		});
	});

});

</script>

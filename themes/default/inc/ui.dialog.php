<script type="text/javascript" src="<?php echo $this->url['theme']['shared'];
		?>js/jq/jqModal.js"></script>
<link href="<?php echo $this->url['theme']['shared']; ?>css/ui.dialog.css"
		type="text/css" rel="stylesheet"/>

<script type="text/javascript">

PommoDialog = {
	init: function(dialogs,params,overloadParams) {
		dialogs = dialogs || 'div.jqmDialog[id!=wait]';
		params = params || {};
		if(!overloadParams)
			params = $.extend(this.params,params);

		$(dialogs).jqm(this.params);
	},
	params: {
		modal: false,
		ajax: '@href',
		target: '.jqmdMSG',
		trigger: false,
		onLoad: function(hash){
			// Automatically prepare forms in ajax loaded content
			if(poMMo.form && $.isFunction(poMMo.form.assign))
				poMMo.form.assign(hash.w);
		}
	}
};

$().ready(function() {
	// Close Button Highlighting. IE doesn't support :hover. Surprise?
	$('input.jqmdX')
	.hover(
		function(){ $(this).addClass('jqmdXFocus'); },
		function(){ $(this).removeClass('jqmdXFocus'); })
	.focus(
        function(){ this.hideFocus=true; $(this).addClass('jqmdXFocus'); })
	.blur(
        function(){ $(this).removeClass('jqmdXFocus'); });

	// Initialize default wait dialog
	$('#wait').jqm({modal: true});

});
</script>

<?php
ob_start();
?>
<div class="imgCache">
	<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif" />
	<img src="<?php echo $this->url['theme']['shared']; ?>images/dialog/close.gif" />
	<img src="<?php echo $this->url['theme']['shared']; ?>images/dialog/close_hover.gif" />
	<img src="<?php echo $this->url['theme']['shared']; ?>images/dialog/sprite.gif" />
	<img src="<?php echo $this->url['theme']['shared']; ?>images/dialog/bl.gif" />
	<img src="<?php echo $this->url['theme']['shared']; ?>images/dialog/br.gif" />
	<img src="<?php echo $this->url['theme']['shared']; ?>images/dialog/bc.gif" />
</div>
<?php

$this->dialogId = 'wait';
$this->dialogWait = true;
include $this->template_dir.'/inc/dialog.php';

$this->capturedFooter = ob_get_clean();

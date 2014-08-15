<form class="ajax" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

	<fieldset>
		<legend><?php echo _('Global Rates'); ?></legend>

		<div>
			<h2><?php echo _('Mail Rate'); ?></h2>
			<div id="mps" class="ui-slider">
				<div class="ui-slider-handle"></div>
			</div>
			<p>
			<?php
				echo sprintf(_('%s per Second'), '<span></span>').'<br>';
				echo sprintf(_('%s per Hour'), '<span></span>');
			?>
			</p>
		</div>

		<div>
			<h2><?php echo _('Bandwidth Limit'); ?></h2>
			<div id="bps" class="ui-slider">
				<div class="ui-slider-handle"></div>
			</div>
			<p>
			<?php
				echo sprintf(_('%s per Second'), '<span></span>').'<br>';
				echo sprintf(_('%s per Hour'), '<span></span>');
			?>
			</p>
		</div>
	</fieldset>

	<div class="output alert">
	<?php
		if ($this->output)
		{
			echo $this->output;
		}
	?>
	</div>

	<input type="submit" value="<?php echo _('Update'); ?>" />
	<input type="submit" name="throttle_restore"
			value="<?php echo _('Restore Defaults'); ?>" />
	<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
			alt="loading..." class="hidden" name="loading" />

	<input type="hidden" name="throttle-submit" value="true" />

	<div id="inputs" class="hidden">
		<input type="hidden" name="mps" value="true" />
		<input type="hidden" name="bps" value="true" />
		<input type="hidden" name="dp" value="true" />
		<input type="hidden" name="dmpp" value="true" />
		<input type="hidden" name="dbpp" value="true" />
	</div>

	<fieldset>
		<legend><?php echo _('Rates per Domain'); ?></legend>

		<?php
			echo _('You may also limit the amount of mail a single domain receives'
			.' in a period. This is useful for larger mailings, and prevents'
			.' the "slamming" of the domain (which can get your mails'
			.' rejected). As example, you can choose to send no more than 1'
			.' mail every 20 seconds to a domain by setting the mails to 1'
			.' and the period interval to 20. Warning; this setting will'
			.' significantly delay a mailing if many of your subscribers'
			.' use the same domain (e.g. @yahoo.com).'); ?>

		<br />

		<div>
			<h2><?php echo _('Period Interval'); ?></h2>
			<div id="dp" class="ui-slider ui-slider-alt">
				<div class="ui-slider-handle"></div>
			</div>
			<p>
			<?php
				echo sprintf(_('%s seconds'), '<span></span>');
			?>
			</p>
		</div>

		<div>
			<h2><?php echo _('Mail Rate'); ?></h2>
			<div id="dmpp" class="ui-slider ui-slider-alt">
				<div class="ui-slider-handle"></div>
			</div>
			<p>
			<?php
				echo sprintf(_('%s per Period'), '<span></span>');
			?>
			</p>
		</div>

		<div>
			<h2><?php echo _('Bandwidth Limit'); ?></h2>
			<div id="dbpp" class="ui-slider ui-slider-alt">
				<div class="ui-slider-handle"></div>
			</div>
			<p>
			<?php
				echo sprintf(_('%s per Period'), '<span></span>');
			?>
			</p>
		</div>
	</fieldset>

	<div class="output alert">
	<?php
		if ($this->output)
		{
			echo $this->output;
		}
	?>
	</div>
	<input type="submit" value="<?php echo _('Update'); ?>" />
	<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
			alt="loading..." class="hidden" name="loading" />

</form>

<script type="text/javascript">

var maxStr='<?php echo _('No Limit'); ?>';

PommoSlider.onSlide = function(slider, v) {
    var $slider = $(slider);
    var out = $slider.parent().find('span');
	switch($slider.attr('id')) {
		case 'mps':
			out[0].innerHTML=(v > 0) ? Math.round(v/60*10000)/10000 : maxStr;
			out[1].innerHTML=(v > 0) ? (v/60)*60*60 : maxStr;
			break;
		case 'bps':
			out[0].innerHTML=(v > 0) ? v : maxStr;
			out[1].innerHTML=(v > 0) ? Math.round(v*60*60/1024) : maxStr;
			break;
		case 'dp':
		case 'dmpp':
		case 'dbpp':
			out[0].innerHTML=(v > 0) ? v : maxStr;
			break;
	};
	var val = (out[0].innerHTML == maxStr) ? 0 : out[0].innerHTML;
	$('#inputs input[name='+slider.id+']').val(val);
};


$('div.ui-slider').each(function(){
	var p = {};
	switch(this.id) {
		case 'mps':
			p.maxValue = 300;
			p.minValue = 0;
			p.startValue = <?php echo $this->mps; ?>;
			break;
		case 'bps':
			p.maxValue = 400;
			p.minValue = 0;
			p.startValue = <?php echo $this->bps; ?>;
			break;
		case 'dp':
			p.maxValue = 20;
			p.minValue = 5;
			p.startValue = <?php echo $this->dp; ?>;
			break;
		case 'dmpp':
			p.maxValue = 5;
			p.minValue = 0;
			p.startValue = <?php echo $this->dmpp; ?>;
			break;
		case 'dbpp':
			p.maxValue = 400;
			p.minValue = 0;
			p.startValue = <?php echo $this->dbpp; ?>;
			break;

	}
	PommoSlider.onSlide(this,p.startValue);
	PommoSlider.init($(this),p);
});
</script>


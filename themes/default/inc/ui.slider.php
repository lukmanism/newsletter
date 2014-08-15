<script type="text/javascript" src="<?php echo($this->url['theme']['shared']); ?>js/jq/jquery-ui.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo $this->url['theme']['shared'] ?>css/ui.slider.css" />

<script type="text/javascript">
var PommoSlider = {
	serial: 0,
	hash: [],
	defaults: {
		minValue: 0,
		maxValue: 100,
		startValue: 50,
        slide: function(e, ui) {
            PommoSlider.onSlide(e.target, ui.value)
        }
	},
	init: function(e,p) {
		var p = $.extend(PommoSlider.defaults,p);
		return $(e).each(function(){
			var s = this.pommoSlider || PommoSlider.serial++;

			this.pommoSlider = s;

			PommoSlider.hash[s] = {
				params: p,
				value: null
			};
            var a = {
                range: 'max',
                min: p.minValue,
                max: p.maxValue,
                value: p.startValue,
                slide: p.slide
            };
			$(this).slider(a);
		});
	},
	onSlide: function(slider,value) {
		alert('no onSlide event assigned');
	}
};
</script>

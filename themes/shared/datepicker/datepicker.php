<link rel="stylesheet" type="text/css" href="<?php echo $this->url['theme']['shared']; ?>datepicker/datepicker.css"/>
<script type="text/javascript" src="<?php echo $this->url['theme']['shared']; ?>js/jq/dom_creator.js"></script>
<script type="text/javascript">
/*
 * Date picker plugin for jQuery
 * http://kelvinluck.com/assets/jquery/datePicker
 *
 * Copyright (c) 2006 Kelvin Luck (kelvnluck.com)
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * $LastChangedDate: 2006-11-14 00:10:48 +0000 (Tue, 14 Nov 2006) $
 * $Rev: 24 $
 *
 * Modified for on the fly translation. -- Selection @ Bottom.
 */
$.datePicker = function()
{
	// so that firebug console.log statements don't break IE
	if (window.console == undefined) { window.console = {log:function(){}}; }

	var months = ['<?php echo htmlentities(_('January')); ?>', '<?php echo htmlentities(_('Febuary')); ?>', '<?php echo htmlentities(_('March')); ?>', '<?php echo htmlentities(_('April')); ?>', '<?php echo htmlentities(_('May')); ?>', '<?php echo htmlentities(_('June')); ?>', '<?php echo htmlentities(_('July')); ?>', '<?php echo htmlentities(_('August')); ?>', '<?php echo htmlentities(_('September')); ?>', '<?php echo htmlentities(_('October')); ?>', '<?php echo htmlentities(_('November')); ?>', '<?php echo htmlentities(_('December')); ?>'];
	var days = ['<?php echo htmlentities(_('Sunday')); ?>', '<?php echo htmlentities(_('Monday')); ?>', '<?php echo htmlentities(_('Tuesday')); ?>', '<?php echo htmlentities(_('Wednesday')); ?>', '<?php echo htmlentities(_('Thursday')); ?>', '<?php echo htmlentities(_('Friday')); ?>', '<?php echo htmlentities(_('Saturday')); ?>'];
	var navLinks = {p:'<?php echo htmlentities(_('Prev')); ?>', n:'<?php echo htmlentities(_('Next')); ?>', c:'<?php echo htmlentities(_('Close')); ?>'};
	var dateFormat = 'yyyy/mm/dd';
	var _firstDate;
	var _lastDate;

	var _selectedDate;
	var _openCal;

	var _zeroPad = function(num) {
		var s = '0'+num;
		return s.substring(s.length-2)
		//return ('0'+num).substring(-2); // doesn't work on IE :(
	};
	var _strToDate = function(dIn)
	{
		switch (dateFormat.toLowerCase()) {
			case 'yyyy/mm/dd':
				dParts = dIn.split('-');
				return new Date(dParts[0], Number(dParts[1])-1, dParts[2]);
			case 'dd/mm/yyyy':
				dParts = dIn.split('/');
				return new Date(dParts[2], Number(dParts[1])-1, Number(dParts[0]));
			case 'mm/dd/yyyy':
			default:
				var parts = parts ? parts : [2, 1, 0];
				dParts = dIn.split('/');
				return new Date(dParts[2], Number(dParts[0])-1, Number(dParts[1]));
		}
	};
	var _dateToStr = function(d)
	{
		var dY = d.getFullYear();
		var dM = _zeroPad(d.getMonth()+1);
		var dD = _zeroPad(d.getDate());
		switch (dateFormat.toLowerCase()) {
			case 'yyyy/mm/dd':
				return dY + '/' + dM + '/' + dD;
			case 'dd/mm/yyyy':
				return dD + '/' + dM + '/' + dY;
			case 'mm/dd/yyyy':
			default:
				return dM + '/' + dD + '/' + dY;
		}
	};

	var _getCalendarDiv = function(dIn)
	{
		var today = new Date();
		if (dIn == undefined) {
			// start from this month.
			d = new Date(today.getFullYear(), today.getMonth(), 1);
		} else {
			// start from the passed in date
			d = dIn;
			d.setDate(1);
		}
		// check that date is within allowed limits:
		if ((d.getMonth() < _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear()) || d.getFullYear() < _firstDate.getFullYear()) {
			d = new Date(_firstDate.getFullYear(), _firstDate.getMonth(), 1);;
		} else if ((d.getMonth() > _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear()) || d.getFullYear() > _lastDate.getFullYear()) {
			d = new Date(_lastDate.getFullYear(), _lastDate.getMonth(), 1);;
		}

		var calDiv = $.DIV({className:'popup-calendar'}, '');
		var jCalDiv = $(calDiv);
		var firstMonth = true;
		var firstDate = _firstDate.getDate();

		// create prev and next links
		var prevLinkDiv = '';
		if (!(d.getMonth() == _firstDate.getMonth() && d.getFullYear() == _firstDate.getFullYear())) {
			// not in first display month so show a previous link
			firstMonth = false;
			var lastMonth = new Date(d.getFullYear(), d.getMonth()-1, 1);
			var prevLink = $.A({href:'javascript:;'}, navLinks.p);
			$(prevLink).click(function()
			{
				$.datePicker.changeMonth(lastMonth, this);
				return false;
			});
			prevLinkDiv = $.DIV({className:'link-prev'}, '<', prevLink);
		}

		var finalMonth = true;
		var lastDate = _lastDate.getDate();
		nextLinkDiv = '';
		if (!(d.getMonth() == _lastDate.getMonth() && d.getFullYear() == _lastDate.getFullYear())) {
			// in the last month - no next link
			finalMonth = false;
			var nextMonth = new Date(d.getFullYear(), d.getMonth()+1, 1);
			var nextLink = $.A({href:'javascript:;'}, navLinks.n);
			$(nextLink).click(function()
			{
				$.datePicker.changeMonth(nextMonth, this);
				return false;
			});
			nextLinkDiv = $.DIV({className:'link-next'}, nextLink, '>');
		}

		var closeLink = $.A({href:'javascript:;'}, navLinks.c);
		$(closeLink).click(function()
		{
			$.datePicker.closeCalendar();
		});

		jCalDiv.append(
			$.DIV({className:'link-close'}, closeLink),
			$.H3({}, months[d.getMonth()], ' ', d.getFullYear())
		);

		var headRow = $.TR({});
		for (var i=0; i<7; i++) {
			var day = days[i];
			headRow.appendChild(
				$.TH({scope:'col', abbr:day, title:day}, day.substr(0, 1))
			);
		}

		var tBody = $.TBODY();

		var lastDay = (new Date(d.getFullYear(), d.getMonth()+1, 0)).getDate();
		var curDay = -d.getDay();

		var todayDate = (new Date()).getDate();
		var thisMonth = d.getMonth() == today.getMonth() && d.getFullYear() == today.getFullYear();

		var w = 0;
		while (w++<6) {
			var thisRow = $.TR({});
			for (var i=0; i<7; i++) {
				var atts = {};

				if (curDay < 0 || curDay >= lastDay) {
					dayStr = ' ';
				} else if (firstMonth && curDay < firstDate-1) {
					dayStr = curDay+1;
					atts.className = 'inactive';
				} else if (finalMonth && curDay > lastDate-1) {
					dayStr = curDay+1;
					atts.className = 'inactive';
				} else {
					d.setDate(curDay+1);
					var dStr = _dateToStr(d);
					dayStr = $.A({href:'#', rel:dStr}, curDay+1);
					$(dayStr).click(function(e)
					{
						$.datePicker.selectDate($.attr(this, 'rel'), this);
						return false;
					});
					if (_selectedDate && _selectedDate==dStr) {
						$(dayStr).addClass('selected');
					}
				}

				if (thisMonth && curDay+1 == todayDate) {
					atts.className = 'today';
				}
				thisRow.appendChild($.TD(atts, dayStr));
				curDay++;
			}
			tBody.appendChild(thisRow);
		}

		jCalDiv.append(
			$.TABLE({cellspacing:2}, $.THEAD({}, headRow), tBody),
			prevLinkDiv,
			nextLinkDiv
		);

		jCalDiv.css({'display':'block'});
		return calDiv;
	};
	var _draw = function(c)
	{
		// explicitly empty the calendar before removing it to reduce the (MASSIVE!) memory leak in IE
		// still not perfect but a lot better!
		// Strangely if you chain the methods it reacts differently - when chained opening the calendar on
		// IE uses a bunch of memory and pressing next/prev doubles this memory. When you close the calendar
		// the memory is freed. If they aren't chained then pressing next or previous doesn't double the used
		// memory so only one chunk of memory is used when you open the calendar (which is also freed when you
		// close the calendar).
		$('div.popup-calendar a', _openCal).unbind();
		$('div.popup-calendar', _openCal).empty();
		$('div.popup-calendar', _openCal).remove();
		_openCal.append(c);
	};
	var _closeDatePicker = function()
	{
		$('div.popup-calendar a', _openCal).unbind();
		$('div.popup-calendar', _openCal).empty();
		$('div.popup-calendar', _openCal).css({'display':'none'});

		$(document).unbind('mousedown', _checkMouse);
		delete _openCal;
		_openCal = null;
	};
	var _handleKeys = function(e)
	{
		var key = e.keyCode ? e.keyCode : (e.which ? e.which: 0);
		//console.log('KEY!! ' + key);
		if (key == 27) {
			_closeDatePicker();
		}
		return false;
	};
	var _checkMouse = function(e)
	{
		var target = e.target;
		var cp = $(target).findClosestParent('div.popup-calendar');
		if (cp.get(0).className != 'date-picker-holder') {
			_closeDatePicker();
		}
	};

	return {
		show: function()
		{
			if (_openCal) {
				_closeDatePicker();
			}
			this.blur();
			var input = $('input', $(this).findClosestParent('input'))[0];
			_firstDate = input._startDate;
			_lastDate = input._endDate;
			_openCal = $(this).findClosestParent('div.popup-calendar');
			var d = $(input).val();
			if (d != '') {
				if (_dateToStr(_strToDate(d)) == d) {
					_selectedDate = d;
					_draw(_getCalendarDiv(_strToDate(d)));
				} else {
					// invalid date in the input field - just default to this month
					_selectedDate = false;
					_draw(_getCalendarDiv());
				}
			} else {
				_selectedDate = false;
				_draw(_getCalendarDiv());
			}
			$(document).bind('mousedown', _checkMouse);
		},
		changeMonth: function(d, e)
		{

			_draw(_getCalendarDiv(d));
		},
		selectDate: function(d, ele)
		{
			selectedDate = d;
            var formattedDate = _dateToStr(new Date(d));
			var $theInput = $('input', $(ele).findClosestParent('input'));
			$theInput.val(formattedDate);
			$theInput.trigger('change');
			_closeDatePicker(ele);
		},
		closeCalendar: function()
		{
			_closeDatePicker(this);
		},
		setInited: function(i)
		{
			i._inited = true;
		},
		isInited: function(i)
		{
			return i._inited != undefined;
		},
		setDateFormat: function(format)
		{
			// set's the format that selected dates are returned in.
			// options are 'dd/mm/yyyy' (european), 'mm/dd/yyyy' (americian) and 'yyyy-mm-dd' (unicode)
			dateFormat = format;
		},
		/**
		* Function: setLanguageStrings
		*
		* Allows you to localise the calendar by passing in relevant text for the english strings in the plugin.
		*
		* Arguments:
		* days		-	Array, e.g. ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
		* months	-	Array, e.g. ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		* navLinks	-	Object, e.g. {p:'Prev', n:'Next', c:'Close'}
		**/
		setLanguageStrings: function(aDays, aMonths, aNavLinks)
		{
			days = aDays;
			months = aMonths;
			navLinks = aNavLinks;
		},
		/**
		* Function: setDateWindow
		*
		* Used internally to set the start and end dates for a given date select
		*
		* Arguments:
		* i			-	The id of the INPUT element this date window is for
		* w			-	The date window - an object containing startDate and endDate properties
		*				each in the current format as set in a call to setDateFormat (or in the
		*				default format dd/mm/yyyy if setDateFormat hasn't been called).
		*				e.g. {startDate:'01/03/2006', endDate:'11/04/2006}
		**/
		setDateWindow: function(i, w)
		{
			if (w == undefined) w = {};
			if (w.startDate == undefined) {
				i._startDate = new Date();
			} else {
				i._startDate = _strToDate(w.startDate);
			}
			if (w.endDate == undefined) {
				i._endDate = new Date();
				i._endDate.setFullYear(i._endDate.getFullYear()+5);
			} else {
				i._endDate = _strToDate(w.endDate);
			};
		}
	};
}();
$.fn.findClosestParent = function(s)
{
	var ele = this;
	while (true) {
		if ($(s, ele).length > 0) {
			return (ele);
		}
		ele = ele.parent();
		if(ele[0].length == 0) {
			return false;
		}
	}
};
$.fn.datePicker = function(a)
{
	this.each(function() {
		if(this.nodeName.toLowerCase() != 'input') return;
		$.datePicker.setDateWindow(this, a);
		if (!$.datePicker.isInited(this)) {
			var calBut = $.A({href:'javascript:;', className:'date-picker', title: '<?php echo htmlentities(_('Choose Date')); ?>'}, $.SPAN({}, '<?php echo htmlentities(_('Choose Date')); ?>'));
			$(calBut).click($.datePicker.show);
			$(this).wrap(
				'<div class="date-picker-holder"></div>'
			).before(
				$.DIV({className:'popup-calendar'})
			).after(
				calBut
			);
			$.datePicker.setInited(this);
		}
	});

};

$().ready(function() {
    $('input.datepicker').datePicker();
});

</script>

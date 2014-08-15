/**
 * Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
 *
 * The contents of this directory (themes/default/*) are part of poMMo (http://www.pommo.org)
 *
 * poMMo is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published
 * by the Free Software Foundation; either version 2, or any later version.
 *
 * poMMo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 * the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with program; see the file docs/LICENSE. If not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */

// Abstraction Libary for CKEdior 2.5.1
var wysiwyg = {
	enabled: false, // state of WYSIWYG. False == Disabled, True == Enabled (visible). Default to disabled. Compose.php will enable it.
	language: 'en', // the WYSIWYG language
	baseURL: '/pommo/themes/wysiwyg/', // the URL path to poMMo's WYSIWYG directory [written via compose.php]
	ck: false, // instance of the CKEditor
	textarea: false, // shortcut to the original textarea [jQuery Object]
	enable: function(){ // enable the WYSIWYG

		if(!this.ck) {
			if (CKEDITOR.instances['body']) {
				CKEDITOR.instances['body'].destroy(true);
			}
			// lock the interface
			poMMo.pause();
			this.textarea = $('#ck_mailing');

			// prepare CKEditor
			this.ck = CKEDITOR.replace('ck_mailing', {
				customConfig: this.baseURL + 'ckeditor.conf.js?v=6',
				language: this.language,
				contentsLangDirection: 'ltr'
			});
		}
		else {
			// hide the textarea, update the wysiwyg with its data
			this.ck.setData(this.textarea.hide()[0].value);

			// show the WYSIWYG
			$('#cke_ck_mailing').show();
		}
		return this.enabled = true;
	},
	disable: function(){
		if(!this.enabled || !this.ck)
			return false;

		// hide the WYSIWYG
		$('#cke_ck_mailing').hide();

		// show the textarea, update it with WYSIWYG contents
		this.textarea.show()[0].value = this.ck.getData();

		// Why jQuery doesn't do this in show() I don't know
		this.textarea.css('visibility', 'visible');

		this.enabled = false;
		return true;
	},
	inject: function(text){ // called when adding a personalization

		// check if WYSIWYG is enabled
		if (this.enabled) {

			// check if text should be encased in an anchor tag.
			if(text == '[[!unsubscribe]]')
				text = '<a href="'+text+'">'+this.t_unsubscribe+'</a>';
			if(text == '[[!weblink]]')
				text = '<a href="'+text+'">'+this.t_weblink+'</a>';

			this.ck.insertHtml(text);
		}
		else {
			this.textarea[0].value += text;
		}
	},
	getBody: function() {
		return (this.enabled) ?
			this.ck.getData() :
			this.textarea.val();
	},
	init: function(o) {
		this.ck = false;
		wysiwyg = $.extend(wysiwyg,o);
	},
	t_weblink: "View this Mailing on the Web", // translated via compose.php
	t_unsubscribe: "Unsubscribe or Update your Records" // translated via compose.php
}


CKEDITOR.on("instanceReady", function(event)
{
	poMMo.resume();
});

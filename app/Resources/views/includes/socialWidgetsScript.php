<?php
/**
 * OCAX -- Citizen driven Observatory software
 * Copyright (C) 2014 OCAX Contributors. See AUTHORS.

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.

 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
?>

<div id="fb-root"></div>
<script>
/*
$( document ).on( "mouseleave", ".socialWidgetBox", function() {
	$(this).fadeOut('fast')
});
*/
// http://www.blackfishweb.com/blog/asynchronously-loading-twitter-google-facebook-and-linkedin-buttons-and-widgets-ajax-bonus
// http://stackoverflow.com/questions/12810018/fb-getloginstatus-called-before-calling-fb-init/16593474#16593474
function showSocialWidgets(){
	$( ".socialWidgetBox" ).show( 0 , function() {
		if (typeof (twttr) != 'undefined') {
			twttr.widgets.load();
		} else {
			$.getScript('http://platform.twitter.com/widgets.js');
		}
		if (typeof (FB) != 'undefined') {
				FB.init({ status: 0, cookie: true, xfbml: true });
				//FB.XFBML.parse();
		} else {
			$.getScript("http://connect.facebook.net/en_US/all.js#xfbml=1&status=0", function () {
				FB.init({ status: 0, cookie: true, xfbml: true });
			});
		}
	});
}
</script>


<?php

/**
OCAX -- Citizen driven Observatory software
Copyright (C) 2015 OCAX Contributors. See AUTHORS.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

header("Content-type: text/css; charset: UTF-8");
header("Pragma: cache");
header("Cache-Control: must-revalidate");
$offset = strtotime('+42 hours'); // same as time() + 42 * 60 * 60
$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", $offset) . " GMT";
header($ExpStr);
		
$color = '#'.Config::model()->getSiteColor();
?>

.color { color:<?php echo $color;?>; }

body {color: #555; background-color: #e4ded7; }
h1, h2, h3, h4, h5, h6 { color:<?php echo $color;?>; }

a { color:<?php echo $color;?>; }
a:hover { color:#676767; }

.link { color:<?php echo $color;?>;}
.link:hover {color:#676767;}

#page { background-color: #f5f1ed; }

#mainmenu ul li a {	color:#f5f1ed; background-color:<?php echo $color;?>; }
#mainmenu ul li a:hover, #mainmenu ul li.active a {	color: <?php echo $color;?>; background-color:#f5f1ed; }

#header_bar > ul > li > a:hover { color:<?php echo $color;?>; }

#logo { color:#a6a29e; }
#observatoryName2 { color: <?php echo $color;?>; }

#footer { color:#1F1F1F; background-color:#676767; }
#footer div { color:#a6a6a6; }
#footer div b {	color:#ffffff; }

div.form .title{ color:<?php echo $color;?>; }
.bigTitle { color: <?php echo $color;?>; }

/* Mainmenu */
#mainMbMenu #nav-bar li a { color:#f5f1ed; background-color: <?php echo $color;?>; }
#mainMbMenu #nav-bar li.active a { color: <?php echo $color;?>; background-color:#f5f1ed; }
#mainMbMenu #nav-bar li a:hover { color: <?php echo $color;?>; background-color:white; }

#mainMbMenu #nav-bar #nav ul li a {
	color:<?php echo $color;?>;
	background-color:rgba(255, 255, 255, 0.9);
}
#mainMbMenu #nav-bar #nav ul li a:hover, #mainmenu ul li.active a {
	color: white;
	background-color:<?php echo $color;?>;
}

.tabMenu { border-bottom: 1px solid <?php echo $color;?>; }
.tabMenu li.activeItem {	border-bottom: 4px solid <?php echo $color;?>; }

/*ENQUIRIYS*/
.enquirys b { color:#00CADC; }
.enquirys_titular {	color:#929292; }
.email-subscribe.active i { color: <?php echo $color;?>; } /* enquiry/_preview */

/*sitePage Pages*/
.sitePage_titulo { color:<?php echo $color;?>; }
.activeMenuItem a { color: #676767; }
.sitePageInsert { border-color: <?php echo $color;?>; }

/*Budgets*/
#budget_titulo_j { color: <?php echo $color;?>; }

.svg-color-fill { fill:<?php echo $color;?>; }
.svg-color-stroke { stroke: <?php echo $color;?>; }

#featured_menu > li:hover {color:white; background-color:<?php echo $color;?>; }

.prev_budget_arrow{	color:<?php echo $color;?>; }

/*graph bar*/
.actual_provision_bar{ background-color:<?php echo $color;?>; }

.highlightWithColor{
	color: white;
	background-color: <?php echo $color;?> !important;
}

span.ocaxButton.active i { color: <?php echo $color;?>; }

/*yii pager*/

ul.yiiPager a:link,
ul.yiiPager a:visited
{
	border:solid 1px #827E78;
	font-weight:bold;
	color:#676767;
	padding:1px 6px;
	text-decoration:none;
}
ul.yiiPager a:hover
{
	border:solid 1px <?php echo $color;?>;
	background-color:<?php echo $color;?>;
	opacity: 0.5;
	color:#000000;
}
ul.yiiPager .selected a
{
	border:solid 1px <?php echo $color;?>;
	background:<?php echo $color;?>;
	color:#FFFFFF;
	font-weight:bold;
}

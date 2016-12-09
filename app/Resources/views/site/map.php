<?php

/**
 * OCAX -- Citizen driven Observatory software
 * Copyright (C) 2015 OCAX Contributors. See AUTHORS.

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

/* @var $this SiteController */


$this->pageTitle=__('Map');
?>

<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>

<style>
#map { height: 100%; }
.ocm_popup_link{ font-size:1.25em }
</style>

<div id="map"></div>

<script>
<?php
$lat = Config::model()->findByPk('administrationLatitude')->value;
$lon = Config::model()->findByPk('administrationLongitude')->value;

if($lat && $lon)
	$zoom = 13;
else
	$zoom = 1;
?>

var RedIcon = L.Icon.Default.extend({ options: { iconUrl: "<?php echo Yii::app()->request->baseUrl.'/images/marker-icon-red.png';?>" } });
var redIcon = new RedIcon();
var map = L.map('map').setView([<?php echo $lat;?>,<?php echo $lon;?>],<?php echo $zoom;?>);

L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
    maxZoom: 18
}).addTo(map);


var marker = L.marker([<?php echo "$lat, $lon";?>], {icon: redIcon}).addTo(map);
marker.bindPopup("<a class='ocm_popup_link' href='<?php echo Yii::app()->getBaseUrl(true);?>' target='_blank' /><?php echo Config::model()->getObservatoryName();?></a>");


</script>


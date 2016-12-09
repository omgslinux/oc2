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

	$config=Config::model();

	echo '<p>';
	
	echo '<span>';
	echo __('Installed').': '.$config->getOCAXVersion().'&nbsp;&nbsp;&nbsp;';
	echo __('Available').': '.$config->getLatestOCAXVersion();
	if($config->isOCAXUptodate())
		echo '<i class="icon-ok-circled"></i>';
	else
		echo '<br />'.__('Please update').' <i class="icon-attention red"></i>';
	echo '</span><br />';

	echo 'Database schema: version '.$config->findByPk('schemaVersion')->value.'<br />';

	echo '</p>';
?>

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

/*
 * https://github.com/kennberg/php-mysql-migrate
 */

Yii::import('application.includes.*');
require_once('runSQL.php');

class Schema
{
	protected $MIGRATIONS_DIR;
	protected $version = Null;
	protected $MIGRATE_FILE_PREFIX = 'migrate-version-';
	protected $MIGRATE_FILE_POSTFIX = '.sql';

	public function __construct( /*...*/ ) {
		$this->MIGRATIONS_DIR = Yii::app()->basePath.'/migrations/';
		$this->version = Config::model()->findByPk('schemaVersion');
	}

	public function migrate()
	{
		$files = $this->get_migrations();

		// Check to make sure there are no conflicts such as 2 files under the same version.
		$errors = array();
		$last_file = false;
		$last_version = false;
		foreach ($files as $file) {
			$file_version = $this->get_version_from_file($file);
			if ($last_version !== false && $last_version === $file_version) {
				$errors[] = "Duplicate file version: $last_file --- $file";
			}
			$last_version = $file_version;
			$last_file = $file;
		}
		if (count($errors) > 0)
			return $errors;

		// Run all the new files.
		foreach ($files as $file) {
			$file_version = $this->get_version_from_file($file);
			if ($file_version <= $this->version->value)
				continue;

			$result = runSQLFile($this->MIGRATIONS_DIR.$file);
			if(!$result)
				return "Migrating file:".$this->MIGRATIONS_DIR.$file." failed";

			$this->version->value = $file_version;
			$this->version->save();
		}
		$postInstall = Config::model()->findByPk('siteConfigStatusPostInstallChecked');
		$postInstall->value = 0;
		$postInstall->save();
		return 0;
	}

	protected function get_migrations() {
		// Find all the migration files in the directory and return sorted.
		$files = array();
		$dir = opendir($this->MIGRATIONS_DIR);
		while ($file = readdir($dir)) {
			if (substr($file, 0, strlen($this->MIGRATE_FILE_PREFIX)) == $this->MIGRATE_FILE_PREFIX)
				$files[] = $file;
		}
		asort($files);
		return $files;
	}

	protected function get_version_from_file($file) {
		return intval(substr($file, strlen($this->MIGRATE_FILE_PREFIX)));
	}

	public function isSchemaUptodate($ocaxVersion)
	{
		$lines = file(Yii::app()->basePath.'/data/schema.versions');
		foreach ($lines as $lineNumber => $line) {
			if(strpos($line, '#') !== false)
				continue;
			$versions = explode(':',$line);
			if (trim($versions[0]) == $ocaxVersion) {
				if(trim($versions[1]) === Config::model()->findByPk('schemaVersion')->value)
					return 1;
			}
		}
		return 0;
	}

/*
	public function add($fn)
	{
		$new_version = $this->version;
		// Check the new version against existing migrations.
		$files = get_migrations();
		$last_file = end($files);
		if ($last_file !== false) {
			$file_version = get_version_from_file($last_file);
			if ($file_version > $new_version)
				$new_version = $file_version;
		}
		// Create migration file path.
		$new_version++;
		$path = $this->MIGRATIONS_DIR.$this->MIGRATE_FILE_PREFIX.sprintf('%04d', $new_version).$this->MIGRATE_FILE_POSTFIX;

		echo "Adding a new migration script: $path\n";
		rename($fn, $path);
	}
*/
}


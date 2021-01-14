<?php
/**
 * System cli file.
 *
 * @package   App
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace App\Cli;

/**
 * System cli class.
 */
class System extends Base
{
	/** {@inheritdoc} */
	public $moduleName = 'System';

	/** @var string[] Methods list */
	public $methods = [
		'history' => 'History of uploaded updates',
		'update' => 'Update',
		'checkRegStatus' => 'Check registration status',
	];

	/**
	 * History of uploaded updates.
	 *
	 * @return void
	 */
	public function history(): void
	{
		$table = array_map(function ($item) {
			$item['result'] = $item['result'] ? 'OK' : 'Error';
			unset($item['id']);
			return $item;
		}, \Settings_Updates_Module_Model::getUpdates());
		if ($table) {
			$this->climate->table($table);
		}
		$this->cli->actionsList('System');
	}

	/**
	 * Update.
	 *
	 * @return void
	 */
	public function update(): void
	{
		$maxExecutionTime = ini_get('max_execution_time');
		if ($maxExecutionTime < 1 || $maxExecutionTime > 600) {
			$this->climate->lightGreen('Max execution time = ' . $maxExecutionTime);
		} else {
			$this->climate->lightRed('Max execution time = ' . $maxExecutionTime);
		}
		$options = [];
		$toInstall = \App\YetiForce\Updater::getToInstall();
		foreach ($toInstall as $package) {
			$option = "{$package['label']}";
			if ($package['fromVersion'] !== $package['toVersion']) {
				$option .= " ({$package['fromVersion']} >> {$package['toVersion']})";
			}
			if (\App\YetiForce\Updater::isDownloaded($package)) {
				$option .= ' - Downloaded, ready to install';
			} else {
				$option .= ' - To download';
			}
			$options[$package['hash']] = $option;
		}
		if (!$options) {
			$this->climate->lightBlue('No updates available');
			return;
		}
		$input = $this->climate->radio('Updates available:', $options);
		$hash = $input->prompt();
		foreach ($toInstall as $package) {
			if ($package['hash'] === $hash) {
				if (\App\YetiForce\Updater::isDownloaded($package)) {
					$startTime = microtime(true);
					try {
						$packageInstance = new \vtlib\Package();
						$response = $packageInstance->import(ROOT_DIRECTORY . \DIRECTORY_SEPARATOR . \Settings_ModuleManager_Module_Model::getUploadDirectory() . \DIRECTORY_SEPARATOR . $package['hash'] . '.zip', true);
						if ($packageInstance->_errorText) {
							$this->climate->lightRed($packageInstance->_errorText);
						} else {
							echo $response;
						}
					} catch (\Throwable $th) {
						$this->climate->lightRed($th->__toString());
					}
					$this->climate->lightBlue('Update time: ' . round(microtime(true) - $startTime, 2));
					$this->climate->lightBlue('Check the update logs: cache/logs/update.log');
				} else {
					\App\YetiForce\Updater::download($package);
					$this->update();
				}
				return;
			}
		}
	}

	/**
	 * Check registration status.
	 *
	 * @return void
	 */
	public function checkRegStatus(): void
	{
		$this->cli->actionsList('System');
	}
}
<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Gaara\Conf\Manager;

class test {

	public function index() {

		$envFile = dirname(__DIR__) . '/dev/.env';
		$configFolderPath = dirname(__DIR__) . '/dev/config/';

		$conf = new Manager($envFile, $configFolderPath);


		return $conf->getEnv('DB_USER', 'account');

	}

}

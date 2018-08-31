<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Xutengx\Conf\Conf;

class test {

	public function index() {

		$envFile = dirname(__DIR__) . '/dev/.env';
		$configFolderPath = dirname(__DIR__) . '/dev/config/';

		$conf = new Conf($envFile, $configFolderPath);


		return $conf->getEnv('DB_USER', 'account');

	}

}

<?php

declare(strict_types = 1);
namespace Gaara\Conf;

use Exception;
use Gaara\Exception\Conf\{NoConnectionException, NotFoundConfFileException, NotFoundEnvFileException,
	NotFoundServerIniFileException, UndefinedConnectionException};

class Manager {

	/**
	 * 配置文件路径
	 * @var string
	 */
	protected $configFolderPath;
	/**
	 * 配置信息
	 * @var array
	 */
	protected $data = [];

	/**
	 * 环境变量
	 * @var array
	 */
	protected $env = [];

	/**
	 * Manager constructor.
	 * @param string $envFile 环境变量ini文件
	 * @param string $configFolderPath 配置文件路径
	 * @throws NotFoundEnvFileException
	 */
	public function __construct(string $envFile, string $configFolderPath) {
		$this->setEnv($envFile);
		$this->configFolderPath = rtrim($configFolderPath . '/') . '/';
	}

	/**
	 * 获取环境变量
	 * @param string $name
	 * @param mixed $default 当此变量不存在时的默认值
	 * @return mixed
	 */
	public function getEnv(string $name, $default = null) {
		return $this->env[$name] ?? $default;
	}

	/**
	 * 读取环境变量ini 并赋值给 $this->env
	 * 包含多配置的选择
	 * @param string $envFile
	 * @return void
	 * @throws NotFoundEnvFileException
	 */
	protected function setEnv(string $envFile): void {
		if (is_file($envFile)) {
			$data      = parse_ini_file($envFile, true);
			$env       = $data['ENV'];
			$this->env = array_merge($data, $data[$env]);
		}
		else throw new NotFoundEnvFileException("[$envFile]");
	}

	/**
	 * 服务器相关初始化文件
	 * @param string $filename
	 * @return array
	 * @throws Exception
	 */
	public function getServerConf(string $filename): array {
		if (is_file($file = $this->configFolderPath . 'server/' . $filename . '.php')) {
			return $this->{'server/' . $filename};
		}
		throw new NotFoundServerIniFileException("[$file]");
	}

	/**
	 * 惰性读取配置文件且缓存
	 * @param string $configName
	 * @return mixed
	 * @throws NotFoundConfFileException
	 */
	public function __get(string $configName) {
		if (array_key_exists($configName, $this->data)) {
			return $this->data[$configName];
		}
		elseif (is_file($filename = $this->configFolderPath . $configName . '.php')) {
			return $this->data[$configName] = require($filename);
		}
		throw new NotFoundConfFileException("[$filename]");
	}

	/**
	 * 得到某个驱动的连接属性
	 * @param string $driver
	 * @param string $connection
	 * @return array
	 * @throws UndefinedConnectionException
	 * @throws NoConnectionException
	 */
	public function getDriverConnection(string $driver, string $connection): array {
		$conf = $this->{$driver};
		if (isset($conf['connections'])) {
			if (isset($conf['connections'][$connection]))
				return $conf['connections'][$connection];
			throw new UndefinedConnectionException("[$driver] didn't have a connection called [$connection].");
		}
		throw new NoConnectionException("[$driver] didn't have connections.");
	}

}

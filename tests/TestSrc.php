<?php
declare(strict_types = 1);

use Gaara\Conf\Manager;
use PHPUnit\Framework\TestCase;

final class TestSrc extends TestCase {

	public function testMakeFileDriver() {
		$envFile          = dirname(__DIR__) . '/dev/.env';
		$configFolderPath = dirname(__DIR__) . '/dev/config';

		$this->assertInstanceOf(Manager::class, $conf = new Manager($envFile, $configFolderPath));
		$this->assertEquals('127.0.0.1', $conf->getEnv('REDIS_HOST'), '获取环境变量 string');
		$this->assertEquals(6379, $conf->getEnv('REDIS_PORT'), '获取环境变量 int');
		$this->assertEquals(true, $conf->getEnv('DEBUG'), '获取环境变量 bool');
		$this->assertEquals('smtp.qq.com', $conf->getEnv('MAIL_HOST'), '获取环境变量 选择');
		$this->assertNull($conf->getEnv('MAIL_HOST_2'), '获取不存在环境变量, 无默认值');
		$this->assertEquals('default', $conf->getEnv('MAIL_HOST_2', 'default'), '获取不存在环境变量, 有默认值');

		$this->assertEquals('300', $conf->cache['expire'], '获取配置变量');

		$this->assertEquals([
			'SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ',
			'SET NAMES UTF8',
		], $conf->{'server/mysql'}['ini_sql'], '获取系统配置');

		$this->assertEquals([
			'SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ',
			'SET NAMES UTF8',
		], $conf->getServerConf('mysql')['ini_sql'], '获取系统配置');
	}

}



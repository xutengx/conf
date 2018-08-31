<?php
declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use Xutengx\Conf\Conf as Manager;
use Xutengx\Conf\Exception\{NoConnectionException, NotFoundConfFileException, NotFoundEnvFileException,
	NotFoundServerIniFileException, UndefinedConnectionException};

final class SrcTest extends TestCase {

	public function testGet() {
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

	public function testException() {
		$envFile          = dirname(__DIR__) . '/dev/.env';
		$configFolderPath = dirname(__DIR__) . '/dev/config';


		$errorConfKeyName = 'not_set_conf';
		try {
			$this->assertInstanceOf(Manager::class, $conf = new Manager($errorConfKeyName, $configFolderPath));
		} catch (NotFoundEnvFileException $exception) {
			$exception1 = true;
			$this->assertEquals("[$errorConfKeyName]", $exception->getMessage());
		} finally {
			$this->assertTrue($exception1);
		}
		

		$this->assertInstanceOf(Manager::class, $conf = new Manager($envFile, $configFolderPath));

		try {
			$this->assertEquals([
				'SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ',
				'SET NAMES UTF8',
			], $conf->getServerConf($errorConfKeyName)['ini_sql'], '获取系统配置');
		} catch (NotFoundServerIniFileException $exception) {
			$exception2 = true;
			$this->assertEquals("[$configFolderPath/server/$errorConfKeyName.php]", $exception->getMessage());
		} finally {
			$this->assertTrue($exception2);
			
		}

		try {
			$this->assertEquals('300', $conf->{$errorConfKeyName}['expire'], '获取配置变量');
		} catch (NotFoundConfFileException $exception) {
			$exception3 = true;
			$this->assertEquals("[$configFolderPath/$errorConfKeyName.php]", $exception->getMessage());
		} finally {
			$this->assertTrue($exception3);
		}

		try {
			$this->assertEquals('', $conf->getDriverConnection($errorConfKeyName, 'con1'));
		} catch (NoConnectionException $exception) {
			$exception4 = true;
			$this->assertEquals("[$configFolderPath/$errorConfKeyName.php]", $exception->getMessage());
		} finally {
			$this->assertTrue($exception4);

		}
	}

}



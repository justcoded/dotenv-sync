<?php

namespace JustCoded\SyncEnv;

use Dotenv\Dotenv;
use Exception;

/**
 * Class EnvChecker
 *
 * @package JustCoded\SyncEnv
 */
class EnvChecker
{
	const ENV = '.env';
	const ENV_EXAMPLE = '.env.example';

	/**
	 * Env File Name
	 *
	 * @var string
	 */
	protected $compare;

	/**
	 * Env Example File Name
	 *
	 * @var string
	 */
	protected $compareWith;

	/**
	 * Content
	 *
	 * @var array
	 */
	protected $keys = [];


	/**
	 * EnvChecker constructor.
	 *
	 * @param string $env
	 * @param string $envExample
	 */
	public function __construct($env = self::ENV, $envExample = self::ENV_EXAMPLE)
	{
		$this->compare = $env;
		$this->compareWith = $envExample;
	}


	/**
	 * Check
	 *
	 * @throws Exception
	 */
	public static function check()
	{
		echo (new static)->performCheck();
	}


	/**
	 * Check
	 *
	 * @throws Exception
	 */
	public static function checkReverse()
	{
		echo (new static(self::ENV_EXAMPLE, self::ENV))->performCheck();
	}


	/**
	 * Perform Check
	 *
	 * @throws Exception
	 */
	protected function performCheck()
	{
		$this->ensureFilesExist();

		foreach ([$this->compare, $this->compareWith] as $file) {
			$this->parseKeys($file);
		}

		$missingKeys = array_diff($this->keys[$this->compare], $this->keys[$this->compareWith]);

		return $this->prepareOutput($missingKeys);
	}


	/**
	 * Ensure Files Exist
	 *
	 * @throws Exception
	 */
	protected function ensureFilesExist()
	{
		foreach ([$this->compare, $this->compareWith] as $file) {
			if (! file_exists($this->getRootPath($file))) {
				throw new Exception("File {$this->getRootPath($file)} does not exists");
			}
		}
	}


	/**
	 * Parse File
	 *
	 * @param $file
	 */
	protected function parseKeys($file)
	{
		$dotenv = new Dotenv($this->getRootPath(), $file);
		$dotenv->load();
		$this->keys[$file] = $dotenv->getEnvironmentVariableNames();
	}


	/**
	 * Get Root Path
	 *
	 * @param null $path
	 *
	 * @return bool|string
	 */
	protected function getRootPath($path = null)
	{
		// TODO: Replace with real vendor based path
		$rootPath = realpath(__DIR__ . '/../../test_admin1');

		return $path ? realpath($rootPath . DIRECTORY_SEPARATOR . $path) : $rootPath;
	}


	/**
	 * Prepare Output
	 *
	 * @param $missingKeys
	 *
	 * @return string
	 * @throws Exception
	 */
	protected function prepareOutput($missingKeys)
	{
		if (empty($missingKeys)) {
			return PHP_EOL . "You file {$this->compareWith} is already in sync with {$this->compare}" . PHP_EOL;
		}

		$message = "The following variables are not present in your {$this->compareWith} file: " . PHP_EOL;
		foreach ($missingKeys as $diffKey) {
			$message .= ' - ' . $diffKey . PHP_EOL;
		}

		throw new Exception($message);
	}
}

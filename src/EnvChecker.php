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
	protected $envFileName;

	/**
	 * Env Example File Name
	 *
	 * @var string
	 */
	protected $envExampleFileName;

	/**
	 * Content
	 *
	 * @var array
	 */
	protected $keys = [];

	/**
	 * Reverse
	 *
	 * @var bool
	 */
	protected $reverse;


	/**
	 * EnvChecker constructor.
	 *
	 * @param bool $reverse
	 * @param string $env
	 * @param string $envExample
	 */
	public function __construct($reverse = false, $env = EnvChecker::ENV, $envExample = EnvChecker::ENV_EXAMPLE)
	{
		$this->reverse = $reverse;
		$this->envFileName = $env;
		$this->envExampleFileName = $envExample;
	}


	/**
	 * Check
	 *
	 * @param bool $reverse
	 * @param null $source
	 * @param null $destination
	 *
	 * @throws Exception
	 */
	public static function check($reverse = false, $source = null, $destination = null)
	{
		echo (new static($reverse, $source, $destination))->performCheck();
	}


	/**
	 * Perform Check
	 *
	 * @throws Exception
	 */
	protected function performCheck()
	{
		$this->ensureFilesExist();

		foreach ([$this->envFileName, $this->envExampleFileName] as $file) {
			$this->parseKeys($file);
		}

		$diffKeys = array_diff($this->keys[static::ENV_EXAMPLE], $this->keys[static::ENV]);


		return array_filter($this->keys[static::ENV], function ($key) use ($diffKeys) {
			return in_array($key, $diffKeys);
		});

	}


	/**
	 * Ensure Files Exist
	 *
	 * @throws Exception
	 */
	protected function ensureFilesExist()
	{
		foreach ([$this->envFileName, $this->envExampleFileName] as $file) {
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
		$rootPath = realpath(__DIR__. '/../../test_admin1');

		return $path ? realpath($rootPath . DIRECTORY_SEPARATOR . $path) : $rootPath;
	}
}

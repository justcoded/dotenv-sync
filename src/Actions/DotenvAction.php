<?php

namespace JustCoded\DotenvSync\Actions;

use Exception;
use Dotenv\Dotenv;

/**
 * Class DotenvAction
 *
 * @package JustCoded\DotenvSync\Actions
 */
abstract class DotenvAction
{
	const ENV = '.env';
	const ENV_EXAMPLE = '.env.example';

	const ACTION_DIFF = 'diff';
	const ACTION_SYNC = 'sync';

	/**
	 * Env File Name
	 *
	 * @var string
	 */
	protected $master;

	/**
	 * Env Example File Name
	 *
	 * @var string
	 */
	protected $slave;

	/**
	 * Keys
	 *
	 * @var array
	 */
	protected $keys;

	/**
	 * Values
	 *
	 * @var array
	 */
	protected $values;

	/**
	 * Is Success
	 *
	 * @var boolean
	 */
	protected $result = true;

	/**
	 * DotenvSync constructor.
	 *
	 * @param string $env
	 * @param string $envExample
	 */
	public function __construct($env = self::ENV, $envExample = self::ENV_EXAMPLE)
	{
		$this->master = $env;
		$this->slave = $envExample;
	}

	/**
	 * Execute
	 *
	 * @return mixed
	 */
	public abstract function execute();

	/**
	 * Parse File
	 *
	 * @param $file
	 */
	protected function parseDotenv($file)
	{
		$dotenv = new Dotenv($this->getRootPath(), $file);
		$dotenv->load();
		$this->keys[$file] = $dotenv->getEnvironmentVariableNames();
		foreach ($this->keys[$file] as $key) {
			$this->values[$file][$key] = getenv($key);
		}
	}

	/**
	 * Ensure Files Exist
	 *
	 * @throws Exception
	 */
	protected function ensureFilesExist()
	{
		foreach ([$this->master, $this->slave] as $file) {
			if (! file_exists($this->getRootPath($file))) {
				throw new Exception("File {$this->getRootPath($file)} does not exists");
			}
		}
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
		if (false !== strpos(__DIR__, 'vendor')) {
			$rootPath = explode('/vendor/', __DIR__, 2)[0];
		} else {
			$rootPath = explode('/src/', __DIR__, 2)[0];
		}

		return $path ? realpath($rootPath . '/' . $path) : realpath($rootPath);
	}

	/**
	 * Get Value
	 *
	 * @param $file
	 * @param $key
	 *
	 * @return null
	 */
	public function getValue($file, $key)
	{
		if (empty($this->values[$file])) {
			$this->parseDotenv($file);
		}

		return ! empty($this->values[$file][$key]) ? $this->values[$file][$key] : null;
	}

	/**
	 * Exit Code
	 *
	 * @return bool
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * Get Opposite File
	 *
	 * @param $file
	 *
	 * @return string
	 */
	public function getOppositeFile($file)
	{
		return $file == $this->slave ? $this->master : $this->slave;
	}
}

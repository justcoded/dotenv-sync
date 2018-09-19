<?php

namespace JustCoded\DotenvSync;

use Dotenv\Dotenv;
use Exception;

/**
 * Class DotenvDiff
 *
 * @package JustCoded\DotenvSync
 */
class DotenvDiff
{
	const ENV = '.env';
	const ENV_EXAMPLE = '.env.example';

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
	 * Content
	 *
	 * @var array
	 */
	protected $keys = [];

	/**
	 * Content
	 *
	 * @var array
	 */
	protected $diffKeys = [];

	/**
	 * Is Success
	 *
	 * @var boolean
	 */
	protected $isSuccess = true;

	/**
	 * Output
	 *
	 * @var string
	 */
	protected $output;


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
	 * Perform Check
	 *
	 * @throws Exception
	 */
	public function diff()
	{
		$this->ensureFilesExist();

		foreach ([$this->master, $this->slave] as $file) {
			$this->parseDotenv($file);
		}

		$this->diffKeys[$this->slave] = array_diff($this->keys[$this->master], $this->keys[$this->slave]);
		$this->diffKeys[$this->master] = array_diff($this->keys[$this->slave], $this->keys[$this->master]);

		if (! empty($this->diffKeys[$this->slave]) || ! empty($this->diffKeys[$this->master])) {
			$this->isSuccess &= false;
		}

		return $this;
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
	 * Parse File
	 *
	 * @param $file
	 */
	protected function parseDotenv($file)
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
		$rootPath = __DIR__ . '/../../../../';

		return $path ? realpath($rootPath . $path) : realpath($rootPath);
	}


	/**
	 * Output
	 *
	 * @throws Exception
	 */
	public function output()
	{
		foreach ($this->diffKeys as $key => $diffKeys) {
			$this->prepareOutput($key, $diffKeys);
		}

		return $this->output;
	}


	/**
	 * Prepare Output
	 *
	 * @param string $file
	 * @param array $missedKeys
	 *
	 * @return string
	 */
	protected function prepareOutput($file, $missedKeys)
	{
		if (empty($missedKeys)) {
			$this->output .= "You file {$file} is already in sync with" . PHP_EOL;

			return;
		}

		$this->isSuccess &= false;

		$message = "The following variables are not present in your {$file} file: " . PHP_EOL;
		foreach ($missedKeys as $diffKey) {
			$message .= ' - ' . $diffKey . PHP_EOL;
		}

		$this->output .= $message;
	}


	/**
	 * Exit Code
	 *
	 * @return bool
	 */
	public function isSuccess()
	{
		return $this->isSuccess;
	}
}

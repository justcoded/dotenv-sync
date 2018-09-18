<?php

namespace JustCoded\DotenvSync;

use Dotenv\Dotenv;
use Exception;

/**
 * Class EnvChecker
 *
 * @package JustCoded\SyncEnv
 */
class DotenvSync
{
	const ENV = '.env';
	const ENV_EXAMPLE = '.env.example';

	/**
	 * Env File Name
	 *
	 * @var string
	 */
	protected $src;

	/**
	 * Env Example File Name
	 *
	 * @var string
	 */
	protected $dest;

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
	protected $isSuccess;


	/**
	 * DotenvSync constructor.
	 *
	 * @param string $env
	 * @param string $envExample
	 */
	public function __construct($env = self::ENV, $envExample = self::ENV_EXAMPLE)
	{
		$this->src = $env;
		$this->dest = $envExample;
	}


	/**
	 * Perform Check
	 *
	 * @throws Exception
	 */
	public function diff()
	{
		$this->ensureFilesExist();

		foreach ([$this->src, $this->dest] as $file) {
			$this->parseKeys($file);
		}

		$this->diffKeys[$this->src] = array_diff($this->keys[$this->src], $this->keys[$this->dest]);
		$this->diffKeys[$this->dest] = array_diff($this->keys[$this->dest], $this->keys[$this->src]);
	}


	/**
	 * Ensure Files Exist
	 *
	 * @throws Exception
	 */
	protected function ensureFilesExist()
	{
		foreach ([$this->src, $this->dest] as $file) {
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
		$output = '';
		foreach ($this->diffKeys as $key => $diffKeys) {
			$output .= $this->prepareOutput($key, $diffKeys);
		}

		echo $output;

		return $this->isSuccess;
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
		if (empty($missingKeys)) {
			return PHP_EOL . "You file {$file} has no missed variables" . PHP_EOL;
		}

		$this->isSuccess &= false;

		$message = "The following variables are not present in your {$file} file: " . PHP_EOL;
		foreach ($missedKeys as $diffKey) {
			$message .= ' - ' . $diffKey . PHP_EOL;
		}

		return $message;
	}
}

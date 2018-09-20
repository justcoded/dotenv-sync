<?php

namespace JustCoded\DotenvSync\Actions;

use Dotenv\Dotenv;
use Exception;

/**
 * Class DotenvDiff
 *
 * @package JustCoded\DotenvSync
 */
class DotenvDiff extends DotenvAction
{
	/**
	 * Content
	 *
	 * @var array
	 */
	protected $diff = [];

	/**
	 * Output
	 *
	 * @var string
	 */
	protected $output;

	/**
	 * Perform Check
	 *
	 * @throws Exception
	 */
	public function execute()
	{
		$this->ensureFilesExist();

		foreach ([$this->master, $this->slave] as $file) {
			$this->parseDotenv($file);
		}

		$this->diff[$this->slave] = array_diff($this->keys[$this->master], $this->keys[$this->slave]);
		$this->diff[$this->master] = array_diff($this->keys[$this->slave], $this->keys[$this->master]);

		if (! empty($this->diff[$this->slave]) || ! empty($this->diff[$this->master])) {
			$this->result &= false;
		}

		return $this;
	}

	/**
	 * Get Diff
	 *
	 * @return array
	 */
	public function getDiff()
	{
		return $this->diff;
	}
}

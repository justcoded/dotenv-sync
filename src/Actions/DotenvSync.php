<?php

namespace JustCoded\DotenvSync\Actions;

use Exception;

/**
 * Class DotenvSync
 *
 * @package JustCoded\DotenvSync
 */
class DotenvSync extends DotenvAction
{
	/**
	 * Diff Action
	 *
	 * @var DotenvAction
	 */
	protected $diffAction;

	/**
	 * Missed Values
	 *
	 * @var array
	 */
	protected $missedValues;


	/**
	 * DotenvSync constructor.
	 *
	 * @param string $env
	 * @param string $envExample
	 */
	public function __construct($env = self::ENV, $envExample = self::ENV_EXAMPLE)
	{
		parent::__construct($env, $envExample);

		$this->diffAction = new DotenvDiff($env, $envExample);
	}


	/**
	 * Perform Check
	 *
	 * @throws Exception
	 */
	public function execute()
	{
		$diff = $this->diffAction->execute()->getDiff();

		if ($this->diffAction->getResult()) {
			return $this;
		}

		$this->result = true;

		if (! empty($diff[$this->master])) {
			$this->append($this->master, $diff[$this->master], true);
		}

		if (! empty($diff[$this->slave])) {
			$this->append($this->slave, $diff[$this->slave], false);
		}

		return $this;
	}


	/**
	 * Append
	 *
	 * @param string $file
	 * @param array $diff
	 * @param bool $withValues
	 */
	protected function append($file, $diff, $withValues)
	{
		$resource = $this->getRootPath($file);

		$lastChar = substr(file_get_contents($resource), -1);
		$prefix = "";

		if ($lastChar != "\n" && $lastChar != "\r" && strlen($lastChar) == 1) {
			$prefix = PHP_EOL;
		}

		$this->missedValues[$file] = [];

		foreach ($diff[$file] as $missedKey) {
			$value = '';
			if ($withValues) {
				$value = $this->prepareValue($file, $missedKey);
			}

			if (! file_put_contents($resource, $prefix . $missedKey . '=' . $value, FILE_APPEND)) {
				$this->result &= false;
				$this->missedValues[$file][] = $missedKey;
			}
		}
	}


	/**
	 * Get Value
	 *
	 * @param $file
	 * @param $missedKey
	 *
	 * @return string
	 */
	protected function prepareValue($file, $missedKey)
	{
		$src = $this->getOppositeFile($file);
		$value = $this->getValue($src, $missedKey);

		if (strpos($value, ' ') !== false && strpos($value, '"') === false) {
			$value = '"' . $value . '"';
		}

		return $value;
	}


	/**
	 * Get Missed Values
	 *
	 * @return array
	 */
	public function getMissedValues()
	{
		return $this->missedValues;
	}
}

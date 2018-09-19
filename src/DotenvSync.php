<?php

namespace JustCoded\DotenvSync;

use Exception;

/**
 * Class DotenvSync
 *
 * @package JustCoded\DotenvSync
 */
class DotenvSync extends DotenvDiff
{
	/**
	 * Values
	 *
	 * @var array
	 */
	protected $values;

	/**
	 * Missed Values
	 *
	 * @var array
	 */
	protected $missedValues;


	/**
	 * Perform Check
	 *
	 * @throws Exception
	 */
	public function sync()
	{
		$this->diff();

		if ($this->isSuccess) {
			return $this;
		}

		$this->isSuccess = true;

		if (! empty($this->diffKeys[$this->master])) {
			$this->append($this->master, true);
		}

		if (! empty($this->diffKeys[$this->slave])) {
			$this->append($this->slave, false);
		}

		return $this;
	}


	/**
	 * Append
	 *
	 * @param string $file
	 * @param bool $withValues
	 */
	protected function append($file, $withValues)
	{
		$resource = $this->getRootPath($file);

		$lastChar = substr(file_get_contents($resource), -1);
		$prefix = "";

		if ($lastChar != "\n" && $lastChar != "\r" && strlen($lastChar) == 1) {
			$prefix = PHP_EOL;
		}

		$this->missedValues[$file] = [];

		foreach ($this->diffKeys[$file] as $missedKey) {
			$value = '';
			if ($withValues) {
				$value = $this->getValue($file, $missedKey);
			}

			if (! file_put_contents($resource, $prefix . $missedKey . '=' . $value, FILE_APPEND)) {
				$this->isSuccess &= false;
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
	 * @return mixed
	 */
	protected function getValue($file, $missedKey)
	{
		$src = $file == $this->master ? $this->slave : $this->master;

		$value = $this->values[$src][$missedKey];

		if (strpos($value, ' ') !== false && strpos($value, '"') === false) {
			$value = '"' . $value . '"';
		}

		return $value;
	}


	/**
	 * Parse File
	 *
	 * @param $file
	 */
	protected function parseDotenv($file)
	{
		parent::parseDotenv($file);

		foreach ($this->keys[$file] as $key) {
			$this->values[$file][$key] = getenv($key);
		}
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
		parent::prepareOutput($file, $missedKeys);

		if (empty($missedKeys)) {
			return;
		}

		if (empty($this->missedValues[$file])) {
			$this->output .= "All the missed variables were added to {$file} file" . PHP_EOL;

			return;
		}

		$this->isSuccess &= false;

		$message = "The following variables were not added to your {$file} file: " . PHP_EOL;
		foreach ($this->missedValues[$file] as $missedValue) {
			$message .= ' - ' . $missedValue . PHP_EOL;
		}

		$this->output .= $message;
	}
}

<?php

namespace JustCoded\DotenvSync\Outputs;

use JustCoded\DotenvSync\Actions\DotenvDiff;

/**
 * Class DiffOutput
 *
 * @package JustCoded\DotenvSync\Outputs
 */
class DiffOutput extends Output
{
	/**
	 * DiffOutput constructor.
	 *
	 * @param DotenvDiff $action
	 */
	public function __construct(DotenvDiff $action)
	{
		parent::__construct($action);
	}

	/**
	 * Prepare Output
	 *
	 * @return mixed|void
	 */
	protected function prepareOutput()
	{
		if ($this->action->getResult()) {
			$this->output = "Your files have no differences" . PHP_EOL;

			return;
		}

		$diff = $this->action->getDiff();

		foreach ($diff as $file => $missedKeys) {
			if (empty($missedKeys)) {
				$this->output .= "You file {$file} is already in sync with" . PHP_EOL;

				continue;
			}

			$message = "The following variables are not present in your {$file} file: " . PHP_EOL;
			foreach ($missedKeys as $diffKey) {
				$value = $this->action->getValue($this->action->getOppositeFile($file), $diffKey);
				$message .= " - {$diffKey}={$value}" . PHP_EOL;
			}

			$this->output .= $message;
		}
	}
}

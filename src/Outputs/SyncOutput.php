<?php

namespace JustCoded\DotenvSync\Outputs;

use JustCoded\DotenvSync\Actions\DotenvSync;

class SyncOutput extends Output
{
	/**
	 * Missed Values
	 *
	 * @var array
	 */
	protected $missedValues = [];


	/**
	 * SyncOutput constructor.
	 *
	 * @param DotenvSync $action
	 */
	public function __construct(DotenvSync $action)
	{
		parent::__construct($action);

		$this->missedValues = $this->action->getMissedValues();
	}


	/**
	 * Prepare Output
	 *
	 * @return mixed|void
	 */
	protected function prepareOutput()
	{
		if ($this->action->getResult()) {
			$this->output .= "Your file has been synced" . PHP_EOL;
		}

		foreach ($this->missedValues as $file => $value) {
			if (empty($this->missedValues[$file])) {
				$this->output .= "All the missed variables were added to {$file} file" . PHP_EOL;

				return;
			}

			$message = "The following variables were not added to your {$file} file: " . PHP_EOL;
			foreach ($this->missedValues[$file] as $missedValue) {
				$message .= ' - ' . $missedValue . PHP_EOL;
			}

			$this->output .= $message;
		}
	}
}
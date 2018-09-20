<?php

namespace JustCoded\DotenvSync\Outputs;

use JustCoded\DotenvSync\Actions\DotenvSync;

/**
 * Class SyncOutput
 *
 * @package JustCoded\DotenvSync\Outputs
 */
class SyncOutput extends Output
{
	/**
	 * SyncOutput constructor.
	 *
	 * @param DotenvSync $action
	 */
	public function __construct(DotenvSync $action)
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
			$this->output .= "Your file has been synced" . PHP_EOL;

			return ;
		}


		$missedValues = $this->action->getMissedValues();

		foreach ($missedValues as $file => $value) {
			if (empty($missedValues[$file])) {
				$this->output .= "All the missed variables were added to {$file} file" . PHP_EOL;

				return;
			}

			$message = "The following variables were not added to your {$file} file: " . PHP_EOL;
			foreach ($missedValues[$file] as $missedValue) {
				$message .= ' - ' . $missedValue . PHP_EOL;
			}

			$this->output .= $message;
		}
	}
}
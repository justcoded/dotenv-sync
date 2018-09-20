<?php

namespace JustCoded\DotenvSync\Outputs;

use JustCoded\DotenvSync\Actions\DotenvAction;

/**
 * Class Output
 *
 * @package JustCoded\DotenvSync\Outputs
 */
abstract class Output
{
	/**
	 * Action
	 *
	 * @var DotenvAction
	 */
	protected $action;

	/**
	 * Output
	 *
	 * @var string
	 */
	protected $output = '';

	/**
	 * Exit Code
	 *
	 * @var int
	 */
	protected $exitCode;


	/**
	 * Output constructor.
	 *
	 * @param DotenvAction $action
	 */
	public function __construct(DotenvAction $action)
	{
		$this->action = $action;
	}

	/**
	 * Get Output
	 *
	 * @return string
	 */
	public function getOutput()
	{
		if (!$this->output) {
			$this->prepareOutput();
		}

		return $this->output;
	}


	/**
	 * Prepare Output
	 *
	 * @return mixed
	 */
	protected abstract function prepareOutput();


	/**
	 * Get Exit Code
	 *
	 * @return int
	 */
	public function getExitCode()
	{
		if (!$this->exitCode) {
			$this->exitCode = (int) !$this->action->getResult();
		}

		return $this->exitCode;
	}
}
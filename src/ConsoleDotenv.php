<?php

namespace JustCoded\DotenvSync;

use JustCoded\DotenvSync\Outputs\Output;
use JustCoded\DotenvSync\Actions\DotenvAction;
use JustCoded\DotenvSync\Outputs\Factory as OutputFactory;
use JustCoded\DotenvSync\Actions\Factory as ActionFactory;

class ConsoleDotenv
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
	 * @var Output
	 */
	protected $output;

	/**
	 * ConsoleDotenv constructor.
	 *
	 * @param $action
	 * @param string $env
	 * @param string $envExample
	 */
	public function __construct($action, $env = DotenvAction::ENV, $envExample = DotenvAction::ENV_EXAMPLE)
	{
		$this->action = ActionFactory::make($action, $env, $envExample);
		$this->output = OutputFactory::make($this->action);
	}

	/**
	 * Execute
	 *
	 * @return $this
	 */
	public function execute()
	{
		$this->action->execute();

		return $this;
	}

	/**
	 * Output
	 *
	 * @return string
	 */
	public function output()
	{
		return $this->output->getOutput();
	}

	/**
	 * Get Exit Code
	 *
	 * @return int
	 */
	public function getExitCode()
	{
		return $this->output->getExitCode();
	}
}


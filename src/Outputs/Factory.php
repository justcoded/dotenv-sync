<?php

namespace JustCoded\DotenvSync\Outputs;

use JustCoded\DotenvSync\Actions\DotenvSync;
use JustCoded\DotenvSync\Actions\DotenvDiff;
use JustCoded\DotenvSync\Actions\DotenvAction;

/**
 * Class Factory
 *
 * @package JustCoded\DotenvSync\Outputs
 */
class Factory
{
	/**
	 * Make
	 *
	 * @param DotenvAction $action
	 *
	 * @return DiffOutput|SyncOutput
	 */
	public static function make(DotenvAction $action)
	{
		if ($action instanceof DotenvDiff) {
			return new DiffOutput($action);
		}

		if ($action instanceof DotenvSync) {
			return new SyncOutput($action);
		}

		$className = get_class($action);

		throw new \InvalidArgumentException("Output for action {$className} does not exists.");
	}
}
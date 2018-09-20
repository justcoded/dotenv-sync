<?php

namespace JustCoded\DotenvSync\Actions;

class Factory
{
	/**
	 * Make
	 *
	 * @param $action
	 * @param $env
	 * @param $envExample
	 *
	 * @return mixed
	 */
	public static function make($action, $env, $envExample)
	{
		if (empty(self::actions()[$action])) {
			throw new \InvalidArgumentException("Class for action {$action} was not found.");
		}

		$className = self::actions()[$action];

		return new $className($env, $envExample);
	}


	/**
	 * Get Map
	 *
	 * @return array
	 */
	protected static function actions()
	{
		return [
			DotenvAction::ACTION_DIFF => "\\JustCoded\\DotenvSync\\Actions\\DotenvDiff",
			DotenvAction::ACTION_SYNC => "\\JustCoded\\DotenvSync\\Actions\\DotenvSync",
		];
	}
}
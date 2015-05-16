<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @author Nico Ziegler <https://github.com/gruenerPetz>
 * @package cs
 * @license LGPL-3.0+
 */

namespace ContaoSports;

use Database;
use Model\Collection;

class CsAthleteModel extends \Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected static $strTable = 'tl_cs_athlete';


	public static function findByTeamId($strTeamId)
	{
		/* @var $objResult \Contao\Database\Mysql\Result */
		$objResult = Database::getInstance()->prepare(
			'SELECT *  FROM tl_cs_athlete AS a '.
			'WHERE a.pid = ? '.
			'ORDER BY a.number ASC, a.lastName'
		)->execute($strTeamId);

		return Collection::createFromDbResult($objResult, static::$strTable);
	}
}
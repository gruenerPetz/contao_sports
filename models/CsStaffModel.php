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

use Model;
use Model\Collection;
use Database;

class CsStaffModel extends Model
{
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_cs_staff';


	public static function findByTeamIdAndPosition($strTeamId, $strPosition)
	{
		/* @var $objResult \Contao\Database\Mysql\Result */
		$objResult = Database::getInstance()->prepare('SELECT * FROM tl_cs_staff WHERE pid = ? AND position = ? ORDER BY lastName')
			->execute($strTeamId, $strPosition);

		return Collection::createFromDbResult($objResult, static::$strTable);
	}
}
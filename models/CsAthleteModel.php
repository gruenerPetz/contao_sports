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

class CsAthleteModel extends \Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected static $strTable = 'tl_cs_athlete';


	public static function findByTeamId($strTeamId, $intLimit=0, $intOffset=0, array $arrOptions=array())
	{
		/* @var $objResult \Contao\Database\Mysql\Result */
		$objResult = \Database::getInstance()->prepare(
			'SELECT *  FROM tl_cs_athlete AS a '.
			'WHERE a.pid = ? '.
			'ORDER BY a.number ASC, a.lastName'
		)->execute($strTeamId);

		return \Model\Collection::createFromDbResult($objResult, static::$strTable);
	}
}
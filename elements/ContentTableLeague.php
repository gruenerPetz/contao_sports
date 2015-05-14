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

class ContentTableLeague extends ContentTable
{
	/**
	 * @return \Model\Collection
	 */
	protected function getEventsCollection()
	{
		/* @var $objResult \Contao\Database\Mysql\Result */
		$objResult = \Database::getInstance()->prepare('
			SELECT ce.*, t1.name AS team_a_name, t2.name AS team_b_name FROM tl_cs_calendar AS c
			LEFT JOIN tl_cs_calendar_events AS ce ON c.id = ce.pid
			LEFT JOIN tl_cs_team AS t1 ON ce.team_a = t1.id
			LEFT JOIN tl_cs_team AS t2 ON ce.team_b = t2.id
			WHERE c.league = ? AND c.year = ?
		')->execute($this->cs_table_league, $this->cs_table_year);

		return \Model\Collection::createFromDbResult($objResult, 'tl_cs_calendar_events');
	}
}

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

use Contao\Database;
use Contao\Model\Collection;

class ContentEventsLeague extends ContentEvents
{
	/**
	 * @return \Contao\Model\Collection
	 */
	protected function getEventsCollection()
	{
		/* @var $objResult \Contao\Database\Mysql\Result */
		$objResult = Database::getInstance()->prepare('
			SELECT ce.* FROM tl_cs_calendar AS c
			LEFT JOIN tl_cs_calendar_events AS ce ON c.id = ce.pid
			WHERE ce.published=1 AND c.league = ? AND c.year = ?
			ORDER BY ce.startTime ASC
		')->execute($this->cs_league, $this->cs_year);

		return Collection::createFromDbResult($objResult, 'tl_cs_calendar_events');
	}
}

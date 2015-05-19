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

class ContentEventsCalendar extends ContentEvents
{
	/**
	 * @return \Contao\Model\Collection
	 */
	protected function getEventsCollection()
	{
		return CsCalendarEventsModel::findBy(array('pid=? AND published=1'), $this->cs_calendar, array(
			'order' => 'startTime ASC'
		));
	}
}

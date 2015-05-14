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

use Contao\Model\Collection;

class ContentCalendar extends \ContentElement
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_cs_calendar';


	/**
	 * Generate the content element
	 */
	protected function compile()
	{
		$objEvents= \CsCalendarEventsModel::findBy('pid', $this->cs_calendar, array(
			'order' => ($this->cs_calendar_sorting === 'startTime_desc') ? 'startTime DESC' : 'startTime ASC'
		));

		$arrResult = array();

		while ($objEvents->next())
		{
			$arrResult[] = $this->parseEvent($objEvents);
		}

		$this->Template->events = $arrResult;
	}


	/**
	 * @param Collection $objEvents
	 * @return array
	 */
	protected function parseEvent(Collection $objEvents)
	{
		$arrEvent = array(
			'title' => $objEvents->title,
			'featured' => (bool) $objEvents->featured,
			'startDate' => \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $objEvents->startDate),
			'startTime' => \Date::parse($GLOBALS['TL_CONFIG']['timeFormat'], $objEvents->startTime),
			'location' => $objEvents->location,
			'teamA' => $this->parseTeamByPk($objEvents->team_a),
			'teamB' => $this->parseTeamByPk($objEvents->team_b),
			'resultTeamA' => $objEvents->result_team_a,
			'resultTeamB' => $objEvents->result_team_b,
			'finish' => (bool) $objEvents->finish,
		);

		return $arrEvent;
	}


	protected function parseTeamByPk($varPk)
	{
		$objTeam = \CsTeamModel::findByPk($varPk);

		$arrResult = array(
			'name' => $objTeam->name,
			'city' => $objTeam->city,
			'country' => $objTeam->country,
			'singleSRC' => NULL
		);

		if ($objTeam->singleSRC)
		{
			$objModel = \FilesModel::findByUuid($objTeam->singleSRC);

			if (is_file(TL_ROOT . '/' . $objModel->path))
			{
				$arrSize = deserialize($this->size);
				$arrResult['src'] = \Image::get($objModel->path, $arrSize[0], $arrSize[1], $arrSize[2]);
			}

		}

		return $arrResult;
	}
}

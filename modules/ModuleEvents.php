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

class ModuleEvents extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_cs_events_list';


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$objEvents= \CsCalendarEventsModel::findBy(array('pid=? AND published=1'), $this->cs_calendar, array(
			'order' => 'startTime ASC'
		));

		$arrResult = array();

		if ($objEvents)
		{
			$intHeaderCount = 0;

			while ($objEvents->next())
			{
				$arrResult[] = $this->parseEvent($objEvents, (($intHeaderCount%2) === 0) ? 'even' : 'odd');
				$intHeaderCount++;
			}
		}

		$this->Template->events = $arrResult;
	}


	/**
	 * @param Collection $objEvents
	 * @return array
	 */
	protected function parseEvent(Collection $objEvents, $strClass)
	{
		$arrEvent = array(
			'title' => $objEvents->title,
			'class' => ($objEvents->color !== '') ? $objEvents->color . ' ' . $strClass : $strClass,
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
				$arrResult['src'] = \Image::get($objModel->path, 40, 40, 'center center');
			}

		}

		return $arrResult;
	}
}

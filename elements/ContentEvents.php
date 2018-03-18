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

use Contao\ContentElement;
use Contao\Model\Collection;

abstract class ContentEvents extends ContentElement
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_cs_events_list';


	/**
	 * @return \Contao\Model\Collection
	 */
	abstract protected function getEventsCollection();


	/**
	 * Generate the module
	 */
	protected function compile()
	{
        $arrResult = array();

        $objGroupsCollection = CsCalendarGroupsModel::findBy(array('pid=?'), $this->cs_calendar, array(
            'order' => 'title ASC'
        ));

        $objEventsCollection = $this->getEventsCollection();

        if ($objGroupsCollection && $objEventsCollection)
        {
            while ($objGroupsCollection->next())
            {
                $arrEvents = [];

                $intHeaderCount = 0;

                while ($objEventsCollection->next())
                {
                    if (in_array($objGroupsCollection->id, unserialize($objEventsCollection->groups)))
                    {
                        $arrEvents[] = $this->parseEvent($objEventsCollection, (($intHeaderCount%2) === 0) ? 'even' : 'odd');
                        $intHeaderCount++;
                    }
                }

                $arrResult[] = [
                    'title' => $objGroupsCollection->title,
                    'events' => $arrEvents,
                ];

                $objEventsCollection->reset();
            }
        }

		$this->Template->groups = $arrResult;
	}


	/**
	 * @param Collection $objEventsCollection
     * @param string $strClass
	 * @return array
	 */
	protected function parseEvent(Collection $objEventsCollection, $strClass)
	{
		$arrEvent = array(
			'title' => $objEventsCollection->title,
			'class' => ($objEventsCollection->color !== '') ? $objEventsCollection->color . ' ' . $strClass : $strClass,
			'featured' => (bool) $objEventsCollection->featured,
			'startDate' => \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $objEventsCollection->startDate),
			'startTime' => \Date::parse($GLOBALS['TL_CONFIG']['timeFormat'], $objEventsCollection->startTime),
			'location' => $objEventsCollection->location,
			'teamA' => $this->parseTeamByPk($objEventsCollection->team_a),
			'teamB' => $this->parseTeamByPk($objEventsCollection->team_b),
			'resultTeamA' => $objEventsCollection->result_team_a,
			'resultTeamB' => $objEventsCollection->result_team_b,
			'finish' => (bool) $objEventsCollection->finish,
		);

		return $arrEvent;
	}


    /**
     * @param $varPk
     * @return array
     */
	protected function parseTeamByPk($varPk)
	{
		$objTeam = CsTeamModel::findByPk($varPk);

		$arrResult = array(
			'name' => $objTeam->name,
			'location' => $objTeam->location,
			'location_short' => $objTeam->location_short,
			'city' => $objTeam->city,
			'country' => $objTeam->country,
			'homepage' => $objTeam->homepage,
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

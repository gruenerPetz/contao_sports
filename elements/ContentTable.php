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

abstract class ContentTable extends ContentElement
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_cs_table';


	protected $arrTeam = array(
		'id' => NULL,
		'name' => NULL,
		'games' => 0,
		'score_made' => 0,
		'score_got' => 0,
		'score_diff' => 0,
		'points_won' => 0,
		'points_lost' => 0,
		'won' => 0,
		'lost' => 0,
		'tie' => 0
	);


	/**
	 * @return \Contao\Model\Collection
	 */
	abstract protected function getEventsCollection();


	/**
	 * Generate the content element
	 */
	protected function compile()
	{
        $arrTable = array();

        $arrTeamGroup = array();

        $objGroupsCollection = CsCalendarGroupsModel::findBy(array('pid=?'), $this->cs_calendar, array(
            'order' => 'title ASC'
        ));

        foreach ($objGroupsCollection AS $objGroupModel)
        {
            foreach (unserialize($objGroupModel->teams) AS $team)
            {
                $arrTeamGroup[$team] = $objGroupModel->id;
            }

            $arrTable[$objGroupModel->id] = [
                'title' => $objGroupModel->title,
                'table' => []
            ];
        }

		/* @var $objEventsCollection \Contao\Model\Collection */
		$objEventsCollection = $this->getEventsCollection();

		/* @var $objEventModel \ContaoSports\CsCalendarEventsModel */
		foreach($objEventsCollection AS $objEventModel)
		{
            $intIdTeamA = $objEventModel->team_a;
            $intIdTeamB = $objEventModel->team_b;

            $arrGroups = unserialize($objEventModel->groups);
            $group = $arrGroups[0];

            if (!isset($arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]))
            {
                $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA] = $this->arrTeam;
                $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['id'] = $intIdTeamA;
                $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['name'] = $objEventModel->team_a_name;
            }

            if (!isset($arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]))
            {
                $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB] = $this->arrTeam;
                $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['id'] = $intIdTeamB;
                $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['name'] = $objEventModel->team_b_name;
            }

            if ($objEventModel->finish == 1)
            {
                $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['games']++;
                $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['score_made']+=$objEventModel->result_team_a;
                $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['score_got']+=$objEventModel->result_team_b;
                $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['score_diff'] = $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['score_made'] - $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['score_got'];

                $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['games']++;
                $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['score_made']+=$objEventModel->result_team_b;
                $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['score_got']+=$objEventModel->result_team_a;
                $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['score_diff'] = $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['score_made'] - $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['score_got'];

                if ($objEventModel->result_team_a == $objEventModel->result_team_b)
                {
                    $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['tie']++;
                    $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['points_won']++;
                    $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['points_lost']++;

                    $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['tie']++;
                    $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['points_won']++;
                    $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['points_lost']++;
                }
                else if ($objEventModel->result_team_a > $objEventModel->result_team_b)
                {
                    $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['won']++;
                    $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['points_won']+=2;

                    $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['lost']++;
                    $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['points_lost']+=2;
                }
                else
                {
                    $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['lost']++;
                    $arrTable[$arrTeamGroup[$intIdTeamA]]['table'][$intIdTeamA]['points_lost']+=2;

                    $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['won']++;
                    $arrTable[$arrTeamGroup[$intIdTeamB]]['table'][$intIdTeamB]['points_won']+=2;
                }
            }
		}

        /*
         * http://stackoverflow.com/questions/3232965/sort-multidimensional-array-by-multiple-keys?answertab=votes#tab-top
         * get a list of sort columns and their data to pass to array_multisort
         */
		foreach ($arrTable AS &$arrGroupTable)
        {
            $arrSort = array(
                'won' => array(),
                'lost' => array(),
                'tie' => array(),
                'score_diff' => array(),
            );

            foreach($arrGroupTable['table'] as $k => $v)
            {
                $arrSort['won'][$k] = $v['won'];
                $arrSort['lost'][$k] = $v['lost'];
                $arrSort['tie'][$k] = $v['tie'];
                $arrSort['score_diff'][$k] = $v['score_diff'];
            }

            // sort by event_type desc and then title asc
            array_multisort(
                $arrSort['won'], SORT_DESC,
                $arrSort['lost'], SORT_ASC,
                $arrSort['tie'], SORT_DESC,
                $arrSort['score_diff'], SORT_DESC,
                $arrGroupTable['table']
            );
        }

		$this->Template->table = $arrTable;
	}
}

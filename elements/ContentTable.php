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

abstract class ContentTable extends \ContentElement
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


	abstract protected function getEventsCollection();


	/**
	 * Generate the content element
	 */
	protected function compile()
	{
		/* @var $objEventsCollection \Contao\Model\Collection */
		$objEventsCollection = $this->getEventsCollection();

		$arrTable = array();

		foreach($objEventsCollection AS $objEventModel)
		{
			$intIdTeamA = $objEventModel->team_a;
			$intIdTeamB = $objEventModel->team_b;

			if (!isset($arrTable[$intIdTeamA]))
			{
				$arrTable[$intIdTeamA] = $this->arrTeam;
				$arrTable[$intIdTeamA]['id'] = $intIdTeamA;
				$arrTable[$intIdTeamA]['name'] = $objEventModel->team_a_name;
			}

			if (!isset($arrTable[$intIdTeamB]))
			{
				$arrTable[$intIdTeamB] = $this->arrTeam;
				$arrTable[$intIdTeamB]['id'] = $intIdTeamB;
				$arrTable[$intIdTeamB]['name'] = $objEventModel->team_b_name;
			}

			if ($objEventModel->finish == 1)
			{
				$arrTable[$intIdTeamA]['games']++;
				$arrTable[$intIdTeamA]['score_made']+=$objEventModel->result_team_a;
				$arrTable[$intIdTeamA]['score_got']+=$objEventModel->result_team_b;
				$arrTable[$intIdTeamA]['score_diff'] = $arrTable[$intIdTeamA]['score_made'] - $arrTable[$intIdTeamA]['score_got'];

				$arrTable[$intIdTeamB]['games']++;
				$arrTable[$intIdTeamB]['score_made']+=$objEventModel->result_team_b;
				$arrTable[$intIdTeamB]['score_got']+=$objEventModel->result_team_a;
				$arrTable[$intIdTeamB]['score_diff'] = $arrTable[$intIdTeamB]['score_made'] - $arrTable[$intIdTeamB]['score_got'];

				if ($objEventModel->result_team_a == $objEventModel->result_team_b)
				{
					$arrTable[$intIdTeamA]['tie']++;
					$arrTable[$intIdTeamA]['points_won']++;
					$arrTable[$intIdTeamA]['points_lost']++;

					$arrTable[$intIdTeamB]['tie']++;
					$arrTable[$intIdTeamB]['points_won']++;
					$arrTable[$intIdTeamB]['points_lost']++;
				}
				else if ($objEventModel->result_team_a > $objEventModel->result_team_b)
				{
					$arrTable[$intIdTeamA]['won']++;
					$arrTable[$intIdTeamA]['points_won']+=2;

					$arrTable[$intIdTeamB]['lost']++;
					$arrTable[$intIdTeamB]['points_lost']+=2;
				}
				else
				{
					$arrTable[$intIdTeamA]['lost']++;
					$arrTable[$intIdTeamA]['points_lost']+=2;

					$arrTable[$intIdTeamB]['won']++;
					$arrTable[$intIdTeamB]['points_won']+=2;
				}
			}
		}

		/*
		 * http://stackoverflow.com/questions/3232965/sort-multidimensional-array-by-multiple-keys?answertab=votes#tab-top
		 * get a list of sort columns and their data to pass to array_multisort
		 */
		$arrSort = array();

		foreach($arrTable as $k => $v)
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
			$arrTable
		);

		$this->Template->table = $arrTable;
	}
}

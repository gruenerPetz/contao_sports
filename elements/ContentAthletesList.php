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

class ContentAthletesList extends ContentElement
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_cs_athletes_list';


	/**
	 * Generate the content element
	 */
	protected function compile()
	{
		/*
		 * JAVASCRIPT / CSS
		 */
		$GLOBALS['TL_JAVASCRIPT'][] = TL_ASSETS_URL . 'assets/jquery/tablesorter/' . $GLOBALS['TL_ASSETS']['TABLESORTER'] . '/js/tablesorter.js';
		$GLOBALS['TL_JQUERY'][] = '<script>(function($){$(document).ready(function(){$(".contao_sports .sortable").each(function(i,table){$(table).tablesorter({sortList:[[2,0]]});});});})(jQuery);</script>';

		$GLOBALS['TL_CSS'][] = 'assets/jquery/tablesorter/' . $GLOBALS['TL_ASSETS']['TABLESORTER'] . '/css/tablesorter.css|static';

		$this->Template->articles = array();

		$objAthletes = CsAthleteModel::findByTeamId($this->cs_team);

		// No items found
		if ($objAthletes === null)
		{
			$this->Template = new \FrontendTemplate('mod_newsarchive_empty');
			$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyList'];
		}
		else
		{
			$arrColumns = deserialize($this->cs_team_columns);

			$this->Template->athletes = $this->parseAthletes($objAthletes);
			$this->Template->columns = $arrColumns ? $arrColumns : array();
		}
	}


	/**
	 * Parse one or more items and return them as array
	 *
	 * @param object
	 * @param boolean
	 *
	 * @return array
	 */
	protected function parseAthletes($objAthletes)
	{
		$limit = $objAthletes->count();

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrAthletes = array();

		while ($objAthletes->next())
		{
			$arrAthletes[] = $this->parseAthlete($objAthletes, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count);
		}

		return $arrAthletes;
	}


	/**
	 * Parse an item and return it as string
	 *
	 * @param object
	 * @param string
	 * @param integer
	 *
	 * @return string
	 */
	protected function parseAthlete($objAthletes, $strClass='', $intCount=0)
	{
		global $objPage;

		$arrAthlete = $objAthletes->row();

		$objTemplate = new \FrontendTemplate('cs_athletes_table');
		$objTemplate->setData($arrAthlete);

		$arrColumns = deserialize($this->cs_team_columns);

		$objTemplate->class = (($objAthletes->cssClass != '') ? ' ' . $objAthletes->cssClass : '') . $strClass;
		$objTemplate->count = $intCount;
		$objTemplate->text = '';
		$objTemplate->columns = $arrColumns ? $arrColumns : array();

		$objTemplate->addImage = false;

		if ($objAthletes->singleSRC != '')
		{
			$objModel = \Contao\FilesModel::findByUuid($objAthletes->singleSRC);

			if (is_file(TL_ROOT . '/' . $objModel->path))
			{
				$arrAthlete['singleSRC'] = $objModel->path;
			}
		}
		else
		{
			$arrAthlete['singleSRC'] = 'system/modules/contao_sports/assets/images/athlete_no_image_raw.png';
		}

		if ($this->size != '')
		{
			$size = deserialize($this->size);

			if ($size[0] > 0 || $size[1] > 0)
			{
				$arrAthlete['size'] = $this->size;
			}
		}

		$this->addImageToTemplate($objTemplate, $arrAthlete);

		return $objTemplate->parse();
	}
}

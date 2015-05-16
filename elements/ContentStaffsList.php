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

class ContentStaffsList extends ContentElement
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_cs_staffs_list';


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$this->Template->articles = array();

		$objStaffsCollection = CsStaffModel::findByTeamIdAndPosition($this->cs_team, $this->cs_staff_position);


		// No items found
		if ($objStaffsCollection === null)
		{
			$this->Template = new \FrontendTemplate('mod_newsarchive_empty');
			$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyList'];
		}
		else
		{
			$this->Template->staffs = $this->parseStaffs($objStaffsCollection);
		}
	}


	/**
	 * Parse one or more items and return them as array
	 * @param Collection $objStaffsCollection
	 *
	 * @return array
	 */
	protected function parseStaffs(Collection $objStaffsCollection)
	{
		$limit = $objStaffsCollection->count();

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrStaffs = array();

		while ($objStaffsCollection->next())
		{
			$arrStaffs[] = $this->parseStaff($objStaffsCollection, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count);
		}

		return $arrStaffs;
	}


	/**
	 * Parse an item and return it as string
	 * @param Collection $objStaffsCollection
	 * @param string
	 * @param integer
	 *
	 * @return string
	 */
	protected function parseStaff(Collection $objStaffsCollection, $strClass = '', $intCount = 0)
	{
		$arrStaff = $objStaffsCollection->row();

		$objTemplate = new \FrontendTemplate('cs_staffs_table');
		$objTemplate->setData($arrStaff);

		$objTemplate->class = (($objStaffsCollection->cssClass != '') ? ' ' . $objStaffsCollection->cssClass : '') . $strClass;
		$objTemplate->count = $intCount; // see #5708
		$objTemplate->text = '';

		$objTemplate->addImage = false;

		if ($objStaffsCollection->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objStaffsCollection->singleSRC);

			if (is_file(TL_ROOT . '/' . $objModel->path))
			{
				$arrStaff['singleSRC'] = $objModel->path;
			}
		}
		else
		{
			$arrStaff['singleSRC'] = 'system/modules/contao_sports/assets/images/athlete_no_image_raw.png';
		}

		if ($this->imgSize != '')
		{
			$size = deserialize($this->imgSize);

			if ($size[0] > 0 || $size[1] > 0)
			{
				$arrStaff['size'] = $this->imgSize;
			}
		}

		$this->addImageToTemplate($objTemplate, $arrStaff);

		return $objTemplate->parse();
	}
}

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

class ModuleStaffsList extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_cs_staffs_list';


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$this->Template->articles = array();

		$objStaffs= \CsStaffModel::findByTeamIdAndPosition($this->cs_team, $this->cs_staff_position);

		// No items found
		if ($objStaffs === null)
		{
			$this->Template = new \FrontendTemplate('mod_newsarchive_empty');
			$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyList'];
		}
		else
		{
			$this->Template->staffs = $this->parseStaffs($objStaffs);
		}
	}


	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parseStaffs($objStaffs)
	{
		$limit = $objStaffs->count();

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrStaffs = array();

		while ($objStaffs->next())
		{
			$arrStaffs[] = $this->parseStaff($objStaffs, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count);
		}

		return $arrStaffs;
	}


	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param string
	 * @param integer
	 * @return string
	 */
	protected function parseStaff($objStaffs, $strClass='', $intCount=0)
	{
		global $objPage;

		$arrStaff = $objStaffs->row();

		$objTemplate = new \FrontendTemplate('cs_staffs_table');
		$objTemplate->setData($arrStaff);

		$objTemplate->class = (($objStaffs->cssClass != '') ? ' ' . $objStaffs->cssClass : '') . $strClass;
//		$objTemplate->link = $this->generateNewsUrl($objArticle, $blnAddArchive);
		$objTemplate->count = $intCount; // see #5708
		$objTemplate->text = '';

		$objTemplate->addImage = false;

		if ($objStaffs->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objStaffs->singleSRC);

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

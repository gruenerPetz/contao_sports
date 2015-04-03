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

class ModuleGflTableJugend extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_cs_gfl_table_jugend';


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$objXml = simplexml_load_file('http://vereine.football-verband.de/xmltabelle.php5?Liga=GFLJ');

		$arrResult = array(
			'middle' => array(),
			'north' => array(),
			'south' => array(),
			'west' => array(),
		);

		foreach ($objXml AS $objItem)
		{
			switch($objItem->Gruppe)
			{
				case 'Nord':
					$strGroup = 'north';
					break;
				case 'Mitte':
					$strGroup = 'middle';
					break;
				case 'West':
					$strGroup = 'west';
					break;
				default:
					$strGroup = 'south';
			}

			$strSrc = NULL;

			if (is_file(TL_ROOT . '/files/team-logos/' . strtolower($objItem->Kuerzel) . '.png'))
			{
				$strSrc = \Image::get('/files/team-logos/' . strtolower($objItem->Kuerzel) . '.png', 40, 40, 'center center');
			}

			$arrResult[$strGroup][] = array(
				'pos' => (int) $objItem->Platz,
				'kurz' => (string) $objItem->Kuerzel,
				'src' => $strSrc,
				'mnrx' => ($objItem->Kuerzel == 'DM'),
				'name' => (string) $objItem->Team,
				'pPlus' => (int) $objItem->PPlus,
				'pMinus' => (int) $objItem->PMinus,
				'tdPlus' => (int) $objItem->TDPlus,
				'tdMinus' => (int) $objItem->TDMinus
			);


		}

		$this->Template->results = $arrResult;
	}
}

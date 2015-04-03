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

class ModuleGflShortTable extends \Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_cs_gfl_short_table';


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$objXml = simplexml_load_file('http://vereine.football-verband.de/xmltabelle.php5?Liga=GFL&Gruppe=Nord');

		$result = array();

		$monarchsPos = 0;

		foreach ($objXml AS $item)
		{
			$result[] = array(
				'pos' => (int)$item->Platz,
				'mnrx' => ($item->Kuerzel == 'DM'),
				'name' => (string)$item->Team,
				'pPlus' => (int)$item->PPlus,
				'pMinus' => (int)$item->PMinus
			);


			if ($item->Kuerzel == 'DM')
			{
				$monarchsPos = $item->Platz - 3;
			}
		}

		$start = ($monarchsPos - 1) < 0 ? 0 : $monarchsPos - 1;

		if ($start > 3)
		{
			$start = 3;
		}

		$this->Template->results = array_slice($result, $start, 4);
	}
}

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

class ModuleGflTable extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_cs_gfl_table';


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$objXml = simplexml_load_file('http://vereine.football-verband.de/xmltabelle.php5?Liga=GFL');

		$result = array(
			'north' => array(),
			'south' => array()
		);

		foreach ($objXml AS $item)
		{
			$group = ($item->Gruppe == 'Nord') ? 'north' : 'south';

			$src = NULL;

			if (is_file(TL_ROOT . '/files/team-logos/' . strtolower($item->Kuerzel) . '.png'))
			{
				$src = \Image::get('/files/team-logos/' . strtolower($item->Kuerzel) . '.png', 40, 40, 'center center');
			}

			$result[$group][] = array(
				'pos' => (int) $item->Platz,
				'kurz' => (string) $item->Kuerzel,
				'src' => $src,
				'mnrx' => ($item->Kuerzel == 'DM'),
				'name' => (string) $item->Team,
				'pPlus' => (int) $item->PPlus,
				'pMinus' => (int) $item->PMinus,
				'tdPlus' => (int) $item->TDPlus,
				'tdMinus' => (int) $item->TDMinus
			);


		}

		$this->Template->results = $result;
	}
}

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

/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['athletes_list']    = '{title_legend},name,headline,type;{config_legend},cs_team,cs_team_columns;{config_legend},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['staffs_list']    	= '{title_legend},name,headline,type;{config_legend},cs_team, cs_staff_position;{config_legend},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['events_list']		= '{title_legend},name,headline,type;{config_legend},cs_calendar;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['cs_team'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['cs_team'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_contao_sports', 'getTeams'),
	'eval'                    => array('mandatory'=>true),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['cs_team_columns'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['cs_team_columns'],
	'inputType'               => 'checkbox',
	'options'                 => array('bodyWeight','bodyHeight'),
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['cs_staff_position'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['cs_staff_position'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('headcoach', 'coach', 'physio', 'equipment'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['cs_staff_position'],
	'sql'                     => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['cs_calendar'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['cs_calendar'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'foreignKey'              => 'tl_cs_calendar.title',
	'relation'                => array('type'=>'hasOne', 'load'=>'lazy'),
	'eval'                    => array('mandatory'=>true),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

class tl_module_contao_sports extends Backend
{
	/**
	 * @return array
	 */
	public function getTeams()
	{
		$arrTeams = array();
		$objTeams = $this->Database->execute('SELECT t.id, t.name FROM tl_cs_team t');

		while ($objTeams->next())
		{
			$arrTeams[$objTeams->id] = $objTeams->name . ' (ID ' . $objTeams->id . ')';
		}

		return $arrTeams;
	}
}
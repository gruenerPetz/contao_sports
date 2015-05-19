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
$GLOBALS['TL_DCA']['tl_content']['palettes']['cs_athletes_list']   = '{type_legend},type,headline;{cs_athletes_legend},cs_team,cs_team_columns;{image_legend},size;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['cs_events_calendar'] = '{type_legend},type,headline;{cs_events_legend},cs_calendar;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['cs_events_league']   = '{type_legend},type,headline;{cs_events_legend},cs_league,cs_year;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['cs_staffs_list']     = '{type_legend},type,headline;{cs_staffs_legend},cs_team,cs_staff_position;{image_legend},size;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['cs_table_calendar']  = '{type_legend},type,headline;{cs_calendar_legend},cs_calendar;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['cs_table_league']    = '{type_legend},type,headline;{cs_league_legend},cs_league,cs_league;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';


/**
 * Add fields to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['cs_team'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['cs_team'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_content_contao_sports', 'getTeams'),
	'eval'                    => array('mandatory'=>true),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['cs_team_columns'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['cs_team_columns'],
	'inputType'               => 'checkbox',
	'options'                 => array('bodyWeight','bodyHeight'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_content']['cs_team_columns'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['cs_staff_position'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['cs_staff_position'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('headcoach', 'coach', 'physio', 'equipment'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_content']['cs_staff_position'],
	'sql'                     => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['cs_calendar'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['cs_calendar'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'foreignKey'              => 'tl_cs_calendar.title',
	'relation'                => array('type'=>'hasOne', 'load'=>'lazy'),
	'eval'                    => array('mandatory'=>true),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['cs_league'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['cs_league'],
	'inputType'               => 'select',
	'foreignKey'              => 'tl_cs_league.name',
	'sql'                     => "int(10) unsigned NOT NULL default '0'",
	'eval'                    => array('mandatory'=>true),
);

$GLOBALS['TL_DCA']['tl_content']['fields']['cs_year'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['cs_year'],
	'default'                 => date('Y'),
	'filter'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength' => 4, 'minlength' => 4, 'rgxp'=>'digit', 'mandatory'=>true, 'doNotCopy'=>true, 'tl_class'=>'w50'),
	'sql'                     => "int(4) unsigned NULL"
);

class tl_content_contao_sports extends Backend
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
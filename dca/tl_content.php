<?php

$GLOBALS['TL_DCA']['tl_content']['palettes']['cs_table_league'] = '{type_legend},type,headline;{cs_league_legend},cs_table_league,cs_table_year;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['cs_table_calender'] = '{type_legend},type,headline;{cs_calendar_legend},cs_table_calendar;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['cs_table_league'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['author'],
	'inputType'               => 'select',
	'foreignKey'              => 'tl_cs_league.name',
	'sql'                     => "int(10) unsigned NOT NULL default '0'",
//	'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
);

$GLOBALS['TL_DCA']['tl_content']['fields']['cs_table_calendar'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['author'],
	'inputType'               => 'select',
	'foreignKey'              => 'tl_cs_calendar.title',
	'sql'                     => "int(10) unsigned NOT NULL default '0'",
//	'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
);

$GLOBALS['TL_DCA']['tl_content']['fields']['cs_table_year'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar']['year'],
	'default'                 => date('Y'),
	'filter'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength' => 4, 'minlength' => 4, 'rgxp'=>'digit', 'mandatory'=>true, 'doNotCopy'=>true, 'tl_class'=>'w50'),
	'sql'                     => "int(4) unsigned NULL"
);
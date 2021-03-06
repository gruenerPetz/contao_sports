<?php

/**
 * Content elements
 */
$GLOBALS['TL_CTE']['contao_sports']['cs_athletes_list'] = 'ContaoSports\ContentAthletesList';
$GLOBALS['TL_CTE']['contao_sports']['cs_events_calendar'] = 'ContaoSports\ContentEventsCalendar';
$GLOBALS['TL_CTE']['contao_sports']['cs_events_league'] = 'ContaoSports\ContentEventsLeague';
$GLOBALS['TL_CTE']['contao_sports']['cs_staffs_list'] = 'ContaoSports\ContentStaffsList';
$GLOBALS['TL_CTE']['contao_sports']['cs_table_calendar'] = 'ContaoSports\ContentTableCalendar';
$GLOBALS['TL_CTE']['contao_sports']['cs_table_league'] = 'ContaoSports\ContentTableLeague';

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['contao_sports'] = array
(
	'cs_leagues' => array
	(
		'tables'     => array('tl_cs_league'),
		'icon'       => 'system/modules/contao_sports/assets/icons/league.png',
	),
	'cs_teams' => array
	(
		'tables'     => array('tl_cs_team', 'tl_cs_athlete', 'tl_cs_staff'),
		'icon'       => 'system/modules/contao_sports/assets/icons/team.png',
		'stylesheet' => 'system/modules/contao_sports/assets/style.css'
	),
	'cs_calendar' => array
	(
		'tables'     => array('tl_cs_calendar', 'tl_cs_calendar_events', 'tl_cs_calendar_groups'),
		'icon'       => 'system/modules/contao_sports/assets/icons/calendar.png',
	),
);

/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'cs_leagues';
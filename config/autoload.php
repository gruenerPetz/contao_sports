<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'ContaoSports',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Elements
	'ContaoSports\ContentAthletesList'   => 'system/modules/contao_sports/elements/ContentAthletesList.php',
	'ContaoSports\ContentEvents'         => 'system/modules/contao_sports/elements/ContentEvents.php',
	'ContaoSports\ContentStaffsList'     => 'system/modules/contao_sports/elements/ContentStaffsList.php',
	'ContaoSports\ContentTable'          => 'system/modules/contao_sports/elements/ContentTable.php',
	'ContaoSports\ContentTableCalendar'  => 'system/modules/contao_sports/elements/ContentTableCalendar.php',
	'ContaoSports\ContentTableLeague'    => 'system/modules/contao_sports/elements/ContentTableLeague.php',

	// Models
	'ContaoSports\CsAthleteModel'        => 'system/modules/contao_sports/models/CsAthleteModel.php',
	'ContaoSports\CsCalendarEventsModel' => 'system/modules/contao_sports/models/CsCalendarEventsModel.php',
	'ContaoSports\CsCalendarModel'       => 'system/modules/contao_sports/models/CsCalendarModel.php',
	'ContaoSports\CsStaffModel'          => 'system/modules/contao_sports/models/CsStaffModel.php',
	'ContaoSports\CsTeamModel'           => 'system/modules/contao_sports/models/CsTeamModel.php',

	// Modules
	'ContaoSports\ModuleAthletesList'    => 'system/modules/contao_sports/modules/ModuleAthletesList.php',
	'ContaoSports\ModuleEvents'          => 'system/modules/contao_sports/modules/ModuleEvents.php',
	'ContaoSports\ModuleStaffsList'      => 'system/modules/contao_sports/modules/ModuleStaffsList.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'cs_athletes_table'       => 'system/modules/contao_sports/templates/athletes',
	'ce_cs_athletes_list'     => 'system/modules/contao_sports/templates/elements',
	'ce_cs_calendar'          => 'system/modules/contao_sports/templates/elements',
	'ce_cs_events_list'       => 'system/modules/contao_sports/templates/elements',
	'ce_cs_staffs_list'       => 'system/modules/contao_sports/templates/elements',
	'ce_cs_table'             => 'system/modules/contao_sports/templates/elements',
	'j_cs_tablesort'          => 'system/modules/contao_sports/templates/jquery',
	'mod_cs_athletes_list'    => 'system/modules/contao_sports/templates/modules',
	'mod_cs_events_list'      => 'system/modules/contao_sports/templates/modules',
	'mod_cs_gfl_short_table'  => 'system/modules/contao_sports/templates/modules',
	'mod_cs_gfl_table'        => 'system/modules/contao_sports/templates/modules',
	'mod_cs_gfl_table_jugend' => 'system/modules/contao_sports/templates/modules',
	'mod_cs_staffs_list'      => 'system/modules/contao_sports/templates/modules',
	'cs_staffs_table'         => 'system/modules/contao_sports/templates/staffs',
));

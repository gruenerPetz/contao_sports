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
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_user']['palettes']['extend'] = str_replace('fop;', 'fop;{contao_sports},cs_leagues;', $GLOBALS['TL_DCA']['tl_user']['palettes']['extend']);
$GLOBALS['TL_DCA']['tl_user']['palettes']['custom'] = str_replace('fop;', 'fop;{contao_sports},cs_leagues;', $GLOBALS['TL_DCA']['tl_user']['palettes']['custom']);


/**
 * Add fields to tl_user
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['cs_leagues'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['cs_leagues'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_cs_league.name',
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);
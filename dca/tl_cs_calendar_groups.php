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

$GLOBALS['TL_DCA']['tl_cs_calendar_groups'] = array
(
	'config' => array
	(
		'dataContainer'               => 'Table',
        'ptable'                      => 'tl_cs_calendar',
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
                'pid' => 'index'
			)
		)
	),
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 0,
			'fields'                  => array('title'),
            'flag'                    => 2,
            'panelLayout'             => 'filter;sort,search',
		),
        'label' => array
        (
            'fields'                  => array('title'),
            'format'                  => '%s',
        ),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cs_calendar']['edit'],
                'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cs_calendar']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
		)
	),
	'palettes' => array
	(
		'default'                     => '{author_legend},title,teams'
	),
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
        'pid' => array
        (
            'foreignKey'              => 'tl_cs_calendar.title',
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
        ),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_groups']['title'],
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''",
		),
        'teams' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_groups']['teams'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'options_callback'        => array('tl_cs_calendar_groups', 'getTeams'),
//            'foreignKey'              => 'tl_cs_team.name',
            'eval'                    => array('multiple'=>true),
            'sql'                     => "blob NULL",
            'relation'                => array('type'=>'belongsToMany', 'load'=>'lazy')
        ),
	),
);

class tl_cs_calendar_groups extends Backend
{
    public function getTeams()
    {
        $arrTeams = array();
        $arrLeagues = array();
        if (!is_array($this->User->cs_leagues) || empty($this->User->cs_leagues))
        {
            /* @var $objTeams \Contao\Database\Mysqli\Result */
            $objTeams = $this->Database->prepare('SELECT id, name, league FROM tl_cs_team ORDER BY name')->execute();
        }
        else
        {
            /* @var $objTeams \Contao\Database\Mysqli\Result */
            $objTeams = $this->Database->prepare('SELECT id, name, league FROM tl_cs_team WHERE league IN ('.implode(',', $this->User->cs_leagues).' ORDER BY name)')->execute();
        }
        $objLeagues = $this->Database->prepare('SELECT id, name FROM tl_cs_league')->execute();
        foreach ($objLeagues->fetchAllAssoc() AS $objLeagues)
        {
            $arrLeagues[$objLeagues['id']] = $objLeagues['name'];
        }
        foreach ($objTeams->fetchAllAssoc() AS $objTeams)
        {
            $arrTeams[$objTeams['id']] = $objTeams['name']." - ".$arrLeagues[$objTeams['league']];
        }
        return $arrTeams;
    }
}

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

$GLOBALS['TL_DCA']['tl_cs_calendar'] = array
(
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ctable'                      => array('tl_cs_calendar_events'),
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'onload_callback' => array
		(
			array('tl_cs_calendar', 'checkPermission'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
			)
		)
	),
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('year'),
			'flag'                    => 12,
			'panelLayout'             => 'filter;search,limit',
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
				'href'                => 'table=tl_cs_calendar_events',
				'icon'                => 'edit.gif',
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cs_calendar']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif',
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

	// Palettes
	'palettes' => array
	(
//		'__selector__'                => array('addReply'),
		'default'                     => '{author_legend},title,alias,author;{game_legend}, league, year'
	),

//	// Subpalettes
//	'subpalettes' => array
//	(
//		'addReply'                    => 'author,reply'
//	),
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar']['title'],
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''",
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar']['alias'],
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'alias', 'unique'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'save_callback' => array
			(
				array('tl_cs_calendar', 'generateAlias')
			),
			'sql'                     => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
		),
		'author' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar']['author'],
			'default'                 => BackendUser::getInstance()->id,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'select',
			'foreignKey'              => 'tl_user.name',
			'eval'                    => array('doNotCopy'=>true, 'chosen'=>true, 'mandatory'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'hasOne', 'load'=>'eager')
		),
		'league' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar']['league'],
			'filter'                  => true,
			'search'                  => true,
			'sorting'                 => true,
			'options_callback'        => array('tl_cs_calendar', 'getLeagues'),
			'inputType'               => 'select',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'year' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar']['year'],
			'default'                 => date('Y'),
			'filter'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength' => 4, 'minlength' => 4, 'rgxp'=>'digit', 'mandatory'=>true, 'doNotCopy'=>true, 'tl_class'=>'w50'),
			'sql'                     => "int(4) unsigned NULL"
		),
	),
);

class tl_cs_calendar extends Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}

		$root = array(0);

		// Set root IDs
		if (is_array($this->User->cs_leagues) && !empty($this->User->cs_leagues))
		{
			/* @var $objCalendar \Contao\Database\Mysqli\Result */
			$objCalendar = $this->Database->prepare('SELECT id FROM tl_cs_calendar WHERE league IN ('.implode(',', $this->User->cs_leagues).')')->execute();

			foreach ($objCalendar->fetchAllAssoc() AS $arrCalendar)
			{
				$root[] = $arrCalendar['id'];
			}
		}

		$GLOBALS['TL_DCA']['tl_cs_calendar']['list']['sorting']['root'] = $root;
	}


	/**
	 * Auto-generate the calendar alias if it has not been set yet
	 * @param mixed
	 * @param \DataContainer
	 * @return mixed
	 * @throws \Exception
	 */
	public function generateAlias($varValue, DataContainer $dc)
	{
		$autoAlias = false;

		// Generate alias if there is none
		if ($varValue == '')
		{
			$autoAlias = true;
			$varValue = standardize(String::restoreBasicEntities($dc->activeRecord->title));
		}

		$objAlias = $this->Database->prepare("SELECT id FROM tl_cs_calendar WHERE alias=?")
			->execute($varValue);

		// Check whether the alias exists
		if ($objAlias->numRows > 1 && !$autoAlias)
		{
			throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
		}

		// Add ID to alias
		if ($objAlias->numRows && $autoAlias)
		{
			$varValue .= '-' . $dc->id;
		}

		return $varValue;
	}


	public function getLeagues()
	{
		$arrLeagues = array();

		if (!is_array($this->User->cs_leagues) || empty($this->User->cs_leagues))
		{
			/* @var $objLeagues \Contao\Database\Mysqli\Result */
			$objLeagues = $this->Database->prepare('SELECT id, name FROM tl_cs_league')
				->execute();
		}
		else
		{
			/* @var $objLeagues \Contao\Database\Mysqli\Result */
			$objLeagues = $this->Database->prepare('SELECT id, name FROM tl_cs_league WHERE id IN ('.implode(',', $this->User->cs_leagues).')')->execute();
		}

		foreach ($objLeagues->fetchAllAssoc() AS $arrLeague)
		{
			$arrLeagues[$arrLeague['id']] = $arrLeague['name'];
		}

		return $arrLeagues;
	}
}
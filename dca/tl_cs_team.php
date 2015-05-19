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

$GLOBALS['TL_DCA']['tl_cs_team'] = array
(
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ctable'                      => array(
			'tl_cs_athlete',
			'tl_cs_staff',

		),
		'enableVersioning'            => true,
		'onload_callback' => array
		(
			array('tl_cs_team', 'checkPermission'),
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
			'mode'                    => 2,
			'fields'                  => array('name'),
			'flag'                    => 1,
			'panelLayout'             => 'filter;sort,search,limit',
		),
		'label' => array
		(
			'fields'                  => array('team_image', 'name', 'city', 'league:tl_cs_league.name'),
			'showColumns'             => true,
			'label_callback'          => array('tl_cs_team', 'getLabel')
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
			'editAthletes' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cs_team']['editAthletes'],
				'href'                => 'table=tl_cs_athlete',
				'icon'                => 'system/modules/contao_sports/assets/icons/athlete.png',
			),
			'editStaffs' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cs_team']['editStaffs'],
				'href'                => 'table=tl_cs_staff',
				'icon'                => 'system/modules/contao_sports/assets/icons/staff.png',
			),
			'editTeam' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cs_team']['editTeam'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cs_team']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{team_legend},name,league,city,country,homepage,info,singleSRC'
	),

	// Fields
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
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_team']['name'],
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'league' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_team']['league'],
			'filter'                  => true,
			'search'                  => true,
			'sorting'                 => true,
			'options_callback'        => array('tl_cs_team', 'getLeagues'),
			'inputType'               => 'select',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'city' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_team']['city'],
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'country' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_team']['country'],
			'filter'                  => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'homepage' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_team']['homepage'],
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'url', 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'info' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_team']['info'],
			'inputType'               => 'textarea',
			'sql'                     => "text NULL",
			'eval'                    => array('rte'=>'tinyMCE'),
		),
		'singleSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_team']['singleSRC'],
			'inputType'               => 'fileTree',
			'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio'),
			'sql'                     => "binary(16) NULL",
		),
	),
);

class tl_cs_team extends Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	public function getLabel($arrRow, $label, DataContainer $dc, $args)
	{
		$strImageURL = 'system/modules/contao_sports/assets/images/team_no_image.png';

		if ($arrRow['singleSRC'])
		{
			$objFile = FilesModel::findByUuid($arrRow['singleSRC']);

			if ($objFile !== null)
			{
				$strImageURL = TL_FILES_URL . Image::get($objFile->path, 40, 40, 'center_center');
			}
		}

		$args[0] = '<img src="'.$strImageURL.'" width="40" height="40" title="'.$arrRow['name'].'" alt="'.$arrRow['name'].'">';
		return $args;

	}


	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}

		// Set root IDs
		if (!is_array($this->User->cs_leagues) || empty($this->User->cs_leagues))
		{
			$root = array(0);
		}
		else
		{
			/* @var $objTeams \Contao\Database\Mysqli\Result */
			$objTeams = $this->Database->prepare('SELECT id FROM tl_cs_team WHERE league IN ('.implode(',', $this->User->cs_leagues).')')->execute();

			foreach ($objTeams->fetchAllAssoc() AS $arrTeam)
			{
				$root[] = $arrTeam['id'];
			}
		}

		$GLOBALS['TL_DCA']['tl_cs_team']['list']['sorting']['root'] = $root;
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
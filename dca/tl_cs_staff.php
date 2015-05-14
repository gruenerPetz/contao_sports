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

$GLOBALS['TL_DCA']['tl_cs_staff'] = array
(
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_cs_team',
		'enableVersioning'            => true,
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
			'fields'                  => array('lastName'),
			'flag'                    => 1,
			'panelLayout'             => 'filter;sort,search,limit'
		),
		'label' => array
		(
			'fields'                  => array('staff_image', 'lastName', 'firstName'),
			'showColumns'             => true,
			'label_callback'          => array('tl_cs_staff', 'getLabel')
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
				'label'               => &$GLOBALS['TL_LANG']['tl_cs_staff']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cs_staff']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_cs_staff']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{staff_legend},lastName,firstName,alias;{function_legend},position,function;{info_legend},info;{image_legend},singleSRC'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'lastName' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_staff']['lastName'],
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'firstName' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_staff']['firstName'],
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_staff']['alias'],
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'alias', 'unique'=>true, 'maxlength'=>128),
			'save_callback' => array
			(
				array('tl_cs_staff', 'generateAlias')
			),
			'sql'                     => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
		),
		'position' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_staff']['position'],
			'default'                 => 'coach',
			'inputType'               => 'select',
			'options'                 => array('headcoach', 'coach', 'physio', 'equipment'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_cs_staff']['position'],
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'function' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_staff']['function'],
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'info' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_staff']['info'],
			'inputType'               => 'textarea',
			'sql'                     => "text NULL",
			'eval'                    => array('rte'=>'tinyMCE', 'tl_class'=>'clr'),
		),
		'singleSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_cs_staff']['singleSRC'],
			'inputType'               => 'fileTree',
			'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio'),
			'sql'                     => "binary(16) NULL",
		),
	)
);

class tl_cs_staff extends Backend
{
	public function getLabel($arrRow, $label, DataContainer $dc, $args)
	{
		$strImageURL = 'system/modules/contao_sports/assets/images/athlete_no_image.png';

		if ($arrRow['singleSRC'])
		{
			$objFile = FilesModel::findByUuid($arrRow['singleSRC']);

			if ($objFile !== null)
			{
				$strImageURL = TL_FILES_URL . Image::get($objFile->path, 30, 40, 'center_center');
			}
		}

		$args[0] = '<img src="'.$strImageURL.'" width="30" height="40" alt="">';
		return $args;
	}


	/**
	 * Auto-generate the event alias if it has not been set yet
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
			$varValue = standardize(String::restoreBasicEntities($dc->activeRecord->lastName.'-'.$dc->activeRecord->firstName));
		}

		$objAlias = $this->Database->prepare("SELECT id FROM tl_cs_staff WHERE alias=?")
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
}
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

$GLOBALS['TL_DCA']['tl_cs_calendar_events'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'ptable'                      => 'tl_cs_calendar',
        'switchToEdit'                => true,
        'enableVersioning'            => true,
        'onsubmit_callback' => array
        (
            array('tl_cs_calendar_events', 'adjustTime'),
            array('tl_cs_calendar_events', 'generateTitle'),
            array('tl_cs_calendar_events', 'generateAlias')
        ),
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
            'mode'                    => 4,
            'fields'                  => array('startTime DESC'),
            'headerFields'            => array('title', 'year'),
            'panelLayout'             => 'filter;sort,search,limit',
            'child_record_callback'   => array('tl_cs_calendar_events', 'listEvents'),
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
                'label'               => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif',
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_calendar_events']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            ),
            'toggle' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_content']['toggle'],
                'icon'                => 'invisible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('tl_cs_calendar_events', 'toggleIcon')
            ),
        )
    ),

    // Palettes
    'palettes' => array
    (
        '__selector__'                => array('featured', 'further_information'),
        'default'                     => '{author_legend},title,author,featured;{details_legend},location,startDate,startTime;{game_legend},groups,team_a,team_b,result_team_a,result_team_b,finish;{further_information_legend},further_information;{publish_legend},published'
    ),
    'subpalettes' => array
    (
        'featured'                    => 'color',
        'further_information'		  => 'result_team_a_q1,result_team_b_q1,result_team_a_q2,result_team_b_q2,result_team_a_q3,result_team_b_q3,result_team_a_q4,result_team_b_q4,visitors'
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
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['title'],
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>255,'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'featured' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['featured'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true,'tl_class'=>'clr'),
            'sql'                     => "char(1) NOT NULL default ''"
        ),
        'color' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['color'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'select',
            'options'				  => array('gold', 'red', 'blue', 'black'),
            'eval'                    => array('tl_class'=>'clr'),
            'reference'               => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['color'],
            'sql'                     => "varchar(32) NOT NULL default ''"
        ),
        'alias' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['alias'],
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'alias', 'unique'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
            'sql'                     => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
        ),
        'author' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['author'],
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
        'location' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['location'],
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>255, 'tl_class'=>'long'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'startDate' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['startDate'],
            'default'                 => time(),
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'date', 'mandatory'=>true, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
            'sql'                     => "int(10) unsigned NULL"
        ),
        'startTime' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['startTime'],
//			'default'                 => time(),
            'default'                 => '15:00',
            'filter'                  => true,
            'sorting'                 => true,
            'flag'                    => 8,
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'time', 'mandatory'=>true, 'doNotCopy'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(10) unsigned NULL"
        ),
        'groups' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['groups'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'options_callback'        => array('tl_cs_calendar_events', 'getGroups'),
//            'foreignKey'              => 'tl_cs_calendar_groups.title',
            'eval'                    => array('multiple'=>true),
            'sql'                     => "blob NULL",
            'relation'                => array('type'=>'belongsToMany', 'load'=>'lazy')
        ),
        'team_a' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['team_a'],
            'inputType'               => 'select',
            'options_callback'        => array('tl_cs_calendar_events', 'getTeams'),
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'eval'                    => array('chosen'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
        ),

        'team_b' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['team_b'],
            'inputType'               => 'select',
            'options_callback'        => array('tl_cs_calendar_events', 'getTeams'),
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'eval'                    => array('chosen'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
        ),
        'result_team_a' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['result_team_a'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'digit', 'doNotCopy'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(10) unsigned NULL"
        ),
        'result_team_b' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['result_team_b'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'digit', 'doNotCopy'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(10) unsigned NULL"
        ),
        'further_information' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['further_information'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true,'tl_class'=>'clr'),
            'sql'                     => "char(1) NOT NULL default ''"
        ),
        'result_team_a_q1' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['result_team_a_q1'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'digit', 'doNotCopy'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(2) unsigned NULL"
        ),
        'result_team_b_q1' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['result_team_b_q1'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'digit', 'doNotCopy'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(2) unsigned NULL"
        ),
        'result_team_a_q2' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['result_team_a_q2'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'digit', 'doNotCopy'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(2) unsigned NULL"
        ),
        'result_team_b_q2' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['result_team_b_q2'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'digit', 'doNotCopy'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(2) unsigned NULL"
        ),
        'result_team_a_q3' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['result_team_a_q3'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'digit', 'doNotCopy'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(2) unsigned NULL"
        ),
        'result_team_b_q3' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['result_team_b_q3'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'digit', 'doNotCopy'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(2) unsigned NULL"
        ),
        'result_team_a_q4' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['result_team_a_q4'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'digit', 'doNotCopy'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(2) unsigned NULL"
        ),
        'result_team_b_q4' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['result_team_b_q4'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'digit', 'doNotCopy'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(2) unsigned NULL"
        ),
        'visitors' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['visitors'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'digit', 'doNotCopy'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(5) unsigned NULL"
        ),
        'finish' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['finish'],
            'inputType'               => 'checkbox',
            'eval'                    => array('doNotCopy'=>true, 'tl_class'=>'clr'),
            'sql'                     => "char(1) NOT NULL default ''"
        ),
        'gameReport' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['gameReport'],
            'inputType'               => 'textarea',
            'eval'                    => array('rte'=>'tinyMCE'),
            'sql'                     => "text NULL"
        ),
        'published' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_cs_calendar_events']['published'],
            'default'				  => 1,
            'inputType'               => 'checkbox',
            'sql'                     => "char(1) NOT NULL default ''"
        ),
    ),
);

class tl_cs_calendar_events extends Backend
{
    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }


    public function listEvents($arrRow)
    {
        $objTeamA = CsTeamModel::findByPk($arrRow['team_a']);
        $objTeamB = CsTeamModel::findByPk($arrRow['team_b']);

        $strTeamAImageURL = $strTeamBImageURL = 'system/modules/contao_sports/assets/images/team_no_image.png';

        if ($objTeamA->singleSRC)
        {
            $objFile = FilesModel::findByUuid($objTeamA->singleSRC);

            if ($objFile !== null)
            {
                $strTeamAImageURL = TL_FILES_URL . Image::get($objFile->path, 40, 40, 'center_center');
            }
        }

        if ($objTeamB->singleSRC)
        {
            $objFile = FilesModel::findByUuid($objTeamB->singleSRC);

            if ($objFile !== null)
            {
                $strTeamBImageURL = TL_FILES_URL . Image::get($objFile->path, 40, 40, 'center_center');
            }
        }

        $strDate = Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $arrRow['startTime']);
        $strTime = Date::parse($GLOBALS['TL_CONFIG']['timeFormat'], $arrRow['startTime']);

        if($arrRow['finish']==1)
        {
            $strResult = '<div  style="float: left;margin-right:220px;><span style="display:inline-block;width: 70px;"><b>Ergebnis: </b></span>'.$arrRow['result_team_a'].':'.$arrRow['result_team_b'].'<br/></div>';
        }

        return '
<div style="margin-bottom:5px;"><b style="text-transform:uppercase;">'.$objTeamA->name.' VS '.$objTeamB->name.'</b></div>
<div style="float:left;margin-right:20px;">
	<img src="'.$strTeamAImageURL.'" width="40" height="40" title="'.$objTeamA->name.'">
	<img src="system/modules/contao_sports/assets/images/calendar_vs.png" width="30" height="40">
	<img src="'.$strTeamBImageURL.'" width="40" height="40" title="'.$objTeamB->name.'">
</div>
<div style="float:left;width:300px;margin-right:20px;">
	<span style="display:inline-block;width: 70px;"><b>Datum: </b></span>'.$strDate.'<br/>
	<span style="display:inline-block;width: 70px;"><b>Zeit: </b></span>'.$strTime.' Uhr<br/>
	<span style="display:inline-block;width: 70px;"><b>Ort: </b></span>'.$arrRow['location'].'<br/>
</div>' . $strResult;
    }


    /**
     * Adjust start end end time of the event based on date, span, startTime and endTime
     * @param \DataContainer
     */
    public function adjustTime(DataContainer $dc)
    {
        // Return if there is no active record (override all)
        if (!$dc->activeRecord)
        {
            return;
        }

        $arrSet = array();
        $arrSet['startTime'] = strtotime(date('Y-m-d', $dc->activeRecord->startDate) . ' ' . date('H:i:s', $dc->activeRecord->startTime));

        $this->Database->prepare("UPDATE tl_cs_calendar_events %s WHERE id=?")->set($arrSet)->execute($dc->id);
    }


    /**
     * Auto-generate the event alias if it has not been set yet
     * @param mixed
     * @param \DataContainer
     * @return mixed
     * @throws \Exception
     */
    public function generateTitle(DataContainer $dc)
    {
        $objNameTeamA = CsTeamModel::findByPk($dc->activeRecord->team_a);
        $objNameTeamB = CsTeamModel::findByPk($dc->activeRecord->team_b);

        if ($dc->activeRecord->title === '' && $objNameTeamA && $objNameTeamB)
        {
            $dc->activeRecord->title = $objNameTeamA->name . ' vs ' . $objNameTeamB->name;

            $this->Database->prepare("UPDATE tl_cs_calendar_events %s WHERE id=?")->set(array(
                'title' => $dc->activeRecord->title
            ))->execute($dc->id);
        }
    }


    /**
     * Auto-generate the event alias if it has not been set yet
     * @param mixed
     * @param \DataContainer
     * @return mixed
     * @throws \Exception
     */
    public function generateAlias(DataContainer $dc)
    {
        $autoAlias = false;

        // Generate alias if there is none
        if ($dc->activeRecord->alias === '')
        {
            $autoAlias = true;
            $dc->activeRecord->alias = standardize(String::restoreBasicEntities($dc->activeRecord->title));
        }

        $objAlias = $this->Database->prepare("SELECT id FROM tl_cs_calendar_events WHERE alias=?")
            ->execute($dc->activeRecord->alias);

        // Check whether the alias exists
        if ($objAlias->numRows > 1 && !$autoAlias)
        {
            throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $dc->activeRecord->alias));
        }

        // Add ID to alias
        if ($objAlias->numRows && $autoAlias)
        {
            $dc->activeRecord->alias .= '-' . $dc->id;
        }

        $this->Database->prepare("UPDATE tl_cs_calendar_events %s WHERE id=?")->set(array(
            'alias' => $dc->activeRecord->alias
        ))->execute($dc->id);
    }


    public function getTeams()
    {
        $arrTeams = array();
        $arrLeagues = array();

        if (!is_array($this->User->cs_leagues) || empty($this->User->cs_leagues))
        {
            /* @var $objTeams \Contao\Database\Mysqli\Result */
            $objTeams = $this->Database->prepare('SELECT id, name, league FROM tl_cs_team')->execute();
        }
        else
        {
            /* @var $objTeams \Contao\Database\Mysqli\Result */
            $objTeams = $this->Database->prepare('SELECT id, name, league FROM tl_cs_team WHERE league IN ('.implode(',', $this->User->cs_leagues).')')->execute();
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


    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (strlen(Input::get('tid')))
        {
            $this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }

        $href .= '&amp;id='.Input::get('id').'&amp;tid='.$row['id'].'&amp;state='.$row['visible'];

        if ($row['published'])
        {
            $icon = 'visible.gif';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }


    /**
     * Toggle the visibility of an element
     * @param integer
     * @param boolean
     * @param \DataContainer
     */
    public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
    {
        // Update the database
        $this->Database->prepare("UPDATE tl_cs_calendar_events SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
            ->execute($intId);
    }


    /**
     * @param DataContainer $dc
     * @return array
     */
    public function getGroups(DataContainer $dc)
    {
        $return = array();

        /* @var $objGroups \Contao\Database\Mysqli\Result */
        $objGroups = $this->Database->prepare('SELECT * FROM tl_cs_calendar_groups WHERE pid = ?')->execute($dc->activeRecord->pid);

        foreach ($objGroups->fetchAllAssoc() AS $objGroup)
        {
            $return[$objGroup['id']] = $objGroup['title'];
        }

        return $return;
    }
}
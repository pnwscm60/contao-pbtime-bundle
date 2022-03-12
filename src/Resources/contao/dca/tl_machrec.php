<?php
declare(strict_types=1);

/*
 * This file is part of pbwork.
 * DCA für machrec for pbtime
 * (c) Markus Schenker 2022 <scm@olternativ.ch>
 * @license LGPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/pnwscm60/contao-pbwork-bundle
 * Table tl_machrec for pbtime
*/
$GLOBALS['TL_DCA']['tl_machrec'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'						=> 'tl_project',
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index'
			)
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('sorting'),
			'headerFields'			=> array('title','start'),
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'	=> array('tl_timerec', 'generateTimeRow')
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
				'label'               => &$GLOBALS['TL_LANG']['tl_timerec']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_timerec']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			/*'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_timerec']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_timerec', 'toggleIcon')
			),*/
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_timerec']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
				'attributes'          => 'style="margin-right:3px"'
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'	=> 'projekt,datum,minutes,descript'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'  => "int(10) unsigned NOT NULL auto_increment"
		), //pid = id_project
		'pid'     => array 
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		), //catid = id_category
		'catid'     => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		), // id_job
		'jobid'     => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		), // id_member
		'memberid'     => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'sorting' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'datum' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_timerec']['datum'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>true, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'				=> "int(10) unsigned NOT NULL default '0'"
		),
		'minutes' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['amount'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>8, 'tl_class'=>'w50'),
			'sql'                     => "int(6) NOT NULL default '0'"
		),
		'descript' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['descript'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
        'trdone' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_timerec']['trdone'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>false, 'tl_class'=>'w50 wizard'),
			'sql'				=> "int(10) unsigned NOT NULL default '0'"
		),
        'trfinal' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_timerec']['trdone'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>false, 'tl_class'=>'w50 wizard'),
			'sql'				=> "int(10) unsigned NOT NULL default '0'"
		),
        'trtext' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['rtext'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
        'transatz' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['tregieansatz'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>8, 'tl_class'=>'w50'),
			'sql'                     => "double(5,2) NOT NULL default '0.00'"
		),
        'tregiekommentar' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['tregiekommentar'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
        'rcatid'     => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		), 
		'rjobid'     => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		), 
        'rminutes' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['amount'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>8, 'tl_class'=>'w50'),
			'sql'                     => "int(6) NOT NULL default '0'"
		),
        'tregie'     => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
        'trnumber' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['trnumber'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>8, 'tl_class'=>'w50'),
			'sql'                     => "int(6) NOT NULL default '0'"
		),
        'typ'     => array
		(
			'sql' => "int(1) unsigned NOT NULL default '0'"
		),
	)
);
/*Classes
class tl_machrec extends Backend {

Backend user object 
public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

  
     * Generate a song row and return it as HTML string
     * @param array
     * @return string
     
public function generateTimeRow($arrRow)
    {
        return '<div><div style="float:left;width:75px;">' . $arrRow['datum'] . '</div><div style="padding-left:3px;float:left;width:30px;">' . $arrRow['minutes'] . '</div><div style="padding-left:3px;float:left;">' . $arrRow['descript'] . '</div></div>';
}

public function generateAlias($varValue, DataContainer $dc)
	{
		$autoAlias = false;

		// Generate alias if there is none
		if ($varValue == '')
		{
			$autoAlias = true;
			$varValue = standardize(String::restoreBasicEntities($dc->activeRecord->descript));
		}

		$objAlias = $this->Database->prepare("SELECT id FROM tl_machrec WHERE alias=?")
								   ->execute($varValue);

		// Check whether the news alias exists
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
*/

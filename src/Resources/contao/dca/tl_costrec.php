<?php
declare(strict_types=1);

/*
 * This file is part of pbwork.
 * DCA für costrec für pbtime
 * (c) Markus Schenker 2022 <scm@olternativ.ch>
 * @license LGPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/pnwscm60/contao-pbwork-bundle
 * Table tl_costrec
 */
$GLOBALS['TL_DCA']['tl_costrec'] = array
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
			'child_record_callback'	=> array('tl_costrec', 'generateCostRow')
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
				'label'               => &$GLOBALS['TL_LANG']['tl_costrec']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_costrec']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			/*'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_costrec']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_costrec', 'toggleIcon')
			),*/
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_costrec']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
				'attributes'          => 'style="margin-right:3px"'
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'	=> 'projekt,datum,title,einheit,amount,descript'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid'     => array
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
		// id_member
		'memberid'     => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'datum' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_costrec']['datum'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>true, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'				=> "int(10) unsigned NOT NULL default '0'"
		),
        'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_costrec']['title'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'einheit' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['einheit'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>8, 'tl_class'=>'w50'),
			'sql'                     => "int(6) NOT NULL default '0'"
		),
		'amount' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_costrec']['amount'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>8, 'tl_class'=>'w50'),
			'sql'                     => "float(5,2) unsigned NOT NULL default '0.00'"
		),
		'descript' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_costrec']['descript'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
        'rmtitle' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['rmtitle'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>1, 'tl_class'=>'w50'),
			'sql'                     => "int(1) NOT NULL default '0'"
		),
        'rmdone' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_timerec']['rmdone'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>false, 'tl_class'=>'w50 wizard'),
			'sql'				=> "int(10) unsigned NOT NULL default '0'"
		),
        'rmfinal' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_timerec']['rmfinal'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>false, 'tl_class'=>'w50 wizard'),
			'sql'				=> "int(10) unsigned NOT NULL default '0'"
		),
         'rmtext' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['rmtext'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
        'rmansatz' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['rmansatz'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>8, 'tl_class'=>'w50'),
			'sql'                     => "double(5,2) NOT NULL default '0.00'"
		),
        'rmkommentar' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['rmkommentar'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'rmeinheit' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['rmeinheit'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>8, 'tl_class'=>'w50'),
			'sql'                     => "int(6) NOT NULL default '0'"
		),
		'rmamount' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_costrec']['rmamount'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>8, 'tl_class'=>'w50'),
			'sql'                     => "float(5,2) unsigned NOT NULL default '0.00'"
		),
		'rmdescript' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_costrec']['rmdescript'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
        'mregie'     => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
        'rmnumber' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_timerec']['rmnumber'],
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
			'sql' => "int(1) unsigned NOT NULL default '1'"
		),
	)
);
/*Classes*/
class tl_costrec extends Backend {

/*Backend user object */
/*public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}
*/
    /**
     * Generate a song row and return it as HTML string
     * @param array
     * @return string
     */
public function generateCostRow($arrRow)
    {
        return '<div><div style="float:left;width:75px;">' . $arrRow['datum'] . '</div><div style="padding-left:3px;float:left;width:25px;">' . $arrRow['einheit'] . '</div><div style="padding-left:3px;float:left;width:60px;">' . $arrRow['amount'] . '</div><div style="padding-left:3px;float:left;">[' . $arrRow['descript'] . ']</div></div>';
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

		$objAlias = $this->Database->prepare("SELECT id FROM tl_costrec WHERE alias=?")
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

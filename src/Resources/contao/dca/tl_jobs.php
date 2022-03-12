<?php
declare(strict_types=1);

/*
 * This file is part of pbwork.
 * DCA für jobs 
 * (c) Markus Schenker 2022 <scm@olternativ.ch>
 * @license LGPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/pnwscm60/contao-pbwork-bundle
 * Table tl_jobs
 */
$GLOBALS['TL_DCA']['tl_jobs'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'						=> 'tl_category',
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
			'child_record_callback'	=> array('tl_jobs', 'generateContRow')
		),
		'label' => array
		(
			'fields'                  => array('datum'),
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
				'label'               => &$GLOBALS['TL_LANG']['tl_jobs']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_jobs']['delete'],
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
				'label'               => &$GLOBALS['TL_LANG']['tl_jobs']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
				'attributes'          => 'style="margin-right:3px"'
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'	=> 'titel,description'
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
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_jobs']['title'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'maxlength'=>100, 'tl_class'=>'w50'),
			'sql'                     => "varchar(100) NOT NULL default ''"
		),
		'description' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_jobs']['description'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'typ'     => array
		(
			'sql' => "int(1) unsigned NOT NULL default '0'"
		),
        'active'     => array
		(
			'sql' => "int(1) unsigned NOT NULL default '0'"
		),
	)
);
/*Classes
class tl_jobs extends Backend {

Backend user object 
public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


     * Generate a song row and return it as HTML string
     * @param array
     * @return string
    
public function generateContRow($arrRow)
    {
        return '<div><div style="padding-left:3px;float:left;width:225px;">' . $arrRow['titel'] . '</div></div>';
}

public function generateAlias($varValue, DataContainer $dc)
	{
		$autoAlias = false;

		// Generate alias if there is none
		if ($varValue == '')
		{
			$autoAlias = true;
			$varValue = standardize(String::restoreBasicEntities($dc->activeRecord->description));
		}

		$objAlias = $this->Database->prepare("SELECT id FROM tl_jobs WHERE alias=?")
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

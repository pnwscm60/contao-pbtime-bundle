<?php

/**
DCA für pbtime - tl_hours
© 2016 Markus Schenker, Phi Network
 */

/**
 * Table tl_hours
 */
$GLOBALS['TL_DCA']['tl_hours'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'						=> 'tl_member',
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
			'headerFields'			=> array('pid','foryear'),
			'panelLayout'             => 'filter;sort,search,limit',
			
		),
		'label' => array
		(
			'fields'                  => array('foryear'),
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
				'label'               => &$GLOBALS['TL_LANG']['tl_hours']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_hours']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			/*'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_hours']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_hours', 'toggleIcon')
			),*/
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_hours']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
				'attributes'          => 'style="margin-right:3px"'
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'	=> 'pid,foryear,yearhours'
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
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'foryear' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_hours']['foryear'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'maxlength'=>4, 'tl_class'=>'w50'),
			'sql'                     => "int(4) NOT NULL default '0'"
		),
        'prozent' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_hours']['prozent'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>4, 'tl_class'=>'w50'),
			'sql'                     => "int(3) NOT NULL default '0'"
		),
		'yearhours' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_hours']['yearhours'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'maxlength'=>4, 'tl_class'=>'w50'),
			'sql'                     => "decimal(5,1) NOT NULL default '0'"
		),
		'yearhoursaldo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_hours']['yearhoursaldo'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>4, 'tl_class'=>'w50'),
			'sql'                     => "decimal(4,1) NOT NULL default '0'"
		),
		'monthhours' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_hours']['monthhours'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkboxWizard',
			'foreignKey'              => 'tl_member.id',
			'eval'                    => array('multiple'=>true, 'feEditable'=>true, 'feGroup'=>'login'),
			'sql'                     => "varchar(255) NOT NULL default ''",
			'relation'                => array('type'=>'belongsToMany', 'load'=>'lazy')
		),
        'dailyhours' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_hours']['dailyhours'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkboxWizard',
			'foreignKey'              => 'tl_member.id',
			'eval'                    => array('multiple'=>true, 'feEditable'=>true, 'feGroup'=>'login'),
			'sql'                     => "varchar(255) NOT NULL default ''",
			'relation'                => array('type'=>'belongsToMany', 'load'=>'lazy')
		),
		'ferien' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_hours']['ferien'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>4, 'tl_class'=>'w50'),
			'sql'                     => "decimal(4,1) NOT NULL default '0'"
		),
		'feriensaldo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_hours']['feriensaldo'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>4, 'tl_class'=>'w50'),
			'sql'                     => "decimal(4,1) NOT NULL default '0'"
		),
		'comment' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_hours']['comment'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),  
	)
);
/*Classes*/
class tl_hours extends Backend {

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

public function generateAlias($varValue, DataContainer $dc)
	{
		$autoAlias = false;

		// Generate alias if there is none
		if ($varValue == '')
		{
			$autoAlias = true;
			$varValue = standardize(String::restoreBasicEntities($dc->activeRecord->descript));
		}

		$objAlias = $this->Database->prepare("SELECT id FROM tl_hours WHERE alias=?")
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

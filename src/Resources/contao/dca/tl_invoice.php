<?php
/**
DCA für ProTime: regie for pbtime
© 2020 Markus Schenker, Phi Network
 */

/**
 * Table tl_regie
 */
$GLOBALS['TL_DCA']['tl_regie'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
        'ptable'						=> 'tl_project',
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
			'fields'                  => array('regienr'),
			'flag'                    => 8,
			'panelLayout'             => 'filter;sort,search,limit'
		),
		'label' => array
		(
			'fields'                  => array('regienr'),
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
				'label'               => &$GLOBALS['TL_LANG']['tl_regie']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_regie']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_regie']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
				'attributes'          => 'style="margin-right:3px"'
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'	=> 'regienr,datum,url'
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
			 'sql'                   => "int(10) unsigned NOT NULL default '0'"
		),
	'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
	'regienr' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_regie']['regienr'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'unique'=>false, 'maxlength'=>125, 'tl_class'=>'w50'),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
	'rrdatum' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_regie']['rrdatum'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>true, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'				=> "int(10) unsigned NOT NULL default '0'"
		),
	'timeids' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_regie']['timeids'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'unique'=>false, 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
	'matids' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_regie']['matids'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'unique'=>false, 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
	'machids' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_regie']['machids'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'unique'=>false, 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
	'descript' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_regie']['descript'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(2048) NOT NULL default ''"
		),
	'status'     => array
		(
			'sql' => "int(1) unsigned NOT NULL default '0'"
		),
	'datconfirm' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),	
	)
);

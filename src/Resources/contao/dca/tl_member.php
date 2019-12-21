<?php
 /**
DCA für pbtime
© 2017 Markus Schenker, Phi Network
 */
/**
 * Ergänzung tl_member
 * 
 */

$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace 
( 
    'dateOfBirth', 
    'dateOfBirth, cpref', 
    $GLOBALS['TL_DCA']['tl_member']['palettes']['default'] 
);
$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace 
( 
    'cpref', 
    'cpref, nkurz', 
    $GLOBALS['TL_DCA']['tl_member']['palettes']['default'] 
); 
$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace 
( 
    'nkurz', 
    'nkurz, minutesoll', 
    $GLOBALS['TL_DCA']['tl_member']['palettes']['default'] 
);
$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace 
( 
    'minutesoll', 
    'minutesoll,zeitmodell', 
    $GLOBALS['TL_DCA']['tl_member']['palettes']['default'] 
); 
// Hinzufügen der Feld-Konfiguration 


$GLOBALS['TL_DCA']['tl_member']['fields']['cpref'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['cpref'],
	'reference'				=> &$GLOBALS['TL_LANG']['tl_member'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'select',
	'foreignKey'			=> "tl_category.title",
	
	'eval'                    => array('includeBlankOption' => true, 'mandatory' => true, 'feViewable' => true,'feGroup' => 'personal','tl_class' => 'w50'),
	'sql'                     => "text NULL"
);
$GLOBALS['TL_DCA']['tl_member']['fields']['nkurz'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['nkurz'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'unique'=>true, 'maxlength'=>10, 'tl_class'=>'w50'),
			'sql'                     => "varchar(10) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_member']['fields']['nkurz'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['nkurz'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'unique'=>true, 'maxlength'=>10, 'tl_class'=>'w50'),
			'sql'                     => "varchar(10) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_member']['fields']['minutesoll'] = array 
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_project']['minutesoll'],
			'inputType' => 'text',
			'exclude'   => true,
			'sorting'   => true,
			'default'	=> 0,
			'flag'      => 11,
            'search'    => true,
			'eval'      => array(
				'mandatory'   => true,
            	'unique'         => false,
				'tl_class'        => 'w50',
			),
			'sql'       => "int(1) unsigned NOT NULL default '0'"
		);
$GLOBALS['TL_DCA']['tl_member']['fields']['zeitmodell'] = array 
		(
	'label'     => &$GLOBALS['TL_LANG']['tl_member']['zeitmodell'],
			'inputType' => 'text',
			'exclude'   => true,
			'sorting'   => true,
			'default'	=> 0,
			'flag'      => 11,
            'search'    => true,
			'eval'      => array(
				'mandatory'   => true,
            	'unique'         => false,
				'tl_class'        => 'w50',
			),
			'sql'       => "int(1) unsigned NOT NULL default '0'"
		);
$GLOBALS['TL_DCA']['tl_member']['fields']['active'] = array 
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_member']['active'],
			'inputType' => 'text',
			'exclude'   => true,
			'sorting'   => true,
			'default'	=> 0,
			'flag'      => 11,
            'search'    => true,
			'eval'      => array(
				'mandatory'   => true,
            	'unique'         => false,
				'tl_class'        => 'w50',
			),
			'sql'       => "int(1) unsigned NOT NULL default '0'"
		);

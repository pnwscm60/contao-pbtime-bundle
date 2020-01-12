<?php
/*
*	DCA für tl_project
*	Pbtime
*	© 2019 Markus Schenker, Phi Network
 * 	Table tl_project
 */
$GLOBALS['TL_DCA']['tl_project'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ctable'            			=> array('tl_costrec','tl_timerec'),
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('start'),
			'flag'                    => 8,
			'panelLayout'             => 'filter;sort,search,limit'
		),
		'label' => array
		(
			'fields'                  => array('start', 'title'),
			'format'                  => '%s %s',
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
			'editco'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_project']['editco'],
				'href'  => 'table=tl_costrec',
				'icon'  => 'files/images/costs1.png'
			),
			'editti'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_project']['editti'],
				'href'  => 'table=tl_timerec',
				'icon'  => 'files/images/times2.png'
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_project']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_project']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_project']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
				'attributes'          => 'style="margin-right:3px"'
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'	=> '{titelbereich},title,descript;{databereich},start,endplanned,enddone;{Referenz},customid,contactid'
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
        'knr' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_project']['knr'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'unique'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'kname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_project']['kname'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'unique'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
        'wohnort' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_project']['wohnort'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'unique'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(25) NOT NULL default ''"
		),
		'descript' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_project']['descript'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
        'ladress' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_project']['ladresse'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'start' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_project']['start'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>true, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'				=> "int(10) unsigned NOT NULL default '0'"
		),
		'enddone' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_project']['enddone'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>true, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'				=> "int(10) unsigned NOT NULL default '0'"
		),
	'status'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_project']['status'],
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
		),
    'memberid'     => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
	)
);
/*
class tl_project extends Backend {

public function AllAdress() 
    { 
        $objDBResult = Database::getInstance()->prepare("SELECT id,aname FROM tl_ladress ORDER BY aname")->execute(); 
        $arrReturn = array(); 

        while($objDBResult->next()) 
        { 
            $arrReturn[$objDBResult->id] = $objDBResult->aname;
            
       }
        return $arrReturn; 
    }  

public function AllCustomer() 
    { 
        $objDBResult = Database::getInstance()->prepare("SELECT * FROM tl_customer ORDER BY kunde")->execute(); 
        $arrReturn = array(); 

        while($objDBResult->next()) 
        { 
            $arrReturn[$objDBResult->id] = $objDBResult->kunde.", ".kname." ".kvname;
            
       }
        return $arrReturn; 
    }
}
*/

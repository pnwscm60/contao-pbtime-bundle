<?php
/**
 * Back end modules
 * Front end modules
 */
use Pnwscm60\ContaoPbtimeBundle\Module\ModuleErfassen;
use Pnwscm60\ContaoPbtimeBundle\Module\ModuleAdmin;
use Pnwscm60\ContaoPbtimeBundle\Module\ModuleDaily;
use Pnwscm60\ContaoPbtimeBundle\Module\ModuleProfil;
use Pnwscm60\ContaoPbtimeBundle\Module\ModuleRReport;
use Pnwscm60\ContaoPbtimeBundle\Module\ModuleReport;
use Pnwscm60\ContaoPbtimeBundle\Module\ModuleVerkauf;
use Pnwscm60\ContaoPbtimeBundle\Module\ModuleRegie;
$GLOBALS['FE_MOD']['pbtime'] = [ 
	'erfassen' => ModuleErfassen::class,
	'admin' => ModuleAdmin::class,
	'daily' => ModuleDaily::class,
	'profil' => ModuleProfil::class,
	'regierap' => ModuleRReport::class,
	'verkauf' => ModuleVerkauf::class,
	'report' => ModuleReport::class,
	'regie' => ModuleRegie::class
];  

<?php
/**
 * Back end modules
 * Front end modules
 */
use Pnwscm60\PbtimeBundle\Module\ModuleErfassen;
use Pnwscm60\PbtimeBundle\Module\ModuleAdmin;
use Pnwscm60\PbtimeBundle\Module\ModuleDaily;
use Pnwscm60\PbtimeBundle\Module\ModuleProfil;
use Pnwscm60\PbtimeBundle\Module\ModuleRReport;
use Pnwscm60\PbtimeBundle\Module\ModuleReport;
use Pnwscm60\PbtimeBundle\Module\ModuleVerkauf;
use Pnwscm60\PbtimeBundle\Module\ModuleRegie;
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

<?php
declare(strict_types=1);

/*
 * This file is part of pbwork.
 * 
 * (c) Markus Schenker 2022 <scm@olternativ.ch>
 * @license LGPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/pnwscm60/contao-pbwork-bundle
 */
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

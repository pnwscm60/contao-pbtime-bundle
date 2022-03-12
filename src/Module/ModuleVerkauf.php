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
namespace Pnwscm60\ContaoPbtimeBundle\Module;
class ModuleVerkauf extends \Contao\Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_verkauf';
 
	public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### VERKAUF ###';
            $objTemplate->project = $this->headline;
            return $objTemplate->parse();
		}
        return parent::generate();
    }
	/**
	 * Compile the current element
	 */
	protected function compile()
	{
        /* PART 0: EINGEHENDE DATEN */
        //memberid = frontendUser
        $this->import('FrontendUser', 'User');
		$userid = $this->User->id;
        
        //projekt
        if($_REQUEST['proj']){
            $projekt = $_REQUEST['proj'];
            }
        //trg
         if($_REQUEST['trg']){
            $trg = $_REQUEST['trg'];
            $this->Template->trg = $trg;
         }
        
        // Projektauswahl mit Filter
        if($_REQUEST['filter']!=''){
				if ($_REQUEST['filter']=="aktiv") {
					$filter = 'WHERE enddone =""';
				}
				if ($_REQUEST['filter']=="alle"){
					$filter = '';
				}
			} else {
			$filter = 'WHERE enddone =""';
		}
        $this->Template->filter = $filter;
     
        
        $this->import('Database');
        $sql ="SELECT id, concat(knr,'/',kname,'/',wohnort) as title from tl_project ".$filter." ORDER by title;";
        $result = $this->Database->prepare($sql)->execute();
        $projectArray = array();
        while($result->next())
        {
            $projectArray[] = array
		(
			'id' => $result->id,
			'title' => $result->title
                );
        }
        $this->Template->projectarray = $projectArray;
        
/**** PART 4: VERKAUF ****/
    //Für Vkf berechtigte Member = Verkauf + Admin
    $sql= "SELECT tl_member.id, concat(lastname,' ',firstname) as name, groups FROM tl_member ORDER by name";
    $objMemb = $this->Database->execute($sql);
    while ($objMemb->next()){
        $grp = unserialize($objMemb->groups);
        if(in_array(2,$grp)||in_array(3,$grp)){

            $arrMemb[] = array
                (
                'id' => $objMemb->id,
                'name' => $objMemb->name
                );
        }
    }
    $this->Template->vkfmemb = $arrMemb;
        
        /* ****************** */
        if($_REQUEST['trg']=='vkf'){
		//******************************
		//**UPDATE PROJEKT
            if($_REQUEST['todo']=='updatepro'){
			$id=$_REQUEST['id'];
            $knr=$_REQUEST['knr'];
			$kname=$_REQUEST['kname'];
            $wohnort=$_REQUEST['wohnort'];
			$descript=$_REQUEST['descript'];
			$start=$_REQUEST['start'];
			$enddone=$_REQUEST['enddone'];
			$ladress=$_REQUEST['ladress'];
            $memberid=$_REQUEST['memberid'];
			$status=$_REQUEST['status'];
            $start1 = strtotime($start);
            if($enddone==''){ 
            $enddone1 = 0; 
            } else {
            $enddone1 = strtotime($enddone);
            }
			$sql='UPDATE tl_project SET tstamp='.time().',kname="'.$kname.'",knr="'.$knr.'",wohnort="'.$wohnort.'",descript="'.$descript.'",start='.$start1.',enddone='.$enddone1.',ladress="'.$ladress.'", memberid='.$memberid.' WHERE id='.$id.';';
            $objResult = \Database::getInstance()->execute($sql);  
		}
		/*    End Update      */
		/*****  NEW PROJECT *****/
        /************************/  
		if($_REQUEST['todo']=='insertpro'){
			$knr=$_REQUEST['knr'];
			$kname=$_REQUEST['kname'];
            $wohnort=$_REQUEST['wohnort'];
			$descript=$_REQUEST['descript'];
			$start=$_REQUEST['start'];
			$enddone=$_REQUEST['enddone'];
            $memberid=$_REQUEST['memberid'];
			$ladress=$_REQUEST['ladress'];
            $start1 = strtotime($start);
            $enddone1 = 0;
			$sql='INSERT into tl_project (tstamp, knr,kname,wohnort, descript, start, enddone, ladress, memberid';
			$sql.=') VALUES ('.time().',"'.$knr.'","'.$kname.'","'.$wohnort.'","'.$descript.'",'.$start1.','.$enddone1.',"'.$ladress.'",'.$memberid;
			$sql.=');';
            //echo $sql;
            $objResult = \Database::getInstance()->execute($sql);  
			$this->Template->mess = 'Neues Projekt wurde erfolgreich angelegt';
		}
		
		/********* DELETE PROJECT *********/
        /* ****************************** */
		if($_REQUEST['todo']=='delpro'){
		$id=$_REQUEST['id'];
        $objResult = \Database::getInstance()->execute($sql);  
            
        // Hier muss noch Routine rein > alle Leistungen/Material löschen mit diesem Projekt ! > ACHTUNG: Projekt nicht löschen, sondern inaktiv setzen
		} 

		if($_REQUEST['filter']!=''){
			if ($_REQUEST['filter']=="aktiv") {
					$filter = 'WHERE status = 1';
				}
				if ($_REQUEST['filter']=="alle"){
					$filter = 'WHERE status = 0 OR status = 1 OR status = 2';
				}
			} else {
			$filter = 'WHERE status = 1';
		}
		
	
    // BEREICH PROJEKT EDITIEREN
	/****************************/
		if($_REQUEST['todo']=='editpro'){
		$sql='SELECT tl_project.id, tl_project.tstamp, knr, kname, wohnort, ladress, descript, tl_project.start, tl_project.enddone, memberid FROM tl_project WHERE tl_project.id='.$projekt;
        $objEProject = $this->Database->execute($sql);
        
		$arrEProject= array();
		
		while ($objEProject->next())
            $start = date("d.m.Y", $objEProject->start);
            if($objEProject->enddone!=0){
                $ende = date("d.m.Y", $objEProject->enddone);
            } else {
                $ende = "";
            }
		{
        echo $objProjects->wohnort;
		$arrEProject[] = array
		(
			'id' => $objEProject->id,
			'knr' => $objEProject->knr,
            'kname' => $objEProject->kname,
            'wohnort' => $objEProject->wohnort,
			'descript' => $objEProject->descript,
			'start' => $start,
			'enddone' => $ende,
			'ladress' => $objEProject->ladress,
			'status' => $objEProject->status,
            'memberid' => $objEProject->memberid,
		);
	}
	$this->Template->eproject = $arrEProject;
         
	//$this->Template->nprocont = $arrCont;
	//$this->Template->nprocust = $arrCust;
	$this->Template->pid = $_REQUEST['id'];
	$this->Template->scope = 'pledit';
		}
       
    /* LEISTUNGSREPORT */
    /*******************/
    if($_REQUEST['todo']=='report'){
        $this->Template->todo = 'report';
        // Regieaufträge
        $sql='SELECT tl_regie.id, tl_regie.pid, regienr, rrdatum, timeids, matids, machids, tl_regie.descript, tl_regie.status, datconfirm, knr from tl_regie, tl_project WHERE tl_project.id=tl_regie.pid AND pid ='.$projekt;
        $objRegierap = $this->Database->execute($sql);      
        
    //Bestehen erstellte/abgeschlossene Regierapporte?
            $arrRegierap[] = array(
                'id' => $objRegierap->id,
                'pid' => $projekt,
                'regienr' => $objRegierap->regienr,
                'rrdatum' => date("d.m.Y",$objRegierap->rrdatum),
                'status' => $objRegierap->status,
                'typnr' => $objRegierap->typ,
                'knr' => $objRegierap->knr,
                'datconfirm' => date("-",$objRegierap->datconfirm)
            );
        
        //Projektdaten ausliefern
        $sql="SELECT * from tl_project WHERE id=".$projekt;
        $objProjekt = $this->Database->execute($sql);
        $this->Template->ptitle = $objProjekt->knr."/".$objProjekt->kname."/".$objProjekt->wohnort;
        $this->Template->start = date("d.m.Y",$objProjekt->start);
        $this->Template->enddone = ($objProjekt->enddone == 0) ? 'nicht abgeschlossen' : date("d.m.Y", $objProjekt->enddone);
        $sql="SELECT concat(lastname,' ',firstname) as name from tl_member, tl_project WHERE tl_member.id = memberid AND memberid = ".$objProjekt->memberid;
        $objPl = $this->Database->execute($sql);
        $this->Template->pl = $objPl->name;
        //Leistungsdaten holen zuerst Summen Kategorie
          $sql2="SELECT SUM(minutes) as sum, catid, tl_category.title as ctitle FROM tl_timerec, tl_jobs, tl_category WHERE tl_jobs.id=jobid and tl_category.id = tl_jobs.pid AND tl_timerec.pid=".$projekt." GROUP BY catid";
        
		$sumtime = $this->Database->execute($sql2);
		
		$arrtime= array();
		$arrsum= array();
		while ($sumtime->next())
		{
		$arrsum[] = array
		(
			'summe' => $sumtime->sum,
			'catid' => $sumtime->catid,
			'ctitle' => $sumtime->ctitle
		);
            
            //Jetzt Summen der Leistungen
            $sql="SELECT SUM(minutes) as jsum, tl_jobs.pid as pid, catid, jobid, tl_jobs.title, tregie FROM tl_timerec, tl_jobs WHERE tl_jobs.id=jobid AND tl_timerec.pid=".$projekt." AND tl_jobs.pid = ".$sumtime->catid." GROUP by jobid;";
            
            $objtime = $this->Database->execute($sql);
            
			while ($objtime->next())
		{
		$total = $total + $objtime->jsum;
		$arrtime[] = array
		(
			'id' => $objtime->id,
			'zeit' => $objtime->jsum,
			'jobtitle' => $objtime->title,
			'pid' => $objtime->pid,
			'catid' => $objtime->catid,
			'jobid' => $objtime->jobid,
            'tregie' => $objtime->tregie,
		);
		}
		}
        //Maschinendaten holen zuerst Summen Kategorie
          $sql2="SELECT SUM(minutes) as sum, catid, tl_category.title as ctitle FROM tl_machrec, tl_jobs, tl_category WHERE tl_jobs.id=jobid and tl_category.id = tl_jobs.pid AND tl_machrec.pid=".$projekt." GROUP BY catid";
        
		$sumtime = $this->Database->execute($sql2);
		
		$arrtimemach= array();
		$arrsummach= array();
		while ($sumtime->next())
		{
		$arrsummach[] = array
		(
			'summe' => $sumtime->sum,
			'catid' => $sumtime->catid,
			'ctitle' => $sumtime->ctitle
		);
            
            //Jetzt Summen der Leistungen
            $sql="SELECT SUM(minutes) as jsum, tl_jobs.pid as pid, catid, jobid, tl_jobs.title FROM tl_machrec, tl_jobs WHERE tl_jobs.id=jobid AND tl_machrec.pid=".$projekt." AND tl_jobs.pid = ".$sumtime->catid." GROUP by jobid;";
            
            $objtime = $this->Database->execute($sql);
            
			while ($objtime->next())
		{
		$totalmach = $totalmach + $objtime->jsum;
		$arrtimemach[] = array
		(
			'id' => $objtime->id,
			'zeit' => $objtime->jsum,
			'jobtitle' => $objtime->title,
			'pid' => $objtime->pid,
			'catid' => $objtime->catid,
			'jobid' => $objtime->jobid,
		);
		}
		}
        
    // Material holen für Materialrapport
       // Material für Report abrufen
     $sql = "SELECT *, nkurz FROM tl_costrec, tl_member WHERE tl_costrec.pid=".$projekt." AND memberid = tl_member.id ORDER BY datum ASC;";   
      $objmat = $this->Database->execute($sql);
            while ($objmat->next())
		{
         $dat2 = date("d.m.Y", $objmat->datum);
                    if($objmat->einheit==0) {
                        $eh = "kg";
               } elseif($objmat->einheit==1) {
                        $eh = "l";
               } elseif($objmat->einheit==2) {
                        $eh = "m";
               } elseif($objmat->einheit==3) {
                        $eh = "m2";
                } elseif($objmat->einheit==4) {
                        $eh = "St.";
                }
		$arrmat[] = array
		(
            'id' => $objmat->id,
            'datum' => $dat2,
			'amount' => $objmat->amount,
            'einheit' => $eh,
			'descript' => $objmat->descript,
			'mattitle' => $objmat->title,
			'pid' => $objmat->pid,
            'nkurz' => $objmat->nkurz,
		);
		}    
        
    $this->Template->regrap = $arrRegierap;    
    $this->Template->rmat = $arrmat; 
    $this->Template->rzeit = $arrtime;    
	$this->Template->rsumme = $arrsum;
    $this->Template->rzeitmach = $arrtimemach;    
	$this->Template->rsummemach = $arrsummach;
    $this->Template->projekt = $projekt;
    $this->Template->total = $total;    
    $this->Template->totalmach = $totalmach;        
    }
        } // ENDE REPORT

	}
}

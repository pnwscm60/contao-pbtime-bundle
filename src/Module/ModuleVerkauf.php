<?php
namespace Pnwscm60\PbtimeBundle\Module;
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
        
    //REGIELISTE 
    //Projekte bereitstellen mit Regieeinträgen
        $sql='SELECT pid from tl_costrec WHERE mregie = 1 AND rmdone = 0 UNION SELECT pid from tl_timerec WHERE tregie = 1 AND trdone=0 UNION SELECT pid from tl_machrec WHERE tregie = 1 AND trdone=0';
        $objRegie = \Database::getInstance()->execute($sql);

        
        // pro gefundenes Projekt  > Projektdaten in neues Array
        while ($objRegie->next())
		{
            $sql='SELECT tl_project.id, concat(knr,"/",kname,"/",wohnort) as title, memberid, concat(lastname," ",firstname) as pl from tl_project, tl_member WHERE tl_project.id='.$objRegie->pid.' AND tl_member.id=memberid';
            $objRegieDet = \Database::getInstance()->execute($sql);
            while ($objRegieDet->next())
            {
            $arrProRegie[]=array(
                'proid' => $objRegieDet->id,
                'ptitle' => $objRegieDet->title,
                'pl' => $objRegieDet->pl
            );
            }
        }
            $this->Template->regielist = $arrProRegie;
        
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
       
    // BEREICH REGIE-RAPPORT
    //**************************
    //* Teil 1: REGIEEDIT SPEICHERN */
    if($_REQUEST['todo']=='saveregieed'){
        
        $tlim=count($_REQUEST['tid']);
        for($i=0;$i<$tlim;$i++){
            //Jeden Regieeintrag Zeiten auslesen > tl_timerec aktualisieren
            //echo $i.": ";
            $tid = $_REQUEST['tid'][$i];
            //echo $tid." ";
            $timarr[]=$tid;
            $rminutes = $_REQUEST['rminutes'][$i]*60;
            $transatz = $_REQUEST['transatz'][$i];
            $trtext = $_REQUEST['trtext'][$i];
            $trdone = mktime(0,0,0,date("m"),date("d"),date("y"));
            //höchste Regienummer für dieses Projekt holen
            $sql2='SELECT trnumber as nr from tl_timerec WHERE pid = '.$projekt.' AND tregie = 1 UNION SELECT rmnumber as nr from tl_costrec where pid = '.$projekt.' AND mregie = 1 UNION SELECT trnumber as nr from tl_machrec WHERE pid = '.$projekt.' AND tregie = 1';
            //echo $sql2;
            $objMaxnr = \Database::getInstance()->execute($sql2);
            $regnrt = array();
            while($objMaxnr->next()){
                 $regnrt[] =  $objMaxnr->nr;
            }
            $newregnr = max($regnrt)+1;
            $sql='UPDATE tl_timerec SET tstamp='.time().', rminutes='.$rminutes.',transatz='.$transatz.',trtext="'.$trtext.'",trdone='.$trdone.', trnumber='.$newregnr.' WHERE id='.$tid.';';
           //echo "</br>".$sql."</br>";
            $objResult = \Database::getInstance()->execute($sql); 
        }
        $matarr = array();
        $mlim=count($_REQUEST['mid']);
        for($i=0;$i<$mlim;$i++){
            //Jeden Regieeintrag Material auslesen > tl_costrec aktualisieren
            $mid = $_REQUEST['mid'][$i];
            $matarr[]=$mid;
            $rmamount = $_REQUEST['rmamount'][$i];
            $rmeinheit = $_REQUEST['rmeinheit'][$i];
            $rmansatz = $_REQUEST['rmansatz'][$i];
            $rmtext = $_REQUEST['rmtext'][$i];
            $rmkommentar = $_REQUEST['rmkommentar'][$i];
            $rmdone = mktime(0,0,0,date("m"),date("d"),date("y"));
            //höchste Regienummer für dieses Projekt holen
            $sql='SELECT trnumber as nr from tl_timerec WHERE pid = '.$projekt.' AND tregie = 1 UNION SELECT rmnumber as nr from tl_costrec where pid = '.$projekt.' AND mregie = 1 UNION SELECT trnumber as nr from tl_machrec WHERE pid = '.$projekt.' AND tregie = 1';
            $objMaxnr = \Database::getInstance()->execute($sql);
            $regnrm = array();
            while($objMaxnr->next()){
                 $regnrm[] = $objMaxnr->nr;
            }
            $newregnr = max($regnrm)+1;
            $sql='UPDATE tl_costrec SET tstamp='.time().', rmamount='.$rmamount.',rmeinheit='.$rmeinheit.',rmansatz='.$rmansatz.',rmtext="'.$rmtext.'",rmkommentar="'.$rmkommentar.'",rmdone='.$rmdone.', rmnumber='.$newregnr.' WHERE id='.$mid.';';
            $objResult = \Database::getInstance()->execute($sql);
            echo $sql;
        }
        $malim=count($_REQUEST['maid']);
        $macharr = array();    
        for($i=0;$i<$malim;$i++){
            //Jeden Regieeintrag Zeiten auslesen > tl_timerec aktualisieren
            $maid = $_REQUEST['maid'][$i];
            $timarr[]=$tid;
            $rminutes = $_REQUEST['mrminutes'][$i]*60;
            $transatz = $_REQUEST['mtransatz'][$i];
            $trtext = $_REQUEST['mtrtext'][$i];
            $trdone = mktime(0,0,0,date("m"),date("d"),date("y"));
            //höchste Regienummer für dieses Projekt holen
            $sql='SELECT trnumber as nr from tl_timerec WHERE pid = '.$projekt.' AND tregie = 1 UNION SELECT rmnumber as nr from tl_costrec where pid = '.$projekt.' AND mregie = 1 UNION SELECT trnumber as nr from tl_machrec WHERE pid = '.$projekt.' AND tregie = 1';
            $objMaxnr = \Database::getInstance()->execute($sql);
            
            $regnrma = array();
            while($objMaxnr->next()){
                $regnrma[] = $objMaxnr->nr;
            }
            $newregnr = max($regnrma)+1;
            echo "NEU:".$newregnr;
            $sql='UPDATE tl_machrec SET tstamp='.time().', rminutes='.$rminutes.',transatz='.$transatz.',trtext="'.$trtext.'",trdone='.$trdone.', trnumber='.$newregnr.' WHERE id='.$maid.';';
            $objResult = \Database::getInstance()->execute($sql); 
        }
            
        $todo = "report";
        }
    
       //Regieedit für dieses Projekt bereitstellen     
      if($_REQUEST['todo']=='prepeditregie'){ 
          $pro = $_REQUEST['proj'];
          // Zuerst Zeiten
          $sql="SELECT * from tl_project WHERE id=".$pro;
          $objRegiePro = \Database::getInstance()->execute($sql);
          //times mit Regie für dieses Projekt
          $sql='SELECT tl_timerec.id as id, datum, jobid, catid, rjobid, rcatid, tl_category.title as ctitle, tl_jobs.title as jtitle, minutes, rminutes, tregiekommentar,  tl_timerec.descript as descript, tregiekommentar, transatz, trtext, trdone from tl_timerec, tl_category, tl_jobs WHERE tl_timerec.catid = tl_category.id AND tl_timerec.jobid = tl_jobs.id AND tl_timerec.pid = '.$pro.' AND tregie = 1 and trdone=0';
          $objRegieTime = \Database::getInstance()->execute($sql);
          
          while ($objRegieTime->next())
            {
              $dattime = date("d.m.Y", $objRegieTime->datum);
              $rdonetime = date("d.m.Y", $objRegieTime->trdone);
              $arrRegieTime[]=array(
                'tid' => $objRegieTime->id,
                'datum' => $dattime,
                'knr' => $objRegieTime->knr,
                'descript' => $objRegieTime->descript,
                'ctitle' => $objRegieTime->ctitle,
                'jtitle' => $objRegieTime->jtitle,
                'minutes' => $objRegieTime->minutes/60,
                'rjobid' => $objRegieTime->rjobid,
                'rcatid' => $objRegieTime->rcatid,
                'rminutes' => $objRegieTime->rminutes/60,
                'tregiekommentar' => $objRegieTime->tregiekommentar,
                'transatz' => $objRegieTime->transatz,
                'trtext' => $objRegieTime->trtext,
                'trdone' => $rdonetime
            );
          }
          
          //mat mit Regie für dieses Projekt
          $sql='SELECT * from tl_costrec WHERE pid = '.$pro.' AND mregie=1 and rmdone=0';
          $objRegieMat = \Database::getInstance()->execute($sql);
          while ($objRegieMat->next())
            {
              $datmat = date("d.m.Y", $objRegieMat->datum);
              $rmdone = date("d.m.Y", $objRegieMat->rmdone);
              if($objRegieMat->einheit==5){$eh = "kg";}
            elseif($objRegieMat->einheit==1){$eh = "l";}
            elseif($objRegieMat->einheit==2){$eh = "m";}
            elseif($objRegieMat->einheit==3){$eh = "m2";}
            elseif($objRegieMat->einheit==4){$eh = "St.";}
            $arrRegieMat[]=array(
                'mid' => $objRegieMat->id,
                'datum' => $datmat,
                'einheit' => $eh,
                'amount' => $objRegieMat->amount,
                'descript' => $objRegieMat->descript,
                'title' => $objRegieMat->title,
                'rmdone' => $rmdone,
                'rmamount' => $objRegieMat->rmamount,
                'rmtext' => $objRegieMat->rmtext,
                'rmansatz' => $objRegieMat->rmansatz,
                'rmkommentar' => $objRegieMat->rmkommentar
            );
            }
          //machinetime for this project
          $sql='SELECT tl_machrec.id as id, datum, jobid, catid, rjobid, rcatid, tl_category.title as ctitle, tl_jobs.title as jtitle, minutes, rminutes, tregiekommentar,  tl_machrec.descript as descript, tregiekommentar, transatz, trtext, trdone from tl_machrec, tl_category, tl_jobs WHERE tl_machrec.catid = tl_category.id AND tl_machrec.jobid = tl_jobs.id AND tl_machrec.pid = '.$pro.' AND tregie = 1 and trdone=0';
          $objRegieMach = \Database::getInstance()->execute($sql);
          
          while ($objRegieMach->next())
            {
              $dattime = date("d.m.Y", $objRegieMach->datum);
              $rdonetime = date("d.m.Y", $objRegieMach->trdone);
              $arrRegieMach[]=array(
                'tid' => $objRegieMach->id,
                'datum' => $dattime,
                'knr' => $objRegieMach->knr,
                'descript' => $objRegieMach->descript,
                'ctitle' => $objRegieMach->ctitle,
                'jtitle' => $objRegieMach->jtitle,
                'minutes' => $objRegieMach->minutes/60,
                'rjobid' => $objRegieMach->rjobid,
                'rcatid' => $objRegieMach->rcatid,
                'rminutes' => $objRegieMach->rminutes/60,
                'tregiekommentar' => $objRegieMach->tregiekommentar,
                'transatz' => $objRegieMach->transatz,
                'trtext' => $objRegieMach->trtext,
                'trdone' => $rdonetime
            );
          }
          
          //Template bereitstellen
          $this->Template->regietime = $arrRegieTime;
          $this->Template->regiemat = $arrRegieMat;
          $this->Template->regiemach = $arrRegieMach;
          $this->Template->pid = $objRegiePro->id;
          $this->Template->knr = $objRegiePro->knr;
          $this->Template->kname = $objRegiePro->kname;
          $this->Template->wohnort = $objRegiePro->wohnort;
          $this->Template->descript = $objRegiePro->descript;
          $this->Template->ladress = $objRegiePro->ladress;
           $dat1 = date("d.m.Y", $objRegiePro->start);
          $this->Template->start = $dat1;
          $this->Template->doit = 'editregie';
      }   
        //** REGIELEIST CONFIRM
        if($_REQUEST['todo']=='rconfirm'){ // Regieleistung bestätigen
            $typ = $_REQUEST['typ'];
            if($typ==0){ // Time
                $sql='UPDATE tl_timerec SET trfinal='.time().' WHERE id='.$_REQUEST['id'];
            } elseif($typ==1) { // Mat
                $sql='UPDATE tl_costrec SET rmfinal='.time().' WHERE id='.$_REQUEST['id'];
            } elseif($typ==2) { // Machine
                $sql='UPDATE tl_machrec SET trfinal='.time().' WHERE id='.$_REQUEST['id'];
            }
            
            $doSql = \Database::getInstance()->execute($sql);
            $todo = 'report';
            $projekt = $_REQUEST['proj'];
             //header ( 'Location: verkauf.html?trg=vkf&todo=report&proj='.$_REQUEST['proj'] );
        }
        
        //Regieleistung editieren/korrigieren > Daten bereitstellen
        if($_REQUEST['todo']=='rleistedit'){ 
            $typ=$_REQUEST['typ'];
            if($typ==0){ // Time
                $sql='SELECT tl_timerec.id as id, datum, jobid, catid, tl_timerec.pid, rjobid, rcatid, trnumber, tl_category.title as ctitle, tl_jobs.title as jtitle, minutes, rminutes, tregiekommentar,  tl_timerec.descript as descript, tregiekommentar, transatz, trtext, trdone from tl_timerec, tl_category, tl_jobs WHERE tl_timerec.catid = tl_category.id AND tl_timerec.jobid = tl_jobs.id AND tl_timerec.id = '.$_REQUEST['id'];
                $objRegieTime = \Database::getInstance()->execute($sql);
                $dattime = date("d.m.Y", $objRegieTime->datum);
                $rdonetime = date("d.m.Y", $objRegieTime->trdone);
                $arrRegieTime=array(
                'tid' => $objRegieTime->id,
                'pid' => $objRegieTime->pid,
                'datum' => $dattime,
                'knr' => $objRegieTime->knr,
                'descript' => $objRegieTime->descript,
                'ctitle' => $objRegieTime->ctitle,
                'jtitle' => $objRegieTime->jtitle,
                'minutes' => $objRegieTime->minutes/60,
                'rjobid' => $objRegieTime->rjobid,
                'rcatid' => $objRegieTime->rcatid,
                'rminutes' => $objRegieTime->rminutes/60,
                'tregiekommentar' => $objRegieTime->tregiekommentar,
                'transatz' => $objRegieTime->transatz,
                'trtext' => $objRegieTime->trtext,
                'trdone' => $rdonetime,
                'rmnumber' => $objRegieTime->trnumber,
                'typ' => $typ,
            );
            $this->Template->arledit = $arrRegieTime;
            $this->Template->rledit = "rledit";
            } elseif($typ==1) { // Mat
                $sql='SELECT * from tl_costrec WHERE tl_costrec.id = '.$_REQUEST['id'];
                $objRegieMat = \Database::getInstance()->execute($sql);
                $datmat = date("d.m.Y", $objRegieMat->datum);
                $rmdone = date("d.m.Y", $objRegieMat->rmdone);
              if($objRegieMat->einheit==5){$eh = "kg";}
            elseif($objRegieMat->einheit==1){$eh = "l";}
            elseif($objRegieMat->einheit==2){$eh = "m";}
            elseif($objRegieMat->einheit==3){$eh = "m2";}
            elseif($objRegieMat->einheit==4){$eh = "St.";}
              if($objRegieMat->rmeinheit==5){$reh = "kg";}
            elseif($objRegieMat->rmeinheit==1){$reh = "l";}
            elseif($objRegieMat->rmeinheit==2){$reh = "m";}
            elseif($objRegieMat->rmeinheit==3){$reh = "m2";}
            elseif($objRegieMat->rmeinheit==4){$reh = "St.";}
            $arrRegieMat=array(
                'mid' => $objRegieMat->id,
                'pid' => $objRegieMat->pid,
                'datum' => $datmat,
                'einheit' => $eh,
                'amount' => $objRegieMat->amount,
                'descript' => $objRegieMat->descript,
                'title' => $objRegieMat->title,
                'rmdone' => $rmdone,
                'rmamount' => $objRegieMat->rmamount,
                'rmtext' => $objRegieMat->rmtext,
                'rmansatz' => $objRegieMat->rmansatz,
                'rmkommentar' => $objRegieMat->rmkommentar,
                'rmeinheit' => $objRegieMat->rmeinheit,
                'rmnumber' => $objRegieMat->rmnumber,
                'typ' => $typ,
            );
            $this->Template->arledit = $arrRegieMat;
            $this->Template->rledit = "rledit";
                
            } elseif($typ==2) { // Machine
                $sql='SELECT tl_machrec.id as id, datum, jobid, catid, rjobid, rcatid, tl_machrec.pid, tl_category.title as ctitle, tl_jobs.title as jtitle, minutes, rminutes, tregiekommentar, tl_machrec.descript as descript, tregiekommentar, transatz, trtext, trdone from tl_machrec, tl_category, tl_jobs WHERE tl_machrec.catid = tl_category.id AND tl_machrec.jobid = tl_jobs.id AND tl_machrec.id = '.$_REQUEST['id'];
                $objRegieTime = \Database::getInstance()->execute($sql);
                $dattime = date("d.m.Y", $objRegieTime->datum);
                $rdonetime = date("d.m.Y", $objRegieTime->trdone);
                $arrRegieTime=array(
                'maid' => $objRegieTime->id,
                'pid' => $objRegieTime->pid,
                'datum' => $dattime,
                'knr' => $objRegieTime->knr,
                'descript' => $objRegieTime->descript,
                'ctitle' => $objRegieTime->ctitle,
                'jtitle' => $objRegieTime->jtitle,
                'minutes' => $objRegieTime->minutes/60,
                'rjobid' => $objRegieTime->rjobid,
                'rcatid' => $objRegieTime->rcatid,
                'rminutes' => $objRegieTime->rminutes/60,
                'tregiekommentar' => $objRegieTime->tregiekommentar,
                'transatz' => $objRegieTime->transatz,
                'trtext' => $objRegieTime->trtext,
                'trdone' => $rdonetime,
                'rmnumber' => $objRegieTime->trnumber,
                'typ' => $typ,
            );
            $this->Template->arledit = $arrRegieTime;
            $this->Template->rledit = "rledit";
            }  
        }    
        // ** BEARBEITETE REGIELEISTUNGEN SPEICHERN
        if($_REQUEST['todo']=='saverleistedit'){
                if($_REQUEST['typ']==0){
                    $tid = $_REQUEST['tid'];
                    $rminutes = $_REQUEST['rminutes']*60;
                    $transatz = $_REQUEST['transatz'];
                    $trtext = $_REQUEST['trtext'];
                    //$trdone = mktime(0,0,0,date("m"),date("d"),date("y"));
                                    
                $sql='UPDATE tl_timerec SET tstamp='.time().', rminutes='.$rminutes.',transatz='.$transatz.',trtext="'.$trtext.'" WHERE id='.$tid.';';
                //echo $sql;
                $objResult = \Database::getInstance()->execute($sql); 
                    }
                if($_REQUEST['typ']==1){
                    $mid = $_REQUEST['tid'];
                    $rmamount = $_REQUEST['rmamount'];
                    $rmeinheit = $_REQUEST['rmeinheit'];
                    $rmtext = $_REQUEST['rmtext'];
                    $rmkommentar = $_REQUEST['rmkommentar'];
                    $sql='UPDATE tl_costrec SET tstamp='.time().', rmamount='.$rmamount.',rmeinheit='.$rmeinheit.',rmtext="'.$rmtext.'",rmkommentar="'.$rmkommentar.'" WHERE id='.$mid.';';
                    $objResult = \Database::getInstance()->execute($sql); 
                    }
                if($_REQUEST['typ']==2){
                    $maid = $_REQUEST['tid'];
                    $rminutes = $_REQUEST['rminutes']*60;
                    $transatz = $_REQUEST['transatz'];
                    $trtext = $_REQUEST['trtext'];
                    //$trdone = mktime(0,0,0,date("m"),date("d"),date("y"));
                                    
                $sql='UPDATE tl_machrec SET tstamp='.time().', rminutes='.$rminutes.',transatz='.$transatz.',trtext="'.$trtext.'" WHERE id='.$maid.';';
                //echo $sql;
                $objResult = \Database::getInstance()->execute($sql); 
                }
                //header ( 'Location: verkauf.html?trg=vkf&todo=report&proj='.$_REQUEST['proj'] );
                }  
         
        
        } // **** ENDE BEREICH VKF

    if($_REQUEST['todo']=='report'||$todo=='report'){
        $this->Template->todo = 'report';
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
    // Bestehen erstellte/abgeschlossene Regierapporte?
    //$sql = 'SELECT tl_timerec.id, trdone as done, trnumber as nr, trfinal as finale, tl_timerec.typ, tl_timerec.tstamp, tl_category.title as ctitle, tl_jobs.title as jtitle from tl_timerec, tl_category, tl_jobs WHERE tl_timerec.catid=tl_category.id AND tl_timerec.jobid=tl_jobs.id AND tl_timerec.pid='.$_REQUEST['proj'].' AND trdone!="0" UNION SELECT id, rmdone as done, rmnumber as nr, rmfinal as finale, typ, tstamp from tl_costrec WHERE tl_costrec.pid='.$_REQUEST['proj'].' AND rmdone!="0" UNION SELECT id, trdone as done, trnumber as nr, trfinal as finale, typ, tstamp from tl_machrec WHERE tl_machrec.pid='.$_REQUEST['proj'].' AND trdone!="0" ORDER by nr' ;    
    $sql = 'SELECT tl_timerec.id, tl_timerec.trdone as done, tl_timerec.trnumber as nr, tl_timerec.trfinal as finale, tl_timerec.typ, tl_timerec.tstamp, tl_category.title as ctitle, tl_jobs.title as jtitle from tl_timerec, tl_category, tl_jobs WHERE tl_timerec.catid=tl_category.id AND tl_timerec.jobid=tl_jobs.id AND tl_timerec.pid='.$_REQUEST['proj'].' AND tl_timerec.trdone!="0" UNION SELECT tl_costrec.id, rmdone as done, rmnumber as nr, rmfinal as finale, tl_costrec.typ, tl_costrec.tstamp, tl_costrec.title as jtitle, tl_costrec.amount as ctitle from tl_costrec WHERE tl_costrec.pid='.$_REQUEST['proj'].' AND rmdone!="0" UNION SELECT tl_machrec.id, tl_machrec.trdone as done, tl_machrec.trnumber as nr, tl_machrec.trfinal as finale, tl_machrec.typ, tl_machrec.tstamp, tl_category.title as ctitle, tl_jobs.title as jtitle from tl_machrec, tl_category, tl_jobs WHERE tl_machrec.pid='.$_REQUEST['proj'].' AND tl_machrec.trdone!="0" AND tl_machrec.catid=tl_category.id AND tl_machrec.jobid=tl_jobs.id ORDER by nr';
        //echo $sql;
    $objRegierap = $this->Database->execute($sql);
        while($objRegierap->next()){
            switch($objRegierap->typ){
                case 0:
                    $typ = 'A';
                    break;
                case 1:
                    $typ = 'V';
                    break;
                case 2:
                    $typ = 'M';
                    break;
            }
            $datregie = date("d.m.Y", $objRegierap->done);
            if($objRegierap->finale!='0'){
                $datfinal = date("d.m.Y", $objRegierap->finale);
            } else {
                $datfinal = 0;
            }
            $arrRegierap[] = array(
                'id' => $objRegierap->id,
                'regiedat' => $datregie,
                'nr' => $objRegierap->nr,
                'finale' => $datfinal,
                'tstamp' => $objRegierap->tstamp,
                'typ' => $typ,
                'typnr' => $objRegierap->typ,
                'marker' => $objRegierap->marker,
                'ctitle' => $objRegierap->ctitle,
                'jtitle' => $objRegierap->jtitle
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
    } // ENDE REPORT

	}
}

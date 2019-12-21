<?php
namespace Pnwscm60\PbtimeBundle\Module;
class ModuleVerkauf extends Module
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
        if($userid==''){
            echo '<script type="text/javascript"> 
            alert ( "Abmeldung wegen Zeitüberschreitung" );
            location.href = "probst.html";
            </script>';
            exit;
        }
        
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
        
        /* PART 1: MANAGING PROJECTS */
        /* PART 2: BEREICH ADMIN */        
        /* PART 3: ERFASSEN
        /* PART 4: VERKAUF */
		/* Update tl_project wenn Anfrage ab editproject */
        /* ****************** */
		if($_REQUEST['trg']=='vkf'){ 
		//******************************
		//Dieser Teil, falls UPDATE angefordert > Projekt update
            if($_REQUEST['todo']=='updatepro'){
			$id=$_REQUEST['id'];
            $knr=$_REQUEST['knr'];
			$kname=$_REQUEST['kname'];
            $wohnort=$_REQUEST['wohnort'];
			$descript=$_REQUEST['descript'];
			$start=$_REQUEST['start'];
			$enddone=$_REQUEST['enddone'];
			$ladress=$_REQUEST['ladress'];
			$status=$_REQUEST['status'];
            $start1 = strtotime($start);
            if($enddone==''){ 
            $enddone1 = 0; 
            } else {
            $enddone1 = strtotime($enddone);
            }
			$sql='UPDATE tl_project SET tstamp='.time().',kname="'.$kname.'",knr="'.$knr.'",wohnort="'.$wohnort.'",descript="'.$descript.'",start="'.$start1.'",enddone="'.$enddone1.'",ladress="'.$ladress.'" WHERE id='.$id.';';
		//echo $sql;
		//$update=mysql_query($sql);
           $objResult = \Database::getInstance()->execute($sql);  
		}
		/* End Update */
		/* Insert NEW PROJECT */
        /* ****************** */  
		if($_REQUEST['todo']=='insertpro'){
			$knr=$_REQUEST['knr'];
			$kname=$_REQUEST['kname'];
            $wohnort=$_REQUEST['wohnort'];
			$descript=$_REQUEST['descript'];
			$start=$_REQUEST['start'];
			$enddone=$_REQUEST['enddone'];
			$ladress=$_REQUEST['ladress'];
            $start1 = strtotime($start);
            $enddone1 = strtotime($enddone);
			$sql='INSERT into tl_project (tstamp, knr,kname,wohnort, descript, start, enddone, ladress';
			$sql.=') VALUES ('.time().',"'.$knr.'","'.$kname.'","'.$wohnort.'","'.$descript.'","'.$start1.'","'.$enddone1.'","'.$ladress.'"';
			$sql.=');';
			echo $sql;
            $objResult = \Database::getInstance()->execute($sql);  
			$this->Template->mess = 'Neues Projekt wurde erfolgreich angelegt';
		}
		
		/* DELETE PROJECT */
        /* ****************** */
		if($_REQUEST['todo']=='delpro'){
		$id=$_REQUEST['id'];
		//$sql='DELETE FROM tl_project WHERE id='.$id.';';
		//echo $sql;
		//$delete=mysql_query($sql);
            $objResult = \Database::getInstance()->execute($sql);  
            
// Hier muss noch Routine rein > alle Leistungen/Material löschen mit diesem Projekt ! > ACHTUNG: Projekt nicht löschen, sondern inaktiv setzen
		} 
        // Alles andere erledigt > Projektliste aufbereiten    
		// Daten für Projektliste aufbereiten
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
			
		/* Ausgabe Projektliste 
		$objProjects = $this->Database->execute("SELECT *, concat(SUBSTRING(start, 7, 4),'/',SUBSTRING(start,4,2),'/',SUBSTRING(start,1,2)) as nstart FROM tl_project ".$filter." ORDER BY nstart DESC ;");
		//Schleife falls kein Projekt vorhanden
		
		$arrProjects = array();
		
		//Generate Projects
		while ($objProjects->next())
		{
		$objCosts = $this->Database->execute('SELECT SUM(amount) as cost FROM tl_costrec WHERE pid = '.$objProjects->id.';');
		$objTimes = $this->Database->execute('SELECT SUM(minutes) as times FROM tl_timerec WHERE pid = '.$objProjects->id.';');
		
		//$objCont = $this->Database->execute('SELECT * FROM tl_contact WHERE id = '.$objProjects->contactid.';');
		//$objCust = $this->Database->execute('SELECT * FROM tl_customer WHERE id = '.$objProjects->customid.';');
		//$objInvoice = $this->Database->execute('SELECT * FROM tl_invoicetext WHERE id = '.$objProjects->invoicetextid.';');
		
		$arrProjects[] = array
		(
			'id' => $objProjects->id,
			'knr' => $objProjects->knr,
            'kname' => $objProjects->kname,
            'wohnort' => $objProjects->wohnort,
			'start' => $objProjects->start,
			'enddone' => $objProjects->enddone,
			'descript' => $objProjects->descript,
			'cost' => $objCosts->cost,
			'times' => $objTimes->times,
			'ladress' => $objProjects->ladress,
			'customer' => $objProjects->customer,
			'lname' => $objCont->lname,
			'fname' => $objCont->fname,
			'firma' => $objCust->firma
		);
	}
	$this->Template->projects = $arrProjects;
		// Daten für neues Projekt > immer bereithalten
		$objCust = $this->Database->execute("SELECT * FROM tl_customer ORDER by firma;");
		$objCont = $this->Database->execute("SELECT * FROM tl_ladress ORDER by lname,fname;");
		
		//Schleife falls keine Kosten vorhanden
		//End Schleife
        */
	
            // Bereich Edit Project
	/**********************/
		if($_REQUEST['todo']=='editpro'){
		$sql='SELECT id, tstamp, knr, kname, wohnort, ladress, descript, start, enddone FROM tl_project WHERE id='.$projekt;
		$objEProject = $this->Database->execute($sql);
            //echo $sql;

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

		);
	}
	$this->Template->eproject = $arrEProject;
            
	//$this->Template->nprocont = $arrCont;
	//$this->Template->nprocust = $arrCust;
	$this->Template->pid = $_REQUEST['id'];
	$this->Template->scope = 'pledit';
		}
            
    // REGIE-RAPPORT
    //********************
        if($_REQUEST['todo']=='saveregieed'){
        // Regierapport bereitstellen
        // Daten bereitstellen
        $timearr = array();
        $tlim=count($_REQUEST['tid']);
        for($i=0;$i<$tlim;$i++){
            //Jeden Regieeintrag Zeiten auslesen > tl_timerec aktualisieren
            $tid = $_REQUEST['tid'][$i];
            $timarr[]=$tid;
            $rminutes = $_REQUEST['rminutes'][$i]*60;
            $transatz = $_REQUEST['transatz'][$i];
            $trtext = $_REQUEST['trtext'][$i];
            $trdone = mktime(0,0,0,date("m"),date("d"),date("y"));
            //höchste Regienummer für dieses Projekt holen
            $sql='SELECT trnumber as nr from tl_timerec WHERE pid = '.$_REQUEST[proj][$i].' AND tregie = 1 UNION SELECT rmnumber as nr from tl_costrec where pid = '.$_REQUEST[proj][$i].' AND mregie = 1 UNION SELECT trnumber as nr from tl_machrec WHERE pid = '.$_REQUEST[proj][$i].' AND tregie = 1';
            $objMaxnr = \Database::getInstance()->execute($sql);
            $regnrt = array();
            while($objMaxnr->next()){
                 $regnrt = $objMaxnr->nr;
            }
            $newregnr = max($regnrt)+1;
            
            $sql='UPDATE tl_timerec SET tstamp='.time().', rminutes='.$rminutes.',transatz='.$transatz.',trtext="'.$trtext.'",trdone='.$trdone.', trnumber='.$newregnr.' WHERE id='.$tid.';';
           
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
            $rmtext = $_REQUEST['rmtext'][$i];
            $rmkommentar = $_REQUEST['rmkommentar'][$i];
            $rmdone = mktime(0,0,0,date("m"),date("d"),date("y"));
            //höchste Regienummer für dieses Projekt holen
            $sql='SELECT trnumber as nr from tl_timerec WHERE pid = '.$_REQUEST[proj][$i].' AND tregie = 1 UNION SELECT rmnumber as nr from tl_costrec where pid = '.$_REQUEST[proj][$i].' AND mregie = 1 UNION SELECT trnumber as nr from tl_machrec WHERE pid = '.$_REQUEST[proj][$i].' AND tregie = 1';
            $objMaxnr = \Database::getInstance()->execute($sql);
            $regnrm = array();
            while($objMaxnr->next()){
                 $regnrm = $objMaxnr->nr;
            }
            $newregnr = max($regnrm)+1;
            $sql='UPDATE tl_costrec SET tstamp='.time().', rmamount='.$rmamount.',rmeinheit='.$rmeinheit.',rmtext="'.$rmtext.'",rmkommentar="'.$rmkommentar.'",rmdone='.$rmdone.', rmnumber='.$newregnr.' WHERE id='.$mid.';';
            $objResult = \Database::getInstance()->execute($sql); 
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
            $sql='SELECT trnumber as nr from tl_timerec WHERE pid = '.$_REQUEST[proj][$i].' AND tregie = 1 UNION SELECT rmnumber as nr from tl_costrec where pid = '.$_REQUEST[proj][$i].' AND mregie = 1 UNION SELECT trnumber as nr from tl_machrec WHERE pid = '.$_REQUEST[proj][$i].' AND tregie = 1';
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
    
    
    // 
            
    //Projekte bereitstellen mit Regieeinträgen
        $sql='SELECT pid from tl_costrec WHERE mregie = 1 AND rmdone = 0 UNION SELECT pid from tl_timerec WHERE tregie = 1 AND trdone=0 UNION SELECT pid from tl_machrec WHERE tregie = 1 AND trdone=0';
        $objRegie = \Database::getInstance()->execute($sql);
        
        // pro gefundenes Projekt  > Projektdaten in neues Array
        while ($objRegie->next())
		{
            $sql='SELECT id, concat(knr,"/",kname,"/",wohnort) as title from tl_project WHERE id='.$objRegie->pid;
            $objRegieDet = \Database::getInstance()->execute($sql);
            while ($objRegieDet->next())
            {
            $arrProRegie[]=array(
                'proid' => $objRegieDet->id,
                'ptitle' => $objRegieDet->title,
            );
            }
        }
            $this->Template->regielist = $arrProRegie;    
        
        
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
              if($objRegieMat->rmeinheit==5){$reh = "kg";}
            elseif($objRegieMat->rmeinheit==1){$reh = "l";}
            elseif($objRegieMat->rmeinheit==2){$reh = "m";}
            elseif($objRegieMat->rmeinheit==3){$reh = "m2";}
            elseif($objRegieMat->rmeinheit==4){$reh = "St.";}
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
                'rmkommentar' => $objRegieMat->rmkommentar,
                'rmeinheit' => $reh
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
        if($_REQUEST['todo']=='rconfirm'){
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
             header ( 'Location: verkauf.html?trg=vkf&todo=report&proj='.$_REQUEST['proj'] );
        }
        
        if($_REQUEST['todo']=='rleistedit'){ //Regieleistung editieren/korrigieren
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
        
            if($_REQUEST['todo']=='saverleistedit'){
                if($_REQUEST['typ']==0){
                    $tid = $_REQUEST['tid'];
                    $rminutes = $_REQUEST['rminutes']*60;
                    $transatz = $_REQUEST['transatz'];
                    $trtext = $_REQUEST['trtext'];
                    //$trdone = mktime(0,0,0,date("m"),date("d"),date("y"));
                                    
                $sql='UPDATE tl_timerec SET tstamp='.time().', rminutes='.$rminutes.',transatz='.$transatz.',trtext="'.$trtext.'" WHERE id='.$tid.';';
                echo $sql;
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
                echo $sql;
                $objResult = \Database::getInstance()->execute($sql); 
                }
                header ( 'Location: verkauf.html?trg=vkf&todo=report&proj='.$_REQUEST['proj'] );
                }  
		} // end vkf

    if($_REQUEST['todo']=='report'||$todo=='report'){
        $this->Template->todo = 'report';
    }
    if($_REQUEST['todo']=='report'||$todo=='report'){
        //Projektdaten ausliefern
        $sql="SELECT * from tl_project WHERE id=".$projekt;
        $objProjekt = $this->Database->execute($sql);
        $this->Template->ptitle = $objProjekt->knr."/".$objProjekt->kname."/".$objProjekt->wohnort;
        $this->Template->start = date("d.m.Y",$objProjekt->start);
        $this->Template->enddone = ($objProjekt->enddone == 0) ? 'nicht abgeschlossen' : date("d.m.Y", $objProjekt->enddone);
        
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
            $sql="SELECT SUM(minutes) as jsum, tl_jobs.pid as pid, catid, jobid, tl_jobs.title FROM tl_timerec, tl_jobs WHERE tl_jobs.id=jobid AND tl_timerec.pid=".$projekt." AND tl_jobs.pid = ".$sumtime->catid." GROUP by jobid;";
            
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
    $sql = 'SELECT id, trdone as done, trnumber as nr, trfinal as finale, typ, tstamp from tl_timerec WHERE tl_timerec.pid='.$_REQUEST['proj'].' AND trdone!="0" UNION SELECT id, rmdone as done, rmnumber as nr, rmfinal as finale, typ, tstamp from tl_costrec WHERE tl_costrec.pid='.$_REQUEST['proj'].' AND rmdone!="0" UNION SELECT id, trdone as done, trnumber as nr, trfinal as finale, typ, tstamp from tl_machrec WHERE tl_machrec.pid='.$_REQUEST['proj'].' AND trdone!="0"' ;    
    echo $sql;
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
   
// End Bereich Verkauf
		//**************************
    // Immer holen > dailydaten, zuerst TIME
    $sql='SELECT concat(knr,"/", kname,"/", wohnort) as ptitle, tl_jobs.title as jtitle, tl_category.title as ctitle, tl_timerec.minutes, tl_timerec.datum from tl_timerec, tl_jobs, tl_category, tl_project WHERE tl_timerec.memberid = '.$userid.' AND tl_timerec.catid = tl_category.id AND tl_timerec.jobid = tl_jobs.id AND tl_project.id = tl_timerec.pid AND tl_timerec.datum = '.strtotime("today");
        
        
        $objdailyt = $this->Database->execute($sql);
        $arrdailyt = array();
        while ($objdailyt->next()){
        $arrdailyt[] = array(
            'ptitle'=>$objdailyt->ptitle,
            'jtitle'=>$objdailyt->jtitle,
            'ctitle'=>$objdailyt->ctitle,
            'hours'=>$objdailyt->minutes/60,
        );
    }
     // Immer holen > dailydaten, SUM TIME
    $sql='SELECT sum(tl_timerec.minutes) as daily from tl_timerec, tl_project WHERE tl_timerec.memberid = '.$userid.' AND tl_project.id = tl_timerec.pid AND tl_timerec.datum = '.strtotime("today");
        
        $objdailyt = $this->Database->execute($sql); 
    
    $this->Template->dailystu = $arrdailyt;
    $this->Template->daily = $objdailyt->daily/60;
    // und dito MAT
    $sql='SELECT concat(knr,"/", kname,"/", wohnort) as ptitle, tl_costrec.title as mtitle, einheit, amount from tl_costrec, tl_project WHERE tl_costrec.memberid = '.$userid.' AND tl_project.id = tl_costrec.pid AND tl_costrec.datum = '.strtotime("today");
    $objdailym = $this->Database->execute($sql);
     $arrdailym = array();
     while ($objdailym->next()){
          if($objdailym->einheit==0){$eh = "kg";}
            elseif($objdailym->einheit==1){$eh = "l";}
            elseif($objdailym->einheit==2){$eh = "m";}
            elseif($objdailym->einheit==3){$eh = "m2";}
            elseif($objdailym->einheit==4){$eh = "St.";}
        $arrdailym[] = array(
            'mtitle'=>$objdailym->mtitle,
            'ptitle'=>$objdailym->ptitle,
            'amount'=>$objdailym->amount,
            'einheit'=>$eh,
        );
    }    
     $this->Template->dailymat = $arrdailym;        
 
    /* BEREICH 5 PROFIL*/
        if($trg='profil'){
            $sql='SELECT zeitmodell from tl_member WHERE id = '.$userid;
            $objZm = $this->Database->execute($sql);
            $zeitmod = $objZm->zeitmodell;
          
            $sql='SELECT DATE_FORMAT(FROM_UNIXTIME(datum), "%m") as Monat, sum(minutes) as minuten FROM `tl_timerec` WHERE DATE_FORMAT(FROM_UNIXTIME(datum), "%Y") AND memberid='.$userid.' GROUP BY DATE_FORMAT(FROM_UNIXTIME(datum), "%m")';
            $objSumMon = $this->Database->execute($sql);
            $arrSumMon = array();
            $i=1;
            while ($objSumMon->next())
            {
                $mon1 = ltrim($objSumMon->Monat,0);
                while ($i<>$mon1) {
                     $arrSumMon[] = array
                    (
                    'mon' =>$i,
                    'std' =>0
                );
                $i++;    
                }
                               
                $arrSumMon[] = array
                    (
                    'mon' =>$mon1,
                    'std' =>($objSumMon->minuten/60)
                );
                $i++;
                $sumMin = $sumMin + $objSumMon->minuten;
            }
            if($i<12){
                while ($i<13) {
                     $arrSumMon[] = array
                    (
                    'mon' =>$i,
                    'std' =>0
                );
                $i++;    
                }
            }
            //print_r($arrSumMon);
            //Berechnen soll pro Monat (100%)
            $start = $current = strtotime('2019-01-01');
            $end = strtotime('2020-01-01');
            $months = array();
            while($current < $end) {
            $month = date('n', $current);
            if (!isset($months[$month])) {
                $months[$month] = 0;
            }
            $months[$month]++;
            $current = strtotime('+1 weekday', $current);
            }
            // Routine zur Bestimmung mobiler Feiertage
            // 1. 1./2. Januar, 1. August, 25./26. Dez.
            // Array $months anpassen
            $tag = 24*60*60;
            $atag = (8*60 + 24)/60;
            $YY = date("Y");
            $ostern = easter_date($YY);
            $karfreitag = $ostern - (2*$tag);
            $ostermontag = $ostern + $tag;
            $pfingstmontag = $ostern + (50*$tag);
            $ersteraugust = mktime(0,0,0,8,1,date("Y"));
            //Routine jeweils für Zeitmodell 1 und Zeitmodell 2!
            if($zeitmod==1){
                 if(date("l", mktime(0,0,0,1,1,date("Y"))<>0 && date("l", mktime(0,0,0,1,1,date("Y"))<>1)))
                    {
                $months[1] = $months[1]-1; // 1. Januar
            }
            if(date("l", mktime(0,0,0,1,1,date("Y"))<>0 && date("l", mktime(0,0,0,1,2,date("Y"))<>1)))
                    {
                $months[1] = $months[1]-1; // 2. Januar
            }
            $mmo = date("n", $karfreitag);        
            $months[$mmo] = $months[$mmo]-1; // Karfreitag
            //$mmo = date("n", $ostermontag);        
            //$months[$mmo] = $months[$mmo]-1; // Ostermontag
            //$mmo = date("n", $pfingstmontag);        
            //$months[$mmo] = $months[$mmo]-1; // Pfingstmontag
            if(date("l", mktime(0,0,0,1,1,date("Y"))<>0 && date("l", mktime(0,0,0,8,1,date("Y"))<>1)))
                    {
                $months[8] = $months[1]-1; // 1. August
            }
            if(date("l", mktime(0,0,0,1,1,date("Y"))<>0 && date("l", mktime(0,0,0,12,25,date("Y"))<>1)))
                    {
                $months[12] = $months[1]-1; // 25. Dez
            }
            if(date("l", mktime(0,0,0,1,1,date("Y"))<>0 && date("l", mktime(0,0,0,12,26,date("Y"))<>1)))
                    {
                $months[12] = $months[1]-1; // 26. Dez
            } // Ende Zeitmodell 1
            } elseif($zeitmod==2){
                 if(date("l", mktime(0,0,0,1,1,date("Y"))<>0 && date("l", mktime(0,0,0,1,1,date("Y"))<>6)))
                    {
                $months[1] = $months[1]-1; // 1. Januar
            }
            if(date("l", mktime(0,0,0,1,1,date("Y"))<>0 && date("l", mktime(0,0,0,1,2,date("Y"))<>6)))
                    {
                $months[1] = $months[1]-1; // 2. Januar
            }
            $mmo = date("n", $karfreitag);        
            $months[$mmo] = $months[$mmo]-1; // Karfreitag
            $mmo = date("n", $ostermontag);        
            $months[$mmo] = $months[$mmo]-1; // Ostermontag
            $mmo = date("n", $pfingstmontag);        
            $months[$mmo] = $months[$mmo]-1; // Pfingstmontag
            if(date("l", mktime(0,0,0,1,1,date("Y"))<>0 && date("l", mktime(0,0,0,8,1,date("Y"))<>6)))
                    {
                $months[8] = $months[1]-1; // 1. August
            }
            if(date("l", mktime(0,0,0,1,1,date("Y"))<>0 && date("l", mktime(0,0,0,12,25,date("Y"))<>6)))
                    {
                $months[12] = $months[1]-1; // 25. Dez
            }
            if(date("l", mktime(0,0,0,1,1,date("Y"))<>0 && date("l", mktime(0,0,0,12,26,date("Y"))<>6)))
                    {
                $months[12] = $months[1]-1; // 26. Dez
            }
            }

            //Hole Stundensoll des Mitarbeiters
            $sql="SELECT minutesoll as soll from tl_member WHERE id = ".$userid;
            $objSoll = $this->Database->execute($sql);
            $masoll = $objSoll->soll;
            $stdsoll = array_sum($months)*$atag; 
            //Anteil masoll/jahressoll100%
            $mafakt = $masoll/$stdsoll;
            
            //Hole Ferienzeiten des Mitarbeiters
            $sql="SELECT sum(minutes)/60 as ferien from tl_timerec WHERE jobid = 3 AND memberid = ".$userid;
            $objFe = $this->Database->execute($sql);
            $maferien = $objFe->ferien;
            
            $this->Template->maferien = $maferien/$atag;
            $this->Template->stdist = $sumMin/60; 
            $this->Template->stdsoll = $stdsoll;
            $this->Template->masoll = $masoll;
            $this->Template->stdfakt = $mafakt;
            $this->Template->sumMon = $arrSumMon;
            $this->Template->sumMoll = $months;
        }
//Ende Profil
	}
}

<?php
namespace Pnwscm60\PbtimeBundle\Module 
class ModuleErfassen extends \Contao\Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_erfassen';
 
	public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ERFASSEN ###';
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
        
        $thisyear = date("Y");
        $thismo = date("m")-1;
        
        $sqls = 'SELECT monthhours, dailyhours from tl_hours WHERE pid = '.$userid.' AND foryear = '.$thisyear;
        $objMonhour = $this->Database->execute($sqls);
        $moho = explode("|",$objMonhour->dailyhours);
        $dayhour = $moho[$thismo];
        $this->Template->dayhours = $dayhour;
        //echo $mohour." ";
        //echo $objMonhour->monthhours;
        //echo $objMonhour->dailyhours;
        
        /* PART 1: MANAGING PROJECTS */
        /* PART 2: BEREICH ADMIN */

        /* PART 3: MANAGING ZEITEN + MATERIAL */
        // Start Beginn Timelist
		//**********************
	   if($trg=='tlist'){
		$this->Template->doit = 'timelist'; 
        //$this->Template->trg = 'tlist';
	
		/* Updatefunktion */
		if($_REQUEST['todo']=='updatetime'){
			$id=$_REQUEST['tid'];
			$datum=$_REQUEST['datum'];
			$minutes=$_REQUEST['zeit']*60;
			$descript=$_REQUEST['descript'];
            if($_REQUEST['regie']==1){
                $regie=1;
            } else {
                $regie=0;
            }
            //datum umwandeln in unixtime:
            $dat1 = strtotime($datum);
			$sql='UPDATE tl_timerec SET tstamp='.time().', datum="'.$dat1.'",minutes='.$minutes.'",tregie='.$regie.',descript="'.$descript.'" WHERE id='.$id.';';
		//$update=mysql_query($sql);
            //echo $sql;
            $objResult = \Database::getInstance()->execute($sql);  
		}
		/* End Update */
		/* Insertfunktion */
		if($_REQUEST['todo']=='inserttime'){
			
			$catid=$_REQUEST['cid'];
			$jobid=$_REQUEST['jid'];
			$datum=$_REQUEST['datum'];
			$minutes=$_REQUEST['zeit']*60;
			$descript=$_REQUEST['descript'];
            if($_REQUEST['regie']==1){
                $regie=1;
            } else {
                $regie=0;
            }
            
            //datum umwandeln in unixtime:
            $dat1 = strtotime($datum);
			$sql='INSERT into tl_timerec (tstamp, datum, minutes, descript, pid, catid, jobid, memberid, tregie) VALUES ('.time().','.$dat1.','.$minutes.',"'.$descript.'",'.$projekt.','.$catid.','.$jobid.','.$userid.','.$regie.');';
			$objResult = \Database::getInstance()->execute($sql);  
		}
		
		/* Deletefunktion */
		if($_REQUEST['todo']=='deltime'){
		$id=$_REQUEST['id'];
		$sql='DELETE from tl_timerec WHERE id='.$id.';'; 
		$objResult = \Database::getInstance()->execute($sql);  
        }
		
        /* TIMELIST*/
		/* Liste erstellen -> nur Einträge dieses users*/
        
        // Titel des aktuellen Projekts ausliefern
        $sql0 = 'SELECT id, concat(knr,"/", kname,"/", wohnort) as title from tl_project WHERE id = '. $projekt;
        $aktprojekt = $this->Database->execute($sql0);
		$this->Template->aktprojekt = $aktprojekt->title;
        $this->Template->aktprojektid = $aktprojekt->id;
		
        $sql2="SELECT SUM(minutes) as sum, catid, tl_category.title as ctitle FROM tl_timerec, tl_jobs, tl_category WHERE tl_jobs.id=jobid and tl_category.id = tl_jobs.pid AND tl_timerec.pid=".$projekt." AND memberid = ".$userid." GROUP BY catid";
           //echo $sql2;
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
            $sql = "SELECT *,tl_timerec.id as tid, tl_jobs.title, tl_category.title as ctitle FROM tl_timerec, tl_jobs, tl_category WHERE tl_jobs.id=jobid and tl_category.id = tl_jobs.pid AND tl_timerec.pid=".$projekt." AND memberid = ".$userid." AND tl_jobs.pid = ".$sumtime->catid." ORDER BY datum ASC;";
        // Falls nicht nur eigene Jobs zeigen > Feld ergänzen kürzel
            $objtime = $this->Database->execute($sql);
            while ($objtime->next())
		{
         $dat1 = date("d.m.Y", $objtime->datum);
		$arrtime[] = array
		(
			'id' => $objtime->tid,
            'datum' => $dat1,
			'zeit' => $objtime->minutes,
			'descript' => $objtime->descript,
			'jobtitle' => $objtime->title,
			'pid' => $objtime->pid,
			'catid' => $objtime->catid,
			'jobid' => $objtime->jobid,
			'tregie' => $objtime->tregie,
			'trdone' => $objtime->trdone,
			'trfinal' => $objtime->trfinal,
		);
		}
		}
    
    $this->Template->zeit = $arrtime;    
	$this->Template->summe = $arrsum;
    $this->Template->projekt = $projekt;
	}
	// Ende Timelist
    if($_REQUEST['todo']=='timeedit'){ //Daten für edittime bereitstellen
        $sql='SELECT *, tl_timerec.id as tid, tl_timerec.pid as pro, tl_jobs.title as jobtit, tl_category.title as cattitle from tl_timerec, tl_jobs, tl_category WHERE tl_timerec.id='.$_REQUEST['id'].' AND tl_timerec.catid=tl_category.id AND tl_timerec.jobid=tl_jobs.id';
        //echo $sql;
        $objResult = \Database::getInstance()->execute($sql); 
        
        $this->Template->tid = $objResult->tid;
        $this->Template->pro = $objResult->pro;
        $this->Template->catid = $objResult->catid;
        $this->Template->jobid = $objResult->jobid;
        $this->Template->job = $objResult->jobtit;
        $this->Template->memberid = $objResult->memberid;
        $dat1 = date("d.m.Y", $objResult->datum);
        $this->Template->dat1 = $dat1;
        $this->Template->minutes = $objResult->minutes;
        $this->Template->descript = $objResult->descript;
        $this->Template->regie = $objResult->tregie;
        $this->Template->edittime = 'edittime';
        $cattimeedit = $objResult->catid;
    }
    
     
    
        
    // Machlist
    if($trg=='machlist'){
		$this->Template->doit = 'maschinelist'; 
        //$this->Template->trg = 'machlist';
        
        
	
		/* Updatefunktion */
		if($_REQUEST['todo']=='updatemach'){
			$id=$_REQUEST['tid'];
			$datum=$_REQUEST['datum'];
			$minutes=$_REQUEST['zeit']*60;
			$descript=$_REQUEST['descript'];
            if($_REQUEST['regie']==1){
                $regie=1;
            } else {
                $regie=0;
            }
            //datum umwandeln in unixtime:
            $dat1 = strtotime($datum);
			$sql='UPDATE tl_machrec SET tstamp='.time().', datum="'.$dat1.'",minutes='.$minutes.'",tregie='.$regie.',descript="'.$descript.'" WHERE id='.$id.';';
		//$update=mysql_query($sql);
            //echo $sql;
            $objResult = \Database::getInstance()->execute($sql);  
		}
		/* End Update */
		/* Insertfunktion */
		if($_REQUEST['todo']=='insertmach'){
			
			$catid=$_REQUEST['cid'];
			$jobid=$_REQUEST['jid'];
			$datum=$_REQUEST['datum'];
			$minutes=$_REQUEST['zeit']*60;
			$descript=$_REQUEST['descript'];
            if($_REQUEST['regie']==1){
                $regie=1;
            } else {
                $regie=0;
            }
            echo 'einfügen!';
            //datum umwandeln in unixtime:
            $dat1 = strtotime($datum);
			$sql='INSERT into tl_machrec (tstamp, datum, minutes, descript, pid, catid, jobid, memberid, tregie) VALUES ('.time().','.$dat1.','.$minutes.',"'.$descript.'",'.$projekt.','.$catid.','.$jobid.','.$userid.','.$regie.');';
			$objResult = \Database::getInstance()->execute($sql);  
            echo $sql;
		}
		
		/* Deletefunktion */
		if($_REQUEST['todo']=='delmach'){
		$id=$_REQUEST['id'];
		$sql='DELETE from tl_machrec WHERE id='.$id.';'; 
		$objResult = \Database::getInstance()->execute($sql);  
        }
		
        /* MACHLIST*/
		/* Liste erstellen -> nur Einträge dieses users*/
        
        // Titel des aktuellen Projekts ausliefern
        $sql0 = 'SELECT id, concat(knr,"/", kname,"/", wohnort) as title from tl_project WHERE id = '. $projekt;
        $aktprojekt = $this->Database->execute($sql0);
		$this->Template->aktprojekt = $aktprojekt->title;
        $this->Template->aktprojektid = $aktprojekt->id;
		
        $sql2="SELECT SUM(minutes) as sum, catid, tl_category.title as ctitle FROM tl_machrec, tl_jobs, tl_category WHERE tl_jobs.id=jobid and tl_category.id = tl_jobs.pid AND tl_machrec.pid=".$projekt." AND memberid = ".$userid." GROUP BY catid";
           //echo $sql2;
		$sumtime = $this->Database->execute($sql2);
           
		$arrtime= array();
		$arrsum= array();
		while ($sumtime->next())
		{
		$arrsummach[] = array
		(
			'summe' => $sumtime->sum,
			'catid' => $sumtime->catid,
			'ctitle' => $sumtime->ctitle
		);
            $sql = "SELECT *,tl_machrec.id as tid, tl_jobs.title, tl_category.title as ctitle FROM tl_machrec, tl_jobs, tl_category WHERE tl_jobs.id=jobid and tl_category.id = tl_jobs.pid AND tl_machrec.pid=".$projekt." AND memberid = ".$userid." AND tl_jobs.pid = ".$sumtime->catid." ORDER BY datum ASC;";
        // Falls nicht nur eigene Jobs zeigen > Feld ergänzen kürzel
            $objtime = $this->Database->execute($sql);
            while ($objtime->next())
		{
         $dat1 = date("d.m.Y", $objtime->datum);
		$arrmach[] = array
		(
			'id' => $objtime->tid,
            'datum' => $dat1,
			'zeit' => $objtime->minutes,
			'descript' => $objtime->descript,
			'jobtitle' => $objtime->title,
			'pid' => $objtime->pid,
			'catid' => $objtime->catid,
			'jobid' => $objtime->jobid,
			'tregie' => $objtime->tregie,
			'trdone' => $objtime->trdone,
			'trfinal' => $objtime->trfinal,
		);
		}
		}
        
    $this->Template->mach = $arrmach;    
	$this->Template->summach = $arrsummach;
    $this->Template->projekt = $projekt;
    
	}
	// Ende Machlist
    if($_REQUEST['todo']=='machedit'){ //Daten für edittime bereitstellen
        $sql='SELECT *, tl_machrec.id as tid, tl_machrec.pid as pro, tl_jobs.title as jobtit, tl_category.title as cattitle from tl_machrec, tl_jobs, tl_category WHERE tl_machrec.id='.$_REQUEST['id'].' AND tl_machrec.catid=tl_category.id AND tl_machrec.jobid=tl_jobs.id';
        //echo $sql;
        $objResult = \Database::getInstance()->execute($sql); 
        
        $this->Template->machid = $objResult->tid;
        $this->Template->mpro = $objResult->pro;
        $this->Template->mcatid = $objResult->catid;
        $this->Template->mjobid = $objResult->jobid;
        $this->Template->mjob = $objResult->jobtit;
        $this->Template->mmemberid = $objResult->memberid;
        $dat1 = date("d.m.Y", $objResult->datum);
        $this->Template->mdat1 = $dat1;
        $this->Template->mminutes = $objResult->minutes;
        $this->Template->mdescript = $objResult->descript;
        $this->Template->machregie = $objResult->tregie;
        $this->Template->editmach = 'editmach';
        $cattimeedit = $objResult->catid;
    }
	
        
    // Start Beginn Matlist
		//**********************
	   if($trg=='vlist'){
		$this->Template->doit = 'matlist'; 
        //$this->Template->trg = 'vlist';
           
		/* Updatefunktion */
		if($_REQUEST['todo']=='updatemat'){
			$id=$_REQUEST['mid'];
			$datum=$_REQUEST['datum'];
            $title=$_REQUEST['mtitle'];
			$einheit=$_REQUEST['einheit'];
            $amount=$_REQUEST['amount'];
			$descript=$_REQUEST['descript'];
            if($_REQUEST['regie']==1){
                $regie=1;
            } else {
                $regie=0;
            }
            //datum umwandeln in unixtime:
            $dat1 = strtotime($datum);
			$sql='UPDATE tl_costrec SET tstamp='.time().', datum="'.$dat1.'",title="'.$title.'",einheit='.$einheit.',amount='.$amount.',descript="'.$descript.'",mregie='.$regie.' WHERE id='.$id.';';
		
            $objResult = \Database::getInstance()->execute($sql);  
		}
		/* End Update */
		/* Insertfunktion */
		if($_REQUEST['todo']=='newmat'){
			$projekt=$_REQUEST['proj'];
			$datum=$_REQUEST['datum'];
            $title=$_REQUEST['mtitle'];
			$einheit=$_REQUEST['einheit'];
            $amount=$_REQUEST['amount'];
			$descript=$_REQUEST['descript'];
            if($_REQUEST['regie']==1){
                $regie=1;
            } else {
                $regie=0;
            }
            //datum umwandeln in unixtime:
            $dat1 = strtotime($datum);
            $jetzt = time();
			$sql='INSERT into tl_costrec (tstamp, datum, title, einheit, amount, descript, pid, memberid, mregie) VALUES ('.$jetzt.','.$dat1.',"'.$title.'",'.$einheit.','.$amount.',"'.$descript.'",'.$projekt.','.$userid.','.$regie.');';
			$objResult = \Database::getInstance()->execute($sql);  
            //$insert=mysql_query($sql);
		}
		
		/* Deletefunktion */
		if($_REQUEST['todo']=='delmat'){
		$id=$_REQUEST['id'];
		$sql='DELETE from tl_costrec WHERE id='.$id.';'; 
		//$delete=mysql_query($sql);
		$objResult = \Database::getInstance()->execute($sql);  
        }
		
        /* MATLIST*/
		/* Liste erstellen -> nur Einträge dieses users*/
        
        // Titel des aktuellen Projekts ausliefern
        $sql0 = 'SELECT id, concat(knr,"/", kname,"/", wohnort) as title from tl_project WHERE id = '. $projekt;
        $aktprojekt = $this->Database->execute($sql0);
		$this->Template->aktprojekt = $aktprojekt->title;
        $this->Template->aktprojektid = $aktprojekt->id;
		
        $sql = "SELECT * FROM tl_costrec WHERE tl_costrec.pid=".$projekt." AND memberid = ".$userid." ORDER BY datum ASC;";
        // Falls nicht nur eigene Jobs zeigen > Feld ergänzen kürzel
        $objmat = $this->Database->execute($sql);
		
		$arrmat= array();          
        while ($objmat->next())
		{
            $dat1 = date("d.m.Y", $objmat->datum);
            if($objmat->einheit==5){$eh = "kg";}
            elseif($objmat->einheit==1){$eh = "l";}
            elseif($objmat->einheit==2){$eh = "m";}
            elseif($objmat->einheit==3){$eh = "m2";}
            elseif($objmat->einheit==4){$eh = "St.";}
		$arrmat[] = array
		(
			'id' => $objmat->id,
			'datum' => $dat1,
            'title' => $objmat->title,
			'einheit' => $eh,
            'amount' => $objmat->amount,
			'descript' => $objmat->descript,
			'pid' => $objmat->pid,
            'rmdone' => $objmat->rmdone,
            'rmfinal' => $objmat->rmfinal,
		);
		}
        
    $this->Template->mat = $arrmat;
    $this->Template->projekt = $projekt;
	}
	// Ende Matlist
    if($_REQUEST['todo']=='editmat'){ //Daten für editmat bereitstellen
        $sql='SELECT *, tl_costrec.id as mid, tl_costrec.pid as pro, tl_costrec.title as mtit from tl_costrec WHERE tl_costrec.id='.$_REQUEST['id'];
        
        $objResult = \Database::getInstance()->execute($sql); 
        
        $this->Template->mid = $objResult->mid;
        $this->Template->pro = $objResult->pro;
        $this->Template->memberid = $objResult->memberid;
        $dat1 = date("d.m.Y", $objResult->datum);
        $this->Template->dat1 = $dat1;
        $this->Template->title = $objResult->mtit;
        $this->Template->amount = $objResult->amount;
        $this->Template->einheit = $objResult->einheit;
        $this->Template->descript = $objResult->descript;
        $this->Template->regie = $objResult->mregie;
        $this->Template->editmat = 'editmat';
    }       
        
        
	// *** BEREICH KATEGORIEWECHSEL / JOBS
	// **********************
	// abhängig davon, ob tlist oder mechlist
	// Immer bereithalten, wenn Timelist aufgerufen wird > Teil von timelist
			
		if($_REQUEST['do']=='newtime'){
			$this->Template->newtime = 'newtime'; //
		}
        if($_REQUEST['do']=='newmach'){
			$this->Template->newmach = 'newmach'; //
		}
		if($_REQUEST['trg']=='tlist'||$_REQUEST['trg']=='machlist'){
        //muss in tlist und mechlist funktionieren 
			
		// id des Projekts: $pid
		$this->Template->catFlag = 0; // Standard, bei Katwechsel überschrieben
		// hole alle Jobs, geordnet nach Kategorien, Jobs
		
		// Katauswahl > immer bereithalten für Wechsel, abhängig von trg
        if($_REQUEST['trg']=='tlist'){
             $sql="SELECT tl_category.id as cid, tl_category.title as ctitle FROM tl_category WHERE id <> 1 AND typ = 0 AND id!=1 AND id!=6 AND id!=10 AND id!=25 ORDER by ctitle";
        }
        if($_REQUEST['trg']=='machlist'){
             $sql="SELECT tl_category.id as cid, tl_category.title as ctitle FROM tl_category WHERE id <> 1 AND typ = 1 ORDER by ctitle";
        }
		$objCat = $this->Database->execute($sql);
			while ($objCat->next())
		{
			$arrCat[] = array(
				'cid' => $objCat->cid,
				'ctitle' => $objCat->ctitle,
			);
		}
		$this->Template->changeCat = $arrCat;
		
		if($_REQUEST['changecat']==1){ // Flag setzen > Kategorien einblenden, anderes Formular ausblenden
			$this->Template->catFlag = 1;
		//echo "ChangeCat";
		
		}
		if($_REQUEST['catchosen']!=''){ //Anfrage für andere Kat gesetzt
			$newpref = $_REQUEST['catchosen'];
            
                $sql="SELECT tl_jobs.title as jtitle, tl_category.title as ctitle, tl_category.id as cid, tl_jobs.id as jid FROM tl_jobs, tl_category WHERE tl_category.id=tl_jobs.pid AND tl_category.id=".$newpref." ORDER by pid, jtitle";

			$objJob = $this->Database->execute($sql);
			$result = $this->Database->prepare("SELECT title FROM tl_category WHERE id=?")->execute($newpref);
			while ($objJob->next())
		{
			$arrJob[] = array(
				'jid' => $objJob->jid,
				'cid' => $objJob->cid,
				'ctitle' => $objJob->ctitle,
				'jtitle' => $objJob->jtitle
			);
		}
		$this->Template->catFlag = 0;
        } elseif($cattimeedit!=''){ //Kategorie des bestehenden Time-eintrags nehmen
			$newpref = $cattimeedit;
            $sql="SELECT tl_jobs.title as jtitle, tl_category.title as ctitle, tl_category.id as cid, tl_jobs.id as jid FROM tl_jobs, tl_category WHERE tl_category.id=tl_jobs.pid AND tl_category.id=".$newpref." ORDER by pid, jtitle";
        
			$objJob = $this->Database->execute($sql);
			$result = $this->Database->prepare("SELECT title FROM tl_category WHERE id=?")->execute($newpref);
			while ($objJob->next())
		{
			$arrJob[] = array(
				'jid' => $objJob->jid,
				'cid' => $objJob->cid,
				'ctitle' => $objJob->ctitle,
				'jtitle' => $objJob->jtitle
			);
		}
		$this->Template->catFlag = 0;	
		} else { // Standard = eingetragene Pref des Users oder falls Absenzen > cat = Absenzen oder falls Maschine = Fahrzeuge
			$this->import("FrontendUser","User");
			$userid = $this->User->id;
            $cpref = ($projekt==5) ? 1 : ($trg=='machlist' ? 11 : ($projekt==7 ? 10 : ($projekt==9 ? 6 : ($projekt==10 ? 25 : $this->User->cpref))));
			$objJob = $this->Database->execute("SELECT tl_jobs.title as jtitle, tl_category.title as ctitle, tl_category.id as cid, tl_jobs.id as jid FROM tl_jobs, tl_category WHERE tl_category.id=tl_jobs.pid AND tl_category.id = ".$cpref." ORDER by pid, jtitle");
			$result = $this->Database->prepare("SELECT title FROM tl_category WHERE id=?")->execute($cpref);
			while ($objJob->next())
		{
			$arrJob[] = array(
				'jid' => $objJob->jid,
				'cid' => $objJob->cid,
				'ctitle' => $objJob->ctitle,
				'jtitle' => $objJob->jtitle
			);
		}
		}
	$this->Template->njob = $arrJob;
	$this->Template->proj = $projekt;
	$this->Template->cat = $result->title;
	$this->Template->pid = $pid;
	$this->Template->cid = $objJob->cid;
	//$this->Template->trg = "tlist";
		}
    
        
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
    //Daily für Machine
        $sql='SELECT concat(knr,"/", kname,"/", wohnort) as ptitle, tl_jobs.title as jtitle, tl_category.title as ctitle, tl_machrec.minutes, tl_machrec.datum from tl_machrec, tl_jobs, tl_category, tl_project WHERE tl_machrec.memberid = '.$userid.' AND tl_machrec.catid = tl_category.id AND tl_machrec.jobid = tl_jobs.id AND tl_project.id = tl_machrec.pid AND tl_machrec.datum = '.strtotime("today");
        
        $objdailymech = $this->Database->execute($sql);
        $arrdailymech = array();
        while ($objdailymech->next()){
        $arrdailymech[] = array(
            'mptitle'=>$objdailymech->ptitle,
            'mjtitle'=>$objdailymech->jtitle,
            'mctitle'=>$objdailymech->ctitle,
            'mhours'=>$objdailymech->minutes/60,
        );
    }
        $this->Template->dailymach = $arrdailymech;
        $this->Template->dailym = $objdailymech->daily/60; 
        
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
	}
}

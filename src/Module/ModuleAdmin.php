<?php
namespace Pnwscm60\PbtimeBundle\Module
class ModuleAdmin extends Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_admin';
 
	public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ADMIN ###';
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
        
        if($_REQUEST['trg']=='admin'){
            
        //Kategorie einfügen
        if($_REQUEST['todo']=='insertcat'){
            $ctitle = $_REQUEST['title'];
            $description = $_REQUEST['descript'];
            $typ = $_REQUEST['ctyp'];
            $sql='INSERT into tl_category (tstamp,title,description,typ) VALUES ('.time().',"'.$ctitle.'","'.$description.'",'.$typ.');';
            $objResult = \Database::getInstance()->execute($sql);
            $this->Template->mess = "Kategorie wurde erfolgreich eingefügt";
        }
        //Kategorie bearbeiten
        if($_REQUEST['todo']=='edittcat'){
            $cid = $_REQUEST['cid'];
            $ctitle = $_REQUEST['title'];
            $description = $_REQUEST['descript'];
            $typ = $_REQUEST['ctyp'];
            $sql='UPDATE tl_category SET tstamp = '.time().', title = "'.$ctitle.'", description="'.$description.'", typ = '.$typ.' WHERE id ='.$cid;
            $objResult = \Database::getInstance()->execute($sql);
            $this->Template->mess = "Kategorie wurde erfolgreich geändert";
            
           echo '<script type="text/javascript"> 
            location.href = "admin.html?trg=admin&todo=clist";
            </script>';
            exit;
        }
        //Kategorie löschen
            if($_REQUEST['todo']=='delcat'){
            /*    
            $cid = $_REQUEST['cid'];
            $sql='DELETE from tl_category WHERE id='.$cid.';'; 
		  $objResult = \Database::getInstance()->execute($sql);
            $this->Template->mess = "Kategorie wurde erfolgreich gelöscht";
            */
            echo '<script type="text/javascript"> 
            alert ( "Löschen der Kategorie führt zu Dateninkonsistenz. Bitte um Löschung an Admin melden." );
            location.href = "admin.html?trg=admin&todo=clist";
            </script>';
            exit;
        }
        
        //Leistung einfügen
        if($_REQUEST['todo']=='insertjob'){
            $jtitle = $_REQUEST['title'];
            $cid = $_REQUEST['cid'];
            $description = $_REQUEST['descript'];
            //Typ der Leistung bestimmen
            $sql='SELECT typ from tl_category WHERE id = '.$cid;
            $objTyp = \Database::getInstance()->execute($sql);
            $typ = $objTyp->typ;
            $sql='INSERT into tl_jobs (tstamp,pid,title,description,typ) VALUES ('.time().','.$cid.',"'.$jtitle.'","'.$description.'",'.$typ.');';
            $objResult = \Database::getInstance()->execute($sql);
            $this->Template->mess = "Leistung wurde erfolgreich eingefügt";
        
        echo '<script type="text/javascript"> 
            location.href = "admin.html?trg=admin&todo=jlist";
            </script>';
            exit;
            }
        //Job bearbeiten
        if($_REQUEST['todo']=='edittjob'){
       		$jid = $_REQUEST['jid'];
            $cid = $_REQUEST['cid'];
            //Typ der Leistung bestimmen
            $sql='SELECT typ from tl_category WHERE id = '.$cid;
            $objTyp = \Database::getInstance()->execute($sql);
            $typ = $objTyp->typ;
            $jtitle = $_REQUEST['title'];
            $description = $_REQUEST['descript'];

            $sql='UPDATE tl_jobs SET tstamp = '.time().',pid ='.$cid.', title = "'.$jtitle.'", description="'.$description.'", typ='.$typ.' WHERE id ='.$jid;
            $objResult = \Database::getInstance()->execute($sql);
            $this->Template->mess = "Leistung wurde erfolgreich geändert";
            echo '<script type="text/javascript"> 
            location.href = "admin.html?trg=admin&todo=jlist";
            </script>';
            exit;
        }
        //Job löschen
             if($_REQUEST['todo']=='deljob'){
            /* $jid = $_REQUEST['jid'];
            $sql='DELETE from tl_jobs WHERE id='.$jid.';'; 
		  $objResult = \Database::getInstance()->execute($sql);
            $this->Template->mess = "Arbeitsbereich wurde erfolgreich gelöscht";*/
            echo '<script type="text/javascript"> 
            alert ( "Löschen der Leistung führt zu Dateninkonsistenz. Bitte um Löschung an Admin melden." );
            location.href = "admin.html?trg=admin&todo=jlist";
            </script>';
            exit;
        }
        
        //Daten bereitstellen        
        //Kategorien bereitstellen
        $sql="SELECT tl_category.id as cid, tl_category.title as ctitle, tl_category.description as cdescription, tl_category.typ as ctyp FROM tl_category ORDER by ctyp, ctitle";
		$objCat = $this->Database->execute($sql);
            while ($objCat->next())
		{
			$arrCat[] = array(
				'cid' => $objCat->cid,
				'ctitle' => $objCat->ctitle,
                'cdescription' => $objCat->cdescription,
                'ctyp' => $objCat->ctyp,
			);
		}
        $this->Template->allcat = $arrCat;
       // $this->Template->alcat = $arrCat;
        //Jobs bereitstellen    
        $sql="SELECT tl_jobs.id as jid, tl_jobs.title as jtitle, tl_jobs.pid, tl_jobs.description as jdescription, tl_jobs.typ as jtyp FROM tl_jobs ORDER by pid,jtitle";
		$objJob = $this->Database->execute($sql);
			while ($objJob->next())
		{
			$arrJob[] = array(
				'jid' => $objJob->jid,
                'pid' => $objJob->pid,
				'jtitle' => $objJob->jtitle,
                'jdescription' => $objJob->jdescription,
                'jtyp' => $objJob->jtyp,
			);
		}
            $this->Template->alljob = $arrJob;    
        //Daten vorbereiten für Editformular Kategorien
        if($_REQUEST['todo']=='editcat'){
            $cid = $_REQUEST['cid'];
            $sql="SELECT tl_category.id as cid, tl_category.title as ctitle, tl_category.description as cdescription, tl_category.typ as ctyp FROM tl_category WHERE id = ".$cid;
            $objnCat = $this->Database->execute($sql);
            $this->Template->cid = $objnCat->cid;
            //echo $objnCat->ctitle;
            $this->Template->catitle = $objnCat->ctitle;
            $this->Template->cdescription = $objnCat->cdescription;
            $this->Template->ctyp = $objnCat->ctyp;
            $this->Template->showCEdit = 1;
        }
        //Daten vorbereiten für Editformular Jobs
        if($_REQUEST['todo']=='editjob'){
            $jid = $_REQUEST['job'];
            $sql="SELECT tl_jobs.id as jid, tl_jobs.title as jtitle, tl_jobs.description as jdescription, tl_jobs.pid as cid, tl_jobs.typ as jtyp FROM tl_jobs WHERE id = ".$jid;

            $objnJob = $this->Database->execute($sql);
            $this->Template->jid = $objnJob->jid;
            $this->Template->jtitle = $objnJob->jtitle;
            $this->Template->jdescription = $objnJob->jdescription;
            $this->Template->cid = $objnJob->cid;
            $this->Template->jtyp = $objnJob->jtyp;
            $this->Template->showJEdit = 1;
        }
            // member speichern nach editieren
        if($_REQUEST['todo']=='editmembersave'||$_REQUEST['todo']=='newmembersave'){
            $mid = $_REQUEST['mid'];
            $lastname=$_REQUEST['lastname'];
            $firstname=$_REQUEST['firstname'];
            $nkurz=$_REQUEST['nkurz'];
            $email=$_REQUEST['email'];
            $cpref=$_REQUEST['cpref'];
            //$minutesoll=$_REQUEST['minutesoll'];
            $zeitmodell=$_REQUEST['zeitmodell'];
            $groups=serialize($_REQUEST['groups']);
            $passwort = '$2y$12$AUkDbvZt45peh6/e0wd22uelLCOR.ZdUqNvOliFQvkOUXkMl.4vsG';
            if($_REQUEST['todo']=='editmembersave'){
               $sql="UPDATE tl_member SET lastname='".$lastname."', firstname='".$firstname."', nkurz='".$nkurz."', email='".$email."', cpref=".$cpref.", zeitmodell=".$zeitmodell.", groups='".$groups."' WHERE id=".$mid.";";
                $objsMarb = $this->Database->execute($sql);
                $message = 'Mitarbeiterdaten erfolgreich geändert'; 
            } //minutesoll=".$minutesoll.",
			
            if($_REQUEST['todo']=='newmembersave'){
                $sql='INSERT into tl_member (tstamp,lastname,firstname,nkurz,email,cpref,zeitmodell,login,username,password) VALUES ('.time().',"'.$lastname.'","'.$firstname.'","'.$nkurz.'","'.$email.'",'.$cpref.','.$zeitmodell.',1,"'.$nkurz.'","'.$passwort.'");';
                 $objsMarb = $this->Database->execute($sql);
                $message = 'Mitarbeiter erfolgreich angelegt'; 
            }
            $this->Template->message = $message;
            $this->Template->mtyp = 1;
            $this->Template->todo = 'marblist';
        }
            //
            if($_REQUEST['todo']=="saveupdatehours"){
				$prozent = $_REQUEST['prozent'];
				$yearhours = $_REQUEST['yearhours'];
				$yearhoursaldo = $_REQUEST['yearhoursaldo'];
				$ferien = $_REQUEST['ferien'];
				$feriensaldo = $_REQUEST['feriensaldo'];
				$foryear = $_REQUEST['jahr'];
				$monsoll = $_REQUEST['monsoll'];
                $daysoll = $_REQUEST['daysoll'];
                $monday = $_REQUEST['mondays'];
                $montage = implode('|',$monday);
                $monathours = implode('|',$monsoll);
                $dailyhours = implode('|',$daysoll);
                //echo $montage;
                //echo $monathours;
                for($i=0;$i<12;$i++){
                    $dayhour .= number_format($monsoll[$i]/$monday[$i],2).",";
                }
                $dailyhours = substr($dayhour,0,-1);
                
				//haben wir schon Datensatz für diese mid && dieses Jahr?
                $sqltest = 'SELECT id, foryear, pid from tl_hours WHERE foryear = '.$foryear.' AND pid ='.$_REQUEST['mid'];
                //echo $sqltest."<br/>";
                $dotest = $this->Database->execute($sqltest);
                if($dotest->numRows>0){
                    $sql = 'UPDATE tl_hours SET pid='.$_REQUEST['mid'].', foryear='.$foryear.', tstamp='.time().', prozent='.$prozent.', yearhours='.$yearhours.', yearhoursaldo='.$yearhoursaldo.', ferien='.$ferien.', feriensaldo='.$feriensaldo.', monthhours="'.$monathours.'", dailyhours="'.$dailyhours.'" WHERE id='.$dotest->id;
                    //echo $sql;
                    $doit = $this->Database->execute($sql);
                } else {
				$sql='INSERT into tl_hours (pid,foryear,tstamp,prozent,yearhours,yearhoursaldo,ferien,feriensaldo,monthhours,dailyhours) VALUES ('.$_REQUEST['mid'].','.$foryear.','.time().','.$prozent.','.$yearhours.','.$yearhoursaldo.','.$ferien.','.$feriensaldo.',"'.$monathours.'","'.$dailyhours.'");';				
			     $doit = $this->Database->execute($sql);
                }
                // Daten bereitstellen, um MA direkt anzuzeigen
                $gomitarbeiter=1;
            }
            //Daten für Mitarbeiterliste > immer bereit halten
            $sql = 'select concat(lastname," ",firstname) as mname, nkurz, id from tl_member ORDER by mname;';
            $objMarb = $this->Database->execute($sql);
            $arrMarb = array();
            while($objMarb->next()){
                $arrMarb[] = array(
                'mid' => $objMarb->id,
                'mname' => $objMarb->mname,
                'nkurz' => $objMarb->nkurz,
                );
            }
            $this->Template->member = $arrMarb;  
             //Zusätzlich Kategorien bereitstellen
                $sql = 'SELECT id, title from tl_category order by title;';
                $objmCat = $this->Database->execute($sql);
                
                while($objmCat->next()){
                    $arrmCat[] = array(
                        'cid' => $objmCat->id,
                        'ctitle' => $objmCat->title
                    );
                }
                $this->Template->mcat = $arrmCat;
            
            //Daten einzelner Marb nur bereitstellen, falls angefordert:
            if($_REQUEST['todo'] == 'editmarb'||$gomitarbeiter==1){
                $sql = 'select lastname, firstname, email, nkurz, cpref, zeitmodell,groups from tl_member where id='.$_REQUEST['mid'];
                
                $objMar = $this->Database->execute($sql);
                    
                    $groups = deserialize($objMar->groups);
                    $this->Template->lastname = $objMar->lastname;
                    $this->Template->firstname = $objMar->firstname;
                    $this->Template->nkurz = $objMar->nkurz;
                    $this->Template->email = $objMar->email;
                    $this->Template->cpref = $objMar->cpref;
                    $this->Template->zeitmodell = $objMar->zeitmodell;
                    $this->Template->groups = deserialize($objMar->groups);
                $this->Template->todo = 'editmarb';
                $this->Template->mid = $_REQUEST['mid'];
               
            }
    }
        /* PART 3: MANAGING ZEITEN + MATERIAL */
        /* PART 4: VERKAUF */
 
    /* BEREICH 5 PROFIL*/
        if($trg='admin'&&($_REQUEST['todo']=='editmarb'||$gomitarbeiter==1)){
            
            $sql='SELECT zeitmodell from tl_member WHERE id = '.$_REQUEST['mid'];
            $objZm = $this->Database->execute($sql);
            $zeitmod = $objZm->zeitmodell;
         
            //Berechnen soll pro Monat (100%)
            $ajahr = date("Y");
            $njahr = $ajahr+1; //nextyear
            $nnjahr = $ajahr+2; //overnextyear
            $ljahr = $ajahr-1; //lastyear
            $this->Template->ajahr = $ajahr;
            $this->Template->njahr = $njahr;
            $this->Template->ljahr = $ljahr;
            
            $sdiesjahr = $ajahr.'-01-01'; //2019
            $ediesjahr = $njahr.'-01-01'; //2020
            $ldiesjahr = $ljahr.'-01-01'; //2018
            $ndiesjahr = $nnjahr.'-01-01'; //2018
            
            //Start/Ende aktuell
            $start_a = strtotime($sdiesjahr);
            $end_a = strtotime($ediesjahr);
            
            //Start/Ende last
            $start_l = strtotime($ldiesjahr);
            $end_l = strtotime($sdiesjahr);
            
            //Start/Ende next
            $start_n = strtotime($ediesjahr);
            $end_n = strtotime($ndiesjahr);
            
            //Durchlaufen mit allen drei Varianten
			$yearvar = array("a", "l", "n");
			foreach($yearvar as $yearvr){
			switch($yearvr){
				case "a"://Variante aktuell
            	$current = $start_a;
				$endcurrent= $end_a;
					break;
				case "l": //last year
				$current = $start_l;
				$endcurrent= $end_l;
					break;
				case "n": //next year
				$current = $start_n;
				$endcurrent= $end_n;
					break;
			}
            $months = array();
            while($current < $endcurrent) {
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
            $atag = 8.40;//(8*60 + 24)/60;
            $YY = date("Y", $current);
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
			switch($yearvr){
				case "a"://Variante aktuell
            	$this->Template->sumMolla = $months;
				$this->Template->sumSolla= array_sum($months)*$atag;
				$this->Template->sumMoll = $months;
					break;
				case "l": //last year
				$this->Template->sumMolll = $months;
				$this->Template->sumSolll= array_sum($months)*$atag;
					break;
				case "n": //next year
				$this->Template->sumMolln = $months;
				$this->Template->sumSolln= array_sum($months)*$atag;
					break;
			}     
			}
			
            //Hole Stundensoll des Mitarbeiters für die 3 Varianten
            $sql='SELECT * from tl_hours WHERE pid = '.$_REQUEST['mid'];
            //echo $sql;
            $objSoll = $this->Database->execute($sql);
            while($objSoll->next()){
				$monthhours = explode("|",$objSoll->monthhours);
                $dayhours = explode("|",$objSoll->dailyhours);
				$masoll[] = array(
					'foryear' => $objSoll->foryear,
					'prozent' => $objSoll->prozent,
					'yearhours' => $objSoll->yearhours,
					'yearhoursaldo' =>$objSoll->yearhoursaldo,
					'monthhours' => $monthhours,
                    'dayhours' => $dayhours,
					'ferien' => $objSoll->ferien,
					'feriensaldo' => $objSoll->feriensaldo,
					'comment' => $objSoll->comment,
				);
			
            }
            //var_dump($monthhours);
			$this->Template->masoll = $masoll;
			
            //$stdsoll = array_sum($months)*$atag;
            //$stdproz = $masoll/$stdsoll;
            //Anteil masoll/jahressoll100%
            //$mafakt = $masoll/$stdsoll;
			
			//Stundensummation pro Monat pro Mitarbeiter
			//aktuelles Jahr
            $sql='SELECT MONTH(FROM_UNIXTIME(datum)) as mo, sum(minutes)/60 as ist_a from tl_timerec WHERE datum>='.$start_a.' AND datum<'.$end_a.' AND catid != 1 AND typ = 0 AND memberid = '.$_REQUEST['mid'].' group by MONTH(FROM_UNIXTIME(datum))';
			$objIst = \Database::getInstance()->execute($sql);
			while($objIst->next()){
				$Ista[] = array(
						'mo' => $objIst->mo,
						'ist_a' => $objIst->ist_a,
						);
			}
			for($i=0;$i<12;$i++){
				$Issta[$i] = 0;
				foreach($Ista as $isar){
					if($isar['mo']==($i)){
						$Issta[$i] = $isar['ist_a'];
				} 
			}
			}
            $this->Template->sumista = array_sum($Issta);
            $this->Template->ist_a = $Issta;

			$sql='SELECT MONTH(FROM_UNIXTIME(datum)) as mo, sum(minutes)/60 as ist_l from tl_timerec WHERE datum>='.$start_l.' AND datum<'.$end_l.' AND catid != 1 AND typ = 0 AND memberid = '.$_REQUEST['mid'].' group by MONTH(FROM_UNIXTIME(datum))';
			$objIst = $this->Database->execute($sql);
			while($objIst->next()){
				$Istl[] = array(
						'mo' => $objIst->mo,
						'ist_l' => $objIst->ist_l,
						);
			}
			for($i=0;$i<12;$i++){
				$Isstl[$i] = 0;
				foreach($Ist as $islr){
					if($islr['mo']==($i)){
						$Isstl[$i] = $islr['ist_l'];
				} 
			}
			}
			$this->Template->ist_l = $Isstl;
            $this->Template->sumistl = array_sum($Isstl);
            
			$sql='SELECT MONTH(FROM_UNIXTIME(datum)) as mo, sum(minutes)/60 as ist_n from tl_timerec WHERE datum>='.$start_n.' AND datum<'.$end_n.' AND catid != 1 AND typ = 0 AND memberid = '.$_REQUEST['mid'].' group by MONTH(FROM_UNIXTIME(datum))';
			$objIst = $this->Database->execute($sql);
			while($objIst->next()){
				$Istn[] = array(
						'mo' => $objIst->mo,
						'ist_n' => $objIst->ist_n,
						);
			}
			for($i=0;$i<12;$i++){
				$Isstn[$i] = 0;
				foreach($Istn as $isnr){
					if($isnr['mo']==($i)){
						$Isstn[$i] = $isnr['ist_n'];
					} 
				}
			}
			$this->Template->ist_n = $Isstn;
            $this->Template->sumistn = array_sum($Isstn);
			
			//Summation Ferienzeiten/Absenzen Mitarbeiter pro Jahr
            //Hole Ferienzeiten des Mitarbeiters
            $sql="SELECT sum(minutes)/60 as ferienist from tl_timerec WHERE datum>='".$start_a."' AND datum<'".$end_a."' AND jobid = 3 AND memberid = ".$_REQUEST['mid'];
			$objFe = $this->Database->execute($sql);

            $this->Template->ferienista = $objFe->ferienist/$atag;
            //echo $objFe->ferienist/$atag;
            $sql="SELECT sum(minutes)/60 as ferienist from tl_timerec WHERE datum>='.$start_n.' AND datum<'.$end_n.' AND jobid = 3 AND memberid = ".$_REQUEST['mid'];
			$objFe = $this->Database->execute($sql);
            $this->Template->ferienistn = $objFe->ferienist;
            $sql="SELECT sum(minutes)/60 as ferienist from tl_timerec WHERE datum>='.$start_l.' AND datum<'.$end_l.' AND jobid = 3 AND memberid = ".$_REQUEST['mid'];
			$objFe = $this->Database->execute($sql);
            $this->Template->ferienistl = $objFe->ferienist; 
			
            //$maferien = $objFe->ferien;
            //$this->Template->ajahr = $ajahr;
            //$this->Template->maferien = $maferien/$atag;
            $this->Template->stdist = $sumMin/60; 
            $this->Template->stdsoll = $stdsoll;
            //$this->Template->masoll = $masoll;
            //$this->Template->stdfakt = $mafakt;
            $this->Template->sumMon = $arrSumMon;
            
            $this->Template->stdproz = $stdproz*100;
            $this->Template->atag = $atag;
            $monat = Array('0'=>'Januar','1'=>'Februar','2'=>'März','3'=>'April','4'=>'Mai','5'=>'Juni','6'=>'Juli','7'=>'August','8'=>'September','9'=>'Oktober','10'=>'November','11'=>'Dezember');
            $this->Template->moname = $monat;
        }
//Ende Profil
	}
}

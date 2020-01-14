<?php
namespace Pnwscm60\PbtimeBundle\Module;
class ModuleRegie extends \Contao\Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_regie';
 
	public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### REGIE ###';
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
        
    //REGIELISTE = AUSWAHL NICHT BEARBEITETE REGIELEISTUNGEN
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
    
    // REGIELISTE 2 > REGIERAPPORT FÜR WELCHES DATUM?
    if($_REQUEST['todo']=='prepregie'){ 
        // Alle Daten für dieses Projekt
        $sql="SELECT * from tl_project WHERE id=".$projekt;
        
        $objRegiePro = \Database::getInstance()->execute($sql);
        $this->Template->pid = $objRegiePro->id;
        $this->Template->knr = $objRegiePro->knr;
        $this->Template->kname = $objRegiePro->kname;
        $this->Template->wohnort = $objRegiePro->wohnort;
        $this->Template->descript = $objRegiePro->descript;
        $this->Template->ladress = $objRegiePro->ladress;
        $dat1 = date("d.m.Y", $objRegiePro->start);
    /*******************/
    //Jetzt Auswahlliste der Daten, für die Regieeinträge bestehen
        $sql='SELECT datum from tl_timerec WHERE tl_timerec.pid = '.$projekt.' AND tl_timerec.tregie = 1 and tl_timerec.trdone=0 GROUP BY datum UNION SELECT datum from tl_costrec WHERE tl_costrec.pid = '.$projekt.' AND mregie = 1 and rmdone=0 GROUP BY datum UNION SELECT datum from tl_machrec WHERE tl_machrec.pid = '.$projekt.' AND tl_machrec.tregie = 1 and tl_machrec.trdone=0 GROUP BY datum';
        $objRegDat = \Database::getInstance()->execute($sql);
        while($objRegDat->next()){
            $newdat = date("d.m.Y",$objRegDat->datum);
            $arrRegDat[] = array(
                'dat' => $newdat
            );
        }
        $this->Template->doit = 'chooseregiedat';
        $this->Template->regiedaten = $arrRegDat;
    }
    
    // BEREICH REGIE-RAPPORT
    //**************************
    //* Teil 1: REGIEFIRSTEDIT SPEICHERN = REGIERAPPORT ANLEGEN */
    //datum wird nur in tl_regie gespeichert, Datum Leistungen bleibt unverändert
    if($_REQUEST['todo']=='saveregieed'){
      
        $tlim=count($_REQUEST['tid']);
        for($i=0;$i<$tlim;$i++){
            //Jeden Regieeintrag Zeiten auslesen > tl_timerec aktualisieren
            //echo $i.": ";
            $tid = $_REQUEST['tid'][$i];
            $timeids .= "-".$_REQUEST['tid'][$i];
            //echo $tid." ";
            $timarr[]=$tid;
            $rminutes = $_REQUEST['rminutes'][$i]*60;
            $transatz = $_REQUEST['transatz'][$i];
            $trtext = $_REQUEST['trtext'][$i];
            $trdone = mktime(0,0,0,date("m"),date("d"),date("y"));
 
            $sql='UPDATE tl_timerec SET tstamp='.time().', rminutes='.$rminutes.',transatz='.$transatz.',trtext="'.$trtext.'",trdone='.$trdone.' WHERE id='.$tid.';';
            $objResult = \Database::getInstance()->execute($sql); 
        }
        $matarr = array();
        $mlim=count($_REQUEST['mid']);
        for($i=0;$i<$mlim;$i++){
            //Jeden Regieeintrag Material auslesen > tl_costrec aktualisieren
            $mid = $_REQUEST['mid'][$i];
            $matids .= "-".$_REQUEST['mid'][$i];
            $matarr[]=$mid;
            $rmamount = $_REQUEST['rmamount'][$i];
            $rmeinheit = $_REQUEST['rmeinheit'][$i];
            $rmansatz = $_REQUEST['rmansatz'][$i];
            $rmtext = $_REQUEST['rmtext'][$i];
            $rmkommentar = $_REQUEST['rmkommentar'][$i];
            $rmdone = mktime(0,0,0,date("m"),date("d"),date("y"));

            $regnrm = array();

            $sql='UPDATE tl_costrec SET tstamp='.time().', rmamount='.$rmamount.',rmeinheit='.$rmeinheit.',rmansatz='.$rmansatz.',rmtext="'.$rmtext.'",rmkommentar="'.$rmkommentar.'",rmdone='.$rmdone.' WHERE id='.$mid.';';
            $objResult = \Database::getInstance()->execute($sql);
            //echo $sql."<br/>";
        }
        $malim=count($_REQUEST['maid']);
        $macharr = array();    
        for($i=0;$i<$malim;$i++){
            //Jeden Regieeintrag Zeiten auslesen > tl_timerec aktualisieren
            $maid = $_REQUEST['maid'][$i];
            $machids .= "-".$_REQUEST['maid'][$i];
            $timarr[]=$tid;
            $rminutes = $_REQUEST['mrminutes'][$i]*60;
            $transatz = $_REQUEST['mtransatz'][$i];
            $trtext = $_REQUEST['mtrtext'][$i];
            $trdone = mktime(0,0,0,date("m"),date("d"),date("y"));
            $sql='UPDATE tl_machrec SET tstamp='.time().', rminutes='.$rminutes.',transatz='.$transatz.',trtext="'.$trtext.'",trdone='.$trdone.' WHERE id='.$maid.';';
            $objResult = \Database::getInstance()->execute($sql);
        }  
        $tids = substr($timeids, 1);
        $mtids = substr($matids, 1);
        $mmids = substr($machids, 1);
        $descript = $_REQUEST['descript'];
        $tstmp = time();
        $rrdatum = $_REQUEST['datum'];
        $rdatum = strtotime($rrdatum);
        // Such die höchste Regienr desselben Projekts
        $sql='SELECT max(regienr) as rmax from tl_regie WHERE pid='.$projekt;
        $objResult = \Database::getInstance()->execute($sql);
        if ($objResult->rmax==''){ $rmax = 1; } else { $rmax = $objResult->rmax;};
        $sql='INSERT INTO tl_regie (pid,tstamp,regienr,rrdatum,timeids, matids, machids, descript) VALUES ('.$projekt.','.$tstmp.','.$rmax.','.$rdatum.',"'.$tids.'","'.$mtids.'","'.$mmids.'", "'.$descript.'")';
        $objResult = \Database::getInstance()->execute($sql);
        $todo = "report";
        }
        /* EDIT EINES BESTEHENDEN REGIERAPPORTS SPEICHERN*/
        if($_REQUEST['todo']=='saveeditregierap'){
        $tlim=count($_REQUEST['tid']);
        for($i=0;$i<$tlim;$i++){
            //Jeden Regieeintrag Zeiten auslesen > tl_timerec aktualisieren
            //echo $i.": ";
            $tid = $_REQUEST['tid'][$i];
            $timeids .= "-".$_REQUEST['tid'][$i];
            //echo $tid." ";
            $timarr[]=$tid;
            $rminutes = $_REQUEST['rminutes'][$i]*60;
            $transatz = $_REQUEST['transatz'][$i];
            $trtext = $_REQUEST['trtext'][$i];
            $trdone = mktime(0,0,0,date("m"),date("d"),date("y"));
 
            $sql='UPDATE tl_timerec SET tstamp='.time().', rminutes='.$rminutes.',transatz='.$transatz.',trtext="'.$trtext.'",trdone='.$trdone.' WHERE id='.$tid.';';
            $objResult = \Database::getInstance()->execute($sql); 
        }
        $matarr = array();
        $mlim=count($_REQUEST['mid']);
        for($i=0;$i<$mlim;$i++){
            //Jeden Regieeintrag Material auslesen > tl_costrec aktualisieren
            $mid = $_REQUEST['mid'][$i];
            $matids .= "-".$_REQUEST['mid'][$i];
            $matarr[]=$mid;
            $rmamount = $_REQUEST['rmamount'][$i];
            $rmeinheit = $_REQUEST['rmeinheit'][$i];
            $rmansatz = $_REQUEST['rmansatz'][$i];
            $rmtext = $_REQUEST['rmtext'][$i];
            $rmkommentar = $_REQUEST['rmkommentar'][$i];
            $rmdone = mktime(0,0,0,date("m"),date("d"),date("y"));

            $sql='UPDATE tl_costrec SET tstamp='.time().', rmamount='.$rmamount.',rmeinheit='.$rmeinheit.',rmansatz='.$rmansatz.',rmtext="'.$rmtext.'",rmkommentar="'.$rmkommentar.'",rmdone='.$rmdone.' WHERE id='.$mid.';';
            $objResult = \Database::getInstance()->execute($sql);
            //echo $sql."<br/>";
        }
        $malim=count($_REQUEST['maid']);
        $macharr = array();    
        for($i=0;$i<$malim;$i++){
            //Jeden Regieeintrag Zeiten auslesen > tl_timerec aktualisieren
            $maid = $_REQUEST['maid'][$i];
            $machids .= "-".$_REQUEST['maid'][$i];
            $timarr[]=$tid;
            $rminutes = $_REQUEST['mrminutes'][$i]*60;
            $transatz = $_REQUEST['mtransatz'][$i];
            $trtext = $_REQUEST['mtrtext'][$i];
            $trdone = mktime(0,0,0,date("m"),date("d"),date("y"));
            $sql='UPDATE tl_machrec SET tstamp='.time().', rminutes='.$rminutes.',transatz='.$transatz.',trtext="'.$trtext.'",trdone='.$trdone.' WHERE id='.$maid.';';
            $objResult = \Database::getInstance()->execute($sql);
        }  
        $tids = substr($timeids, 1);
        $mtids = substr($matids, 1);
        $mmids = substr($machids, 1);
        $descript = $_REQUEST['descript'];
        $regienr = $_REQUEST['regienr'];
        $tstmp = time();
        $rrdatum = $_REQUEST['datum'];
        $rdatum = strtotime($rrdatum);
        // Such die höchste Regienr desselben Projekts
        $sql='UPDATE tl_regie SET tstamp='.$tstmp.',descript="'.$descript.'" WHERE tl_regie.id='.$regienr;
        $objResult = \Database::getInstance()->execute($sql);
        header("location:verkauf.html?trg=vkf&todo=report&proj=".$projekt."");
        }
      //REGIEFIRSTEDIT ANGEFORDERT FÜR EIN BESTIMMTES DATUM > DATEN FÜR RR VORBEREITUNG BEREITSTELLEN     
      if($_REQUEST['todo']=='prepeditregie'){
          $pro = $_REQUEST['proj'];
          // Zuerst Zeiten
          $sql="SELECT * from tl_project WHERE id=".$projekt;
          $objRegiePro = \Database::getInstance()->execute($sql);

          //times mit Regie für dieses Projekt
          $dat = $_REQUEST['datum'];
          $this->Template->datum = $dat;
          $datum = strtotime($dat);

          $sql='SELECT tl_timerec.id as id, datum, jobid, catid, rjobid, rcatid, tl_category.title as ctitle, tl_jobs.title as jtitle, minutes, rminutes, tregiekommentar, tl_timerec.descript as descript, tregiekommentar, transatz, trtext, trdone, nkurz from tl_timerec, tl_category, tl_jobs, tl_member WHERE tl_timerec.catid = tl_category.id AND tl_timerec.jobid = tl_jobs.id AND memberid = tl_member.id AND tl_timerec.pid = '.$pro.' AND tregie = 1 and trdone=0 AND datum='.$datum.' ORDER BY ctitle, jtitle';
          $objRegieTime = \Database::getInstance()->execute($sql);
          
          while ($objRegieTime->next())
            {
              $dattime = date("d.m.Y", $objRegieTime->datum);
              $rdonetime = date("d.m.Y", $objRegieTime->trdone);
              $arrRegieTime[]=array(
                'tid' => $objRegieTime->id,
                'datum' => $dattime,
                'knr' => $objRegieTime->knr,
                'nkurz' => $objRegieTime->nkurz,
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
          $sql='SELECT tl_costrec.id, pid, datum, einheit, amount, descript, title, memberid, rmdone, rmdescript, rmamount, rmeinheit, rmkommentar, rmansatz, rmtext, rmtitle, mregie, rmnumber, rmfinal, typ, nkurz from tl_costrec, tl_member WHERE pid = '.$pro.' AND mregie=1 AND memberid = tl_member.id AND rmdone=0 AND datum='.$datum;
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
                'einheit' => $objRegieMat->einheit,
                'eh' => $eh,
                'nkurz' => $objRegieMat->nkurz,
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
          $sql='SELECT tl_machrec.id as id, datum, jobid, catid, rjobid, rcatid, tl_category.title as ctitle, tl_jobs.title as jtitle, minutes, rminutes, tregiekommentar, tl_machrec.descript as descript, tregiekommentar, transatz, trtext, trdone, nkurz from tl_machrec, tl_category, tl_jobs, tl_member WHERE tl_machrec.catid = tl_category.id AND memberid = tl_member.id AND tl_machrec.jobid = tl_jobs.id AND tl_machrec.pid = '.$pro.' AND tregie = 1 and trdone=0 AND datum='.$datum;
          $objRegieMach = \Database::getInstance()->execute($sql);
          
          while ($objRegieMach->next())
            {
              $dattime = date("d.m.Y", $objRegieMach->datum);
              $rdonetime = date("d.m.Y", $objRegieMach->trdone);
              $arrRegieMach[]=array(
                'tid' => $objRegieMach->id,
                'datum' => $dattime,
                'nkurz' => $objRegieMach->nkurz,
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
          $this->Template->pdescript = $objRegiePro->descript;
          $this->Template->ladress = $objRegiePro->ladress;
           $dat1 = date("d.m.Y", $objRegiePro->start);
          $this->Template->start = $dat1;
          $this->Template->doit = 'editregie';
      }
    /* REGIEEDIT BEREITSTELLEN FÜR BEREITS ANGELEGTEN REGIERAPPORT*/    
     if($_REQUEST['todo']=='prepeditregierap'){
         $rid = $_REQUEST['rid'];

         // Zuerst Zeiten
         $sql='SELECT tl_regie.id as rid, tl_regie.pid as pid, knr, kname, wohnort, tl_regie.descript as descript, ladress, regienr, rrdatum as datum, timeids, matids, machids, concat(lastname," ",firstname) as pl from tl_project, tl_regie, tl_member WHERE tl_project.memberid = tl_member.id AND tl_regie.id='.$rid.' AND tl_regie.pid=tl_project.id';
         
         $objRegie = \Database::getInstance()->execute($sql);
         $this->Template->doit = 'editregrap';
         $this->Template->rid = $objRegie->rid;
         $this->Template->pid = $objRegie->pid;
         $this->Template->knr = $objRegie->knr;
         $this->Template->kname = $objRegie->kname;
         $this->Template->wohnort = $objRegie->wohnort;
         $this->Template->descript = $objRegie->descript;
         $this->Template->ladress = $objRegie->ladress;
         $this->Template-> pl = $objRegie->pl;
         $this->Template-> regienr = $objRegie->regienr;
         $this->Template-> datum = date("d.m.Y",$objRegie->datum);
         $this->Template-> timeids = explode("-",$objRegie->timeids);
         $this->Template-> matids = explode("-",$objRegie->matids);
         $this->Template-> machids = explode("-",$objRegie->machids);

          //times dieser regienr
         $searchtimes = "'" . implode("','", explode("-",$objRegie->timeids)) . "'";
         $sql='SELECT tl_timerec.id,tl_timerec.pid,catid,jobid,datum,tl_category.title as ctitle, tl_jobs.title as jtitle,minutes,descript,tregie,trdone,transatz,trtext,rminutes,nkurz FROM tl_timerec, tl_category, tl_jobs, tl_member WHERE catid=tl_category.id AND jobid=tl_jobs.id AND tl_member.id=memberid AND tl_timerec.id IN ('.$searchtimes.') ORDER by catid, jobid';

         $objRegieTime = \Database::getInstance()->execute($sql);
         
          while ($objRegieTime->next())
            {
              $dattime = date("d.m.Y", $objRegieTime->datum);
              $rdonetime = date("d.m.Y", $objRegieTime->trdone);
              $arrRegieTime[]=array(
                'tid' => $objRegieTime->id,
                'datum' => $dattime,
                'knr' => $objRegieTime->knr,
                'nkurz' => $objRegieTime->nkurz,
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
         $searchmat = "'" . implode("','", explode("-",$objRegie->matids)) . "'";
         $sql='SELECT tl_costrec.id, pid, datum, einheit, amount, descript, title, rmdone, rmdescript, rmamount, rmeinheit, rmkommentar, rmansatz, rmtext, rmtitle, mregie, rmnumber, rmfinal, typ, nkurz from tl_costrec, tl_member WHERE tl_costrec.id IN ('.$searchmat.') AND memberid = tl_member.id';
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
                'einheit' => $objRegieMat->einheit,
                'eh' => $eh,
                'nkurz' => $objRegieMat->nkurz,
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
         
         $searchmach = "'" . implode("','", explode("-",$objRegie->machids)) . "'";
          //machinetime for this project
          $sql='SELECT tl_machrec.id as id, tl_machrec.pid, datum, rjobid, rcatid, tl_category.title as ctitle, tl_jobs.title as jtitle, minutes, rminutes, tregiekommentar, tl_machrec.descript as descript, tregiekommentar, transatz, trtext, trdone, nkurz from tl_machrec, tl_category, tl_jobs, tl_member WHERE catid=tl_category.id AND jobid=tl_jobs.id AND tl_member.id=memberid AND tl_machrec.id IN ('.$searchmach.') ORDER by catid, jobid';;
         $objRegieMach = \Database::getInstance()->execute($sql);
          
          while ($objRegieMach->next())
            {
              $dattime = date("d.m.Y", $objRegieMach->datum);
              $rdonetime = date("d.m.Y", $objRegieMach->trdone);
              $arrRegieMach[]=array(
                'tid' => $objRegieMach->id,
                'datum' => $dattime,
                'nkurz' => $objRegieMach->nkurz,
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
        
        //REGIERAPPORT EDITIEREN
        if($_REQUEST['todo']=='rleistedit'){ 
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

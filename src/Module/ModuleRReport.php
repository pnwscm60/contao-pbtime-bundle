<?php
namespace Pnwscm60\PbtimeBundle\Module;
class ModuleRReport extends \Contao\Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_rreport';
 
	public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['project'][0]) . ' ###';
            $objTemplate->project = $this->headline;
            $objTemplate->start = $this->start;
            $objTemplate->descript = $this->descript;
            return $objTemplate->parse();
		}
        return parent::generate();
    }
	/**
	 * Compile the current element
	 */
	protected function compile()
	{

        //projekt
        if($_REQUEST['proj']){
            $pro = $_REQUEST['proj'];
            }
        //trg
         if($_REQUEST['trg']){
            $trg = $_REQUEST['trg'];
            $this->Template->trg = $trg;
         }
                 
        
    // BEREICH REGIE-REPORT

        
  
       
       $sql='SELECT *, concat(knr,"/",kname,"/",wohnort) as title from tl_project WHERE id='.$pro;
       $objRegieDet = \Database::getInstance()->execute($sql);
            $arrProRegie=array(
                'proid' => $objRegieDet->id,
                'pkname' => $objRegieDet->kname,
                'pwohnort' => $objRegieDet->wohnort,
                'ladress' => $objRegieDet->ladress,
                'descript' => $objRegieDet->descript,
            );
            $this->Template->ptitle = $objRegieDet->kname.'<br/>'.$objRegieDet->ladress.'<br/>'.$objRegieDet->wohnort;
            $this->Template->projektdata = $arrProRegie;    
            $this->Template->todo = 'rreport';
        
       //Regiedaten für dieses Projekt bereitstellen     
          
          // Zuerst Zeiten
          //$sql="SELECT * from tl_project WHERE id=".$pro;
          //$objRegiePro = \Database::getInstance()->execute($sql);
          //times mit Regie für dieses Projekt
          
        //Abfragen je nach Typ des Eintrags (time, mat oder mach)
        //1. Time
        if($_REQUEST['typ']==0){
        $sql='SELECT tl_timerec.id as id, datum, jobid, catid, rjobid, trnumber, rcatid, tl_category.title as ctitle, tl_jobs.title as jtitle, minutes, rminutes, tregiekommentar,  tl_timerec.descript as descript, tregiekommentar, transatz, trtext, trdone, nkurz, concat(lastname," ",firstname) as name from tl_timerec, tl_category, tl_jobs, tl_member WHERE tl_timerec.catid = tl_category.id AND tl_timerec.jobid = tl_jobs.id AND tl_timerec.memberid = tl_member.id AND tl_timerec.id='.$_REQUEST['regid'];
         $objRegieTime = \Database::getInstance()->execute($sql); 

              $dattime = date("d.m.Y", $objRegieTime->datum);
              $rdonetime = date("d.m.Y", $objRegieTime->trdone);
              $arrRegieTime=array(
                'typ' => 0,
                'tid' => $objRegieTime->id,
                'datum' => $dattime,
                //'descript' => $objRegieTime->descript,
                //'ctitle' => $objRegieTime->ctitle,
                //'jtitle' => $objRegieTime->jtitle,
                //'minutes' => $objRegieTime->minutes/60,
                'rjobid' => $objRegieTime->rjobid,
                'jtitle' => $objRegieTime->jtitle,
                'ctitle' => $objRegieTime->ctitle,
                'rcatid' => $objRegieTime->rcatid,
                'rminutes' => $objRegieTime->rminutes/60,
                'tregiekommentar' => $objRegieTime->tregiekommentar,
                'transatz' => $objRegieTime->transatz,
                'trtext' => $objRegieTime->trtext,
                'trdone' => $rdonetime,
                'rnumber' => $objRegieTime->trnumber,
                'nkurz' => $objRegieTime->nkurz,
                'name' => $objRegieTime->name
            );
        $this->Template->regietime = $arrRegieTime;
        }
        
        if($_REQUEST['typ']==1){
          //mat mit Regie für dieses Projekt
         $sql='SELECT *, nkurz, concat(lastname," ",firstname) as name, rmnumber from tl_costrec, tl_member WHERE pid = '.$pro.' AND tl_costrec.memberid = tl_member.id AND tl_costrec.id='.$_REQUEST['regid'];
         
            $objRegieMat = \Database::getInstance()->execute($sql);
              $datmat = date("d.m.Y", $objRegieMat->datum);
              $rmdone = date("d.m.Y", $objRegieMat->rmdone);
              if($objRegieMat->rmeinheit==5){$reh = "kg";}
            elseif($objRegieMat->rmeinheit==1){$reh = "l";}
            elseif($objRegieMat->rmeinheit==2){$reh = "m";}
            elseif($objRegieMat->rmeinheit==3){$reh = "m2";}
            elseif($objRegieMat->rmeinheit==4){$reh = "St.";}
            $arrRegieMat=array(
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
                'rmeinheit' => $reh,
                'nkurz' => $objRegieMat->nkurz,
                'name' => $objRegieMat->name,
                'rnumber' => $objRegieMat->rmnumber,
                'typ' => 1,
            );
       
         $this->Template->regiemat = $arrRegieMat;
        
         }
        
        if($_REQUEST['typ']==2){
        $sql='SELECT tl_machrec.id as id, datum, jobid, catid, rjobid, trnumber, rcatid, tl_category.title as ctitle, tl_jobs.title as jtitle, minutes, rminutes, tregiekommentar, tregiekommentar, transatz, trtext, trdone, nkurz, concat(lastname," ",firstname) as name from tl_machrec, tl_category, tl_jobs, tl_member WHERE tl_machrec.catid = tl_category.id AND tl_machrec.jobid = tl_jobs.id AND tl_machrec.memberid = tl_member.id AND tl_machrec.id='.$_REQUEST['regid'];
         $objRegieTime = \Database::getInstance()->execute($sql); 

          
              $dattime = date("d.m.Y", $objRegieTime->datum);
              $rdonetime = date("d.m.Y", $objRegieTime->trdone);
              $arrRegieMach=array(
                'typ' => 2,
                'tid' => $objRegieTime->id,
                'datum' => $dattime,
                //'descript' => $objRegieTime->descript,
                //'ctitle' => $objRegieTime->ctitle,
                //'jtitle' => $objRegieTime->jtitle,
                //'minutes' => $objRegieTime->minutes/60,
                'rjobid' => $objRegieTime->rjobid,
                'jtitle' => $objRegieTime->jtitle,
                'ctitle' => $objRegieTime->ctitle,
                'rcatid' => $objRegieTime->rcatid,
                'rminutes' => $objRegieTime->rminutes/60,
                'tregiekommentar' => $objRegieTime->tregiekommentar,
                'transatz' => $objRegieTime->transatz,
                'trtext' => $objRegieTime->trtext,
                'trdone' => $rdonetime,
                'rnumber' => $objRegieTime->trnumber,
                'nkurz' => $objRegieTime->nkurz,
                'name' => $objRegieTime->name
            );
        $this->Template->regiemach = $arrRegieMach;    
        }
          //Template bereitstellen
        
        /* 
        $this->Template->regiemat = $arrRegieMat;
          $this->Template->pid = $objRegiePro->id;
          $this->Template->knr = $objRegiePro->knr;
          $this->Template->kname = $objRegiePro->kname;
          $this->Template->wohnort = $objRegiePro->wohnort;
          $this->Template->descript = $objRegiePro->descript;
          $this->Template->ladress = $objRegiePro->ladress;
           $dat1 = date("d.m.Y", $objRegiePro->start);
          $this->Template->start = $dat1;
        */ 
    }
}

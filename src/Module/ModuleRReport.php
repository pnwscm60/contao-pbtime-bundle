<?php
namespace Pnwscm60\ContaoPbtimeBundle\Module;
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
            $objTemplate->wildcard = '### REGIERAPPORT DRUCKEN ###';
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
}

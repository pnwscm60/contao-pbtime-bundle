<?php
namespace Pnwscm60\PbtimeBundle\Module;
class ModuleDaily extends Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_daily';
 
	public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### DAILY ###';

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

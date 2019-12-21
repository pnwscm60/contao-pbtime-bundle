<?php
namespace Pnwscm60\PbtimeBundle\Module;
class ModuleReport extends Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_report';
 
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

    // BEREICH PROJEKT-REPORT
         $this->Template->todo = 'report';
        //Projektdaten ausliefern
        $sql="SELECT * from tl_project WHERE id=".$projekt;
        $objProjekt = $this->Database->execute($sql);
        $this->Template->ptitle = $objProjekt->knr.'/'.$objProjekt->kname.'/'.$objProjekt->wohnort;
        $this->Template->start = date("d.m.Y",$objProjekt->start);
        $this->Template->enddone = ($objProjekt->enddone == 0) ? 'nicht abgeschlossen' : date("d.m.Y", $objProjekt->enddone);
        
        //Leistungsdaten holen
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
            $sql = "SELECT *, tl_jobs.title, tl_category.title as ctitle, nkurz FROM tl_timerec, tl_jobs, tl_category, tl_member WHERE tl_jobs.id=jobid and tl_category.id = tl_jobs.pid AND tl_timerec.pid=".$projekt." AND memberid = tl_member.id AND tl_jobs.pid = ".$sumtime->catid." ORDER BY datum ASC;";
            $objtime = $this->Database->execute($sql);
            while ($objtime->next())
		{
         $dat1 = date("d.m.Y", $objtime->datum);
		$arrtime[] = array
		(
			'id' => $objtime->id,
            'datum' => $dat1,
			'zeit' => $objtime->minutes,
			'descript' => $objtime->descript,
			'jobtitle' => $objtime->title,
			'pid' => $objtime->pid,
			'catid' => $objtime->catid,
			'jobid' => $objtime->jobid,
            'nkurz' => $objtime->nkurz,
		);
		}
		}
        
    $this->Template->rzeit = $arrtime;    
	$this->Template->rsumme = $arrsum;
    $this->Template->projekt = $projekt;
        
    //Gesamtzeit
    $sql = "SELECT sum(minutes) as totzeit from tl_timerec WHERE tl_timerec.pid=".$projekt;
     $objttime = $this->Database->execute($sql); 
    $this->Template->ttzeit = $objttime->totzeit;
        
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
       $this->Template->repmat = $arrmat;       
	}
}

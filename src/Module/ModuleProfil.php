<?php
namespace Pnwscm60\PbtimeBundle\Module
class ModuleProfil extends Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_profil';
 
	public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### PROFIL ###';
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
          $sql = 'select lastname, firstname, email, nkurz, cpref, zeitmodell,groups from tl_member where id='.$userid;
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
               
            $sql='SELECT zeitmodell from tl_member WHERE id = '.$userid;
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
            $sql='SELECT * from tl_hours WHERE pid = '.$userid;
            $objSoll = $this->Database->execute($sql);
            while($objSoll->next()){
				$monthhours = explode("|",$objSoll->monthhours);
				$masoll[] = array(
					'foryear' => $objSoll->foryear,
					'prozent' => $objSoll->prozent,
					'yearhours' => $objSoll->yearhours,
					'yearhoursaldo' =>$objSoll->yearhoursaldo,
					'monthhours' => $monthhours,
					'ferien' => $objSoll->ferien,
					'feriensaldo' => $objSoll->feriensaldo,
					'comment' => $objSoll->comment,
				);
			
            }
            
			$this->Template->masoll = $masoll;
		
			//Stundensummation pro Monat pro Mitarbeiter
			//aktuelles Jahr
            $sql='SELECT MONTH(FROM_UNIXTIME(datum)) as mo, sum(minutes)/60 as ist_a from tl_timerec WHERE datum>='.$start_a.' AND datum<'.$end_a.' AND catid != 1 AND typ = 0 AND memberid = '.$userid.' group by MONTH(FROM_UNIXTIME(datum))';
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

			$sql='SELECT MONTH(FROM_UNIXTIME(datum)) as mo, sum(minutes)/60 as ist_l from tl_timerec WHERE datum>='.$start_l.' AND datum<'.$end_l.' AND catid != 1 AND typ = 0 AND memberid = '.$userid.' group by MONTH(FROM_UNIXTIME(datum))';
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
            
			$sql='SELECT MONTH(FROM_UNIXTIME(datum)) as mo, sum(minutes)/60 as ist_n from tl_timerec WHERE datum>='.$start_n.' AND datum<'.$end_n.' AND catid != 1 AND typ = 0 AND memberid = '.$userid.' group by MONTH(FROM_UNIXTIME(datum))';
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
            $sql="SELECT sum(minutes)/60 as ferienist from tl_timerec WHERE datum>='".$start_a."' AND datum<'".$end_a."' AND jobid = 3 AND memberid = ".$userid;
			$objFe = $this->Database->execute($sql);

            $this->Template->ferienista = $objFe->ferienist/$atag;
            $sql="SELECT sum(minutes)/60 as ferienist from tl_timerec WHERE datum>='.$start_n.' AND datum<'.$end_n.' AND jobid = 3 AND memberid = ".$userid;
			$objFe = $this->Database->execute($sql);
            $this->Template->ferienistn = $objFe->ferienist;
            $sql="SELECT sum(minutes)/60 as ferienist from tl_timerec WHERE datum>='.$start_l.' AND datum<'.$end_l.' AND jobid = 3 AND memberid = ".$userid;
			$objFe = $this->Database->execute($sql);
            $this->Template->ferienistl = $objFe->ferienist;
            
            $this->Template->stdist = $sumMin/60; 
            $this->Template->stdsoll = $stdsoll;
            $this->Template->sumMon = $arrSumMon;
            
            $this->Template->stdproz = $stdproz*100;
            $this->Template->atag = $atag;
            $monat = Array('0'=>'Januar','1'=>'Februar','2'=>'März','3'=>'April','4'=>'Mai','5'=>'Juni','6'=>'Juli','7'=>'August','8'=>'September','9'=>'Oktober','10'=>'November','11'=>'Dezember');
            $this->Template->moname = $monat;
        
            
        
        // BEREITSTELLEN DER LISTE ALLER EINTRÄGE    
        // Titel des aktuellen Projekts ausliefern
        if($_REQUEST['fi']){
            if($_REQUEST['fi']<10){
                $fi="0".$_REQUEST['fi'];
            } else {
                $fi=$_REQUEST['fi'];
            }
        } else { // kein Filter gewählt > aktueller Monat
            $fi=date("m");
        }
        $jahr = date("Y");
        $sql = "SELECT datum, sum(minutes) as daymin FROM tl_timerec WHERE DATE_FORMAT(FROM_UNIXTIME(`datum`), '%m') LIKE '".$fi."' AND DATE_FORMAT(FROM_UNIXTIME(`datum`), '%Y') LIKE '".$jahr."' AND memberid=".$userid." GROUP BY datum;";
        $objSumday = \Database::getInstance()->execute($sql);
        $i=0;
        while ($objSumday->next())
        
		{
        $arrdm[] = array
          (
              'datum' => substr(date("d.m.Y",$objSumday->datum),0,10),
              'daymin' => $objSumday->daymin,
              'counter' => $i,
          );
            
            $sql1 = 'SELECT datum, minutes, tl_category.title as ctitle, tl_jobs.title as jtitle FROM tl_timerec, tl_category, tl_jobs WHERE datum='.$objSumday->datum.' AND tl_category.id=catid AND tl_jobs.id=jobid AND memberid='.$userid.' ORDER BY datum;';
            $obDay = \Database::getInstance()->execute($sql1);
                while ($obDay->next())
		          {
                    $arrshowday[$i][] = array
                    (
                    'datum' => substr(date("d.m.Y",$obDay->datum),0,10),
                    'minutes' => $obDay->minutes,
                    'ctitle' => $obDay->ctitle,
                    'jtitle' => $obDay->jtitle
                    );
                }
                $i++;
                echo $objSumday->mo." ";
        }
        $this->Template->dayminutes = $arrdm;
        $this->Template->showday = $arrshowday;
       
//Ende Profil
	}
}

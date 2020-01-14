<?php 
$objUser = FrontendUser::getInstance();
$nkurz = $objUser->nkurz;
?>

<?php // BEREICH VERKAUF ?>
<div id="verkaufjobs" style="clear:left;"> 
<div id="leistheader"><div id="neuespro" class="leistwahl">NEUES PROJEKT</div><div id="promanage"  class="leistwahl">PROJEKTRAPPORT </div><div id="regie" class="leistwahl">REGIE</div>
</div>

<?php //falls projekt gewählt -> EDIT-Maske ?>
<div class="" id="editproject" style="
<?php if($this->proj!=''): echo "display:block"; else: "display:none"; endif;?>
">
<?php if ($this->eproject): foreach ($this->eproject as $epro): ?>
<h1>Projekt editieren</h1>
<form action="verkauf.html" id="projektedit" class="protimeform" method="post" enctype="application/x-www-form-urlencoded">
<input type="hidden" name="id" value="<?php echo $epro['id']?>">
<input type="hidden" name="todo" value="updatepro">
<input type="hidden" name="trg" value="vkf">
<input type="hidden" name="REQUEST_TOKEN" value="{{request_token}}">
 <div class="selectformtop">
         <label for="memberid" class="customform">Projektleiter</label>
            <select name="memberid" class="form-control selectform">
              <option value="-">Mitarbeiter wählen</option>
                <?php foreach($this->vkfmemb as $memb):?>
                    <option value="<?php echo $memb['id']?>" <?php if (!(strcmp($epro['memberid'], $memb['id']))) {echo "selected=\"selected\"";} ?>><?php echo $memb['name']?></option>
                <?php endforeach;?>
 		</select></div> 
    
<div class="formgroup grundform"><span class="hint--right hint--rounded" aria-label="K-Nr. aus SAP">
	<label for="knr" class="grundform">K-Nummer*</label>
	<input type="text" name="knr" id="knr" class="form-control grundform" value="<?php echo $epro['knr']?>" required>
</div>
<div class="formgroup grundform"><span class="hint--right hint--rounded" aria-label="Name des Kunden">
	<label for="kname" class="grundform">Name*</label>
	<input type="text" name="kname" id="kname" class="form-control grundform w250" value="<?php echo $epro['kname']?>" required>
</div>
<div class="formgroup grundform"><span class="hint--right hint--rounded" aria-label="Wohnort des Kunden">
	<label for="wohnort" class="grundform">Wohnort*</label>
	<input type="text" name="wohnort" id="wohnort" class="form-control grundform w250" value="<?php echo $epro['wohnort']?>" required>
</div>
<div class="formgroup grundform"><span class="hint--right hint--rounded" aria-label="Lieferadresse aus SAP">
	<label for="ladress" class="grundform">Lieferadresse*</label>
    <input type="text" name="ladress" id="ladress" class="form-control grundform w250" value="<?php echo $epro['ladress']?>">
    </span>
</div>
<div class="formgroup grundform">
	<label for="descript" class="grundform">Projektbeschreibung</label>
    <input type="text" name="descript" id="descript" class="form-control grundform" value="<?php echo $epro['descript']?>">
</div>
 <div class="formgroup datumform" style="float:left;margin-right:1em;clear:left;">
          <label for="datepicker" class="datumform">Projektstart</label>
          <input type="text" class="form-control datumform" name="start" id="datepicker" readonly="readonly"
                 value="<?php echo $epro['start']?>" required>
      </div>
<div class="formgroup datumform" style="float:left;margin-right:1em;">
          <label for="datepicker" class="datumform">Projektende</label>
          <input type="text" class="form-control datumform" name="enddone" id="datepicker2" readonly="readonly"
                 value="<?php echo $epro['enddone']?>" required>
      </div>


<div style="clear:both;"></div>
<div class="submit">    
<button type="submit" class="btn btn-primary submit" id="submit">speichern</button>
<button type="button" class="btn btn-primary goHome">abbrechen</button>
</div>
</form>
<button class="filter btn" style="margin-top:1em;float:left;" id="projDelete" data-id="<?php echo $this->pid ?>">Projekt löschen</button>
<!-- </form> -->
<?php endforeach; endif; ?>
</div>


<?php // NEUES PROJEKT ERFASSEN ?>
<div id="verkaufjobs" style="clear:left;margin-top:6px;">

<div class="" id="newproject" style="display:none;">
<h1>Projekt erfassen</h1>
<form action="verkauf.html" id="newpro" class="protimeform" method="post" enctype="application/x-www-form-urlencoded">
<input type="hidden" name="todo" value="insertpro">
<input type="hidden" name="trg" value="vkf">
<input type="hidden" name="REQUEST_TOKEN" value="{{request_token}}">
<div class="selectformtop">
         <label for="memberid" class="customform">Projektleiter</label>
            <select name="memberid" class="form-control selectform">
              <option value="-">Mitarbeiter wählen</option>
                <?php foreach($this->vkfmemb as $memb):?>
                    <option value="<?php echo $memb['id']?>" <?php if (!(strcmp($epro['memberid'], $memb['id']))) {echo "selected=\"selected\"";} ?>><?php echo $memb['name']?></option>
                <?php endforeach;?>
 		</select></div>  
<div class="formgroup grundform"><span class="hint--right hint--rounded" aria-label="K-Nr. aus SAP">
	<label for="knr" class="grundform">K-Nummer*</label>
	<input type="text" name="knr" id="knr" class="form-control grundform" value="" required>
</div>
<div class="formgroup grundform"><span class="hint--right hint--rounded" aria-label="Name des Kunden">
	<label for="kname" class="grundform">Name*</label>
	<input type="text" name="kname" id="kname" class="form-control grundform w250" value="" required>
</div>
<div class="formgroup grundform"><span class="hint--right hint--rounded" aria-label="Wohnort des Kunden">
	<label for="wohnort" class="grundform">Wohnort*</label>
	<input type="text" name="wohnort" id="wohnort" class="form-control grundform w250" value="" required>
</div>
<div class="formgroup grundform"><span class="hint--right hint--rounded" aria-label="Lieferadresse aus SAP">
	<label for="ladress" class="grundform">Lieferadresse*</label>
    <input type="text" name="ladress" class="form-control grundform w250" value="">
    </span>
</div>
<div class="formgroup grundform">
	<label for="descript" class="grundform">Beschreibung</label>
    <input type="text" name="descript" class="form-control grundform" value="">
</div>

  <div class="formgroup formgroup datumform" style="float:left;margin-right:1em;clear:left;">
          <label for="datepicker" class="datumform">Datum</label>
          <input type="text" class="form-control datumform" name="start" id="datepicker" readonly="readonly"
                 value="<?php echo date("d.m.Y")?>" required>
      </div>

<div style="clear:both;"></div>
<div class="submit">    
<button type="submit" class="btn btn-primary submit" id="submit">speichern</button>
<button type="button" class="btn btn-primary goHome">abbrechen</button>
</div>
</form>
</div>     
</div>
    
<div id="vkfreport" style="clear:left;margin-top:6px;<?php echo $_REQUEST['todo']=='report' ? 'display:block;' : 'display:none;'?>
">
<h1>Leistungsrapport <div style="display:inline-block;margin-left:1em;"><img class="pdfrep" data-pdf="<?php echo $this->projekt?>" src="files/images/pdf.svg" style="width:25px;float:left;margin-top:2px;padding-top:0;"><img style="margin-left:0.4em;width:25px;margin-top:-4px;padding-top:0;" class="editpro" data-editpro="<?php echo $this->projekt?>" src='files/images/edit.svg' ></div></h1>
    <div>Projekt: <?php echo $this->ptitle;?></div>
    <div style="float:left;margin-right:1em;">Start: <?php echo $this->start;?></div>
    <div style="float:left;margin-right:1em;">Ende: <?php echo $this->enddone;?></div><div style="">Projektleiter: <?php echo $this->pl;?></div>
    <?php if($this->regrap):?>
    <h2 style="margin-top:1em;">Regierapporte</h2>
    <?php foreach($this->regrap as $regrap): ?>
    <div class="prolist">
            <div class="w150" style="float:left;">Regierapport <?php echo $regrap['knr']?>–<?php echo $regrap['regienr']?></div>
            <div class="w100" style="float:left;"><?php echo $regrap['rrdatum']?></div>
            <div class="w100" style="float:left;"><?php if($regrap['dateconfirm']==0):?>
            <span class="hint--right hint--rounded" aria-label="erstellt, nicht bestätigt"><a href="verkauf.html?trg=vkf&todo=rconfirm&proj=<?php echo $this->projekt?>&typ=<?php echo $regrap['typnr']?>&id=<?php echo $regrap['id']?>" class="confirmregie"><img class="erstellt" src="files/images/erstellt.svg" style="width:18px;margin-top:-2px;"></a></span>
            <span class="hint--right hint--rounded" aria-label="Regierapport editieren"><img class="rledit" src="files/images/edit.svg" style="width:18px;margin-top:-2px;" data-pro="<?php echo $this->projekt?>" data-id="<?php echo $regrap['id']?>"></span>
        <?php else: ?>    
            <span class="hint--right hint--rounded" aria-label="abgeschlossen"><img class="erledigt" src="files/images/erledigt.svg" style="width:18px;margin-top:-2px;"></span>
        <?php endif;?>
            <span class="hint--right hint--rounded" aria-label="PDF drucken"><img id="printrr" class="pdfrrep" data-pdf="proj=<?php echo $this->projekt?>&regid=<?php echo $regrap['id']?>" src="files/images/pdf.svg" style="width:18px;margin-top:-2px;"></span>
            </div>
    </div>
    <?php endforeach; endif; ?>
    <h2 style="margin-top:1em;">Arbeitsleistungen</h2>
     <div style="margin-top:1em;">
    <div class="time header">
    <div class='prolist' style="margin-bottom:5px;">
   	 	<div class='dat leftind'>Total Std.</div>
    	 	<div class='times'> <?php echo $this->total/60?></div>
    	<div class='timetitle'></div>
		</div></div>
 <?php if ($this->rsumme): foreach ($this->rsumme as $sum): ?>
 	<div class="time header">
    <div class='prolist'>
   	 	<div class='dat leftind'>Total Std.</div>
    	 	<div class='times'> <?php echo ($sum['summe'])/60?></div>
    	<div class='timetitle'> <?php echo $sum['ctitle']?></div>
		</div></div>
    <?php if ($this->rzeit): foreach ($this->rzeit as $tim): ?>
    <?php if ($tim['pid']==$sum['catid']):?>
  <div class="time">
    <div class='prolist'>
    	<div class='dat'> Total Std.</div>
    	<div class='times'> <?php echo ($tim['zeit'])/60?></div>
    	<div class='timetitle'> <?php echo $tim['jobtitle']?></div>
        <div class='times' style="float:right;"> <?php echo $tim['nkurz']?></div>
    </div>
  </div>
    <?php endif; ?>
  <?php endforeach; endif; //Jobs ?>
    <div style="height:5px;clear:both;">&nbsp;</div>
  <?php endforeach; endif; //Kategorie ?>
</div>
<h2 style="margin-top:1em;">Verbrauchsmaterial</h2>
 <div style="margin-top:1em;">
     <?php if ($this->rmat): foreach ($this->rmat as $rmat): ?>
     <div class="time">
    <div class='prolist'>
    	<div class='dat'> <?php echo $rmat['datum']?></div>
    	<div class='times w50' style="text-align: left;"> <?php echo $rmat['amount']?> <?php echo $rmat['einheit']?></div>
    	<div class='timetitle'> <?php echo $rmat['mattitle']?></div>
        <div class='times' style="float:right;"> <?php echo $rmat['nkurz']?></div>
    </div>
  </div>
      <?php endforeach; endif; //Ende Mat ?>
     </div>
   <h2 style="margin-top:1em;">Maschinenstunden</h2>
     <div style="margin-top:1em;">
    <div class="time header">
    <div class='prolist' style="margin-bottom:5px;">
   	 	<div class='dat leftind'>Total Std.</div>
    	 	<div class='times'> <?php echo $this->totalmach/60?></div>
    	<div class='timetitle'></div>
		</div></div>
 <?php if ($this->rsummemach): foreach ($this->rsummemach as $msum): ?>
 	<div class="time header">
    <div class='prolist'>
   	 	<div class='dat leftind'>Total Std.</div>
    	 	<div class='times'> <?php echo ($msum['summe'])/60?></div>
    	<div class='timetitle'> <?php echo $msum['ctitle']?></div>
		</div></div>
    <?php if ($this->rzeitmach): foreach ($this->rzeitmach as $mtim): ?>
    <?php if ($mtim['pid']==$msum['catid']):?>
  <div class="time">
    <div class='prolist'>
    	<div class='dat'> Total Std.</div>
    	<div class='times'> <?php echo ($mtim['zeit'])/60?></div>
    	<div class='timetitle'> <?php echo $mtim['jobtitle']?></div>
        <div class='times' style="float:right;"> <?php echo $mtim['nkurz']?></div>
    </div>
  </div>
    <?php endif; ?>
  <?php endforeach; endif; //Jobs ?>
    <div style="height:5px;clear:both;">&nbsp;</div>
  <?php endforeach; endif; //Kategorie ?>
</div>
</div>
    


<?php //Projektdaten bereitstellen ?>

    <?php //ACHTUNG SCHALTER EINBAUEN AKTIV/INAKTIV ?>
<script type="text/javascript">
var projects = [
<?php if($this->projectarray): foreach ($this->projectarray as $proj):?>
   { value: '<?php echo $proj['title']?>', data: '<?php echo $proj['id']?>' },
<?php endforeach; endif;?>
];
</script>

<?php /*Teil Projektsuche VERKAUF */ ?>
<div id="searchfieldvkf" style="background-color:white;float:left; display:none;<?php /* einblenden in folgenden targets */ if(in_array($_REQUEST['trg'], array('vkf')) && in_array($_REQUEST['filter'], array('alle','aktiv')) || in_array($this->trg, array('vkf')) && in_array($this->filter, array('alle','aktiv'))):?>
	display:block;
<?php endif;?>">
<div style="margin-bottom:5px;background-color:white;text-align:left;">Projekt auswählen:</div>
    <form><input autofocus type="text" name="project" class="form-control biginput" id="procomplete" data-trg="vkf&todo=report"/>
</form>
<div class="autocomplete-suggestions">
    <div class="autocomplete-suggestion autocomplete-selected"></div>
</div>
</div>
<div style="float:left;margin-top:1.36em;display:none;<?php /* einblenden in folgenden targets */ if(in_array($_REQUEST['trg'], array('vkf')) && in_array($_REQUEST['filter'], array('alle','aktiv')) || in_array($this->trg, array('vkf')) && in_array($this->filter, array('alle','aktiv'))):?>
	display:block;
<?php endif;?>"><button style="margin-top:6px;" type="button" class="btn filter" id="<?php if($_REQUEST['filter']=='alle'): echo "showActive"; else: echo "showAll"; endif;?>"><?php if($_REQUEST['filter']=='alle'): echo "AKTIVE"; else: echo "ALLE"; endif;?></button></div>
</div>

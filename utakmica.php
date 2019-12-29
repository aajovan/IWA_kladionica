<?php
	include_once("zaglavlje.php");
	$bp=spojiSeNaBazu();
?>
<?php
function moderatorMozeDodati() {
	global $bp;
	global $mozeUrediti;
	if(isset($_GET['liga_id']) && isset($_SESSION['aktivni_korisnik_id'])){
		$liga_id=$_GET['liga_id'];
		if(!is_numeric($liga_id)) {header("Location:lige.php");}
		$sql="SELECT * FROM liga WHERE liga_id='$liga_id'";
		if(isset($_SESSION['aktivni_korisnik_tip']) && $_SESSION['aktivni_korisnik_tip']!=0) {
			$moderator_id=$_SESSION['aktivni_korisnik_id'];
			$sql=$sql." AND moderator_id='$moderator_id'";
		}
		$rs=izvrsiUpit($bp,$sql);
		if(mysqli_num_rows($rs)>0) {
			$mozeUrediti=3;
			return true;
		} else {return false;}
	}
}
function moderatorMozeUrediti() {
	global $mozeUrediti;
	global $bp;
	if(isset($_GET['utakmica_id']) && !empty($_GET['utakmica_id']) && isset($_SESSION['aktivni_korisnik_tip'])) {
		$utakmica_id=$_GET['utakmica_id'];
		if(!is_numeric($utakmica_id)) {header("Location:lige.php");}
		$sql="SELECT liga.moderator_id, momcad.liga_id, datum_vrijeme_zavrsetka, rezultat_1 FROM momcad, utakmica ,liga
              WHERE momcad.momcad_id = utakmica.momcad_1 AND  momcad.liga_id = liga.liga_id
              AND utakmica_id=$utakmica_id";
		if($_SESSION['aktivni_korisnik_tip']==1){
			$korisnik_id=$_SESSION['aktivni_korisnik_id'];
			$sql=$sql." AND liga.moderator_id=$korisnik_id";
		}
			$rs=izvrsiUpit($bp,$sql);
			
			if(mysqli_num_rows($rs)>0) {
				$row = mysqli_fetch_array($rs);
				$datum_vrijeme_zavrsetka=$row['datum_vrijeme_zavrsetka'];
				$rezultat_1=$row['rezultat_1'];
				if(strtotime($datum_vrijeme_zavrsetka) < strtotime(date("d-m-Y H:i:s")) && ($rezultat_1 == -1)) {$mozeUrediti=1;}
				else if (strtotime($datum_vrijeme_zavrsetka) > strtotime(date("d-m-Y H:i:s"))) {$mozeUrediti=0;} 
				else {$mozeUrediti=2;}
				$liga_id=$row['liga_id'];
				return $liga_id;
								
			} else {return false;}
		}
}
    $mozeUrediti=0;
	$greska="";
	if(isset($_POST['submit'])){
		foreach($_POST as $key => $value){
			if(strlen($value)==0){$greska="Sva polja za unos su obavezna";}}
			if(!isset($_POST['naziv1']) && !isset($_POST['naziv1'])) {$greska='Ne postoje momčadi';}
		if((isset($_POST['naziv1']) && isset($_POST['naziv1'])) && ($_POST['naziv1']==$_POST['naziv2'])){$greska="Momčadi moraju biti različite";}
		else if($_POST['novi']==1 && ($_POST['rezultat1']<0 || $_POST['rezultat2']<0)){
				$greska="Rezultat ne može biti manji od 0";
		}
		else {
			$datum_vrijeme_pocetka=strtotime($_POST['datum_pocetka'].' '.$_POST['vrijeme_pocetka']);
			$datum_vrijeme_zavrsetka=$datum_vrijeme_pocetka+(90*60);
			if(($_POST['datum_pocetka'].' '.$_POST['vrijeme_pocetka'])!= date("d.m.Y H:i:s", $datum_vrijeme_pocetka)) {$greska="Datum nije validan";
		}
			else if(( $_POST['novi']==3 || $_POST['novi']==0 ) && ($datum_vrijeme_zavrsetka < strtotime(date("d-m-Y H:i:s")))) {
				$greska="Nije moguće unijeti utakmicu koja je završila.";}
			else {
				$datum_vrijeme_zavrsetka=date("Y-m-d H:i:s", $datum_vrijeme_zavrsetka);
				$datum_vrijeme_pocetka=date("Y-m-d H:i:s", $datum_vrijeme_pocetka);	
			}
		}
		
		if(empty($greska)){
			$id=$_POST['novi'];
			if(isset($_POST['utakmica_id'])) { $utakmica_id=$_POST['utakmica_id'];}
			$momcad_1=$_POST['naziv1'];
			$momcad_2=$_POST['naziv2'];
			$rezultat_1=$_POST['rezultat1'];
			$rezultat_2=$_POST['rezultat2'];
			$opis=$_POST['opis'];
			if($id==0){
				$sql="UPDATE utakmica SET momcad_1='$momcad_1', momcad_2='$momcad_2', datum_vrijeme_pocetka='$datum_vrijeme_pocetka',
			          datum_vrijeme_zavrsetka='$datum_vrijeme_zavrsetka', opis='$opis' WHERE utakmica_id = $utakmica_id";
			}
			else if($id==1) {
				$sql="UPDATE utakmica SET rezultat_1='$rezultat_1', rezultat_2='$rezultat_2' WHERE utakmica_id = $utakmica_id";
				$sql_azuriraj_listice="UPDATE listic SET listic.status = IF((SELECT 
									(CASE WHEN rezultat_1 > rezultat_2 
									THEN 1 WHEN rezultat_1 < rezultat_2 
									THEN 2 ELSE 0 END) AS konacni_rezultat 
									FROM utakmica WHERE utakmica_id=$utakmica_id) = listic.ocekivani_rezultat, 'D', 'N') 
									WHERE utakmica_id = $utakmica_id AND listic.status ='P' ";
			}
			else if($id==3){
				$sql="INSERT INTO utakmica
			         (momcad_1,momcad_2,datum_vrijeme_pocetka,datum_vrijeme_zavrsetka,rezultat_1,rezultat_2,opis)
			         VALUES ('$momcad_1','$momcad_2','$datum_vrijeme_pocetka','$datum_vrijeme_zavrsetka','$rezultat_1','$rezultat_2','$opis')";
			}
			
			if(isset($sql)){izvrsiUpit($bp,$sql);}
			if(isset($sql_azuriraj_listice)) {izvrsiUpit($bp,$sql_azuriraj_listice);}
			header("Location:lige.php");
		}
	}
	$upitValidan=false;
		if(moderatorMozeUrediti()) {
			$upitValidan=true;
			$utakmica_id=$_GET['utakmica_id'];
			$sql="SELECT * from utakmica WHERE utakmica_id = $utakmica_id";
			$rs=izvrsiUpit($bp,$sql);
			while($red=mysqli_fetch_array($rs)) {
				$opis=$red['opis'];
				$naziv1=$red['momcad_1'];
				$naziv2=$red['momcad_2'];
				$rezultat1=$red['rezultat_1'];
				$rezultat2=$red['rezultat_2'];
				$datum_vrijeme_pocetka=$red['datum_vrijeme_pocetka'];
				$vrijeme_pocetka=substr($datum_vrijeme_pocetka, strpos($datum_vrijeme_pocetka, strlen($datum_vrijeme_pocetka)));
				$datum_pocetka=substr($datum_vrijeme_pocetka, 0, strpos($datum_vrijeme_pocetka, " "));
				$datum_pocetka=date("d.m.Y",strtotime($datum_pocetka));
				$vrijeme_pocetka=date("H:i:s",strtotime($vrijeme_pocetka));
				
			};
			$liga_id=moderatorMozeUrediti();
			
		}
		else if(moderatorMozeDodati()) {
			$upitValidan=true;
			$rezultat1=-1;
			$rezultat2=-1;
			$vrijeme_pocetka='';
			$datum_pocetka='';
			$opis='';
			$liga_id=$_GET['liga_id'];
		}
		if($upitValidan){
			$sql_naziv="SELECT * FROM momcad WHERE liga_id='$liga_id' ";
			$rs_naziv1=izvrsiUpit($bp,$sql_naziv);
			$rs_naziv2=izvrsiUpit($bp,$sql_naziv);
		}
		else {
			$greska="Ne postoje ovlasti za navedenu ligu/utakmicu ili liga/utakmica ne postoji";
			header("Location:lige.php");
			
		}
	if(isset($_POST['reset']))header("Location:lige.php");
	
?>
<form method="POST" action="<?php if(isset($_GET['liga_id'])) {echo "utakmica.php?liga_id=$liga_id";} 
else if (isset($_GET['utakmica_id'])) {
	echo "utakmica.php?utakmica_id=$utakmica_id";
}else {echo "utakmica.php";}?>">
	<table class="forma">
		<caption>
			<?php
				if($mozeUrediti==3) {echo "Dodaj utakmicu";}
				else {echo "Uredi utakmicu";}
			?>
		</caption>
		<tbody>
		<tr>
				<td style="display:none" colspan="2">
					<input type="hidden" name="novi" value="<?php if(isset($mozeUrediti)){echo $mozeUrediti;} else {echo '3';}?>"/>
					
				</td>
			</tr>
			<?php if(isset($utakmica_id)) {
				echo"
					<tr>
				<td colspan='2' style='display:none'>
					<input type='hidden' name='utakmica_id' value='$utakmica_id'/>
				</td>
			</tr>
			";} ?>
			<tr>
				<td colspan="2" style="text-align:center; <?php if($greska=="")echo 'display:none;'; ?>">
					<div class="greska"><label><?php if($greska!="")echo $greska; ?></label>
				</td>
			</tr>
			<tr>
				<td><label for="naziv1"><strong>Naziv 1.momčadi:</strong></label></td>
				<td>
					<select id="naziv1" name="naziv1">
						<?php
							if(isset($_POST['naziv1']) || isset($naziv1)){
								while(list($momcad_id,$liga_id,$naziv)=mysqli_fetch_array($rs_naziv1)) {
									if(($_POST['naziv1']==$momcad_id) || ($naziv1==$momcad_id) ){echo "<option value='$momcad_id' selected>$naziv</option>";} else {
										if($mozeUrediti!==2 && $mozeUrediti!==1) {
										echo "<option value='$momcad_id'>$naziv</option>";
										}
									}
									
								}
								
				
							}
							else{
								while(list($momcad_id,$liga_id,$naziv)=mysqli_fetch_array($rs_naziv1)) {
									echo "<option value='$momcad_id'>$naziv</option>";
							}
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="naziv2"><strong>Naziv 2.momčadi:</strong></label></td>
				<td>
					<select id="naziv2" name="naziv2">
						<?php
							if(isset($_POST['naziv2']) || isset($naziv2)){
								while(list($momcad_id,$liga_id,$naziv)=mysqli_fetch_array($rs_naziv2)) {
									if(($_POST['naziv2']==$momcad_id) || ($naziv2==$momcad_id)){echo "<option value='$momcad_id' selected>$naziv</option>";} else {
										if($mozeUrediti!==2 && $mozeUrediti!==1) {
										echo "<option value='$momcad_id'>$naziv</option>";
										}
									}
									
								}
								
				
							}
							else{
								while(list($momcad_id,$liga_id,$naziv)=mysqli_fetch_array($rs_naziv2)) {
										echo "<option value='$momcad_id'>$naziv</option>";	
									
							}
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="datum_pocetka"><strong>Datum početka:</strong></label>
				</td>
				<td>
					<input type="text" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}" name="datum_pocetka" id="datum_pocetka" 
					value="<?php if(isset($_POST['datum_pocetka'])){echo $_POST['datum_pocetka'];} else{echo $datum_pocetka;}?>" placeholder="Unesite datum u formatu dd.mm.yyyy npr.03.07.2019" required <?php if($mozeUrediti==2 || $mozeUrediti==1) echo " readonly" ?>/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="vrijeme_pocetka"><strong>Vrijeme početka:</strong></label>
				</td>
				<td> 
					<input type="text" pattern="(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}" name="vrijeme_pocetka" id="vrijeme_pocetka" 
					value="<?php if(isset($_POST['vrijeme_pocetka'])){echo $_POST['vrijeme_pocetka'];} else{echo $vrijeme_pocetka;}?>" placeholder="Unesite vrijeme u formatu hh:mm:ss npr 12:30:00" required <?php if($mozeUrediti==2 || $mozeUrediti==1) echo " readonly" ?>/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="rezultat1"><strong>Rezultat 1. momčadi:</strong></label>
				</td>
				<td>
					<input type="number" name="rezultat1" id="rezultat1" min="0" value="<?php if($mozeUrediti==1){
						if(isset ($_POST["rezultat1"])) {echo $_POST["rezultat1"].'"';}
					else {echo $rezultat1.'"';}
					} else {
						if(isset ($_POST["rezultat1"])) {echo $_POST["rezultat1"];}
						else {echo $rezultat1;}
						echo '" readonly';
					}?>
					 placeholder="Nije moguće unijeti rezultat ako utakmica nije završila"/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="rezultat2"><strong>Rezultat 2. momčadi:</strong></label>
				</td>
				<td>
					<input type="number" name="rezultat2" id="rezultat2" min="0" value="<?php if($mozeUrediti==1){
						if(isset ($_POST["rezultat2"])) {echo $_POST["rezultat2"].'"';}
					else {echo $rezultat2.'"';}
					} else {
						if(isset ($_POST["rezultat2"])) {echo $_POST["rezultat2"];}
						else {echo $rezultat2;}
						echo '" readonly';
					} ?>
					 placeholder="Nije moguće unijeti rezultat ako utakmica nije završila"/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="opis"><strong>Opis:</strong></label>
				</td>
				<td>
					<textarea name="opis" id="opis" required <?php if($mozeUrediti==2 || $mozeUrediti==1) echo " readonly" ?>><?php if(isset($_POST['opis']))
					{echo $_POST['opis'];}
				else {echo $opis;}?></textarea>
				</td>
			</tr>
			
			<?php
				if($_SESSION['aktivni_korisnik_tip']==0){
			?>
			
			<?php
				}
			?>
			<tr>
				<td colspan="2" style="text-align:center;">
					<?php
						if(isset($mozeUrediti)&&$mozeUrediti!==2)echo '<input style="width:auto; margin:5px;" type="submit" name="reset" value="Izbriši"/><input style="width:auto; margin:5px" type="submit" name="submit" value="Pošalji"/>';
						else echo '<div class="greska"><label>Utakmicu nije moguće uređivati</label></div>';
					?>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<?php
	zatvoriVezuNaBazu($bp);
	include("podnozje.php");
?>

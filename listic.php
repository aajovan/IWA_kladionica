<?php

$greska='';
	include_once("zaglavlje.php");
	include_once("datum_vrijeme.php");
	
	function provjeriListic($utakmica, $korisnik) {
		global $bp;
		global $greska;
		$sql="SELECT *FROM listic WHERE utakmica_id = $utakmica AND korisnik_id=$korisnik";
		$rs=izvrsiUpit($bp,$sql);
		if(mysqli_num_rows($rs)>0) {
			$red=mysqli_fetch_array($rs);
			$status=$red['status'];
			if($status=='P'){
		$greska = "Listić za navedenu utakmicu već postoji, te ga nije moguće promijeniti";
		return false;
		  } else if ($status=='O') return $listic_id=$red['listic_id'];
	}
	return false;
	}
	
	if(!isset($_SESSION['aktivni_korisnik']))header("Location:index.php");
	 $bp=spojiSeNaBazu();
	 $korisnik_id=$_SESSION["aktivni_korisnik_id"];
    if(isset($_POST['spremi']) && strlen($_POST['rezultat'])>0 && strlen($_POST['utakmica_id'])>0){
				 $rezultat=$_POST['rezultat'];
				 $status='"O"';
				 $utakmica_id=$_POST['utakmica_id'];
				if(isset($_POST['listic_id']) && !empty($_POST['listic_id'])) {
					$listic_id=$_POST['listic_id'];
					$sql="UPDATE listic SET
						ocekivani_rezultat='$rezultat',
						status='O'
						WHERE listic_id = '$listic_id'";
				} else {
					$sql="INSERT INTO listic
						(korisnik_id,utakmica_id,ocekivani_rezultat,status)
						VALUES
						($korisnik_id,$utakmica_id,$rezultat,$status)";
				}
					izvrsiUpit($bp,$sql);
					header("Location:pregled_listica.php");
	}
	else if(isset($_POST['submit']) && strlen($_POST['rezultat'])>0 && strlen($_POST['utakmica_id'])>0){
			   $utakmica_id=$_POST['utakmica_id'];
			   $rezultat=$_POST['rezultat'];
			   $status='"P"';
			   if(isset($_POST['listic_id']) && !empty($_POST['listic_id'])) {
				    $listic_id=$_POST['listic_id'];
					$sql="UPDATE listic SET
						ocekivani_rezultat='$rezultat',
						status='P'
						WHERE listic_id = '$listic_id'";
				} else {
			   $sql="INSERT INTO listic
					(korisnik_id,utakmica_id,ocekivani_rezultat,status)
					VALUES
					($korisnik_id,$utakmica_id,$rezultat,$status);";
				}
				izvrsiUpit($bp,$sql);
				header("Location:pregled_listica.php");
	}
	
	if(!isset($_GET['utakmica_id']) || empty($_GET['utakmica_id']))$greska= "Utakmica ne postoji!";
	else {
		$id=$_GET['utakmica_id'];
		$sql="SELECT utakmica_id,datum_vrijeme_pocetka,datum_vrijeme_zavrsetka,momcad_1,momcad_2,t2.naziv AS naziv1,t3.naziv AS naziv2,utakmica.opis FROM utakmica INNER JOIN momcad t2 ON utakmica.momcad_1 = t2.momcad_id INNER JOIN momcad t3 ON utakmica.momcad_2 = t3.momcad_id WHERE utakmica_id='$id'";
		$rs=izvrsiUpit($bp,$sql);
		if(mysqli_num_rows($rs)==0)$greska = "Utakmica ne postoji";
		   else {
				list($utakmica_id,$datum_vrijeme_pocetka,$datum_vrijeme_zavrsetka,$momcad_1,$momcad_2,$naziv_1,$naziv_2,$opis)=mysqli_fetch_array($rs);
			    $vrijeme = strtotime($datum_vrijeme_zavrsetka);
			    $trenutno_vrijeme=strtotime(date("Y-m-d h:i:sa"));
			    if (!($vrijeme>$trenutno_vrijeme))$greska = "Utakmica je završila!";
			    else {
			     $korisnik=$_SESSION["aktivni_korisnik_id"];
		         $sql="SELECT *FROM listic WHERE utakmica_id = $id AND korisnik_id=$korisnik";
		         $rs=izvrsiUpit($bp,$sql);
		            if(mysqli_num_rows($rs)>0) {
			           $red=mysqli_fetch_array($rs);
			           $status=$red['status'];
			              if($status=='P')$greska = "Već postoji listić za navedenu utakmicu, te ga nije moguće promijeniti";
		                  else if ($status=='O') $listic_id=$red['listic_id'];$ocekivani_rezultat=$red['ocekivani_rezultat'];
		            }
	            }
		    }
	}
	
    if (!empty($greska)) echo "<div class='greska'><label>$greska</label></div>";
		else {
?>

<form method="POST" action="listic.php">
	<table class="forma">
		<caption>Stvori listić</caption>
		<tbody>
			<tr>
			<td style="display:none">
					<input type="hidden" id="listic_id" name="listic_id"
						value="<?php if(isset($listic_id)) echo $listic_id;?>"/>
				</td>
				<td style="display:none">
					<input type="hidden" id="utakmica_id" name="utakmica_id"
						value="<?php echo $id;?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="naziv_1"><strong>Naziv prve momcadi:</strong></label>
				</td>
				<td>
					<input type="text" id="naziv_1"
						value="<?php echo $naziv_1;?>" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="naziv_2"><strong>Naziv druge momcadi:</strong></label>
				</td>
				<td>
					<input type="text" id="naziv_2"
						value="<?php echo $naziv_2;?>" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="datum_vrijeme_pocetka"><strong>Datum početka:</strong></label>
				</td>
				<td>
					<input type="text" id="datum_vrijeme_pocetka"
						value="<?php echo pretvoriDatum($datum_vrijeme_pocetka);?>" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="datum_vrijeme_zavrsetka"><strong>Datum završetka:</strong></label>
				</td>
				<td>
					<input type="text" id="datum_vrijeme_zavrsetka"
						value="<?php echo pretvoriDatum($datum_vrijeme_zavrsetka);?>" readonly/>
				</td>
			</tr>
			<tr>
				<td><label for="rezultat"><strong>Očekivani rezultat:</strong></label></td>
				<td>
					<select id="rezultat" name="rezultat">

							<option value="0" <?php if(isset($ocekivani_rezultat) && $ocekivani_rezultat==0) echo 'selected';?>>neriješeno</option>;
							<option value="1" <?php if(isset($ocekivani_rezultat) && $ocekivani_rezultat==1) echo 'selected';?>>pobjeda 1.momčadi</option>;
							<option value="2" <?php if(isset($ocekivani_rezultat) && $ocekivani_rezultat==2) echo 'selected';?>>pobjeda 2.momčadi</option>;	
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
				
<input type="submit" style="width:auto; margin:5px" name="submit" value="Predaj"/>
<input type="submit" style="width:auto; margin:5px" name="spremi" value="Spremi"/>
					
				
				</td>
			</tr>
		</tbody>
	</table>
</form>
<?php
	}
	?>

<?php
	zatvoriVezuNaBazu($bp);
	include("podnozje.php");
?>

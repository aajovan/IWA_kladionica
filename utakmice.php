<?php
	include_once("zaglavlje.php");
	include_once("datum_vrijeme.php");
	$bp=spojiSeNaBazu();
?>
<?php
function mozeDodati ($liga) {
	global $bp;
	if(isset($_SESSION['aktivni_korisnik_id']) && !empty($liga) && is_numeric($liga)) {
		$korisnik = $_SESSION['aktivni_korisnik_id'];
		$sql="SELECT * from liga WHERE liga_id = $liga AND moderator_id = $korisnik";
		$rs=izvrsiUpit($bp,$sql);
		if(mysqli_num_rows($rs)>0) { 
		return true;
		}
		
	}
	return false;
}

function mozeUrediti($utakmica_id) {
	global $bp;
	if(isset($_SESSION['aktivni_korisnik_id']) && isset($_SESSION['aktivni_korisnik_tip'])) {
		$korisnik_id=$_SESSION['aktivni_korisnik_id'];
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
				$rezultat_1=$row['rezultat_1'];
				global $liga;
			    $liga=$row['liga_id'];
				if($rezultat_1==-1) {return true;} else {return false;}
			}
			
	}
	return false;
}
	if(isset($_GET["liga"])  && !empty($_GET["liga"]) && is_numeric($_GET["liga"])) {
		$idLige = $_GET["liga"];
		
			if(isset($_SESSION['aktivni_korisnik_id']) && !empty($_SESSION['aktivni_korisnik_id'])) {
				$sql_vel="SELECT COUNT(*) FROM utakmica
						INNER JOIN momcad n1 ON
						utakmica.momcad_1 = n1.momcad_id
						INNER JOIN momcad n2 ON
						utakmica.momcad_2 = n2.momcad_id
						WHERE n1.liga_id = $idLige AND n2.liga_id = $idLige";
				
				$sql="SELECT utakmica_id, datum_vrijeme_pocetka, datum_vrijeme_zavrsetka, momcad_1, momcad_2,
						 n1.naziv AS naziv1, n2.naziv AS naziv2, rezultat_1, rezultat_2, utakmica.opis 
						 FROM utakmica
						 INNER JOIN momcad n1 ON utakmica.momcad_1 = n1.momcad_id
						 INNER JOIN momcad n2 ON utakmica.momcad_2 = n2.momcad_id
						 WHERE n1.liga_id = $idLige AND n2.liga_id = $idLige ORDER BY datum_vrijeme_zavrsetka DESC LIMIT ".$vel_utakmice;
			} else {
				$sql_vel="SELECT COUNT(*) FROM utakmica
						INNER JOIN momcad n1 ON utakmica.momcad_1 = n1.momcad_id
						INNER JOIN momcad n2 ON utakmica.momcad_2 = n2.momcad_id
						WHERE n1.liga_id = $idLige AND n2.liga_id = $idLige AND datum_vrijeme_zavrsetka < NOW()";
						
				$sql="SELECT utakmica_id, datum_vrijeme_pocetka, datum_vrijeme_zavrsetka, momcad_1, momcad_2, 
						n1.naziv AS naziv1, n2.naziv AS naziv2, rezultat_1, rezultat_2, utakmica.opis
						FROM utakmica
						INNER JOIN momcad n1 ON utakmica.momcad_1 = n1.momcad_id
						INNER JOIN momcad n2 ON utakmica.momcad_2 = n2.momcad_id
						WHERE n1.liga_id = $idLige AND n2.liga_id = $idLige AND datum_vrijeme_zavrsetka < NOW() LIMIT ".$vel_utakmice;
					}
		  $rs=izvrsiUpit($bp,$sql_vel);
		  $red=mysqli_fetch_array($rs);
		  $broj_redaka=$red[0];
		  $broj_stranica=ceil($broj_redaka/$vel_utakmice);

		  if(isset($_GET['stranica']) && !empty($_GET['stranica']) && is_numeric($_GET['stranica'])){
				$sql=$sql." OFFSET ".(($_GET['stranica']-1)*$vel_utakmice);
				$aktivna=$_GET['stranica'];
			} else $aktivna=1;
			
			$rs=izvrsiUpit($bp,$sql);
			if(mysqli_num_rows($rs)==0) {
				echo '<div class="greska"><label>Liga ne postoji ili ne sadrži niti jednu utakmicu!</label></div>';
			}
			echo "<table class='tablica'>";
			if(isset($_SESSION['aktivni_korisnik_tip'])){echo "<caption>Popis svih utakmica</caption>";}
				else {echo "<caption>Popis završenih utakmica</caption>";}
				echo "<thead><tr>
				<th>Vrijeme početka</th>
				<th>Vrijeme završetka</th>
				<th>Momčadi</th>
				<th>Rezultat</th>
				<th>Opis</th>
				<th></th>
				<th></th>
			</tr></thead>";
			echo "<tbody>";
			while(list($utakmica_id,$datum_vrijeme_pocetka,$datum_vrijeme_zavrsetka,$momcad_1,$momcad_2,$naziv_1,$naziv_2,$rezultat_1,$rezultat_2,$opis)=mysqli_fetch_array($rs)){
				$datum_poc=pretvoriDatum($datum_vrijeme_pocetka);
				$datum_zav=pretvoriDatum($datum_vrijeme_zavrsetka);
					 echo "<tr>
					<td>$datum_poc</td>
					<td>$datum_zav</td>
					<td>$naziv_1 - $naziv_2</td>";
					if($rezultat_1==-1 && $rezultat_2==-1) echo '<td>-</td>';
			        else echo "<td>$rezultat_1 - $rezultat_2</td>";
					echo "<td>$opis</td>";
					
					if(strtotime($datum_vrijeme_zavrsetka) > strtotime(date("d-m-Y H:i:s"))) {
						echo"<td><a href='listic.php?utakmica_id=$utakmica_id'>Stvori listić</a></td>";
					} else {echo '<td></td>';}
					
					if(isset($_SESSION['aktivni_korisnik_tip']) && $_SESSION['aktivni_korisnik_tip']!=2 && mozeUrediti($utakmica_id)){
						echo "<td><a href='utakmica.php?utakmica_id=$utakmica_id'>Uredi</a></td>";
					} else {echo '<td></td>';}
				
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";

			echo '<div id="paginacija">';
			if ($aktivna!=1){
				$prethodna=$aktivna-1;
				echo "<a href=\"utakmice.php?liga=".$_GET['liga']."&stranica=".$prethodna."\">&lt;</a>";
			}
			for($i=1;$i<=$broj_stranica;$i++){
				echo "<a class='";
				if($aktivna==$i)echo " aktivna";
				echo "' href=\"utakmice.php?liga=".$_GET['liga']."&stranica=".$i."\">$i</a>";
			}
			if($aktivna<$broj_stranica){
				$sljedeca=$aktivna+1;
				echo "<a href=\"utakmice.php?liga=".$_GET['liga']."&stranica=".$sljedeca."\">&gt;</a>";
			}
			echo '<br/>';
			if($aktivni_korisnik_tip==0 || mozeDodati($idLige)) {echo "</br><a href='utakmica.php?liga_id=$idLige'>DODAJ UTAKMICU</a>";}
			echo '</div>';
			}
else {
	echo '<div style="text-align:center;"><label class="greska">Nije odabrana liga!</label></div>';
	header("Location:lige.php");
}
	
?>
<?php
	zatvoriVezuNaBazu($bp);
	include("podnozje.php");
?>

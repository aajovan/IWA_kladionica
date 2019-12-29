<?php
	include_once("zaglavlje.php");
	include_once("datum_vrijeme.php");
	$bp=spojiSeNaBazu();
?>
<?php
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
				if($rezultat_1==-1) {return true;} else {return false;}
			}
			
	}
	return false;
}
	
	if(!isset($_SESSION['aktivni_korisnik']))header("Location:index.php");
	$sql="SELECT COUNT(*) FROM utakmica WHERE datum_vrijeme_zavrsetka > NOW()";
	$rs=izvrsiUpit($bp,$sql);
	$red=mysqli_fetch_array($rs);
	$broj_redaka=$red[0];
	$broj_stranica=ceil($broj_redaka/$vel_utakmice);

	$sql="SELECT utakmica_id, datum_vrijeme_pocetka, datum_vrijeme_zavrsetka, momcad_1, momcad_2, t2.naziv AS naziv1, t3.naziv AS naziv2, utakmica.opis
         FROM utakmica
         INNER JOIN momcad t2 ON utakmica.momcad_1 = t2.momcad_id
         INNER JOIN momcad t3 ON utakmica.momcad_2 = t3.momcad_id
         WHERE datum_vrijeme_zavrsetka > NOW() LIMIT ".$vel_utakmice;
	if(isset($_GET['stranica']) && !empty($_GET['stranica']) && is_numeric($_GET['stranica'])){
		$sql=$sql." OFFSET ".(($_GET['stranica']-1)*$vel_utakmice);
		$aktivna=$_GET['stranica'];
	}
	else $aktivna=1;
	$rs=izvrsiUpit($bp,$sql);
	if(mysqli_num_rows($rs)==0) {
		echo '<div style="text-align:center;"><label class="greska">Trenutno nema utakmica u tijeku!</label></div>';
	}
	echo "<table class='tablica'>";
		echo "<caption>Popis utakmica koje nisu završile</caption>";
		echo "<thead><tr>
		<th>Vrijeme početka</th>
		<th>Vrijeme završetka</th>
		<th>Momčadi</th>
		<th>Opis</th>
		<th></th>
		<th></th>
	</tr></thead>";

	echo "<tbody>";
	while(list($utakmica_id,$datum_vrijeme_pocetka,$datum_vrijeme_zavrsetka,$momcad_1,$momcad_2,$naziv_1,$naziv_2,$opis)=mysqli_fetch_array($rs)){
            $datum_poc=pretvoriDatum($datum_vrijeme_pocetka);
			$datum_zav=pretvoriDatum($datum_vrijeme_zavrsetka);
			echo "<tr>
			<td>$datum_poc</td>
			<td>$datum_zav</td>
			<td>$naziv_1 - $naziv_2</td>
			<td>$opis</td>
			<td><a href='listic.php?utakmica_id=$utakmica_id'>Stvori listić</a></td>";
			
			if(isset($_SESSION['aktivni_korisnik_tip']) && $_SESSION['aktivni_korisnik_tip']!=2 && mozeUrediti($utakmica_id)){
						echo "<td><a href='utakmica.php?utakmica_id=$utakmica_id'>Uredi</a></td>";
					} else {echo '<td></td>';}
	}
	echo "</tr></tbody>";
	echo "</table>";
	echo '<div id="paginacija">';
	if ($aktivna!=1){
		$prethodna=$aktivna-1;
		echo "<a href=\"u_tijeku.php?stranica=".$prethodna."\">&lt;</a>";
	}
	for($i=1;$i<=$broj_stranica;$i++){
		echo "<a class='";
		if($aktivna==$i)echo " aktivna";
		echo "' href=\"u_tijeku.php?stranica=".$i."\">$i</a>";
	}
	if($aktivna<$broj_stranica){
		$sljedeca=$aktivna+1;
		echo "<a href=\"u_tijeku.php?stranica=".$sljedeca."\">&gt;</a>";
	}

	echo '</div>';
	
?>
<?php
	zatvoriVezuNaBazu($bp);
	include("podnozje.php");
?>

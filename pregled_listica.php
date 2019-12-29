<?php
	include_once("zaglavlje.php");
	include_once("datum_vrijeme.php");
	if(!isset($_SESSION['aktivni_korisnik']))header("Location:index.php");
	$bp=spojiSeNaBazu();
?>
<?php
	if(isset($_SESSION['aktivni_korisnik'])) {
		$korisnik = $_SESSION['aktivni_korisnik_id'];
		$sql="SELECT COUNT(*) FROM listic WHERE korisnik_id = $korisnik";
	    $rs=izvrsiUpit($bp,$sql);
	    $red=mysqli_fetch_array($rs);
	    $broj_redaka=$red[0];
	    $broj_stranica=ceil($broj_redaka/$vel_utakmice);
	$sql="SELECT listic_id, korisnik_id, listic.utakmica_id, ocekivani_rezultat, status, utakmica.datum_vrijeme_zavrsetka,n1.naziv AS naziv1, n2.naziv AS naziv2, rezultat_1, rezultat_2 FROM listic, utakmica
		INNER JOIN momcad n1 ON utakmica.momcad_1 = n1.momcad_id
		INNER JOIN momcad n2 ON utakmica.momcad_2 = n2.momcad_id
		WHERE utakmica.utakmica_id=listic.utakmica_id AND korisnik_id = $korisnik  LIMIT ".$vel_utakmice;
	if(isset($_GET['stranica'])){
		$sql=$sql." OFFSET ".(($_GET['stranica']-1)*$vel_utakmice);
		$aktivna=$_GET['stranica'];
	}
	else $aktivna=1;
	$rs=izvrsiUpit($bp,$sql);
	if(mysqli_num_rows($rs)==0) {
		echo '<div class="greska"><label>Ne postoji niti jedan listić!</label></div>';
	}
	echo "<table class='tablica'>";
		echo "<caption>Popis svih listića</caption>";
		echo "<thead><tr>
		<th>Utakmica</th>
		<th>Završetak</th>
		<th>Rezultat</th>
		<th>Očekivani rezultat</th>
		<th>Status</th>
		<th></th>
	</tr></thead>";
	$mozeUrediti=true;
	echo "<tbody>";
	while(list($listic_id,$korisnik_id,$utakmica_id,$ocekivani_rezultat,$status, $datum_vrijeme_zavrsetka,$naziv1, $naziv2, $rezultat1, $rezultat2)=mysqli_fetch_array($rs)){
			$datum_zav=pretvoriDatum($datum_vrijeme_zavrsetka);
			echo "<tr>
			<td>$naziv1 - $naziv2</td>
			<td>$datum_zav</td>";
			
			if($rezultat1==-1 && $rezultat2==-1) echo '<td>-</td>';
			else echo "<td>$rezultat1 - $rezultat2</td>";
			if($ocekivani_rezultat==0) echo '<td>Neriješeno</td>';
			else if($ocekivani_rezultat==1)echo "<td>Pobjeda za $naziv1</td>";
			else echo "<td>Pobjeda za $naziv2</td>";
			if($status=='P')echo '<td>PREDAN</td>';
			else if($status=='O')echo '<td>NIJE PREDAN</td>';
			else if($status=='D')echo '<td>DOBITAN</td>';
			else echo '<td>NEDOBITAN</td>';
			if(strtotime($datum_vrijeme_zavrsetka) > strtotime(date("d-m-Y H:i:s")) && $status=='O') {echo "<td><a href='listic.php?utakmica_id=$utakmica_id'>Uredi</a></td>";}
			else  {echo '<td></td>';}
	}
	echo "</tr></tbody>";
	echo "</table>";
	echo '<div id="paginacija">';

	if ($aktivna!=1){
		$prethodna=$aktivna-1;
		echo "<a href=\"pregled_listica.php?stranica=".$prethodna."\">&lt;</a>";
	}
	for($i=1;$i<=$broj_stranica;$i++){
		echo "<a class='";
		if($aktivna==$i)echo " aktivna"; 
		echo "' href=\"pregled_listica.php?stranica=".$i."\">$i</a>";
	}
	if($aktivna<$broj_stranica){
		$sljedeca=$aktivna+1;
		echo "<a href=\"pregled_listica.php?stranica=".$sljedeca."\">&gt;</a>";
	}
	echo '</div>';
	}

	
?>
<?php
	zatvoriVezuNaBazu($bp);
	include("podnozje.php");
?>

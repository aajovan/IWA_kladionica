<?php
	include_once("zaglavlje.php");
	$bp=spojiSeNaBazu();
?>
<?php
	if(!isset($_SESSION['aktivni_korisnik']) || $_SESSION['aktivni_korisnik_tip']!=0) {header("Location:index.php");}
	$sql="SELECT COUNT(*) FROM momcad";
	$rs=izvrsiUpit($bp,$sql);
	$red=mysqli_fetch_array($rs);
	$broj_redaka=$red[0];
	$broj_stranica=ceil($broj_redaka/$vel_utakmice);

	$sql="SELECT momcad_id, momcad.naziv, momcad.opis, liga.naziv FROM momcad, liga WHERE momcad.liga_id = liga.liga_id ORDER BY momcad_id LIMIT ".$vel_utakmice;
	if(isset($_GET['stranica']) && !empty($_GET['stranica']) && is_numeric($_GET['stranica'])){
		$sql=$sql." OFFSET ".(($_GET['stranica']-1)*$vel_utakmice);
		$aktivna=$_GET['stranica'];
	}
	else $aktivna=1;
	$rs=izvrsiUpit($bp,$sql);
	echo "<table class='tablica'>";
		echo "<caption>Popis momčadi</caption>";
		echo "<thead><tr>
		<th>Naziv momčadi</th>
		<th>Opis</th>
		<th>Naziv lige</th>
		<th></th>
	</tr></thead>";
	echo "<tbody>";
	while(list($momcad_id,$momcad_naziv,$opis,$liga_naziv)=mysqli_fetch_array($rs)){
		echo "<tr>
			<td>$momcad_naziv</td>
			<td>$opis</td>
			<td>$liga_naziv</td>
			<td><a href='momcad.php?momcad_id=$momcad_id'>UREDI</a></td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";

	echo '<div id="paginacija">';
	if ($aktivna!=1){
		$prethodna=$aktivna-1;
		echo "<a href=\"momcadi.php?stranica=".$prethodna."\">&lt;</a>";
	}
	for($i=1;$i<=$broj_stranica;$i++){
		echo "<a class='";
		if($aktivna==$i)echo " aktivna"; 
		echo "' href=\"momcadi.php?stranica=".$i."\">$i</a>";
	}
	if($aktivna<$broj_stranica){
		$sljedeca=$aktivna+1;
		echo "<a href=\"momcadi.php?stranica=".$sljedeca."\">&gt;</a>";
	}
	echo '<br/>';
	if($aktivni_korisnik_tip==0)echo '<br/><a href="momcad.php">DODAJ MOMČAD</a>';
	echo '</div>';
?>
<?php
	zatvoriVezuNaBazu($bp);
	include("podnozje.php");
?>

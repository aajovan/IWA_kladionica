<?php
	include_once("zaglavlje.php");
	$bp=spojiSeNaBazu();
?>
<?php
	$sql="SELECT COUNT(*) FROM liga";
	$rs=izvrsiUpit($bp,$sql);
	$red=mysqli_fetch_array($rs);
	$broj_redaka=$red[0];
	$broj_stranica=ceil($broj_redaka/$vel_lige);

	$sql="SELECT * FROM liga ORDER BY liga_id LIMIT ".$vel_lige;
	if(isset($_GET['stranica']) && !empty($_GET['stranica']) && is_numeric($_GET['stranica'])){
		$sql=$sql." OFFSET ".(($_GET['stranica']-1)*$vel_lige);
		$aktivna=$_GET['stranica'];
	}
	else $aktivna=1;
	$rs=izvrsiUpit($bp,$sql);
	echo "<table class='tablica'>";
		echo "<caption>Popis liga</caption>";
		echo "<thead><tr>
		<th>Naziv lige</th>
		<th>Slika</th>
		<th>Video</th>
		<th>Opis</th>
		<th></th>
	</tr></thead>";
	echo "<tbody>";
	while(list($liga_id,$moderator_id,$naziv,$slika,$video,$opis)=mysqli_fetch_array($rs)){
		echo "<tr>
			<td><a href=\"utakmice.php?liga=$liga_id\">$naziv</a></td>
			<td><figure><img src='$slika' height='70px' alt='$naziv'/></figure></td>
			<td><iframe src='$video'></iframe></td>
			<td>$opis</td>";
			if($aktivni_korisnik_tip==0)echo "<td><a href='liga.php?liga_id=$liga_id'>UREDI</a></td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
	echo '<div id="paginacija">';
	if ($aktivna!=1){
		$prethodna=$aktivna-1;
		echo "<a href=\"lige.php?stranica=".$prethodna."\">&lt;</a>";
	}
	for($i=1;$i<=$broj_stranica;$i++){
		echo "<a class='";
		if($aktivna==$i)echo " aktivna"; 
		echo "' href=\"lige.php?stranica=".$i."\">$i</a>";
	}
	if($aktivna<$broj_stranica){
		$sljedeca=$aktivna+1;
		echo "<a href=\"lige.php?stranica=".$sljedeca."\">&gt;</a>";
	}
	echo '<br/>';
	if($aktivni_korisnik_tip==0)echo '</br><a href="liga.php">DODAJ LIGU</a>';
	echo '</div>';
?>

<?php
	zatvoriVezuNaBazu($bp);
	include("podnozje.php");
?>

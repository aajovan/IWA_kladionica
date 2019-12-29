<?php
	include_once("zaglavlje.php");
	$bp=spojiSeNaBazu();
?>

<?php
    if(!isset($_SESSION['aktivni_korisnik']) || $_SESSION['aktivni_korisnik_tip']!=0) {header("Location:index.php");}
	$sql="SELECT COUNT(*) FROM korisnik";
	$rs=izvrsiUpit($bp,$sql);
	$red=mysqli_fetch_array($rs);
	$broj_redaka=$red[0];
	$broj_stranica=ceil($broj_redaka/$vel_korisnik);

	$sql="SELECT * FROM korisnik ORDER BY korisnik_id LIMIT ".$vel_korisnik;
	if(isset($_GET['stranica']) && !empty($_GET['stranica']) && is_numeric($_GET['stranica'])){
		$sql=$sql." OFFSET ".(($_GET['stranica']-1)*$vel_korisnik);
		$aktivna=$_GET['stranica'];
	}
	else $aktivna = 1;

	$rs=izvrsiUpit($bp,$sql);
	echo "<table class='tablica'>";
	echo "<caption>Popis korisnika sustava</caption>";
	echo "<thead><tr>
		<th>Korisničko ime</th>
		<th>Ime</th>
		<th>Prezime</th>
		<th>E-mail</th>
		<th>Lozinka</th>
		<th>Slika</th>
		<th></th>";
	echo "</tr></thead>";

	echo "<tbody>";
	while(list($id,$tip,$kor_ime,$lozinka,$ime,$prezime,$email,$slika)=mysqli_fetch_array($rs)){
		echo "<tr>
			<td>$kor_ime</td>
			<td>$ime</td>";
		echo "<td>".(empty($prezime)?"&nbsp;":"$prezime")."</td>
			<td>".(empty($email)?"&nbsp;":"$email")."</td>
			<td>$lozinka</td>
			<td><figure><img src='$slika' width='70' height='100' alt='Slika korisnika $ime $prezime'/></figure></td>
            <td><a href='korisnik.php?korisnik=$id'>UREDI</a></td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";


	echo '<div id="paginacija">';

	if($aktivna!=1){
		$prethodna=$aktivna-1;
		echo "<a href=\"korisnici.php?stranica=".$prethodna."\">&lt;</a>";
	}
	for($i=1;$i<=$broj_stranica;$i++){
		echo "<a class='";
		if($aktivna==$i)echo " aktivna"; 
		echo "' href=\"korisnici.php?stranica=".$i."\">$i</a>";
	}

	if($aktivna<$broj_stranica){
		$sljedeca=$aktivna+1;
		echo "<a href=\"korisnici.php?stranica=".$sljedeca."\">&gt;</a>";
	}
	echo "<br/>";
	echo '<br/><a href="korisnik.php">DODAJ KORISNIKA</a>';
	echo '</div>';
?>

<?php
	zatvoriVezuNaBazu($bp);
	include("podnozje.php");
?>

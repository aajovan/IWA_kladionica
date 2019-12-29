<?php
	include_once("zaglavlje.php");
	$bp=spojiSeNaBazu();
?>
<?php
		$greska='';
		$group='L';
		if(isset($_GET['group'])) {$group=$_GET['group'];}
		if(!isset($_SESSION['aktivni_korisnik']) || $_SESSION['aktivni_korisnik_tip']==2) {header("Location:index.php");}
		echo '<div id="opis">
			<h2>Napomena</h2>
			<p>Datumi se unose u formatu dd.mm.yyyy., npr. 03.07.2019.,
			za pretragu po korisniku unesite jedno ili više slova iz imena ili prezimena</p>
			<p> Ukoliko je unesen samo datum od, datum do se postavlja na današnji datum</p>
			<p> Ukoliko je unesen samo datum do, dpotrebno je unijeti datum od</p>
			<br/>
		    </div>';
		echo '<form method="GET" action="statistika.php">
			<table class="tablica"><caption>FILTER</caption><tbody>
			<tr>';
		echo '<td><label for="korisnik">Korisnik:</label>';
		echo '<input type="text" value="';if(isset($_GET['korisnik'])&&!isset($_GET['reset']))echo $_GET['korisnik'];
		echo '" name="korisnik" id="korisnik"/></td>
			<td><label for="liga">Liga:</label>';
		echo '<select id="liga" name="liga">
		      <option disabled selected value> -- Nije odabrano -- </option>';
		if($_SESSION['aktivni_korisnik_tip']==1) {
			$korisnik=$_SESSION['aktivni_korisnik_id'];
			$sql_liga="SELECT liga_id, naziv FROM liga WHERE moderator_id=$korisnik";
		} else {$sql_liga="SELECT liga_id, naziv FROM liga";}
		$rs_liga=izvrsiUpit($bp,$sql_liga);
        while(list($id_lige,$naziv)=mysqli_fetch_array($rs_liga)) {
		 if(isset($_GET['liga'])&&!isset($_GET['reset']) && $_GET['liga']==$id_lige)
			{echo "<option value='$id_lige' selected>$naziv</option>";
		    } else {echo "<option value='$id_lige'>$naziv</option>";}
		}
		
		echo '</select>';
		echo '<td><label for="od">Od datuma:</label>';
		echo '<input type="text" placeholder="dd.mm.yyyy" value="';if(isset($_GET['od'])&&!isset($_GET['reset']))echo $_GET['od'];
		echo '" name="od" id="od" size="10"/></td>
			<td><label for="do">Do datuma:</label>';
		echo '<input type="text" placeholder="dd.mm.yyyy" value="';if(isset($_GET['do'])&&!isset($_GET['reset']))echo $_GET['do'];
		echo '" name="do" id="do" size="10"/></td>
		     <td> <input type="radio" name="group" value="L" '; if ($group=='L') {echo 'checked';}echo '> PO LIGI<br>';
				 echo'<input type="radio" name="group" value="K" '; if ($group=='K') {echo 'checked';}echo '>PO KORISNIKU<br></td>';
			echo '<td><input type="submit" name="reset" value="Izbriši"/></td>
			<td><input type="submit" name="submit" value="Filter"/></td>
			</tr></tbody>
			</table></form>';
	
	    $sql="SELECT l.korisnik_id, SUM(CASE WHEN l.status = 'D' THEN 1 ELSE 0 END) AS dobitni, 
			SUM(CASE WHEN l.status='N' THEN 1 ELSE 0 END) AS nedobitni, m.liga_id, liga.naziv, korisnik.ime, korisnik.prezime
			FROM listic l, utakmica u ,momcad m, liga, korisnik
			WHERE l.utakmica_id=u.utakmica_id AND m.momcad_id=u.momcad_1 AND m.liga_id = liga.liga_id AND l.korisnik_id = korisnik.korisnik_id";
        if($_SESSION['aktivni_korisnik_tip']==1) {
			$korisnik=$_SESSION['aktivni_korisnik_id'];
			$sql=$sql." AND liga.moderator_id=$korisnik";
		}
		
		if(isset($_GET['korisnik']) && !empty($_GET['korisnik'])){
			$korisnik=mysqli_real_escape_string($bp,$_GET['korisnik']);
			$sql=$sql." AND (korisnik.ime like'%$korisnik%' OR korisnik.prezime like'%$korisnik%')";
		}
		if(isset($_GET['liga']) && !empty($_GET['liga'])){
			$liga=$_GET['liga'];
			$sql=$sql." AND m.liga_id=$liga";
		}
		
		if(isset($_GET['od'])&&strlen($_GET['od']>0)){
		  $od = strtotime($_GET['od']); 
		  $od=date('Y-m-d H:i:s',$od ); 
			$sql=$sql." AND u.datum_vrijeme_zavrsetka BETWEEN '$od'";
			if(!isset($_GET['do'])||strlen($_GET['do']==0)){
				$_GET['do']=date('d-m-Y');
				$do=date('Y-m-d H:i:s'); 
			$sql=$sql." AND '$do'";	
			}
		}
		if(isset($_GET['do']) && strlen($_GET['do']>0)){
			$do=strtotime($_GET['do']); 
		  $do=date('Y-m-d H:i:s',$do); 
		  if(isset($_GET['od']) && strlen($_GET['od']>0)){
			  $sql=$sql." AND '$do'";
		  } else {$greska="Potrebno je unijeti datum od!";}
			
		}
        if($group=='L') {$sql=$sql." GROUP BY m.liga_id";
		} else {$sql=$sql." GROUP BY l.korisnik_id";}
		
		if(isset($_GET['sort'])) {$sql=$sql." ORDER BY ".$_GET['sort'];} 
	
		$rs=izvrsiUpit($bp,$sql);
		$par=$putanja.(strpos($putanja,"?")?"&":"?");
		echo "<table class='tablica'>";
		echo "<caption>Listići</caption>";
		echo '<div style="text-align:center;"><label class="greska">';if($greska!="")echo $greska;
		echo'</label></div>';
		echo "<thead><tr>";
		echo "<th>Korisnik</th>";
		echo "<th>Liga</th>";
		echo "<th><a href='".$par."sort=dobitni' >Dobitni <img src='images/strelica.png' alt='strelica' style='border:0;' title='Sortiraj prema dobitnim listićima'/></a></th>";
		echo "<th><a href='".$par."sort=nedobitni' >Nedobitni <img src='images/strelica.png' alt='strelica' style='border:0;' title='Sortiraj prema nedobitnim'/></a></th>";
		echo "</tr></thead>";
		date_default_timezone_set("Europe/Zagreb");

		echo '<tbody>';
		if(isset($group) && $group=='L') {
			while(list($korisnik_id,$dobitni,$nedobitni,$liga_id,$naziv_lige,$ime,$prezime)=mysqli_fetch_array($rs)){
			echo '<tr>';
			if(isset($_GET['korisnik']) && !empty($_GET['korisnik']) && (isset($_GET['liga']) && !empty($_GET['liga']))) {
				echo"<td>$ime $prezime</td>
			<td>$naziv_lige</td>"; 
			} else if (isset($_GET['liga']) && !empty($_GET['liga'])) {
				echo"<td>-</td>
			<td>$naziv_lige</td>"; 
			}else if (isset($_GET['korisnik']) && !empty($_GET['korisnik'])) {
				echo"<td>$ime $prezime</td>
			<td>$naziv_lige</td>"; 
			} else {echo"<td>-</td>
			<td>$naziv_lige</td>"; }
				echo"<td>$dobitni</td>
				<td>$nedobitni</td>
			</tr>";
			}
		} else {
			while(list($korisnik_id,$dobitni,$nedobitni,$liga_id,$naziv_lige,$ime,$prezime)=mysqli_fetch_array($rs)){
			echo '<tr>';
			if(isset($_GET['korisnik']) && !empty($_GET['korisnik']) && (isset($_GET['liga']) && !empty($_GET['liga']))) {
				echo"<td>$ime $prezime</td>
			<td>$naziv_lige</td>"; 
			} else if (isset($_GET['liga']) && !empty($_GET['liga'])) {
				echo"<td>$ime $prezime</td>
			<td>$naziv_lige</td>"; 
			}else if (isset($_GET['korisnik']) && !empty($_GET['korisnik'])) {
				echo"<td>$ime $prezime</td>
			<td>-</td>"; 
			} else {echo"<td>$ime $prezime</td>
			<td>-</td>"; }
				echo"<td>$dobitni</td>
				<td>$nedobitni</td>
			</tr>";
			}
		}
		
		echo "</tbody>";
		echo "</table>";
?>
<?php
	zatvoriVezuNaBazu($bp);
	include("podnozje.php");
?>

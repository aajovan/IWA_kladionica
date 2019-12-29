<?php
	include_once("zaglavlje.php");
	$bp=spojiSeNaBazu();
?>
<?php
if(!isset($_SESSION['aktivni_korisnik']) || $_SESSION['aktivni_korisnik_tip']!=0) {header("Location:index.php");}
	$greska="";
	if(isset($_POST['submit'])){
		if(strlen($_POST['liga_id']) ==0 && strlen($_POST['momcad_naziv'])==0){$greska="Obavezna polja za unos nisu ispunjena";}
		if(empty($greska)){
			$liga_id=$_POST['liga_id'];
			$momcad_naziv=$_POST['momcad_naziv'];
            if(isset($_POST['opis'])) {
				$opis=$_POST['opis'];
			} else {
				$opis='';
			}
			
			$id=$_POST['novi'];
			if($id==0){
				$sql="INSERT INTO momcad
					(liga_id, naziv, opis)
					VALUES
					('$liga_id','$momcad_naziv','$opis');
				";
			}
			else{
				$sql="UPDATE momcad SET
					liga_id='$liga_id',
					naziv='$momcad_naziv',
					opis='$opis'
					WHERE momcad_id='$id'
				";
			}
			izvrsiUpit($bp,$sql);
			header("Location:momcadi.php");
		}
	}
	if(isset($_GET['momcad_id']) && !empty($_GET['momcad_id']) && is_numeric($_GET['momcad_id'])){
		$id=$_GET['momcad_id'];
		$sql="SELECT momcad_id, momcad.naziv, momcad.opis, momcad.liga_id, liga.naziv FROM momcad, liga WHERE momcad.liga_id = liga.liga_id AND momcad.momcad_id='$id'";
		$rs=izvrsiUpit($bp,$sql);
		if(mysqli_num_rows($rs)>0){
		list($momcad_id,$momcad_naziv,$opis,$liga_id,$liga_naziv)=mysqli_fetch_array($rs);
		} else {header("Location:momcadi.php");}
	}
	else{
		$momcad_id = "";
		$momcad_naziv= "";
		$opis = "";
		$liga_id = "";
		$liga_naziv = "";
		
	}
	$sql2="SELECT liga_id, naziv FROM liga";
		$rs_2=izvrsiUpit($bp,$sql2);
		if(isset($_POST['reset']))header("Location:momcadi.php");
?>
<form method="POST" action="<?php if(isset($_GET['momcadi_id']))echo "momcad.php?momcad_id=$id";else echo "momcad.php";?>">
	<table class="forma">
		<caption>
			<?php
				if(isset($id) && !empty($id)) echo "Uredi momčad";
				else echo "Dodaj momčad";
			?>
		</caption>
		<tbody>
			<tr>
				<td colspan="2" style="display:none">
					<input type="hidden" name="novi" value="<?php if(isset($id) && !empty($id)){echo $id;}else {echo '0';}?>"/>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center; <?php if($greska=="")echo 'display:none;'; ?>>
					<label class="greska"><?php if($greska!="")echo $greska; ?></label>
				</td>
			</tr>
			<tr>
				<td>
					<label for="momcad_naziv"><strong>Naziv momčadi:</strong></label>
				</td>
				<td>
					<input type="text" name="momcad_naziv" id="momcad_naziv"  minlength="5" maxlength="100"
						placeholder="Unesite naziv momčadi"
						required value="<?php if(!isset($_POST['momcad_naziv']))echo $momcad_naziv; else echo $_POST['momcad_naziv'];?>"/>
				</td>
			</tr>
			<tr>
				<td><label for="liga_id"><strong>Liga:</strong></label></td>
				<td>
					<select id="liga_id" name="liga_id">
						<?php
							if(isset($_POST['liga_id']) || !empty($liga_id)){
								while(list($id_lige,$naziv)=mysqli_fetch_array($rs_2)) {
									if($_POST['liga_id']==$id_lige){echo "<option value='$id_lige' selected>$naziv</option>";}
									else if($id_lige==$liga_id){echo "<option value='$id_lige' selected>$naziv</option>";}
									else {
										echo "<option value='$id_lige'>$naziv</option>";
									}
									
								}
								
				
							}
							else{
								while(list($id_lige,$naziv)=mysqli_fetch_array($rs_2)) {
									echo "<option value='$id_lige'>$naziv</option>";
							}
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="opis"><strong>Opis:</strong></label>
				</td>
				<td>
					<textarea name="opis" id="opis" rows="4" cols="50" ><?php if(isset($_POST['opis'])){echo $_POST['opis'];} else{echo $opis;}?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<?php
						echo '<input type="submit" name="reset" style="width:auto; margin:5px" value="Odustani"/><input type="submit" name="submit" style="width:auto; margin:5px" value="Pošalji"/>';
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

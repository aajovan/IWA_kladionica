<?php
	include_once("zaglavlje.php");
	$bp=spojiSeNaBazu();
?>
<?php
    if(!isset($_SESSION['aktivni_korisnik']) || $_SESSION['aktivni_korisnik_tip']!=0) {header("Location:index.php");}
	$greska="";
	if(isset($_POST['submit'])){
		if(strlen($_POST['moderator_id'])==0&&strlen($_POST['naziv'])==0&&strlen($_POST['slika'])==0)$greska="Obavezna polja za unos nisu ispunjena"; 
		if(empty($greska)){
			$moderator_id=$_POST['moderator_id'];
			$naziv=$_POST['naziv'];
			$slika=$_POST['slika'];
			if(isset($_POST['video'])) {
				$video=$_POST['video'];
			} else {
				$video='';
			}
			if(isset($_POST['opis'])) {
				$opis=$_POST['opis'];
			} else {
				$opis='';
			}
			$id=$_POST['novi'];
			if($id==0){
				$sql="INSERT INTO liga
					(moderator_id, naziv, slika, video, opis)
					VALUES
					('$moderator_id','$naziv','$slika','$video','$opis');
				";
			}
			else{
				$sql="UPDATE liga SET
					moderator_id='$moderator_id',
					naziv='$naziv',
					slika='$slika',
					video='$video',
					opis='$opis'
					WHERE liga_id='$id'
				";
			}
			izvrsiUpit($bp,$sql);
			header("Location:lige.php");
		}
	}
	if(isset($_GET['liga_id']) && is_numeric($_GET['liga_id'])){
		$id=$_GET['liga_id'];
		$sql="SELECT * FROM liga WHERE liga_id='$id'";
		$rs=izvrsiUpit($bp,$sql);
		if(mysqli_num_rows($rs)>0) {
		list($liga_id,$moderator_id,$naziv,$slika,$video,$opis)=mysqli_fetch_array($rs);
		} else {header("Location:lige.php");}
	}
	else{
		$moderator_id = "";
		$naziv= "";
		$slika = "";
		$video = "";
		$opis = "";
	}
	$sql2="SELECT korisnik_id, korisnicko_ime FROM korisnik WHERE tip_korisnika_id=1";
		$rs_2=izvrsiUpit($bp,$sql2);
		if(isset($_POST['reset']))header("Location:liga.php");
?>
<form method="POST" action="<?php if(isset($_GET['liga_id']) && is_numeric($_GET['liga_id']))echo "liga.php?liga=$id";else echo "liga.php";?>">
	<table class="forma">
		<caption>
			<?php
				if(isset($id) && !empty($id)) {echo "Uredi ligu";}
				else {echo "Dodaj ligu";}
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
				<td><label for="moderator_id"><strong>Moderator:</strong></label></td>
				<td>
					<select id="moderator_id" name="moderator_id">
						<?php
							if(isset($_POST['moderator_id']) || !empty($moderator_id)){
								while(list($korisnik_id,$korisnicko_ime)=mysqli_fetch_array($rs_2)) {
									if($_POST['moderator_id']==$korisnik_id){echo "<option value='$korisnik_id' selected>$korisnicko_ime</option>";}
									else if($moderator_id==$korisnik_id){echo "<option value='$korisnik_id' selected>$korisnicko_ime</option>";}
									else {
										echo "<option value='$korisnik_id'>$korisnicko_ime</option>";
									}
									
								}
								
				
							}
							else{
								while(list($korisnik_id,$korisnicko_ime)=mysqli_fetch_array($rs_2)) {
									echo "<option value='$korisnik_id'>$korisnicko_ime</option>";
							}
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="naziv"><strong>Naziv lige:</strong></label>
				</td>
				<td>
					<input type="text" name="naziv" id="naziv" minlength="4" maxlength="80"
						placeholder="Upišite naziv lige"
						required value="<?php if(!isset($_POST['naziv']))echo $naziv; else echo $_POST['naziv'];?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="slika"><strong>Slika:</strong></label>
				</td>
				<td>
					<input type="url" name="slika" id="slika" placeholder="Unesite URL slike"
						maxlength="250" required value="<?php if(!isset($_POST['slika']))echo $slika; else echo $_POST['slika'];?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="video"><strong>Video:</strong></label>
				</td>
				<td>
					<input type="url" name="video" id="video" placeholder="Unesite URL videa"
						maxlength="250" value="<?php if(!isset($_POST['video']))echo $video; else echo $_POST['video'];?>"/>
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
					
						 <input type="submit" style="width:auto; margin:5px" name="submit" value="Pošalji"/>
					
				</td>
			</tr>
		</tbody>
	</table>
</form>
<?php
	zatvoriVezuNaBazu($bp);
	include("podnozje.php");
?>

<?php
	include_once("zaglavlje.php");
?>

<article>
	<br/>
	<table class="forma">
		<caption>Korisnici sustava</caption>
		<thead>
			<tr>
				<th style="text-align:right; padding-right:10px;">Popis uloga</th>
				<th style="text-align:left; padding-left:10px;">Opis uloga</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Administrator</td>
				<td>Dodavanje i uređivanje korisnika, momčadi i liga, dodjeljivanje moderatora za ligu</td>
			</tr>
			<tr>
				<td>Moderator</td>
				<td>Unosi, pregledava i ažurira utakmice svoje lige, vidi statistiku listića</td>
			</tr>
			<tr>
				<td>Obični korisnik</td>
				<td>Vidi popis nezavršenih utakmica i može kreirati listić, vidi stanje listića</td>
			</tr>
			<tr>
				<td>Anonimni korisnik</td>
				<td>Pregledavanje liga i završenih utakmica</td>
			</tr>
		</tbody>
	</table>
	<br/>
	<table class="forma">
		<caption>Datoteke sustava</caption>
		<thead>
			<tr>
				<th style="text-align:right; padding-right:10px;">Popis datoteka</th>
				<th style="text-align:left; padding-left:10px;">Opis datoteka</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>baza.php</td>
				<td>Skripta za rad s bazom podataka</td>
			</tr>
			<tr>
				<td>index.php</td>
				<td>Kratak opis aplikacije</td>
			</tr>
			<tr>
				<td>zaglavlje.php</td>
				<td>Zaglavlje, sve ostale datoteke je uključuju, sadrži meni i uključuje css</td>
			</tr>
			<tr>
				<td>podnozje.php</td>
				<td>Podnožje stranice</td>
			</tr>
			<tr>
				<td>kladionica.css</td>
				<td>CSS upute</td>
			</tr>
			<tr>
				<td>korisnici.php</td>
				<td>Tablica koja izlistava korisnike te postoji mogućnost dodavanja novog korisnika i uređivanje, samo za tip administrator</td>
			</tr>
			<tr>
				<td>utakmice.php</td>
				<td>Tablica koja izlistava utakmice prema odabranoj ligi, završene za anonimnog korisnika, sve utakmice za ostale korisnike, omogućuje unos i uređivanje</td>
			</tr>
                        <tr>
				<td>momcadi.php</td>
				<td>Tablica koja izlistava momčadi te omogućuje uređivanje, samo za tip administrator</td>
			</tr>
                        <tr>
				<td>lige.php</td>
				<td>Tablica koja izlistava lige, ako je tip administrator tada je moguće dodati ili urediti ligu</td>
			</tr>
			<tr>
				<td>pregled_listica.php</td>
				<td>Tablica koja izlistava listiće za prijavljenog korisnika, moguće je urediti ukoliko je spremljen</td>
			</tr>
			<tr>
				<td>korisnik.php</td>
				<td>Obrazac za unos novog ili uređivanje postojećeg korisnika, samo za tip administrator</td>
			</tr>
			<tr>
				<td>utakmica.php</td>
				<td>Obrazac za dodavanje ili uređivanje utakmica, samo za tip moderator ili administrator</td>
			</tr>
                        <tr>
				<td>u_tijeku.php</td>
				<td>Tablica koja prikazuje sve utakmice koje nisu završile, samo za prijavljene korisnike</td>
			</tr>
			<tr>
				<td>listic.php</td>
				<td>Obrazac za unos očekivanog rezultata određene utakmice, samo za prijavljene korisnike</td>
			</tr>
                        <tr>
				<td>liga.php</td>
				<td>Obrazac koji omogućuje dodavanje i uređivanje liga, samo za tip administrator</td>
			</tr>
                        <tr>
				<td>statistika.php</td>
				<td>Omogućuje filtriranje listića prema ligi i korisniku za određeni period</td>
			</tr>
                         <tr>
				<td>momcad.php</td>
				<td>Tablica koja izlistava momčadi, te omogućuje dodavanje i uređivanje, samo za tip administrator</td>
			</tr>
			<tr>
				<td>login.php</td>
				<td>Obrazac za prijavu u sustav</td>
			</tr>
                        <tr>
				<td>datum_vrijeme.php</td>
				<td>Skripta koja sadrži funkciju za pretvorbu datuma</td>
			</tr>
                        <tr>
				<td>o_autoru.html</td>
				<td>Web stranica koja sadrži informacije o autoru</td>
			</tr>
		</tbody>
	</table>
</article>

<?php
	include_once("podnozje.php");
?>

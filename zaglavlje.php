<?php
	include_once("baza.php");
	if(session_id()=="")session_start();

	$trenutna=basename($_SERVER["PHP_SELF"]);
	$putanja=$_SERVER['REQUEST_URI'];
	$aktivni_korisnik=0;
	$aktivni_korisnik_tip=-1;
	$vel_lige=3; 
	$vel_utakmice=10; 
    $vel_korisnik=7;

	if(isset($_SESSION['aktivni_korisnik'])){
		$aktivni_korisnik=$_SESSION['aktivni_korisnik'];
		$aktivni_korisnik_ime=$_SESSION['aktivni_korisnik_ime'];
		$aktivni_korisnik_tip=$_SESSION['aktivni_korisnik_tip'];
		$aktivni_korisnik_id=$_SESSION["aktivni_korisnik_id"];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>IWA Kladionica</title>
		<meta name="autor" content="Alen Jovan"/>
		<meta name="datum" content="2.7.2019."/>
		<meta charset="utf-8"/>
		<link href="kladionica.css" rel="stylesheet" type="text/css"/>

	</head>
	<body>
		<header>
		</header>
		<nav class="navigacija">
		<div class="navigacija_linkovi">
		
		<?php
		if($trenutna=="index.php") {
			?>
		<a href="index.php"><img src="images/index_a.png" alt="Početna"></a>
		<?php
		} else {
		?>
		<a href="index.php"><img src="images/index.png" alt="Početna"></a>
		<?php
		}
		?>
		
		<div>
		
			<?php
				switch(true){
					case $trenutna:
						switch($aktivni_korisnik_tip) {
							case 2:
							
								echo '<a href="lige.php"';
								if($trenutna=="lige.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">LIGE</a>";
								echo '<a href="u_tijeku.php"';
								if($trenutna=="u_tijeku.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">UTAKMICE U TIJEKU</a>";
								echo '<a href="pregled_listica.php"';
								if($trenutna=="pregled_listica.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">PREGLED LISTIĆA</a>";
								echo '<a href="o_autoru.html" target="_blank">O AUTORU</a>';
								break;
									case 0:
								
								echo '<a href="lige.php"';
								if($trenutna=="lige.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">LIGE</a>";
								echo '<a href="u_tijeku.php"';
								if($trenutna=="u_tijeku.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">UTAKMICE U TIJEKU</a>";
								echo '<a href="pregled_listica.php"';
								if($trenutna=="pregled_listica.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">PREGLED LISTIĆA</a>";
								echo '<a href="korisnici.php"';
								if($trenutna=="korisnici.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								    echo ">KORISNICI</a>";
									echo '<a href="momcadi.php"';
								if($trenutna=="momcadi.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">MOMČADI</a>";
								echo '<a href="statistika.php"';
								if($trenutna=="statistika.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">STATISTIKA</a>";
								echo '<a href="o_autoru.html" target="_blank">O AUTORU</a>';
								break;
									case 1:
								
								echo '<a href="lige.php"';
								if($trenutna=="lige.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">LIGE</a>";
								echo '<a href="u_tijeku.php"';
								if($trenutna=="u_tijeku.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">UTAKMICE U TIJEKU</a>";
								echo '<a href="pregled_listica.php"';
								if($trenutna=="pregled_listica.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">PREGLED LISTIĆA</a>";
									echo '<a href="statistika.php"';
								if($trenutna=="statistika.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">STATISTIKA</a>";
								echo '<a href="o_autoru.html" target="_blank">O AUTORU</a>';
								break;

							default:
								
								echo '<a href="lige.php"';
								if($trenutna=="lige.php")echo ' style="color:#3579dc;border-bottom:3px solid #3579dc;"';
								echo ">LIGE</a>";
								echo '<a href="o_autoru.html" target="_blank">O AUTORU</a>';
								break;
						}

				}
			?>
			        </div>
					</div>
					<div class="navigacija_prijava">
		
		<?php
			if($aktivni_korisnik===0){
						echo "<span><strong>Status: </strong>Neprijavljeni korisnik</span><br/>";
						
						
					}
					else{
						echo "<span><strong>Status:</strong>Dobrodošli, $aktivni_korisnik_ime</span><br/>";
					
					}
		?>
		
		
		<?php
			if($aktivni_korisnik===0){
					
						
						echo "<a class='prijava' href='login.php'>prijava</a>";
					}
					else{
						
					
						echo "<a class='prijava' href='login.php?logout=1'>odjava</a>";
					}
		?>
		
	         </div>
		</nav>
		<section id="sadrzaj">

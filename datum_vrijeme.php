<?php
function pretvoriDatum($datum) {
	$vrijeme_pocetka=substr($datum, strpos($datum, strlen($datum)));
	$datum_pocetka=substr($datum, 0, strpos($datum, " "));
	$datum_pocetka=date("d.m.Y",strtotime($datum_pocetka));
	$vrijeme_pocetka=date("H:i:s",strtotime($vrijeme_pocetka));
	$konacanDatum=$datum_pocetka.' '.$vrijeme_pocetka;
	return $konacanDatum;
}
?>
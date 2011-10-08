<?php
chdir("..");
$_GET["a"] = 1;
include "ic/sistem.php";

$keyword = $_GET["q"];
$limit = $_GET["limit"];

$vt->SorguAta("SELECT DISTINCT etiket FROM ". GaleriIslemleri::$EtiketTablosu ." WHERE etiket LIKE '{$keyword}%' LIMIT {$limit}");

$etiketler = array();
if ($vt->SatirlariAl($veri)) {
	foreach ($veri as $v)
		$etiketler[] = $v["etiket"];
}

echo implode("\n", $etiketler);
?>
<?php
chdir("..");
include "ic/sistem.php";

$galeri_id = post("g");
$etiketler = post("e");

$islem = new GaleriIslemleri($galeri_id);
$islem->Etiketle(explode(",", $etiketler));
?>
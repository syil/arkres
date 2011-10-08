<?php
// ç
chdir("..");
include "ic/dosya-yukle.inc.php";
include "ic/sistem-fonksiyonlari.inc.php";
$Ayarlar = json_decode(file_get_contents("veri/ayarlar.json"));

$allowedExtensions = $Ayarlar->Resim->Uzantilar;
// max file size in bytes
$sizeLimit = $Ayarlar->Resim->EnYuksekBoyut;
$rastgele = get("r") or $result["error"] = _r("Geçersiz işlem");
$dosya_adi = str2url(get("qqfile"));
$klasor = $Ayarlar->Resim->GeciciKlasoru . $rastgele . "/";
if (!file_exists($klasor))
	mkdir($klasor);

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
$result = $uploader->handleUpload($klasor);
// to pass data through iframe you will need to encode all html tags
if (!isset($result["error"])) {
	$result["success"] = true;
	$result["folder"] = $klasor;
	$result["path"] = urlencode($klasor.$dosya_adi);
}
	
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
?>
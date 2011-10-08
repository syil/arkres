<?php
$Ayarlar = json_decode(file_get_contents("../veri/ayarlar.json"));

echo "var cozunurluk_cerezi = '{$Ayarlar->Kullanici->CozunurlukCerezi}';";
echo "var site_adresi = '{$Ayarlar->Genel->SiteAdresi}';";
?>
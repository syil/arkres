<?php
include "ic/sistem.php";
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

$Galeriler = GaleriIslemleri::GalerileriAl();
$GaleriSayisi = count($Galeriler);
$SayfaSayisi = ceil($GaleriSayisi / $Ayarlar->Diger->SayfaBasinaGaleri);
$vt->SorguAta("SELECT DISTINCT etiket FROM ". GaleriIslemleri::$EtiketTablosu);
$vt->SatirlariAl($Etiketler);
?>
<?xml-stylesheet type="text/xsl" href="sitemap.xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
    <loc><?php echo $Ayarlar->Genel->SiteAdresi; ?></loc>
    <lastmod><?php echo date("c"); ?></lastmod>
    <changefreq>daily</changefreq>
    <priority>1</priority>
</url>
<url>
    <loc><?php echo $Ayarlar->Genel->SiteAdresi; ?>/tum</loc>
    <lastmod><?php echo date("c", strtotime($Galeriler[0]->EklenmeTarihi)); ?></lastmod>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
</url>
<?php for($i=2; $i <= $SayfaSayisi; $i++): ?>
<url>
    <loc><?php echo $Ayarlar->Genel->SiteAdresi; ?>/tum?sayfa:<?php echo $i; ?></loc>
    <lastmod><?php echo date("c", strtotime($Galeriler[0]->EklenmeTarihi)); ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
</url> 
<?php endfor; ?>
<url>
    <loc><?php echo $Ayarlar->Genel->SiteAdresi; ?>/ekle</loc>
    <lastmod><?php echo date("c", filemtime("ekle.php")); ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
</url> 
<url>
    <loc><?php echo $Ayarlar->Genel->SiteAdresi; ?>/kayit-ol</loc>
    <lastmod><?php echo date("c", filemtime("kayit-ol.php")); ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
</url>
<?php foreach($Galeriler as $Galeri): ?>
<url>
    <loc><?php echo htmlspecialchars($Ayarlar->Genel->SiteAdresi.galeri_link($Galeri, true)); ?></loc>
    <lastmod><?php echo date("c", strtotime($Galeri->EklenmeTarihi)); ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
</url> 
<?php endforeach; ?>
<?php foreach($Etiketler as $Etiket): ?>
<url>
    <loc><?php echo $Ayarlar->Genel->SiteAdresi; ?>/tum?etiket:<?php echo $Etiket["etiket"]; ?></loc>
    <lastmod><?php echo date("c", strtotime($Galeriler[0]->EklenmeTarihi)); ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
</url> 
<?php endforeach; ?>
</urlset>
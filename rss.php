<?php
require_once "ic/sistem.php";

$galeriler = GaleriIslemleri::GalerileriAl(NULL, "eklenme_tarihi DESC");
$galeriler = array_slice($galeriler, 0, 10);
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/" version="2.0">
	<channel>
		<title><?php echo $Ayarlar->Genel->SiteAdi; ?></title>
		<link><?php echo $Ayarlar->Genel->SiteAdresi; ?></link>
		<language>tr</language>
		<copyright>Sinan Yıl</copyright>
		<pubDate><?php echo date("r"); ?></pubDate>
		<dc:date><?php echo date("c"); ?>Z</dc:date>
		<description><?php echo $Ayarlar->Genel->SiteAciklamasi; ?></description>
		<image>
		  <title><?php echo $Ayarlar->Genel->SiteAdi; ?></title>
		  <url><?php echo $Ayarlar->Genel->SiteAdresi; ?>/images/siyah_logo.png</url>
		  <link><?php echo $Ayarlar->Genel->SiteAdresi; ?></link>
		</image>
		<?php 
			foreach($galeriler as $galeri):
				$resimler = $galeri->Resimler();
		?>
		<item>
			<title><?php echo $galeri->Baslik; ?></title>
			<guid isPermalink="true"><?php echo htmlspecialchars($Ayarlar->Genel->SiteAdresi.galeri_link($galeri, true)); ?></guid>
			<description>&lt;img src="<?php echo $Ayarlar->Genel->SiteAdresi."/".$galeri->RastgeleResim()->KucukResim; ?>" border="0" align="left" width="180" height="135" /&gt;<?php echo htmlspecialchars($galeri->Yazi); ?></description>
			<link><?php echo htmlspecialchars($Ayarlar->Genel->SiteAdresi.galeri_link($galeri, true)); ?></link>
			<pubDate><?php echo date("r", strtotime($galeri->EklenmeTarihi)); ?></pubDate>
			<category><?php echo galeri_kategorisi_bul($galeri); ?></category>
			<author>Gönderen: <?php echo $galeri->Ekleyen()->Isim; ?></author>
			<dc:creator>Gönderen: <?php echo $galeri->Ekleyen()->Isim; ?></dc:creator>
			<dc:date><?php echo date("c", strtotime($galeri->EklenmeTarihi)); ?>Z</dc:date>
		</item>
		<?php endforeach; ?>
	</channel>
</rss>
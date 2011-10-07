<?php
require_once "ic/sistem.php";

$cozunurluk = get("coz");

if ($cozunurluk === false)
	$cozunurluk = new Cozunurluk("1600x1200");
else
	$cozunurluk = new Cozunurluk($cozunurluk);

$galeriler = GaleriIslemleri::GalerileriAl(NULL, "eklenme_tarihi DESC");
$galeriler = array_slice($galeriler, 0, 30);
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?>
<rss version="2.0">
	<channel>
		<title><?php echo $Ayarlar->Genel->SiteAdi; ?></title>
		<link><?php echo $Ayarlar->Genel->SiteAdresi; ?></link>
		<ttl>24</ttl>
		<description>Otomatik duvarkağıdı beslemesi (<?php echo $cozunurluk; ?> için)</description>
		<?php 
			foreach($galeriler as $galeri):
				$rastgele_resim = $galeri->RastgeleResim();
				$resim_link = htmlspecialchars($Ayarlar->Genel->SiteAdresi.indirme_link($rastgele_resim, $galeri->Baslik, $cozunurluk, true));
		?>
		<item>
			<title><?php echo $galeri->Baslik; ?></title>
			<guid><?php echo $rastgele_resim->Dosya; ?></guid>
			<description><?php echo $galeri->Baslik; ?> (<?php echo $cozunurluk; ?>)</description>
			<link ref="<?php echo $resim_link; ?>" />
			<enclosure url="<?php echo $resim_link; ?>" type="image/jpg" />
			<pubDate><?php echo date("r", strtotime($galeri->EklenmeTarihi)); ?></pubDate>
		</item>
		<?php endforeach; ?>
	</channel>
</rss>
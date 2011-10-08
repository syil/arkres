<?php
set_title("{$Ayarlar->Genel->SiteAdi} - ". _r("Tüm resimler"));

$galeriler = array();
$feature_info = "";
$sayfa_n = 1;
$toplam_sayfa = 0;
$galeri_sayisi = 0;
$toplam_galeri_sayisi = 0;

// Sayfa bilgisi alınıyor
if (isset($_GET["sayfa"]))
	$sayfa_n = $_GET["sayfa"];

$adet = $Ayarlar->Diger->SayfaBasinaGaleri;
$baslangic = ($sayfa_n - 1) * $adet;
/* 
	Durum değelendirilmesi 
	
	"etiket&kategori", "benim&kullanici", "ara" veya boş olabilir
 
*/
if (isset($_GET["etiket"]) || isset($_GET["kategori"])) {
	$etiket = get("etiket") or $etiket = get("kategori");
	
	$tum_galeriler = GaleriIslemleri::GalerileriAl(array("etiket" => $etiket), "eklenme_tarihi DESC");
	$toplam_galeri_sayisi = count($tum_galeriler);
	
	add_head("<meta name=\"description\" content=\"". etiket_desc($etiket) ." {$toplam_galeri_sayisi} adet duvarkağıdı galerisi görüntüleniyor\" />");
	add_head("<meta name=\"keywords\" content=\"{$Ayarlar->Genel->AnahtarKelimeler}\" />");
}
else if (isset($_GET["ara"])) { 	// Arama sorgusu
	$aranacak = explode(" ", get("ara"));
	$tum_galeriler = GaleriIslemleri::Ara($aranacak);
	$toplam_galeri_sayisi = count($tum_galeriler);
	if ($toplam_galeri_sayisi == 0) {
		$feature_info = sprintf(_r("'%s' ile ilgili resim bulunamadı"), get("ara"));
	}
}
else if (isset($_GET["kullanici"]) || isset($_GET["benim"])) {
	
	if (isset($_GET["benim"]) && !isset($_GET["kullanici"])) {	// Eğer "benim" tanımlı ise o an giriş yapmış kullanıcı
		$kullanici_id = $Kullanici->ID;
	}
	else {							// "kullanici" tanımlı ise belirtilen kullanıcı
		$kul = KullaniciIslemleri::IsimdenKullanici(get("kullanici"));
		$kullanici_id = $kul->ID;
	}
	
	$tum_galeriler = GaleriIslemleri::GalerileriAl(array("kullanici" => $kullanici_id), "eklenme_tarihi DESC");
	$toplam_galeri_sayisi = count($tum_galeriler);
}
else {
	$tum_galeriler = GaleriIslemleri::GalerileriAl(NULL, "eklenme_tarihi DESC");
	$toplam_galeri_sayisi = count($tum_galeriler);
}

$galeriler = array_slice($tum_galeriler, $baslangic, $adet);
$galeri_sayisi = count($galeriler);
$toplam_sayfa = ceil($toplam_galeri_sayisi / $Ayarlar->Diger->SayfaBasinaGaleri);
?>
<div class="wrapper" id='wrapper_main'>
<div class="center">
	<div id="feature_info">
		<h2><?php echo $feature_info; ?></h2>
	</div>	
	
	<div id="main">
	
		<div class="content the_gallery">
			<?php 
				for ($i = 0; $i < $galeri_sayisi; $i++){
					$bas = $i % 3 == 0;
					$son = $i % 3 == 2;
					if ($bas)
						echo "<div class=\"entry\">";
					
					galeri_ogesi($galeriler[$i], $son);
					
					if ($son || $i == ($galeri_sayisi - 1))
						echo "</div><!--end entry -->";
				}
				sayfalama($sayfa_n, $toplam_sayfa, $_SERVER["QUERY_STRING"]);
			?>

		<!--end content -->
		</div>
	
	
		<div class="sidebar">
			<div class="box widget">
				<p><?php printf(_r("%d galeri listeleniyor"), $toplam_galeri_sayisi); ?></p>
			</div>
			<div class="box">
				<?php
					$bilesenler = array(
						"bilesen_gorunum_secenekleri", 
						"bilesen_kategoriler", 
						"bilesen_cozunurluk_secici");
						
					foreach ($bilesenler as $fonk)
						call_user_func($fonk);
				?>
			</div>
			
			<div class="box_small box widget community_news">	
				<?php bilesen_son_yorumlar(); ?>
			</div>
			
		<!-- end sidebar-->	
		</div>
	
	<!--end main-->
	</div>

<!-- end center-->
</div>
<!--end wrapper-->
</div>
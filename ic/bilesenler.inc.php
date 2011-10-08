<?php
function bilesen_son_yorumlar()
{
	global $Ayarlar;
	$yorumlar = array_slice(YorumIslemleri::YorumlariAl(NULL, "yazim_tarihi DESC"), 0, 3);
?>
	<h3 class="widgettitle"><?php _e("Son yorumlar") ?></h3>
<?php
	foreach ($yorumlar as $yorum) {
		$yazan = $yorum->Yazan();
		$yazan_adi = $yazan->Isim;
		$yazan_link = kullanici_link($yazan_adi, true);
		$galeri = $yorum->Galeri();
		$galeri_baslik = $yorum->Galeri()->Baslik;
		$yazan_resim = get_gravatar($yazan->Eposta, 40, "identicon");
		
		if ($yazan instanceof Kullanici) {
			$yazan_link = "<a class=\"url\" href=\"{$yazan_link}\"><img title=\"{$yazan_adi}\" alt=\"{$yazan_adi}\" src=\"{$yazan_resim}\" class=\"rounded alignleft\" /></a>";
		}
		else {
			$yazan_link = "<a class=\"url\" rel=\"external, nofollow\" href=\"{$yazan->Adres}\"><img title=\"{$yazan_adi}\" alt=\"{$yazan_adi}\" src=\"{$yazan_resim}\" class=\"rounded alignleft\" /></a>";
		}

		$yorum_tarih = tarih_bicimlendir(_r(YORUM_TARIH), $yorum->YazimTarihi);
		$gecen_zaman = zaman_araligi(tarih_bicimlendir(MYSQL_TARIHSAAT, $yorum->YazimTarihi));
		$yorum_metin = $yorum->Yazi;	
?>
	<div class="entry box_entry">
		<h4><a href="<?php galeri_link($galeri); ?>"><?php echo $galeri_baslik; ?></a> <small>(<?php echo $gecen_zaman; ?>)</small></h4>
		<?php echo $yazan_link; ?>
		<p><?php echo $yorum_metin; ?></p>
		<p class="clearboth"></p>
	</div>
<?php
	}
}

function bilesen_gorunum_secenekleri()
{
	global $Ayarlar;
	$cerez = isset($_COOKIE[$Ayarlar->Diger->GorunumCerezi]) ? $_COOKIE[$Ayarlar->Diger->GorunumCerezi] : "item_small_gallery";
?>
	<div class="display_buttons">
		<a href='#' id='item_small' class='display<?php if ($cerez == 'item_small_gallery') echo " display_active"; ?>'><span><?php _e("Toplu"); ?></span></a>
		<a href='#' id='item_medium' class='display<?php if ($cerez == 'item_medium_gallery') echo " display_active"; ?>'><span><?php _e("Detaylı"); ?></span></a>
		<a href='#' id='item_large' class='display<?php if ($cerez == 'item_large_gallery') echo " display_active"; ?>'><span><?php _e("Büyük"); ?></span></a>
	</div>
<?php
}

function bilesen_kategoriler()
{
?>
	<h3><?php _e("Kategoriler") ?></h3>
	<ul>
		<?php
			foreach (kategorileri_al() as $kat) {
		?>
			<li><a href="<?php echo kategori_link($kat["kisa_ad"]); ?>"><?php echo $kat["tam_ad"]; ?></a></li>
		<?php
			}
		?>
	</ul>
<?php
}

function bilesen_cozunurluk_secici()
{
	global $Ayarlar;
?>
	<h3><?php _e("İndirme Çözünürlüğü"); ?></h3>
	<p id="selected_res"><span><?php echo $_COOKIE[$Ayarlar->Kullanici->CozunurlukCerezi]; ?></span><a href="#"><?php _e("Değiştir"); ?></a></p>
	<div class="res_selection">
		<?php 
			$cozunurlukler = Cozunurluk::BilinenCozunurlukler();
			
			foreach ($cozunurlukler as $grup => $value) {
				echo "<h4>{$grup}</h4>";
				echo "<ul>";
				foreach ($value as $cozunurluk) {
					echo "<li><a href='#' class='change_res'><span class='res'>{$cozunurluk}</span>";
					echo "<span class='res_tag'>{$cozunurluk->Etiket}</span>";
					echo "</a></li>";
				}
				echo "</ul>";
			}
		?>
	</div>
<?php
}
?>
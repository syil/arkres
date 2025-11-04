<?php
function head_changer($html)
{
	return str_replace("{head}", get_head(), $html);
}

function tarih_bicimlendir($bicim = KISA_TARIHSAAT, $tarih)
{
	if ($tarih == BOS_TARIH)
		return _r("Yok");
	else {
		if ($bicim === "")
			return iconv("windows-1254", "utf-8", strftime(KISA_TARIHSAAT, strtotime($tarih)));
		else
			return iconv("windows-1254", "utf-8", strftime($bicim, strtotime($tarih)));
	}
}

function resim_indir($resim_id, $coz, $indir = true)
{
	if (!headers_sent()) {
		global $Ayarlar;
		$resim = ResimIslemleri::Al($resim_id);
		
		$dosya_ismi = str2url($resim->Galeri()->Baslik). "_" .$coz. ".jpg";
		$onbellek = $Ayarlar->Resim->OnbellekKlasoru . $dosya_ismi;
		
		header("Content-Type: image/jpeg");
		if ($indir) {
			header("Content-Disposition: attachment; filename={$dosya_ismi}");
		}
		
		if (!file_exists($onbellek) || filesize($onbellek) == 0) {
			boyutlandir($resim->Dosya, $onbellek, $coz);
		}
		
		$res = imagecreatefromjpeg($onbellek);
		imagejpeg($res, NULL, $Ayarlar->Resim->ResimKalitesi);
		imagedestroy($res);
	}
}

function galeri_link(&$galeri, $return = false)
{
	$link = "/galeri?i:{$galeri->ID}&amp;". str2url($galeri->Baslik);
	if ($return)
		return $link;
	else
		echo $link;
}

function indirme_link(&$resim, $baslik, $coz, $return = false)
{
	$resim_adi = str2url($baslik);
	$link = "/indir/{$resim_adi}-{$resim->ID}_{$coz}.jpg";
	if ($return)
		return $link;
	else
		echo $link;
}

function kullanici_link($kullanici_adi, $return = false)
{
	$link = "/tum?kullanici:{$kullanici_adi}";
	if ($return)
		return $link;
	else
		echo $link;
}

function kategori_link($kategori, $return = false)
{
	$link = "/tum?kategori:{$kategori}";
	if ($return)
		return $link;
	else
		echo $link;
}

function puanla($eylem, $galeri_id)
{
	global $Ayarlar;
	global $vt;
	
	switch($eylem) {
		case "indirme"		: $puan = $Ayarlar->Diger->Puan->Indirme; break;
		case "goruntuleme"	: $puan = $Ayarlar->Diger->Puan->Goruntuleme; break;
		case "yorum"		: $puan = $Ayarlar->Diger->Puan->Yorum; break;
		case "bulma"		: $puan = $Ayarlar->Diger->Puan->Bulma; break;
		case "yildiz"		: $puan = $Ayarlar->Diger->Puan->Yildiz; break;
		default	: return; break;
	}
	
	if (giris())
		$puan *= $Ayarlar->Diger->Puan->KayitliCarp;
	
	$vt->SorguAta("INSERT INTO puanlar (eylem, galeri_id, puan, ip) VALUES (\"{$eylem}\", {$galeri_id}, {$puan}, '{$_SERVER["REMOTE_ADDR"]}')");
	$vt->Calistir();
}

function indirme_sayisi($resim_id)
{
	global $vt;
	
	$bilgiler = array();
	$vt->SorguAta("SELECT * FROM olaylar WHERE tur = 'indirme'");
	if ($vt->SatirlariAl($Veri)) {
		foreach ($Veri as $v) {
			$bilgi = unserialize($v["ek_bilgi"]);
			if ($bilgi["Resim ID"] == $resim_id) {
				$bilgiler[] = $bilgi;
			}
		}
	}
	
	return count($bilgiler);
}

function gunlukle($tur, $aciklama = "", $ek_bilgi = array())
{
	global $vt;
	
	if (count($ek_bilgi))
		$ek = serialize($ek_bilgi);
	else
		$ek = "NULL";
	
	$sql = "INSERT INTO olaylar (tur, aciklama, ek_bilgi) VALUES ('{$tur}', '{$aciklama}', '{$ek}')";
	$vt->SorguAta($sql);
	if (!$vt->Calistir())
		throw new Exception(sprintf("Sorgu hatası: %s", $sql));
}

function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
	$url = 'http://www.gravatar.com/avatar/';
	$url .= md5( strtolower( trim( $email ) ) );
	$url .= "?s=$s&amp;d=$d&amp;r=$r";
	if ( $img ) {
		$url = '<img src="' . $url . '"';
		foreach ( $atts as $key => $val )
			$url .= ' ' . $key . '="' . $val . '"';
		$url .= ' />';
	}
	return $url;
}

function dirsize($directory) {
    $size = 0;
    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
        $size+=$file->getSize();
    }
    return $size;
} 

function zaman_araligi($start, $end = null) {
    if (!($start instanceof DateTime)) {
        $start = new DateTime($start, new DateTimeZone("EET"));
    }
	
    if ($end === null) {
        $end = new DateTime(NULL, new DateTimeZone("EET"));
    }
   
    if (!($end instanceof DateTime)) {
        $end = new DateTime($end, new DateTimeZone("EET"));
    }
   
    $interval = $end->diff($start);
   
    $format = array();
	if ($interval->y !== 0){
		$format[] = "%y yıl";
	}
	if ($interval->m !== 0) {
		$format[] = "%m ay";
	}
	if ($interval->d !== 0) {
		if (!count($format) && $interval->d < 7) {
			if (($end->format("d") - $start->format("d")) == 1)
				return "Dün ". $start->format("H:i");
			else if (($end->format("W") - $start->format("W")) == 1)
				return "Geçen ". iconv("windows-1254", "utf-8", strftime("%A", $start->format("U")));
			else
				$format[] = "%d gün";
		} else {
			$format[] = "%d gün";
		}
	}
	if ($interval->h !== 0) {
		$format[] = "%h saat";
	}
	if ($interval->i !== 0) {
		$format[] = "%i dk";
	}
	if ($interval->s !== 0) {
		if (!count($format)) {
			return "saniyeler önce";
		} else {
			$format[] = "%s sn";
		}
	}
	
    // if(count($format) > 1) {
        // $format = array_shift($format).", ".array_shift($format);
    // } else {
        // $format = array_pop($format);
    // }
	$format = array_shift($format);
	
    return $interval->format($format) . " önce";
}

function get($key)
{
	if (isset($_GET[$key]))
		return $_GET[$key];
	else
		return false;
}

function post($key)
{
	if (isset($_POST[$key]))
		return $_POST[$key];
	else
		return false; 
}

function kucuk_resim_olustur($dosya, $kayit_yeri)
{
	global $Ayarlar;
	boyutlandir($dosya, $kayit_yeri, new Cozunurluk($Ayarlar->Resim->KucukResimBoyutu));
}

function onizleme_olustur($dosya, $kayit_yeri)
{
	global $Ayarlar;
	boyutlandir($dosya, $kayit_yeri, new Cozunurluk($Ayarlar->Resim->OnizlemeBoyutu));
}

function boyutlandir($dosya, $kayit_yeri, $yeni_coz)
{
	global $Ayarlar;
	
	list($gen, $yuk) = getimagesize($dosya);
	$eski_coz = new Cozunurluk($gen ."x". $yuk);
	touch($kayit_yeri);
	
	if ($yeni_coz->Oran()->Deger() == $eski_coz->Oran()->Deger()) {
		if ($Ayarlar->Resim->DisBoyutlandirma) {
			$komut = sprintf('"%s" "%s" %s "%s"', 
				$Ayarlar->Resim->DisBoyutAraci, 
				realpath($dosya), 
				"-scale \"{$yeni_coz}\"", 
				realpath($kayit_yeri));
			system($komut, $sonuc);
		}
	} 
	else if ($yeni_coz->Oran()->Deger() > $eski_coz->Oran()->Deger()) {
		
		$oranli = $eski_coz->Oranla($yeni_coz->Oran());
		$fark = ($eski_coz->Yukseklik - $oranli->Yukseklik) / 2;
		
		if ($fark == 0)
			$oranli = $yeni_coz;
		
		if ($Ayarlar->Resim->DisBoyutlandirma) {
			$komut = sprintf('"%s" "%s" %s %s "%s"', 
				$Ayarlar->Resim->DisBoyutAraci, 
				realpath($dosya), 
				"-crop \"{$oranli}+0+{$fark}\"", 
				"-scale \"{$yeni_coz}\"", 
				realpath($kayit_yeri));
			system($komut, $sonuc);
		}
	} 
	else {
		$oranli = $eski_coz->Oranla($yeni_coz->Oran());
		$fark = ($eski_coz->Genislik - $oranli->Genislik) / 2;
		
		if ($fark == 0)
			$oranli = $yeni_coz;
		
		if ($Ayarlar->Resim->DisBoyutlandirma) {
			$komut = sprintf('"%s" "%s" %s %s "%s"', 
				$Ayarlar->Resim->DisBoyutAraci, 
				realpath($dosya), 
				"-crop \"{$oranli}+{$fark}+0\"", 
				"-scale \"{$yeni_coz}\"", 
				realpath($kayit_yeri));
			system($komut, $sonuc);
		}
	}
	
	if ($sonuc != 0)
		throw new Exception(sprintf("Dış boyutlandırma aracı hatası: %s", $komut));
}

function hata_log($errno, $message, $file, $line)
{
	return false;
}

function istisna_log($exc)
{
	return false;
}

function kategorileri_al()
{
	global $vt;
	$vt->SorguAta("SELECT * FROM kategoriler ORDER BY tam_ad ASC");
	$vt->SatirlariAl($v);
	return $v;
}

function galeri_kategorisi_bul($galeri)
{
	// Get gallery tags
	$etiketler = $galeri->Etiketler();
	
	if (empty($etiketler)) {
		return "Genel";
	}
	
	// Get all categories
	$kategoriler = kategorileri_al();
	
	// Check if categories exist and if any tag matches a category short name
	if (!empty($kategoriler)) {
		foreach ($etiketler as $etiket) {
			foreach ($kategoriler as $kategori) {
				if (strcasecmp($etiket, $kategori["kisa_ad"]) == 0) {
					return $kategori["tam_ad"];
				}
			}
		}
	}
	
	// If no match found, return default category
	return "Genel";
}

function ulkeleri_al()
{
	global $vt;
	$ret = array();
	$vt->SorguAta("SELECT ulke FROM ulkeler ORDER BY id ASC");
	if ($vt->SatirlariAl($veri)) {
		foreach ($veri as $v)
			$ret[] = $v["ulke"];
		
	}
	
	return $ret;
}

function _e($k)
{
	echo _($k);
}

function _r($k)
{
	return $k;
}

function tr_karakter_ayikla($str)
{
	$tr  = array("ç", "Ç", "ğ", "Ğ", "ı", "İ", "ö", "Ö", "ş", "Ş", "ü", "Ü");
	$ntr = array("c", "C", "g", "G", "i", "I", "o", "O", "s", "S", "u", "U");
	
	$str = str_replace($tr, $ntr, $str);
	
	return $str;
}

function ozel_karakter_ayikla($str)
{
	$karakterler = array("`", "+", "=", "#", "%", '"', "<", ">", "|", "{", "}", "[", "]", "^", "?", ":", "@", "$", ";", "'");
	$str = str_replace($karakterler, "", $str);
	
	$str = str_replace("&", "n", $str);
	$str = str_replace(",", ".", $str);
	$str = str_replace(" ", "-", $str);
	
	return $str;
}

function str2url($str)
{
	$str = tr_karakter_ayikla($str);
	$str = str_replace("-", "_", $str);
	$str = ozel_karakter_ayikla($str);
	
	$str = str_replace("--", "-", $str);
	$str = trim($str, "-");
	
	return $str;
}

function giris()
{
	global $Kullanici;
	return isset($Kullanici);
}

function etiket_desc($etiket)
{
	return "{$etiket} etiketine sahip resimler, {$etiket} ile ilgili resimler, {$etiket} resimleri, {$etiket} galerisi, {$etiket} duvarkağıtları.";
}

function etiket_keywords($etiket)
{
	return "{$etiket}, {$etiket} resimleri, {$etiket} duvarkağıtları, {$etiket} resimleri galerisi, {$etiket} resimleri indir";
}
?>

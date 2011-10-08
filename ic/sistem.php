<?php
require "veritabani.inc.php";
require "kullanici.inc.php";
require "galeri.inc.php";
require "resim.inc.php";
require "yorum.inc.php";
require "cozunurluk.inc.php";
//require "gravatar.inc.php";
require "globaller.inc.php";

if (!defined("DEBUG"))
	error_reporting(0);

setlocale(LC_TIME, $Ayarlar->Genel->SiteDili);

// Bunun değişkenler tanımlandıktan sonra dahil edilmesi gerekiyor.
require "sistem-fonksiyonlari.inc.php"; 
require "arayuz-fonksiyonlari.inc.php";
require "bilesenler.inc.php";

set_exception_handler("istisna_log");
set_error_handler("hata_log");

if (!isset($_GET["a"])) // Query String düzenlemesi. Eğer "a" tanımlı ise düzenlemeyi geç.
{
	$keys = array_keys($_GET);
	$i = 0;
	foreach($keys as $key)
	{
		unset($_GET[$key]);
		$a = explode(":", $key);
		if (count($a) >= 2) {
			$_GET[$a[0]] = $a[1];
			$_GET[$i] = $a[1];
		}
		else {
			$_GET[$a[0]] = "";
		}
		$i++;
	}
}

// Giriş - Çıkış işlemleri
if ((isset($_POST["u"]) || isset($_POST["p"])) && !giris()) {
	$kul = KullaniciIslemleri::IsimdenKullanici($_POST["u"]);
	try {
		$kul->GirisYap($_POST["p"], true);
		$Kullanici = $kul;
	}
	catch(Exception $exc) {
		echo $exc->getMessage();
	}
}

if (isset($_POST["cikis"]) && giris()) {
	$Kullanici->CikisYap();
	$Kullanici = null;
}
// -------

add_css("css/style.css");
add_css("css/style1.css");
//add_css("js/prettyPhoto/css/prettyPhoto.css");
add_css("js/fancybox/jquery.fancybox-1.3.1.css");
add_head('<meta http-equiv="X-UA-Compatible" content="IE=8" />');
add_head('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');
add_head('<meta name="google-site-verification" content="evLoiNpH7UenQuLfD_1bUQ9JPyDJbk3moFm7j7syup4" />');
add_head('<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />');
add_head('<link rel="icon" type="image/ico" href="./favicon.ico" />');
add_head("<script type=\"text/javascript\" src=\"https://apis.google.com/js/plusone.js\">{lang: 'tr'}</script>");
add_js("js/degisenler.js.php");
add_js("js/jquery.js");
//add_js("js/prettyPhoto/js/jquery.prettyPhoto.js");
add_js("js/fancybox/jquery.fancybox-1.3.1.pack.js");
add_js("js/custom.js");
?>

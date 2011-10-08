<?php
class Galeri
{
	private $_islemler;
	private $_resimler;
	private $_yorumlar;
	private $_ekleyen;
	private $_etiketler;
	
	public $ID;
	public $Yazi;
	public $Baslik;
	public $EklenmeTarihi;
	public $EkleyenID;
	public $Onay;
	public $Puan;
	
	public function Resimler()
	{
		if ($this->_resimler == NULL) {
			$this->_resimler = ResimIslemleri::GaleriResimleri($this->ID);
		}
		return $this->_resimler;
	}
	
	public function RastgeleResim()
	{	
		$this->Resimler();
		return $this->_resimler[array_rand($this->_resimler)];
	}
	
	public function Yorumlar()
	{
		if ($this->_yorumlar == NULL) {
			$this->_yorumlar = YorumIslemleri::GaleriYorumlari($this->ID);
		}
		return $this->_yorumlar;
	}
	
	public function Etiketler()
	{
		if ($this->_etiketler == NULL) {
			$this->_etiketler = $this->Islemler()->EtiketleriAl();
		}
		return $this->_etiketler;
	}
	
	public function Ekleyen()
	{
		if ($this->_ekleyen == NULL) {
			$this->_ekleyen = KullaniciIslemleri::Al($this->EkleyenID);
		}
		return $this->_ekleyen;
	}
	
	public function Islemler(&$islem = NULL)
	{
		if ($islem === NULL) {
			if ($this->_islemler == NULL) {
				$this->_islemler = new GaleriIslemleri($this->ID);
			}
			return $this->_islemler;
		}
		else {
			$this->_islemler = &$islem;
		}
	}
	
	public function __toString()
	{
		return $this->Baslik;
	}
}

class GaleriIslemleri
{
	public static $Tablo = "galeriler";
	public static $PuanliTablo = "puanli_galeri";
	public static $PopulerTablo = "populer_galeri";
	public static $EtiketTablosu = "etiketler";
	public static $YildizTablosu = "yildizlar";
	public static $SecilenlerTablosu = "seckin_galeri";
	
	private static $VT = NULL;

	private $_ID;
	private $_galeri;
	
	public function GaleriIslemleri($ID)
	{
		$this->_ID = intval($ID);
	}
	
	public function Galeri()
	{
		if ($this->_galeri == NULL) {
			$this->_galeri = self::Al($this->_ID);
			$this->_galeri->Islemler($this);
		}
		return $this->_galeri;
	}
	
	public function Sil()
	{
		if(self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
	
		if(self::YetkiDenetle("Editör")) // Erişim Kontrolü
		{
			$sql = "DELETE FROM ". self::$Tablo ." WHERE id = {$this->_ID}";
			self::$VT->SorguAta($sql);
			
			if(!self::$VT->Calistir())
				throw new Exception(sprintf("Sorgu hatası: %s", $sql));
		}
		else
			throw new Exception("Galeri üzerinde değişiklik yapma izniniz yok");
	}
	
	/* Diğer Fonksiyonlar */
	
	public function YildizVer($deger)
	{
		if (giris()) {
			global $Kullanici;
			$kullanici = $Kullanici->ID;
		}
		else
			$kullanici = "NULL";
		
		if (self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
		
		$sql = "INSERT INTO ". self::$YildizTablosu ." (yildiz, galeri_id, kullanici_id) VALUES ({$deger}, {$this->_ID}, {$kullanici})";
		self::$VT->SorguAta($sql);
		if(!self::$VT->Calistir())
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	public function Etiketle($etiketler)
	{
		if(!self::YetkiDenetle("Kullanıcı"))
			throw new Exception(sprintf("%s üzerinde değişiklik yapma izniniz yok", "Galeri"));
		
		if (self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
			
		if (is_array($etiketler)) {
			$sql = "DELETE FROM ". self::$EtiketTablosu ." WHERE galeri_id = {$this->_ID}";
			self::$VT->SorguAta($sql);
				
			if (self::$VT->Calistir()) {
				foreach ($etiketler as $etiket) {
					$sql = "INSERT INTO ". self::$EtiketTablosu ." VALUES ('{$etiket}', {$this->_ID})";
					self::$VT->SorguAta($sql);
					self::$VT->Calistir();
				}
			}
			else
				throw new Exception(sprintf("Sorgu hatası: %s", $sql));
		}
		else
			throw new Exception(sprintf("%s bir %s olmalıdır", "\$etiketler", "dizi"));
	}
	
	public function EtiketleriAl()
	{
		if(self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
		
		$sql = "SELECT etiket FROM ". self::$EtiketTablosu ." WHERE galeri_id = {$this->_ID}";
		self::$VT->SorguAta($sql);
		
		if (self::$VT->SatirlariAl($Veri)) {
			$etiketler = array();
			foreach ($Veri as $v) {
				$etiketler[] = $v["etiket"];
			}
			
			return $etiketler;
		}
	}
	
	public function YorumYaz($yorum)
	{
		$yorum->GaleriID = $this->_ID;
		
		return YorumIslemleri::Ekle($yorum);
	}
	
	public function ResimEkle($resim)
	{
		if(!self::YetkiDenetle("Editör", $this->Galeri()->EkleyenID))
			throw new Exception(sprintf("%s üzerinde değişiklik yapma izniniz yok", "Galeri"));
		
		$resim->GaleriID = $this->_ID;
			
		return ResimIslemleri::Ekle($resim);
	}
	
	public function BaslikDegistir($yeniBaslik)
	{
		if(!self::YetkiDenetle("Editör", $this->Galeri()->EkleyenID))
			throw new Exception(sprintf("%s üzerinde değişiklik yapma izniniz yok", "Galeri"));
			
		if(self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
			
		$sql = "UPDATE ". self::$Tablo ." SET baslik = '{$yeniBaslik}' WHERE id = {$this->_ID}";
		self::$VT->SorguAta($sql);
		
		if(!self::$VT->Calistir())
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	public function YaziDegistir($yeniYazi)
	{
		if(!self::YetkiDenetle("Editör", $this->Galeri()->EkleyenID))
			throw new Exception(sprintf("%s üzerinde değişiklik yapma izniniz yok", "Galeri"));
			
		if(self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
			
		$sql = "UPDATE ". self::$Tablo ." SET yazi = '{$yeniYazi}' WHERE id = {$this->_ID}";
		self::$VT->SorguAta($sql);
		
		if(!self::$VT->Calistir())
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	/* ------ */
	
	private static function YetkiDenetle($yetki, $id = NULL)
	{
		global $Kullanici;
		if (isset($Kullanici) || $Kullanici != NULL) {
			if ($id != NULL)
				return $Kullanici->YetkiliMi($yetki) || $Kullanici->KendiMi($id);
			else
				return $Kullanici->YetkiliMi($yetki);
		}
		else
			return false;
	}
	
	private static function GaleriOgesi(&$result)
	{
		$galeri = new Galeri;
		$galeri->ID = $result["id"];
		$galeri->Yazi = $result["yazi"];
		$galeri->Baslik = $result["baslik"];
		$galeri->EklenmeTarihi = $result["eklenme_tarihi"];
		$galeri->EkleyenID = $result["kullanici_id"];
		$galeri->Onay = $result["onay"] > 0;
		$galeri->Puan = $result["puan"];
		
		return $galeri;
	}
	
	private static function Kriterize($kriterler = array())
	{
		global $Kullanici;
		
		$kosullar = array();
		
		if (!isset($kriterler["onaysiz"]) || !self::YetkiDenetle("Editör")) {
			if (self::YetkiDenetle("Kullanıcı"))
				$kosullar[] = "onay = 1 OR kullanici_id = {$Kullanici->ID}";
			else
				$kosullar[] = "onay = 1";
		}
		
		if (isset($kriterler["etiket"])) {
			$kosullar[] = "id IN (SELECT galeri_id FROM ". self::$EtiketTablosu . " WHERE etiket = '{$kriterler["etiket"]}')";
		}
		
		if (isset($kriterler["kullanici"])) {
			$kosullar[] = "kullanici_id = {$kriterler["kullanici"]}";
		}
		
		if (count($kosullar))
			return "(". implode(") AND (", $kosullar) .")";
		else
			return "1";
	}
	
	public static function VtAyarla(&$vt)
	{
		self::$VT = $vt;
	}
	
	public static function Al($ID)
	{
		if(self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
		
		$sql = "SELECT * FROM ". self::$PuanliTablo ." WHERE id = {$ID}";
		self::$VT->SorguAta($sql);
		if (self::$VT->SatirAl($Veri))
		{
			return self::GaleriOgesi($Veri);
		}
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	public static function Ekle($nesne)
	{
		if (self::$VT == NULL)
			throw new Exception("Veritabanı hazır değil");
		
		if (!self::YetkiDenetle("Kullanıcı"))
			throw new Exception("Giriş yapmış kullanıcı yok");
		
		$sql = "INSERT INTO ". self::$Tablo ." (baslik, yazi, kullanici_id, onay) VALUES ('{$nesne->Baslik}', '{$nesne->Yazi}', {$nesne->EkleyenID}, {$nesne->Onay})";

		self::$VT->SorguAta($sql);
		if (self::$VT->Calistir())
			return self::Al(self::$VT->SonEklenenID());
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	public static function GaleriSayisi($kriterler = array(), $tablo = "")
	{
		if (empty($tablo))
			$tablo = self::$PuanliTablo;
		
		$kosul = "WHERE ". self::Kriterize($kriterler);
		
		$sql = "SELECT COUNT(*) FROM {$tablo} {$kosul}";
		self::$VT->SorguAta($sql);
		
		if (self::$VT->DegerAl($sayi))
			return $sayi;
	}
	
	public static function SeckinGalerileriAl()
	{
		return self::GalerileriAl(NULL, NULL, self::$SecilenlerTablosu);
	}
	
	public static function PopulerGalerileriAl()
	{
		return self::GalerileriAl(NULL, NULL, self::$PopulerTablo);
	}
	
	public static function BenzerGaleriler($etiketler, $kriterler = array())
	{
		$et = self::$EtiketTablosu;
		$gt = self::$PuanliTablo;
		$sayi = 3;
		
		$sql = "SELECT * FROM {$et} INNER JOIN {$gt} ON {$gt}.id = {$et}.galeri_id
					WHERE etiket IN ('". implode("', '", $etiketler) ."') AND ". self::Kriterize($kriterler) ." GROUP BY {$et}.galeri_id
					ORDER BY COUNT({$et}.galeri_id) DESC LIMIT {$sayi}";
		self::$VT->SorguAta($sql);
		
		if (self::$VT->SatirlariAl($Veri)) {
			$galeriler = array();
			foreach($Veri as $v) {
				$galeriler[] = self::GaleriOgesi($v);
			}
			
			return $galeriler;
		}
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	public static function Ara($aranacak, $kriterler = array())
	{
		$kriter = self::Kriterize($kriterler);
		$sorgular = array();
		
		foreach ($aranacak as $ara) {
			$sorgular[] = "SELECT * FROM ". self::$PuanliTablo ." WHERE ((baslik LIKE '{$ara}%' OR baslik LIKE '% {$ara}%' OR yazi LIKE '{$ara}%' OR yazi LIKE '% {$ara}%' OR id IN (SELECT galeri_id FROM ". self::$EtiketTablosu ." WHERE etiket LIKE '{$ara}%' OR etiket LIKE '% {$ara}%')) AND {$kriter})";
		}
		
		$sql = implode(" UNION ", $sorgular);
		
		self::$VT->SorguAta($sql);
		
		if (self::$VT->SatirlariAl($Veri)) {
			$galeriler = array();
			foreach($Veri as $v) {
				$galeriler[] = self::GaleriOgesi($v);
			}
			
			return $galeriler;
		}
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	public static function GalerileriAl($kriterler = array(), $Siralama = "", $tablo = "")
	{
		if(self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
			
		if (empty($tablo))
			$tablo = self::$PuanliTablo;
		
		if (!empty($Siralama)) {
			$Siralama = "ORDER BY {$Siralama}";
		}
		
		$kosul = "WHERE ". self::Kriterize($kriterler);
		
		$sql = "SELECT * FROM {$tablo} {$kosul} {$Siralama}";
		self::$VT->SorguAta($sql);
		
		if(self::$VT->SatirlariAl($Veri))
		{
			$galeriler = array();
			foreach($Veri as $v) {
				$galeriler[] = self::GaleriOgesi($v);
			}
			
			return $galeriler;
		}
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
}
?>
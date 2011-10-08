<?php
class Kullanici
{
	private $_islemler;
	
	public $ID;
	public $Isim;
	public $Sifre;
	public $Seviye;
	public $Eposta;
	public $KayitTarihi;
	public $SonGirisZamani;
	public $Engel;
	public $DogumTarihi;
	public $Sehir;
	public $Ulke;
	
	public function __toString() 
	{ 
		return $this->Isim; 
	}
	
	public function Islemler(&$islem = NULL)
	{
		if ($islem === NULL) {
			if ($this->_islemler == NULL) {
				$this->_islemler = new KullaniciIslemleri($this->ID);
			}
			return $this->_islemler;
		}
		else {
			$this->_islemler = &$islem;
		}
	}
	
	public function SifreKontrol($girilen)
	{		
		$girilen = KullaniciIslemleri::KullaniciSifreHash($girilen);
		if($girilen == $this->Sifre)
			return true;
		else
			return false;
	}
	
	public function CikisYap()
	{
		global $Ayarlar;
		
		if(!headers_sent()) {
			setcookie($Ayarlar->Kullanici->CerezAdi, "", time() - 1, "/");
		}
		else
			throw new Exception(_r("HTTP başlıkları gönderilmiş"));
	}
	
	public function KendiMi($k_id = -1)
	{
		global $Kullanici;
		
		if ($Kullanici === NULL)
			return false;
		
		if ($k_id != -1) {
			if ($Kullanici->ID == $k_id)
				return true;
			else
				return false;
		}
		else {
			if ($Kullanici->ID == $this->ID)
				return true;
			else
				return false;
		}
	}
	
	public function YetkiliMi($enAzYetki)
	{
		global $KullaniciSeviyeleri;
		
		if (array_search($this->Seviye, $KullaniciSeviyeleri) >= array_search($enAzYetki, $KullaniciSeviyeleri))
			return true;
		else
			return false;
	}
	
	public function GirisYap($GirilenSifre, $Hatirla = false)
	{
		if ($this->Engel)
			throw new Exception(_r("Kullanıcı engelli"));
		
		if ($this->SifreKontrol($GirilenSifre)) {
			$this->Islemler()->Giris($Hatirla);
		}
		else
			throw new Exception(_r("Girilen şifre yanlış"));
	}
}

class KullaniciIslemleri
{
	public static $Tablo = "kullanicilar";
	private static $VT = NULL; // Veritabanı Nesnesi
	
	private $_ID;
	private $_kullanici;
	
	public function KullaniciIslemleri($ID)
	{
		$this->_ID = intval($ID);
	}
	
	public static function Al($ID)
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		self::$VT->SorguAta("SELECT * FROM ". self::$Tablo ." WHERE id = {$ID}");
		if (self::$VT->SatirAl($Veri))
		{
			return self::KullaniciOgesi($Veri);
		}
		else
			throw new Exception(_r("Kullanıcı bulunamadı"));
	}
	
	public function Sil()
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		if(self::YetkiDenetle("Üst Yönetici")) // Yetki Kontrolü
		{
			$sql = "DELETE FROM ". self::$Tablo ." WHERE id = {$this->_ID}";
			self::$VT->SorguAta($sql);
			
			return self::$VT->Calistir();
		}
	}
	
	public function Kullanici()
	{
		if ($this->_kullanici == NULL) {
			$this->_kullanici = self::Al($this->_ID);
			$this->_kullanici->Islemler($this);
		}
		return $this->_kullanici;
	}
	
	/* Diğer Fonksiyonlar */

	public function Giris($Hatirla)
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		$sql = "UPDATE ". self::$Tablo ." SET son_giris = CURRENT_TIMESTAMP WHERE id = {$this->_ID}";
		self::$VT->SorguAta($sql);
		if(self::$VT->Calistir()) {
			self::KullaniciCookieOlustur($this->_ID, $Hatirla);
		}
		else
			throw new Exception(sprintf(_r("Sorgu hatası: %s"), $sql));
	}
	
	public function UyelikSeviyesiDegistir($YeniSeviye)
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		if(self::YetkiDenetle("Üst Yönetici")) // Yetki Kontrolü
		{
			$sql = "UPDATE ". self::$Tablo ." SET seviye = '{$YeniSeviye}' WHERE id = {$this->_ID}";
			self::$VT->SorguAta($sql);
			
			if (!self::$VT->Calistir())
				throw new Exception(sprintf(_r("Sorgu hatası: %s"), $sql));
		}
	}
	
	public function EPostaDegistir($YeniEPosta)
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		if(self::YetkiDenetle("Yönetici", $this->_ID)) // Yetki Kontrolü
		{
			$sql = "UPDATE ". self::$Tablo ." SET eposta = '{$YeniEPosta}' WHERE id = {$this->_ID}";
			self::$VT->SorguAta($sql);
			
			if (!self::$VT->Calistir())
				throw new Exception(sprintf(_r("Sorgu hatası: %s"), $sql));
		}
	}
	
	public function SehirDegistir($YeniSehir)
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		if(self::YetkiDenetle("Yönetici", $this->_ID)) // Yetki Kontrolü
		{
			$sql = "UPDATE ". self::$Tablo ." SET sehir = '{$YeniSehir}' WHERE id = {$this->_ID}";
			self::$VT->SorguAta($sql);
			
			if (!self::$VT->Calistir())
				throw new Exception(sprintf(_r("Sorgu hatası: %s"), $sql));
		}
	}
	
	public function UlkeDegistir($YeniUlke)
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		if(self::YetkiDenetle("Yönetici", $this->_ID)) // Yetki Kontrolü
		{
			$sql = "UPDATE ". self::$Tablo ." SET ulke = '{$YeniUlke}' WHERE id = {$this->_ID}";
			self::$VT->SorguAta($sql);
			
			if (!self::$VT->Calistir())
				throw new Exception(sprintf(_r("Sorgu hatası: %s"), $sql));
		}
	}
	
	public function DogumTarihiDegistir($YeniTarih)
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		if(self::YetkiDenetle("Yönetici", $this->_ID)) // Yetki Kontrolü
		{
			$sql = "UPDATE ". self::$Tablo ." SET dogum_tarihi = '{$YeniTarih}' WHERE id = {$this->_ID}";
			self::$VT->SorguAta($sql);
			
			if (!self::$VT->Calistir())
				throw new Exception(sprintf(_r("Sorgu hatası: %s"), $sql));
		}
	}
	
	public function SifreDegistir($YeniSifre)
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		if(self::YetkiDenetle("Yönetici", $this->_ID)) // Yetki Kontrolü
		{
			$sql = "UPDATE ". self::$Tablo ." SET sifre = '". self::KullaniciSifreHash($YeniSifre). "' WHERE id = {$this->_ID}";
			self::$VT->SorguAta($sql);
			
			if (!self::$VT->Calistir())
				throw new Exception(sprintf(_r("Sorgu hatası: %s"), $sql));
		}
	}
	
	public function Engelle()
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		if(self::YetkiDenetle("Yönetici")) // Yetki Kontrolü
		{
			$sql = "UPDATE ". self::$Tablo ." SET engel = 1 WHERE id = {$this->_ID}";
			self::$VT->SorguAta($sql);
			
			if (!self::$VT->Calistir())
				throw new Exception(sprintf(_r("Sorgu hatası: %s"), $sql));
		}
	}
	
	public function IzinVer()
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		if(self::YetkiDenetle("Yönetici")) // Yetki Kontrolü
		{
			$sql = "UPDATE ". self::$Tablo ." SET engel = 0 WHERE id = {$this->_ID}";
			self::$VT->SorguAta($sql);
			
			if (!self::$VT->Calistir())
				throw new Exception(sprintf(_r("Sorgu hatası: %s"), $sql));
		}
	}
	
	public static function KullaniciSifreHash($sifre)
	{
		$sifre = base64_encode($sifre);
		return md5($sifre);
	}
	
	private static function KullaniciCookieOlustur($id, $hatirla = false)
	{
		global $Ayarlar;
		if(headers_sent())
			throw new Exception(_r("HTTP başlıkları gönderilmiş"));
		
		$cookie_verisi = base64_encode($id) ."-". md5($id);
		$cookie_verisi = base64_encode($cookie_verisi);
		if($hatirla)
			$sure = time() + $Ayarlar->Kullanici->CerezSuresi;
		else
			$sure = 0;
		
		if(!setcookie($Ayarlar->Kullanici->CerezAdi, $cookie_verisi, $sure, "/"))
			throw new Exception(_r("Çerez oluşturulamıyor"));
	}
	
	private static function KullaniciOgesi(&$result)
	{
		$kullanici = new Kullanici;
		$kullanici->ID = $result["id"];
		$kullanici->Isim = $result["isim"];
		$kullanici->Sifre = $result["sifre"];
		$kullanici->Seviye = $result["seviye"];
		$kullanici->Eposta = $result["eposta"];
		$kullanici->KayitTarihi = $result["kayit_tarihi"];
		$kullanici->SonGirisZamani = $result["son_giris"];
		$kullanici->Engel = $result["engel"];
		$kullanici->DogumTarihi = $result["dogum_tarihi"];
		$kullanici->Sehir = $result["sehir"];
		$kullanici->Ulke = $result["ulke"];
		
		return $kullanici;
	}
	
	public static function VtAyarla(&$vt)
	{
		self::$VT = $vt;
	}
	
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
	
	/* ------ */
	
	public static function Ekle($nesne)
	{
		global $Ayarlar;
				
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		try {
			self::IsimdenKullanici($nesne->Isim);
			throw new Exception(_r("Kullanıcı adı kullanılıyor"));
		}
		catch (Exception $exc) {
			if ($exc->getMessage() == _r("Kullanıcı bulunamadı")) {
				$sql = "INSERT INTO ". self::$Tablo ." (isim, sifre, eposta, seviye) 
						VALUES ('{$nesne->Isim}', '". self::KullaniciSifreHash($nesne->Sifre) ."', '{$nesne->Eposta}', '{$nesne->Seviye}')";
				self::$VT->SorguAta($sql);
				
				if(self::$VT->Calistir())
				{
					return self::Al(self::$VT->SonEklenenID());
				}
				else
					throw new Exception(sprintf(_r("Sorgu hatası: %s"), $sql));
			}
			else
				throw $exc;
		}
	}
	
	public static function Sehirler()
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		$sql = "SELECT * FROM sehirler ORDER BY plaka";
		self::$VT->SorguAta($sql);
		$arr = array();
		if (self::$VT->SatirlariAl($Veri))
		{
			foreach($Veri as $v)
				$arr[$v["plaka"]] = $v["ad"];
		}
		return $arr;
	}
	
	public static function CerezdenKullanici()
	{
		global $Ayarlar, $Kullanici;
		
		if (!isset($Kullanici)) {
			if(!isset($_COOKIE[$Ayarlar->Kullanici->CerezAdi]))
				return NULL;
				
			$cookie_verisi = base64_decode($_COOKIE[$Ayarlar->Kullanici->CerezAdi]);
			list($id, $md5_id) = explode("-", $cookie_verisi);
			$id = base64_decode($id);
			if(md5($id) == $md5_id)
				$Kullanici = self::Al($id);
			else
				return NULL;
		}
		
		return $Kullanici;
	}
	
	public static function IsimdenKullanici($KullaniciAdi)
	{
		if (self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
		
		$sql = "SELECT * FROM ". self::$Tablo ." WHERE isim = '{$KullaniciAdi}'";
		self::$VT->SorguAta($sql);
		
		if(self::$VT->SatirAl($Veri))
		{
			if(self::$VT->SatirSayisi() > 0) {
				
				return self::KullaniciOgesi($Veri);
			}
			else
				throw new Exception(_r("Kullanıcı bulunamadı"));
		}
		else
			throw new Exception(sprintf(_r("Sorgu hatası: %s"), $sql));
	}
	
	public static function KullanicilariAl($Baslangic = NULL, $Adet = NULL, $SiralamaKriteri = "id", $SiralamaYonu = "ASC")
	{
		if(self::$VT == NULL)
			throw new Exception(_r("Veritabanı bağlantısı hazır değil"));
			
		if($Baslangic === NULL)
			$Baslangic = 0;
			
		if($Adet === NULL || $Adet == 0)
		{
			$SaySQL = "SELECT COUNT(*) AS sayi FROM ". self::$Tablo;
			self::$VT->SorguAta($SaySQL);
			if(self::$VT->DegerAl($sayi))
				$Adet = $sayi - $Baslangic;
		}
		
		$sql = "SELECT * FROM ". self::$Tablo ." ORDER BY ". $SiralamaKriteri ." ". $SiralamaYonu;
		self::$VT->SorguAta($sql, $Adet, $Baslangic);
		
		if(self::$VT->SatirlariAl($Veri))
		{
			$kullanicilar = array();
			foreach($Veri as $v) {
				$kullanicilar[] = self::KullaniciOgesi($v);
			}
			
			return $kullanicilar;
		}
		else
			throw new Exception(sprintf(_r("Sorgu hatası: %s"), $sql));
	}
}

?>
<?php
class Yorum
{
	private $_islemler;
	private $_galeri;
	private $_yazan;
	private $_yanitlar;
	private $_ust_yorum;
	
	public $ID;
	public $Yazi;
	public $YazimTarihi;
	public $YazanID;
	public $UstYorumID;
	public $GaleriID;
	public $Yazan;
	
	public function Yorum() { $this->Yazan = new stdClass; }
	
	public function Galeri()
	{
		if ($this->_galeri == NULL) {
			$this->_galeri = GaleriIslemleri::Al($this->GaleriID);
		}
		return $this->_galeri;
	}
	
	public function Yazan()
	{
		if ($this->YazanID != NULL) {
			if ($this->_yazan == NULL) {
				$this->_yazan = KullaniciIslemleri::Al($this->YazanID);
			}
			return $this->_yazan;	
		}
		else {
			return $this->Yazan;
		}
	}
	
	public function UstYorum()
	{
		if ($this->_ust_yorum == NULL) {
			$this->_ust_yorum = YorumIslemleri::Al($this->UstYorumID);
		}
		return $this->_ust_yorum;
	}
	
	public function Yanitlar()
	{
		if ($this->_yanitlar == NULL) {
			$this->_yanitlar = YorumIslemleri::YorumYanitlari($this->ID);
		}
		return $this->_yanitlar;
	}
	
	public function Islemler(&$islem = NULL)
	{
		if ($islem === NULL) {
			if ($this->_islemler == NULL) {
				$this->_islemler = new YorumIslemleri($this->ID);
			}
			return $this->_islemler;
		}
		else {
			$this->_islemler = &$islem;
		}
	}
}

class YorumIslemleri
{
	public static $Tablo = "yorumlar";
	private static $VT = NULL;

	private $_ID;
	private $_yorum;
	
	public function YorumIslemleri($ID)
	{
		$this->_ID = intval($ID);
	}
	
	public function Yorum()
	{
		if ($this->_yorum == NULL) {
			$this->_yorum = self::Al($this->_ID);
			$this->_yorum->Islemler($this);
		}
		return $this->_yorum;
	}
	
	public function Sil()
	{
		if(self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
	
		if(self::YetkiDenetle("Editör", $this->Yorum()->YazanID)) // Erişim Kontrolü
		{
			$sql = "DELETE FROM ". self::$Tablo ." WHERE id = {$this->_ID}";
			self::$VT->SorguAta($sql);
			
			if(!self::$VT->Calistir())
				throw new Exception(sprintf("Sorgu hatası: %s", $sql));
		}
		else
			throw new Exception(sprintf("%s üzerinde değişiklik yapma izniniz yok", "Yorum"));
	}
	
	public static function Al($ID)
	{
		if(self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
		
		$sql = "SELECT * FROM ". self::$Tablo ." WHERE id = {$ID}";
		
		self::$VT->SorguAta($sql);
		if (self::$VT->SatirAl($Veri))
		{
			return self::YorumOgesi($Veri);
		}
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	/* Diğer Fonksiyonlar */
	
	public function YaziDegistir($yeniYazi)
	{
		if (!self::YetkiDenetle("Editör", $this->Yorum()->YazanID))
			throw new Exception(sprintf("%s üzerinde değişiklik yapma izniniz yok", "Yorum"));
			
		if (self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
			
		$sql = "UPDATE ". self::$Tablo ." SET yazi = '{$yeniYazi}' WHERE id = {$this->_ID}";
		self::$VT->SorguAta($sql);
		
		if (!self::$VT->Calistir())
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	public static function GaleriYorumlari($galeriID)
	{
		if (self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
		
		$yorumlar = array();
		
		$sql = "SELECT * FROM ". self::$Tablo ." WHERE galeri_id = {$galeriID} AND yorum_id IS NULL";
		self::$VT->SorguAta($sql);
		if (self::$VT->SatirlariAl($Veri)) {
			foreach($Veri as $v) {
				$yorumlar[] = self::YorumOgesi($v);
			}
			
			return $yorumlar;
		}
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	public static function YorumYanitlari($yorumID)
	{
		if (self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
		
		$yorumlar = array();
		
		$sql = "SELECT * FROM ". self::$Tablo ." WHERE yorum_id = {$yorumID}";
		self::$VT->SorguAta($sql);
		if (self::$VT->SatirlariAl($Veri)) {
			foreach($Veri as $v) {
				$yorumlar[] = self::YorumOgesi($v);
			}
			
			return $yorumlar;
		}
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	private static function YorumOgesi(&$result)
	{
		$yorum = new Yorum;
		$yorum->ID = $result["id"];
		$yorum->Yazi = $result["yazi"];
		$yorum->YazimTarihi = $result["yazim_tarihi"];
		$yorum->YazanID = $result["kullanici_id"];
		if ($yorum->YazanID == NULL) {
			$yorum->Yazan->Isim = $result["yazan_adi"];
			$yorum->Yazan->Eposta = $result["yazan_eposta"];
			$yorum->Yazan->Adres = $result["yazan_adres"];
		}
		$yorum->GaleriID = $result["galeri_id"];
		$yorum->UstYorumID = $result["yorum_id"];
		
		return $yorum;
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
	
	private static function Kriterize($kriterler = array())
	{
		global $Kullanici;
		
		$kosullar = array();
		
		if (isset($kriterler["kayitli"]))
			$kosullar[] = "kullanici_id IS NOT NULL";
			
		if (isset($kriterler["ziyaretci"]))
			$kosullar[] = "kullanici_id IS NULL";
		
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
	
	public static function Ekle($nesne)
	{
		if(self::$VT == NULL)
			throw new Exception("Veritabanı hazır değil");
		
		$ust_yorum = !empty($nesne->UstYorum) ? "'{$nesne->UstYorum}'" : "NULL";
		
		if ($nesne->YazanID != NULL)
			$sql = "INSERT INTO ". self::$Tablo ." (yazi, galeri_id, kullanici_id, yorum_id, yazan_ip) VALUES ('{$nesne->Yazi}', '{$nesne->GaleriID}', '{$nesne->YazanID}', {$ust_yorum}, '{$_SERVER["REMOTE_ADDR"]}')";
		else
			$sql = "INSERT INTO ". self::$Tablo ." (yazi, galeri_id, yazan_adi, yazan_eposta, yazan_adres, yorum_id, yazan_ip) VALUES ('{$nesne->Yazi}', '{$nesne->GaleriID}', '{$nesne->Yazan->Isim}', '{$nesne->Yazan->Eposta}', '{$nesne->Yazan->Adres}', {$ust_yorum}, '{$_SERVER["REMOTE_ADDR"]}')";
		
		self::$VT->SorguAta($sql);
		if (self::$VT->Calistir())
			return self::Al(self::$VT->SonEklenenID());
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	public static function YorumSayisi($kriterler = array(), $tablo = "")
	{
		if (empty($tablo))
			$tablo = self::$Tablo;
		
		$kosul = "WHERE ". self::Kriterize($kriterler);
		
		$sql = "SELECT COUNT(*) FROM {$tablo} {$kosul}";
		self::$VT->SorguAta($sql);
		
		if (self::$VT->DegerAl($sayi))
			return $sayi;
	}
	
	public static function YorumlariAl($kriterler = array(), $Siralama = "")
	{
		if(self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
		
		if (!empty($Siralama)) {
			$Siralama = "ORDER BY {$Siralama}";
		}
		
		$tablo = self::$Tablo;
		
		if (count($kriterler))
			$kosul = "WHERE ". self::Kriterize($kriterler);
		else
			$kosul = "";
		
		$sql = "SELECT * FROM {$tablo} {$kosul} {$Siralama}";
		self::$VT->SorguAta($sql);
		
		$yorumlar = array();
		if(self::$VT->SatirlariAl($Veri))
		{
			foreach($Veri as $v) {
				$yorumlar[] = self::YorumOgesi($v);
			}
			
			return $yorumlar;
		}
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
}
?>
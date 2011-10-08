<?php
class Resim
{
	private $_islemler;
	private $_galeri;
	
	public $ID;
	public $Dosya;
	public $KucukResim;
	public $Onizleme;
	public $OzCozunurluk;
	public $Cozunurlukler;
	public $GaleriID;
	
	public function Galeri(&$gal = NULL)
	{
		if ($gal === NULL) {
			if ($this->_galeri == NULL) {
				$this->_galeri = GaleriIslemleri::Al($this->GaleriID);
			}
			return $this->_galeri;
		}
		else {
			$this->_galeri = &$gal;
		}
	}
	
	public function Islemler(&$islem = NULL)
	{
		if ($islem === NULL) {
			if ($this->_islemler == NULL) {
				$this->_islemler = new ResimIslemleri($this->ID);
			}
			return $this->_islemler;
		}
		else {
			$this->_islemler = &$islem;
		}
	}
}

class ResimIslemleri
{
	public static $Tablo = "resimler";
	private static $VT = NULL;
	
	private $_ID;
	private $_resim;
	
	public function ResimIslemleri($ID)
	{
		$this->_ID = intval($ID);
	}
	
	public function Resim()
	{
		if ($this->_resim == NULL) {
			$this->_resim = self::Al($this->_ID);
			$this->_resim->Islemler($this);
		}
		return $this->_resim;
	}
	
	public function Sil()
	{
		if(self::YetkiDenetle("Editör", $this->Resim()->Galeri()->EkleyenID)) // Erişim Kontrolü
		{
			$sql = "DELETE FROM ". self::$Tablo ." WHERE id = {$this->_ID}";
			self::$VT->SorguAta($sql);
			
			if(!self::$VT->Calistir())
				throw new Exception(sprintf("Sorgu hatası: %s", $sql));
		}
	}
	
	/* Diğer Fonksiyonlar */
	
	public function DosyaDegistir($yeniDosya)
	{
		global $Ayarlar;
		
		if(!self::YetkiDenetle("Editör", $this->Resim()->Galeri()->EkleyenID))
			throw new Exception("Resim üzerinde değişiklik yapma izniniz yok");
		
		list($gen, $yuk) = getimagesize($Ayarlar->Resim->ResimKlasoru . $yeniDosya);
		
		$sql = "UPDATE ". self::$Tablo ." SET dosya = '{$yeniDosya}', oz_cozunurluk = '{$gen}x{$yuk}' WHERE id = {$this->_ID}";
		self::$VT->SorguAta($sql);
		
		if(!self::$VT->Calistir())
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	public static function GaleriResimleri($galeriID)
	{
		global $Ayarlar;
		
		if(self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
		
		$resimler = array();
		
		$sql = "SELECT * FROM ". self::$Tablo ." WHERE galeri_id = {$galeriID}";
		self::$VT->SorguAta($sql);
		if (self::$VT->SatirlariAl($Veri)) {
			foreach($Veri as $v) {
				$resim = new Resim;
				$resim->ID = $v["id"];
				$resim->Dosya = $Ayarlar->Resim->ResimKlasoru . $v["dosya"];
				$resim->KucukResim = $Ayarlar->Resim->OnizlemeKlasoru ."kucuk_". $v["dosya"];
				$resim->Onizleme = $Ayarlar->Resim->OnizlemeKlasoru ."onizleme_". $v["dosya"];
				$resim->OzCozunurluk = new Cozunurluk($v["oz_cozunurluk"]);
				$resim->Cozunurlukler = Cozunurluk::UygunCozunurlukler($resim->OzCozunurluk);
				$resim->GaleriID = $v["galeri_id"];
				
				$resimler[] = $resim;
			}
			
			return $resimler;
		}
		else
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
	
	public static function VtAyarla(&$vt)
	{
		self::$VT = $vt;
	}
	
	public static function Ekle($nesne)
	{
		global $Ayarlar;
		
		if (self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
		
		if (!self::YetkiDenetle("Kullanıcı"))
			throw new Exception(sprintf("%s eklemek için giriş yapmalısınız", "Resim"));
			
		list($gen, $yuk) = getimagesize($Ayarlar->Resim->ResimKlasoru . $nesne->Dosya);
			
		$sql = "INSERT INTO ". ResimIslemleri::$Tablo ." (dosya, oz_cozunurluk, galeri_id) VALUES ('{$nesne->Dosya}', '{$gen}x{$yuk}', {$nesne->GaleriID})";
		self::$VT->SorguAta($sql);
		
		if(self::$VT->Calistir())
			return self::Al(self::$VT->SonEklenenID());
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	public static function Al($id)
	{
		global $Ayarlar;
		
		if(self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
		
		$sql = "SELECT * FROM ". self::$Tablo ." WHERE id = {$id}";
		self::$VT->SorguAta($sql);
		if(self::$VT->SatirAl($Veri)) {
			$resim = new Resim;
			$resim->ID = $Veri["id"];
			$resim->Dosya = $Ayarlar->Resim->ResimKlasoru . $Veri["dosya"];
			$resim->KucukResim = $Ayarlar->Resim->OnizlemeKlasoru ."kucuk_". $Veri["dosya"];
			$resim->Onizleme = $Ayarlar->Resim->OnizlemeKlasoru ."onizleme_". $Veri["dosya"];
			$resim->OzCozunurluk = new Cozunurluk($Veri["oz_cozunurluk"]);
			$resim->Cozunurlukler = Cozunurluk::UygunCozunurlukler($resim->OzCozunurluk);
			$resim->GaleriID = $Veri["galeri_id"];
			
			return $resim;
		}
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
	
	public static function ResimleriAl($Adet = NULL, $Baslangic = NULL, $SiralamaKriteri = "id", $SiralamaYonu = "ASC")
	{
		global $Ayarlar;
		
		if(self::$VT == NULL)
			throw new Exception("Veritabanı bağlantısı hazır değil");
			
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
		
		$resimler = array();
		if(self::$VT->SatirlariAl($Veri))
		{
			foreach($Veri as $v) {
				$resim = new Resim;
				$resim->ID = $v["id"];
				$resim->Dosya = $Ayarlar->Resim->ResimKlasoru . $v["dosya"];
				$resim->KucukResim = $Ayarlar->Resim->OnizlemeKlasoru ."kucuk_". $v["dosya"];
				$resim->Onizleme = $Ayarlar->Resim->OnizlemeKlasoru ."onizleme_". $v["dosya"];
				$resim->OzCozunurluk = new Cozunurluk($v["oz_cozunurluk"]);
				$resim->Cozunurlukler = Cozunurluk::UygunCozunurlukler($resim->OzCozunurluk);
				$resim->GaleriID = $v["galeri_id"];
				
				$resimler[] = $resim;
			}
			
			return $resimler;
		}
		else
			throw new Exception(sprintf("Sorgu hatası: %s", $sql));
	}
}
?>
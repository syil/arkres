<?php
class Cozunurluk
{
	public static $Bos = NULL;
	private static $_cozunurlukler = array();
	
	public $Genislik;
	public $Yukseklik;
	public $Etiket;
	public $Grup;
	
	private $_oran;
	
	public function Cozunurluk($str = "")
	{
		if (!empty($str)) {
			self::JsonKontrol();
			list($this->Genislik, $this->Yukseklik) = explode("x", $str);
			
			foreach (self::$_cozunurlukler as $coz) {
				if (self::Karsilastir($this, $coz) == 0) {
					$this->Etiket = $coz->Etiket;
					$this->Grup = $coz->Grup;
				}
			}
			
			if ($this->Etiket == NULL || $this->Grup == NULL) {
				$this->Etiket = "";
				$this->Grup = "Diğer";
			}
		}
	}
	
	public function PikselSayisi()
	{
		return $this->Genislik * $this->Yukseklik;
	}
	
	public function Oran()
	{
		if ($this->_oran == NULL)
			$this->_oran = new Oran($this->Genislik, $this->Yukseklik);
		
		return $this->_oran;
	}
	
	public function Oranla($oran)
	{
		$cOran = $this->Oran();
		if ($cOran->Deger() == $oran->Deger())
			return $this;
			
		if ($cOran->Deger() > $oran->Deger()) {
			$yeniG = $oran->Pay * $this->Yukseklik / $oran->Payda;
			return new Cozunurluk($yeniG ."x". $this->Yukseklik);
		}
		else {
			$yeniY = $oran->Payda * $this->Genislik / $oran->Pay;
			return new Cozunurluk($this->Genislik ."x". $yeniY);
		}
	}
	
	public function __toString()
	{
		return "{$this->Genislik}x{$this->Yukseklik}";
	}
	
	private static function JsonKontrol()
	{
		if (count(self::$_cozunurlukler) == 0) {
			$json = json_decode(file_get_contents("veri/cozunurlukler.json"));
			foreach ($json as $item) {
				$coz = new Cozunurluk;
				$coz->Genislik = $item->Genislik;
				$coz->Yukseklik = $item->Yukseklik;
				$coz->Etiket = $item->Etiket;
				$coz->Grup = $item->Grup;
				
				self::$_cozunurlukler[] = $coz;
			}
		}
	}
	
	public static function UygunCozunurlukler($coz)
	{
		self::JsonKontrol();
		$uygunlar = array();
		foreach (self::$_cozunurlukler as $oge) {
			if (self::Karsilastir($coz, $oge) === 1 || self::Karsilastir($coz, $oge) === 0) {
				$uygunlar[$oge->Grup][] = $oge;
			}
		}
		return $uygunlar;
	}
	
	public static function BilinenCozunurlukler()
	{
		self::JsonKontrol();
		$ret = array();
		foreach (self::$_cozunurlukler as $oge) {
			$ret[$oge->Grup][] = $oge;
		}
		return $ret;
	}
	
	public static function Karsilastir($coz1, $coz2)
	{
		if (($coz1->Genislik == $coz2->Genislik) && ($coz1->Yukseklik == $coz2->Yukseklik)) { 		// $coz1 == $coz2
			return 0;
		}
		else if (($coz1->Genislik >= $coz2->Genislik) && ($coz1->Yukseklik >= $coz2->Yukseklik)) { 	// $coz1, $coz2' yi kapsar
			return 1;
		}
		else {																						// Hiçbiri
			return -1;
		}
			
		/* if ($coz1->PikselSayisi() > $coz2->PikselSayisi()) {
			return 1;
		}
		else if($coz1->PikselSayisi() < $coz2->PikselSayisi()) {
			return -1;
		}
		else {
			if (($coz1->Genislik == $coz2->Genislik) && ($coz1->Yukseklik == $coz2->Yukseklik)) {
				return 0;
			}
			else {
				if($coz1->Genislik > $coz2->Genislik)
					return 1;
				else
					return -1;
			}
		} */
	}
}

class Oran
{
	public $Pay, $Payda;
	
	public function Oran($a, $b)
	{
		$this->Pay = $a;
		$this->Payda = $b;
		$this->Sadelestir();
	}
	
	private static function gcd($n, $m) 
	{
		$n = abs($n); 
		$m = abs($m);
		
		if ($n == 0 && $m == 0)
			return 1;
		
		if ($n == $m && $n >= 1)
			return $n;
		  
		return $m < $n ? self::gcd($n - $m, $n) : self::gcd($n, $m - $n);
	}
	
	public function Sadelestir()
	{
		$ebob = self::gcd($this->Pay, $this->Payda);
		$this->Pay /= $ebob;
		$this->Payda /= $ebob;
	}
	
	public function Deger()
	{
		$a = $this->Pay / $this->Payda;
		return sprintf('%01.2f', $a);
	}
}

Cozunurluk::$Bos = new Cozunurluk('-1x-1');
?>
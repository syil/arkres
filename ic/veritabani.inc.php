<?php
class Veritabani
{
	private $_Query = "";
	private $_Identifier = NULL;
	private $_Log = array();
	private $_Error = false;
	private $_MysqlResource = NULL;
	private $_Limit = 0;
	private $_Offset = 0;
	private $_DateFormat = "d/m/Y H:i:s";
	
	public function Veritabani($Host, $KullaniciAdi, $Sifre, $VeritabaniAdi, $KarakterSeti = "utf8")
	{
		$this->_Identifier = mysqli_connect($Host, $KullaniciAdi, $Sifre, $VeritabaniAdi);
		if($this->_Identifier)
		{
			$this->Gunlukle("Bağlantı kuruldu");
		}
		else
		{
			$this->_Error = true;
			$this->Gunlukle("Bağlantı kurulamadı");
		}
		
		mysqli_query($this->_Identifier, "SET NAMES '". $KarakterSeti ."'");
		mysqli_query($this->_Identifier, "SET CHARACTER SET ". $KarakterSeti);
		mysqli_query($this->_Identifier, "SET lc_time_names = 'tr_TR'");
	}
	
	public function SorguAta($Sorgu, $Sinir = false, $Baslangic = false)
	{
		$Str = "";
		if($Sinir !== false && $Baslangic === false)
			$Str = " LIMIT ". $Sinir;
		else if($Sinir !== false && $Baslangic !== false)
			$Str = " LIMIT ". $Baslangic .", ". $Sinir;
		
		$this->_Query = $Sorgu . $Str;
	}
	
	public function SorguAl()
	{
		return "<pre>". htmlentities($this->_Query) ."</pre>";
	}
	
	public function SatirAl(&$Nesne)
	{
		$Nesne = NULL;
		if($this->Calistir())
		{
			$Nesne = mysqli_fetch_array($this->_MysqlResource);
			return true;
		}
		else
			return false;
	}
	
	public function DegerAl(&$Degisken)
	{
		$Degisken = NULL;
		if($this->Calistir())
		{
			$row = mysqli_fetch_array($this->_MysqlResource, MYSQLI_NUM);
			$Degisken = $row ? $row[0] : NULL;
			return true;
		}
		else
			return false;
	}
	
	public function SatirlariAl(&$Dizi)
	{
		$Dizi = array();
		if($this->Calistir())
		{
			while($row = mysqli_fetch_array($this->_MysqlResource))
				$Dizi[] = $row;
			
			return true;
		}
		else
			return false;
	}
	
	public function Calistir()
	{
		if($this->_Query == "" || $this->_Identifier == NULL)
			return false;
		
		if($this->_MysqlResource = mysqli_query($this->_Identifier, $this->_Query))
		{
			$this->Gunlukle("Sorgu Çalıştırıldı : ". $this->SorguAl());
			return true;
		}
		else
			return false;
	}
	
	public function KarakterTemizle($String)
	{
		if($this->_Identifier)
			return mysqli_real_escape_string($this->_Identifier, $String);
		else
			return $String;
	}
	
	public function SonEklenenID()
	{
		return mysqli_insert_id($this->_Identifier);
	}
	
	public function SatirSayisi()
	{
		if($this->_MysqlResource == NULL || $this->_Identifier == NULL)
			return false;
		if(strpos("select", $this->_Query) === false)
			return mysqli_affected_rows($this->_Identifier);
		else
			return mysqli_num_rows($this->_MysqlResource);
	}
	
	public function Kapat()
	{
		mysqli_close($this->_Identifier);
		$this->Gunlukle("Bağlantı kesildi");
	}
	
	private function Gunlukle($Olay)
	{
		$this->_Log[] = date($this->_DateFormat) ." - ". $Olay;
	}
	
	public function Rapor()
	{
		$str = "<ol>";
		foreach($this->_Log as $l)
			$str .= "<li>". $l ."</li>";
		$str .= "</ol>";
		return $str;
	}
}
?>
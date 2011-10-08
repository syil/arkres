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
		$this->_Identifier = mysql_connect($Host, $KullaniciAdi, $Sifre);
		if($this->_Identifier)
		{
			mysql_select_db($VeritabaniAdi, $this->_Identifier);
			$this->Gunlukle("Bağlantı kuruldu");
		}
		else
		{
			$this->_Error = true;
			$this->Gunlukle("Bağlantı kurulamadı");
		}
		
		mysql_query("SET NAMES '". $KarakterSeti ."'", $this->_Identifier);
		mysql_query("SET CHARACTER SET ". $KarakterSeti, $this->_Identifier);
		mysql_query("SET lc_time_names = 'tr_TR'");
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
			$Nesne = mysql_fetch_array($this->_MysqlResource);
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
			$Degisken = mysql_result($this->_MysqlResource, 0);
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
			while($row = mysql_fetch_array($this->_MysqlResource))
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
		
		if($this->_MysqlResource = mysql_query($this->_Query, $this->_Identifier))
		{
			$this->Gunlukle("Sorgu Çalıştırıldı : ". $this->SorguAl());
			return true;
		}
		else
			return false;
	}
	
	public function KarakterTemizle($String)
	{
		// if(function_exists("mysql_real_escape_string"))
			// return mysql_real_escape_string(str_replace('\\', '\\\\', $String), $this->_Identifier);
		// else
			// return mysql_escape_string(str_replace('\\', '\\\\', $String));
		return $String;
	}
	
	public function SonEklenenID()
	{
		return mysql_insert_id($this->_Identifier);
	}
	
	public function SatirSayisi()
	{
		if($this->_MysqlResource == NULL || $this->_Identifier == NULL)
			return false;
		if(strpos("select", $this->_Query) === false)
			return mysql_affected_rows($this->_Identifier);
		else
			return mysql_num_rows($this->_MysqlResource);
	}
	
	public function Kapat()
	{
		mysql_close($this->_Identifier);
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
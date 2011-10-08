<?php 
chdir("..");
include "ic/sistem.php";

$username = post("username");
$password = post("password");
$ajax = post("ajax") or $ajax = false;
try {
	if (giris())
		throw new Exception(_r("Zaten giriş yapılmış"));
	if (!preg_match("/^[\w\._-]{3,32}$/", $username))
		throw new Exception(_r("Geçersiz kullanıcı adı"));
	if (empty($password))
		throw new Exception(_r("Şifre belirtmelisin"));
		
	$giris = KullaniciIslemleri::IsimdenKullanici($username);
	$giris->GirisYap($password);
	
	if ($ajax) {
?>
	<p class="info">
		<?php _e("Giriş başarılı, yönlendiriliyorsunuz"); ?>
	</p>
	<script type="text/javascript">
		redirect();
	</script>
<?php
	}
}
catch (Exception $exc) {
	echo "<p class='error'>{$exc->getMessage()}<br />";
	echo "</p>";
}
$vt->Kapat();
?>
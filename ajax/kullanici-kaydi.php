<?php
chdir("..");
include "ic/sistem.php";

$username = post("reg_user");
$email = post("reg_mail");
$password = post("reg_pass");
$password2 = post("reg_pass2");
$ajax = post("ajax") or $ajax = false;
$agreement = post("agreement") or $agreement = 0;

$enaz = $Ayarlar->Kullanici->IsimUzunlugu[0];
$encok = $Ayarlar->Kullanici->IsimUzunlugu[1];
try {
	if (!preg_match("/^[\w\._-]{{$enaz},{$encok}}$/", $username))
		throw new Exception(sprintf(_r("Kullanıcı adı %d-%d karakterli olmalıdır ve sadece (%s) karakterleri kullanılabilir"), $enaz, $encok, ". _ -"));
	if (!preg_match("/^[\w._%-]+@[\w._%-]+\.[\w|\.]{2,6}$/", $email))
		throw new Exception(_r("E-posta adresi geçersiz"));
	if (empty($password))
		throw new Exception(_r("Şifre belirtmelisin"));
	if (!$agreement)
		throw new Exception(_r("Kullanıcı sözleşmesini kabul etmelisin"));
	if ($password != $password2)
		throw new Exception(_r("Şifreler birbiriyle uyuşmuyor"));
	
	$yeni = new Kullanici;
	$yeni->Isim = $username;
	$yeni->Eposta = $email;
	$yeni->Seviye = $KullaniciSeviyeleri[0];
	$yeni->Sifre = $password;
	
	$yeni = KullaniciIslemleri::Ekle($yeni);
	$yeni->GirisYap($password);
	
	if ($ajax) {
?>
	<p class="info">
		<strong><?php printf(_r("Hayırlı olsun; %s"), $yeni->Isim); ?></strong><br />
		<?php _e("Birkaç saniye içerisinde yönlendirileceksiniz..."); ?>
		<script type="text/javascript">
			setTimeout(function() { redirect(); }, 2000);
		</script>
	</p>
<?php
	}
}
catch (Exception $exc) {
	echo "<p class='error'><strong>". _r("Aşağıdaki hatayı düzeltmen gerekir"). "</strong><br />";
		echo "{$exc->getMessage()}<br />";
	echo "</p>";
}
$vt->Kapat();
?>
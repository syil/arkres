<?php
chdir("..");
include "ic/sistem.php";

$country = post("country");
$city = post("city");
$email = post("email");
$day = post("day");
$month = post("month");
$year = post("year");
$password = post("password");
$password2 = post("password2");
$oldpassword = post("oldpassword");
$ajax = post("ajax") or $ajax = false;

try {
	if (!giris())
		throw new Exception(_r("Giriş yapmış kullanıcı yok"));
	if (!$Kullanici->SifreKontrol($oldpassword))
		throw new Exception(_r("Şifre yanlış"));
	if (!preg_match("/^[\w._%-]+@[\w._%-]+\.[\w|\.]{2,6}$/", $email))
		throw new Exception(_r("E-posta adresi geçersiz"));
	if (!empty($password))
		if ($password != $password2)
			throw new Exception(_r("Şifreler birbiriyle uyuşmuyor"));
	if (!empty($day) || !empty($month) || !empty($year))
		if (!checkdate($month, $day, $year))
			throw new Exception(_r("Girdiğiniz tarih geçersiz"));
	
	$islem = $Kullanici->Islemler();
	
	$islem->EPostaDegistir($email);
	$islem->SehirDegistir($city);
	$islem->UlkeDegistir($country);
	$islem->DogumTarihiDegistir(strftime(MYSQL_TARIH, mktime(0, 0, 0, $month, $day, $year)));
	if (!empty($password)) {
		$islem->SifreDegistir($password);
	}
	if ($ajax) {
?>
	<p class="info">
		<?php _e("Bilgileriniz güncellendi"); ?>
	</p>
<?php
	}
}
catch (Exception $exc) {
	echo "<p class='error'>{$exc->getMessage()}</p>";
}
$vt->Kapat();
?>
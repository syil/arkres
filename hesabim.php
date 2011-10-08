<?php 
if (!giris())
	header("location: /kayit-ol?r:%2Fhesabim");

set_title("{$Ayarlar->Genel->SiteAdi} - ". _r("Hesap Ayarlarım"));
?>
<div class="wrapper" id='wrapper_main'>
<div class="center">
	<div id="feature_info">
	<h2><?php _e(""); ?></h2>
	<!-- end feature_info-->
	</div>		
	
	<div id="main">
			
	<div class='content'>
			
		<div class="entry">
		    <h1 class='siteheading'><?php printf(_r("%s - Hesap Ayarları"), $Kullanici->Isim); ?></h1>
					   
		    <div class="entry-content">
				<p></p>
				<form action="#" id="personal" method="post" class="ajax_form">
					<fieldset>
						<h3><span><?php _e("Kişisel Bilgiler") ?></span></h3>
						
						<label for="country"><?php _e("Ülke"); ?></label>
						<p>
							<select name="country" id="country" class="text_input">
								<option value=""><?php _e("..."); ?></option>
								<?php 
									foreach (ulkeleri_al() as $ulke) {
										$sec = '';
										if ($ulke == $Kullanici->Ulke)
											$sec = "selected='selected'";
										echo "<option value='{$ulke}' {$sec}>{$ulke}</option>";
									}
								?>
							</select>
						</p>
						
						<label for="city"><?php _e("Şehir"); ?></label>
						<p><input name="city" class="text_input" type="text" id="city" value="<?php echo $Kullanici->Sehir; ?>" /></p>
						
						<label for="day"><?php _e("Doğum tarihi"); ?></label>
						<p>
							<select name="day" class="dropdown" id="day">
								<option value=""><?php _e("..."); ?></option>
								<?php
									$eski_gun = tarih_bicimlendir("%d", $Kullanici->DogumTarihi);
									foreach (range(1, 31) as $gun) {
										$sec = '';
										if ($gun == $eski_gun)
											$sec = "selected='selected'";
										echo "<option value='{$gun}' {$sec}>{$gun}</option>";
									}
								?>
							</select>
							<select name="month" class="dropdown" id="month">
								<option value=""><?php _e("..."); ?></option>
								<?php
									$eski_ay = tarih_bicimlendir("%m", $Kullanici->DogumTarihi);
									$aylar = array("", "Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık");
									foreach (range(1, 12) as $ay) {
										$sec = '';
										if ($ay == $eski_ay)
											$sec = "selected='selected'";
										echo "<option value='{$ay}' {$sec}>". _r($aylar[$ay]) ."</option>";
									}
								?>
							</select>
							<input name="year" class="text_input" type="text" id="year" value="<?php echo tarih_bicimlendir("%Y", $Kullanici->DogumTarihi) ?>" />
						</p>
						
						<h3><span><?php _e("Üyelik Bilgileri") ?></span></h3>
						<label for="email"><?php _e("E-Posta adresi"); ?></label>
						<p><input name="email" class="text_input is_email" type="text" id="email" value="<?php echo $Kullanici->Eposta; ?>" /></p>
						
						<label for="password"><?php _e("Şifre Değiştir"); ?></label>
						<p><input name="password" class="text_input" type="password" id="password" /></p>
						
						<label for="password2"><?php _e("Şifre doğrula"); ?></label>
						<p><input name="password2" class="text_input" type="password" id="password2" /></p>
						
						<div class="hr"></div>
						
						<label for="oldpassword"><?php _e("Eski Şifre"); ?>*</label>
						<p><input name="oldpassword" class="text_input is_empty" type="password" id="oldpassword" /></p>
						<p>Değişiklikleri güncellemeye devam etmek için eski şifrenizi girmelisiniz</p>
						<div id="p_formstatus" class="rounded ajax_response"></div>
						<p class="clearboth"></p>
						<p>
							<input name="send" type="submit" value="<?php _e("Bilgileri Güncelle"); ?>" class="button" id="send" size="16"/>
						</p>
					</fieldset>
						
				</form>
				
				<script type="text/javascript">
					jQuery(function() {
						jQuery('#personal').kriesi_ajax_form({
							sendPath: 'ajax/kullanici-guncelle.php',
							responseContainer : '#p_formstatus'
						});
					});
				</script>
			</div>
		</div>
	<!--end content -->
	</div>
	<div class="sidebar">
		<div class="box_small box widget">	
			<h1 class='widgettitle'><?php _e("İstatistikler"); ?></h1>
			<?php
				$kriterler = array("kullanici" => $Kullanici->ID);
				// Kullanıcının yazdığı yorumların sayısı
				$YorumSayisi  = YorumIslemleri::YorumSayisi($kriterler);
				// Kullanıcının eklediği galeri sayısı
				$GaleriSayisi = GaleriIslemleri::GaleriSayisi($kriterler);
				// Kullanıcının beğendiği galeri sayısı (Beğenme eşik değeri 3)
				$vt->SorguAta("SELECT COUNT(*) FROM ".GaleriIslemleri::$YildizTablosu." WHERE kullanici_id = {$Kullanici->ID} AND yildiz > 3");
				$vt->DegerAl($BegendigiSayisi);
				// Kullanıcının beğenilen galerileri
				$vt->SorguAta("SELECT COUNT(*) FROM ".GaleriIslemleri::$YildizTablosu." WHERE galeri_id IN (SELECT id FROM ".GaleriIslemleri::$PuanliTablo." WHERE kullanici_id = {$Kullanici->ID}) AND yildiz > 3");
				$vt->DegerAl($BegenildigiSayisi);
			?>
			<h3><?php _e("Galeriler") ?></h3>
			<p>
				<?php printf(_r("<strong>%d</strong> galeri eklemişsin"), $GaleriSayisi); ?>
				<br />
				<?php printf(_r("<strong>%d</strong> galeri beğenmişsin"), $BegendigiSayisi); ?>
				<br />
				<?php printf(_r("Senin galerilerin <strong>%d</strong> defa beğenilmiş"), $BegenildigiSayisi); ?>
			</p>
			<h3><?php _e("Yorumlar") ?></h3>
			<p><?php printf(_r("<strong>%d</strong> yorum yazmışsın"), $YorumSayisi); ?></p>
			<!--end box-->
		</div>
	<!-- end sidebar-->	
	</div>
	
	<!--end main-->
	</div>

<!-- end center-->
</div>
<!--end wrapper-->
</div>
<?php
set_title("{$Ayarlar->Genel->SiteAdi} - ". _r("Kayıt ol"));

$yonlen = get("r") or $yonlen = "%2F";
$yonlen = urldecode($yonlen);
?>
<div class="wrapper" id='wrapper_main'>
<div class="center">
	<div id="feature_info">
	<h2><?php _e("Üye olun veya giriş yapın"); ?></h2>
	<!-- end feature_info-->
	</div>		
	
	<div id="main">
			
	<div class='content'>
			
		<div class="entry">
		    <h1 class='siteheading'><?php _e("Aramıza katıl"); ?></h1>
					   
		    <div class="entry-content">
				<p>Sadece 4 küçük alanı doldurarak aramıza katılabilirsin. Duvarkağıdı galerimize sende katkı sağlar ve beğendiğin duvarkağıdını herkesle paylaşırsın.</p>
		   
			   <form action="#" id="register" method="post" class="ajax_form">
					<fieldset>
						<h3><span><?php _e("Kullanıcı üyelik formu") ?></span></h3>
						<div id="r_formstatus" class="rounded ajax_response"></div>
						
						<label for="reg_mail"><?php _e("E-Posta adresi"); ?>*</label>
						<p><input name="reg_mail" class="text_input is_email" type="text" id="reg_mail" /></p>
						
						<label for="reg_user"><?php _e("Kullanici Adi"); ?>*</label>
						<p><input name="reg_user" class="text_input is_empty" type="text" id="reg_user" size="50" value='' /></p>
						
						<label for="reg_pass"><?php _e("Şifre"); ?>*</label>
						<p><input name="reg_pass" class="text_input is_empty" type="password" id="reg_pass" /></p>
						
						<label for="reg_pass2"><?php _e("Şifre doğrula"); ?>*</label>
						<p><input name="reg_pass2" class="text_input is_empty" type="password" id="reg_pass2" /></p>
						
						<p>
							<label>
								<input name="agreement" type="checkbox" value="1" id="agreement" />
								<?php printf(_r("Kullanıcı sözleşmesini <a href='%s'>okudum</a> ve onaylıyorum"), "#"); ?>
							</label>
						</p>
						
						<p>
							<input name="send" type="submit" value="<?php _e("Kayıt ol"); ?>" class="button" id="send" size="16"/>
						</p>
					</fieldset>
						
				</form>
				<script type="text/javascript">
					function redirect() {
						document.location.href = '<?php echo $yonlen; ?>';
					}
					jQuery(function() {
						jQuery('#register').kriesi_ajax_form({
							sendPath: 'ajax/kullanici-kaydi.php',
							responseContainer : '#r_formstatus'
						});
						
						jQuery('#login').kriesi_ajax_form({
							sendPath: 'ajax/kullanici-giris.php',
							responseContainer : '#l_formstatus'
						});
					});
				</script>
			<!--end entry-content-->
			</div>
			
		<!--end entry -->	
		</div>
<!--end content -->
	</div>
	<div class="sidebar">
		
		<div class="box_small box widget">	
			<h1 class='widgettitle'><?php _e("Giriş"); ?></h1>
			<form action="#" id="login" method="post" class="ajax_form">
				<fieldset>
					<h3><span><?php _e("Kullanıcı girişi") ?></span></h3>
					<div id="l_formstatus" class="rounded ajax_response"></div>
					
					<label for="username"><?php _e("Kullanici Adi"); ?>*</label>
					<p><input name="username" class="text_input is_empty" type="text" id="username" size="50" value='' /></p>
					
					<label for="password"><?php _e("Şifre"); ?>*</label>
					<p><input name="password" class="text_input is_empty" type="password" id="password" /></p>
					<p>
						<input name="submit_login" type="submit" value="<?php _e("Giriş Yap"); ?>" class="button" id="submit_login" size="16"/>
					</p>
				</fieldset>
			</form>
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
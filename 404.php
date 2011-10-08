<?php
header("HTTP/1.1 404 Not Found");
?>
<div class="wrapper" id='wrapper_main'>
	<div class="center">
		<div id="main">
			<div class='content'>
		
				<div class="entry">
					<h1 class='siteheading'><?php _e("Üzgünüz, sayfa bulunamadı") ?></h1>
					<div class="entry-content aligncenter"> 
						<img src="/images/warning.png" class="alignleft" />
						<p>Ulaşmaya çalıştığınız sayfa şuan mevcut değil veya geçici olarak erişime engellenmiş.</p>
						<p>Eğer bu sayfaya site içerisindeki bir bağlantıya tıkayarak ulaştıysanız lütfen site yöneticisi ile iletişime geçin</p>
					</div>
					<div class="entry-content clearboth">
						<script type="text/javascript">
							var GOOG_FIXURL_LANG = 'tr';
							var GOOG_FIXURL_SITE = '<?php echo $Ayarlar->Genel->SiteAdresi; ?>'
						</script>
						<script type="text/javascript"
							src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js">
						</script>
					</div>
				</div>
			</div>
			<div class="sidebar">
				<div class="box">
					<?php
						bilesen_kategoriler();
					?>
				</div>
			</div>
		</div>
	</div>
<!--end wrapper-->
</div>
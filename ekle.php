<?php
if (!giris())
	header("location: /kayit-ol?r:%2Fekle");

add_css("css/jquery.tagsinput.css");
add_js("js/fileuploader.js");
add_js("js/jquery.autocomplete.pack.js");
add_js("js/jquery.tagsinput.js");
set_title("{$Ayarlar->Genel->SiteAdi} - ". _r("Duvarkağıdı ekle"));

$uniqid = uniqid();
?>
<div class="wrapper" id='wrapper_main'>
<div class="center">
	<div id="feature_info">
	<h2></h2>
	<!-- end feature_info-->
	</div>		
	
	<div id="main">
			
	<div class='content'>
			
		<div class="entry">
		  
		   <h1 class='siteheading'><?php _e("Duvarkağıdı ekle"); ?></h1>
					   
		   <div class="entry-content">
		   <p>Aşağıdaki kutucukları eksiksiz doldurarak siteye duvarkağıdı gönderebilirsiniz. Gönderdiğiniz resimler editörler tarafından onaylandıktan sonra tüm kullanıcılar tarafından görülebilecektir.</p>
		   
		   <form action="#" method="post" class="ajax_form">
				<fieldset>
					<h3><span><?php _e("Resim ekleme formu") ?></span></h3>
					<div id="formstatus" class="rounded ajax_response"></div>
					
					<label for="title"><?php _e("Resim başlığı"); ?>*</label>
					<p id="titlebox"><input name="title" class="text_input is_empty" type="text" id="title" size="50" value='' /></p>
					
					<label for="tags"><?php printf("%s (%s)*", _r("Resim etiketleri"), _r("Virgül ile ayırarak")); ?></label>
					<p id="tageditor"><input name="tags" class="text_input is_empty" type="text" id="tags" /></p>
					
					<label><?php printf("%s (%s)*", _r("Resimler"), _r("Birden fazla seçilebilir")); ?></label>
					<p id="uploadbox" class="uploader"></p>
					
					<label for="message" class="blocklabel"><?php _e("Resim Yazısı"); ?></label>
					<p><textarea name="content" class="text_area" rows="4" id="content" ></textarea></p>
					
					<p>
						<input type="hidden" name="uniqid" value="<?php echo $uniqid; ?>" />
						<input name="send" type="submit" value="<?php _e("Gönder"); ?>" class="button" id="send" size="16"/>
					</p>
				</fieldset>
					
			</form>
			<script type="text/javascript">
				var uploader;
				jQuery(function() {
					jQuery('#tags').tagsInput({    
						autocomplete_url : 'ajax/etiket-tamamlama.php',
						autocomplete : {
							selectFirst : false,
							width : '300px',
							autoFill : false
						},
						defaultText : " "
					});
					
					jQuery('.ajax_form').kriesi_ajax_form({
						sendPath: 'ajax/galeri-ekle.php',
						responseContainer : '#formstatus'
					});
					
					uploader = new qq.FileUploader({
						element: jQuery('#uploadbox').get(0),
						action: 'ajax/resim-yukle.php',
						debug: true,
						params: { 
							"r" : "<?php echo $uniqid; ?>"
						},
						sizeLimit: 4194304,   
						minSizeLimit: 102400, 
						template: '<div class="qq-uploader">' + 
									'<div class="qq-upload-drop-area"><span><?php _e("Dosyaları buraya bırakın"); ?></span></div>' +
									'<div class="qq-upload-button"><?php _e("Resim seç"); ?></div>' +
									'<ul class="qq-upload-list"></ul>' + 
								  '</div>',
						fileTemplate: '<li>' +
										'<span class="qq-upload-file"></span>' +
										'<span class="qq-upload-spinner"></span>' +
										'<span class="qq-upload-size"></span>' +
										'<a class="qq-upload-cancel" href="#"><?php _e("İptal"); ?></a>' +
										'<span class="qq-upload-failed-text"><?php _e("Başarısız"); ?></span>' +
									  '</li>',
						messages: {
							typeError: "<strong>{file}</strong> bir resim dosyası ({extensions}) değil.",
							sizeError: "<strong>{file}</strong> dosyasının boyutu çok büyük, izin verilen en büyük boyut: {sizeLimit}.",
							minSizeError: "<strong>{file}</strong> dosyasının boyutu çok küçük, dosyanın boyutu en az {minSizeLimit} olmalıdır.",
							emptyError: "<strong>{file}</strong> boş bir dosya, lütfen dosyayı kontrol edin.",
							onLeave: "Karşıya yüklenen dosyaları mevcut, eğer şimdi ayrılırsanız yükleme iptal edilecek."            
						},
						allowedExtensions: ['<?php echo join("', '", $Ayarlar->Resim->Uzantilar); ?>'],
						showMessage : function(message) {
							jQuery('#formstatus').html(message).slideDown('fast');
							setTimeout(function(){ jQuery('#formstatus').slideUp('fast'); }, 5000);
						},
						onComplete : function(id, fileName, json){
							
						}
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
		
		<div class="box_small box widget"><h3 class="widgettitle"><?php _e("İpuçları") ?></h3>		
			<div class="entry box_entry">
				<h4><?php _e("Yüksek çözünürlüklü resimler iyidir"); ?></h4>
				
				<img title="" alt="" src="images/resolution.png" class="rounded alignleft noborder"/>
				<p><?php _e("Resminizin çözünürlüğü ne kadar büyük olursa o kadar çok türevi oluşturulur"); ?></p>
			</div>
			
			<div class="entry box_entry">
				<h4><?php _e("Resimlerde konu bütünlüğü olsun"); ?></h4>
				
				<img title="" alt="" src="images/subject.png" class="rounded alignleft noborder"/>
				<p><?php _e("Birden fazla resim olan galeride, resimler ortak bir konuya sahip olmalıdır"); ?></p>
			</div>
			
			<div class="entry box_entry">
				<h4><?php _e("Etiketleri özenle seç"); ?></h4>
				
				<img title="" alt="" src="images/tag-green.png" class="rounded alignleft noborder"/>
				<p><?php _e("Resimlerinizin tam hedefe ulaşabilmesi için resmi anlatan anahtar kelimeler iyi seçilmelidir"); ?></p>
			</div>
			<!--end box-->
		</div>
		
		<div class="box">
			<?php
				bilesen_kategoriler();
			?>
		</div>
			
		<!-- end sidebar-->	
	</div>
	
	<!--end main-->
	</div>

<!-- end center-->
</div>
<!--end wrapper-->
</div>
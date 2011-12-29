<?php
add_js("js/jquery.autocomplete.pack.js");
add_js("js/jquery.tagsinput.js");
add_js("js/jquery.selectBox.min.js");
add_css("css/jquery.autocomplete.css");
add_css("css/jquery.selectBox.css");
add_js("http://platform.twitter.com/widgets.js");

$galeri_id = get("i");
$galeri = GaleriIslemleri::Al($galeri_id);
puanla("goruntuleme", $galeri_id);

// Galeri bilgileri
$yorum_sayisi = count($galeri->Yorumlar());
$ekleyen = $galeri->Ekleyen();
$eklenme_gun = tarih_bicimlendir("%d", $galeri->EklenmeTarihi);
$eklenme_ay = tarih_bicimlendir("%b", $galeri->EklenmeTarihi);
$baslik = $galeri->Baslik;
$yazi = $galeri->Yazi;
$puan = $galeri->Puan;
$etiketler = $galeri->Etiketler();

set_title("{$Ayarlar->Genel->SiteAdi} - {$baslik}");
?>
<script type="text/javascript">
jQuery(function(){
	var indirme_oneki = "";
	function indirme_baglantilari(el) {
		var cozunurlukler = el.data("resolutions"),
			$dl = jQuery('.download-section').empty(),
			$liste, cozunurluk;
		
		for (grup in cozunurlukler) {
			$dl.append("<h4>"+grup+"</h4>");
			$liste = jQuery("<ul></ul>");
			$dl.append($liste);
			
			for (i in cozunurlukler[grup]) {
				cozunurluk = cozunurlukler[grup][i];
				
				$liste.append(jQuery("<li>"+
					"<a href='"+cozunurluk.Indirme+"' class='download-link'>"+
					"<span class='res'>"+cozunurluk.Genislik+"x"+cozunurluk.Yukseklik+"</span>"+
					"<span class='res_tag'>"+cozunurluk.Etiket+"</span></a></li>"));
			}
		}
	}
	
	jQuery(".multislide1").kriesi_block_slider(
	{
		slides: '.featured',					// wich element inside the container should serve as slide
			animationSpeed: 600,					// animation duration
			autorotation: true,						// autorotation true or false?
			autorotationSpeed:3,					// duration between autorotation switch in Seconds
			slideControlls: 'items',				// which controlls should the be displayed for the user: none, items
			appendControlls: '.feature_wrap1',		//element to append the controlls to
			showText: true,							// wether description text should be shown or not
			transition: 'fade',						//slide or fade	
			betweenBlockDelay:13,				// delay between each block change
			display: 'topleft', 					// showing up blocks: random, topleft, bottomright, diagonaltop, diagonalbottom, all
			blockSize: {height:'full', width:'full'},	//heigth and width of the blocks in number or the word 'full'
			
			onChange: function(current, next) {
				//indirme_baglantilari(next);
			}
	});
	
	jQuery('#tags').tagsInput({    
		autocomplete_url : 'ajax/etiket-tamamlama.php',
		autocomplete : {
			selectFirst : false,
			autoFill : false
		},
		width : '600px',
		defaultText : "<?php _e("etiket ekle") ?>",
		taglink : "/tum?etiket:{tag}",
		onChange: function(csv) {
			if (jQuery('#tags').val() != "<?php echo implode(",", $etiketler); ?>")
				jQuery('#update-link').show();
			else
				jQuery('#update-link').hide();
		}
	});
	
	jQuery('#update-link').hide().click(function(){
		etiketler = jQuery('#tags').val();
		if (etiketler == "") {
			jQuery('#update-link').hide();
			return false;
		}
		jQuery(this).text("<?php echo _e("Bekleyin ..."); ?>");
		jQuery.post("ajax/etiket-guncelle.php", {
			g: <?php echo $galeri_id; ?>,
			e: etiketler
		},
		function(response) {
			jQuery('#update-link').text("<?php echo _e("Güncelle"); ?>").hide();
		});
		
		return false;
	});
	
	jQuery("#commentform").kriesi_ajax_form({
		sendPath: 'ajax/yorum-ekle.php',
		responseContainer : '.new:last',
		onSubmit : function() {
			jQuery('.commentlist').append("<li class='comment new'></li>");
		},
		onComplete: function(response) {
			return true;
		}
	});
	
	jQuery('.resolution-list').selectBox({
		menuTransition: "slide",
		menuSpeed: "fast"
	});
	
	jQuery('.download-column a').click(function(){
		url = jQuery(this).parents("tr").find("select.resolution-list").val();
		location.href = url;
		return false;
	});
});
</script>
<div class="wrapper" id='wrapper_main'>
<div class="center">
	<!--	
	<div id="feature_info">
	<h2>Put a special headline here if you want to have one, or leave empty in case you don't :)</h2>
	
	</div>	
	-->
	
	<div id="main">
			
	<div class='content'>
	
		<div class="entry">
		  
		   <!-- ###################################################################### -->
			<div class="feature_wrap feature_wrap1">
			<!-- ###################################################################### -->
				<div class='featured_inside medium_sized_slider multislide1'>
				<?php
					$resim_i = 1;
					foreach ($galeri->Resimler() as $resim) {
						if ($resim_i > 10)
							break;
				?>
						<div class="featured featured<?php echo $resim_i; ?>">
							<a href="<?php echo indirme_link($resim, $baslik, new Cozunurluk("800x600"), true) ?>?p" rel="lightbox<?php echo $resim_i; ?>">
								<img src="<?php echo $resim->Onizleme; ?>" alt="<?php echo $baslik, " - ", sprintf(_r("Resim %d"), $resim_i); ?>" />
							</a>
						</div><!-- end .featured -->
				<?php
						$resim_i++;
					}
				?>
				</div>						
			<!-- ###################################################################### -->
			</div><!-- end featuredwrap -->
			<!-- ###################################################################### -->
		    <span class="date">
				<span class='date_day'><?php echo $eklenme_gun; ?></span>
				<span class='date_month'><?php echo $eklenme_ay; ?></span>
		    </span>
		   
			<div class="entry-head bloghead">
				<h1 class='siteheading'><a href="#"><?php echo $baslik; ?></a></h1>
				<span class="author"><?php echo sprintf(_r("<a href=\"%s\">%s</a> tarafından eklendi"), kullanici_link($ekleyen, true), $ekleyen); ?></span>
				<span class="comments"><a href="#yorumlar"><?php echo $yorum_sayisi == 0 ? _r("Yorum yok") : sprintf(_r("%d yorum"), $yorum_sayisi); ?></a></span>
				<span class='rating_label'><?php _e("Puan"); ?>:</span>
				<?php 
					yildizlari_yazdir($puan, $galeri_id);
				?>
			</div>
		   
			<div class="entry-content">
				<?php echo $yazi; ?>
				<!-- Sosyal Entegrasyon -->
				<p>
					<a href="http://twitter.com/share" class="twitter-share-button" 
						data-count="horizontal" data-via="arkaplanresmi" data-related="sinanyil:Site owner">Tweet</a>
					<iframe src="http://www.facebook.com/plugins/like.php?app_id=213235068717480&href&send=false&layout=button_count&width=450&show_faces=false&action=like&colorscheme=light&font=trebuchet+ms&height=21" 
						scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:21px;" allowTransparency="true"></iframe>
				</p>
				<!------------------------>
				<input name="tags" class="text_input" type="text" id="tags" value="<?php echo implode(",", $etiketler); ?>" />
				<a href="#" id="update-link"><?php _e("Güncelle"); ?></a>
				<div class=""></div>
				<table class="download-list" cellspacing="0">
					<tr>
						<th colspan="4"><?php _e("İNDİRMELER"); ?></th>
					</tr>
					<?php
						$resim_i = 1;
						foreach ($galeri->Resimler() as $resim) {
					?>
					<tr id="pic-<?php echo $resim_i; ?>">
						<th class="spec check-column"><input type="checkbox" /></th>
						<td class="thumbnail-column"><img src="<?php echo $resim->KucukResim; ?>" /></td>
						<td class="resolution-column">
							<select class="resolution-list">
								<?php
								foreach ($resim->Cozunurlukler as $key => $value) {
								?>
								<optgroup label="<?php echo $key; ?>">
									<?php
									for ($i = 0; $i < count($value); $i++) {
										$c = $value[$i];
										$label = $c;
										if ($c->Etiket != "")
											$label .= " ({$c->Etiket})";
											
										$selected = "";
										if (Cozunurluk::Karsilastir($c, $coz) == 0)
											$selected = "selected='selected'";
									?>
										<option value="<?php indirme_link($resim, $baslik, $c); ?>" 
											<?php echo $selected; ?>><?php echo $label; ?></option>
									<?php
									}
									?>
								</optgroup>
								<?php
								}
								?>
							</select>
							<div class="clearboth"></div>
							<p>Toplam indirilme: <strong><?php echo indirme_sayisi($resim->ID); ?></strong></p>
						</td>
						<td class="download-column">
							<a href="#">İndir</a>
						</td>
					</tr>
					<?php
							$resim_i++;
						}
					?>
					<tr>
						<th class="spec" colspan="4">
							
						</th>
					</tr>
				</table>
			<!--end entry-content-->
			</div>
		<!--end entry -->	
		</div>
					
		<div class="entry commententries">

			<h4 id="yorumlar" class="heading"><?php echo $yorum_sayisi == 0 ? _r("İlk yorum yazan siz olun!") : sprintf(_r("Bu galeriye %d cevap yazılmış!"), $yorum_sayisi); ?></h4>
		
			<ol class="commentlist">
			<?php 
				foreach ($galeri->Yorumlar() as $yorum) {
					$yazan = $yorum->Yazan();
					$yazan_adi = $yazan->Isim;
					$yazan_link = kullanici_link($yazan_adi, true);
					$yazan_resim = get_gravatar($yazan->Eposta, 60, "identicon");
					
					if ($yazan instanceof Kullanici) {
						$yazan_link = "<a class=\"url\" href=\"{$yazan_link}\">{$yazan_adi}</a>";
					}
					else {
						$yazan_link = "<a class=\"url\" rel=\"external, nofollow\" href=\"{$yazan->Adres}\">{$yazan_adi}</a>";
					}	
					
					$yorum_tarih = tarih_bicimlendir(_r(YORUM_TARIH), $yorum->YazimTarihi);
					$gecen_zaman = zaman_araligi(tarih_bicimlendir(MYSQL_TARIHSAAT, $yorum->YazimTarihi));
					$yorum_metin = $yorum->Yazi;
			?>
				<li class="comment"><!--one comment-->
					<div>
						<div class="gravatar">
							<img class="avatar avatar-60 photo" src="<?php echo $yazan_resim; ?>" alt="" />
						</div>

						<div class="comment_content">
							<a href='#commentform' class='comment-reply-link'><?php _e("Cevapla"); ?></a>
							<cite class="author_name heading">
								<?php echo $yazan_link; ?>
							</cite> 
							<span class="says"><?php _e("diyor"); ?>:</span> 
							<div class="comment-meta commentmetadata">
								<a href="#"><?php echo "{$gecen_zaman} ({$yorum_tarih})"; ?></a>
							</div>
							<div class="comment_text">
								<?php echo $yorum_metin; ?>
							</div>
						</div>
					<!--comment end-->    
					</div>
				</li>
			<?php } ?>
			</ol>
			
			<div id="respond">
				<?php if ($yorum_sayisi != 0): ?>
				<h3 id='reply_headding'><?php _e("Yorum yazın") ?></h3>
				<?php endif; ?>
				
				<form id="commentform" method="post" action="#">
					<div class="personal_data">
						<?php if (giris()): ?>
						<p><?php echo sprintf("<strong>%s</strong> diyor ki:", $Kullanici); ?></p>
						<?php else: ?>
						<p><label for="author"><small><?php printf("%s (%s)", _r("İsim"), _r("gerekli")); ?></small></label><input type="text" tabindex="1" size="22" value="<?php _e("İsim") ?>" id="author" class="text_input is_empty" name="author"/>
						</p>
						
						<p><label for="email"><small><?php printf("%s (%s, %s)", _r("E-posta"), _r("gizli tutulacak"), _r("gerekli")); ?></small></label><input type="text" tabindex="2" size="22" value="<?php _e("E-Posta adresi") ?>" id="email" class="text_input is_email" name="email"/>
						</p>
						
						<p><label for="url"><small><?php _e("Website"); ?></small></label><input type="text" tabindex="3" size="22" value="<?php _e("Website") ?>" id="url" class="text_input" name="url"/>
						</p>
						<?php endif; ?>
					</div>
					<div class="message_data">
						<input type="hidden" id="gallery" name="gallery" value="<?php echo $galeri_id; ?>" />
						<p><textarea tabindex="4" class="text_area is_empty" id="comment" name="comment"></textarea></p>
					</div>
					<p><input type="submit" value="<?php _e("Yorumla"); ?>" tabindex="5" id="submit" class="button" name="submit"/>
					</p>
				</form>
			</div>
		<!-- end commententry -->
		</div>
	<!--end content -->
		</div>
	
		<div class="sidebar">
		
			<div class="box">
				<?php
					bilesen_kategoriler();
				?>
			</div>
			
			<div class="box_small box widget releated_galleries">
				<h3 class="widgettitle"><?php _e("Benzer galeriler"); ?></h3>	
				<?php
					$benzerler = GaleriIslemleri::BenzerGaleriler($etiketler);
					
					foreach ($benzerler as $oge) {
						if ($oge->ID == $galeri_id)
							continue;
						
						$resim = $oge->RastgeleResim();
				?>
				<div class="entry box_entry">
					<h4><a href="<?php galeri_link($oge); ?>"><?php echo $oge->Baslik; ?></a></h4>
					
					<a href="<?php galeri_link($oge); ?>">
						<img title="<?php echo $oge->Baslik; ?>" alt="<?php echo $oge->Baslik; ?>" src="<?php echo $resim->KucukResim; ?>" class="rounded alignleft image-60h"/>
					</a>
					<?php echo $oge->Yazi; ?>
					<p class="clearboth"></p>
				</div>
				<?php 
					}
				?>
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

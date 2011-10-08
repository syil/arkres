<?php
set_title("{$Ayarlar->Genel->SiteAdi} - {$Ayarlar->Genel->SiteSlogan}");
add_head("<meta name=\"description\" content=\"{$Ayarlar->Genel->SiteAciklamasi}\" />");
add_head("<meta name=\"keywords\" content=\"{$Ayarlar->Genel->AnahtarKelimeler}\" />");
?>
<div class="wrapper" id='wrapper_main'>
<div class="center">
	<div id="feature_info">
		
	</div>	
	
	<div id="main">
		<div class="content the_gallery">
			<div class="entry">
				<div class="feature_wrap" id="seckinler">
					<div class='featured_inside medium_sized_slider' id="seckin_galeriler">
					<?php
						$seckinler = array_slice(GaleriIslemleri::SeckinGalerileriAl(), 0, 5);
						
						$resim_i = 1;
						foreach ($seckinler as $galeri) {
							$galeri_resimleri = $galeri->Resimler();
							$rastgele_resim = $galeri_resimleri[array_rand($galeri_resimleri)];
					?>
							<div class="featured featured<?php echo $resim_i; ?>">
								<a href="<?php galeri_link($galeri); ?>">
									<img src="<?php echo $rastgele_resim->Onizleme; ?>" alt="<?php echo $rastgele_resim->Dosya; ?>" />
								</a>
								<div class='gallery_excerpt'>
									<h3><?php echo $galeri->Baslik; ?></h3>
									<!--<p>
										<?php 
											$yazi = substr($galeri->Yazi, 0, stripos($galeri->Yazi, "</p>"));
											$yazi = strip_tags($yazi);
											
											echo $yazi;
										?>
									</p>-->
								</div>
							</div><!-- end .featured -->
					<?php
						$resim_i++;
						}
					?>
					</div>						
				</div><!-- end featuredwrap -->
				<script type="text/javascript">
					jQuery("#seckin_galeriler").kriesi_block_slider(
					{
						slides: '.featured',					// wich element inside the container should serve as slide
						animationSpeed: 600,					// animation duration
						autorotation: true,						// autorotation true or false?
						autorotationSpeed:5,					// duration between autorotation switch in Seconds
						slideControlls: 'items',				// which controlls should the be displayed for the user: none, items
						appendControlls: '#seckinler',		//element to append the controlls to
						showText: true,							// wether description text should be shown or not
						transition: 'slide',						//slide or fade	
						betweenBlockDelay:13,				// delay between each block change
						display: 'all', 					// showing up blocks: random, topleft, bottomright, diagonaltop, diagonalbottom, all
						blockSize: {height:'full', width:'full'}, 	//heigth and width of the blocks in number or the word 'full'	
					});
				</script>
			</div>
		
			<h2><?php _e("En yeniler"); ?></h2>
			<?php 
				$en_yeniler = array_slice(GaleriIslemleri::GalerileriAl(NULL, "eklenme_tarihi DESC"), 0, 6);
				$en_yeniler_sayi = count($en_yeniler);
				
				for ($i = 0; $i < $en_yeniler_sayi; $i++) {
					$bas = $i % 3 == 0;
					$son = $i % 3 == 2;
					if ($bas)
						echo "<div class=\"entry\">";
					
					galeri_ogesi($en_yeniler[$i], $son);
			
					if ($son || $i == ($en_yeniler_sayi - 1))
						echo "</div><!--end entry -->";
				}
			?>
			<h2><?php _e("Haftanın popülerleri"); ?></h2>
			<?php 
				$populerler = array_slice(GaleriIslemleri::PopulerGalerileriAl(), 0, 6);
				$populerler_sayi = count($populerler);
			
				for ($i = 0; $i < $populerler_sayi; $i++) {
					$bas = $i % 3 == 0;
					$son = $i % 3 == 2;
					if ($bas)
						echo "<div class=\"entry\">";
					
					galeri_ogesi($populerler[$i], $son);
			
					if ($son || $i == ($populerler_sayi - 1))
						echo "</div><!--end entry -->";
				}
			?>
		<!--end content -->
		</div>
	
	
		<div class="sidebar">
			<div class="box widget">
				<?php
					$bilesenler = array(
						"bilesen_gorunum_secenekleri", 
						"bilesen_kategoriler", 
						"bilesen_cozunurluk_secici");
						
					foreach ($bilesenler as $fonk)
						call_user_func($fonk);
				?>
			</div>
			
			<div class="box_small box widget community_news">
				<?php bilesen_son_yorumlar(); ?>
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
<?php
function get_head() {
	global $head;
	$ret = "";
	foreach ($head as $group) {
		foreach ($group as $key => $value)
			$ret .= $value."\n";
	}
	return $ret;
}

function add_css($href, $media = NULL) {
	global $head;
	$head["css"][] = "<link href=\"{$href}\" rel=\"stylesheet\" type=\"text/css\" />";
}

function add_js($src) {
	global $head;
	$head["js"][] = "<script type=\"text/javascript\" src=\"{$src}\"></script>";
}

function set_title($title) {
	global $head;
	$head["title"][0] = "<title>{$title}</title>";
}

function add_meta($name, $content) {
	global $head;
	$head["meta"][] = "<meta name=\"{$name}\" content=\"{$content}\"/>";
}

function add_head($tag) {
	global $head;
	$head["other"][] = $tag;
}

function get_menu($current = "anasayfa") {
	$isaretle = " class='current'";
?>
	<ul id="nav">
		<li<?php if($current == "anasayfa")	echo $isaretle ?> id='home'><a href="/"><?php _e("Anasayfa"); ?></a></li>
		<li<?php if($current == "tum") 		echo $isaretle ?>><a href="/tum"><?php _e("Tüm resimler"); ?></a></li>
		<li<?php if($current == "ekle") 	echo $isaretle ?>><a href="/ekle" id="ekle_menusu"><?php _e("Resim Ekle"); ?></a></li>
		<?php if (!giris()): ?>
			<li<?php if($current == "kayit-ol") echo $isaretle ?>><a href="/kayit-ol"><?php _e("Kayıt Ol"); ?></a></li>
		<?php endif; ?>
		<li<?php if($current == "program") 	echo $isaretle ?>><a href="/program"><?php _e("Arkres"); ?></a></li>
		<li<?php if($current == "yardim") 	echo $isaretle ?>><a href="/yardim"><?php _e("Yardım"); ?></a></li>
	</ul>
<?php
}	

function get_head_extras()
{
	global $Kullanici;
?>
	<div id="headextras" class='rounded'>
	<?php if (giris()): ?>
		<div id="loginstatus">
			<span id="loginname"><?php echo $Kullanici; ?></span>
			<a href="/hesabim" class="rounded"><?php _e("Hesap Ayarları"); ?></a>
			<a href="/tum?benim" class="rounded"><?php _e("Resimlerim"); ?></a>
			<a href="#" class="rounded" id="logout"><?php _e("Çıkış"); ?></a>
			<form action="/" method="post" class="hidden" id="logoutform">
				<input type="hidden" value="1" name="cikis" />
				<input type="submit" />
			</form>
		</div>
	<?php else: ?>
		<form action="" id="loginform" method="post">
			<div>
			<input type="submit" value="." id="loginsubmit" class="button ie6fix" />
			<input type="text" class='rounded' id="u" name="u" value="" />
			<input type="password" class='rounded' id="p" name="p" value="" />
			</div>
		</form><!-- end loginform-->
	<?php endif; ?>
		<form action="tum" id="searchform" method="get">
			<div>
			<input type="hidden" name="a" value="1" />
			<input type="submit" value="." id="searchsubmit" class="button ie6fix" />
			<input type="text" class='rounded' id="s" name="ara" value="<?php echo get("ara"); ?>" />
			</div>
		</form><!-- end searchform-->
		
		<ul class="social_bookmarks">
			<li class='email'><a class='ie6fix' href="#"><?php _e("E-posta"); ?></a></li>
			<li class='rss'><a class='ie6fix' href="http://feeds.feedburner.com/arkres">RSS</a></li>
			<li class='twitter'><a class='ie6fix' href="http://twitter.com/arkaplanresmi">Twitter</a></li>
		</ul><!-- end social_bookmarks-->

	<!-- end headextras: --> 
	</div>
<?php
}

function galeri_ogesi(&$galeri, $son = false)
{
	global $Ayarlar, $coz;
	$galeri_resimleri = $galeri->Resimler();
	$rastgele_resim = $galeri->RastgeleResim();
?>
	<div class="gallery_entry <?php if ($son) echo "last"; ?>" id="entry-<?php echo $galeri->ID; ?>">
		<div class="gallery_inner">
			<a class='preloading gallery_image' href="<?php galeri_link($galeri); ?>">
				<span class="number_of_picture"><?php echo sprintf(_r("%d resim"), count($galeri_resimleri)); ?></span>
				<img src="<?php echo $rastgele_resim->KucukResim; ?>" alt="<?php $galeri->Baslik; ?> small" class='item_small' />
				<img src="<?php echo $rastgele_resim->Onizleme; ?>" alt="<?php $galeri->Baslik; ?> thumbnail" class='item_big no_preload' />
			</a>
			<a class='comment_link' href='<?php galeri_link($galeri); ?>#yorumlar'><?php echo count($galeri->Yorumlar()); ?></a>
			<a class='download_link' href='<?php indirme_link($rastgele_resim, $galeri->Baslik, $coz); ?>' title="<?php _e("İndir"); ?>">&nbsp;</a>
			<?php
				yildizlari_yazdir($galeri->Puan, $galeri->ID);
			?>
			<div class='gallery_excerpt'>
				<?php 
					$yazi = substr($galeri->Yazi, 0, stripos($galeri->Yazi, "</p>"));
					$yazi = strip_tags($yazi);
					
					echo $yazi;
				?>
			</div>
		</div>
	<h3><a href='<?php echo galeri_link($galeri); ?>'><?php echo $galeri->Baslik; ?></a></h3>
	</div>
<?php
}

function yildizlari_yazdir($yildiz, $id)
{
	global $Ayarlar;
	
	$kalan_puan = $Ayarlar->Diger->YildizSayisi;
	$yildiz = round($yildiz);
	echo "<div class='post-ratings' id='rate-{$id}'>";
	for (; $yildiz != 0; $kalan_puan--, $yildiz--) {
		echo "<span class='star rating_on'></span>";
	}
	for (; $kalan_puan != 0; $kalan_puan--) {
		echo "<span class='star rating_off'></span>";
	}
	echo "</div>";
}

function sayfalama($suan, $toplam)
{
	global $Sayfa;
	if ($Sayfa == "anasayfa")
		$sayfa = "";
	else
		$sayfa = $Sayfa;
	
	if ($toplam == 1) {
		echo "<div class='pagination'></div>";
		return;
	}
	
	$qs = "";
	foreach ($_GET as $k => $v) {
		if (is_int($k) || $k == "sayfa")
			continue;
		if (!empty($v))
			$qs .= "&{$k}:{$v}";
		else
			$qs .= "&{$k}";
	}
	if (!empty($qs)) {
		$qs[0] = "?";
		$qs .= "&sayfa:";
	}
	else {
		$qs = "?sayfa:";
	}
	echo "<div class='pagination'>";
	for ($i = 1; $i <= $toplam; $i++) {
		if ($i == $suan)
			echo "<span class='current'>{$i}</span>";
		else
			echo "<a href='{$sayfa}{$qs}{$i}' class='inactive'>{$i}</a>";
	}
	echo "</div>";
}

function get_gtm_body() {
	global $Ayarlar;
	
	if (!isset($Ayarlar->GoogleTagManager) || 
	    !$Ayarlar->GoogleTagManager->Aktif || 
	    empty($Ayarlar->GoogleTagManager->ContainerID)) {
		return "";
	}
	
	$containerID = htmlspecialchars($Ayarlar->GoogleTagManager->ContainerID);
	
	return <<<GTM
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$containerID}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

GTM;
}

?>
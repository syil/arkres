<?php 
chdir("..");
include "ic/sistem.php";

$author = post("author");
$email = post("email");
$url = post("url");
$comment = post("comment");
$gallery = post("gallery");
$ajax = post("ajax") or $ajax = false;

try {
	$yeni = new Yorum;
	$galeri = GaleriIslemleri::Al($gallery);

	if (!giris()) {
		if (empty($author) || $author == _r("İsim"))
			throw new Exception(_r("Adınızı belirtmelisin"));
		
		if (!preg_match("/^\w+@\w+\.[\w|\.]{2,6}$/", $email))
			throw new Exception(_r("E-posta adresi geçersiz"));
		
		if (empty($comment))
			throw new Exception(_r("Adınızı belirtmelisin"));
		
		if ($url == _r("Website"))
			$url = "";
		
		$yeni->Yazan->Isim = $author;
		$yeni->Yazan->Eposta = $email;
		$yeni->Yazan->Adres = $url;
	}
	else {
		$yeni->YazanID = $Kullanici->ID;
	}
	
	$yeni->Yazi = $comment;
	//$yeni->YorumID
	
	$yeni = $galeri->Islemler()->YorumYaz($yeni);
	
	if ($ajax) {
		$yazan = $yeni->Yazan();
		$yazan_adi = $yazan->Isim;
		$yazan_resim = get_gravatar($yazan->Eposta, 60, "identicon");
		
		if ($yazan instanceof Kullanici) {
			$yazan_link = "<a class=\"url\" href=\"?kullanici:{$yazan_adi}\">{$yazan_adi}</a>";
		}
		else {
			$yazan_link = "<a class=\"url\" rel=\"external, nofollow\" href=\"{$yazan->Adres}\">{$yazan_adi}</a>";
		}
		
		$yorum_tarih = tarih_bicimlendir(_r(YORUM_TARIH), $yeni->YazimTarihi);
		$gecen_zaman = zaman_araligi(tarih_bicimlendir(MYSQL_TARIHSAAT, $yeni->YazimTarihi));
		$yorum_metin = $yeni->Yazi;
			
?>
		<div>
			<div class="gravatar">
				<img width="60" height="60" class="avatar avatar-60 photo" src="<?php echo $yazan_resim; ?>" alt="" />
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
<?php
	}
}	
catch (Exception $exc) {
	echo "<div class='ajax_response' style='display:block;'><p class='error'>{$exc->getMessage()}</p></div>";
}
?>
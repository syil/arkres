<?php
	$Ayarlar = json_decode(file_get_contents("veri/ayarlar.json"));
	$cookie = $Ayarlar->Kullanici->CozunurlukCerezi;
?>
<script type="text/javascript">
var expire = new Date((new Date()).getTime() + 24 * 3600000);
expire = "; expires=" + expire.toGMTString();

document.cookie = "<?php echo $cookie; ?>=" + escape(screen.width + "x" + screen.height) + expire;
location.reload();
</script>
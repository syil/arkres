<?php 
require_once "ic/sistem.php";

$coz_query = "";

$cozunurluk = get("coz");
if ($cozunurluk === false) {
	if (isset($coz)) {
		$cozunurluk = $coz;
	}
}
else {
	$cozunurluk = new Cozunurluk($cozunurluk);
}

$coz_query = "?coz:".$cozunurluk;
$rss_url = $Ayarlar->Genel->SiteAdresi ."/resim-rss". $coz_query;
$dosya_ismi = "arkres-theme.themepack";

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename={$dosya_ismi}");
?>
; Copyright © Microsoft Corp.

[Theme]
; Windows 7 - IDS_THEME_DISPLAYNAME_AERO
DisplayName=ArkaplanResmi Dinamik

; Computer - SHIDI_SERVER
[CLSID\{20D04FE0-3AEA-1069-A2D8-08002B30309D}\DefaultIcon]
DefaultValue=%SystemRoot%\System32\imageres.dll,-109

; UsersFiles - SHIDI_USERFILES
[CLSID\{59031A47-3F72-44A7-89C5-5595FE6B30EE}\DefaultIcon]
DefaultValue=%SystemRoot%\System32\imageres.dll,-123

; Network - SHIDI_MYNETWORK
[CLSID\{F02C1A0D-BE21-4350-88B0-7367FC96EF3C}\DefaultIcon]
DefaultValue=%SystemRoot%\System32\imageres.dll,-25

; Recycle Bin - SHIDI_RECYCLERFULL SHIDI_RECYCLER
[CLSID\{645FF040-5081-101B-9F08-00AA002F954E}\DefaultIcon]
Full=%SystemRoot%\System32\imageres.dll,-54
Empty=%SystemRoot%\System32\imageres.dll,-55

[Control Panel\Cursors]
AppStarting=%SystemRoot%\cursors\aero_working.ani
Arrow=%SystemRoot%\cursors\aero_arrow.cur
Hand=%SystemRoot%\cursors\aero_link.cur
Help=%SystemRoot%\cursors\aero_helpsel.cur
No=%SystemRoot%\cursors\aero_unavail.cur
NWPen=%SystemRoot%\cursors\aero_pen.cur
SizeAll=%SystemRoot%\cursors\aero_move.cur
SizeNESW=%SystemRoot%\cursors\aero_nesw.cur
SizeNS=%SystemRoot%\cursors\aero_ns.cur
SizeNWSE=%SystemRoot%\cursors\aero_nwse.cur
SizeWE=%SystemRoot%\cursors\aero_ew.cur
UpArrow=%SystemRoot%\cursors\aero_up.cur
Wait=%SystemRoot%\cursors\aero_busy.ani
DefaultValue=Windows Aero

[Sounds]
; IDS_SCHEME_DEFAULT
SchemeName=Windows Default

[Control Panel\Desktop]
TileWallpaper=0
WallpaperStyle=10
Pattern=

[VisualStyles]
Path=%SystemRoot%\resources\themes\Aero\Aero.msstyles
ColorStyle=NormalColor
Size=NormalSize
ColorizationColor=0X45409EFE
Transparency=1
VisualStyleVersion=10
Composition=1

[MasterThemeSelector]
MTSM=DABJDKT

[Slideshow]
Interval=1800000
Shuffle=1
RSSFeed=<?php echo $rss_url; ?>
[boot]
SCRNSAVE.EXE=

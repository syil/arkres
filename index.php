<?php
include "ic/sistem.php";

$Sayfa = str_replace("?". $_SERVER["QUERY_STRING"], "", str_replace("/", "", $_SERVER["REQUEST_URI"]));
if (empty($Sayfa))
	$Sayfa = "anasayfa";
if (!file_exists($Sayfa.".php"))
	$Sayfa = "404";

ob_start("head_changer");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
{head}
</head>
<body id="top">
<?php echo get_gtm_body(); ?>
<div class="wrapper" id='wrapper_head'>
	<div class="center">
		<div id="head">
			<h1 class="logo"><a href="/" class='ie6fix'><?php echo $Ayarlar->Genel->SiteAdi; ?></a></h1>
			<?php get_menu($Sayfa); ?>
			<?php get_head_extras(); ?>
		<!--end head-->
		</div>
	<!--end center-->
	</div>
<!--end wrapper-->
</div>

<?php include $Sayfa.".php"; ?>

<div class="wrapper fullwidth" id='wrapper_footer'>

		<div class="center">
		<!--
		<div id="footer">
				
				<div class="one_third">
				
				<div class="box_small box widget community_news"><h3 class="widgettitle">Latest News</h3>		
				<div class="entry box_entry">
				<h4><a href="single.html">Apple rebrands to Banana!</a></h4>
				
				<a href="single.html"><img title="Apple rebrands to Banana!" alt="" src="files/mini_pic1.jpg" class="rounded alignleft"/></a><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
				</div>
			
				<div class="entry box_entry">
				<h4><a href="single.html">Hello world!</a></h4>
				
				<a href="single.html"><img title="Hello world!" alt="" src="files/mini_pic2.jpg" class="rounded alignleft"/></a><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex</p>
				</div>
			
			
			</div>
			
		</div>
			
			
		<div class="one_third">
		
		<div class="box_small box widget widget_categories" ><h3 class="widgettitle">Categories</h3>		
			<ul>
				<li><a href="index-2.html">Network News</a></li>
				<li><a href="blog.html">Web Design &amp; Development</a></li>
				<li><a href="single.html">Adobe Creative Suite 4</a></li>
				<li><a href="fullwidth.html">Dreamweaver CS4</a></li>
				<li><a href="page.html">jQuery - Write less do more</a></li>
				<li><a href="fullwidth.html">Mootools - A compact javascript framework</a></li>
			</ul>	
		</div>
		
		<div class="box_small box widget widget_archive" ><h3 class="widgettitle">Archives</h3>		
			<ul>
				<li><a title="March 2010" href="archive.html">March 2010</a></li>
				<li><a title="February 2010" href="archive.html">February 2010</a></li>
				<li><a title="January 2010" href="archive.html">January 2010</a></li>
			</ul>
		</div>
		</div>
		<div class="one_third last">		
		<div class="box widget">
				<h3>Contribute to our Site!</h3>
				<p>Consectetur adipisicing elit tempor incididunt ut labore. Sed do eiusmod tempor incididunt ut labore. Consectetur adipisicing elit.</p>
				<p class="small_block"><img alt="" src="images/skin1/injection.png" class="ie6fix noborder alignleft"/>If you want to contribute tutorials, news or other stuff please contact us.</p>
				<p class="small_block"><img alt="" src="images/skin1/tag-green.png" class="ie6fix noborder alignleft"/>Consectetur adipisicing elit. Sed do eiusmod tempor incididunt ut labore.</p>
				<p class="small_block"><img alt="" src="images/skin1/blueprintsticky.png" class="ie6fix noborder alignleft"/>This site uses valid HTML and CSS. All content Copyright &copy; 2010 Expose, Inc</p>
				<p class="small_block"><img alt="" src="images/skin1/rssorange.png" class="ie6fix noborder alignleft"/>If you like what we do, please don't hestitate and subscribe to our <a href="http://www.kriesi.at/demos/newscast/feed/">RSS Feed.</a></p>
			</div>
		</div>
		 -->
	</div>
</div>	
	
	
<!--end wrapper -->
</div>

<div id="footer_bottom" class="wrapper">

	<div class="center">
		<span class="copyright"><?php printf(_r("Site içerisindeki resimlerin hakkı, resmi gönderen kullanıcılara aittir")); ?></span>
		<a href="http://www.sinanyil.com/" class="scrollTop "><?php printf(_r("Tasarım ve yönetim: %s"), "Sinan Yıl"); ?></a>
	<!-- end center -->
	</div>

<!-- end footer -->
</div>
</body>
</html>
<?php 
	// file_put_contents("rapor.html", $vt->Rapor());
	$vt->Kapat();
	ob_end_flush(); 
?>

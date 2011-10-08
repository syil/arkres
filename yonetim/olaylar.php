<?php
chdir("..");
include "ic/sistem.php";

$vt->SorguAta("SELECT * FROM olaylar ORDER BY tarih DESC LIMIT 200");

$vt->SatirlariAl($Veri);
?>
<table style="font:12px 'Trebuchet MS'; width:100%;">
<tr>
	<th>tür</th>
	<th>tarih</th>
	<th>ek bilgi</th>
</tr>
<?php
foreach ($Veri as $v):
?>
<tr>
	<td><?php echo $v["tur"]; ?></td>
	<td><?php echo $v["tarih"]; ?></td>
	<?php
		$ek_bilgi = unserialize($v["ek_bilgi"]);
	?>
	<td>
		<strong>Galeri: </strong><?php echo $ek_bilgi["Galeri"]; ?><br />
		<strong>Çözünürlük (İndirilen - Kullanılan): </strong><?php echo $ek_bilgi["Çözünürlük"], " - ", $ek_bilgi["Kullanıcı Çözünürlüğü"]; ?><br />
		<strong>İndiren: </strong><?php echo $ek_bilgi["Kullanıcı"]; ?>
		<strong>İndirilen: </strong><?php echo $ek_bilgi["Resim ID"]; ?>
	</td>
</tr>
<?php
endforeach;
?>
</table>

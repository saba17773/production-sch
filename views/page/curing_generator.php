<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?php echo root; ?>/assets/css/theme.min.css" />
	<title>Generate Curing Barcode</title>
</head>
<body>
	<?php  
		$code = explode("@", $barcode);
	?>
	<table border="0" width="100%">
		<tr>
			<td width="100%" style="text-align: center;" valign="top">
				<br>
				<br>
				<br>
				<br>
				<br>
				<div style="font-size: 3em;">PRESS No. : <span style="font-size: 1.5em; font-weight: bold;"><?php echo $code[0]; ?><?php echo $code[1]; ?></span></div>
				<br>
				<br>
				<br>
				<br>
				<div style="font-size: 3em;">CURED TIRE CODE</div>
				<br>
				<br>
				<br>
				<div style="font-size: 3em;">
					<span style="font-size: 1.5em; font-weight: bold;">
						<?php echo $code[3]; ?>
					</span>
				</div>
				<br>
				<br>
				<br>
				<div style="font-size: 3em; ">MOLD No. : <span style="font-size: 1.5em; font-weight: bold;"><?php echo $code[2]; ?></span></div>
				<br>
				<br>
				<br>
				<br>
				<div>
					<?php 
						$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
						echo '<img width="200px" height="40" src="data:image/png;base64,' . base64_encode($generator->getBarcode($barcode, $generator::TYPE_CODE_128)) . '"><br />';
					?>
				</div>
				<br>
				<br>
				<br>
				<br>
			</td>
		</tr>
	</table>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf=new mPDF('th','A5', 0, 0, 0, 0, 0, 0);
$mpdf->WriteHTML($html);
$mpdf->Output();
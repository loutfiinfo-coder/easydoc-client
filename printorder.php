<?php
session_start();
include("config.php");
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();

ob_start();
$back = $bdd->query("SELECT * FROM payments WHERE id='".$_GET['id']."'");
$payment = $back->fetch();
$note = "";
if(trim(strip_tags($_GET['note'])) !== ""){
	$note = '<p><b>Note supplimentaire: </b>'.addslashes($_GET['note']).'</p>';
}
$req = $bdd->prepare("UPDATE payments SET note=CONCAT(note,'".$note."') WHERE id='".$payment['id']."'");
$req->execute();

$back = $bdd->query("SELECT * FROM facturesuppliers WHERE id='".$payment['doc']."'");
$facturesupplier = $back->fetch();
$back = $bdd->query("SELECT * FROM companies WHERE id='".$payment['company']."'");
$company = $back->fetch();
$back = $bdd->query("SELECT * FROM suppliers WHERE id='".$payment['supplier']."'");
$supplier = $back->fetch();
$back = $bdd->query("SELECT * FROM clients WHERE id='".$payment['client']."'");
$client = $back->fetch();
$back = $bdd->query("SELECT * FROM bankaccounts WHERE id='".$payment['rib']."'");
$bankaccount = $back->fetch();
$back = $bdd->query("SELECT * FROM suppliersbankaccounts WHERE id='".$payment['rib2']."'");
$bankaccountsupplier = $back->fetch();

$req = $bdd->prepare("UPDATE printoptions SET signature='".$_GET['signature']."',placement='".$_GET['placement']."',header='".$_GET['header']."',label='".$_GET['label']."'");
$req->execute();
?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<style>
			@page {
				<?php
				if(isset($_GET['header'])){
					if($_GET['header'] == "1"){
						?>
				margin:<?php echo $company['headerheight'];?>cm 0.5cm <?php echo $company['footerheight'];?>cm 0.5cm;
						<?php
					}
					elseif($_GET['header'] == "0"){
						?>
				margin:<?php echo $company['headerheight'];?>cm 0.5cm <?php echo $company['footerheight'];?>cm 0.5cm;
						<?php						
					}
				}
				else{
					?>
				margin:<?php echo $company['headerheight'];?>cm 0.5cm <?php echo $company['footerheight'];?>cm 0.5cm;
					<?php					
				}
				?>
				header: html_MyCustomHeader;
				footer: html_MyCustomFooter;
			}
		</style>
	</head>
	<body>
		<div style="padding:20px;font-family:'Times New Roman';">
			<htmlpageheader name="MyCustomHeader">
				<table style="width:100%;font-family:'Arial';">
					<tr>
						<td style="width:50%;padding:0px 15px;">
							<?php
							$height = $company['headerheight'] - 1.5;
							if(isset($_GET['header'])){
								if($_GET['header'] == "1"){
									?>
							<img src="uploads/<?php echo $company['logo1'];?>" style="height:<?php echo $height;?>cm;" />
									<?php
								}
							}
							else{
								?>
							<img src="uploads/<?php echo $company['logo1'];?>" style="height:<?php echo $height;?>cm;" />
								<?php					
							}
							?>
						</td>
					</tr>
				</table>
			</htmlpageheader>
			<div style="margin-left:350px;font-size:12px;font-family:'Times New Roman';text-align:right;">
				<p style="margin:5px 0px;"><?php echo $bankaccount['bank'];?></p>
				<p style="margin:5px 0px;"><?php echo $bankaccount['agency'];?></p>
				<p style="margin:5px 0px;"><?php echo $bankaccount['city'];?>, Le <?php echo date("d/m/Y",$payment['dateadd']);?></p>
			</div>
			<div style="font-size:12px;font-family:'Times New Roman';">
				<p><b>Objet:</b> <?php echo $_GET['type'];?></p>
				<p>Messieurs,</p>
				<p>Par le débit de notre compte n° <b><?php echo $bankaccount['rib'];?></b>, nous vous prions de bien vouloir procéder au virement en faveur de bénéficiaire ci-après:</p>
			</div>
			<?php
			$h = 0;
			$price = $payment['currency']!=""?$payment['price2']:$payment['price'];
			$maindiscount = 0;			
			$existdiscount = 0;
			?>
			<table style="margin-top:10px;border-collapse:collapse;width:100%;font-size:12px;font-family:'Times New Roman';text-align:center;">
				<tr>
					<td style="padding:10px 4px;border:1px solid #888888;text-align:left;font-weight:bold;background:#EEEEEE;">Bénéficiaire</td>
					<td style="padding:10px 4px;border:1px solid #888888;white-space:nowrap;text-align:left;"><?php echo $supplier['title'];?></td>
				</tr>
				<tr>
					<td style="padding:10px 4px;border:1px solid #888888;text-align:left;font-weight:bold;background:#EEEEEE;">Montant</td>
					<td style="padding:10px 4px;border:1px solid #888888;white-space:nowrap;text-align:left;">
						<?php echo number_format((float)$price,2,'.',' ')." ".($payment['currency']!=""?$payment['currency']:$settings['currency']);?><br />
						# <?php 
							$chiffre = chifre_en_lettre(number_format((float)($price), 2, '.', ''),$payment['currency']);
							echo $chiffre;
						?> #
					</td>
				</tr>
				<?php
				if(isset($facturesupplier['code'])){
					?>
				<tr>
					<td style="padding:10px 4px;border:1px solid #888888;text-align:left;font-weight:bold;background:#EEEEEE;">Facture N°</td>
					<td style="padding:10px 4px;border:1px solid #888888;white-space:nowrap;text-align:left;"><?php echo $facturesupplier['code'];?></td>
				</tr>
				<?php
				}
				if($payment['currency']==""){
					?>
				<tr>
					<td style="padding:10px 4px;border:1px solid #888888;text-align:left;font-weight:bold;background:#EEEEEE;">RIB</td>
					<td style="padding:10px 4px;border:1px solid #888888;white-space:nowrap;text-align:left;"><?php echo $bankaccountsupplier['rib'];?></td>
				</tr>
					<?php
				}
				else{
					?>
				<tr>
					<td style="padding:10px 4px;border:1px solid #888888;text-align:left;font-weight:bold;background:#EEEEEE;">IBAN</td>
					<td style="padding:10px 4px;border:1px solid #888888;white-space:nowrap;text-align:left;"><?php echo $bankaccountsupplier['iban'];?></td>
				</tr>
				<tr>
					<td style="padding:10px 4px;border:1px solid #888888;text-align:left;font-weight:bold;background:#EEEEEE;">SWIFT</td>
					<td style="padding:10px 4px;border:1px solid #888888;white-space:nowrap;text-align:left;"><?php echo $bankaccountsupplier['swift'];?></td>
				</tr>					
					<?php
				}
				?>
				<tr>
					<td style="padding:10px 4px;border:1px solid #888888;text-align:left;font-weight:bold;background:#EEEEEE;">Banque</td>
					<td style="padding:10px 4px;border:1px solid #888888;white-space:nowrap;text-align:left;"><?php echo $bankaccountsupplier['bank'];?></td>
				</tr>
				<?php
				if($payment['currency'] != ""){
				?>
				<tr>
					<td style="padding:10px 4px;border:1px solid #888888;text-align:left;font-weight:bold;background:#EEEEEE;">Adresse</td>
					<td style="padding:10px 4px;border:1px solid #888888;white-space:nowrap;text-align:left;"><?php echo $bankaccountsupplier['address'];?></td>
				</tr>	
				<?php
				}
				?>
			</table>
			<div style="font-size:12px;font-family:'Times New Roman';">
				<?php echo $_GET['note'];?>
			</div>
			<div style="font-size:12px;font-family:'Times New Roman';">
				<p>Veuillez agréer, Messieurs, l'expression de nos salutation les plus distinguées.</p>
			</div>			
			<div style="page-break-inside: avoid;">
				<p style="margin:30px 10px 10px 10px;text-align:center;font-family:arial;font-size:12px;text-decoration:underline;"><?php echo $_GET['label'];?></p>
				<?php
				if(preg_match("#Impression des documents avec cachet et signature#",$_SESSION['easybm_roles'])){
					if(isset($_GET['signature'])){
						if($_GET['signature'] == "1" AND $company['signature'] !== ""){
						?>
				<p style="padding:0px 0px 0px 0px;text-align:<?php echo $_GET['placement']?>;"><img src="uploads/<?php echo $company['signature'];?>" style="display:block;height:100px;margin:10px auto;" /></p>
						<?php
						}
					}
				}
				?>
			</div>
		</div>
		<htmlpagefooter name="MyCustomFooter">
			<div class="footer">
				<?php
				if(isset($_GET['header'])){
					if($_GET['header'] == "1"){
						?>
						<p style='padding:5px 0px 0px 0px;font-family:arial;font-size:9px;text-align:center;border-top:1px solid #000000;'><b>Siège social: </b><?php echo $company['rs'];?> - <?php echo $company['address'];?><br />
						<?php
						if($company['phone'] != ""){
							?>
						<b>Téléphone: </b><?php echo $company['phone'];?> 
							<?php
						}
						if($company['email'] != ""){
							?>
						- <b>E-mail: </b><?php echo $company['email'];?>
							<?php
						}
						if($company['website'] != ""){
							?>
						- <b>Siteweb: </b><?php echo $company['website'];?>
							<?php
						}
						?>
						<br />
						<?php
						if($company['capital'] != ""){
							?>
						<b>Capital de </b><?php echo $company['capital'];?> 
							<?php
						}
						if($company['rc'] != ""){
							?>
						- <b>R.C.: </b><?php echo $company['rc'];?> 
							<?php
						}
						if($company['patente'] != ""){
							?>
						- <b>Patente: </b><?php echo $company['patente'];?>
							<?php
						}
						?>
						<br />
						<?php
						if($company['iff'] != ""){
							?>
						<b>I.F.: </b><?php echo $company['iff'];?> 
							<?php
						}
						if($company['cnss'] != ""){
							?>
						- <b>C.N.S.S.: </b><?php echo $company['cnss'];?> 
							<?php
						}
						if($company['ice'] != ""){
							?>
						- <b>ICE: </b><?php echo $company['ice'];?>
							<?php
						}
						?>
						<br /><b>Page: {PAGENO}/{nbpg}</b></p>
						<?php
					}
					else{
						?>
				<p style="padding:0px 50px 30px 0px;text-align:right;"><img src="images/fakesignature2.png" style="display:block;height:45px;margin:0px auto;" /></p>
						<?php
					}
				}
				else{
					?>
					<p style='padding:5px 0px 0px 0px;font-family:arial;font-size:9px;text-align:center;border-top:1px solid #000000;'><b>Siège social: </b><?php echo $company['rs'];?> - <?php echo $company['address'];?><br />
					<?php
					if($company['phone'] != ""){
						?>
					<b>Téléphone: </b><?php echo $company['phone'];?> 
						<?php
					}
					if($company['email'] != ""){
						?>
					- <b>E-mail: </b><?php echo $company['email'];?>
						<?php
					}
					if($company['website'] != ""){
						?>
					- <b>Siteweb: </b><?php echo $company['website'];?>
						<?php
					}
					?>
					<br />
					<?php
					if($company['capital'] != ""){
						?>
					<b>Capital de </b><?php echo $company['capital'];?> 
						<?php
					}
					if($company['rc'] != ""){
						?>
					- <b>R.C.: </b><?php echo $company['rc'];?> 
						<?php
					}
					if($company['patente'] != ""){
						?>
					- <b>Patente: </b><?php echo $company['patente'];?>
						<?php
					}
					?>
					<br />
					<?php
					if($company['iff'] != ""){
						?>
					<b>I.F.: </b><?php echo $company['iff'];?> 
						<?php
					}
					if($company['cnss'] != ""){
						?>
					- <b>C.N.S.S.: </b><?php echo $company['cnss'];?> 
						<?php
					}
					if($company['ice'] != ""){
						?>
					- <b>ICE: </b><?php echo $company['ice'];?>
						<?php
					}
					?>
					<br /><b>Page: {PAGENO}/{nbpg}</b></p>
					<?php
				}
				?>
			</div>
		</htmlpagefooter>
	</body>
</html>
<?php
$content = ob_get_clean();
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;
$mpdf->SetAutoPageBreak(false);
$mpdf->WriteHTML($content);
$mpdf->Output('order_de_virement.pdf','D');
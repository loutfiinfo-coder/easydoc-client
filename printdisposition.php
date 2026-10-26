<?php
session_start();
include("config.php");
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();

ob_start();
$note = '<p><b>Note supplimentaire: </b>'.addslashes($_GET['fullname']).' / '.addslashes($_GET['cin']).' / '.addslashes($_GET['note']).'</p>';
$req = $bdd->prepare("UPDATE payments SET note=CONCAT(note,'".$note."'),rib='".$_GET['bankaccount']."' WHERE id='".$_GET['id']."'");
$req->execute();

$back = $bdd->query("SELECT * FROM payments WHERE id='".$_GET['id']."'");
$payment = $back->fetch();

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
				<p style="margin:5px 0px;"><?php echo $bankaccount['city'];?>, Le <?php echo date("d/m/Y",$payment['dateadd']);?></p>
				<p style="margin:5px 0px;">A Monsieur le Directeur de</p>
				<p style="margin:5px 0px;"><?php echo $bankaccount['bank'].", ".$bankaccount['agency'];?></p><br />
			</div>
			<div style="font-size:14px;font-family:'Times New Roman';">
				<p><b>Objet:</b> Mise à Disposition</p>
				<br />
				<p>Monsieur Le Directeur,</p>
				<p>Nous vous prionws de bien vouloir débiter notre compte N° <b><?php echo $bankaccount['rib'];?></b>, Pour mettre à la disposition à l'ordre de Monsieur:</p>
				<p><b><?php echo $_GET['fullname'];?></b> titulaire de la carte d'identité nationale N° <b><?php echo $_GET['cin'];?></b></p>
				<p>Un montant de <b><?php echo number_format((float)$payment['price'],2,'.',' ')." ".$settings['currency'];?></b></p>
				<p>Soit <b><?php echo chifre_en_lettre(number_format((float)($payment['price']), 2, '.', ''),$payment['currency']);?></b></p>
				<?php
				if($_GET['note'] != ""){
				?>
				<p>Et ce pour règlement: <b><?php echo $_GET['note'];?></b></p>
				<?php
				}
				?>
				<p style="margin-top:30px;">Veuillez agréer Monsieur le directeur nos salutations les plus distinguées</p>
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
$mpdf->Output('mise_a_disposition.pdf','D');

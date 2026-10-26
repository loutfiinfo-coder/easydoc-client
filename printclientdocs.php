<?php
session_start();
include("config.php");
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();

ob_start();
$back = $bdd->query("SELECT * FROM documents WHERE id='".$_GET['id']."'");
$facture = $back->fetch();
if($facture['type'] != "bl" AND $facture['type'] != "bs"){
	$_GET['valeur'] = "1";
}
$back = $bdd->query("SELECT * FROM companies WHERE id='".$facture['company']."'");
$company = $back->fetch();

$check = $bdd->prepare("
    SELECT id FROM printoptions
    WHERE company = :company AND document = :document
");
$check->execute([
    ':company'  => $_GET['company'],
    ':document' => $_GET['document']
]);

if ($check->rowCount()) {
    // UPDATE
    $sql = "
        UPDATE printoptions SET
        refdocs=:refdocs, signature=:signature, placement=:placement, header=:header,
        ice=:ice, valeur=:valeur, label=:label,
        d1=:d1, d2=:d2, d3=:d3, d4=:d4, d5=:d5, d6=:d6,
        payments=:payments, avoirs=:avoirs, reste=:reste
        WHERE company=:company AND document=:document
    ";
} else {
    // INSERT
    $sql = "
        INSERT INTO printoptions (
            refdocs, company, document, signature, placement, header, ice, valeur, label,
            d1, d2, d3, d4, d5, d6, payments, avoirs, reste
        ) VALUES (
            :refdocs, :company, :document, :signature, :placement, :header, :ice, :valeur, :label,
            :d1, :d2, :d3, :d4, :d5, :d6, :payments, :avoirs, :reste
        )
    ";
}

$req = $bdd->prepare($sql);
$req->execute([
    ':refdocs'   => $_GET['refdocs'],
    ':company'   => $_GET['company'],
    ':document'  => $_GET['document'],
    ':signature' => $_GET['signature'],
    ':placement' => $_GET['placement'],
    ':header'    => $_GET['header'],
    ':ice'       => $_GET['ice'],
    ':valeur'    => $_GET['valeur'],
    ':label'     => $_GET['label'],
    ':d1'        => $_GET['d1'],
    ':d2'        => $_GET['d2'],
    ':d3'        => $_GET['d3'],
    ':d4'        => $_GET['d4'],
    ':d5'        => $_GET['d5'],
    ':d6'        => $_GET['d6'],
    ':payments'  => isset($_GET['payments'])?$_GET['payments']:0,
    ':avoirs'    => isset($_GET['avoirs'])?$_GET['avoirs']:0,
    ':reste'     => isset($_GET['reste'])?$_GET['reste']:0,
]);
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
				margin:<?php echo $_GET['refdocs']=="under"?$company['headerheight']:'4';?>cm 0.5cm <?php echo $company['footerheight'];?>cm 0.5cm;
						<?php
					}
					elseif($_GET['header'] == "0"){
						?>
				margin:<?php echo $_GET['refdocs']=="under"?$company['headerheight']:'4';?>cm 0.5cm <?php echo $company['footerheight'];?>cm 0.5cm;
						<?php						
					}
				}
				else{
					?>
				margin:<?php echo $_GET['refdocs']=="under"?$company['headerheight']:'4';?>cm 0.5cm <?php echo $company['footerheight'];?>cm 0.5cm;
					<?php					
				}
				?>
				header: html_MyCustomHeader;
				footer: html_MyCustomFooter;
			}
			@page :first{
				header: html_MyFirstCustomHeader;
			}
		</style>
	</head>
	<body>
		<div style="padding:0px 20px 0px;font-family:'Arial';">
			<htmlpageheader name="MyFirstCustomHeader">
				<table style="width:100%;font-family:'Arial';">
					<tr>
						<?php
						$back = $bdd->query("SELECT * FROM clients WHERE id='".$facture['client']."'");
						$client = $back->fetch();
						$back = $bdd->query("SELECT * FROM prospects WHERE id='".$facture['prospect']."'");
						$prospect = $back->fetch();
						if($_GET['refdocs'] == "left"){
							?>
						<td style="width:50%;padding:0px 15px;">
							<p style="font-weight:bold;margin-top:0px;padding-top:0px;font-size:32px;"><?php echo $_GET['title'];?></p>
							<p style="font-weight:bold;font-size:12px;margin-top:0px;padding-top:0px;">Numéro: <strong style="font-weight:normal;"><?php echo $facture['code'];?></strong></p>
							<p style="font-size:8px;color:#FFFFFF;">-</p>
							<p style="font-size:12px;"><b>Date création: </b><?php echo date("d/m/Y",$facture['dateadd']);?></p>
							<?php
							if($facture['client'] > 0){
							?>
							<p style="font-size:12px;"><?php echo ($client['codecl']!=""?"<b>Code client: </b>".$client['codecl']:"<b>Réf client: </b>".$client['code']);?></p>
							<?php
							if($facture['type'] == "avoir" OR $facture['type'] == "br"){
								?>
							<p style="font-size:12px;"><b>Corrige la facture N°: </b><?php echo $facture['correctdoc'];?></p>
								<?php
							}
							}
							?>
						</td>
							<?php
						}
						?>
						<td style="width:50%;padding:0px 15px;" align="<?php echo $_GET['refdocs']=="left"?"right":"left";?>">
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
						<?php
						if($_GET['refdocs'] == "right"){
							?>
						<td style="width:50%;padding:0px 15px;" align="right">
							<p style="font-weight:bold;margin-top:0px;padding-top:0px;font-size:32px;"><?php echo $_GET['title'];?></p>
							<p style="font-weight:bold;font-size:12px;margin-top:0px;padding-top:0px;">Numéro: <strong style="font-weight:normal;"><?php echo $facture['code'];?></strong></p>
							<p style="font-size:8px;color:#FFFFFF;">-</p>
							<p style="font-size:12px;"><b>Date création: </b><?php echo date("d/m/Y",$facture['dateadd']);?></p>
							<?php
							if($facture['client'] > 0){
							?>
							<p style="font-size:12px;"><?php echo ($client['codecl']!=""?"<b>Code client: </b>".$client['codecl']:"<b>Réf client: </b>".$client['code']);?></p>
							<?php
							if($facture['type'] == "avoir" OR $facture['type'] == "br"){
								?>
							<p style="font-size:12px;"><b>Corrige la facture N°: </b><?php echo $facture['correctdoc'];?></p>
								<?php
							}
							}
							?>
						</td>
							<?php
						}
						?>
					</tr>
				</table>
			</htmlpageheader>
			<htmlpageheader name="MyCustomHeader">
				<table style="width:100%;font-family:'Arial';">
					<tr>
						<td style="width:100%;padding:0px 15px;" align="<?php echo $_GET['refdocs']=="left"?"right":"left";?>">
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
			<?php
			if($_GET['refdocs'] == "under"){
				?>			
			<table cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:30px;font-family:'Arial';">
				<tr>
					<td style="width:50%;padding:0px;text-align:left;">
						<p style="font-weight:bold;margin-top:0px;padding-top:0px;font-size:32px;"><?php echo $_GET['title'];?></p>
						<p style="font-weight:bold;font-size:12px;margin-top:0px;padding-top:0px;">Numéro: <strong style="font-weight:normal;"><?php echo $facture['code'];?></strong></p>
					</td>
					<td style="width:50%;padding:0px;text-align:right;">
						<p style="font-size:12px;"><b>Date création: </b><?php echo date("d/m/Y",$facture['dateadd']);?></p>
						<?php
						if($facture['client'] > 0){
						?>
						<p style="font-size:12px;"><?php echo ($client['codecl']!=""?"<b>Code client: </b>".$client['codecl']:"<b>Réf client: </b>".$client['code']);?></p>
						<?php
						if($facture['type'] == "avoir" OR $facture['type'] == "br"){
							?>
						<p style="font-size:12px;"><b>Corrige la facture N°: </b><?php echo $facture['correctdoc'];?></p>
							<?php
						}
						}
						?>
					</td>
				</tr>
			</table>
				<?php
			}
			?>
			<table style="width:100%;margin-top:-20px;font-family:'Arial';">
				<tr>
					<td><p style="margin-top:0px;padding-top:0px;font-size:12px;">Émetteur</p></td>
					<td></td>
					<td><p style="font-size:12px;">Adressé à</p></td>
				</tr>
				<tr>
					<td style="width:50%;padding:10px;background:#EEEEEE;">
						<p style="font-weight:bold;"><?php echo $company['rs'];?></p>
						<p style="font-size:12px;"><?php echo $company['address'];?></p>
						<?php
						if($company['phone'] != ""){
						?>
						<p style="font-size:12px;"><b>Téléphone: </b><?php echo $company['phone'];?></p>
						<?php
						}
						if($company['email'] != ""){
						?>
						<p style="font-size:12px;"><b>Email: </b><?php echo $company['email'];?></p>
						<?php
						}
						if($company['website'] != ""){
						?>
						<p style="font-size:12px;"><b>Siteweb: </b>: <?php echo $company['website'];?></p>
						<?php
						}
						?>
					</td>
					<td style="width:1%;"></td>
					<td style="width:49%;padding:10px;border:1px solid #000000;">
						<?php
						if($facture['client'] > 0){
						?>
						<p style="font-weight:bold;"><?php echo $client['fullname']!=""?$client['fullname']:"Client divers";?></p>
						<?php
						if($client['ice'] != "" AND $_GET['ice'] == "1"){
							?>
						<p style="font-size:12px;"><b><?php echo "ICE: <b>".$client['ice']."</b>";?></p>
							<?php
						}
						if($client['iff'] != "" AND $_GET['ice'] == "2"){
							?>
						<p style="font-size:12px;"><b><?php echo "IF: <b>".$client['iff']."</b>";?></p>
							<?php
						}
						if($client['phone'] != ""){
						?>
						<p style="font-size:12px;"><b>Téléphone: </b><?php echo $client['phone']?></p>
						<?php
						}
						if($client['address'] != ""){
							?>
						<p style="font-size:12px;"><b>Adresse: </b><?php echo $client['address']?></p>
							<?php
						}
						}
						elseif($facture['prospect'] > 0){
						?>
						<p style="font-weight:bold;"><?php echo $prospect['fullname']!=""?$prospect['fullname']:"Prospect";?></p>
						<?php
						if($prospect['ice'] != "" AND $_GET['ice'] == "1"){
							?>
						<p style="font-size:12px;"><b><?php echo "ICE: <b>".$prospect['ice']."</b>";?></p>
							<?php
						}
						if($prospect['phone'] != ""){
						?>
						<p style="font-size:12px;"><b>Téléphone: </b><?php echo $prospect['phone']?></p>
						<?php
						}
						if($prospect['address'] != ""){
							?>
						<p style="font-size:12px;"><b>Adresse: </b><?php echo $prospect['address']?></p>
							<?php
						}
						}
						?>
					</td>
				</tr>
			</table>
			<?php
			if($facture['abovetable'] != ""){
			?>
			<div>
				<?php echo str_replace('<p','<p style="margin:0px;padding:0px;"',$facture['abovetable']);?>
			</div>
			<?php
			}
			$h = 0;
			$price = 0;
			$maindiscount = 0;			
			$existdiscount = 0;
			?>
			<table style="margin-top:10px;border-collapse:collapse;width:100%;font-size:12px;font-family:'Arial';text-align:center;">
				<?php
				$products = explode("||",$facture['title']);
				$abovenotes = explode("||",$facture['abovenote']);
				$belownotes = explode("||",$facture['belownote']);
				$units = explode("||",$facture['unit']);
				$qtys = explode("||",$facture['qty']);
				$uprices = explode("||",$facture['uprice']);
				$tvas = explode("||",$facture['ptva']);
				$tprices = explode("||",$facture['tprice']);
				$discounts = explode("||",$facture['discount']);
				$discounttype = explode("||",$facture['discounttype']);
				for($i=0;$i<count($discounts);$i++){
					if($discounts[$i] > 0){
						$existdiscount = 1;
					}					
				}
				?>
				<thead>
					<tr>
						<?php
						if($_GET['d1'] == "1"){
						?>
						<th style="width:50%;padding:10px;border:1px solid #000000;">Désignation</th>
						<?php
						}
						if($_GET['d2'] == "1"){
						?>
						<th style="padding:10px;border:1px solid #000000;">Qté</th>
						<?php
						}
						if($_GET['valeur'] == "1"){
							if($_GET['d3'] == "1"){
							?>
						<th style="padding:10px;border:1px solid #000000;white-space:nowrap;">P.U. HT</th>
							<?php
							}
							if($existdiscount == 1){
								if($_GET['d4'] == "1"){
								?>
						<th style="padding:10px;border:1px solid #000000;white-space:nowrap;">Remise totale HT</th>
								<?php
								}
							}
							if($_GET['d5'] == "1"){
							?>
						<th style="padding:10px;border:1px solid #000000;">TVA</th>
							<?php
							}
							if($_GET['d6'] == "1"){
							?>
						<th style="padding:10px;border:1px solid #000000;white-space:nowrap;">Total HT</th>
							<?php
							}
						}
						?>
					</tr>
				</thead>
				<?php
				$tvarate = [];
				$tvavalue = [];
				for($i=0;$i<count($products);$i++){
					if($qtys[$i] > 0){
					?>
				<tr>
					<?php
					if($_GET['d1'] == "1"){
					?>
					<td style="height:20px;padding:10px 4px;border-top:1px solid #000000;border-right:1px solid #000000;border-left:1px solid #000000;border-bottom:1px solid #000000;text-align:left;">
						<?php echo $products[$i];?>
					</td>
					<?php
					}
					if($_GET['d2'] == "1"){
					?>
					<td style="padding:10px 4px;border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;white-space:nowrap;"><?php echo $qtys[$i]." ".$units[$i];?></td>
					<?php
					}
					if($_GET['valeur'] == "1"){
						if($_GET['d3'] == "1"){
						?>
					<td style="padding:10px 4px;border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;white-space:nowrap;"><?php echo number_format((float)$uprices[$i],2,'.',' ');?></td>
						<?php
						}
						if($existdiscount == 1){
							if($_GET['d4'] == "1"){
						?>
					<td style="padding:10px 4px;border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;white-space:nowrap;"><?php echo $discounts[$i]>0?$discounts[$i].($discounttype[$i]=="%"?"%":""):"";?></td>
						<?php
							}
						}
						if($_GET['d5'] == "1"){
						?>
					<td style="padding:10px 4px;border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;white-space:nowrap;"><?php echo $tvas[$i];?>%</td>
						<?php
						}
						if($_GET['d6'] == "1"){
						?>
					<td style="padding:10px 4px;border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;white-space:nowrap;"><?php echo number_format((float)$tprices[$i],2,'.',' ');?></td>
						<?php
						}
					}
					?>
				</tr>						
					<?php
					$tprice = number_format((float)$tprices[$i],2,'.','');
					$tvarate[] = $tvas[$i];
					$tvavalue[] = (number_format((float)($tprice * ((int)$tvas[$i]/100)),2,".",""));
					$maindiscount += number_format((float)($discounttype[$i]=="%"?(($uprices[$i] * $qtys[$i]) * ($discounts[$i] / 100)):$discounts[$i]), 2, '.', '');
					$price += number_format((float)($tprices[$i]), 2, '.', '');
					}
				}
				?>
			</table>
			<?php
			if($_GET['valeur'] == "1"){
			?>
			<div style="width:351px;float:left;">
				<table style="width:100%;border-collapse:collapse;font-family:'Arial';font-size:12px;font-weight:bold;">
					<tr>
						<td>
							<br />
							<p style="font-weight:normal;">Total arrêté à la somme de:</p>
							<p style="font-size:8px;color:#FFFFFF;">-</p>
							<p style="white-space:nowrap;"># <?php echo chifre_en_lettre(number_format((float)($facture['price']), 2, '.', ''),$settings['currency']);?> #</p>
						</td>
					</tr>
				</table>
			</div>
			<div style="width:351px;float:right;">
				<table style="width:100%;border-collapse:collapse;font-family:'Arial';font-size:12px;font-weight:bold;">
					<?php
					if($maindiscount > 0){
						?>
					<tr>
						<td style="padding:5px;font-weight:normal;height:22px;">Remise totale HT</td>
						<td style="padding:5px;font-weight:normal;text-align:right;"><?php echo number_format((float)($maindiscount), 2, '.', ' ');?></td>
					</tr>
						<?php
					}				
					?>
					<tr>
						<td style="width:15%;padding:5px;height:22px;">Total HT <?php echo $maindiscount > 0?"après remise":"";?></td>
						<td style="width:15%;padding:5px;text-align:right;"><?php echo number_format((float)($price), 2, '.', ' ');?></td>					
					</tr>
					<?php
					$tval = 0;
					$uniquetvarate = array_unique($tvarate);
					sort($uniquetvarate);
					foreach($uniquetvarate AS $value){
						$val = 0;
						for($j=0;$j<count($tvarate);$j++){
							if($tvarate[$j] == $value){
								$val += $tvavalue[$j];
								$tval += $tvavalue[$j];
							}
						}
						?>
					<tr>
						<td style="padding:5px;height:22px;">TVA <?php echo $value;?>%</td>
						<td style="padding:5px;text-align:right;"><?php echo number_format((float)$val, 2, '.', ' ');?></td>
					</tr>
						<?php
					}
					?>
					<tr style="background:#EEEEEE;">
						<td style="padding:5px;height:22px;" valign="top">Total TTC</td>
						<td style="padding:5px;text-align:right;" valign="top"><?php echo number_format((float)($price + $tval), 2, '.', ' ');?></td>
					</tr>
				</table>
				<?php
				if($facture['type'] == "facture" OR $facture['type'] == "nh" OR $facture['type'] == "nf"){
				?>
				<table cellspacing="0" cellpadding="0" style="margin-top:10px;width:100%;font-size:10px;font-family:'Arial';border:1px solid #242424;">
					<?php
					$back = $bdd->query("SELECT SUM(price) AS price,SUM(payments) AS payments FROM documents WHERE type='avoir' AND correctdoc='".$facture['code']."'");
					$row = $back->fetch();
					if($_GET['avoirs'] == "1"){
					?>
					<tr>
						<td style="padding:5px;height:22px;font-weight:normal;border-right:1px solid #242424;border-bottom:1px solid #242424;" valign="top"><em>Avoirs appliqués</em></td>
						<td style="padding:5px;text-align:right;border-bottom:1px solid #242424;font-weight:bold;" valign="top"><?php echo number_format((float)$row['price'], 2, '.', ' ')." ".$settings['currency'];?></td>
					</tr>
					<?php
					}
					if($_GET['payments'] == "1"){
					?>
					<tr>
						<td style="padding:5px;height:22px;font-weight:normal;border-right:1px solid #242424;border-bottom:1px solid #242424;" valign="top"><em>Payé</em></td>
						<td style="padding:5px;text-align:right;border-bottom:1px solid #242424;font-weight:bold;" valign="top"><?php echo number_format((float)$facture['payments'], 2, '.', ' ')." ".$settings['currency'];?></td>
					</tr>
					<?php
					}
					if($_GET['reste'] == "1"){
					?>
					<tr>
						<td style="padding:5px;height:22px;font-weight:normal;border-right:1px solid #242424;" valign="top"><em>Reste</em></td>
						<td style="padding:5px;text-align:right;font-weight:bold;" valign="top"><?php echo number_format((float)($facture['price'] - $facture['payments']) - ($row['price'] - $row['payments']), 2, '.', ' ')." ".$settings['currency'];?></td>
					</tr>
					<?php
					}
					?>
				</table>
				<?php
				}
				?>
			</div>
			<?php
			}
			?>
			<div style="clear:both;"></div>
			<div style="margin-top:10px;">
				<?php
				if($facture['modepayment'] != ""){
					if($facture['modepayment'] != ""){
						echo str_replace('<p','<p style="margin:0px;padding:0px;"',$facture['modepayment']);
					}
				}
				else{
					echo "&nbsp;";
				}
				?>				
			</div>
			<div style="clear:both;"></div>
			<?php
			if($facture['picture'] != ""){
			?>
			<div>
				<img src="uploads/<?php echo $facture['picture'];?>" style="max-width:100%;max-height:110%;" />
			</div>
			<?php
			}
			?>
			<div style="clear:both;"></div>
			<div style="page-break-inside: avoid;">
				<p style="margin:30px 10px 10px 10px;text-align:center;font-family:arial;font-size:12px;text-decoration:underline;"><?php echo $_GET['label'];?></p>
				<?php
				if(isset($_GET['cachet']) AND $_GET['cachet'] == 1){
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
						<p style="font-size:8px;color:#FFFFFF;">-</p>
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
$css = "
ul {
  list-style-type: disc !important;
  margin-left: 20px !important;
  padding-left: 20px !important;
}

ol {
  list-style-type: decimal !important;
  margin-left: 20px !important;
  padding-left: 20px !important;
}

li {
  display: list-item !important;
  margin-bottom: 5px;
}
";

$content = ob_get_clean();
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;
$mpdf->WriteHTML(wrapListsInTables($content));
$mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->Output($facture['code'].'.pdf','D');


/**
 * Wrap all <ul> or <ol> elements that are inside tables
 * into a 2-column structure:
 *   <table><tr>
 *      <td style="width:25px;"></td>
 *      <td> ... list ... </td>
 *   </tr></table>
 *
 * @param string $html The input HTML.
 * @param int $widthPx The width in pixels for the left spacer column (default: 25).
 * @return string Modified HTML ready for mPDF.
 */
function wrapListsInTables(string $html, int $widthPx = 25): string
{
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML(
        '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>' . $html . '</body></html>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );

    $xpath = new DOMXPath($doc);

    // Find all ul and ol inside tables
    $lists = $xpath->query('//table//ul | //table//ol');

    foreach ($lists as $list) {
        $parent = $list->parentNode;
        if (!$parent) continue;

        // Build the new table wrapper
        $wrapperTable = $doc->createElement('table');
        $tr = $doc->createElement('tr');

        $td1 = $doc->createElement('td');
        $td1->setAttribute('style', "width:{$widthPx}px;");

        $td2 = $doc->createElement('td');

        // Move the list into td2
        $td2->appendChild($list->cloneNode(true));

        $tr->appendChild($td1);
        $tr->appendChild($td2);
        $wrapperTable->appendChild($tr);

        // Replace the old list with the new table
        $parent->replaceChild($wrapperTable, $list);
    }

    $body = $doc->getElementsByTagName('body')->item(0);
    $result = '';
    foreach ($body->childNodes as $child) {
        $result .= $doc->saveHTML($child);
    }

    libxml_clear_errors();
    return $result;
}

function cmToPx($cm, $dpi = 96) {
    // 1 inch = 2.54 cm
    // px = inch * dpi
    $inches = $cm / 2.54;
    return $inches * $dpi;
}
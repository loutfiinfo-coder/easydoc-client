<?php
session_start();
include("config.php");
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'c',
    'format' => 'A4-L'
]);

ob_start();
?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<style>
			@page {
				margin:0px;
				header: html_MyCustomHeader;
				footer: html_MyCustomFooter;
			}
			.lx-clear-fix{
				clear:both;
			}
			.lx-caisse-total{
				float:right;
				width:200px;
				margin-bottom:20px;
				text-align:center;
				color:#FFFFFF;
				background:#828282;
				border-radius:6px;
			}
			.lx-caisse-total h3{
				font-size:16px;
				margin:0px;
				padding:8px;
			}
			.lx-caisse-total p{
				font-size:16px;
				margin:0px;
				padding:20px;
				background:rgba(255,255,255,0.2);
			}
			.lx-caisse-total span{
				float:left;
				display:block;
				width:160px;
				padding:10px;
				font-weight:bold;
				font-size:14px;
			}
			.lx-caisse-total strong{
				float:right;
				display:block;
				width:160px;
				padding:10px;
				font-size:14px;
			}
		</style>
	</head>
	<body>
		<div style="padding:20px;font-family:'Arial';">
			<htmlpageheader name="MyCustomHeader">

			</htmlpageheader>
			<h3 style="margin-bottom:30px;text-align:center;">Historique des paiements</h3>
				<table style="border-collapse:collapse;width:100%;font-size:8px;font-family:'Arial';text-align:center;">
					<tr>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Date <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Société <i class="fa fa-sort" data-sort="company"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Nature <i class="fa fa-sort" data-sort="type"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Mode de paiement <i class="fa fa-sort" data-sort="modepayment"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Libellé <i class="fa fa-sort" data-sort="title"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Fournisseur <i class="fa fa-sort" data-sort="supplier"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Note <i class="fa fa-sort" data-sort="note"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Date d'écheance <i class="fa fa-sort" data-sort="datedue"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Imputation comptable <i class="fa fa-sort" data-sort="imputation"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Compte banquaire encaissement / décaissement <i class="fa fa-sort" data-sort="rib"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Date d'encaissement / décaissement (Rapprochement): <i class="fa fa-sort" data-sort="datepaid"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Remis <i class="fa fa-sort" data-sort="remis"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Impayé <i class="fa fa-sort" data-sort="paid"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Facturé <i class="fa fa-sort" data-sort="invoiced"></i></td>
						<td style="padding:4px;border:1px solid #000000;font-weight:bold;">Utilisateur <i class="fa fa-sort" data-sort="worker"></i></td>
					</tr>
					<?php
					$back = $bdd->query($_GET['req']);
					$nbin = 0;
					$totalin = 0;
					$nbout = 0;
					$totalout = 0;
					while($row = $back->fetch()){
						?>
					<tr style="<?php echo $row['paid']=="1"?"background:rgba(255,0,0,0.1);":"";?>">
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;border-left:1px solid #000000;"><?php echo ($row['dateadd']!=""?gmdate("d/m/Y H:i",3600+$row['dateadd']):"&mdash;");?></td>
						<?php
						$back1 = $bdd->query("SELECT rs FROM companies WHERE id='".$row['company']."'");
						$row1 = $back1->fetch();
						?>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;"><?php echo $row1['rs'];?></td>						
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;"><?php echo $row['code'];?></td>
						<?php
						if($row['type'] == "Entrée"){
							if($row['paid'] == "0"){
								$nbin++;
								$totalin += $row['price'];								
							}
							$color = "color:#a2d695;";
							$background = "background:#a2d695;";
						}
						else{
							if($row['paid'] == "0"){
								$nbout++;
								$totalout += $row['price'];							
							}
							$color = "color:#ff7373;";
							$background = "background:#ff7373;";
						}
						?>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;<?php echo $color;?>;font-weight:bold;"><?php echo $row['type'];?></td>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;"><?php echo $row['modepayment'];?></td>
						<td style="text-align:right;padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;width:80px;<?php echo $background;?>">
							<?php echo number_format((float)$row['price'],2,"."," ")." ".$settings['currency'];?> TTC
						</td>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;"><?php echo $row['title'];?></td>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;">	
							<?php
							$back1 = $bdd->query("SELECT code,fullname FROM clients WHERE id='".$row['client']."'");
							$client = $back1->fetch();
							if($row['client'] != "0"){
								echo $client['fullname']." (".$client['code'].")";
							}
							?>
						</td>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;">	
							<?php
							$back1 = $bdd->query("SELECT title FROM suppliers WHERE id='".$row['supplier']."'");
							$supplier = $back1->fetch();
							if($row['supplier'] != "0"){
								echo $supplier['title'];
							}
							?>
						</td>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;"><?php echo $row['note'];?></td>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;<?php echo (($row['datepaid']=="" AND $row['datedue']!="" AND $row['datedue']<=time())?"color:orange;font-weight:bold;;":"");?>"><?php echo ($row['datedue']!=""?gmdate("d/m/Y",3600+$row['datedue']):"&mdash;");?></td>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;"><?php echo $row['imputation'];?></td>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;">	
							<?php
							$back1 = $bdd->query("SELECT * FROM bankaccounts WHERE id='".$row['rib']."'");
							$rib = $back1->fetch();
							if($row['rib'] != "0"){
								?>
							<?php echo $rib['bank']." | ".$rib['rib'];?>
								<?php
							}
							?>
						</td>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;<?php echo ($row['datepaid']!=""?"color:#7EC855;font-weight:bold;":"");?>"><?php echo ($row['datepaid']!=""?gmdate("d/m/Y",3600+$row['datepaid']):"&mdash;");?></td>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;">
							<?php
							if($row['remis'] == '1'){
								?>
						Remis<br />
						Le: <?php echo gmdate("d/m/Y",$row['dateremis']+3600)?>
								<?php
							}
							?>
						</td>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;">
							<?php
							if($row['paid'] == '1'){
								echo "Impayé";
							}
							?>
						</td>
						<?php
						if($row['invoiced'] == "Oui"){
							$color = "color:#a2d695;";
						}
						else{
							$color = "color:#ff7373;";
						}
						?>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;<?php echo $color;?>;font-weight:bold;"><?php echo $row['invoiced'];?></td>
						<td style="padding:4px;border-bottom:1px solid #000000;border-right:1px solid #000000;">
							<?php
							$back1 = $bdd->query("SELECT fullname FROM users WHERE id='".$row['user']."'");
							$user = $back1->fetch();
							?>
							<?php echo $user['fullname'];?>
						</td>
					</tr>
						<?php
					}
					?>
				<tr>
					<td style="height:30px;"></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td colspan="3" style="padding:4px;border:1px solid #000000;font-weight:bold;">Total entrée</td>
					<td colspan="3" style="padding:4px;border:1px solid #000000;background:#a2d695;text-align:right;"><?php echo number_format((float)$totalin,2,"."," ")." ".$settings['currency'];?></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td colspan="3" style="padding:4px;border:1px solid #000000;font-weight:bold;">Total sortie</td>
					<td colspan="3" style="padding:4px;border:1px solid #000000;background:#ff7373;text-align:right;"><?php echo number_format((float)$totalout,2,"."," ")." ".$settings['currency'];?></td>
				</tr>
			</table>
		</div>
		<htmlpagefooter name="MyCustomFooter">
		
		</htmlpagefooter>
	</body>
</html>
<?php
$content = ob_get_clean();
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;
$mpdf->WriteHTML($content);
$mpdf->Output("CAISSE.pdf",'D');

function sanitize_vars($var){
	if(preg_match("#script|select|update|delete|concat|create|table|union|length|show_table|mysql_list_tables|mysql_list_fields|mysql_list_dbs#i",$var)){
		$var = "";
	}
	return htmlspecialchars(addslashes(trim($var)));
}
?>
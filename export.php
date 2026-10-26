<?php
session_start();
header("Content-Type: application/vnd.ms-excel; charset=utf-8"); 
header('Content-Disposition: attachment;filename="'.$_GET['table'].'-'.date('d/m/Y').'.xls"'); 
include('config.php');
?>
<html>
	<head>
		<meta charset='UTF-8'>
		<style>
			.text{
			  mso-number-format:"\@";
			}
			.float{
			  mso-number-format:0.00;
			}
			td{
				white-space:nowrap;
			}
		</style>
	</head>
	<body>
		<table cellpadding="0" cellspacing="0" border="1">
			<tr align="center" valign="top">
				<?php
				$columntitles = explode(",",$_GET['columntitles']);
				$columns = explode(",",$_GET['columns']);
				for($i=1;$i<count($columntitles);$i++){
					?>
				<td style="font-weight:bold;background:#39add1;"><?php echo $columntitles[$i]?></td>
					<?php						
				}
				?>
			</tr>
			<?php
			$back = $bdd->query($_GET['req']);
			while($row = $back->fetch()){
				$price_1 = 0;
				$price0 = 0;
				$price1 = 0;
				$price2 = 0;
				$discount = 0;
				$paid = 0;
				$rest = 0;
				?>
			<tr valign="top">
				<?php
				for($i=1;$i<count($columns);$i++){
					$j = 0;
					if($_GET['table'] == "documents"){
						$titles = explode("||",$row['title']);
						$units = explode("||",$row['unit']);
						$qtys = array_map('floatval',explode("||",$row['qty']));
						$uprices = array_map('floatval',explode("||",$row['uprice']));
						$tprices = array_map('floatval',explode("||",$row['tprice']));
						$ptvas = array_map('floatval',explode("||",$row['ptva']));
						$abovenotes = explode("||",$row['belownote']);
						$belownotes = explode("||",$row['belownote']);
						$discounttypes = explode("||",$row['discounttype']);
						$discounts = array_map('floatval',explode("||",$row['discount']));
						for($x=0;$x<count($titles);$x++){
							$d = 0;
							if($row['discounttype'] == $settings['currency']){
								$discount += $discounts[$x];
								$d = $discounts[$x];
							}
							else{
								$discount += (($uprices[$x]*$qtys[$x])*($discounts[$x]/100));
								$d = (($uprices[$x]*$qtys[$x])*($discounts[$x]/100));
							}
							$price1 += (($uprices[$x]+$d)*(1+($ptvas[$x]/100)));
						}
						$price_1 = (($row['price']+$d)-$row['tva']);
						$price0 = ($row['price']-$row['tva']);
						$price2 = ($row['price']-$row['rastva']);
					}
					if($_GET['table'] == "clients" AND $columns[$i] == "ca"){
						$back1 = $bdd->query("SELECT SUM(CASE WHEN type='facture' OR type='nh' THEN (price - rastva) WHEN type='avoir' THEN price*(-1) ELSE 0 END) AS ca FROM documents WHERE client='".$row['id']."'");
						$row1 = $back1->fetch();
						$ca = $row1['ca'];	
						$back1 = $bdd->query("SELECT SUM(CASE WHEN typedoc='facture' OR typedoc='nh' THEN price WHEN typedoc='avoir' THEN price*(-1) ELSE 0 END) AS paid FROM payments WHERE client='".$row['id']."'");
						$row1 = $back1->fetch();
						$paid = $row1['paid'];
						$rest = ($ca - $row1['paid']);
						?>
				<td><?php echo number_format((float)$ca,2,",","");?></td>
						<?php						
					}
					elseif($_GET['table'] == "clients" AND $columns[$i] == "paid"){
						?>
				<td><?php echo number_format((float)$paid,2,",","");?></td>
						<?php						
					}
					elseif($_GET['table'] == "clients" AND $columns[$i] == "encours"){
						?>
				<td><?php echo number_format((float)$rest,2,",","");?></td>
						<?php						
					}
					elseif($_GET['table'] == "suppliers" AND $columns[$i] == "ca"){
						$back1 = $bdd->query("SELECT SUM(price+(tva-rastva)) AS ca FROM facturesuppliers WHERE supplier='".$row['id']."'");
						$row1 = $back1->fetch();
						$ca = $row1['ca'];
						$back1 = $bdd->query("SELECT SUM(price) AS paid FROM payments WHERE typedoc='facturesupplier' AND supplier='".$row['id']."'");
						$row1 = $back1->fetch();
						$paid = $row1['paid'];
						$rest = ($ca - $row1['paid']);
						?>
				<td><?php echo number_format((float)$ca,2,",","");?></td>
						<?php						
					}
					elseif($_GET['table'] == "suppliers" AND $columns[$i] == "paid"){
						?>
				<td><?php echo number_format((float)$paid,2,",","");?></td>
						<?php						
					}
					elseif($_GET['table'] == "suppliers" AND $columns[$i] == "encours"){
						?>
				<td><?php echo number_format((float)$rest,2,",","");?></td>
						<?php						
					}
					elseif($_GET['table'] == "documents" AND $columns[$i] == "products"){
						?>
				<td>
					<?php
					for($x=0;$x<count($titles);$x++){
						if($x > 0){
							echo "||";
						}
						echo trim(strip_tags($titles[$x]))." (<b>".number_format((float)($uprices[$x]*(1+($ptvas[$x]/100))),2,".","")." ".$settings['currency']."</b> TTC) x <b>".$qtys[$x]." ".$units[$x]."</b>";
					}
					?>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "documents" AND $columns[$i] == "avoir"){
						?>
				<td>
						<?php
						if($row['avoirs'] != ""){
							?>
						Avoirs: 
							<?php
							$avoirs = explode("|",$row['avoirs']);
							for($j=0;$j<count($avoirs);$j++){
								$avoirdetails = explode("/",$avoirs[$j]);
								echo $avoirdetails[1]." / ";
							}
						}
						if($row['brs'] != ""){
							?>
						|| Bons de retour: 
							<?php
							$brs = explode("|",$row['brs']);
							for($j=0;$j<count($brs);$j++){
								$brdetails = explode("/",$brs[$j]);
								echo $brdetails[1]." / ";
							}
						}					
						?>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "documents" AND $columns[$i] == "price_1"){
						?>
				<td>
					<?php echo number_format((float)abs($price_1),2,",","");?>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "documents" AND $columns[$i] == "price0"){
						?>
				<td>
					<?php echo number_format((float)abs($price0),2,",","");?>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "documents" AND $columns[$i] == "price1"){
						?>
				<td>
					<?php echo number_format((float)abs($price1),2,",","");?>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "documents" AND $columns[$i] == "price2"){
						?>
				<td>
					<?php echo number_format((float)abs($price2),2,",","");?>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "documents" AND $columns[$i] == "discount"){
						?>
				<td>
					<?php echo number_format((float)abs($discount),2,",","");?>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "documents" AND $columns[$i] == "rest"){
						$smpaid = 0;
						$phtable = "";
						$back1 = $bdd->query("SELECT * FROM payments WHERE doc='".$row['id']."'");
						while($row1 = $back1->fetch()){
							$smpaid += $row1['price'];
						}
						?>
				<td>
					<?php echo number_format((float)abs($price2-$smpaid),2,",","");?>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "payments" AND $columns[$i] == "type"){
						?>
				<td>
					<span style="font-weight:600;<?php echo $row['type']=="Entrée"?"color:#a2d695;":"color:#ff7373;";?>"><?php echo $row['type'];?></span>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "payments" AND $columns[$i] == "price"){
						?>
				<td>
					<span style="font-weight:600;<?php echo $row['type']=="Entrée"?"color:#a2d695;":"color:#ff7373;";?>"><?php echo number_format((float)$row['price'],2,",","");?></span>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "payments" AND $columns[$i] == "remis"){
						?>
				<td>
						<?php
						if($row['remis'] == '1'){
							?>
						<span style="font-weight:500;color:#39add1;">Remis</span>
						<span style="white-space:nowrap;">Le: <?php echo date("d/m/Y",$row['dateremis']);?></span>
							<?php
							if($row['nremise'] != ""){
							?>
						<span>(N° de remise: <?php echo $row['nremise'];?>)</span>
							<?php
							}
						}
						?>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "payments" AND $columns[$i] == "paid"){
						?>
				<td>
						<?php
						if($row['paid'] == '1'){
							?>
						<span style="font-weight:500;color:#FF0000;">Impayé</span>
							<?php
						}
						?>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "facturesuppliers" AND $columns[$i] == "price1"){
						?>
				<td>
					<span><?php echo number_format((float)$row['price'],2,",","");?></span>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "facturesuppliers" AND $columns[$i] == "price2"){
						?>
				<td>
					<span><?php echo number_format((float)($row['price']+($row['tva']-$row['rastva'])),2,",","");?></span>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "facturesuppliers" AND $columns[$i] == "price3"){
						?>
				<td>
					<span><?php echo number_format((float)$row['price2'],2,",","");?></span>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "facturesuppliers" AND $columns[$i] == "price4"){
						?>
				<td>
					<span><?php echo number_format((float)($row['price2']+$row['tva2']),2,",","");?></span>
				</td>
						<?php						
					}					
					elseif($_GET['table'] == "facturesuppliers" AND $columns[$i] == "paymenthistory"){
						$smpaid = 0;
						$phtable = "";
						$back1 = $bdd->query("SELECT * FROM payments WHERE doc='".$row['id']."' and typedoc='facturesupplier'");
						while($row1 = $back1->fetch()){
							$smpaid += $row1['price'];
						}
						?>
				<td>
					<span><?php echo $row['state'];?></span>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "facturesuppliers" AND $columns[$i] == "delay"){
						?>
				<td>
					<span><?php echo $row['delay'];?></span>
				</td>
						<?php						
					}
					elseif($_GET['table'] == "companies" AND $columns[$i] == "bankaccounts"){
						?>
				<td>
						<?php
						$j = 0;
						$back1 = $bdd->query("SELECT * FROM bankaccounts WHERE company='".$row['id']."'");
						if($back1->rowCount() > 0){
							echo '"';
						}
						while($row1 = $back1->fetch()){
							if($j > 0){
								echo " || ";
							}
							echo "RIB: ".$row1['rib']." - Banque: ".$row1['bank']." - Agence: ".$row1['agence']." - Ville: ".$row1['city'];
							$j++;
						}	
						if($back1->rowCount() > 0){
							echo '"';
						}						
						?>
				</td>
						<?php
					}
					elseif($_GET['table'] == "companies" AND $columns[$i] == "regularity"){
						?>
				<td>
						<?php
							$back1 = $bdd->query("SELECT * FROM myregularity WHERE company='".$row['id']."'");
							$row1 = $back1->fetch();
							if(isset($row1['state'])){
							?>
							" - Statut: <b><?php echo $row1['state'];?></b>
							<?php
							}
							if(isset($row1['type'])){
								if($row1['type'] != ""){
							?>
							<span>- Type d'activité: <b><?php echo $row1['type'];?></b></span>
							<?php
								}
							}
							if(isset($row1['state'])){
								if($row1['state'] != ""){
							?>
							<span>- RAS applicable: <b><?php echo $row1['appras'];?></b></span>
							<?php
								}
							}
							if(isset($row1['doc'])){
								if($row1['doc'] != ""){
								?>
							<span>- Régularité fiscale: <b><?php echo $row1['doc'];?></b></span>
								<?php
								}
								if($row1['doc'] == "Avec"){
									if(isset($row1['attachments'])){
										if($row1['attachments'] != ""){
									?>
								<span><b>- Avec Document</b></span>
									<?php
										}
									}
									if(isset($row1['datedoc'])){
										if($row1['datedoc'] != ""){
									?>
								<span>- Date demande: <b><?php echo date("d/m/Y",$row1['datedoc']);?></b></span>
									<?php
										}
									}
									if(isset($row1['jdoc'])){
										if($row1['jdoc'] != ""){
									?>
								<span>- Période de validité en jours: <b><?php echo $row1['jdoc'];?> jours</b></span>
									<?php
										}
									}
								}
							}
							if(isset($row1['percentage'])){
								if($row1['percentage'] != ""){
								?>
							<span>- % TVA à retenir par le client: <b><?php echo $row1['percentage'];?>%</b></span>
								<?php
								}
							}
						if(isset($row1['state'])){
						?>
						"
						<?php
						}
						?>
				</td>
						<?php
					}
					elseif($_GET['table'] == "suppliers" AND $columns[$i] == "bankaccounts"){
						?>
				<td>
						<?php
						$j = 0;
						$back1 = $bdd->query("SELECT * FROM suppliersbankaccounts WHERE supplier='".$row['id']."'");
						if($back1->rowCount() > 0){
							echo '"';
						}
						while($row1 = $back1->fetch()){
							if($j > 0){
								echo " || ";
							}
							echo "RIB: ".$row1['rib']." - Banque: ".$row1['bank']." - Agence: ".$row1['agence']." - Ville: ".$row1['city'];
							$j++;
						}
						if($back1->rowCount() > 0){
							echo '"';
						}						
						?>
				</td>
						<?php
					}
					elseif($_GET['table'] == "suppliers" AND $columns[$i] == "regularity"){
						?>
				<td style="width:300px; white-space:normal; mso-width-source:auto; mso-width-alt:15000;">
						<?php
							$back1 = $bdd->query("SELECT * FROM regularity WHERE supplier='".$row['id']."'");
							$row1 = $back1->fetch();
							if(isset($row1['state'])){
							?>
							" - Statut: <b><?php echo $row1['state'];?></b>
							<?php
							}
							if(isset($row1['type'])){
								if($row1['type'] != ""){
							?>
							- Type d'activité: <b><?php echo $row1['type'];?></b>
							<?php
								}
							}
							if(isset($row1['state'])){
								if($row1['state'] != ""){
							?>
							- RAS applicable: <b><?php echo $row1['appras'];?></b>
							<?php
								}
							}
							if(isset($row1['doc'])){
								if($row1['doc'] != ""){
								?>
							- Régularité fiscale: <b><?php echo $row1['doc'];?></b>
								<?php
								}
								if($row1['doc'] == "Avec"){
									if(isset($row1['attachments'])){
										if($row1['attachments'] != ""){
									?>
								<b>- Avec Document</b>
									<?php
										}
									}
									if(isset($row1['datedoc'])){
										if($row1['datedoc'] != ""){
									?>
								- Date demande: <b><?php echo date("d/m/Y",$row1['datedoc']);?></b>
									<?php
										}
									}
									if(isset($row1['jdoc'])){
										if($row1['jdoc'] != ""){
									?>
								- Période de validité en jours: <b><?php echo $row1['jdoc'];?> jours</b>
									<?php
										}
									}
								}
							}
							if(isset($row1['percentage'])){
								if($row1['percentage'] != ""){
								?>
							- % TVA à retenir par le client: <b><?php echo $row1['percentage'];?>%</b>
								<?php
								}
							}
						if(isset($row1['state'])){
						?>
						"
						<?php
						}
						?>
				</td>
						<?php
					}
					elseif($_GET['table'] == "companies" AND $columns[$i] == "ca"){
						$back1 = $bdd->query("SELECT SUM(CASE WHEN type='facture' OR type='nh' THEN (price-rastva) WHEN type='avoir' THEN price*(-1) ELSE 0 END) AS ca FROM documents WHERE company='".$row['id']."'");
						$row1 = $back1->fetch();
						$ca = $row1['ca'];	
						$back1 = $bdd->query("SELECT SUM(CASE WHEN typedoc='facture' OR typedoc='nh' THEN price WHEN typedoc='avoir' THEN price*(-1) ELSE 0 END) AS paid FROM payments WHERE company='".$row['id']."'");
						$row1 = $back1->fetch();
						$paid = $row1['paid'];
						$rest = ($ca - $row1['paid']);
						?>
				<td><span><?php echo number_format((float)$ca,2,",","");?></span></td>
						<?php						
					}
					elseif($_GET['table'] == "companies" AND $columns[$i] == "paid"){
						?>
				<td><?php echo number_format((float)$paid,2,",","");?></td>
						<?php						
					}
					elseif($_GET['table'] == "companies" AND $columns[$i] == "encours"){
						?>
				<td><?php echo number_format((float)$rest,2,",","");?></td>
						<?php						
					}
					elseif($_GET['table'] == "payments" AND $columns[$i] == "tva"){
						?>
				<td>
					<?php
					if($row['invoiced'] == "Oui" AND $row['doc'] == "0"){
						echo number_format((float)$row['price']-($row['price']/(1+($row['tva']/100))),2,",","");
					}				
					?>
				</td>
						<?php						
					}		
					elseif($columns[$i] == "tva"){
						?>
				<td>
					<?php echo number_format((float)abs($row['tva']),2,",","");?>
				</td>
						<?php						
					}
					elseif($columns[$i] == "rastva"){
						?>
				<td>
					<?php echo number_format((float)abs($row['rastva']),2,",","");?>
				</td>
						<?php						
					}					
					elseif($columns[$i] == "dateadd"){
						?>
				<td><?php echo date("d/m/Y H:i",$row['dateadd']);?></td>
						<?php						
					}
					elseif($columns[$i] == "recalldate"){
						?>
				<td><?php echo $row['recalldate']!=""?date("d/m/Y H:i",$row['recalldate']):"";?></td>
						<?php						
					}
					elseif($columns[$i] == "dateupdate"){
						?>
				<td><?php echo date("d/m/Y H:i",$row['dateupdate']);?></td>
						<?php						
					}
					elseif($columns[$i] == "datedue"){
						?>
				<td><span style="font-weight:500;<?php echo (($row['datepaid']=="" AND $row['datedue']!="" AND $row['datedue']<=time())?"color:orange;":"");?>border-radius:4px;"><ins><?php echo ($row['datedue']!=""?date("d/m/Y",$row['datedue']):"&mdash;");?></ins></span></td>
						<?php						
					}
					elseif($columns[$i] == "datepaid"){
						?>
				<td><span style="font-weight:500;<?php echo ($row['datepaid']!=""?"color:#7EC855;":"");?>border-radius:4px;"><ins><?php echo ($row['datepaid']!=""?date("d/m/Y",$row['datepaid']):"&mdash;");?></ins></span></td>
						<?php						
					}
					elseif($columns[$i] == "datepaidras"){
						?>
				<td><span style="font-weight:500;<?php echo ($row['datepaidras']!=""?"color:#7EC855;":"");?>border-radius:4px;"><ins><?php echo ($row['datepaidras']!=""?date("d/m/Y",$row['datepaidras']):"&mdash;");?></ins></span></td>
						<?php						
					}
					elseif($columns[$i] == "dateretard"){
						?>
				<td><span style="font-weight:500;<?php echo ($row['dateretard']!=""?"color:#7EC855;":"");?>border-radius:4px;"><ins><?php echo ($row['dateretard']!=""?date("d/m/Y",$row['dateretard']):"&mdash;");?></ins></span></td>
						<?php						
					}
					elseif($columns[$i] == "dateduedelay"){
						?>
				<td><?php echo date("d/m/Y",$row['dateadd']+($row['delay']*24*60*60));?></td>
						<?php						
					}		
					elseif($columns[$i] == "stateras"){
						?>
				<td><?php echo $row['rastva'] > 0?$row['stateras']:"&mdash;";?></td>
						<?php						
					}						
					elseif($columns[$i] == "company"){
						?>
				<td>
						<?php
						$companylist = "";
						$back1 = $bdd->query("SELECT rs FROM companies WHERE id IN(".($row['company']!=""?$row['company']:0).") ORDER BY rs");
						while($company = $back1->fetch()){
							$companylist .= " - ".$company['rs'];
						}
						echo substr($companylist,3);
						?>
				</td>
						<?php
					}
					elseif($columns[$i] == "ice" OR $columns[$i] == "cnss"){
						?>
				<td class="text"><?php echo $row[$columns[$i]]?></td>
						<?php						
					}
					elseif($columns[$i] == "user"){
						$back1 = $bdd->query("SELECT fullname FROM users WHERE id='".$row['user']."'");
						$row1 = $back1->fetch();
						?>
				<td><?php echo $row1['fullname'];?></td>
						<?php						
					}
					elseif($columns[$i] == "rib"){
						$back1 = $bdd->query("SELECT bank,rib FROM bankaccounts WHERE id='".$row['rib']."'");
						$row1 = $back1->fetch();
						?>
				<td><?php echo isset($row1['rib'])?$row1['bank']." | ".$row1['rib'].")":"";?></td>
						<?php						
					}
					elseif($columns[$i] == "client"){
						$back1 = $bdd->query("SELECT fullname,code FROM clients WHERE id='".$row['client']."'");
						$row1 = $back1->fetch();
						?>
				<td><?php echo isset($row1['fullname'])?$row1['fullname']." (".$row1['code'].")":"";?></td>
						<?php						
					}
					elseif($columns[$i] == "prospect"){
						$back1 = $bdd->query("SELECT fullname FROM prospects WHERE id='".$row['prospect']."'");
						$row1 = $back1->fetch();
						?>
				<td><?php echo isset($row1['fullname'])?$row1['fullname']:"";?></td>
						<?php						
					}
					elseif($columns[$i] == "iceclient"){
						$back1 = $bdd->query("SELECT ice FROM clients WHERE id='".$row['client']."'");
						$row1 = $back1->fetch();
						?>
				<td class="text"><?php echo $row1['ice'];?></td>
						<?php						
					}
					elseif($columns[$i] == "ifclient"){
						$back1 = $bdd->query("SELECT iff FROM clients WHERE id='".$row['client']."'");
						$row1 = $back1->fetch();
						?>
				<td class="text"><?php echo $row1['iff'];?></td>
						<?php						
					}
					elseif($columns[$i] == "supplier"){
						$back1 = $bdd->query("SELECT title,code FROM suppliers WHERE id='".$row['supplier']."'");
						$row1 = $back1->fetch();
						?>
				<td><?php echo isset($row1['title'])!=""?$row1['title']." (".$row1['code'].")":"";?></td>
						<?php						
					}
					elseif($columns[$i] == "icesupplier"){
						$back1 = $bdd->query("SELECT ice FROM suppliers WHERE id='".$row['supplier']."'");
						$row1 = $back1->fetch();
						?>
				<td class="text"><?php echo $row1['ice'];?></td>
						<?php						
					}
					elseif($columns[$i] == "ifsupplier"){
						$back1 = $bdd->query("SELECT iff FROM suppliers WHERE id='".$row['supplier']."'");
						$row1 = $back1->fetch();
						?>
				<td class="text"><?php echo $row1['iff'];?></td>
						<?php						
					}
					else{
					?>
				<td><?php echo trim(strip_tags($row[$columns[$i]]),",");?></td>
					<?php
					}
				}
				?>
			</tr>
				<?php
			}
			?>
		</table>
	</body>
</html>

<?php
function sanitize_vars($var){
	if(preg_match("#script|select|update|delete|concat|create|table|union|length|show_table|mysql_list_tables|mysql_list_fields|mysql_list_dbs#i",$var)){
		$var = "";
	}
	return htmlspecialchars(addslashes(trim($var)));
}
function normalizeString($str) {
    $str = trim($str);

    // Check if wrapped in double quotes
    if (strlen($str) >= 2 && $str[0] === '"' && $str[strlen($str)-1] === '"') {
        // Remove the surrounding quotes
        return addslashes(substr($str, 1, -1));
    } else {
        // Wrap in %%
        return "%" . addslashes($str) . "%";
    }
}
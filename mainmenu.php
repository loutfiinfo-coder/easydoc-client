<div class="lx-main-menu">
	<a href="javascript:;" class="lx-mobile-menu-hide"><i class="material-icons"><img src="images/close.svg" /></i></a>
	<div class="lx-logo">
		<a href="javascript:;"><img src="<?php echo $settings['logo']=="logo.png"?"images/".$settings['logo']:"uploads/".$settings['logo'];?>" /></a>
	</div>
	<div class="lx-main-menu-scroll">
		<ul>
			<li style="<?php echo !preg_match("#Consulter Tableau de bord#",$_SESSION['easybm_roles'])?"display:none;":"";?>"><a href="index.php" class="<?php if(basename($_SERVER['PHP_SELF']) == "index.php"){echo 'active';}?>"><i class="fa fa-chart-line"></i>Tableau de bord</a></li>
			<li style="<?php echo !preg_match("#Consulter Trésorerie#",$_SESSION['easybm_roles'])?"display:none;":"";?>"><a href="payments.php" class="<?php if(basename($_SERVER['PHP_SELF']) == "payments.php"){echo 'active';}?>"><i class="fa fa-coins"></i>Trésorerie</a></li>
			<?php
			if(preg_match("#Consulter Tableau de bord|Consulter Trésorerie#",$_SESSION['easybm_roles'])){
			?>
			<li><div style="margin:10px 0px;height:20px;background:#253340;"></div></li>
			<?php
			}
			?>
			<li style="<?php echo !preg_match("#Consulter Factures clients|Consulter Notes honoraires|Consulter Notes de frais|Consulter Devis,|Consulter Propositions honoraires|Consulter Factures proforma|Consulter Bons de livraison|Consulter Bons de sortie|Consulter Bons de retour|Consulter Factures avoir#",$_SESSION['easybm_roles'])?"display:none;":"";?>">
				<?php
				$page = "404.php";
				if(preg_match("#Consulter Factures clients#",$_SESSION['easybm_roles'])){	
					$page = "factures.php";
				}
				elseif(preg_match("#Consulter Notes honoraires#",$_SESSION['easybm_roles'])){	
					$page = "nh.php";
				}
				elseif(preg_match("#Consulter Notes de frais#",$_SESSION['easybm_roles'])){	
					$page = "nf.php";
				}
				elseif(preg_match("#Consulter Devis,#",$_SESSION['easybm_roles'])){	
					$page = "devis.php";
				}
				elseif(preg_match("#Consulter Propositions honoraires#",$_SESSION['easybm_roles'])){	
					$page = "ph.php";
				}
				elseif(preg_match("#Consulter Factures proforma#",$_SESSION['easybm_roles'])){	
					$page = "facturesproforma.php";
				}
				elseif(preg_match("#Consulter Bons de livraison#",$_SESSION['easybm_roles'])){	
					$page = "bl.php";
				}
				elseif(preg_match("#Consulter Bons de sortie#",$_SESSION['easybm_roles'])){	
					$page = "bs.php";
				}
				elseif(preg_match("#Consulter Bons de retour#",$_SESSION['easybm_roles'])){	
					$page = "br.php";
				}
				elseif(preg_match("#Consulter Factures avoir#",$_SESSION['easybm_roles'])){	
					$page = "avoirs.php";
				}
				?>
				<a href="<?php echo $page;?>" class="<?php echo (preg_match("#factures.php|nh.php|nf.php|devis.php|ph.php|facturesproforma.php|bl.php|bs.php|br.php|avoirs.php#",basename($_SERVER['PHP_SELF'])) OR (preg_match("#doclabels.php#",basename($_SERVER['PHP_SELF'])) AND isset($_GET['type']) AND $_GET['type']=="client"))?'active':'';?>"><i class="fa fa-file-invoice"></i>Documents clients</a>
			</li>
			<li style="<?php echo !preg_match("#Consulter Clients#",$_SESSION['easybm_roles'])?"display:none;":"";?>"><a href="clients.php" class="<?php if(basename($_SERVER['PHP_SELF']) == "clients.php"){echo 'active';}?>"><i class="fa fa-users"></i>Clients</a></li>
			<?php
			if(preg_match("#Consulter Clients|Consulter Factures clients|Consulter Devis|Consulter Factures proforma|Consulter Bons de livraison|Consulter Bons de sortie|Consulter Bons de retour|Consulter Factures avoir#",$_SESSION['easybm_roles'])){
			?>
			<li><div style="margin:10px 0px;height:20px;background:#253340;"></div></li>
			<?php
			}
			?>
			<li style="<?php echo !preg_match("#Consulter Bons de commande|Consulter Bons de réception|Consulter Factures fournisseurs#",$_SESSION['easybm_roles'])?"display:none;":"";?>">
				<?php
				$page = "404.php";
				if(preg_match("#Consulter Factures fournisseurs#",$_SESSION['easybm_roles'])){	
					$page = "facturesuppliers.php";
				}
				elseif(preg_match("#Consulter Bons de commande#",$_SESSION['easybm_roles'])){	
					$page = "bc.php";
				}
				elseif(preg_match("#Consulter Bons de réception#",$_SESSION['easybm_roles'])){	
					$page = "bre.php";
				}
				?>
				<a href="<?php echo $page;?>" class="<?php echo (preg_match("#bc.php|bre.php|facturesuppliers.php#",basename($_SERVER['PHP_SELF'])) OR (preg_match("#doclabels.php#",basename($_SERVER['PHP_SELF'])) AND isset($_GET['type']) AND $_GET['type']=="supplier"))?'active':'';?>"><i class="fa fa-file-invoice"></i>Documents fournisseurs</a>
			</li>			
			<li style="<?php echo !preg_match("#Consulter Fournisseurs#",$_SESSION['easybm_roles'])?"display:none;":"";?>"><a href="suppliers.php" class="<?php if(basename($_SERVER['PHP_SELF']) == "suppliers.php"){echo 'active';}?>"><i class="fa fa-truck-loading"></i>Fournisseurs</a></li>
			<?php
			if(preg_match("#Consulter Fournisseurs|Consulter Bons de commande|Consulter Bons de réception|Consulter Factures fournisseurs#",$_SESSION['easybm_roles'])){
			?>
			<li><div style="margin:10px 0px;height:20px;background:#253340;"></div></li>
			<?php
			}
			?>
			<li style="<?php echo !preg_match("#Consulter Autres dépences et recettes#",$_SESSION['easybm_roles'])?"display:none;":"";?>"><a href="expenses.php" class="<?php if(basename($_SERVER['PHP_SELF']) == "expenses.php"){echo 'active';}?>"><i class="fa fa-exchange-alt" style="font-size:16px;transform:rotate(90deg);"></i>Autres dépences et recettes</a></li>
			<?php
			if(preg_match("#Consulter Autres dépences et recettes#",$_SESSION['easybm_roles'])){
			?>
			<li><div style="margin:10px 0px;height:20px;background:#253340;"></div></li>
			<?php
			}
			?>
			<li style="<?php echo !preg_match("#Consulter Prospects|Consulter Tags#",$_SESSION['easybm_roles'])?"display:none;":"";?>"><a href="prospects.php" class="<?php echo (preg_match("#prospects.php|labels.php#",basename($_SERVER['PHP_SELF'])) AND !isset($_GET['type']))?'active':'';?>"><i class="fa fa-user-tag"></i>Prospects</a></li>
			<?php
			if(preg_match("#Consulter Prospects|Consulter Tags#",$_SESSION['easybm_roles'])){
			?>
			<li><div style="margin:10px 0px;height:20px;background:#253340;"></div></li>
			<?php
			}			
			?>
			<li style="<?php echo !preg_match("#Consulter Sociétés#",$_SESSION['easybm_roles'])?"display:none;":"";?>"><a href="companies.php" class="<?php if(basename($_SERVER['PHP_SELF']) == "companies.php"){echo 'active';}?>"><i class="far fa-building"></i>Sociétés</a></li>
			<li style="<?php echo !preg_match("#Consulter Utilisateurs#",$_SESSION['easybm_roles']) AND $_SESSION['easybm_superadmin'] == "0"?"display:none;":"";?>"><a href="users.php" class="<?php if(basename($_SERVER['PHP_SELF']) == "users.php"){echo 'active';}?>"><i class="fa fa-users-cog"></i>Utilisateurs</a></li>
		</ul>
	</div>
</div>
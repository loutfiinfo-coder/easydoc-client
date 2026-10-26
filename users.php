<?php
session_start();
include("config.php");

if(!isset($_SESSION['easybm_id'])){
	header('location: login.php');
}
else{
	if(!preg_match("#Consulter Utilisateurs#",$_SESSION['easybm_roles']) AND $_SESSION['easybm_superadmin'] == "0"){
		header('location: 404.php');
	}
}

if(isset($_SESSION['easybm_id']) AND isset($_SESSION['easybm_fullname'])){
?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<title>Utilisateurs</title>
		<meta name="description" content="<?php echo $settings['store'];?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex,nofollow" />
		<!-- General CSS Settings -->
		<link rel="stylesheet" href="css/general_style.css">
		<!-- Main Style of the template -->
		<link rel="stylesheet" href="css/main_style.php?v=<?php echo rand();?>">
		<!-- Landing Page Style -->
		<link rel="stylesheet" href="css/reset_style.css">
		<!-- Awesomefont -->
		<link rel="stylesheet" href="css/fontawesome-free-5.15.4-web/css/all.css" crossorigin="anonymous">
		<!-- DateRangePicker -->
		<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
		<!-- Fav Icon -->
		<link rel="shortcut icon" href="favicon.ico">
		<?php include("onesignal.php");?>
	</head>
	<body>

		<!-- Wrapper -->
		<div class="lx-wrapper">
			<!-- Header -->
			<div class="lx-header">
				<?php include('header.php');?>
			</div>
			<!-- Main -->
			<div class="lx-main">
				<div class="lx-main-leftside">
					<?php include('mainmenu.php');?>
				</div>
				<!-- Main Content -->
				<div class="lx-main-content">
					<div class="lx-page-header">
						<h2>Utilisateurs</h2>
					</div>
					<div class="lx-clear-fix"></div>
					<?php
					if($settings['inventaire'] == "1"){
						?>
					<div class="lx-g1">
						<div class="lx-notices-item">
							<i class="fa fa-exclamation-triangle"></i>
							<p>Un inventaire de stock est lancé, vous ne pouvez pas effectuer aucune action sur l'application avant de le terminer</p>
						</div>
					</div>
						<?php
					}
					?>
					<div class="lx-page-content">
						<div class="lx-g1">
							<?php
							if(preg_match("#Ajouter Utilisateurs#",$_SESSION['easybm_roles'])){	
							?>
							<div class="lx-add-form">
								<a href="javascript:;" class="lx-new lx-new-user lx-open-popup" data-header="Ajouter un nouvel" data-title="user">+ Nouvel utilisateur</a>
							</div>
							<?php
							}
							?>
							<div class="lx-keyword">
								<fieldset style="margin-bottom:20px;padding:10px;background:#F8F8F8;">
									<legend><b>Filtre avancé:</b></legend>
									<label><span>Mot clé:</span><a href="javascript:;" class="lx-search-keyword"><i class="fa fa-search"></i></a><input type="text" autocomplete="off" name="keyword" id="keyword" placeholder="" data-table="users" /></label>
									<input type="hidden" name="sortby" value="" />
									<input type="hidden" name="orderby" value="DESC" />
									<label>
										<a href="users.php" class="lx-refresh-filter"><i class="fa fa-sync-alt"></i></a>
									</label>
								</fieldset>
							</div>
							<div class="lx-table-container">
								<div class="lx-table-shadow lx-table-shadow-left"></div>
								<div class="lx-table-shadow lx-table-shadow-right"></div>
								<div class="lx-table lx-table-users">

								</div>
							</div>
							<?php
							$nb = 50;
							if($parametres['nbrows'] != "" AND $parametres['nbrows'] != "0"){
								$nb = $parametres['nbrows'];
							}				
							?>
							<div class="lx-action-bulk">
								<label><span>Afficher: </span>
									<select name="nbrows">
										<option value="50" <?php if($nb==50){echo "selected";}?>>50</option>
										<option value="100" <?php if($nb==100){echo "selected";}?>>100</option>
										<option value="200" <?php if($nb==200){echo "selected";}?>>200</option>
										<option value="500" <?php if($nb==500){echo "selected";}?>>500</option>
									</select>
								</label><span>lignes par page</span>
							</div>
							<?php
							$back = $bdd->query("SELECT COUNT(*) AS nb FROM users WHERE trash='1'");
							$row = $back->fetch();
							?>
							<div class="lx-pagination" style="<?php if($row['nb'] <= $nb){echo "display:none;";}?>">
								<?php
								$nbpages = ceil($row['nb']/$nb);
								?>
								<ul data-table="users" data-state="1" data-start="0" data-nbpage="<?php echo $nb;?>" data-posts="<?php echo $row['nb'];?>">
									<li><span>Page <ins>1</ins> sur <abbr><?php echo $nbpages;?></abbr></span></li>
									<li><a href="javascript:;" class="previous disabled"><i class="fa fa-angle-left"></i></a></li>
									<li>
										<select id="pgnumber">
											<?php
											for($i=1;$i<=$nbpages;$i++){
												?>
											<option value="<?php echo ($i-1);?>"><?php echo $i;?></option>
												<?php
											}
											?>
										</select>
									</li>
									<li><a href="javascript:;" class="next <?php if($nbpages == 1){echo 'disabled';}?>"><i class="fa fa-angle-right"></i></a></li>
								</ul>
								<div class="lx-clear-fix"></div>
							</div>
							<div class="lx-clear-fix"></div>
						</div>
						<div class="lx-clear-fix"></div>
					</div>
					<div class="lx-clear-fix"></div>
				</div>
				<div class="lx-clear-fix"></div>
			</div>
			<!-- End Popup -->
			<div tabindex="0" class="lx-popup user">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3><span></span> utilisateur</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="usersform">
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Nom et prénom<sup>*</sup>: </span><input type="text" autocomplete="off" name="fullname" data-isnotempty="" data-message="Saisissez un nom et prénom!!" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Téléphone: </span><input type="text" autocomplete="off" name="phone" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Login<sup>*</sup>: </span><input type="text" autocomplete="off" name="email" data-isemail="" data-message="Saisissez un login" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Mot de passe<sup>*</sup>: </span><input type="password" name="password" data-isnotempty="" data-message="Saisissez un mot de passe" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Fonction<sup>*</sup>: </span><input type="text" autocomplete="off" name="job" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<span>Permissions d'accès<sup>*</sup>:</span>
											<span><label class="lx-selectall-all-roles"><input type="checkbox" name="selectall" value="" /> Tout sélectionner<del class="checkmark"></del></label></span>
											<div class="lx-table lx-table-roles">
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Sociétés à gérer</td>
													</tr>
													<tr>
														<td>
															<?php
															$back = $bdd->query("SELECT id,rs FROM companies WHERE trash='1' ORDER BY rs");
															while($row = $back->fetch()){
																?>
															<label><input type="checkbox" name="companies" value="<?php echo $row['id'];?>" /> <?php echo $row['rs'];?><del class="checkmark"></del></label> &nbsp; 
																<?php 
															}
															?>
														</td>
													</tr>
												</table>
												<label><input type="hidden" name="companiestext" value="" data-isnotempty="" data-message="Veuillez choisir au moins une société" /></label>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Page/Permission</td>
														<td>Consulter</td>
														<td>Ajouter</td>
														<td>Modifier</td>
														<td>Supprimer</td>
														<td>Exporter</td>
														<td>CA, Paiement ...</td>
														<td>Consultation de la journée en cours seulement</td>
													</tr>
													<tr>
														<td style="width:auto;white-space:nowrap;">Tableau de bord</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Tableau de bord" /><del class="checkmark"></del></label></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td><label><input type="checkbox" name="roles" value="CA Tableau de bord" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Tableau de bord" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Trésorerie</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Trésorerie" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Trésorerie" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Trésorerie" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Trésorerie" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Trésorerie" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Trésorerie" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Factures clients</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Factures clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Factures clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Factures clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Factures clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Factures clients" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Factures clients" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Notes d'honoraires</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Notes honoraires" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Notes honoraires" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Notes honoraires" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Notes honoraires" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Notes honoraires" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Notes honoraires" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Notes de frais</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Notes de frais" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Notes de frais" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Notes de frais" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Notes de frais" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter " /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Notes de frais" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Devis</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Devis" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Devis" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Devis" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Devis" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Devis" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Devis" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Propositions d'honoraires</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Propositions honoraires" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Propositions honoraires" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Propositions honoraires" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Propositions honoraires" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Propositions honoraires" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Propositions honoraires" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Factures proforma</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Factures proforma" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Factures proforma" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Factures proforma" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Factures proforma" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Factures proforma" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Factures proforma" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Bons de livraison</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Bons de livraison" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Bons de livraison" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Bons de livraison" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Bons de livraison" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Bons de livraison" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Bons de livraison" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Bons de sortie</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Bons de sortie" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Bons de sortie" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Bons de sortie" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Bons de sortie" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Bons de sortie" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Bons de sortie" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Bons de retour</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Bons de retour" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Bons de retour" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Bons de retour" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Bons de retour" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Bons de retour" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Bons de retour" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Factures avoir</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Factures avoir" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Factures avoir" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Factures avoir" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Factures avoir" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Factures avoir" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Factures avoir" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td style="width:auto;white-space:nowrap;">Tags documents</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Tags documents" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Tags documents" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Tags documents" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Tags documents" /><del class="checkmark"></del></label></td>
														<td></td>
														<td></td>
														<td></td>
													</tr>
													<tr>
														<td>Clients</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="CA Clients" /><del class="checkmark"></del></label></td>
														<td></td>
													</tr>
													<tr>
														<td>Factures fournisseurs</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Factures fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Factures fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Factures fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Factures fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Factures fournisseurs" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Factures fournisseurs" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Bons de commande</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Bons de commande" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Bons de commande" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Bons de commande" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Bons de commande" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Bons de commande" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Bons de commande" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Bons de réception</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Bons de réception" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Bons de réception" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Bons de réception" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Bons de réception" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Bons de réception" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles"data-uncheck="1" value="Consultation de la journée en cours seulement Bons de réception" /><del class="checkmark"></del></label></td>
													</tr>
													<tr>
														<td>Fournisseurs</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="CA Fournisseurs" /><del class="checkmark"></del></label></td>
														<td></td>
													</tr>
													<tr>
														<td style="width:auto;white-space:nowrap;">Prospects</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Prospects" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Prospects" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Prospects" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Prospects" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Prospects" /><del class="checkmark"></del></label></td>
														<td></td>
														<td></td>
													</tr>
													<tr>
														<td style="width:auto;white-space:nowrap;">Tags prospects</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Tags prospects" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Tags prospects" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Tags prospects" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Tags prospects" /><del class="checkmark"></del></label></td>
														<td></td>
														<td></td>
														<td></td>
													</tr>
													<tr>
														<td>Sociétés</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Sociétés" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Sociétés" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Sociétés" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Sociétés" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Sociétés" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="CA Sociétés" /><del class="checkmark"></del></label></td>
														<td></td>
													</tr>
													<tr>
														<td>Utilisateurs</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Utilisateurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Utilisateurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Utilisateurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Utilisateurs" /><del class="checkmark"></del></label></td>
														<td></td>
														<td></td>
														<td></td>
													</tr>
													<tr>
														<td style="width:auto;white-space:nowrap;">Modèles de messages factures clients</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Modèles de messages factures clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Modèles de messages factures clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Modèles de messages factures clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Modèles de messages factures clients" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Modèles de messages factures clients" /><del class="checkmark"></del></label></td>
														<td></td>
														<td></td>
													</tr>
													<tr>
														<td style="width:auto;white-space:nowrap;">Modèles de messages factures fournisseurs</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Modèles de messages factures fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Modèles de messages factures fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Modèles de messages factures fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Modèles de messages factures fournisseurs" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Modèles de messages factures fournisseurs" /><del class="checkmark"></del></label></td>
														<td></td>
														<td></td>
													</tr>
													<tr>
														<td style="width:auto;white-space:nowrap;">Modèles de messages prospects</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Modèles de messages prospects" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Modèles de messages prospects" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Modèles de messages prospects" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Modèles de messages prospects" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Modèles de messages prospects" /><del class="checkmark"></del></label></td>
														<td></td>
														<td></td>
													</tr>
													<tr>
														<td style="width:auto;white-space:nowrap;">Modèles de messages d'envoi de documents</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Modèles de messages d'envoi de documents" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Modèles de messages d'envoi de documents" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Modifier Modèles de messages d'envoi de documents" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Modèles de messages d'envoi de documents" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Exporter Modèles de messages d'envoi de documents" /><del class="checkmark"></del></label></td>
														<td></td>
														<td></td>
													</tr>
													<tr>
														<td style="width:auto;white-space:nowrap;">TVA</td>
														<td><label><input type="checkbox" name="roles" value="Consulter TVA" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter TVA" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer TVA" /><del class="checkmark"></del></label></td>
														<td></td>
														<td></td>
														<td></td>
													</tr>
													<tr>
														<td style="width:auto;white-space:nowrap;">Devises</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Devises" /><del class="checkmark"></del></label></td>
														<td><label><input type="checkbox" name="roles" value="Ajouter Devises" /><del class="checkmark"></del></label></td>
														<td></td>
														<td><label><input type="checkbox" name="roles" value="Supprimer Devises" /><del class="checkmark"></del></label></td>
														<td></td>
														<td></td>
														<td></td>
													</tr>
													<tr>
														<td>Formation</td>
														<td><label><input type="checkbox" name="roles" value="Consulter Formation" /><del class="checkmark"></del></label></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Mise à jour paiement</td>
													</tr>
													<tr>
														<td>
															<label><input type="checkbox" name="roles" value="Mise à jour paiement factures clients" /> Factures clients<del class="checkmark"></del></label>&nbsp;
															<label><input type="checkbox" name="roles" value="Mise à jour paiement factures fournisseurs" /> Factures fournisseurs<del class="checkmark"></del></label>&nbsp;
															<label><input type="checkbox" name="roles" value="Mise à jour paiement factures avoirs (remboursement)" /> Factures avoirs (remboursement)<del class="checkmark"></del></label>&nbsp;
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Suppression d'historique de paiement</td>
													</tr>
													<tr>
														<td>
															<label><input type="checkbox" name="roles" value="Suppression historique de paiement" /> Oui<del class="checkmark"></del></label>
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Mise à jour RAS TVA</td>
													</tr>
													<tr>
														<td>
															<label><input type="checkbox" name="roles" value="Mise à jour RAS TVA factures clients" /> Factures clients<del class="checkmark"></del></label>&nbsp;
															<label><input type="checkbox" name="roles" value="Mise à jour RAS TVA factures fournisseurs" /> Factures fournisseurs<del class="checkmark"></del></label>&nbsp;
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Mise à jour délai de paiement</td>
													</tr>
													<tr>
														<td>
															<label><input type="checkbox" name="roles" value="Mise à jour délai de paiement factures clients" /> Factures clients<del class="checkmark"></del></label>&nbsp;
															<label><input type="checkbox" name="roles" value="Mise à jour délai de paiement factures fournisseurs" /> Factures fournisseurs<del class="checkmark"></del></label>&nbsp;
														</td>
													</tr>
												</table>	
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Modification date opération</td>
													</tr>
													<tr>
														<td>
															<label><input type="checkbox" name="roles" value="Modification date opération" /> Oui<del class="checkmark"></del></label>
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Transformation / Dupplication documents</td>
													</tr>
													<tr>
														<td>
															<label><input type="checkbox" name="roles" value="Transformation / Dupplication documents" /> Oui<del class="checkmark"></del></label>
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Impression des documents avec cachet et signature</td>
													</tr>
													<tr>
														<td>
															<label><input type="checkbox" name="roles" value="Impression des documents avec cachet et signature" /> Oui<del class="checkmark"></del></label>
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Transformation groupée des documents</td>
													</tr>
													<tr>
														<td>
															<label><input type="checkbox" name="roles" value="Transformation groupée des documents" /> Oui<del class="checkmark"></del></label>
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Messages (E-mail / WhatsApp)</td>
													</tr>
													<tr>
														<td>
															<label><input type="checkbox" name="roles" value="Envoi des messages" /> Envoi<del class="checkmark"></del></label>&nbsp;
															<label><input type="checkbox" name="roles" value="Consultation historique des messages" /> Consultation<del class="checkmark"></del></label>&nbsp;
															<label><input type="checkbox" name="roles" value="Suppression historique des messages" /> Suppression<del class="checkmark"></del></label>
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Consultation seulement les prospects d'utilisateur</td>
													</tr>
													<tr>
														<td>
															<label><input type="checkbox" name="roles" value="Consulter seulement ses prospects" /> Oui<del class="checkmark"></del></label>
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Notifications</td>
													</tr>
													<tr>
														<td>
															<label><input type="checkbox" name="roles" value="Consultation des notifications" /> Consultation des notifications<del class="checkmark"></del></label>
															<label><input type="checkbox" name="roles" value="Réglage des notifications" /> Réglage des notifications<del class="checkmark"></del></label>
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0">
													<tr>
														<td>Téléchargement Backup</td>
													</tr>
													<tr>
														<td>
															<label><input type="checkbox" name="roles" value="Télécharger Backup" /> Oui<del class="checkmark"></del></label>
														</td>
													</tr>
												</table>
												<label><input type="hidden" name="rolestext" value="" /></label>
											</div>
										</div>	
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="id" value="0" />
											<a href="javascript:;" class="">Enregistrer</a>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Popup -->
			<div tabindex="0" class="lx-popup emails">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Modifier email</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="emailsform">										
										<div class="lx-textfield lx-g1 lx-pb-0">
											<div class="lx-accordion">
												<a href="javascript:;">Guide de configuration <i class="fa fa-angle-down"></i></a>
												<div class="lx-accordion-quide">
														<div class="tabs">
															<div class="tab active" data-tab="gmail">Gmail</div>
															<div class="tab" data-tab="office">Office 365</div>
															<div class="tab" data-tab="yahoo">Yahoo Mail</div>
															<div class="tab" data-tab="icloud">iCloud</div>
															<div class="tab" data-tab="zoho">Zoho Mail</div>
															<div class="tab" data-tab="custom">Domaine perso</div>
														</div>
														
														<div id="gmail" class="tab-content active">
															<h2>Gmail / Google Workspace</h2>
															<p><strong>Activer 2FA :</strong> <a href="https://myaccount.google.com/security" target="_blank">https://myaccount.google.com/security</a></p>
															<p><strong>Mot de passe d’application :</strong> <a href="https://myaccount.google.com/apppasswords" target="_blank">https://myaccount.google.com/apppasswords</a></p>
															<table>
																<tr><th>Paramètre</th><th>Valeur</th></tr>
																<tr><td>SMTP Host</td><td>smtp.gmail.com</td></tr>
																<tr><td>Port</td><td>587</td></tr>
																<tr><td>SMTPSecure</td><td>TLS (STARTTLS)</td></tr>
															</table>
														</div>

														<div id="office" class="tab-content">
															<h2>Outlook / Office 365 / Microsoft 365</h2>
															<p><strong>Activer 2FA :</strong> <a href="https://account.microsoft.com/security" target="_blank">https://account.microsoft.com/security</a></p>
															<p><strong>Mot de passe d’application :</strong> <a href="https://mysignins.microsoft.com/security-info" target="_blank">https://mysignins.microsoft.com/security-info</a></p>
															<table>
																<tr><th>Paramètre</th><th>Valeur</th></tr>
																<tr><td>SMTP Host</td><td>smtp.office365.com</td></tr>
																<tr><td>Port</td><td>587</td></tr>
																<tr><td>SMTPSecure</td><td>TLS (STARTTLS)</td></tr>
															</table>
														</div>

														<div id="yahoo" class="tab-content">
															<h2>Yahoo Mail</h2>
															<p><strong>Activer 2FA :</strong> <a href="https://login.yahoo.com/account/security" target="_blank">https://login.yahoo.com/account/security</a></p>
															<p><strong>Mot de passe d’application :</strong> Sur la même page de sécurité.</p>
															<table>
																<tr><th>Paramètre</th><th>Valeur</th></tr>
																<tr><td>SMTP Host</td><td>smtp.mail.yahoo.com</td></tr>
																<tr><td>Port</td><td>587</td></tr>
																<tr><td>SMTPSecure</td><td>TLS (STARTTLS)</td></tr>
															</table>
														</div>

														<div id="icloud" class="tab-content">
															<h2>iCloud / Apple Mail</h2>
															<p><strong>Activer 2FA :</strong> <a href="https://appleid.apple.com/" target="_blank">https://appleid.apple.com/</a></p>
															<p><strong>Mot de passe d’application :</strong> Dans Sécurité → Mots de passe spécifiques aux apps</p>
															<table>
																<tr><th>Paramètre</th><th>Valeur</th></tr>
																<tr><td>SMTP Host</td><td>smtp.mail.me.com</td></tr>
																<tr><td>Port</td><td>587</td></tr>
																<tr><td>SMTPSecure</td><td>TLS (STARTTLS)</td></tr>
															</table>
														</div>

														<div id="zoho" class="tab-content">
															<h2>Zoho Mail</h2>
															<p><strong>Activer 2FA :</strong> <a href="https://accounts.zoho.com/u/h#security/tfa" target="_blank">https://accounts.zoho.com/u/h#security/tfa</a></p>
															<p><strong>Mot de passe d’application :</strong> Section Mots de passe spécifiques aux applications</p>
															<table>
																<tr><th>Paramètre</th><th>Valeur</th></tr>
																<tr><td>SMTP Host</td><td>smtp.zoho.com</td></tr>
																<tr><td>Port</td><td>587</td></tr>
																<tr><td>SMTPSecure</td><td>TLS (STARTTLS)</td></tr>
															</table>
														</div>

														<div id="custom" class="tab-content">
															<h2>Domaine personnalisé (OVH, Hostinger, GoDaddy…)</h2>
															<p><strong>Activer 2FA :</strong> Selon l’hébergeur, souvent pas nécessaire.</p>
															<p><strong>Mot de passe :</strong> Utilisez le mot de passe habituel.</p>
															<table>
																<tr><th>Paramètre</th><th>Valeur</th></tr>
																<tr><td>SMTP Host</td><td>smtp.votrehebergeur.com</td></tr>
																<tr><td>Port</td><td>587</td></tr>
																<tr><td>SMTPSecure</td><td>TLS (STARTTLS)</td></tr>
															</table>
														</div>
												</div>
											</div>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Société: </span><input type="text" autocomplete="off" name="companytxt" readonly /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>E-mail<sup>*</sup>: </span><input type="text" autocomplete="off" name="email" data-isemail="" data-message="Saisissez un email !!" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Password<sup>*</sup>: </span><input type="password" autocomplete="off" name="password" data-ispassword="" data-message="Saisissez un password !!" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Host<sup>*</sup>: </span><input type="text" autocomplete="off" name="host" data-isnotempty="" data-message="Saisissez un host !!" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>SMTPSecure<sup>*</sup>: </span>
												<select name="smtpsecure" data-isnotempty="" data-message="Choisissez une option !!">
													<option value=""></option>
													<option value="STARTTLS">STARTTLS / tls</option>
													<option value="SMTPS">SMTPS / ssl</option>
													<option value="false">False</option>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Port<sup>*</sup>: </span><input type="text" autocomplete="off" name="port" data-isnumber="" data-message="Saisissez un port !!" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="user" value="0" />
											<input type="hidden" name="company" value="0" />
											<input type="hidden" name="id" value="0" />
											<a href="javascript:;" class="">Enregistrer</a>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Popup -->
			<div tabindex="0" class="lx-popup export">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Export utilisateurs</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="exportform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="email" data-title="Email" checked /> Email<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="password" data-title="Mot de passe" checked /> Mot de passe<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="fullname" data-title="Nom et prénom" checked /> Nom et prénom<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="phone" data-title="Téléphone" checked /> Téléphone<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="type" data-title="Type" checked /> Type<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="roles" data-title="Permission" checked /> Permission<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="table" value="users" />
											<a href="javascript:;" class="">Télécharger</a>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Popup -->	
			<div tabindex="0" class="lx-popup resetpassword">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Réinitialiser mot de passe</h3>
								</div>
								<div class="lx-add-form">
									<div class="lx-delete-box">
										<p>Voulez vous vraiment réinitialiser le mot de passe de cet utilisateur?</p>
										<a href="javascript:;" class="lx-reset-pass" data-id="">Oui</a>
										<a href="javascript:;" class="lx-cancel-delete">Non</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Popup -->	
			<div tabindex="0" class="lx-popup deleterecord">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Confirmation suppression</h3>
								</div>
								<div class="lx-add-form">
									<div class="lx-delete-box">
										<p>Voulez vous vraiment supprimer cet utilisateur?</p>
										<a href="javascript:;" class="lx-delete-record" data-action="deleteuser" data-id="">Oui</a>
										<a href="javascript:;" class="lx-cancel-delete">Non</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- JQuery Script -->
		<script src="js/jquery-1.12.4.min.js"></script>
		<!-- Popup Script -->
		<script src="js/jquery.popup.js"></script>
		<!-- Calendar Script -->
		<script src="js/moment.min.js"></script>
		<script src="js/daterangepicker.js"></script>
		<!-- Main Script -->
		<script src="js/script.js?v=<?php echo rand();?>"></script>
		<script>
			$(document).ready(function(){
				loadUsers($(".lx-pagination ul").attr("data-state"));
			});

			const tabs = document.querySelectorAll('.tab');
			const contents = document.querySelectorAll('.tab-content');

			tabs.forEach(tab => {
				tab.addEventListener('click', () => {
					tabs.forEach(t => t.classList.remove('active'));
					contents.forEach(c => c.classList.remove('active'));

					tab.classList.add('active');
					document.getElementById(tab.dataset.tab).classList.add('active');
				});
			});
		</script>
	</body>
</html>
<?php
DB_Sanitize();
}
?>
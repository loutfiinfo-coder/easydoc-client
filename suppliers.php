<?php
session_start();
include("config.php");

if(!isset($_SESSION['easybm_id'])){
	header('location: login.php');
}
else{
	if(!preg_match("#Consulter Fournisseurs#",$_SESSION['easybm_roles']) OR $settings['onlyproduct'] == "0"){	
		header('location: 404.php');
	}
}

if(isset($_SESSION['easybm_id']) AND isset($_SESSION['easybm_fullname'])){
?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<title>Fournisseurs</title>
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
		<script src="js/tinymce/tinymce.min.js"></script>
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
						<h2>Fournisseurs</h2>
					</div>
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
					<div class="lx-clear-fix"></div>
					<div class="lx-page-content">
						<div class="lx-g1">
							<?php
							if(preg_match("#Ajouter Fournisseurs#",$_SESSION['easybm_roles'])){	
							?>
							<div class="lx-add-form">
								<a href="javascript:;" class="lx-new lx-new-supplier lx-open-popup" data-header="Ajouter un nouveau" data-title="supplier">+ Nouveau fournisseur</a>
								<a href="javascript:;" class="lx-new lx-open-popup" data-title="importer"><i class="fa fa-upload"></i> Importer</a>
							</div>
							<?php
							}
							?>
							<div class="lx-keyword">
								<fieldset style="margin-bottom:20px;padding:10px;background:#F8F8F8;">
									<legend><b>Filtre avancé:</b></legend>
									<label><span>Mot clé:</span><a href="javascript:;" class="lx-search-keyword"><i class="fa fa-search"></i></a><input type="text" autocomplete="off" name="keyword" id="keyword" placeholder="" data-table="clients" /></label>
									<label class="lx-advanced-select">
										<span>Affecté à la société:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="company" id="company" placeholder="" data-ids="" title="Société" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" autocomplete="off" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
												<?php
												$back = $bdd->query("SELECT id,rs FROM companies WHERE trash='1'".$companiesid." ORDER BY rs");
												while($row = $back->fetch()){
													?>
												<li><label><input type="checkbox" value="<?php echo $row['rs'];?>" data-ids="<?php echo $row['id'];?>" /> <?php echo $row['rs'];?><del class="checkmark"></del></label></li>
													<?php
												}
												?>
											</ul>
										</div>
									</label>
									<label class="lx-advanced-select">
										<span>État de paiement:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="paid" id="paid" title="État de paiement" placeholder="" data-ids="" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" autocomplete="off" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
												<li><label><input type="checkbox" value="100% Payée" data-ids="paid" /> 100% Payée<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="En cours" data-ids="encours" /> En cours<del class="checkmark"></del></label></li>
											</ul>
										</div>
									</label>
									<?php
									$daterangerecall = "";
									$datestartrecall = "";
									$dateendrecall = "";	
									if(isset($_GET['datestartrecall']) AND isset($_GET['dateendrecall'])){
										$daterangerecall = $_GET['datestartrecall']." - ".$_GET['dateendrecall'];
										$datestartrecall = $_GET['datestartrecall'];
										$dateendrecall = $_GET['dateendrecall'];											
									}									
									?>
									<label><span>Date rappel:</span><input type="text" autocomplete="off" name="daterecall" id="daterecall" value="<?php echo $daterangerecall;?>" title="Date rappel" placeholder="" readonly style="background:white;cursor:pointer;" /></label>
									<input type="hidden" name="datestartrecall" id="datestartrecall" value="<?php echo $datestartrecall;?>" />
									<input type="hidden" name="dateendrecall" id="dateendrecall" value="<?php echo $dateendrecall;?>" />
									<label class="lx-advanced-select">
										<span>Utilisateurs:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="user" id="user" placeholder="" data-ids="" title="Utilisateur" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" autocomplete="off" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
												<?php
												$back = $bdd->query("SELECT id,fullname FROM users WHERE trash='1'".(str_replace("company","companies",$multicompanies))." ORDER BY fullname");
												while($row = $back->fetch()){
													?>
												<li><label><input type="checkbox" value="<?php echo $row['fullname'];?>" data-ids="<?php echo $row['id'];?>" /> <?php echo $row['fullname'];?><del class="checkmark"></del></label></li>
													<?php
												}
												?>
											</ul>
										</div>
									</label>
									<input type="hidden" name="sortby" value="" />
									<input type="hidden" name="orderby" value="DESC" />
									<label>
										<a href="suppliers.php" class="lx-refresh-filter"><i class="fa fa-sync-alt"></i></a>
									</label>
								</fieldset>
							</div>
							<div class="lx-table-container">
								<div class="lx-caisse-total lx-caisse-total-1"></div>
								<div class="lx-caisse-total lx-caisse-total-2"></div>
								<div class="lx-caisse-total lx-caisse-total-4"></div>
								<div class="lx-table-shadow lx-table-shadow-left"></div>
								<div class="lx-table-shadow lx-table-shadow-right"></div>
								<div class="lx-table lx-table-suppliers">

								</div>
							</div>
							<?php
							$nb = 50;
							if($parametres['nbrows'] != "" AND $parametres['nbrows'] != "0"){
								$nb = $parametres['nbrows'];
							}				
							?>
							<div class="lx-action-bulk">
								<?php
								if(preg_match("#Exporter Fournisseurs#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-new lx-open-popup" data-title="export"><i class="fa fa-download"></i> Exporter</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<?php
								}
								?>
								<label><span>Afficher: </span>
									<select name="nbrows"><option value="50" <?php if($nb==50){echo "selected";}?>>50</option>
										<option value="100" <?php if($nb==100){echo "selected";}?>>100</option>
										<option value="200" <?php if($nb==200){echo "selected";}?>>200</option>
										<option value="500" <?php if($nb==500){echo "selected";}?>>500</option>
									</select>
								</label><span>lignes par page</span>
							</div>
							<?php
							$back = $bdd->query("SELECT COUNT(*) AS nb FROM suppliers WHERE trash='1'");
							$row = $back->fetch();
							?>
							<div class="lx-pagination" style="<?php if($row['nb'] <= $nb){echo "display:none;";}?>">
								<?php
								$nbpages = ceil($row['nb']/$nb);
								?>
								<ul data-table="suppliers" data-state="1" data-start="0" data-nbpage="<?php echo $nb;?>" data-posts="<?php echo $row['nb'];?>">
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
			<div tabindex="0" class="lx-popup supplier">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3><span></span> fournisseur</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="suppliersform">
										<div class="lx-textfield lx-g2 lx-g lx-pb-0">
											<label><span>Fournisseur<sup>*</sup>: </span><input type="text" autocomplete="off" name="title" data-isnotempty="" data-message="Saisissez un fournisseur!!" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-g lx-pb-0">
											<label><span>Code fournisseur: </span><input type="text" autocomplete="off" name="codefo" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label class="lx-advanced-select"><span>Affecté à la société<sup>*</sup>:</span>
												<i class="fa fa-caret-down" style="top:22px;"></i>
												<?php
												$back = $bdd->query("SELECT id,rs FROM companies WHERE trash='1'".$companiesid." ORDER BY rs");
												$idcompany = "";
												$namecompany = "";
												if($back->rowCount() == 1){
													$row = $back->fetch();
													$idcompany = $row['id'];
													$namecompany = $row['rs'];
												}
												?>
												<input type="text" autocomplete="off" name="company" value="<?php echo $namecompany;?>" data-one="<?php echo $back->rowCount();?>" placeholder="Choisissez une société" data-isnotempty="" data-message="Choisissez une société" data-ids="<?php echo $idcompany;?>" readonly />
												<div>
													<input type="text" autocomplete="off" name="searchadvanced" style="margin-bottom:20px;" />
													<ul>
														<?php
														$back = $bdd->query("SELECT id,rs FROM companies WHERE trash='1'".$companiesid." ORDER BY rs");
														while($row = $back->fetch()){
															?>
														<li style="margin-bottom:10px;"><label><input type="checkbox" value="<?php echo $row['rs'];?>" data-ids="<?php echo $row['id'];?>" <?php echo $idcompany!=""?"checked":"";?> /> <?php echo $row['rs'];?><del class="checkmark"></del></label></li>
															<?php
														}
														?>
													</ul>
												</div>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Responsable: </span><input type="text" autocomplete="off" name="respname" data-isnotempty="" data-message="Saisissez un responsable!!" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Téléphone: </span><input type="text" autocomplete="off" name="respphone" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>WhatsApp: </span><input type="text" autocomplete="off" name="whatsapp" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Email: </span><input type="text" autocomplete="off" name="respemail" data-isemail="" data-message="Saisissez un email valid!!" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Fax: </span><input type="text" autocomplete="off" name="respfax" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>ICE: </span><input type="text" autocomplete="off" name="ice" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>IF: </span><input type="text" autocomplete="off" name="iff" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>RC: </span><input type="text" autocomplete="off" name="rc" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Adresse: </span><input type="text" autocomplete="off" name="address" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Note: </span><textarea name="note" /></textarea></label>
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
			<div tabindex="0" class="lx-popup recall">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Mise à jour rappel</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="recallform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Date: </span><input type="text" name="recalldate" data-isnotempty="" data-message="Saisissez une date" /></label>
										</div>	
										<div class="lx-clear-fix"></div>										
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Note: </span><textarea name="note" data-isnotempty="" data-message="Saisissez une note"></textarea></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="table" value="suppliers" />
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
			<div tabindex="0" class="lx-popup importer">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Importation fournisseurs</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="importsuppliersform" enctype="multipart/form-data">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<p><b>Important: </b>Veuillez impoter un fichier excel avec les colonnes suivantes en respectant l'ordre (Fournisseur (Obligatoire / Unique) - ICE - IF - RC - Responsable - Téléphone - WhatsApp - Adresse - Email - Fax - Délai de paiement - Note - IDs sociétés affectés séparer par point virgule ";" (Obligatoire)) ou bien <a href="ExampleSuppliers.xlsx" style="display:inline;padding:0px;font-weight:normal;background:none;color:#39add1;box-shadow:0px 0px 0px #FFFFFF;">télécharger un modèle</a></p>
											<br />
											<p><b>NB: </b>Les fournisseurs existants seront automatiquement ignorés</p>
											<br />
											<div class="lx-importer">
												<input type="file" name="xlsfile" id="importsupplier" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
												<span>Choisissez un fichier (excel)</span>
											</div>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="id" value="0" />
											<a href="javascript:;">Importer</a>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Popup -->
			<div tabindex="0" class="lx-popup regularity">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Mise à jour régularité fiscale</h3>
								</div>
								<div class="lx-add-form">
									<style>
										#regularityform td{
											font-weight:600;
										}
									</style>
									<form autocomplete="off" action="#" method="post" id="regularityform">										
										<div class="lx-table lx-table-notifications lx-g1" style="overflow:visible;">
											<table cellpadding="0">
												<tr>
													<td style="width:50%;background:#EEEEEE;border:1px solid #DDDDDD;"><span>Statut<sup style="color:#FF0000;">*</sup>:</span></td>
													<td style="width:50%;background:#FFFFFF;border:1px solid #DDDDDD;">
														<div class="lx-textfield lx-g1-f">
															<label style="display:block;">
																<select name="status" data-isnotempty="" data-message="Choisissez un statut" style="padding:10px;">
																	<option value=""></option>
																	<option value="Personne physique">Personne physique</option>
																	<option value="Personne morale">Personne morale</option>
																</select>
															</label>
														</div>															
													</td>
												</tr>
												<tr>
													<td style="width:50%;background:#EEEEEE;border:1px solid #DDDDDD;"><span>Type d'activité:</span></td>
													<td style="width:50%;background:#FFFFFF;border:1px solid #DDDDDD;">
														<div class="lx-textfield lx-g1-f">
															<label style="display:block;">
																<select name="type" class="todropdown" data-isnotempty="" data-message="Choisissez un type" style="padding:10px;">
																	<option value=""></option>
																	<?php
																	$back = $bdd->query("SELECT DISTINCT type FROM regularity WHERE type<>'' ORDER BY type");
																	while($row = $back->fetch()){
																		?>
																	<option value="<?php echo $row['type'];?>"><?php echo $row['type'];?></option>
																		<?php
																	}
																	?>
																</select>
															</label>
														</div>															
													</td>
												</tr>
												<tr>
													<td style="width:50%;background:#EEEEEE;border:1px solid #DDDDDD;"><span>RAS applicable?<sup style="color:#FF0000;">*</sup>:</span></td>
													<td style="width:50%;background:#FFFFFF;border:1px solid #DDDDDD;">
														<div class="lx-textfield lx-g1-f">
															<label style="display:block;">
																<select name="appras" data-isnotempty="" data-message="Choisissez un option" style="padding:10px;">
																	<option value=""></option>
																	<option value="Oui">Oui</option>
																	<option value="Non">Non</option>
																</select>
															</label>
														</div>
													</td>
												</tr>
												<tr class="appras" style="display:none;">
													<td rowspan="5" style="width:50%;background:#EEEEEE;border:1px solid #DDDDDD;"><span>Document régularité fiscale:</span></td>
													<td style="width:50%;background:#FFFFFF;border:1px solid #DDDDDD;">
														<div class="lx-textfield lx-g1-f">
															<label style="display:block;"><span>Régularité fiscale<sup>*</sup>:</span>
																<select name="doc" data-isnotempty="" data-message="Choisissez un option" style="padding:10px;">
																	<option value=""></option>
																	<option value="Avec">Avec</option>
																	<option value="Sans">Sans</option>
																</select>
															</label>
														</div>
													</td>
												</tr>
												<tr>
													<td class="avec" style="display:none;width:50%;background:#FFFFFF;border:1px solid #DDDDDD;">
														<div class="lx-textfield lx-g1-f lx-pb-0">
															<label style="display:block;"><span>Document: </span></label>
															<input type="file" name="medias1" id="medias1" style="top:15px;width:140px;height:40px;" accept="image/x-png,image/jpeg,.pdf,.xls,.xlsx,.doc,.docx,.txt" />
															<input type="hidden" name="attachments" value="" data-isnotempty="" data-message="Upload an fichier!!" />
															<a href="javascript:;" class="lx-upload-picture" style="background:#F8F8F8;color:#242424;border:1px solid #EEEEEE;"><i class="fa fa-file"></i> Uploder document</a>
															<i class="fa fa-check" style="display:none;color:#7EC855;"></i>
														</div>
													</td>
												</tr>
												<tr>
													<td class="avec" style="display:none;width:50%;background:#FFFFFF;border:1px solid #DDDDDD;">
														<div class="lx-textfield lx-g1-f">
															<label style="display:block;"><span>Date demande<sup>*</sup>: </span><input type="text" name="datedoc" data-isnotempty="" data-message="Saisissez une date!!" /></label>
														</div>
													</td>
												</tr>
												<tr>
													<td class="avec" style="display:none;width:50%;background:#FFFFFF;border:1px solid #DDDDDD;">
														<div class="lx-textfield lx-g1-f">
															<label style="display:block;"><span>Période de validité en jours<sup>*</sup>: </span><input type="text" name="jdoc" data-isnumber="" data-message="Saisissez une période!!" /></label>
														</div>
													</td>
												</tr>
												<tr>
													<td class="avec sans" style="display:none;width:50%;background:#FFFFFF;border:1px solid #DDDDDD;">
														<div class="lx-textfield lx-g1-f">
															<label style="display:block;"><span>% TVA à retenir<sup>*</sup>: </span><input type="text" name="percentage" data-isnumber="" data-message="Saisissez un percentage!!" /></label>
														</div>
													</td>
												</tr>
											</table>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="supplier" value="0" />
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
			<div tabindex="0" class="lx-popup delay">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Modifier délai</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="delayform">										
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Délai en jours<sup>*</sup>: </span><input type="text" autocomplete="off" name="delay" data-isnumber="" data-message="Saisissez un délai" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="table" value="suppliers" />
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
			<div tabindex="0" class="lx-popup supplierbankaccount">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3><span></span> compte banquaire</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="suppliersbankaccountsform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label>
												<span>Type<sup>*</sup>: </span>
												<select name="type" data-isnotempty="" data-message="Choisissez une option">
													<option value="">Choisissez une option</option>
													<option value="in">Local</option>
													<option value="out">Étranger</option>
												</select>
											</label>
										</div>										
										<div class="lx-textfield lx-g1 lx-pb-0  lx-in lx-out">
											<label>
												<span>La banque<sup>*</sup>: </span>
												<select name="bank" class="todropdown" data-isnotempty="" data-message="Saisissez une banque">
													<option value="">Saisissez une banque</option>
													<?php
													$back = $bdd->query("SELECT DISTINCT bank FROM suppliersbankaccounts");
													while($row = $back->fetch()){
														?>
													<option value="<?php echo $row['bank']?>"><?php echo $row['bank']?></option>
														<?php
													}
													?>
												</select>
											</label>
										</div>
										<div class="lx-textfield lx-g1 lx-pb-0 lx-in">
											<label><span>RIB<sup>*</sup>: </span><input type="text" autocomplete="off" name="rib" data-isnotempty="" data-message="Saisissez un RIB" /></label>
										</div>
										<div class="lx-textfield lx-g1 lx-pb-0 lx-out">
											<label><span>IBAN<sup>*</sup>: </span><input type="text" autocomplete="off" name="iban" data-isnotempty="" data-message="Saisissez un IBAN" /></label>
										</div>
										<div class="lx-textfield lx-g1 lx-pb-0 lx-out">
											<label><span>SWIFT<sup>*</sup>: </span><input type="text" autocomplete="off" name="swift" data-isnotempty="" data-message="Saisissez un SWIFT" /></label>
										</div>
										<div class="lx-textfield lx-g1 lx-pb-0 lx-out">
											<label><span>Adresse banque<sup>*</sup>: </span><input type="text" autocomplete="off" name="address" data-isnotempty="" data-message="Saisissez une adresse" /></label>
										</div>
	
										<div class="lx-textfield lx-g1 lx-pb-0 lx-in">
											<label><span>Agence: </span><input type="text" autocomplete="off" name="agency" /></label>
										</div>			
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="supplier" value="0" />
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
									<h3>Export Fournisseurs</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="exportform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="code" data-title="Référence" checked /> Référence<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="title" data-title="Fournisseur" checked /> Fournisseur<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="codefo" data-title="Code fournisseur" checked /> Code fournisseur<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="respname" data-title="Responsable" checked /> Responsable<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="company" data-title="Affecté à la société" checked /> Affecté à la société<del class="checkmark"></del></label>
										</div>																
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="address" data-title="Adresse" checked /> Adresse<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="ice" data-title="ICE" checked /> ICE<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="iff" data-title="IF" checked /> IF<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="rc" data-title="RC" checked /> RC<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="respphone" data-title="Téléphone" checked /> Téléphone<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="whatsapp" data-title="WhatsApp" checked /> WhatsApp<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="respemail" data-title="Email" checked /> Email<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="respfax" data-title="Fax" checked /> Fax<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="note" data-title="Note" checked /> Note<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="delay" data-title="Délai de paiement" checked /> Délai de paiement<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="bankaccounts" data-title="Comptes banquaires" checked /> Comptes banquaires<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="regularity" data-title="Régularité fiscale	" checked /> Régularité fiscale	<del class="checkmark"></del></label>
										</div>
										<?php
										if(preg_match("#CA Fournisseurs#",$_SESSION['easybm_roles'])){
										?>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="ca" data-title="Chiffre d'affaires TTC" checked /> Chiffre d'affaires TTC<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="paid" data-title="Payé TTC" checked /> Payé TTC<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="encours" data-title="En cours TTC" checked /> En cours TTC<del class="checkmark"></del></label>
										</div>
										<?php
										}
										?>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="user" data-title="Utilisateur" checked /> Utilisateur<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="table" value="suppliers" />
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
										<p>Voulez vous vraiment supprimer ce fournisseur?</p>
										<a href="javascript:;" class="lx-delete-record" data-action="deletesupplier" data-id="">Oui</a>
										<a href="javascript:;" class="lx-cancel-delete">Non</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Popup -->	
			<div tabindex="0" class="lx-popup deleterecord1">
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
										<p>Voulez vous vraiment supprimer ce compte banquaire?</p>
										<a href="javascript:;" class="lx-delete-record" data-action="deletesupplierbankaccount" data-id="">Oui</a>
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
				loadSuppliers($(".lx-pagination ul").attr("data-state"));
				toDropDown();
			});
			$(function() {
				$('input[name="datedoc"]').daterangepicker({
					locale: {
					  format: 'DD/MM/YYYY'
					},
					singleDatePicker: true,
					showDropdowns: true,
					autoUpdateInput: false
				}).on('apply.daterangepicker', function(ev, picker) {
					$(this).val(picker.startDate.format('DD/MM/YYYY'));
				});

			$('input[name="daterecall"]').daterangepicker({
					"locale": {
						"format": "MM/DD/YYYY",
						"separator": " - ",
						"applyLabel": "Appliquer",
						"cancelLabel": "Annuler",
						"fromLabel": "De",
						"toLabel": "à",
						"customRangeLabel": "Custom",
						"daysOfWeek": [
							"Di",
							"Lu",
							"Ma",
							"Me",
							"Je",
							"Ve",
							"Sa"
						],
						"monthNames": [
							"Janvier",
							"Février",
							"Mars",
							"Avril",
							"Mai",
							"Juin",
							"Juillet",
							"Août",
							"Septembre",
							"Octobere",
							"Novembre",
							"Decembre"
						],
						"firstDay": 1
					},
					ranges: {
						'Aujourd\'hui': [moment(), moment()],
						'Hier': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
						'Derniers 7 Jours': [moment().subtract(6, 'days'), moment()],
						'Derniers 30 Jours': [moment().subtract(29, 'days'), moment()],
						'Ce mois': [moment().startOf('month'), moment().endOf('month')],
						'Le mois dernier': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
					},
					"linkedCalendars": false,
					"autoUpdateInput": false,
					"showCustomRangeLabel": false,
					"alwaysShowCalendars": true
					}, function(start, end, label) {
						$('input[name="daterecall"]').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
						$('input[name="datestartrecall"]').val(start.format('DD/MM/YYYY'));
						$('input[name="dateendrecall"]').val(end.format('DD/MM/YYYY'));
						loadSuppliers($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="daterecall"]').on('apply.daterangepicker', function(ev, picker) {
					$('input[name="daterecall"]').val(picker.startDate.format('DD/MM/YYYY') + " - " + picker.endDate.format('DD/MM/YYYY'));
					$('input[name="datestartrecall"]').val(picker.startDate.format('DD/MM/YYYY'));
					$('input[name="dateendrecall"]').val(picker.endDate.format('DD/MM/YYYY'));
					loadSuppliers($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="daterecall"]').on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
					$('input[name="datestartrecall"]').val('');
					$('input[name="dateendrecall"]').val('');
					loadSuppliers($(".lx-pagination ul").attr("data-state"));
				});				
				
				// Add an event listener to the "Today" button
				var activeDateInput = null;

				// Track the last clicked date input
				$('body').on('show.daterangepicker', 'input[type="text"].date', function() {
					activeDateInput = $(this);
				});

				// Listen for the "Today" button click
				$('body').on('click', '.daterangepicker .ranges li:first-child', function() {
					if (!activeDateInput) {
						console.log("No active input field detected.");
						return;
					}

					// Get today's date
					var today = moment();

					// Apply the date selection to the active input
					activeDateInput.data('daterangepicker').setStartDate(today);
					activeDateInput.data('daterangepicker').setEndDate(today);

					// Update the input field value
					activeDateInput.val(today.format('DD/MM/YYYY') + ' - ' + today.format('DD/MM/YYYY'));

					// Determine and update the corresponding hidden fields
					var name = activeDateInput.attr('name');
						
					if (name === 'daterecall') {
						$('input[name="datestartrecall"]').val(today.format('DD/MM/YYYY'));
						$('input[name="dateendrecall"]').val(today.format('DD/MM/YYYY'));
					} 

					// Trigger the "Apply" button to update the date range
					activeDateInput.data('daterangepicker').clickApply();

					// Reload commands if necessary
					loadSuppliers($(".lx-pagination ul").attr("data-state"));
				});		
				$('input[name="recalldate"]').daterangepicker({
					locale: {
					  format: 'DD/MM/YYYY'
					},
					singleDatePicker: true,
					showDropdowns: true,
					autoUpdateInput: false
				}).on('apply.daterangepicker', function(ev, picker) {
					$(this).val(picker.startDate.format('DD/MM/YYYY'));
				});				
			});
		</script>
	</body>
</html>
<?php
DB_Sanitize();
}
?>
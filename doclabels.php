<?php
session_start();
include("config.php");

if(!isset($_SESSION['easybm_id'])){
	header('location: login.php');
}
else{
	if(!preg_match("#Consulter Tags documents#",$_SESSION['easybm_roles'])){
		header('location: 404.php');
	}
}

if(isset($_SESSION['easybm_id']) AND isset($_SESSION['easybm_fullname'])){
?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<title>Tags documents</title>
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
						<h2>Tags documents</h2>
					</div>
					<div class="lx-clear-fix"></div>
					<div class="lx-tabs">
						<?php
						if(isset($_GET['type'])){
							if($_GET['type'] == "client"){
								if(preg_match("#Consulter Factures clients#",$_SESSION['easybm_roles'])){	
									?>
						<a href="factures.php">Factures clients</a>
									<?php
								}
								if(preg_match("#Consulter Notes honoraires#",$_SESSION['easybm_roles'])){	
									?>
						<a href="nh.php">Notes d'honoraires</a>
									<?php
								}
								if(preg_match("#Consulter Notes de frais#",$_SESSION['easybm_roles'])){	
									?>
						<a href="nf.php">Notes de frais</a>
									<?php
								}
								if(preg_match("#Consulter Devis,#",$_SESSION['easybm_roles'])){	
									?>
						<a href="devis.php">Devis</a>
									<?php
								}
								if(preg_match("#Consulter Propositions honoraires#",$_SESSION['easybm_roles'])){	
									?>
						<a href="ph.php">Propositions d'honoraires</a>
									<?php
								}
								if(preg_match("#Consulter Factures avoir#",$_SESSION['easybm_roles'])){	
									?>
						<a href="avoirs.php">Factures avoir</a>
									<?php
								}
								if(preg_match("#Consulter Bons de retour#",$_SESSION['easybm_roles'])){	
									?>
						<a href="br.php">Bons de retour</a>
									<?php
								}
								if(preg_match("#Consulter Factures proforma#",$_SESSION['easybm_roles'])){
									?>
						<a href="facturesproforma.php">Factures proforma</a>
									<?php
								}
								if(preg_match("#Consulter Bons de livraison#",$_SESSION['easybm_roles'])){	
									?>
						<a href="bl.php">Bons de livraison</a>
									<?php
								}
								if(preg_match("#Consulter Bons de sortie#",$_SESSION['easybm_roles'])){	
									?>
						<a href="bs.php">Bons de sortie</a>
									<?php
								}
							}
							if($_GET['type'] == "supplier"){
								if(preg_match("#Consulter Factures fournisseurs#",$_SESSION['easybm_roles'])){	
									?>
						<a href="facturesuppliers.php">Factures fournisseurs</a>
									<?php
								}
								if(preg_match("#Consulter Bons de commande#",$_SESSION['easybm_roles'])){	
									?>
						<a href="bc.php">Bons de commande</a>
									<?php
								}
								if(preg_match("#Consulter Bons de réception#",$_SESSION['easybm_roles'])){	
									?>
						<a href="bre.php">Bons de réception</a>
									<?php
								}
							}
						}
						if(preg_match("#Consulter Tags documents#",$_SESSION['easybm_roles'])){	
							?>
						<a href="doclabels.php?type=<?php echo isset($_GET['type'])?$_GET['type']:"client";?>" class="active">Tags</a>
							<?php
						}
						?>
					</div>
					<div class="lx-page-content">
						<div class="lx-g1">
							<?php
							if(preg_match("#Ajouter Tags documents#",$_SESSION['easybm_roles'])){	
							?>
							<div class="lx-add-form">
								<a href="javascript:;" class="lx-new lx-new-doclabel lx-open-popup" data-header="Ajouter un nouveau" data-title="doclabel">+ Nouveau tag</a>
							</div>
							<?php
							}
							?>
							<div class="lx-keyword">
								<fieldset style="margin-bottom:20px;padding:10px;background:#F8F8F8;">
									<legend><b>Filtre avancé:</b></legend>
									<label><span>Mot clé:</span><a href="javascript:;" class="lx-search-keyword"><i class="fa fa-search"></i></a><input type="text" autocomplete="off" name="keyword" id="keyword" placeholder="" data-table="doclabels" /></label>
									<label class="lx-advanced-select">
										<span>Affecté à la société:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="company" id="company" placeholder="" value="<?php echo isset($_SESSION['companynames'])?$_SESSION['companynames']:'';?>" data-ids="<?php echo isset($_SESSION['companyids'])?$_SESSION['companyids']:'';?>" title="Société" readonly />
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
												<li><label><input type="checkbox" value="<?php echo $row['rs'];?>" data-ids="<?php echo $row['id'];?>" <?php echo isset($_SESSION['companyids'])?(in_array($row['id'],explode(",",$_SESSION['companyids']))?'checked':''):'';?> /> <?php echo $row['rs'];?><del class="checkmark"></del></label></li>
													<?php
												}
												?>
											</ul>
										</div>
									</label>
									<input type="hidden" name="type" id="type" value="<?php echo isset($_GET['type'])?$_GET['type']:"client";?>" />
									<input type="hidden" name="sortby" value="" />
									<input type="hidden" name="orderby" value="DESC" />
									<label>
										<a href="doclabels.php?type=<?php echo isset($_GET['type'])?$_GET['type']:"client";?>" class="lx-refresh-filter"><i class="fa fa-sync-alt"></i></a>
									</label>
								</fieldset>
							</div>
							<div class="lx-table-container">
								<div class="lx-table-shadow lx-table-shadow-left"></div>
								<div class="lx-table-shadow lx-table-shadow-right"></div>
								<div class="lx-table lx-table-doclabels">

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
									<select name="nbrows"><option value="50" <?php if($nb==50){echo "selected";}?>>50</option>
										<option value="100" <?php if($nb==100){echo "selected";}?>>100</option>
										<option value="200" <?php if($nb==200){echo "selected";}?>>200</option>
										<option value="500" <?php if($nb==500){echo "selected";}?>>500</option>
									</select>
								</label><span>lignes par page</span>
							</div>
							<?php
							$back = $bdd->query("SELECT COUNT(*) AS nb FROM doclabels WHERE trash='1'");
							$row = $back->fetch();
							?>
							<div class="lx-pagination" style="<?php if($row['nb'] <= $nb){echo "display:none;";}?>">
								<?php
								$nbpages = ceil($row['nb']/$nb);
								?>
								<ul data-table="doclabels" data-state="1" data-start="0" data-nbpage="<?php echo $nb;?>" data-posts="<?php echo $row['nb'];?>">
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
			<div tabindex="0" class="lx-popup doclabel">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3><span></span> tag</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="doclabelsform">
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
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Tag<sup>*</sup>: </span><input type="text" autocomplete="off" name="label" data-isnotempty="" data-message="Saisissez un tag!!" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Couleur<sup>*</sup>: </span><input type="color" autocomplete="off" name="color" data-isphone="" data-message="Choisissez une couleur!!" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="id" value="0" />
											<input type="hidden" name="type" value="<?php echo isset($_GET['type'])?$_GET['type']:"client";?>" />
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
									<h3>Export tag</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="exportform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="doclabel" data-title="Tag" checked /> Tag<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="color" data-title="Couleur" checked /> Couleur<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="table" value="doclabels" />
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
										<p>Voulez vous vraiment supprimer ce tag?</p>
										<a href="javascript:;" class="lx-delete-record" data-action="deletedoclabel" data-id="">Oui</a>
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
				loadDocLabels($(".lx-pagination ul").attr("data-state"));
			});
		</script>
	</body>
</html>
<?php
DB_Sanitize();
}
?>
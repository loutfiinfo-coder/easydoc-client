<?php
session_start();
include("config.php");

if(!isset($_SESSION['easybm_id'])){
	header('location: login.php');
}
else{
	if(!preg_match("#Consulter Trésorerie#",$_SESSION['easybm_roles'])){	
		header('location: 404.php');
	}
}

if(isset($_SESSION['easybm_id']) AND isset($_SESSION['easybm_fullname'])){
?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<title>Trésorerie</title>
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
		<link rel="stylesheet" href="css/ion.rangeSlider.min.css"/>
		<!-- Fav Icon -->
		<link rel="shortcut icon" href="favicon.ico">
		<?php include("onesignal.php");?>
		<script src="js/tinymce/tinymce.min.js"></script>
		<script>
			tinymce.init({
			  selector: 'textarea:not(.no-tinymce)',
			  height: 200,
			  menubar: false,
			  plugins: [
				'advlist autolink lists link image charmap print preview anchor',
				'searchreplace visualblocks code fullscreen',
				'insertdatetime media table paste code help wordcount'
			  ],
			  toolbar: 'undo redo | ' +
			  'bold italic underline | fontsizeselect | forecolor | alignleft aligncenter ' +
			  'alignright alignjustify | bullist numlist outdent indent | ' +
			  'removeformat',fontsize_formats: "8px 10px 12px 14px 16px 18px 24px 36px",
			  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
			});
		</script>
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
						<h2>Trésorerie</h2>
					</div>
					<div class="lx-clear-fix"></div>
					<div class="lx-page-content">
						<div class="lx-g1">
							<?php
							if(preg_match("#Modifier Trésorerie#",$_SESSION['easybm_roles'])){	
							?>
							<div class="lx-add-form">
								<a href="javascript:;" class="lx-new lx-new-expense lx-open-popup" data-header="Ajouter une nouvelle" data-title="expense">+ Nouvelle dépense / encaissement</a>
								<a href="javascript:;" class="lx-new lx-bulk-remise">Remise multiple</a>
								<a href="javascript:;" class="lx-new lx-bulk-remise-real lx-open-popup" data-title="remisebulk" style="display:none;"></a>
								<a href="javascript:;" class="lx-new lx-bulk-caisse">Rapprochement multiple</a>
								<a href="javascript:;" class="lx-new lx-bulk-caisse-real lx-open-popup" data-title="caissebulk" style="display:none;"></a>
							</div>
							<?php
							}
							?>
							<div class="lx-keyword">
								<fieldset style="margin-bottom:20px;padding:10px;background:#F8F8F8;">
									<legend><b>Filtre avancé:</b></legend>
									<label><span>Mot clé:</span><a href="javascript:;" class="lx-search-keyword"><i class="fa fa-search"></i></a><input type="text" autocomplete="off" name="keyword" id="keyword" placeholder="Réf, libellé ..." data-table="caisse" /></label>
									<label class="lx-advanced-select">
										<span>Affecté à la sociétés:</span>
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
										<span>Natures:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="type" id="type" value="<?php echo isset($_GET['type'])?str_replace("||",",",$_GET['type']):"";?>" placeholder="" data-ids="<?php echo isset($_GET['type'])?$_GET['type']:"";?>" title="Nature" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" autocomplete="off" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
												<li><label><input type="checkbox" value="Encaissement depuis facture" data-ids="Entrée depuis facture" <?php echo isset($_GET['type'])?($_GET['type']=="Entrée depuis facture"?"checked":""):"";?> /> Encaissement depuis facture<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Encaissement manuelle" data-ids="Entrée manuelle" <?php echo isset($_GET['type'])?($_GET['type']=="Entrée manuelle"?"checked":""):"";?> /> Encaissement manuelle<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Dépense depuis facture" data-ids="Sortie depuis facture" <?php echo isset($_GET['type'])?($_GET['type']=="Sortie depuis facture"?"checked":""):"";?> /> Dépense depuis facture<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Dépense manuelle" data-ids="Sortie manuelle" <?php echo isset($_GET['type'])?($_GET['type']=="Sortie manuelle"?"checked":""):"";?> /> Dépense manuelle<del class="checkmark"></del></label></li>											
											</ul>
										</div>
									</label>
									<label class="lx-advanced-select">
										<span>Mode de paiement:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="modepayment" id="modepayment" title="Mode de paiement" value="<?php echo isset($_GET['modepayment'])?str_replace("||",",",$_GET['modepayment']):"";?>" placeholder="" data-ids="<?php echo isset($_GET['modepayment'])?$_GET['modepayment']:"";?>" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" autocomplete="off" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
												<li><label><input type="checkbox" value="Espèce" data-ids="Espèce" <?php echo isset($_GET['modepayment'])?(in_array("Espèce", explode("||",$_GET['modepayment']))?"checked":""):"";?> /> Espèce<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Chèque" data-ids="Chèque" <?php echo isset($_GET['modepayment'])?(in_array("Chèque", explode("||",$_GET['modepayment']))?"checked":""):"";?> /> Chèque<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Effet" data-ids="Effet" <?php echo isset($_GET['modepayment'])?(in_array("Effet", explode("||",$_GET['modepayment']))?"checked":""):"";?> /> Effet<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Virement" data-ids="Virement" <?php echo isset($_GET['modepayment'])?(in_array("Virement", explode("||",$_GET['modepayment']))?"checked":""):"";?> /> Virement<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="TPE" data-ids="TPE" <?php echo isset($_GET['modepayment'])?(in_array("TPE", explode("||",$_GET['modepayment']))?"checked":""):"";?> /> TPE<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="En attente de paiement" data-ids="En attente de paiement" <?php echo isset($_GET['modepayment'])?(in_array("En attente de paiement", explode("||",$_GET['modepayment']))?"checked":""):"";?> /> En attente de paiement<del class="checkmark"></del></label></li>
											</ul>
										</div>
									</label>
									<label class="lx-advanced-select">
										<span>Statut de paiement:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="status" id="status" title="Statut de paiement" value="<?php echo isset($_GET['status'])?str_replace("||",",",$_GET['status']):"";?>" placeholder="" data-ids="<?php echo isset($_GET['statusid'])?$_GET['statusid']:"";?>" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" autocomplete="off" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
												<li><label><input type="checkbox" value="Réglements reçus non encaissés" data-ids="a1a" <?php echo isset($_GET['statusid'])?(in_array("a1a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Réglements reçus non encaissés<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Réglements émis non décaissés" data-ids="a2a" <?php echo isset($_GET['statusid'])?(in_array("a2a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Réglements émis non décaissés<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Réglements reçus non échus" data-ids="a9a" <?php echo isset($_GET['statusid'])?(in_array("a9a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Réglements reçus non échus <del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Réglements émis non échus" data-ids="a13a" <?php echo isset($_GET['statusid'])?(in_array("a13a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Réglements émis non échus <del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Réglements sans date d'écheance" data-ids="a10a" <?php echo isset($_GET['statusid'])?(in_array("a10a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Réglements sans date d'écheance<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Réglements reçus échus non encaissés" data-ids="a4a" <?php echo isset($_GET['statusid'])?(in_array("a4a", explode("||",$_GET['statusid']))?"checked":""):"";?>/> Réglements reçus échus non encaissés<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Réglements émis échus non décaissés" data-ids="a5a" <?php echo isset($_GET['statusid'])?(in_array("a5a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Réglements émis échus non décaissés<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Chèques / Effets échus non remis" data-ids="a3a" <?php echo isset($_GET['statusid'])?(in_array("a3a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Chèques / Effets (reçus) échus non remis<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Chèques / Effets / Espèce (reçus) non remis" data-ids="a14a" <?php echo isset($_GET['statusid'])?(in_array("a14a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Chèques / Effets / Espèce (reçus) non remis<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Chèques / Effets / Espèce (reçus) remis" data-ids="a6a" <?php echo isset($_GET['statusid'])?(in_array("a6a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Chèques / Effets / Espèce (reçus) remis<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Réglements reçus encaissés" data-ids="a7a" <?php echo isset($_GET['statusid'])?(in_array("a7a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Réglements reçus encaissés<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Réglements émis décaissés" data-ids="a12a" <?php echo isset($_GET['statusid'])?(in_array("a12a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Réglements émis décaissés<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Chèques / Effets impayés" data-ids="a8a" <?php echo isset($_GET['statusid'])?(in_array("a8a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Chèques / Effets impayés<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Exclure les impayés" data-ids="a11a" <?php echo isset($_GET['statusid'])?(in_array("a11a", explode("||",$_GET['statusid']))?"checked":""):"";?> /> Exclure les impayés<del class="checkmark"></del></label></li>
											</ul>
										</div>
									</label>
									<label class="lx-advanced-select">
										<span>Clients:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="client" value="" id="client" placeholder="" data-ids="" data-values="" title="Client" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" placeholder="Rechercher ..." autocomplete="off" data-table="clients" data-value="fullname" data-ids="id" data-text="fullname,code" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
											</ul>
										</div>
									</label>
									<label class="lx-advanced-select">
										<span>Fournisseurs:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="supplier" value="" id="supplier" placeholder="" data-ids="" data-values="" title="Fournisseur" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" placeholder="Rechercher ..." autocomplete="off" data-table="suppliers" data-value="title" data-ids="id" data-text="title,code" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
											</ul>
										</div>
									</label>
									<label class="lx-advanced-select">
										<span>Imputation comptable:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="imputation" id="imputation" placeholder="" data-ids="" title="Imputation comptable" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" autocomplete="off" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
												<?php
												$back = $bdd->query("SELECT DISTINCT imputation FROM payments WHERE imputation<>'' ORDER BY imputation");
												while($row = $back->fetch()){
													?>
												<li><label><input type="checkbox" value="<?php echo $row['imputation'];?>" data-ids="<?php echo $row['imputation'];?>" /> <?php echo $row['imputation'];?><del class="checkmark"></del></label></li>
													<?php
												}
												?>
											</ul>
										</div>
									</label>
									<label class="lx-advanced-select">
										<span>Comptes banquaires:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="rib" id="rib" placeholder="" data-ids="" title="Compte banquaire" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" autocomplete="off" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
												<?php
												$back = $bdd->query("SELECT * FROM bankaccounts WHERE 1=1".$multicompanies);
												while($row = $back->fetch()){
													?>
												<li><label><input type="checkbox" value="<?php echo $row['bank']." | ".$row['rib'];?>" data-ids="<?php echo $row['id'];?>" /> <?php echo $row['bank']." | ".$row['rib'];?><del class="checkmark"></del></label></li>
													<?php
												}
												?>
											</ul>
										</div>
									</label>
									<?php
									$styleday = "";
									$rangedateplaceholder = "Date création";
									$rangedate = "";
									$startdate = "";
									$enddate = "";
									if(preg_match("#Consultation de la journée en cours seulement Trésorerie#",$_SESSION['easybm_roles'])){
										$styleday = "display:none;";
										$rangedate = gmdate("d/m/Y")." - ".gmdate("d/m/Y");
										$rangedateplaceholder = "Date création";
										$startdate = gmdate("d/m/Y");
										$enddate = gmdate("d/m/Y");	
									}
									elseif(isset($_GET['datestart']) AND isset($_GET['dateend'])){
										$rangedate = $_GET['datestart']." - ".$_GET['dateend'];
										$startdate = $_GET['datestart'];
										$enddate = $_GET['dateend'];											
									}

									$rangedatedue = "";
									$startdatedue = "";
									$enddatedue = "";									
									if(isset($_GET['dateduestart']) AND isset($_GET['datedueend'])){
										$rangedatedue = $_GET['dateduestart']." - ".$_GET['datedueend'];
										$startdatedue = $_GET['dateduestart'];
										$enddatedue = $_GET['datedueend'];											
									}
									?>
									<label style="<?php echo $styleday;?>"><span>Date création:</span><input type="text" autocomplete="off" name="dateadd" id="dateadd" title="Date création" value="<?php echo $rangedate;?>" placeholder="" readonly style="background:white;cursor:pointer;" class="date" /></label>
									<input type="hidden" name="datestart" id="datestart" value="<?php echo $startdate;?>" />
									<input type="hidden" name="dateend" id="dateend" value="<?php echo $enddate;?>" />
									<label><span>Date d'écheance:</span><input type="text" autocomplete="off" name="datedue" id="datedue" title="Date d'écheance" data-table="jcaisse" value="<?php echo $rangedatedue;?>" placeholder="" readonly style="background:white;cursor:pointer;" class="date" /></label>
									<input type="hidden" name="dateduestart" id="dateduestart" value="<?php echo $startdatedue;?>" />
									<input type="hidden" name="datedueend" id="datedueend" value="<?php echo $enddatedue;?>" />
									<label><span>Date de remmis:</span><input type="text" autocomplete="off" name="dateremis" id="dateremis" data-table="jcaisse" value="" title="Date de remise" placeholder="" readonly style="background:white;cursor:pointer;" class="date" /></label>
									<input type="hidden" name="dateremisstart" id="dateremisstart" value="" />
									<input type="hidden" name="dateremisend" id="dateremisend" value="" />
									<label><span>Date valeur:</span><input type="text" autocomplete="off" name="datepaid" id="datepaid" data-table="jcaisse" value="" title="Date valeur (Rapprochement)" placeholder="" readonly style="background:white;cursor:pointer;" class="date" /></label>
									<input type="hidden" name="datepaidstart" id="datepaidstart" value="" />
									<input type="hidden" name="datepaidend" id="datepaidend" value="" />
									<input type="hidden" name="sortby" value="" />
									<input type="hidden" name="orderby" value="DESC" />
									<label class="lx-advanced-select">
										<span>À comptabiliser:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="invoiced" id="invoiced" value="" placeholder="" data-ids="" title="À comptabiliser" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" autocomplete="off" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
												<li><label><input type="checkbox" value="Oui" data-ids="Oui" /> Oui<del class="checkmark"></del></label></li>
												<li><label><input type="checkbox" value="Non" data-ids="Non" /> Non<del class="checkmark"></del></label></li>
											</ul>
										</div>
									</label>
									<label class="lx-advanced-select">
										<span>Utilisateur:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="worker" id="worker" placeholder="" data-ids="" title="Utilisateur" readonly />
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
									<br />
									<fieldset style="display:inline-block;padding:0px 10px;">
										<legend>Prix TTC</legend>
										<label style="margin-right:0px;margin-bottom:5px;" class="lx-price-range">
											<input type="text" class="js-range-slider" name="my_range" value="" />
											<input type="hidden" id="pricemin" />
											<input type="hidden" id="pricemax" />
											<a href="javascript:;" onclick="loadCaisse('1')" class="lx-price-filter">Appliquer</a>
										</label>
									</fieldset>
									<label>
										<a href="payments.php" class="lx-refresh-filter" style="margin-left:10px;"><i class="fa fa-sync-alt"></i></a>
									</label>
								</fieldset>
							</div>
							<div class="lx-table-container">
								<div class="lx-caisse-total lx-caisse-total-1"></div>
								<div class="lx-table-shadow lx-table-shadow-left"></div>
								<div class="lx-table-shadow lx-table-shadow-right"></div>
								<div class="lx-table lx-table-caisse">

								</div>
								<div class="lx-caisse-total lx-caisse-total-2"></div>
								<div class="lx-caisse-total-3"></div>
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
							$back = $bdd->query("SELECT COUNT(*) AS nb FROM payments WHERE trash='1'");
							$row = $back->fetch();
							?>
							<div class="lx-pagination" style="<?php if($row['nb'] <= $nb){echo "display:none;";}?>">
								<?php
								$nbpages = ceil($row['nb']/$nb);
								?>
								<ul data-table="caisse" data-state="1" data-start="0" data-nbpage="<?php echo $nb;?>" data-posts="<?php echo $row['nb'];?>">
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
			<div tabindex="0" class="lx-popup expense">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3><span></span> dépense / encaissement</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="expenseform">
										<div class="lx-textfield lx-g1 lx-pb-0" style="<?php echo !preg_match("#Modification date opération#",$_SESSION['easybm_roles'])?"display:none;":"";?>">
											<label><span>Date de création: </span><input type="text" name="dateaddcommand" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Affectée à la société<sup>*</sup>:</span>
												<select name="company" class="lx-companies-list" data-isnumber="" data-message="Choisissez une société!!">
													<?php
													$back = $bdd->query("SELECT id,rs FROM companies WHERE trash='1'".$companiesid." ORDER BY rs");
													if($back->rowCount() > 1){
													?>
													<option value="">Choisissez une société</option>
													<?php
													}
													while($row = $back->fetch()){
														?>
													<option value="<?php echo $row['id'];?>"><?php echo $row['rs'];?></option>
														<?php 
													}
													?>
												</select>
											</label>
										</div>											
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Nature<sup>*</sup>:</span>
												<select name="type" data-isnotempty="" data-message="Choisissez un nature!!">
													<option value="">Choisissez un type</option>
													<option value="Entrée">Encaissement</option>
													<option value="Sortie">Dépense</option>
												</select>
											</label>
										</div>	
										<div class="lx-textfield lx-g2 lx-pb-0 lx-invoiced-yesno">
											<label><span>À comptabiliser<sup>*</sup>:</span>
												<select name="invoiced" data-isnotempty="" data-message="Choisissez une option!!">
													<option value=""></option>
													<option value="Oui">Oui</option>
													<option value="Non">Non</option>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0" style="display:none;">
											<label><span>Catégorie:</span>
												<select name="nature" class="todropdown">
													<option value="">Choisissez une catégorie</option>
													<?php
													$back = $bdd->query("SELECT DISTINCT nature,company FROM payments WHERE nature<>''".$multicompanies." ORDER BY nature");
													while($row = $back->fetch()){
														?>
													<option value="<?php echo $row['nature'];?>" data-company="<?php echo $row['company'];?>"><?php echo $row['nature'];?></option>
														<?php 
													}
													?>
												</select>
											</label>
										</div>	
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Libellé<sup>*</sup>: </span><input type="text" autocomplete="off" name="title" data-isnotempty="" data-message="Saisissez un libellé!!" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Mode de paiement<sup>*</sup>:</span>
												<select name="modepayment" data-isnotempty="" data-message="Choisissez un mode de paiement!">
													<option value="">Choisissez un mode de paiement</option>
													<option value="Espèce">Espèce</option>
													<option value="Chèque">Chèque</option>
													<option value="Effet">Effet</option>
													<option value="Virement">Virement</option>
													<option value="TPE">TPE</option>
												</select>
												<input type="hidden" name="modepayment" />
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-clientsupplier lx-g1 lx-pb-0 lx-pt-0">
											<fieldset class="lx-client-expense" style="margin-top:15px;">
												<legend>Client</legend>
												<div class="lx-textfield lx-g1 lx-pb-0 lx-client-expense">
													<label style="display:inline-block;"><input type="radio" name="newclient" value="1" checked style="position:relative;top:2px;transform:scale(1.3);margin-right:10px;" /> Existant<u class="circlemark"></u></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
													<label style="display:inline-block;"><input type="radio" name="newclient" value="0" style="position:relative;top:2px;transform:scale(1.3);margin-right:10px;" /> Nouveau<u class="circlemark"></u></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
													<div class="lx-clear-fix" style="margin-top:5px;margin-bottom:20px;"></div>
													<label><input type="hidden" name="existclient" value="" data-isnotempty="" data-message="Choisissez une option" /></label>
												</div>												
												<div class="lx-textfield lx-g3 lx-pt-0 lx-client-expense" style="display:none;">
													<label data-message="Veuillez d'abord choisir une société"><span>Client <sup style="color:#FF0000;">*</sup>: </span>
														<select name="client" class="todropdown" data-isnotempty="" data-message="Veuillez choisir un client de la liste!!">
															<option value="">Choisissez un client</option>
															<?php
															$back = $bdd->query("SELECT id,fullname,code,phone,email,company FROM clients WHERE trash='1' AND fullname<>'' ORDER BY fullname");
															while($row = $back->fetch()){
																?>
															<option value="<?php echo $row['id'];?>" data-phone="<?php echo $row['phone'];?>" data-email="<?php echo $row['email'];?>" data-company="<?php echo $row['company'];?>"><?php echo $row['fullname']." (".$row['code'].")";?></option>
																<?php 
															}
															?>
														</select>
													</label>
												</div>
												<div class="lx-textfield lx-g3 lx-pt-0">
													<label><span>Téléphone: </span><input type="text" autocomplete="off" name="phoneclient" /></label>
												</div>
												<div class="lx-textfield lx-g3 lx-pt-0">
													<label><span>Email: </span><input type="text" autocomplete="off" name="emailclient" data-isemail="" data-message="Veuillez saisir un email valid!!" /></label>
												</div>
											</fieldset>
											<fieldset class="lx-supplier-expense" style="margin-top:15px;">
												<legend>Fournisseur</legend>
												<div class="lx-textfield lx-g1 lx-pb-0 lx-supplier-expense">
													<label style="display:inline-block;"><input type="radio" name="newsupplier" value="1" checked style="position:relative;top:2px;transform:scale(1.3);margin-right:10px;" /> Existant<u class="circlemark"></u></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
													<label style="display:inline-block;"><input type="radio" name="newsupplier" value="0" style="position:relative;top:2px;transform:scale(1.3);margin-right:10px;" /> Nouveau<u class="circlemark"></u></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
													<div class="lx-clear-fix" style="margin-top:5px;margin-bottom:20px;"></div>
													<label><input type="hidden" name="existsupplier" value="" data-isnotempty="" data-message="Choisissez une option" /></label>
												</div>	
												<div class="lx-textfield lx-g3 lx-pt-0 lx-supplier-expense" style="display:none;">
													<label data-message="Veuillez d'abord choisir une société"><span>Fournisseur <sup style="color:#FF0000;">*</sup>: </span>
														<select name="supplier" class="todropdown" data-isnotempty="" data-message="Veuillez choisir un fournisseur de la liste!!">
															<option value="">Choisissez un fournisseur</option>
															<?php
															$back = $bdd->query("SELECT id,title,code,respphone,respemail,company FROM suppliers WHERE trash='1' AND title<>'' ORDER BY title");
															while($row = $back->fetch()){
																?>
															<option value="<?php echo $row['id'];?>" data-phone="<?php echo $row['respphone'];?>" data-email="<?php echo $row['respemail'];?>" data-company="<?php echo $row['company'];?>"><?php echo $row['title']." (".$row['code'].")";?></option>
																<?php 
															}
															?>
														</select>
													</label>
												</div>
												<div class="lx-textfield lx-g3 lx-pt-0">
													<label><span>Téléphone: </span><input type="text" autocomplete="off" name="phonesupplier" /></label>
												</div>
												<div class="lx-textfield lx-g3 lx-pt-0">
													<label><span>Email: </span><input type="text" autocomplete="off" name="emailsupplier" data-isemail="" data-message="Veuillez saisir un email valid!!" /></label>
												</div>
											</fieldset>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0 lx-supplier-bankaccount" style="display:none;">
											<label><span>Compte banquaire fournisseur:<sup>*</sup></span>
												<select name="supplierbankaccount" data-isnotempty="" data-message="Veuillez choisir un compte banquaire!!">
													<option value="">Choisissez un compte banquaire</option>
													<?php
													$req = "SELECT * FROM suppliersbankaccounts";
													$back = $bdd->query($req);
													while($row = $back->fetch()){
														if($row['location'] == "in"){
														?>
													<option value="<?php echo $row['id'];?>" data-location="in" data-supplier="<?php echo $row['supplier'];?>"><?php echo $row['bank']." | ".$row['rib'];?></option>
														<?php 
														}
														if($row['location'] == "out"){
														?>
													<option value="<?php echo $row['id'];?>" data-location="out" data-supplier="<?php echo $row['supplier'];?>"><?php echo $row['bank']." | ".$row['iban']." | ".$row['swift'];?></option>
														<?php 
														}
													}
													?>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label data-message="Veuillez d'abord choisir une société"><span>Compte banquaire société<sup class="lx-company-account-ob" style="display:none;">*</sup>:</span>
												<select name="rib" class="lx-bankaccounts-list" data-isnotempty="" data-message="Choisissez un compte banquaire!!">
													<option value="0">Choisissez un compte banquaire</option>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0 lx-price-in">
											<label><span>Montant en <?php echo $settings['currency'];?> TTC<sup>*</sup>: </span><input type="text" autocomplete="off" name="price" data-isnumber="" data-message="Saisissez un montant!!" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0 countinprofit">
											<label><span>Taux de TVA<sup>*</sup>:</span>
												<select name="tva" data-isnotempty="" data-message="Choisissez un taux de TVA!">
													<option value=""></option>
													<?php
													$back = $bdd->query("SELECT * FROM tvas ORDER BY tva");
													while($row = $back->fetch()){
														?>
													<option value="<?php echo $row['tva'];?>%"><?php echo $row['tva'];?>%</option>
														<?php
													}
													?>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0 lx-price-out" style="display:none;">
											<label><span>Montant en Devise TTC<sup>*</sup>: </span><input type="text" autocomplete="off" name="price2" data-isnumber="" data-message="Saisissez un montant!!" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0 lx-price-out" style="display:none;">
											<label><span>Devise<sup>*</sup>: </span>
												<select name="currency" data-isnotempty="" data-message="Choisissez une devise!!">
													<option value="">Choisissez une devise</option>
													<?php
													$back = $bdd->query("SELECT * FROM currencys ORDER BY title");
													while($row = $back->fetch()){
														?>
													<option value="<?php echo $row['title'];?>"><?php echo $row['title'];?></option>
														<?php
													}
													?>
												</select>												
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g3 lx-pb-0 lx-remis" style="display:none;">
											<label><span style="color:#FFFFFF;">Remis: </span></label>
											<label style="padding:10px 10px;font-weight:bold;border:1px solid #39add1;color:#39add1;border-radius:6px;"><input type="checkbox" name="remis" value="0" /> Marquer comme rémis<del class="checkmark" style="top:8px;left:8px;"></del></label>
										</div>
										<div class="lx-textfield lx-g3 lx-pb-0 lx-remis" style="display:none;">
											<label><span>Date remis: </span><input type="text" name="dateremiscommand" data-isnotempty="" data-message="Choisissez une date!!" /></label>
										</div>
										<div class="lx-textfield lx-g3 lx-pb-0 lx-remis" style="display:none;">
											<label><span>N° de remise: </span><input type="text" autocomplete="off" name="nremise" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0 lx-umpaid" style="display:none;">
											<label style="padding:10px 10px;font-weight:bold;border:1px solid #FF0000;color:#FF0000;border-radius:6px;"><input type="checkbox" name="paid" value="0" /> Marquer comme impayé<del class="checkmark" style="top:8px;left:8px;"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Date d'écheance: </span><input type="text" name="dateduecommand" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Date valeur (Rapprochement): </span><input type="text" name="datepaidcommand" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Imputation comptable:</span>
												<select name="imputation" class="todropdown">
													<option value="">Saisissez une imputation comptable</option>
													<?php
													$back = $bdd->query("SELECT DISTINCT imputation FROM payments WHERE imputation<>'' ORDER BY imputation");
													while($row = $back->fetch()){
														?>
													<option value="<?php echo $row['imputation'];?>"><?php echo $row['imputation'];?></option>
														<?php 
													}
													?>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Description: </span><textarea type="text" name="description" id="description"></textarea></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="page1" value="expense" />
											<input type="hidden" name="page" value="payments" />
											<input type="hidden" name="id" value="0" />
											<a href="javascript:;" class="">Enregistrer</a>
											<br />
											<br />
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Popup -->
			<div tabindex="0" class="lx-popup caisse">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Modification paiement / remboursement</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="caisseform">										
										<div class="lx-textfield lx-g3 lx-pb-0">
											<label><span>Référence: </span><input type="text" name="code1" readonly /></label>
										</div>
										<div class="lx-textfield lx-g3 lx-pb-0">
											<label><span>Société: </span><input type="text" name="companytxt" readonly /></label>
										</div>
										<div class="lx-textfield lx-g3 lx-pb-0">
											<label><span>Mode de paiement: </span><input type="text" name="modepayment" readonly /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div style="margin:20px;margin-bottom:10px;border-top:1px dashed #BEBEBE;"></div>
										<div class="lx-textfield lx-g1 lx-pb-0 lx-pt-0">
											<label><strong class="lx-caisse-type" style="font-style:italic;display:block;text-align:center;"></strong></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div style="margin:20px;margin-top:10px;border-top:1px dashed #BEBEBE;"></div>
										<div class="lx-textfield lx-g3 lx-pb-0 lx-cheque lx-remis">
											<label><span style="color:#FFFFFF;">Remis: </span></label>
											<label style="padding:10px 10px;font-weight:bold;border:1px solid #39add1;color:#39add1;border-radius:6px;"><input type="checkbox" name="remis" value="0" /> Marquer comme rémis<del class="checkmark" style="top:8px;left:8px;"></del></label>
										</div>	
										<div class="lx-textfield lx-g3 lx-pb-0 lx-cheque lx-remis">
											<label><span>Date remis: </span><input type="text" name="dateremiscommand" data-isnotempty="" data-message="Choisissez une date!!" /></label>
										</div>
										<div class="lx-textfield lx-g3 lx-pb-0 lx-cheque lx-remis">
											<label><span>N° de remise: </span><input type="text" autocomplete="off" name="nremise" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0 lx-cheque">
											<label style="padding:10px 10px;font-weight:bold;border:1px solid #FF0000;color:#FF0000;border-radius:6px;"><input type="checkbox" name="paid" value="0" /> Marquer comme impayé<del class="checkmark" style="top:8px;left:8px;"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0" style="<?php echo !preg_match("#Modification date opération#",$_SESSION['easybm_roles'])?"display:none;":"";?>">
											<label><span>Date d'écheance: </span><input type="text" name="dateduecommand" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0" style="<?php echo !preg_match("#Modification date opération#",$_SESSION['easybm_roles'])?"display:none;":"";?>">
											<label><span>Date valeur (Rapprochement): </span><input type="text" name="datepaidcommand" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Compte banquaire société:</span>
												<select name="rib" class="lx-bankaccounts-list">
													<option value="0">Choisissez un compte banquaire</option>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0 lx-supplier-bankaccount" style="display:none;">
											<label><span>Compte banquaire fournisseur:</span>
												<select name="supplierbankaccount" data-isnotempty="" data-message="Veuillez choisir un compte banquaire!!">

												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Imputation comptable:</span>
												<select name="imputation" class="todropdown">
													<option value="">Saisissez une imputation comptable</option>
													<?php
													$back = $bdd->query("SELECT DISTINCT imputation FROM payments WHERE imputation<>'' ORDER BY imputation");
													while($row = $back->fetch()){
														?>
													<option value="<?php echo $row['imputation'];?>"><?php echo $row['imputation'];?></option>
														<?php 
													}
													?>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Description: </span><textarea type="text" name="description" id="description1"></textarea></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<select name="company" style="display:none;" class="lx-companies-list" />
												<?php
												$back = $bdd->query("SELECT id,rs FROM companies WHERE trash='1'".$companiesid." ORDER BY rs");
												while($row = $back->fetch()){
													?>
												<option value="<?php echo $row['id'];?>"><?php echo $row['rs'];?></option>
													<?php
												}	
												?>
											</select>
											<input type="hidden" name="id" value="0" />
											<input type="hidden" name="page" value="payments" />
											<a href="javascript:;" class="">Enregistrer</a>
											<br />
											<br />
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Popup -->
			<div tabindex="0" class="lx-popup ordervirement">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Imprimer ordre de virement</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="orderform">										
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Type de virement<sup>*</sup>:</span>
												<select name="type" data-isnotempty="" data-message="Choisissez un type !!">
													<option value="">Choisissez un type</option>
													<option value="Order de virement">Order de virement</option>
													<option value="Order de virement instantané">Order de virement instantané</option>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span class="lx-order-text">Note supplimentaire (N° engagement, Frais ...): </span><textarea type="text" name="note" id="note2"></textarea></label>
										</div>
										<div class="lx-clear-fix"></div>
										<?php
										$back = $bdd->query("SELECT * FROM printoptions");
										$row = $back->fetch();
										?>
										<h3 style="text-align:center;margin-bottom:10px;margin-top:20px;font-size:16px;"><u style="text-decoration:underline;">Options de génération de document:</u></h3>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label>
												<span>Texte au dessus de cachet et signature:</span>
												<input type="text" name="signaturetext" value="<?php echo $row['label'];?>" />
											</label>
										</div>
										<div style="<?php echo !preg_match("#Impression des documents avec cachet et signature#",$_SESSION['easybm_roles'])?"display:none;":"";?>">
											<div class="lx-textfield lx-g2 lx-pb-0">
												<label>
													<span>Cachet et signature:</span>
													<select name="signature" data-isnotempty="" data-message="Choisissez une option">
														<option value="" <?php echo $row['signature']==""?"selected":"";?>></option>
														<option value="&signature=1" <?php echo $row['signature']=="1"?"selected":"";?>>Avec</option>
														<option value="&signature=0" <?php echo $row['signature']=="0"?"selected":"";?>>Sans</option>
													</select>
												</label>
											</div>
											<div class="lx-textfield lx-g2 lx-pb-0">
												<label>
													<span>Emplacement cachet et signature:</span>
													<select name="placement" data-isnotempty="" data-message="Choisissez une option">
														<option value="" <?php echo $row['placement']==""?"selected":"";?>></option>
														<option value="&placement=left" <?php echo $row['placement']=="left"?"selected":"";?>>Gauche</option>
														<option value="&placement=center" <?php echo $row['placement']=="center"?"selected":"";?>>Centre</option>
														<option value="&placement=right" <?php echo $row['placement']=="right"?"selected":"";?>>Droite</option>
													</select>
												</label>
											</div>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label>
												<span>En-tête et pied de page:</span>
												<select name="header" data-isnotempty="" data-message="Choisissez une option">
													<option value="" <?php echo $row['header']==""?"selected":"";?>></option>
													<option value="&header=1" <?php echo $row['header']=="1"?"selected":"";?>>Avec</option>
													<option value="&header=0" <?php echo $row['header']=="0"?"selected":"";?>>Sans</option>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="company" value="0" />
											<input type="hidden" name="document" value="caisse" />
											<input type="hidden" name="id" value="0" />
											<a href="javascript:;" class="">Imprimer</a>
											<br />
											<br />
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Popup -->
			<div tabindex="0" class="lx-popup disposition">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Imprimer mise à disposition</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="dispositionform">										
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Compte société (<b class="lx-company-mise"></b>)<sup>*</sup>:</span>
												<select name="rib" data-isnotempty="" data-message="Choisissez un compte banquaire!!">
													
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>À disposition de<sup>*</sup>: </span><input type="text" autocomplete="off" name="fullname" data-isnotempty="" data-message="Saisissez un nom !!" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Titulaire de la CIN<sup>*</sup>: </span><input type="text" autocomplete="off" name="cin" data-isnotempty="" data-message="Saisissez un CIN !!" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span class="lx-order-text">Et ce pour règlement: </span><textarea type="text" name="note" class="no-tinymce"></textarea></label>
										</div>
										<div class="lx-clear-fix"></div>
										<?php
										$back = $bdd->query("SELECT * FROM printoptions");
										$row = $back->fetch();
										?>
										<h3 style="text-align:center;margin-bottom:10px;margin-top:20px;font-size:16px;"><u style="text-decoration:underline;">Options de génération de document:</u></h3>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label>
												<span>Texte au dessus de cachet et signature:</span>
												<input type="text" name="signaturetext" value="<?php echo $row['label'];?>" />
											</label>
										</div>
										<div style="<?php echo !preg_match("#Impression des documents avec cachet et signature#",$_SESSION['easybm_roles'])?"display:none;":"";?>">
											<div class="lx-textfield lx-g2 lx-pb-0">
												<label>
													<span>Cachet et signature:</span>
													<select name="signature" data-isnotempty="" data-message="Choisissez une option">
														<option value="" <?php echo $row['signature']==""?"selected":"";?>></option>
														<option value="&signature=1" <?php echo $row['signature']=="1"?"selected":"";?>>Avec</option>
														<option value="&signature=0" <?php echo $row['signature']=="0"?"selected":"";?>>Sans</option>
													</select>
												</label>
											</div>
											<div class="lx-textfield lx-g2 lx-pb-0">
												<label>
													<span>Emplacement cachet et signature:</span>
													<select name="placement" data-isnotempty="" data-message="Choisissez une option">
														<option value="" <?php echo $row['placement']==""?"selected":"";?>></option>
														<option value="&placement=left" <?php echo $row['placement']=="left"?"selected":"";?>>Gauche</option>
														<option value="&placement=center" <?php echo $row['placement']=="center"?"selected":"";?>>Centre</option>
														<option value="&placement=right" <?php echo $row['placement']=="right"?"selected":"";?>>Droite</option>
													</select>
												</label>
											</div>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label>
												<span>En-tête et pied de page:</span>
												<select name="header" data-isnotempty="" data-message="Choisissez une option">
													<option value="" <?php echo $row['header']==""?"selected":"";?>></option>
													<option value="&header=1" <?php echo $row['header']=="1"?"selected":"";?>>Avec</option>
													<option value="&header=0" <?php echo $row['header']=="0"?"selected":"";?>>Sans</option>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="id" value="0" />
											<a href="javascript:;" class="">Imprimer</a>
											<br />
											<br />
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Popup -->
			<div tabindex="0" class="lx-popup caissebulk">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Rapprochement en masse</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="caissebulkform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Société<sup>*</sup>:</span>
												<select name="company" class="lx-companies-list" data-isnumber="" data-message="Choisissez une société!!" disabled>
													<?php
													$back = $bdd->query("SELECT id,rs FROM companies WHERE trash='1'".$companiesid." ORDER BY rs");
													if($back->rowCount() > 1){
													?>
													<option value="">Choisissez une société</option>
													<?php
													}
													while($row = $back->fetch()){
														?>
													<option value="<?php echo $row['id'];?>"><?php echo $row['rs'];?></option>
														<?php 
													}
													?>
												</select>
											</label>
										</div>	
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Date valeur (Rapprochement)<sup>*</sup>: </span><input type="text" name="datepaidcommand" data-isnotempty="" data-message="Choisissez une date" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Compte banquaire société:</span>
												<select name="rib" class="lx-bankaccounts-list">
													<option value="0">Choisissez un compte banquaire</option>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
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
			<div tabindex="0" class="lx-popup remisebulk">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Remise en masse</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="remisebulkform">
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Date remis: </span><input type="text" name="dateremiscommand" data-isnotempty="" data-message="Choisissez une date!!" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>N° de remise: </span><input type="text" autocomplete="off" name="nremise" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
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
									<h3>Export trésorerie</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="exportform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="dateadd" data-title="Date d'ajout" checked /> Date d'ajout<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="company" data-title="Affecté à la société" checked /> Affecté à la société<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="code" data-title="Code" checked /> Référence<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="type" data-title="Nature" checked /> Nature<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="modepayment" data-title="Mode de paiement" checked /> Mode de paiement<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="price" data-title="Montant TTC en <?php echo $settings['currency'];?>" checked /> Montant TTC en <?php echo $settings['currency'];?><del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="tva" data-title="Montant TVA en <?php echo $settings['currency'];?>" checked /> Montant TVA en <?php echo $settings['currency'];?><del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="price2" data-title="Montant TTC en devise" checked /> Montant TTC en devise<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="title" data-title="Libellé" checked /> Libellé<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="client" data-title="Client" checked /> Client<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="supplier" data-title="Fournisseur" checked /> Fournisseur<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="note" data-title="Note" checked /> Note<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="datedue" data-title="Date d'écheance" checked /> Date d'écheance<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="imputation" data-title="Imputation comptable " checked /> Imputation comptable <del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="rib" data-title="Compte banquaire encaissement / décaissement " checked /> Compte banquaire encaissement / décaissement <del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="datepaid" data-title="Date valeur (Rapprochement)" checked /> Date valeur (Rapprochement)<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="remis" data-title="Remis" checked /> Remis<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="paid" data-title="Impayé" checked /> Impayé<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="invoiced" data-title="À comptabiliser" checked /> À comptabiliser<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="user" data-title="Utilisateur" checked /> Utilisateur<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="table" value="payments" />
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
										<p>Voulez vous vraiment supprimer cette dépense / encaissement?</p>
										<a href="javascript:;" class="lx-delete-record" data-action="deletecaisse" data-id="">Oui</a>
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
		<script src="js/ion.rangeSlider.min.js"></script>
		<!-- Main Script -->
		<script src="js/script.js?v=<?php echo rand();?>"></script>
		<script>
			$(document).ready(function(){
				loadCaisse($(".lx-pagination ul").attr("data-state"));
				loadPriceRange('payments');
				toDropDown();
			});
			$(function() {
				$('#dateadd').daterangepicker({
					locale: {
					  format: 'DD/MM/YYYY',
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
						$('#dateadd').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
						$('#datestart').val(start.format('DD/MM/YYYY'));
						$('#dateend').val(end.format('DD/MM/YYYY'));
						$("input[name='orderby']").val("ASC");
						loadCaisse($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="dateadd"]').on('apply.daterangepicker', function(ev, picker) {
					$('input[name="dateadd"]').val(picker.startDate.format('DD/MM/YYYY') + " - " + picker.endDate.format('DD/MM/YYYY'));
					$('input[name="datestart"]').val(picker.startDate.format('DD/MM/YYYY'));
					$('input[name="dateend"]').val(picker.endDate.format('DD/MM/YYYY'));
					loadCaisse($(".lx-pagination ul").attr("data-state"));
				});
				$('#dateadd').on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
					$('#datestart').val('');
					$('#dateend').val('');
					loadCaisse($(".lx-pagination ul").attr("data-state"));
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
					if (name === 'dateadd') {
						$('input[name="datestart"]').val(today.format('DD/MM/YYYY'));
						$('input[name="dateend"]').val(today.format('DD/MM/YYYY'));
					} else if (name === 'datepaid') {
						$('input[name="datepaidstart"]').val(today.format('DD/MM/YYYY'));
						$('input[name="datepaidend"]').val(today.format('DD/MM/YYYY'));
					} else if (name === 'datedue') {
						$('input[name="dateduestart"]').val(today.format('DD/MM/YYYY'));
						$('input[name="datedueend"]').val(today.format('DD/MM/YYYY'));
					} else if (name === 'dateremis') {
						$('input[name="dateremisstart"]').val(today.format('DD/MM/YYYY'));
						$('input[name="dateremisend"]').val(today.format('DD/MM/YYYY'));
					}

					// Trigger the "Apply" button to update the date range
					activeDateInput.data('daterangepicker').clickApply();

					// Reload commands if necessary
					loadFactureSuppliers($(".lx-pagination ul").attr("data-state"));
				});
				
				$('#datedue').daterangepicker({
					locale: {
					  format: 'DD/MM/YYYY',
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
						$('#datedue').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
						$('#dateduestart').val(start.format('DD/MM/YYYY'));
						$('#datedueend').val(end.format('DD/MM/YYYY'));
						$("input[name='orderby']").val("ASC");
						loadCaisse($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="datedue"]').on('apply.daterangepicker', function(ev, picker) {
					$('input[name="datedue"]').val(picker.startDate.format('DD/MM/YYYY') + " - " + picker.endDate.format('DD/MM/YYYY'));
					$('input[name="dateduestart"]').val(picker.startDate.format('DD/MM/YYYY'));
					$('input[name="datedueend"]').val(picker.endDate.format('DD/MM/YYYY'));
					loadCaisse($(".lx-pagination ul").attr("data-state"));
				});
				$('#datedue').on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
					$('#dateduestart').val('');
					$('#datedueend').val('');
					loadCaisse($(".lx-pagination ul").attr("data-state"));
				});
				
				$('#datepaid').daterangepicker({
					locale: {
					  format: 'DD/MM/YYYY',
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
						$('#datepaid').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
						$('#datepaidstart').val(start.format('DD/MM/YYYY'));
						$('#datepaidend').val(end.format('DD/MM/YYYY'));
						$("input[name='orderby']").val("ASC");
						loadCaisse($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="datepaid"]').on('apply.daterangepicker', function(ev, picker) {
					$('input[name="datepaid"]').val(picker.startDate.format('DD/MM/YYYY') + " - " + picker.endDate.format('DD/MM/YYYY'));
					$('input[name="datepaidstart"]').val(picker.startDate.format('DD/MM/YYYY'));
					$('input[name="datepaidend"]').val(picker.endDate.format('DD/MM/YYYY'));
					loadCaisse($(".lx-pagination ul").attr("data-state"));
				});
				$('#datepaid').on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
					$('#datepaidstart').val('');
					$('#datepaidend').val('');
					loadCaisse($(".lx-pagination ul").attr("data-state"));
				});

				$('#dateremis').daterangepicker({
					locale: {
					  format: 'DD/MM/YYYY',
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
						$('#dateremis').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
						$('#dateremisstart').val(start.format('DD/MM/YYYY'));
						$('#dateremisend').val(end.format('DD/MM/YYYY'));
						$("input[name='orderby']").val("ASC");
						loadCaisse($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="dateremis"]').on('apply.daterangepicker', function(ev, picker) {
					$('input[name="dateremis"]').val(picker.startDate.format('DD/MM/YYYY') + " - " + picker.endDate.format('DD/MM/YYYY'));
					$('input[name="dateremisstart"]').val(picker.startDate.format('DD/MM/YYYY'));
					$('input[name="dateremisend"]').val(picker.endDate.format('DD/MM/YYYY'));
					loadCaisse($(".lx-pagination ul").attr("data-state"));
				});
				$('#dateremis').on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
					$('#dateremisstart').val('');
					$('#dateremisend').val('');
					loadCaisse($(".lx-pagination ul").attr("data-state"));
				});

				$('input[name="dateremiscommand"]').daterangepicker({
					locale: {
					  format: 'DD/MM/YYYY'
					},
					singleDatePicker: true,
					showDropdowns: true,
					autoUpdateInput: false
				}).on('apply.daterangepicker', function(ev, picker) {
					$(this).val(picker.startDate.format('DD/MM/YYYY'));
				});		
				$('input[name="dateduecommand"]').daterangepicker({
					locale: {
					  format: 'DD/MM/YYYY'
					},
					singleDatePicker: true,
					showDropdowns: true,
				});
				$('input[name="datepaidcommand"]').daterangepicker({
					locale: {
					  format: 'DD/MM/YYYY'
					},
					singleDatePicker: true,
					showDropdowns: true,
					autoUpdateInput: false
				}).on('apply.daterangepicker', function(ev, picker) {
					$(this).val(picker.startDate.format('DD/MM/YYYY'));
				});
				var $dateInput = $('input[name="dateaddcommand"]');
				var FORMAT = 'DD/MM/YYYY';

				$dateInput.daterangepicker({
					locale: { format: FORMAT },
					singleDatePicker: true,
					showDropdowns: true,
					autoUpdateInput: false, // IMPORTANT: we will update input manually
					// optional: prevent auto-show when focusing if you want to type freely
					opens: 'right'
				});

				// helper: strict-parse a string and return moment or null
				function strictParseDate(str) {
					if (!str || !str.trim()) return null;
					var normalized = str.trim().replace(/-/g, '/'); // allow '-' or '/'
					var m = moment(normalized, FORMAT, true); // strict
					return m.isValid() ? m : null;
				}

				function setInputAndPicker($input, m) {
					var picker = $input.data('daterangepicker');
					// accept either moment or null -> if null use today
					var finalMoment = m ? m.clone() : moment();
					$input.val(finalMoment.format(FORMAT));
					picker.setStartDate(finalMoment);
					picker.setEndDate(finalMoment);
				}

				// When the user picks from the calendar
				$dateInput.on('apply.daterangepicker', function(ev, picker) {
					// picker.startDate is a strict moment already; just format it
					$(this).val(picker.startDate.format(FORMAT));
					// ensure internal state is correct (redundant but safe)
					picker.setStartDate(picker.startDate);
					picker.setEndDate(picker.startDate);
				});

				// Prevent the picker from resetting the input when canceled/hidden
				$dateInput.on('cancel.daterangepicker', function() {
					// do nothing — we manage input manually
				});

				// Validate when the user leaves the field (or presses Enter)
				$dateInput.on('blur', function() {
					var $this = $(this);
					var m = strictParseDate($this.val());
					setInputAndPicker($this, m); // if m is null it will set today
				});

				$dateInput.on('keypress', function(e) {
					if (e.which === 13) { // Enter
						e.preventDefault();
						var $this = $(this);
						var m = strictParseDate($this.val());
						setInputAndPicker($this, m);
						// optionally close the picker if open
						var picker = $this.data('daterangepicker');
						if (picker && picker.isShowing) picker.hide();
					}
				});

				// Optional: if you want the input to show current picker value when opening
				$dateInput.on('show.daterangepicker', function(ev, picker) {
					// display the current startDate in the input while the picker is open
					$(this).val(picker.startDate.format(FORMAT));
				});

			});
		</script>
	</body>
</html>
<?php
DB_Sanitize();
}
?>
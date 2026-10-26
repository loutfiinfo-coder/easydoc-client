<?php
session_start();
include("config.php");

$_SESSION['easybm_errorimport'] = "";

if(!isset($_SESSION['easybm_id'])){
	header('location: login.php');
}
else{
	if(!preg_match("#Consulter Bons de livraison#",$_SESSION['easybm_roles'])){	
		header('location: 404.php');
	}
}

if(isset($_SESSION['easybm_id']) AND isset($_SESSION['easybm_fullname'])){
?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<title>Bons de livraison</title>
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
			  selector: 'textarea:not(#whatsappMessage):not(#recallnote)',
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
			  paste_data_images: false, // disable pasting base64 images

			  valid_elements: 'p,strong,em,u,span[style],ul,ol,li,a[href],h1,h2,h3,h4,h5,h6', // allow only text and basic formatting

			  setup: function (editor) {

				// Prevent pasting disallowed content (images, tables, videos, etc.)
				editor.on('paste', function (e) {
				  const clipboard = (e.clipboardData || e.originalEvent.clipboardData);
				  if (!clipboard) return;

				  // Block any pasted files (like images)
				  for (let i = 0; i < clipboard.items.length; i++) {
					const item = clipboard.items[i];
					if (item.kind === 'file' || item.type.indexOf('image') !== -1) {
					  e.preventDefault();
					  alert('Pasting images or files is not allowed.');
					  return false;
					}
				  }

				  // Clean pasted HTML to keep only allowed tags
				  let html = clipboard.getData('text/html');
				  if (html) {
					// Remove disallowed tags (table, video, iframe, img, etc.)
					html = html.replace(/<(table|thead|tbody|tfoot|tr|td|th|video|iframe|img|object|embed|style|script)[^>]*>.*?<\/\1>/gis, '');
					html = html.replace(/<(table|thead|tbody|tfoot|tr|td|th|video|iframe|img|object|embed|style|script)[^>]*\/?>/gis, '');
					e.preventDefault();
					editor.insertContent(html);
				  }
				});

				// Prevent dropping files into the editor
				editor.on('drop', function (e) {
				  if (e.dataTransfer && e.dataTransfer.files.length) {
					e.preventDefault();
					alert('Dropping files or media is not allowed.');
					return false;
				  }
				});
			  },
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
						<h2>Bons de livraison</h2>
					</div>
					<div class="lx-clear-fix"></div>
					<div class="lx-tabs">
						<?php
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
						<a href="bl.php" class="active">Bons de livraison</a>
							<?php
						}
						if(preg_match("#Consulter Bons de sortie#",$_SESSION['easybm_roles'])){	
							?>
						<a href="bs.php">Bons de sortie</a>
							<?php
						}
						if(preg_match("#Consulter Tags documents#",$_SESSION['easybm_roles'])){	
							?>
						<a href="doclabels.php?type=client">Tags</a>
							<?php
						}
						?>
					</div>
					<div class="lx-page-content">
						<div class="lx-g1">
							<div class="lx-add-form">
								<?php
								if(preg_match("#Ajouter Bons de livraison#",$_SESSION['easybm_roles'])){	
									?>							
								<a href="javascript:;" class="lx-new lx-new-command lx-open-popup" data-header="Ajouter un nouveau" data-table="bl" data-title="command">+ Nouveau bon de livraison</a>
									<?php
								}					
								if(preg_match("#Transformation groupée des documents#",$_SESSION['easybm_roles'])){	
									?>						
								<a href="javascript:;" class="lx-new lx-transform-bulk">Transformation groupée</a>
								<a href="javascript:;" class="lx-new lx-open-popup lx-transform-bulk-real" style="display:none;" data-title="merge"></a>
									<?php
								}
								?>						
							</div>
							<div class="lx-keyword">
								<fieldset style="margin-bottom:20px;padding:10px;background:#F8F8F8;">
									<legend><b>Filtre avancé:</b></legend>
									<label><span>Mot clé:</span><a href="javascript:;" class="lx-search-keyword"><i class="fa fa-search"></i></a><input type="text" autocomplete="off" name="keyword" id="keyword" placeholder="Réf, note ..." data-table="devis" /></label>
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
										<span>Produits / Services:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="product" id="product" placeholder="" data-ids="" title="Produits / Services" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" placeholder="Rechercher ..." autocomplete="off" data-table="products" data-value="rs" data-ids="id" data-text="rs" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
											</ul>
										</div>
									</label>
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
										<span>Tags:</span>
										<i class="fa fa-caret-down"></i>
										<input type="text" autocomplete="off" name="tags" id="tags" placeholder="" data-ids="" title="Tags" readonly />
										<div>
											<a href="javascript:;" class="lx-state-empty">Vider</a>
											<a href="javascript:;" class="lx-state-filter">Filtrer</a>
											<div class="lx-clear-fix"></div>
											<input type="text" autocomplete="off" name="searchadvanced" style="margin-bottom:20px;" />
											<ul>
												<?php
												$back = $bdd->query("SELECT label FROM doclabels WHERE type='client' AND trash='1'".$multicompanies." ORDER BY label");
												while($row = $back->fetch()){
													?>
												<li><label><input type="checkbox" value="<?php echo $row['label'];?>" data-ids="<?php echo $row['label'];?>" /> <?php echo $row['label'];?><del class="checkmark"></del></label></li>
													<?php
												}
												?>
											</ul>
										</div>
									</label>
									<?php
									$styleday = "";
									$rangedate = "";
									$rangedateplaceholder = "Date création";
									$startdate = "";
									$enddate = "";
									if(preg_match("#Consultation de la journée en cours seulement Propositions honoraires#",$_SESSION['easybm_roles'])){
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
									?>
									<label style="<?php echo $styleday;?>"><span>Date création:</span><input type="text" autocomplete="off" name="dateadd" id="dateadd" title="Date création" value="<?php echo $rangedate;?>" placeholder="" readonly style="background:white;cursor:pointer;" /></label>
									<input type="hidden" name="datestart" id="datestart" value="<?php echo $startdate;?>" />
									<input type="hidden" name="dateend" id="dateend" value="<?php echo $enddate;?>" />
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
									<br />
									<fieldset style="display:inline-block;padding:0px 10px;">
										<legend>Prix TTC</legend>
										<label style="margin-right:0px;margin-bottom:5px;" class="lx-price-range">
											<input type="text" class="js-range-slider" name="my_range" value="" />
											<input type="hidden" id="pricemin" />
											<input type="hidden" id="pricemax" />
											<a href="javascript:;" onclick="loadCommands('1')" class="lx-price-filter">Appliquer</a>
										</label>
									</fieldset>
									<label>
										<a href="bl.php" class="lx-refresh-filter" style="margin-left:10px;"><i class="fa fa-sync-alt"></i></a>
									</label>
								</fieldset>
							</div>
							<div class="lx-table-container">
								<div class="lx-caisse-total lx-caisse-total-1"></div>
								<div class="lx-caisse-total lx-caisse-total-2"></div>
								<div class="lx-caisse-total lx-caisse-total-4"></div>
								<div class="lx-table-shadow lx-table-shadow-left"></div>
								<div class="lx-table-shadow lx-table-shadow-right"></div>
								<div class="lx-table lx-table-bl">

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
								if(preg_match("#Exporter Bons de livraison#",$_SESSION['easybm_roles'])){
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
							$back = $bdd->query("SELECT COUNT(*) AS nb FROM documents WHERE category='client' AND type='bl' AND trash='1'");
							$row = $back->fetch();
							?>
							<div class="lx-pagination" style="<?php if($row['nb'] <= $nb){echo "display:none;";}?>">
								<?php
								$nbpages = ceil($row['nb']/$nb);
								?>
								<ul data-table="bl" data-state="1" data-start="0" data-nbpage="<?php echo $nb;?>" data-posts="<?php echo $row['nb'];?>">
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
			<div class="lx-select-avoir" style="display:none;">
				<option value="avoir" data-prefix="AV">Facture avoir</option>
				<?php
				if(preg_match("#Consulter Bons de retour#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Bons de retour#",$_SESSION['easybm_roles'])){
				?>
				<option value="br" data-prefix="BR">Bon de retour</option>
				<?php
				}
				?>		
			</div>
			<div class="lx-select-facture" style="display:none;">
				<option value="bl" data-prefix="BL">Bons de livraison</option>
				<?php
				if(preg_match("#Consulter Factures clients#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Factures clients#",$_SESSION['easybm_roles'])){
				?>
				<option value="facture" data-prefix="FA">Facture</option>
				<?php
				}
				if(preg_match("#Consulter Notes honoraires#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Notes honoraires#",$_SESSION['easybm_roles'])){
				?>
				<option value="nh" data-prefix="NH">Notes d'honoraires</option>
				<?php
				}
				if(preg_match("#Consulter Notes de frais#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Notes de frais#",$_SESSION['easybm_roles'])){
				?>
				<option value="nf" data-prefix="NF">Notes de frais</option>
				<?php
				}
				if(preg_match("#Consulter Devis,#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Devis,#",$_SESSION['easybm_roles'])){
				?>
				<option value="devis" data-prefix="DE">Devis</option>
				<?php
				}
				if(preg_match("#Consulter Propositions honoraires#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Propositions honoraires#",$_SESSION['easybm_roles'])){
				?>
				<option value="ph" data-prefix="PH">Propositions d'honoraires</option>
				<?php
				}
				if(preg_match("#Consulter Factures proforma#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Factures proforma#",$_SESSION['easybm_roles'])){
				?>
				<option value="factureproforma" data-prefix="FP">Facture proforma</option>
				<?php
				}
				if(preg_match("#Consulter Bons de sortie#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Bons de sortie#",$_SESSION['easybm_roles'])){
				?>
				<option value="bs" data-prefix="BS">Bon de sortie</option>
				<?php
				}
				?>					
			</div>
			<!-- End Popup -->
			<div tabindex="0" class="lx-popup command">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons lx-direct-click"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3><span></span> bon de livraison</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="commandsform">
										<div class="lx-textfield lx-g1 lx-pb-0 lx-transform">
											<label><span>Duppliquer ou transformer vers:</span>
												<select name="transform">
													<option value="bl" data-prefix="BL">Bons de livraison</option>
													<option value="facture" data-prefix="FA">Facture</option>
													<option value="nh" data-prefix="NH">Note d'honoraire</option>
													<option value="devis" data-prefix="DE">Devis</option>
													<option value="ph" data-prefix="PH">Propositions d'honoraires</option>
													<option value="factureproforma" data-prefix="FP">Facture proforma</option>
													<option value="bs" data-prefix="BS">Bon de sortie</option>
												</select>
											</label>
										</div>										
										<div class="lx-clear-fix"></div>
										<div class="lx-transform" style="margin:20px;border-top:1px dashed #BEBEBE;"></div>
										<div class="lx-textfield lx-g3 lx-pb-0 datereported" style="<?php echo !preg_match("#Modification date opération#",$_SESSION['easybm_roles'])?"display:none;":"";?>">
											<label><span>Date<sup>*</sup>: </span><input type="text" name="dateaddcommand" /></label>
										</div>
										<div class="lx-textfield lx-g<?php echo !preg_match("#Modification date opération#",$_SESSION['easybm_roles'])?"2":"3";?> lx-pb-0">
											<label><span>Affecté à la société<sup>*</sup>:</span>
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
										<div class="lx-textfield lx-g<?php echo !preg_match("#Modification date opération#",$_SESSION['easybm_roles'])?"2":"3";?>">
											<label><span>Numéro<sup>*</sup>:</span>
												<strong class="lx-code-label">FA<?php echo date("ym");?>-</strong>
												<input type="text" autocomplete="off" name="code" data-isnumber="" data-message="Saisissez un numéro!!" />
												<i class="fa fa-sync-alt lx-refresh-codes"></i>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0" style="display:none;">
											<label><span style="font-weight:500;color:#FFA500;">Corrige la facture N°<sup>*</sup>:</span>
												<input type="text" name="correctdoc" id="correctdoc" data-isnotempty="" data-message="Saisissez un numéro facture!!" />
											</label>
										</div>											
										<div class="lx-clear-fix"></div>
										<div style="margin:20px;border-top:1px dashed #BEBEBE;"></div>
											<div class="lx-clear-fix"></div>
											<div class="lx-textfield lx-g1 lx-pb-0">
												<div class="lx-accordion-quide">
													<div class="tabs">
														<div class="tab active" data-tab="manual">Saisie manuelle</div>
														<div class="tab" data-tab="import">Import</div>
													</div>
															
													<div id="manual" class="tab-content active">
														<div class="lx-textfield lx-g1 lx-pb-0">
															<label data-message="Veuillez d'abord choisir une société"><span>Produits / services<sup>*</sup>:</span>
																<select name="product" class="todropdown">
																	
																</select>
															</label>
														</div>	
														<div class="lx-clear-fix"></div>
														<div class="lx-textfield lx-g1 lx-pb-0 lx-pt-0">
															<label><textarea name="textproduct" id="textproduct" data-isnotempty="" data-message="Saisissez un produit / service!!" /></textarea></label>
														</div>
														<div class="lx-clear-fix"></div>
														<div class="lx-textfield lx-g3 lx-pb-0">
															<div class="lx-textfield lx-g2-f lx-pb-0">
																<label><span>Quantité<i class="lx-unit-product"></i><sup>*</sup>: </span><input type="text" autocomplete="off" name="qty" value="1" data-isnumber="" data-message="Saisissez une quantité!!" /></label>
															</div>
															<div class="lx-textfield lx-g2-f lx-pb-0">
																<label><span>Unité de mesure:</span>
																	<select name="unit" id="unit" class="todropdown" data-isnotempty="" data-message="Saisissez une unité!!">
																		
																	</select>
																</label>
															</div>
														</div>
														<div class="lx-textfield lx-g3 lx-pb-0">
															<label><span>P.U. de vente<sup>*</sup>: </span>
																<input type="text" autocomplete="off" name="uprice" data-isnumber="" data-message="Saisissez un prix!!" />
																<select name="pricebase">
																	<option value="HT">HT</option>
																	<option value="TTC">TTC</option>
																</select>
															</label>
														</div>
														<div class="lx-textfield lx-g3 lx-pb-0">
															<label><span>TVA<sup>*</sup>: </span>
																<select name="utva" data-isnotempty="" data-message="Choisissez une tva de la liste!!">
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
														<div class="lx-textfield lx-g6-5 lx-pb-0"></div>
														<div class="lx-textfield lx-g6 lx-pb-0">
															<a href="javascript:;" class="lx-add-product-to-order" style="display:block;text-align:center;">Ajouter</a>
														</div>	
														<div class="lx-clear-fix"></div>
													</div>
													
													<div id="import" class="tab-content">
														<div class="lx-textfield lx-g1 lx-pb-0">
															<p style="max-width:760px;margin-bottom:10px;"><b>Important: </b>Veuillez impoter un fichier excel avec les colonnes suivantes en respectant l'ordre: (Produit / Service - Quantité - Unité de mesure - P.U. de vente - Base de prix (HT / TTC) - TVA) ou bien <a href="ExampleProducts.xlsx" style="padding:0px;font-weight:normal;background:none;color:#39add1;">télécharger un modèle</a></p>
															<p style="max-width:760px;"><b>NB: </b>Le champ "Produit / Service" est obligatoire, toute ligne ne contenant pas ce champ sera ignorée lors de l’import.</p>
														</div>		
														<div class="lx-textfield lx-g1 lx-pb-0">
															<label><span>Format du montant utilisé dans le fichier Excel importé<sup>*</sup>: </span>
																<select name="montantformat" id="montantformat" data-isnotempty="" data-message="Choisissez une tva de la liste!!">
																	<option value=""></option>
																	<option value="1001.01">1001.01</option>
																	<option value="1 001.01">1 001.01</option>
																	<option value="1,001.01">1,001.01</option>
																	<option value="1001,01">1001,01</option>
																	<option value="1 001,01">1 001,01</option>
																	<option value="1.001,01">1.001,01</option>
																</select>
															</label>
														</div>		
														<div class="lx-textfield lx-g1 lx-pb-0">
															<div class="lx-importer">
																<input type="file" name="xlsfile" id="importproduct" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
																<span>Choisissez un fichier (excel)</span>
															</div>
														</div>
														<div class="lx-clear-fix"></div>
														<div class="lx-textfield lx-g6-5 lx-pb-0"></div>
														<div class="lx-textfield lx-g6 lx-pb-0">
															<a href="javascript:;" class="lx-import-product" style="display:block;text-align:center;">Importer</a>
														</div>	
														<div class="lx-clear-fix"></div>
													</div>
												</div>
											</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0"></div>	
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Appliquer une remise:</span>
												<select name="typediscount" data-isnotempty="" data-message="Choisissez un type!!">
													<option value="">Choisissez un type</option>
													<option value="%">%</option>
													<option value="<?php echo $settings['currency'];?>"><?php echo $settings['currency'];?></option>
												</select>
											</label>
										</div>									
										<div class="lx-clear-fix"></div>
										<div class="lx-list-products lx-table" style="width:auto;">
											<table>
												<tr class="lx-firstrow">
													<td style="width:auto;">Designation</td>
													<td>Qté</td>
													<td>P.U. HT</td>
													<td class="lx-discount">Remise HT</td>
													<td>TVA</td>
													<td>Total HT</td>
													<td class="lx-qty-back">Qté à retourner</td>
													<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
													<td class="lx-note-cell" style="display:none;">Description</td>
													<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
												</tr>
												<tr class="lx-secondrow">
													<td colspan="9">
														<center><i>0 produits / services</i></center>
													</td>
												</tr>
											</table>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1">	
											<div class="lx-error-products"></div>
										</div>	
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g3 lx-pb-0"></div>		
										<div class="lx-textfield lx-g3 lx-pb-0">
											<label class="maindiscount"><span>Remise globale: </span><input type="number" min="0" name="maindiscount" /><u class="discounttype"></u></label>
										</div>		
										<div class="lx-textfield lx-g3 lx-pb-0 invoicedyes">
											<label><span>Montant Total HT: </span><input type="text" autocomplete="off" name="price" value="0" style="text-align:right;" readonly /></label>
										</div>		
										<div class="lx-clear-fix invoicedyes"></div>
										<div class="lx-textfield lx-g3-2 lx-pb-0 invoicedyes"></div>		
										<div class="lx-textfield lx-g3 lx-pb-0 invoicedyes">
											<label><span>Total TVA: </span><input type="text" autocomplete="off" name="tva" value="0" style="text-align:right;" readonly /></label>
										</div>
										<div class="lx-clear-fix invoicedyes"></div>
										<div class="lx-textfield lx-g3-2 lx-pb-0 invoicedyes"></div>		
										<div class="lx-textfield lx-g3 lx-pb-0">
											<label><span>Montant total TTC: </span><input type="text" autocomplete="off" name="ttcprice" value="0" style="text-align:right;" readonly /></label>
										</div>	
										<div class="lx-clear-fix"></div>
										<div class="lx-g1">
											<fieldset>
												<legend>Informations client<sup>*</sup>:</legend>
												<div class="lx-textfield lx-g1 lx-pb-0">
													<label style="display:inline-block;"><input type="radio" name="new" value="1" checked style="position:relative;top:2px;transform:scale(1.3);margin-right:10px;" /> Client existant<u class="circlemark"></u></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
													<label style="display:inline-block;"><input type="radio" name="new" value="0" style="position:relative;top:2px;transform:scale(1.3);margin-right:10px;" /> Nouveau client<u class="circlemark"></u></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
													<div class="lx-clear-fix" style="margin-top:5px;margin-bottom:20px;"></div>
													<label><input type="hidden" name="exist" value="" data-isnotempty="" data-message="Choisissez une option" /></label>
												</div>												
												<div class="lx-clear-fix"></div>							
												<div class="lx-textfield lx-g2 lx-pb-0">
													<label data-message="Veuillez d'abord choisir une société"><span>Client<sup>*</sup>: </span>
														<select name="client" data-isnotempty="" data-message="Choisissez un client" class="todropdown clientlist">

														</select>											
													</label>
												</div>
												<div class="lx-textfield lx-g2 lx-pb-0">
													<label><span>ICE: </span><input type="text" autocomplete="off" name="ice" data-isnotempty="" data-message="Saisissez un ICE" /></label>
												</div>
												<div class="lx-clear-fix"></div>							
												<div class="lx-textfield lx-g2 lx-pb-0">
													<label><span>Téléphone: </span><input type="text" autocomplete="off" name="phone" /></label>
												</div>
												<div class="lx-textfield lx-g2 lx-pb-0">
													<label><span>Adresse: </span><input type="text" autocomplete="off" name="address" data-isnotempty="" data-message="Saisissez une adresse" /></label>
												</div>
												<div class="lx-clear-fix"></div>
												<div class="lx-textfield lx-g1">
													<label><span>Email: </span><input type="text" autocomplete="off" name="email" /></label>
												</div>
											</fieldset>
										</div>
										<div class="lx-clear-fix lx-cash" style="display:none;"></div>
										<div class="lx-cash" style="display:none;margin:15px;border-top:1px dashed #BEBEBE;"></div>
										<div class="lx-clear-fix"></div>
										<div style="display:none;">
										<div class="lx-textfield lx-g2 lx-pb-0 lx-cash">
											<label><span>Montant payé<sup>*</sup>: </span><input type="text" autocomplete="off" name="cash" data-isnumber="" data-message="Saisissez un montant!!" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0 lx-cash">
											<label><span>Reste: </span><input type="text" autocomplete="off" name="rest" style="text-align:right;" readonly /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0 lx-cash">
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
										<div class="lx-textfield lx-g2 lx-pb-0 lx-cash">
											<label><span>Compte banquaire:</span>
												<select name="rib" class="lx-bankaccounts-list">
													<option value="0">Choisissez un compte banquaire</option>
												</select>
											</label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0 lx-cash">
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
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Note: </span><textarea name="note" id="note" /></textarea></label>
										</div>
										<div class="lx-clear-fix"></div>							
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="modepayment" value="" />
											<input type="hidden" name="conditions" value="" />
											<input type="hidden" name="abovetable" value="" />
											<input type="hidden" name="category" value="client" />
											<input type="hidden" name="type" value="bl" />
											<input type="hidden" name="prefix" value="BL" />
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
			<div tabindex="0" class="lx-popup merge">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons lx-direct-click"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3><span></span> facture</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="mergeform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Regrouper ou transformer vers:</span>
												<select name="transform">
													<option value="bl" data-prefix="BL">Bon de livraison</option>
													<option value="facture" data-prefix="FA">Facture</option>
													<option value="nh" data-prefix="NH">Note d'honoraire</option>
													<option value="nf" data-prefix="NF">Note de frais</option>
													<option value="devis" data-prefix="DE">Devis</option>
													<option value="ph" data-prefix="PH">Propositions d'honoraires</option>
													<option value="factureproforma" data-prefix="FP">Facture proforma</option>
													<option value="bs" data-prefix="BS">Bon de sortie</option>
												</select>
											</label>
										</div>										
										<div class="lx-clear-fix"></div>
										<div class="lx-transform" style="margin:20px;border-top:1px dashed #BEBEBE;"></div>
										<div class="lx-create-avoir">
											<div class="lx-textfield lx-g3 lx-pb-0 datereported" style="<?php echo !preg_match("#Modification date opération#",$_SESSION['easybm_roles'])?"display:none;":"";?>">
												<label><span>Date<sup>*</sup>: </span><input type="text" name="dateaddcommand" /></label>
											</div>
											<div class="lx-textfield lx-g<?php echo !preg_match("#Modification date opération#",$_SESSION['easybm_roles'])?"2":"3";?> lx-pb-0">
												<label><span>Affecté à la société<sup>*</sup>:</span>
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
											<div class="lx-textfield lx-g<?php echo !preg_match("#Modification date opération#",$_SESSION['easybm_roles'])?"2":"3";?>">
												<label><span>Numéro<sup>*</sup>:</span>
													<strong class="lx-code-label1">BL<?php echo date("ym");?>-</strong>
													<input type="text" autocomplete="off" name="code" data-isnumber="" data-message="Saisissez un numéro!!" />
													<i class="fa fa-sync-alt lx-refresh-codes"></i>
												</label>
											</div>											
											<div class="lx-clear-fix"></div>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-g1">
											<div class="lx-list-docs lx-table" style="width:auto;">

											</div>	
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-create-avoir">
											<div class="lx-g1">
												<fieldset>
													<legend>Informations client<sup>*</sup>:</legend>
													<div class="lx-textfield lx-g1 lx-pb-0">
														<label style="display:inline-block;"><input type="radio" name="new" value="1" checked style="position:relative;top:2px;transform:scale(1.3);margin-right:10px;" /> Client existant<u class="circlemark"></u></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
														<label style="display:inline-block;"><input type="radio" name="new" value="0" style="position:relative;top:2px;transform:scale(1.3);margin-right:10px;" /> Nouveau client<u class="circlemark"></u></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
														<div class="lx-clear-fix" style="margin-top:5px;margin-bottom:20px;"></div>
														<label><input type="hidden" name="exist" value="1" data-isnotempty="" data-message="Choisissez une option" /></label>
													</div>												
													<div class="lx-clear-fix"></div>							
													<div class="lx-textfield lx-g2 lx-pb-0">
														<label data-message="Veuillez d'abord choisir une société"><span>Client<sup>*</sup>: </span>
															<select name="client" data-isnotempty="" data-message="Choisissez un client" class="todropdown clientlist">

															</select>											
														</label>
													</div>
													<div class="lx-textfield lx-g2 lx-pb-0">
														<label><span>ICE: </span><input type="text" autocomplete="off" name="ice" data-isnotempty="" data-message="Saisissez un ICE" /></label>
													</div>
													<div class="lx-clear-fix"></div>							
													<div class="lx-textfield lx-g2 lx-pb-0">
														<label><span>Téléphone: </span><input type="text" autocomplete="off" name="phone" /></label>
													</div>
													<div class="lx-textfield lx-g2 lx-pb-0">
														<label><span>Adresse: </span><input type="text" autocomplete="off" name="address" data-isnotempty="" data-message="Saisissez une adresse" /></label>
													</div>
													<div class="lx-clear-fix"></div>
													<div class="lx-textfield lx-g1">
														<label><span>Email: </span><input type="text" autocomplete="off" name="email" /></label>
													</div>
												</fieldset>
											</div>
											<div class="lx-clear-fix"></div>
											<div class="lx-textfield lx-g1 lx-pb-0">
												<label><span>Note: </span><textarea name="notemerge" id="notemerge" /></textarea></label>
											</div>
										</div>
										<div class="lx-clear-fix"></div>							
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="ids" value="" />
											<input type="hidden" name="codes" value="" />
											<input type="hidden" name="prices" value="" />
											<input type="hidden" name="clients" value="" />
											<input type="hidden" name="clientid" value="" />
											<input type="hidden" name="prospects" value="" />
											<input type="hidden" name="prospectid" value="" />	
											<input type="hidden" name="category" value="client" />
											<input type="hidden" name="type" value="bl" />
											<input type="hidden" name="prefix" value="BL" />
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
			<div tabindex="0" class="lx-popup extrainfo">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Informations supplémentaires sur le document</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="extrainfoform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Note au-dessus du tableau: </span><textarea name="abovetable" id="abovetable" /></textarea></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Note au-dessous du tableau: </span><textarea name="modepayment" id="modepayment" /></textarea></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0" style="display:none;">
											<label><span>Conditions (au-dessous du tableau): </span><textarea name="conditions" id="conditions" /></textarea></label>
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
											<label><span>Note: </span><textarea name="note" id="recallnote" data-isnotempty="" data-message="Saisissez une note"></textarea></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="table" value="documents" />
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
			<div tabindex="0" class="lx-popup message">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Envoi message</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="sendmessagesform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Envoyer via<sup>*</sup>:</span>
												<select name="type" data-isnotempty="" data-message="Choisissez un canal!!">
													<option value="">Choisissez un canal</option>
													<option value="WhatsApp">WhatsApp</option>
													<option value="E-mail">E-mail</option>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0 viaemail">
											<label><span class="lx-via-label">Envoyer de<sup>*</sup>: </span><input type="text" name="sender" data-isnotempty="" data-message="Champ obligatoire" readonly /></label>
										</div>
										<div class="lx-textfield lx-g1 lx-pb-0 via">
											<label><span class="lx-via-label">Envoyer vers (E-mail séparées par “;”)<sup>*</sup>: </span><input type="text" name="contact" data-isnotempty="" data-message="Champ obligatoire" /></label>
										</div>	
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Modèle<sup>*</sup>:</span>
												<select name="model" data-isnotempty="" data-message="Choisissez un modèle!!">
													<option value="">Choisissez un modèle</option>
													<?php
													$back = $bdd->query("SELECT * FROM messages WHERE trash='1' AND page IN('fc','document')".$multicompanies);
													while($row = $back->fetch()){
														?>
													<option value="<?php echo $row['id'];?>" 
														data-button="<?php echo $row['buttons'];?>" 
														data-type="<?php echo $row['type'];?>" 
														data-subject="<?php echo $row['subject'];?>"
														data-message="<?php echo $row['message'];?>"><?php echo $row['title'];?></option>
														<?php 
													}
													?>
												</select>
											</label>
										</div>	
										<style>
										  .editor-toolbar button {
											margin: 2px;
											padding: 5px 10px;
											font-weight: bold;
											cursor: pointer;
										  }
										  .editor-toolbar {
											margin-bottom: 5px;
										  }
										</style>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0 viaemail">
											<label><span class="lx-via-label">Objet<sup>*</sup>: </span><input type="text" name="subject" data-isnotempty="" data-message="saisissez un objet" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Message<sup>*</sup>: </span>

											<div class="editor-toolbar">
											  <button type="button" onclick="wrapText('*', '*')">Gras</button>
											  <button type="button" onclick="wrapText('_', '_')">Italic</button>
											  <button type="button" onclick="wrapText('~', '~')">Barré</button>
											</div>

											<textarea id="whatsappMessage" name="message" data-isnotempty="" data-message="Saisissez un message!!" rows="8" style="width:100%;"></textarea>
											</label>
										</div>
										<h3 style="text-align:center;margin-bottom:10px;margin-top:20px;font-size:16px;" class="printdoc"><u style="text-decoration:underline;">Options de génération de document:</u></h3>
										<div class="lx-print-options printdoc" style="position:relative;">

										</div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="fullname" value="" />
											<input type="hidden" name="email1" value="" />
											<input type="hidden" name="email" value="" />
											<input type="hidden" name="phone" value="" />
											<input type="hidden" name="whatsapp" value="" />
											<input type="hidden" name="sender" value="" />
											<input type="hidden" name="senderphone" value="" />
											<input type="hidden" name="fonction" value="" />
											<input type="hidden" name="typedoc" value="Facture" />
											<input type="hidden" name="numero_document" value="" />
											<input type="hidden" name="montant" value="" />
											<input type="hidden" name="societe" value="" />
											<input type="hidden" name="delai_paiement" value="" />
											<input type="hidden" name="echeance_paiement" value="" />
											<input type="hidden" name="utilisateur" value="" />
											<input type="hidden" name="button" value="" />
											<input type="hidden" name="page" value="fc" />
											<input type="hidden" name="id" value="" />
											<a href="javascript:;" class="">Envoyer</a>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Popup -->
			<div tabindex="0" class="lx-popup addtags">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Affecter des tags</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="addtagsform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<fieldset style="margin-bottom:20px;padding:10px;background:#F8F8F8;">
												<legend>Affecter des tags: </legend>
												<div class="lx-labels">
													<?php
													$back = $bdd->query("SELECT * FROM doclabels WHERE type='client' AND trash='1'".$multicompanies);
													while($row = $back->fetch()){
														?>
													<a href="javascript:;" data-label="<?php echo $row['label'];?>" style="background:<?php echo $row['color'];?>;"><?php echo $row['label'];?></a>
														<?php
													}
													?>
												</div>
											</fieldset>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="id" value="0" />
											<input type="hidden" name="table" value="documents" />
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
									<h3>Export bons de livraison</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="exportform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="dateadd" data-title="Date création" checked /> Date création<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="company" data-title="Affecté à la société" checked /> Affecté à la société<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="code" data-title="Réf" checked /> Réf<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="client" data-title="Client" checked /> Client<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="iceclient" data-title="ICE client" checked /> ICE client<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="ifclient" data-title="IF client" checked /> IF client<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="labels" data-title="Tags" checked /> Tags<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="products" data-title="Détails bon de livraison" checked /> Détails bon de livraison<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="price1" data-title="Montant TTC sans remise" checked /> Montant TTC sans remise<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="discount" data-title="Remise HT" checked /> Remise HT<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="price2" data-title="Montant TTC avec remise" checked /> Montant TTC avec remise<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="note" data-title="Note" checked /> Note<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="abovetable" data-title="Note ou-dessus de la table" checked /> Note ou-dessus de la table<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="modepayment" data-title="Note ou-dessous de la table" checked /> Note ou-dessous de la table<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="user" data-title="Utilisateur" checked /> Utilisateur<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="table" value="documents" />
											<input type="hidden" name="type" value="bl" />
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
			<div tabindex="0" class="lx-popup zoombox">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons">close</i></a>
						<div class="lx-popup-details">
							<div class="lx-zoombox">
								<div class="lx-zoombox-content">
									
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
										<p>Voulez vous vraiment supprimer ce bon de livraison?</p>
										<a href="javascript:;" class="lx-delete-record" data-action="deletedocument" data-id="">Oui</a>
										<a href="javascript:;" class="lx-cancel-delete">Non</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Modal Structure -->
			<div id="bookingModal" class="modal">
			  <div class="modal-content">
				<span class="close-btn" onclick="closePopup()">&times;</span>
				<form id="bookingForm">
				  <div class="form-group">
						<label for="abovenote">Note au dessus :</label>
						<textarea id="abovenote" name="abovenote"></textarea>
				  </div>
				  <div class="form-group">
						<label for="belownote">Produit:</label>
						<span class="lx-product-label" style="font-size:18px;"></span>
				  </div>				  
				  <div class="form-group">
						<label for="belownote">Note au dessous:</label>
						<textarea id="belownote" name="belownote"></textarea>
				  </div>
				  <input type="hidden" id="idnote" value="0" />
				  <button type="button" class="submit-btn">Enregistrer</button>
				</form>
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
				loadBL($(".lx-pagination ul").attr("data-state"));
				loadPriceRange("bl");
				toDropDown();
			});
			$(function() {
				$('input[name="dateadd"]').daterangepicker({
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
						$('input[name="dateadd"]').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
						$('input[name="datestart"]').val(start.format('DD/MM/YYYY'));
						$('input[name="dateend"]').val(end.format('DD/MM/YYYY'));
						loadBL($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="dateadd"]').on('apply.daterangepicker', function(ev, picker) {
					$('input[name="dateadd"]').val(picker.startDate.format('DD/MM/YYYY') + " - " + picker.endDate.format('DD/MM/YYYY'));
					$('input[name="datestart"]').val(picker.startDate.format('DD/MM/YYYY'));
					$('input[name="dateend"]').val(picker.endDate.format('DD/MM/YYYY'));
					loadBL($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="dateadd"]').on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
					$('input[name="datestart"]').val('');
					$('input[name="dateend"]').val('');
					loadBL($(".lx-pagination ul").attr("data-state"));
				});
				// Add an event listener to the "Today" button
				$("body").delegate('.daterangepicker .ranges li:first-child','click', function() {
					// Get today's date
					var today = moment();

					// Set the start and end dates to today
					$('input[name="dateadd"]').data('daterangepicker').setStartDate(today);
					$('input[name="dateadd"]').data('daterangepicker').setEndDate(today);

					// Update the input field with the selected date range
					$('input[name="dateadd"]').val(today.format('DD/MM/YYYY') + ' - ' + today.format('DD/MM/YYYY'));
					$('input[name="datestart"]').val(today.format('DD/MM/YYYY'));
					$('input[name="dateend"]').val(today.format('DD/MM/YYYY'));

					// Trigger the "Apply" button to update the date range
					$('input[name="dateadd"]').data('daterangepicker').clickApply();
					loadBL($(".lx-pagination ul").attr("data-state"));
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
					if($(".command").css("display") === "block"){
						if($("#commandsform select[name='company']").val() !== ""){
							var ajaxurl = "ajax.php";
							$.ajax({
								url : ajaxurl,
								type : 'post',
								data : {
									company : $("#commandsform select[name='company']").val(),
									type : $("#commandsform input[name='type']").val(),
									dateadd : finalMoment.format(FORMAT),
									category : $("#commandsform input[name='categories']").val(),
									action : 'loadcode'
								},
								success : function(response){
									console.log(response);
									$("#commandsform input[name='code']").val(response);
									$(".lx-code-label").text($("#commandsform input[name='prefix']").val()+convertDateFormat($('#commandsform input[name="dateaddcommand"]').val())+"-");
									$("#commandsform input[name='code']").trigger("blur");
								}
							});
						}			
					}
					else{
						if($("#mergeform select[name='company']").val() !== ""){
							var ajaxurl = "ajax.php";
							$.ajax({
								url : ajaxurl,
								type : 'post',
								data : {
									company : $("#mergeform select[name='company']").val(),
									type : $("#mergeform input[name='type']").val(),
									dateadd : finalMoment.format(FORMAT),
									category : $("#mergeform input[name='categories']").val(),
									action : 'loadcode'
								},
								success : function(response){
									console.log(response);
									$("#mergeform input[name='code']").val(response);
									$(".lx-code-label1").text($("#mergeform input[name='prefix']").val()+convertDateFormat($('#mergeform input[name="dateaddcommand"]').val())+"-");
									$("#mergeform input[name='code']").trigger("blur");
								}
							});
						}							
					}
				}

				// When the user picks from the calendar
				$dateInput.on('apply.daterangepicker', function(ev, picker) {
					// picker.startDate is a strict moment already; just format it
					$(this).val(picker.startDate.format(FORMAT));
					// ensure internal state is correct (redundant but safe)
					picker.setStartDate(picker.startDate);
					picker.setEndDate(picker.startDate);
					if($(".command").css("display") === "block"){
						if($("#commandsform select[name='company']").val() !== ""){
							var ajaxurl = "ajax.php";
							$.ajax({
								url : ajaxurl,
								type : 'post',
								data : {
									company : $("#commandsform select[name='company']").val(),
									type : $("#commandsform input[name='type']").val(),
									dateadd : picker.startDate.format(FORMAT),
									category : $("#commandsform input[name='categories']").val(),
									action : 'loadcode'
								},
								success : function(response){
									console.log(response);
									$("#commandsform input[name='code']").val(response);
									$(".lx-code-label").text($("#commandsform input[name='prefix']").val()+convertDateFormat($('#commandsform input[name="dateaddcommand"]').val())+"-");
									$("#commandsform input[name='code']").trigger("blur");
								}
							});
						}			
					}
					else{
						if($("#mergeform select[name='company']").val() !== ""){
							var ajaxurl = "ajax.php";
							$.ajax({
								url : ajaxurl,
								type : 'post',
								data : {
									company : $("#mergeform select[name='company']").val(),
									type : $("#mergeform input[name='type']").val(),
									dateadd : picker.startDate.format(FORMAT),
									category : $("#mergeform input[name='categories']").val(),
									action : 'loadcode'
								},
								success : function(response){
									console.log(response);
									$("#mergeform input[name='code']").val(response);
									$(".lx-code-label1").text($("#mergeform input[name='prefix']").val()+convertDateFormat($('#mergeform input[name="dateaddcommand"]').val())+"-");
									$("#mergeform input[name='code']").trigger("blur");
								}
							});
						}							
					}
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
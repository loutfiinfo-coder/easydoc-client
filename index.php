<?php
session_start();
include("config.php");

$_SESSION['easybm_errorimport'] = "";

if(!isset($_SESSION['easybm_id'])){
	header('location: login.php');
}
else{
	if(!preg_match("#Consulter Tableau de bord#",$_SESSION['easybm_roles'])){	
		if(preg_match("#Consulter Historique des paiements#",$_SESSION['easybm_roles'])){	
			$page = "payments.php";
		}
		elseif(preg_match("#Consulter Factures clients#",$_SESSION['easybm_roles'])){	
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
		elseif(preg_match("#Consulter Clients#",$_SESSION['easybm_roles'])){	
			$page = "clients.php";
		}
		elseif(preg_match("#Consulter Bons de commande#",$_SESSION['easybm_roles'])){	
			$page = "bc.php";
		}
		elseif(preg_match("#Consulter Bons de réception#",$_SESSION['easybm_roles'])){	
			$page = "bre.php";
		}
		elseif(preg_match("#Consulter Fournisseurs#",$_SESSION['easybm_roles'])){	
			$page = "suppliers.php";
		}
		elseif(preg_match("#Consulter Prospects#",$_SESSION['easybm_roles'])){	
			header('location: prospects.php');
		}
		elseif(preg_match("#Consulter Tags#",$_SESSION['easybm_roles'])){	
			header('location: labels.php');
		}
		elseif(preg_match("#Consulter Modèles de messages#",$_SESSION['easybm_roles'])){	
			header('location: messages.php');
		}
		elseif(preg_match("#Consulter Sociétés#",$_SESSION['easybm_roles'])){	
			$page = "companies.php";
		}
		elseif(preg_match("#Consulter Utilisateurs#",$_SESSION['easybm_roles'])){	
			$page = "users.php";
		}
		else{
			$page = "login.php";
		}
		header('location: '.$page);
	}
}

if(isset($_SESSION['easybm_id']) AND isset($_SESSION['easybm_fullname'])){
?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<title>Tableau de bord</title>
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
						<h2>Tableau de bord</h2>
					</div>
					<div class="lx-clear-fix"></div>
					<div class="lx-page-content">
						<div class="lx-g1-f">
							<div class="lx-keyword lx-g1">
								<h3>Filtre global: </h3><br />
								<?php
								$styleday = "";
								$rangedate = gmdate("d/m/Y",time()-(60*60*24*29))." - ".gmdate("d/m/Y");
								$rangedateplaceholder = gmdate("d/m/Y",time()-(60*60*24*29))." - ".gmdate("d/m/Y");
								$startdate = gmdate("d/m/Y",time()-(60*60*24*29));
								$enddate = gmdate("d/m/Y");
								if(preg_match("#Consultation de la journée en cours seulement Tableau de bord#",$_SESSION['easybm_roles'])){
									$styleday = "display:none;";
									$rangedate = gmdate("d/m/Y")." - ".gmdate("d/m/Y");
									$rangedateplaceholder = "Date création";
									$startdate = gmdate("d/m/Y");
									$enddate = gmdate("d/m/Y");	
								}
								?>
								<label style="<?php echo $styleday;?>" class="lx-advanced-select">
									<span>Date création:</span>
									<input type="text" autocomplete="off" name="dateadd" id="dateadd" title="Date création" value="<?php echo $rangedate;?>" placeholder="" readonly style="background:white;cursor:pointer;" /></label>
								<input type="hidden" name="datestart" id="datestart" value="<?php echo $startdate;?>" />
								<input type="hidden" name="dateend" id="dateend" value="<?php echo $enddate;?>" />
								<label class="lx-advanced-select">
									<span>Sociétés</span>
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
								<label class="lx-advanced-select" style="<?php echo preg_match("#Consulter Clients|Consulter Factures clients|Consulter Devis|Consulter Factures proforma|Consulter Bons de livraison|Consulter Bons de sortie|Consulter Bons de retour|Consulter Factures avoir#",$_SESSION['easybm_roles'])?"":"display:none;";?>">
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
								<label class="lx-advanced-select" style="<?php echo preg_match("#Consulter Fournisseurs|Consulter Bons de commande|Consulter Bons de réception#",$_SESSION['easybm_roles'])?"":"display:none;";?>">
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
								<label>
									<a href="index.php" class="lx-refresh-filter"><i class="fa fa-sync-alt"></i></a>
								</label>								
							</div>
						</div>
					</div>
					<?php
					if(preg_match("#CA Tableau de bord#",$_SESSION['easybm_roles'])){
					?>
					<div class="lx-page-content">
						<div class="lx-g1-f">
							<div class="lx-g1 lx-pb-0">
								<h3>KPIs paiements</h3>
							</div>
							<div id="kpi">
								
							</div>
						</div>
					</div>
					<?php
					}
					?>
					<div class="lx-page-content">
						<div class="lx-g1-f">
							<div class="lx-g1 lx-pb-0">
								<h3>KPIs documents</h3>
							</div>
							<div id="documents">
								
							</div>
						</div>
					</div>
					<?php
					if(preg_match("#CA Tableau de bord#",$_SESSION['easybm_roles'])){
					?>
					<div class="lx-page-content">
						<div class="lx-g1-f">
							<div class="lx-keyword lx-g1">
								<h3>Filtre avancé: </h3><br />
								<label>
									<span>Type de documents:</span>
									<select name="typedoc" id="typedoc">
										<?php
										if(preg_match("#Consulter Factures clients#",$_SESSION['easybm_roles'])){
										?>
										<option value="facture" data-type="client">Factures clients</option>
										<?php
										}
										if(preg_match("#Consulter Factures fournisseurs#",$_SESSION['easybm_roles'])){
										?>
										<option value="facturesupplier" data-type="supplier">Factures fournisseurs</option>
										<?php
										}
										if(preg_match("#Consulter Notes honoraire#",$_SESSION['easybm_roles'])){
										?>
										<option value="nh" data-type="client">Notes d'honoraires</option>
										<?php
										}
										if(preg_match("#Consulter Notes de frais#",$_SESSION['easybm_roles'])){
										?>
										<option value="nf" data-type="client">Notes de frais</option>
										<?php
										}
										if(preg_match("#Consulter Devis,#",$_SESSION['easybm_roles'])){
										?>
										<option value="devis" data-type="client">Devis</option>
										<?php
										}
										if(preg_match("#Consulter Propositions honoraires#",$_SESSION['easybm_roles'])){
										?>
										<option value="ph" data-type="client">Propositions d'honoraires</option>
										<?php
										}
										if(preg_match("#Consulter Factures avoir#",$_SESSION['easybm_roles'])){
										?>
										<option value="avoir" data-type="client">Factures avoir</option>
										<?php
										}
										if(preg_match("#Consulter Bons de retour#",$_SESSION['easybm_roles'])){
										?>
										<option value="br" data-type="client">Bons de retour</option>
										<?php
										}
										if(preg_match("#Consulter Factures proforma#",$_SESSION['easybm_roles'])){
										?>
										<option value="pacturesproforma" data-type="client">Factures proforma</option>
										<?php
										}
										if(preg_match("#Consulter Bons de livraison#",$_SESSION['easybm_roles'])){
										?>
										<option value="bl" data-type="client">Bons de livraison</option>
										<?php
										}
										if(preg_match("#Consulter Bons de sortie#",$_SESSION['easybm_roles'])){
										?>
										<option value="bs" data-type="client">Bons de sortie</option>
										<?php
										}
										if(preg_match("#Consulter Bons de commande#",$_SESSION['easybm_roles'])){
										?>
										<option value="bc" data-type="supplier">Bons de commande</option>
										<?php
										}
										if(preg_match("#Consulter Bons de réception#",$_SESSION['easybm_roles'])){
										?>
										<option value="bre" data-type="supplier">Bons de réception</option>
										<?php
										}
										?>
									</select>
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
											$back = $bdd->query("SELECT label,type FROM doclabels WHERE trash='1'".$multicompanies." ORDER BY label");
											while($row = $back->fetch()){
												?>
											<li class="lx-tags-index" data-type="<?php echo $row['type'];?>"><label><input type="checkbox" value="<?php echo $row['label'];?>" data-ids="<?php echo $row['label'];?>" /> <?php echo $row['label'];?><del class="checkmark"></del></label></li>
												<?php
											}
											?>
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
							</div>
							<div class="lx-g1" style="padding-top:30px;border-top:1px dashed #BEBEBE;">
								<h3>Evolution nombre, valeur des documents</h3>
							</div>
							<div class="lx-table lx-g1 lx-table-ca">
								<ul>
									<li><span>Par: </span></li>
									<li><a href="javascript:;" class="active" data-period="<?php echo 60*60*24;?>">Jour</a></li>
									<li><a href="javascript:;" data-period="<?php echo 60*60*24*7;?>">Semaine</a></li>
									<li><a href="javascript:;" data-period="<?php echo 60*60*24*30;?>">Mois</a></li>
									<li><a href="javascript:;" data-period="<?php echo 60*60*24*365;?>">Année</a></li>
									<input type="hidden" name="period" id="period" value="<?php echo 60*60*24;?>" />
								</ul>
								<div class="lx-clear-fix"></div>
								<div class="lx-g1 lx-pl-0">
									<div id="ca"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="lx-page-content">
						<div class="lx-g1-f">
							<div class="lx-keyword lx-g1">
								<div class="lx-g1 lx-pl-0">
									<h3>Top 10 &nbsp;
										<label>
											<select name="topwho" id="topwho">
												<?php
												if(preg_match("#Consulter Clients|Consulter Factures clients|Consulter Devis|Consulter Factures proforma|Consulter Bons de livraison|Consulter Bons de sortie|Consulter Bons de retour|Consulter Factures avoir#",$_SESSION['easybm_roles'])){
												?>
												<option value="clients">Clients</option>
												<?php
												}
												?>
												<option value="products">Produits et Services</option>
												<?php
												if(preg_match("#Consulter Fournisseurs|Consulter Bons de commande|Consulter Bons de réception#",$_SESSION['easybm_roles'])){
												?>
												<option value="suppliers">Fournisseurs</option>
												<?php
												}
												?>
											</select>
										</label> 
										par &nbsp;<label>
											<select name="typedoc" id="typedoc2">
												<?php
												if(preg_match("#Consulter Factures clients#",$_SESSION['easybm_roles'])){
												?>
												<option value="facture" data-type="clients,products">Factures clients</option>
												<?php
												}
												if(preg_match("#Consulter Factures fournisseurs#",$_SESSION['easybm_roles'])){
												?>
												<option value="facturesupplier" data-type="suppliers">Factures fournisseurs</option>
												<?php
												}
												if(preg_match("#Consulter Notes honoraire#",$_SESSION['easybm_roles'])){
												?>
												<option value="nh" data-type="clients,products">Notes d'honoraires</option>
												<?php
												}
												if(preg_match("#Consulter Notes de frais#",$_SESSION['easybm_roles'])){
												?>
												<option value="nf" data-type="clients,products">Notes de frais</option>
												<?php
												}
												if(preg_match("#Consulter Devis,#",$_SESSION['easybm_roles'])){
												?>
												<option value="devis" data-type="clients,products">Devis</option>
												<?php
												}
												if(preg_match("#Consulter Propositions honoraires#",$_SESSION['easybm_roles'])){
												?>
												<option value="ph" data-type="clients,products">Propositions d'honoraires</option>
												<?php
												}
												if(preg_match("#Consulter Factures avoir#",$_SESSION['easybm_roles'])){
												?>
												<option value="avoir" data-type="clients,products">Factures avoir</option>
												<?php
												}
												if(preg_match("#Consulter Bons de retour#",$_SESSION['easybm_roles'])){
												?>
												<option value="br" data-type="clients,products">Bons de retour</option>
												<?php
												}
												if(preg_match("#Consulter Factures proforma#",$_SESSION['easybm_roles'])){
												?>
												<option value="pacturesproforma" data-type="clients,products">Factures proforma</option>
												<?php
												}
												if(preg_match("#Consulter Bons de livraison#",$_SESSION['easybm_roles'])){
												?>
												<option value="bl" data-type="clients,products">Bons de livraison</option>
												<?php
												}
												if(preg_match("#Consulter Bons de sortie#",$_SESSION['easybm_roles'])){
												?>
												<option value="bs" data-type="clients,products">Bons de sortie</option>
												<?php
												}
												if(preg_match("#Consulter Bons de commande#",$_SESSION['easybm_roles'])){
												?>
												<option value="bc" data-type="clients,products">Bons de commande</option>
												<?php
												}
												if(preg_match("#Consulter Bons de réception#",$_SESSION['easybm_roles'])){
												?>
												<option value="bre" data-type="clients,products">Bons de réception</option>
												<?php
												}
												?>
											</select>
										</label>
										par &nbsp;<label class="lx-advanced-select">
											<i class="fa fa-caret-down"></i>
											<input type="text" autocomplete="off" name="product" id="product2" placeholder="" data-ids="" title="Produits / Services" readonly />
											<div>
												<a href="javascript:;" class="lx-state-empty">Vider</a>
												<a href="javascript:;" class="lx-state-filter">Filtrer</a>
												<div class="lx-clear-fix"></div>
												<input type="text" placeholder="Rechercher ..." autocomplete="off" data-table="products" data-value="rs" data-ids="id" data-text="rs" name="searchadvanced" style="margin-bottom:20px;" />
												<ul>
												</ul>
											</div>
										</label>
									</h3>
									<div class="lx-clear-fix"></div>
									<div id="topca"></div>
								</div>
								<div class="lx-clear-fix"></div>
							</div>
						</div>
					</div>
					<?php
					}
					?>
					<div class="lx-clear-fix"></div>
				</div>
				<div class="lx-clear-fix"></div>
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
				var val = $("#typedoc option:selected").attr("data-type");
				$(".lx-tags-index").each(function(){
					if(val === $(this).attr("data-type")){
						$(this).css("display","block");
					}
					else{
						$(this).css("display","none");
					}
				});

				loadKPI();
				loadKPI1();
				loadCA();
				loadTop();
				$("#topwho").trigger("change");
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
					startDate: moment().startOf('day').subtract(30,'day'),
					endDate: moment(),
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
						loadKPI();
						loadKPI1();
						loadCA();
						loadTop();
				});
				$('input[name="dateadd"]').on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
					$('input[name="datestart"]').val('');
					$('input[name="dateend"]').val('');
					loadKPI();
					loadKPI1();
					loadCA();
					loadTop();
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

					// Trigger the "Apply" button to update the date range
					$('input[name="dateadd"]').data('daterangepicker').clickApply();
					loadKPI();
					loadKPI1();
					loadCA();
					loadTop();
				});
			});
		</script>
		<script src="js/highcharts.js"></script>
		<script src="js/exporting.js"></script>		
		<script src="js/export-data.js"></script>		
		<script src="js/annotations.js"></script>
	</body>
</html>
<?php
DB_Sanitize();
}
?>
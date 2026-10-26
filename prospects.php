<?php
session_start();
include("config.php");

if(!isset($_SESSION['easybm_id'])){
	header('location: login.php');
}
else{
	if(!preg_match("#Consulter Prospects#",$_SESSION['easybm_roles'])){
		header('location: 404.php');
	}
}

if(isset($_SESSION['easybm_id']) AND isset($_SESSION['easybm_fullname'])){
?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<title>Prospects</title>
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
						<h2>Prospects</h2>
					</div>
					<div class="lx-clear-fix"></div>
					<div class="lx-tabs">
						<?php
						if(preg_match("#Consulter Prospects#",$_SESSION['easybm_roles'])){	
							?>
						<a href="prospects.php" class="active">Prospects</a>
							<?php
						}
						if(preg_match("#Consulter Tags prospects#",$_SESSION['easybm_roles'])){	
							?>
						<a href="labels.php">Tags</a>
							<?php
						}
						?>
					</div>
					<div class="lx-page-content">
						<div class="lx-g1">
							<?php
							if(preg_match("#Ajouter Prospects#",$_SESSION['easybm_roles'])){	
							?>
							<div class="lx-add-form">
								<a href="javascript:;" class="lx-new lx-new-prospect lx-open-popup" data-header="Ajouter un nouveau" data-title="prospect">+ Nouveau prospect</a>
								<a href="javascript:;" class="lx-new lx-open-popup" data-title="importer"><i class="fa fa-upload"></i> Importer</a>
							</div>
							<?php
							}
							?>
							<div class="lx-keyword">
								<fieldset style="margin-bottom:20px;padding:10px;background:#F8F8F8;">
									<legend><b>Filtre avancé:</b></legend>
									<label><span>Mot clé:</span><a href="javascript:;" class="lx-search-keyword"><i class="fa fa-search"></i></a><input type="text" autocomplete="off" name="keyword" id="keyword" placeholder="" data-table="prospects" /></label>
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
												$back = $bdd->query("SELECT label FROM labels WHERE trash='1'".$multicompanies." ORDER BY label");
												while($row = $back->fetch()){
													?>
												<li><label><input type="checkbox" value="<?php echo $row['label'];?>" data-ids="<?php echo $row['label'];?>" /> <?php echo $row['label'];?><del class="checkmark"></del></label></li>
													<?php
												}
												?>
											</ul>
										</div>
									</label>
									<label><span>Date création:</span><input type="text" autocomplete="off" name="dateadd" id="dateadd" title="Date création" placeholder="" readonly style="background:white;cursor:pointer;" /></label>
									<input type="hidden" name="datestart" id="datestart" />
									<input type="hidden" name="dateend" id="dateend" />
									<label><span>Date mise à jour:</span><input type="text" autocomplete="off" name="dateupdate" id="dateupdate" title="Date mise à jour" placeholder="" readonly style="background:white;cursor:pointer;" /></label>
									<input type="hidden" name="datestartupdate" id="datestartupdate" />
									<input type="hidden" name="dateendupdate" id="dateendupdate" />
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
									<input type="hidden" name="sortby" value="" />
									<input type="hidden" name="orderby" value="DESC" />
									<label>
										<a href="prospects.php" class="lx-refresh-filter"><i class="fa fa-sync-alt"></i></a>
									</label>
								</fieldset>
							</div>
							<div class="lx-table-container">
								<div class="lx-table-shadow lx-table-shadow-left"></div>
								<div class="lx-table-shadow lx-table-shadow-right"></div>
								<div class="lx-table lx-table-prospects">

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
								if(preg_match("#Exporter Prospects#",$_SESSION['easybm_roles'])){
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
							$back = $bdd->query("SELECT COUNT(*) AS nb FROM prospects WHERE trash='1'");
							$row = $back->fetch();
							?>
							<div class="lx-pagination" style="<?php if($row['nb'] <= $nb){echo "display:none;";}?>">
								<?php
								$nbpages = ceil($row['nb']/$nb);
								?>
								<ul data-table="prospects" data-state="1" data-start="0" data-nbpage="<?php echo $nb;?>" data-posts="<?php echo $row['nb'];?>">
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
			<div tabindex="0" class="lx-popup prospect">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3><span></span> prospect</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="prospectsform">
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Affecté à la société<sup>*</sup>:</span>
												<select name="company" class="lx-companies-list" data-isnumber="" data-message="Choisissez une société">
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
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Source d'aquisition:</span>
												<select name="source" class="todropdown" class="">
													<option value="">Choisissez une source</option>
													<?php
													$back = $bdd->query("SELECT DISTINCT source FROM prospects WHERE trash='1'".$multicompanies." ORDER BY source");
													while($row = $back->fetch()){
														?>
													<option value="<?php echo $row['source'];?>"><?php echo $row['source'];?></option>
														<?php 
													}
													?>
												</select>
											</label>
										</div>	
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Genre<sup>*</sup>: </span>
												<select name="gender" data-isnotempty="" data-message="Choisissez un genre">
													<option value=""></option>
													<option value="Mr">Mr</option>
													<option value="Mme">Mme</option>
												</select>
											</label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label>
												<span>Nom et prénom<sup>*</sup>: </span>
												<select name="title" data-isnotempty="" data-message="Saisissez un nom" class="todropdown">
													<option value="">Saisissez un nom</option>
													<?php
													$back = $bdd->query("SELECT DISTINCT fullname FROM prospects WHERE fullname<>''".$multicompanies." ORDER BY fullname");
													while($row = $back->fetch()){
														?>
													<option value="<?php echo $row['fullname'];?>"><?php echo $row['fullname'];?></option>
														<?php 
													}
													?>
												</select>											
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Société prospect: </span><input type="text" autocomplete="off" name="company1" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>ICE: </span><input type="text" autocomplete="off" name="ice" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Téléphone<sup>*</sup>: </span><input type="text" autocomplete="off" name="phone" data-isphone="" data-message="Saisissez un téléphone" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>WhatsApp: </span><input type="text" autocomplete="off" name="whatsapp" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Email: </span><input type="text" autocomplete="off" name="email" data-isemail="" data-message="Saisissez un email valide" /></label>
										</div>
										<div class="lx-textfield lx-g2 lx-pb-0">
											<label><span>Adresse: </span><input type="text" autocomplete="off" name="address" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<fieldset style="margin-bottom:20px;padding:10px;background:#F8F8F8;">
												<legend>Affecter des tags: </legend>
												<div class="lx-labels">
													<?php
													$back = $bdd->query("SELECT * FROM labels WHERE trash='1'".$multicompanies);
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
									<h3>Importation prospects</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="importprospectsform" enctype="multipart/form-data">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<p><b>Important: </b>Veuillez impoter un fichier excel avec les colonnes suivantes en respectant l'ordre (Nom et prénom - Société prospect - Téléphone - WhatsApp - Email - Source - Tags - Note - IDs sociétés affectés séparer par point virgule ";" (Obligatoire)) ou bien <a href="ExampleProspects.xlsx" style="font-weight:normal;background:none;color:#39add1;">télécharger un modèle</a></p>
											<br />
											<p><b>NB: </b>Les prospect existants seront automatiquement ignorés</p>
											<br />
											<div class="lx-importer">
												<input type="file" name="xlsfile" id="importprospect" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
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
			<div tabindex="0" class="lx-popup prospectnote">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Mise à jour prospect note</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="prospectsnoteform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Note: </span><textarea name="note" data-isnotempty="" data-message="Saisissez une note"></textarea></label>
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
											<input type="hidden" name="table" value="prospects" />
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
												<select name="type" data-isnotempty="" data-message="Choisissez un canal">
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
												<select name="model" data-isnotempty="" data-message="Choisissez un modèle">
													<option value="">Choisissez un modèle</option>
													<?php
													$back = $bdd->query("SELECT * FROM messages WHERE trash='1' AND page='prospect'".$multicompanies);
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

											<textarea id="whatsappMessage" name="message" data-isnotempty="" data-message="Saisissez un message" rows="8" style="width:100%;"></textarea>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="fullname" value="" />
											<input type="hidden" name="email1" value="" />
											<input type="hidden" name="whatsapp" value="" />
											<input type="hidden" name="sender" value="" />
											<input type="hidden" name="email" value="" />
											<input type="hidden" name="gender" value="" />
											<input type="hidden" name="societe" value="" />
											<input type="hidden" name="phone" value="" />
											<input type="hidden" name="fonction" value="" />
											<input type="hidden" name="utilisateur" value="" />
											<input type="hidden" name="button" value="" />
											<input type="hidden" name="page" value="prospect" />
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
			<div tabindex="0" class="lx-popup prospecttoclient">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Transformer un prospect en client</h3>
								</div>
								<div class="lx-add-form">
									<div class="lx-delete-box">
										<p>Voulez vous vraiment transformer ce prospect en client?</p>
										<a href="javascript:;" class="lx-transform-record" data-id="">Oui</a>
										<a href="javascript:;" class="lx-cancel-delete">Non</a>
									</div>
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
													$back = $bdd->query("SELECT * FROM labels WHERE trash='1'".$multicompanies);
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
											<input type="hidden" name="table" value="prospects" />
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
									<h3>Export prospects</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="exportform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="fullname" data-title="Nom et prénom" checked /> Nom et prénom<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="company1" data-title="Société prospect" checked /> Société prospect<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="phone" data-title="Téléphone" checked /> Téléphone<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="email" data-title="Email" checked /> Email<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="source" data-title="Source" checked /> Source<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="labels" data-title="Labels" checked /> Labels<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="note" data-title="Note" checked /> Note<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="recalldate" data-title="Date Rappel" checked /> Date Rappel<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="recallnote" data-title="Note Rappel" checked /> Note Rappel<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="company" data-title="Affecté à la société" checked /> Affecté à la société<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="dateadd" data-title="Date ajout" checked /> Date ajout<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="dateupdate" data-title="Date mise à jour" checked /> Date mise à jour<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="user" data-title="Utilisateur" checked /> Utilisateur<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="table" value="prospects" />
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
			<div tabindex="0" class="lx-popup messagehistory">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3>Historique des messages</h3>
								</div>
								<div class="lx-add-form">
									<div class="lx-table lx-table-messageshistory">
									
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
										<p>Voulez vous vraiment supprimer ce prospect?</p>
										<a href="javascript:;" class="lx-delete-record" data-action="deleteprospect" data-id="">Oui</a>
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
				loadProspects($(".lx-pagination ul").attr("data-state"));
				toDropDown();
			});
			
			function wrapText(before, after) {
			  const textarea = document.getElementById('whatsappMessage');
			  const start = textarea.selectionStart;
			  const end = textarea.selectionEnd;
			  const selected = textarea.value.substring(start, end);

			  const newText = before + selected + after;
			  textarea.setRangeText(newText, start, end, 'end');
			  textarea.focus();
			}

			function insertNewLine() {
			  const textarea = document.getElementById('whatsappMessage');
			  const pos = textarea.selectionStart;
			  const before = textarea.value.substring(0, pos);
			  const after = textarea.value.substring(pos);
			  textarea.value = before + "\n" + after;
			  textarea.selectionStart = textarea.selectionEnd = pos + 1;
			  textarea.focus();
			}
			
			const textarea = document.getElementById('whatsappMessage');

			textarea.addEventListener('keydown', function(event) {
			  if (event.key === 'Enter') {
				event.preventDefault();  // Prevent the default Enter behavior (like submitting a form)
				
				const start = this.selectionStart;
				const end = this.selectionEnd;
				const value = this.value;

				// Insert newline at cursor position or replace selection
				this.value = value.substring(0, start) + '\n' + value.substring(end);

				// Move cursor after the newline
				this.selectionStart = this.selectionEnd = start + 1;
			  }
			});
			
			$(function() {
				$('input[name="dateadd"]').daterangepicker({
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
						$('input[name="dateadd"]').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
						$('input[name="datestart"]').val(start.format('DD/MM/YYYY'));
						$('input[name="dateend"]').val(end.format('DD/MM/YYYY'));
						loadProspects($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="dateadd"]').on('apply.daterangepicker', function(ev, picker) {
					$('input[name="dateadd"]').val(picker.startDate.format('DD/MM/YYYY') + " - " + picker.endDate.format('DD/MM/YYYY'));
					$('input[name="datestart"]').val(picker.startDate.format('DD/MM/YYYY'));
					$('input[name="dateend"]').val(picker.endDate.format('DD/MM/YYYY'));
					loadProspects($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="dateadd"]').on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
					$('input[name="datestart"]').val('');
					$('input[name="dateend"]').val('');
					loadProspects($(".lx-pagination ul").attr("data-state"));
				});
				
				$('input[name="dateupdate"]').daterangepicker({
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
						$('input[name="dateupdate"]').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
						$('input[name="datestartupdate"]').val(start.format('DD/MM/YYYY'));
						$('input[name="dateendupdate"]').val(end.format('DD/MM/YYYY'));
						loadProspects($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="dateupdate"]').on('apply.daterangepicker', function(ev, picker) {
					$('input[name="dateupdate"]').val(picker.startDate.format('DD/MM/YYYY') + " - " + picker.endDate.format('DD/MM/YYYY'));
					$('input[name="datestartupdate"]').val(picker.startDate.format('DD/MM/YYYY'));
					$('input[name="dateendupdate"]').val(picker.endDate.format('DD/MM/YYYY'));
					loadProspects($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="dateupdate"]').on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
					$('input[name="datestartupdate"]').val('');
					$('input[name="dateendupdate"]').val('');
					loadProspects($(".lx-pagination ul").attr("data-state"));
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
						loadProspects($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="daterecall"]').on('apply.daterangepicker', function(ev, picker) {
					$('input[name="daterecall"]').val(picker.startDate.format('DD/MM/YYYY') + " - " + picker.endDate.format('DD/MM/YYYY'));
					$('input[name="datestartrecall"]').val(picker.startDate.format('DD/MM/YYYY'));
					$('input[name="dateendrecall"]').val(picker.endDate.format('DD/MM/YYYY'));
					loadProspects($(".lx-pagination ul").attr("data-state"));
				});
				$('input[name="daterecall"]').on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
					$('input[name="datestartrecall"]').val('');
					$('input[name="dateendrecall"]').val('');
					loadProspects($(".lx-pagination ul").attr("data-state"));
				});					
				
				// Add an event listener to the "Today" button
				let activeInput = null;

				// Track the active input when focus occurs
				$('input[data-toggle="daterangepicker"]').on('show.daterangepicker', function() {
					activeInput = this; // Save the currently active input
				});

				// Handle click on "Today" range (first li in ranges list)
				$('body').on('click', '.daterangepicker .ranges li:first-child', function() {
					if (!activeInput) return;

					var today = moment();
					var $input = $(activeInput);

					$input.data('daterangepicker').setStartDate(today);
					$input.data('daterangepicker').setEndDate(today);

					var formatted = today.format('DD/MM/YYYY');
					$input.val(`${formatted} - ${formatted}`);

					// Optional: update hidden or related fields if they follow naming convention
					$('input[name="datestart"]').val(formatted);
					$('input[name="dateend"]').val(formatted);

					// Trigger the apply event (simulate clicking Apply)
					$input.trigger('apply.daterangepicker', [today, today]);

					// Load relevant data
					loadProspects($(".lx-pagination ul").attr("data-state"));
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
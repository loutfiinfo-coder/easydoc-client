<?php
session_start();
include("config.php");

if(!isset($_SESSION['easybm_id'])){
	header('location: login.php');
}
else{
	if(!preg_match("#Consulter Modèles de messages#",$_SESSION['easybm_roles'])){
		header('location: 404.php');
	}
}

if(isset($_SESSION['easybm_id']) AND isset($_SESSION['easybm_fullname'])){
?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<title>Modèles de messages</title>
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
						<h2>Modèles de messages</h2>
					</div>
					<div class="lx-clear-fix"></div>
					<div class="lx-tabs">
						<?php
						if(preg_match("#Consulter Modèles de messages factures clients#",$_SESSION['easybm_roles'])){	
							?>
						<a href="messages.php?type=fc" class="<?php echo $_GET['type']=="fc"?"active":"";?>">Modèles de messages factures clients</a>
							<?php
						}
						if(preg_match("#Consulter Modèles de messages factures fournisseurs#",$_SESSION['easybm_roles'])){	
							?>
						<a href="messages.php?type=fs" class="<?php echo $_GET['type']=="fs"?"active":"";?>">Modèles de messages factures fournissseurs</a>
							<?php
						}
						if(preg_match("#Consulter Modèles de messages prospects#",$_SESSION['easybm_roles'])){	
							?>
						<a href="messages.php?type=prospect" class="<?php echo $_GET['type']=="prospect"?"active":"";?>">Modèles de messages prospects</a>
							<?php
						}
						if(preg_match("#Consulter Modèles de messages d'envoi de documents#",$_SESSION['easybm_roles'])){	
							?>
						<a href="messages.php?type=document" class="<?php echo $_GET['type']=="document"?"active":"";?>">Modèles de messages d'envoi des documents</a>
							<?php
						}
						?>
					</div>
					<div class="lx-page-content">
						<div class="lx-g1">
							<?php
							if(preg_match("#Ajouter Modèles de messages#",$_SESSION['easybm_roles'])){	
							?>
							<div class="lx-add-form">
								<a href="javascript:;" class="lx-new lx-new-message lx-open-popup" data-header="Ajouter un nouveau" data-title="message">+ Nouveau message</a>
							</div>
							<?php
							}
							?>
							<div class="lx-keyword">
								<fieldset style="margin-bottom:20px;padding:10px;background:#F8F8F8;">
									<legend><b>Filtre avancé:</b></legend>
									<label><span>Mot clé:</span><a href="javascript:;" class="lx-search-keyword"><i class="fa fa-search"></i></a><input type="text" autocomplete="off" name="keyword" id="keyword" placeholder="" data-table="messages" /></label>
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
									<label style="<?php echo $_GET['type']=="document"?"display:none;":"";?>">
										<span>Canal:</span>
										<select name="type" id="type">
											<option value="">Choisissez une canal</option>
											<option value="WhatsApp">WhatsApp</option>
											<option value="E-mail">E-mail</option>
										</select>
									</label>
									<input type="hidden" name="page" id="page" value="<?php echo isset($_GET['type'])?$_GET['type']:"";?>" />
									<input type="hidden" name="sortby" value="" />
									<input type="hidden" name="orderby" value="DESC" />
									<label>
										<a href="messages.php?type=<?php echo isset($_GET['type'])?$_GET['type']:"";?>" class="lx-refresh-filter"><i class="fa fa-sync-alt"></i></a>
									</label>
								</fieldset>
							</div>
							<div class="lx-table-container">
								<div class="lx-table-shadow lx-table-shadow-left"></div>
								<div class="lx-table-shadow lx-table-shadow-right"></div>
								<div class="lx-table lx-table-messages">

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
								if(preg_match("#Exporter Modèles de messages#",$_SESSION['easybm_roles'])){
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
							$back = $bdd->query("SELECT COUNT(*) AS nb FROM messages WHERE trash='1'");
							$row = $back->fetch();
							?>
							<div class="lx-pagination" style="<?php if($row['nb'] <= $nb){echo "display:none;";}?>">
								<?php
								$nbpages = ceil($row['nb']/$nb);
								?>
								<ul data-table="messages" data-state="1" data-start="0" data-nbpage="<?php echo $nb;?>" data-posts="<?php echo $row['nb'];?>">
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
			<div tabindex="0" class="lx-popup message">
				<div class="lx-popup-inside">
					<div class="lx-popup-content">
						<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
						<div class="lx-popup-details">
							<div class="lx-form">
								<div class="lx-form-title">
									<h3><span></span> message</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="messagesform">
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
											<label><span>Botton<sup>*</sup>: 
												<select name="button" id="button" data-isnotempty="" data-message="Choisissez un botton!!">
													<option value="">Choisissez un botton</option>
													<?php
													if($_GET['type']=="fc"){
													?>
													<option value="Recouvrement">Recouvrement</option>
													<option value="Rappel paiement RAS">Rappel paiement RAS</option>
													<?php
													}
													if($_GET['type']=="document"){
													?>
													<option value="Envoyer le document">Envoyer le document</option>
													<?php
													}
													if($_GET['type']=="fs"){
													?>
													<option value="Rappel encaissement">Rappel encaissement</option>
													<?php
													}
													if($_GET['type']=="prospect"){
													?>
													<option value="Envoyer un message">Envoyer un message</option>
													<?php
													}
													?>
												</select>
											</label>
										</div>
										<div class="lx-textfield lx-g1 lx-pb-0 type">
											<label><span>Type<sup>*</sup>: 
												<select name="type" id="type" data-isnotempty="" data-message="Choisissez un type!!">
													<option value="">Choisissez un type</option>
													<option value="WhatsApp">WhatsApp</option>
													<option value="E-mail">E-mail</option>
												</select>
											</label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Titre<sup>*</sup>: </span><input type="text" autocomplete="off" name="title" data-isnotempty="" data-message="Saisissez un titre!!" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0 subject" style="display:none;">
											<label><span>Objet<sup>*</sup>: </span><input type="text" autocomplete="off" name="subject" data-isnotempty="" data-message="Saisissez un objet!!" /></label>
											<?php
											if(isset($_GET['type'])){
												if($_GET['type'] == "prospect"){
													?>
											<p style="margin-top:5px;"><i>Liste des variables: </i><b>[gender], [fullname], [phone], [email], [societe], [utilisateur], [telephone_utilisateur],[email_utilisateur], [fonction_utilisateur]</b></p>
													<?php
												}
												elseif($_GET['type'] == "fs"){
													?>
											<p style="margin-top:5px;"><i>Liste des variables: </i><b>[fullname], [phone], [email], [societe], [numero_document], [ref_commande], [utilisateur], [montant], [telephone_utilisateur],[email_utilisateur], [fonction_utilisateur]</b></p>
													<?php
												}
												elseif($_GET['type'] == "document"){
													?>
											<p style="margin-top:5px;"><i>Liste des variables: </i><b>[client_ou_fournisseur], [numero_document], [montant], [societe], [delai_paiement], [echeance_paiement], [type_document], [utilisateur], [telephone_utilisateur],[email_utilisateur], [fonction_utilisateur]</b></p>
													<?php
												}
												else{
													?>
											<p style="margin-top:5px;"><i>Liste des variables: </i><b>[client], [numero_document], [montant], [societe], [delai_paiement], [echeance_paiement], [type_document], [utilisateur], [telephone_utilisateur],[email_utilisateur], [fonction_utilisateur]</b></p>
													<?php													
												}
											}
											?>
										</div>
										<div class="lx-clear-fix"></div>
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
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><span>Message<sup>*</sup>: </span>
											<div class="editor-toolbar">
											  <button type="button" onclick="wrapText('*', '*')">Gras</button>
											  <button type="button" onclick="wrapText('_', '_')">Italic</button>
											  <button type="button" onclick="wrapText('~', '~')">Barré</button>
											</div>

											<textarea id="whatsappMessage" name="message" data-isnotempty="" data-message="Saisissez un message!!" rows="8" style="width:100%;"></textarea>
											</label>
											<?php
											if(isset($_GET['type'])){
												if($_GET['type'] == "prospect"){
													?>
											<p style="margin-top:5px;"><i>Liste des variables: </i><b>[gender], [fullname], [phone], [email], [societe], [utilisateur], [telephone_utilisateur],[email_utilisateur], [fonction_utilisateur]</b></p>
													<?php
												}
												elseif($_GET['type'] == "fs"){
													?>
											<p style="margin-top:5px;"><i>Liste des variables: </i><b>[fullname], [phone], [email], [societe], [numero_document], [ref_commande], [utilisateur], [montant], [telephone_utilisateur],[email_utilisateur], [fonction_utilisateur]</b></p>
													<?php
												}
												elseif($_GET['type'] == "document"){
													?>
											<p style="margin-top:5px;"><i>Liste des variables: </i><b>[client_ou_fournisseur], [numero_document], [montant], [societe], [delai_paiement], [echeance_paiement], [type_document], [utilisateur], [telephone_utilisateur],[email_utilisateur], [fonction_utilisateur]</b></p>
													<?php
												}
												else{
													?>
											<p style="margin-top:5px;"><i>Liste des variables: </i><b>[client], [numero_document], [montant], [societe], [delai_paiement], [echeance_paiement], [type_document], [utilisateur], [telephone_utilisateur],[email_utilisateur], [fonction_utilisateur]</b></p>
													<?php													
												}
											}
											?>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="page" value="<?php echo isset($_GET['type'])?$_GET['type']:"";?>" />
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
									<h3>Export message</h3>
								</div>
								<div class="lx-add-form">
									<form autocomplete="off" action="#" method="post" id="exportform">
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="type" data-title="Type" checked /> Type<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="title" data-title="Titre" checked /> Titre<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g1 lx-pb-0">
											<label><input type="checkbox" name="columns" value="message" data-title="Message" checked /> Message<del class="checkmark"></del></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-submit lx-g1 lx-pb-0">
											<input type="hidden" name="table" value="messages" />
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
										<p>Voulez vous vraiment supprimer ce message?</p>
										<a href="javascript:;" class="lx-delete-record" data-action="deletemessage" data-id="">Oui</a>
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
				loadMessages($(".lx-pagination ul").attr("data-state"));
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

		</script>
	</body>
</html>
<?php
DB_Sanitize();
}
?>
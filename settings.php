<?php
session_start();
include("config.php");

if(!isset($_SESSION['easybm_id'])){
	header('location: login.php');
}

if(isset($_SESSION['easybm_id']) AND isset($_SESSION['easybm_fullname'])){
?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<title>Affichage</title>
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
						<h2>Affichage</h2>
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
						<div class="lx-g2 lx-pb-0">
							<div class="lx-add-form">
								<form autocomplete="off" action="#" method="post" id="settingsform" autocomplete="off">
									<div style="<?php echo $_SESSION['easybm_superadmin']!="1"?"display:none;":"";?>">
										<div class="lx-textfield" style="<?php if($_SESSION['easybm_type'] != "moderator"){echo "display:none;";}?>">
											<input type="file" name="medias" id="medias" accept="image/x-png,image/jpeg" />
											<input type="hidden" name="picture" value="<?php echo $settings['logo'];?>" />
											<a href="javascript:;" class="lx-upload-picture">Ajouter logo</a>
										</div>
										<div class="lx-medias-item">
											<?php
											$logo = "images/logo.png";
											if($settings['logo'] != "logo.png"){
												$logo = "uploads/".$settings['logo'];
											}
											?>
											<div><img src="<?php echo $logo;?>" class="logo" /></div>
											<a href="javascript:;" class="lx-reset-photo lx-open-popup" data-title="deleterecord" data-file="medias" data-img="ce logo" data-input="picture" data-default="logo.png"><img src="images/close1.svg" /></a>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield" style="<?php if($_SESSION['easybm_type'] != "moderator"){echo "display:none;";}?>">
											<input type="file" name="mediascover" id="mediascover" accept="image/x-png,image/jpeg" />
											<input type="hidden" name="cover" value="<?php echo $settings['cover'];?>" />
											<a href="javascript:;" class="lx-upload-picture">Ajouter couverture</a>
										</div>
										<div class="lx-medias-item-cover">
											<?php
											$cover = "images/bg.jpg";
											if($settings['cover'] != "bg.jpg"){
												$cover = "uploads/".$settings['cover'];
											}
											?>
											<div><img src="<?php echo $cover;?>" class="cover" /></div>
											<a href="javascript:;" class="lx-reset-photo lx-open-popup" data-title="deleterecord" data-file="mediascover" data-img="cette couverture" data-input="cover" data-default="bg.jpg"><img src="images/close1.svg" /></a>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2-f">
											<label><span>Nom de l'application: </span><input type="text" autocomplete="off" name="store" value="<?php echo $settings['store'];?>" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2-f">
											<label><span>Devise par default: </span><input type="text" autocomplete="off" name="currency" value="<?php echo $settings['currency'];?>" /></label>
										</div>
										<div class="lx-clear-fix"></div>
										<div class="lx-textfield lx-g2-f" style="display:none;">
											<label><input type="checkbox" name="rowcolor" <?php if($parametres['rowcolor']=="1"){echo "checked";}?> /> Colorer la ligne entier de la commande<del class="checkmark"></del></label>
										</div>
										<div class="lx-textfield lx-g2-f" style="display:none;">
											<label><span>État de commande client par defaut: </span></label>
											<label style="display:block;margin:10px 0px;"><input type="radio" name="defaultstate" value="Nouvelle" <?php if($settings['defaultstate']=="Nouvelle"){echo "checked";}?> /> Nouvelle <del class="circlemark"></del></label>
											<label style="display:block;margin:10px 0px;"><input type="radio" name="defaultstate" value="Expédiée" <?php if($settings['defaultstate']=="Expédiée"){echo "checked";}?> /> Expédiée <del class="circlemark"></del></label>
											<label style="display:block;margin:10px 0px;"><input type="radio" name="defaultstate" value="Livrée" <?php if($settings['defaultstate']=="Livrée"){echo "checked";}?> /> Livrée <del class="circlemark"></del></label>
										</div>										
									</div>
									<div class="lx-clear-fix"></div>
									<?php
									$nb = 50;
									if($parametres['nbrows'] != "" AND $parametres['nbrows'] != "0"){
										$nb = $parametres['nbrows'];
									}	
									?>
									<div class="lx-textfield lx-g2-f">
										<label><span>Nombre de ligne d'affichage sur les tableaux: </span>
											<select name="nbrows">
												<option value="50" <?php if($nb==50){echo "selected";}?>>50</option>
												<option value="100" <?php if($nb==100){echo "selected";}?>>100</option>
												<option value="200" <?php if($nb==200){echo "selected";}?>>200</option>
												<option value="500" <?php if($nb==500){echo "selected";}?>>500</option>
											</select>
										</label>
									</div>
									<div class="lx-clear-fix"></div>
									<div class="lx-submit">
										<input type="hidden" name="id" value="<?php echo $settings['id'];?>" />
										<a href="javascript:;">Enregistrer</a>
									</div>
								</form>
							</div>
						</div>
						<div class="lx-clear-fix"></div>
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
										<p>Voulez vous vraiment supprimer <ins class="lx-title"></ins>?</p>
										<a href="javascript:;" class="lx-reset-pictures" data-file="" data-img="" data-input="" data-default="">Oui</a>
										<a href="javascript:;" class="lx-cancel-delete">Non</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- JQuery -->
		<script src="js/jquery-1.12.4.min.js"></script>
		<!-- Popup Script -->
		<script src="js/jquery.popup.js"></script>
		<!-- Main Script -->
		<script src="js/script.js?v=<?php echo rand();?>"></script>
	</body>
</html>
<?php
DB_Sanitize();
}
?>
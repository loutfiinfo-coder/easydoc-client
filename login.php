<?php
session_start();
include("config.php");

if(isset($_POST['username'])){
	$back = $bdd->query("SELECT * FROM users WHERE email='".sanitize_vars($_POST['username'])."' AND password='".sanitize_vars($_POST['password'])."'");
	if($back->rowCount() == 1){
		$back = $bdd->query("SELECT * FROM users WHERE email='".sanitize_vars($_POST['username'])."' AND password='".sanitize_vars($_POST['password'])."' AND trash='1'");
		if($back->rowCount() == 1){
			$row = $back->fetch();
			$_SESSION['easybm_id'] = $row['id'];
			$_SESSION['easybm_fullname'] = $row['fullname'];
			$_SESSION['easybm_picture'] = $row['picture'];
			$_SESSION['easybm_phone'] = $row['phone'];
			$_SESSION['easybm_email'] = $row['email'];
			$_SESSION['easybm_password'] = $row['password'];
			$_SESSION['easybm_roles'] = $row['roles'];
			$_SESSION['easybm_companies'] = "0,".$row['companies']!=""?$row['companies']:0;
			$_SESSION['easybm_type'] = $row['type'];
			$_SESSION['easybm_superadmin'] = $row['superadmin'];

			if(isset($_POST['rememberme'])){
				setcookie('rememberme', 'yes', time() + (86400 * 2));
				setcookie('id', $_SESSION['easybm_id'], time() + (86400 * 2));
				setcookie('fullname', $_SESSION['easybm_fullname'], time() + (86400 * 2));
				setcookie('picture', $_SESSION['easybm_picture'], time() + (86400 * 2));
				setcookie('phone', $_SESSION['easybm_phone'], time() + (86400 * 2));
				setcookie('email', $_SESSION['easybm_email'], time() + (86400 * 2));
				setcookie('password', $_SESSION['easybm_password'], time() + (86400 * 2));
				setcookie('roles', $_SESSION['easybm_roles'], time() + (86400 * 2));
				setcookie('companies', $_SESSION['easybm_companies'], time() + (86400 * 2));
				setcookie('type', $_SESSION['easybm_type'], time() + (86400 * 2));
				setcookie('superadmin', $_SESSION['easybm_superadmin'], time() + (86400 * 2));
			}
			else{
				setcookie('rememberme', 'yes', time() - 3600);
				setcookie('id', $_SESSION['easybm_id'], time() - 3600);
				setcookie('fullname', $_SESSION['easybm_fullname'], time() - 3600);
				setcookie('picture', $_SESSION['easybm_picture'], time() - 3600);
				setcookie('phone', $_SESSION['easybm_phone'], time() - 3600);
				setcookie('email', $_SESSION['easybm_email'], time() - 3600);
				setcookie('password', $_SESSION['easybm_password'], time() - 3600);
				setcookie('roles', $_SESSION['easybm_roles'], time() - 3600);
				setcookie('companies', $_SESSION['easybm_companies'], time() - 3600);
				setcookie('type', $_SESSION['easybm_type'], time() - 3600);	
				setcookie('superadmin', $_SESSION['easybm_superadmin'], time() - 3600);	
			}
						
			if(preg_match("#Consulter Tableau de bord#",$_SESSION['easybm_roles'])){	
				$page = "index.php";
			}
			elseif(preg_match("#Consulter Historique des paiements#",$_SESSION['easybm_roles'])){	
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
	else{
		$error = 'Login ou mot de passe est incorrect';
	}
}

function sanitize_vars($var){
	if(preg_match("#script|select|update|delete|concat|create|table|union|length|show_table|mysql_list_tables|mysql_list_fields|mysql_list_dbs#i",$var)){
		$var = "";
	}
	return htmlspecialchars(addslashes(trim($var)));
}

if(file_exists("configdb.data")){
	unlink("configdb.data");
}
if(file_exists("installer.php")){
	unlink("installer.php");
}

?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<title>Login</title>
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
		<link href="https://use.fontawesome.com/releases/v5.0.7/css/all.css" rel="stylesheet">
		<!-- Fav Icon -->
		<link rel="shortcut icon" href="favicon.ico">
		<?php include("onesignal.php");?>
	</head>
	<body>

		<!-- Wrapper -->
		<div class="lx-wrapper">
			<!-- Main -->
			<div class="lx-main">
				<div class="lx-left-bg">
					<div class="lx-login">
						<div class="lx-login-content">
							<center><img src="<?php echo $settings['logo']=="logo.png"?"images/".$settings['logo']:"uploads/".$settings['logo'];?>" /></center>
							<p>Se connecter</p>
							<form action="login.php" method="post">
								<?php
								if(isset($error)){
									?>
								<p class="lx-login-error"><?php echo $error;?></p>
									<?php
								}
								?>
								<div class="lx-textfield">
									<label><input type="text" autocomplete="off" name="username" value="<?php echo isset($_COOKIE['email'])?$_COOKIE['email']:"";?>" placeholder="Login" /></label>
								</div>
								<div class="lx-textfield">
									<label><input type="password" name="password" value="<?php echo isset($_COOKIE['password'])?$_COOKIE['password']:"";?>" placeholder="Mot de passe" /><i class="fa fa-eye-slash"></i></label>
								</div>
								<div class="lx-textfield">
									<label style="float:left;"><input type="checkbox" name="rememberme" value="yes" <?php echo isset($_COOKIE['rememberme'])?"checked":"";?> /> Se souvenir de moi<del class="checkmark"></del></label>
									<div class="lx-clear-fix"></div>
								</div>
								<div class="lx-submit">
									<a href="javascript:;">Se connecter</a>
								</div>
							</form>
						</div>
					</div>				
				</div>
				<?php
				$cover = "images/bg.jpg";
				if($settings['cover'] != "bg.jpg"){
					$cover = "uploads/".$settings['cover'];
				}
				?>				
				<div class="lx-right-bg" style="background:url('<?php echo $cover;?>');background-size:cover;">
					<div class="lx-right-bg-shadow">
						<div>
							<h3>EasyDoc</h3>
							<b>Your Commercial Docs Creator!</b>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php DB_Sanitize();?>
		<!-- JQuery -->
		<script src="js/jquery-1.12.4.min.js"></script>
		<!-- Main Script -->
		<script src="js/script.js?v=<?php echo rand();?>"></script>
	</body>
</html>
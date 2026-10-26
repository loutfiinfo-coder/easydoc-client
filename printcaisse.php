<?php
session_start();
include("config.php");

?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<title>Bon de caisse</title>
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
		<style>
			@page { size: auto;  margin: 0mm; }
		</style>
	</head>
	<body style="background:#FFFFFF;">
		<div class="lx-caisse">
			<?php
			$back = $bdd->query("SELECT * FROM payments WHERE id='".$_GET['id']."'");
			$caisse = $back->fetch();		
			?>
			<div class="lx-caisse-bloc2" style="float:none;">
				<p style="float:right;">Date: <b><?php echo date("d/m/Y H:i:s",$caisse['dateadd']);?></b></p>
				<div class="lx-clear-fix"></div>
			</div>
			<div class="lx-clear-fix"></div>
			<div class="lx-caisse-bloc3">
				<?php
				$type = "d'entrée";
				if($caisse['type'] == "Sortie"){
					$type = "sortie";
				}
				?>
				<h2>Bon <?php echo $type;?> caisse: <?php echo $caisse['code'];?></h2>
			</div>	
			<div class="lx-caisse-bloc4">
				<p>Montant total : <b># <?php echo $caisse['price'];?> <?php echo $settings['currency'];?> #</b></p>
			</div>		
			<div class="lx-caisse-bloc5">
				<p>Montant en lettres : <b># <?php echo chifre_en_lettre($caisse['price'],$settings['currency']);?> #</b></p>
				<p>Libellé : <b><?php echo $caisse['title'];?></b></p>
				<p>Note : <b><?php echo $caisse['note'];?></b></p>
			</div>	
			<div class="lx-caisse-bloc6">
				<?php
				$back = $bdd->query("SELECT fullname FROM users WHERE id='".$caisse['worker']."'");
				$user = $back->fetch();
				?>
				<p>Caissier(e): <b><?php echo $user['fullname'];?></b></p>
				<p>Direction:</p>
			</div>			
		</div>
		<script>
			window.print();
		</script>
	</body>
</html>
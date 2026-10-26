<?php
session_start();
// release lock so this request doesn't block others
session_write_close();
include("config.php");
include('classes/Utilities.class.php');
include('classes/SimpleImage.class.php');
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_SESSION['easybm_id']) AND isset($_SESSION['easybm_fullname'])){
	if($_SESSION['easybm_id'] != "" AND $_SESSION['easybm_fullname'] != ""){
		if(isset($_POST['action'])){
			if($_POST['action'] == "loadprintoptions"){
				$back = $bdd->query("SELECT * FROM documents WHERE id='" . $_POST['id'] . "'");
				$row1 = $back->fetch(PDO::FETCH_ASSOC) ?: [];

				$back = $bdd->query(
					"SELECT * FROM printoptions 
					 WHERE document='" . ($row1['type'] ?? '') . "' 
					 AND company='" . ($row1['company'] ?? '') . "'"
				);
				$row = $back->fetch(PDO::FETCH_ASSOC) ?: [];

				/* Default values (PHP 7.4+ compatible) */
				$row['signature'] = $row['signature'] ?? '';
				$row['header']    = $row['header'] ?? '';
				$row['ice']       = $row['ice'] ?? '';
				$row['valeur']    = $row['valeur'] ?? '';
				$row['label']     = $row['label'] ?? '';
				$row['d1']        = $row['d1'] ?? '';
				$row['d2']        = $row['d2'] ?? '';
				$row['d3']        = $row['d3'] ?? '';
				$row['d4']        = $row['d4'] ?? '';
				$row['d5']        = $row['d5'] ?? '';
				$row['d6']        = $row['d6'] ?? '';
				$row['payments']  = $row['payments'] ?? '';
				$row['avoirs']    = $row['avoirs'] ?? '';
				$row['reste']     = $row['reste'] ?? '';
				$row['placement'] = $row['placement'] ?? '';
				$row['refdocs']   = $row['refdocs'] ?? '';
				$cachet = 0;
				if(preg_match("#Impression des documents avec cachet et signature#",$_SESSION['easybm_roles'])){
					$cachet = 1;
				}
								?>
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
											<select name="signature" data-isnotempty="" data-message="Choisissez une option !!">
												<option value="" <?php echo $row['signature']==""?"selected":"";?>></option>
												<option value="&signature=1&cachet=<?php echo $cachet;?>" <?php echo $row['signature']=="1"?"selected":"";?>>Avec</option>
												<option value="&signature=0&cachet=<?php echo $cachet;?>" <?php echo $row['signature']=="0"?"selected":"";?>>Sans</option>
											</select>
										</label>
									</div>
									<div class="lx-textfield lx-g2 lx-pb-0">
										<label>
											<span>Emplacement cachet et signature:</span>
											<select name="placement" data-isnotempty="" data-message="Choisissez une option !!">
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
										<select name="header" data-isnotempty="" data-message="Choisissez une option !!">
											<option value="" <?php echo $row['header']==""?"selected":"";?>></option>
											<option value="&header=1" <?php echo $row['header']=="1"?"selected":"";?>>Avec</option>
											<option value="&header=0" <?php echo $row['header']=="0"?"selected":"";?>>Sans</option>
										</select>
									</label>
								</div>
								<div class="lx-textfield lx-g1 lx-pb-0">
									<label>
										<span>Références et date du document:</span>
										<select name="refdocs" data-isnotempty="" data-message="Choisissez une option !!">
											<option value="" <?php echo $row['refdocs']==""?"selected":"";?>></option>
											<option value="&refdocs=right" <?php echo $row['refdocs']=="right"?"selected":"";?>>Références et date du document à droite de l’en-tête</option>
											<option value="&refdocs=left" <?php echo $row['refdocs']=="left"?"selected":"";?>>Références et date du document à gauche de l’en-tête</option>
											<option value="&refdocs=under" <?php echo $row['refdocs']=="under"?"selected":"";?>>Références et date du document sous l’en-tête</option>
										</select>
									</label>
								</div>
								<div class="lx-textfield lx-g1 lx-pb-0 bcbre" style="display:none;">
									<label>
										<span>ICE / IF:</span>
										<select name="ice" data-isnotempty="" data-message="Choisissez une option !!">
											<option value="" <?php echo $row['ice']==""?"selected":"";?>></option>
											<option value="&ice=1" <?php echo $row['ice']=="1"?"selected":"";?>>Avec ICE</option>
											<option value="&ice=2" <?php echo $row['ice']=="2"?"selected":"";?>>Avec IF</option>
											<option value="&ice=0" <?php echo $row['ice']=="0"?"selected":"";?>>Sans</option>
										</select>
									</label>
								</div>
								<div class="lx-textfield lx-g1 lx-pb-0 blbs" style="display:none;">
									<label>
										<span>Valeurs (Les montants):</span>
										<select name="valeur" data-isnotempty="" data-message="Choisissez une option !!">
											<option value="" <?php echo $row['valeur']==""?"selected":"";?>></option>
											<option value="&valeur=1" <?php echo $row['valeur']=="1"?"selected":"";?>>Avec</option>
											<option value="&valeur=0" <?php echo $row['valeur']=="0"?"selected":"";?>>Sans</option>
										</select>
									</label>
								</div>
								<div class="lx-textfield lx-g1 lx-pb-0 fc printdoc" style="display:none;">
									<fieldset class="fc printdoc" style="display:none;">
										<legend>Informations de paiement:</legend>
										<div class="lx-textfield lx-g1 lx-pb-0 fc printdoc" style="display:none;">
											<label>
												<span>Avoirs:</span>
												<select name="avoirs" data-isnotempty="" data-message="Choisissez une option !!">
													<option value="" <?php echo $row['avoirs']==""?"selected":"";?>></option>
													<option value="&avoirs=1" <?php echo $row['avoirs']=="1"?"selected":"";?>>Avec</option>
													<option value="&avoirs=0" <?php echo $row['avoirs']=="0"?"selected":"";?>>Sans</option>
												</select>
											</label>
										</div>
										<div class="lx-textfield lx-g1 lx-pb-0 fc printdoc" style="display:none;">
											<label>
												<span>Montant payé:</span>
												<select name="payments" data-isnotempty="" data-message="Choisissez une option !!">
													<option value="" <?php echo $row['payments']==""?"selected":"";?>></option>
													<option value="&payments=1" <?php echo $row['payments']=="1"?"selected":"";?>>Avec</option>
													<option value="&payments=0" <?php echo $row['payments']=="0"?"selected":"";?>>Sans</option>
												</select>
											</label>
										</div>
										<div class="lx-textfield lx-g1 fc printdoc" style="display:none;">
											<label>
												<span>Reste:</span>
												<select name="reste" data-isnotempty="" data-message="Choisissez une option !!">
													<option value="" <?php echo $row['reste']==""?"selected":"";?>></option>
													<option value="&reste=1" <?php echo $row['reste']=="1"?"selected":"";?>>Avec</option>
													<option value="&reste=0" <?php echo $row['reste']=="0"?"selected":"";?>>Sans</option>
												</select>
											</label>
										</div>
									</fieldset>
								</div>
								<div class="lx-textfield lx-g1 lx-pb-0">
									<div class="lx-table lx-table-roles">
										<span>Les colonnes à afficher:</span>
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td><label><input type="checkbox" name="columns" value="Designation" <?php echo $row['d1']=="1"?"checked":"";?> /> Designation<del class="checkmark"></del></label></td>
												<td><label><input type="checkbox" name="columns" value="Qté" <?php echo $row['d2']=="1"?"checked":"";?> /> Qté<del class="checkmark"></del></label></td>
												<td><label><input type="checkbox" name="columns" value="P.U. HT" <?php echo $row['d3']=="1"?"checked":"";?> /> P.U. HT<del class="checkmark"></del></label></td>
												<td><label><input type="checkbox" name="columns" value="Remise HT" <?php echo $row['d4']=="1"?"checked":"";?> /> Remise HT<del class="checkmark"></del></label></td>
												<td><label><input type="checkbox" name="columns" value="TVA" <?php echo $row['d5']=="1"?"checked":"";?> /> TVA<del class="checkmark"></del></label></td>
												<td><label><input type="checkbox" name="columns" value="Total HT" <?php echo $row['d6']=="1"?"checked":"";?> /> Total HT<del class="checkmark"></del></label></td>
											</tr>
										</table>
										<label><input type="hidden" name="columnstxt" value="&d1=<?php echo $row['d1'];?>&d2=<?php echo $row['d2'];?>&d3=<?php echo $row['d3'];?>&d4=<?php echo $row['d4'];?>&d5=<?php echo $row['d5'];?>&d6=<?php echo $row['d6'];?>" /></label>
									</div>
								</div>	
								<input type="hidden" name="document" value="<?php echo $row1['type'];?>" />
								<input type="hidden" name="company" value="<?php echo $row1['company'];?>" />															
								<?php
			}
			
			if($_POST['action'] == "editsettings"){
				if($_SESSION['easybm_type'] == "moderator"){
					$back = $bdd->query("SELECT logo,cover FROM settings");
					$row = $back->fetch();
					if($row['logo'] != $_POST['logo'] AND $row['logo'] != "avatar.png"){
						if(file_exists("uploads/".$row['lofloadgo'])){
							unlink("uploads/".$row['logo']);
						}
						if(file_exists("uploads/micro_".$row['logo'])){
							unlink("uploads/micro_".$row['logo']);
						}
						if(file_exists("uploads/cropped_".$row['logo'])){
							unlink("uploads/cropped_".$row['logo']);
						}
						if(file_exists("uploads/small_".$row['logo'])){
							unlink("uploads/small_".$row['logo']);
						}
						if(file_exists("uploads/large_".$row['logo'])){
							unlink("uploads/large_".$row['logo']);
						}
					}
					if($row['cover'] != $_POST['cover'] AND $row['cover'] != "avatar.png"){
						if(file_exists("uploads/".$row['cover'])){
							unlink("uploads/".$row['cover']);
						}
						if(file_exists("uploads/micro_".$row['cover'])){
							unlink("uploads/micro_".$row['cover']);
						}
						if(file_exists("uploads/cropped_".$row['cover'])){
							unlink("uploads/cropped_".$row['cover']);
						}
						if(file_exists("uploads/small_".$row['cover'])){
							unlink("uploads/small_".$row['cover']);
						}
						if(file_exists("uploads/large_".$row['cover'])){
							unlink("uploads/large_".$row['cover']);
						}
					}
					$back = $bdd->query("SELECT id FROM settings");
					if($back->rowCount() == 0){
						$req = $bdd->prepare("INSERT INTO settings(id,logo,cover,store,defaultstate,currency) VALUE('','".$_POST['logo']."','".$_POST['cover']."','".$_POST['store']."','".$_POST['defaultstate']."','".$_POST['currency']."')");
					}
					else{
						$req = $bdd->prepare("UPDATE settings SET logo='".$_POST['logo']."',cover='".$_POST['cover']."',store='".$_POST['store']."',defaultstate='".$_POST['defaultstate']."',currency='".$_POST['currency']."'");
					}
					$req->execute();
				}
				$back = $bdd->query("SELECT id FROM parametres WHERE user='".$_SESSION['easybm_id']."'");
				if($back->rowCount() == 0){
					$req = $bdd->prepare("INSERT INTO parametres(id,user,nbrows,rowcolor) VALUE('','".$_SESSION['easybm_id']."','".$_POST['nbrows']."','".$_POST['rowcolor']."')");
				}
				else{
					$req = $bdd->prepare("UPDATE parametres SET nbrows='".$_POST['nbrows']."',rowcolor='".$_POST['rowcolor']."' WHERE user='".$_SESSION['easybm_id']."'");
				}
				$req->execute();				
			}

			if($_POST['action'] == "editnotifications"){
				$req = $bdd->prepare("UPDATE notifications SET fbefore='".$_POST['fbefore']."',
																fafter='".$_POST['fafter']."',
																fsbefore='".$_POST['fsbefore']."',
																fsafter='".$_POST['fsafter']."',
																avoir='".$_POST['avoir']."',
																unpaid='".$_POST['unpaid']."',
																recnoenc='".$_POST['recnoenc']."',
																emisnodec='".$_POST['emisnodec']."',
																unremis='".$_POST['unremis']."',
																inwaiting='".$_POST['inwaiting']."',
																outwaiting='".$_POST['outwaiting']."',
																caissein='".$_POST['caissein']."',
																caisseout='".$_POST['caisseout']."',
																remis='".$_POST['remis']."',
																incach='".$_POST['incach']."',
																outcach='".$_POST['outcach']."',
																rcaissein='".$_POST['rcaissein']."',
																rcaisseout='".$_POST['rcaisseout']."',
																rasclientbefore='".$_POST['rasclientbefore']."',
																rasclientafter='".$_POST['rasclientafter']."',
																rassupplierbefore='".$_POST['rassupplierbefore']."',
																rassupplierafter='".$_POST['rassupplierafter']."',
																rremis='".$_POST['rremis']."'");
				$req->execute();				
			}

			if($_POST['action'] == "editinstall"){
				if($_SESSION['easybm_type'] == "moderator"){
					$apikey = simpleEncrypt((float)$_POST['apikey']);
					$req = $bdd->prepare("UPDATE settings SET apikey='".$apikey."'");
					$req->execute();
				}
			}
			
			if($_POST['action'] == "lc"){
				$back = $bdd->query("SELECT id FROM companies");
				if($back->rowCount() >= (float)(simpleDecrypt($settings['apikey']))){
				?>
				<div class="lx-notices-item lx-lc">
					<i class="fa fa-exclamation-triangle"></i>
					<p>Le nombre maximal de sociétés autorisées par votre licence est atteint. Pour augmenter cette limite, contactez notre service client par téléphone ou WhatsApp: <a href="https://wa.me/212601810237" style="color:#39add1;" target="_blank">0601810237</a></p>
				</div>
				<?php
				}
			}			

			if($_POST['action'] == "adduser"){
				if($_POST['companies'] == ""){
					$_POST['companies'] = ",0";
				}
				if($_POST['id'] == "0"){
					$back = $bdd->query("SELECT id FROM users WHERE email='".$_POST['email']."' AND trash='1'");
					if($back->rowCount() == 0){
						$req = $bdd->prepare("INSERT INTO users(id,fullname,picture,email,password,phone,job,type,roles,companies,datesignup,trash) 
						VALUES ('0','".sanitize_vars($_POST['fullname'])."','avatar.png','".sanitize_vars($_POST['email'])."','".sanitize_vars($_POST['password'])."','".sanitize_vars($_POST['phone'])."','".sanitize_vars($_POST['job'])."','moderator','".addslashes(substr($_POST['roles'],1))."','".sanitize_vars(substr($_POST['companies'],1))."','".time()."','1')");
						$req->execute();
					}
					else{
						echo "Login existe déjà !";
					}
				}
				else{
					$req = $bdd->prepare("UPDATE users SET fullname='".sanitize_vars($_POST['fullname'])."',password='".sanitize_vars($_POST['password'])."',phone='".sanitize_vars($_POST['phone'])."',job='".sanitize_vars($_POST['job'])."',roles='".addslashes(substr($_POST['roles'],1))."',companies='".sanitize_vars(substr($_POST['companies'],1))."' WHERE id='".$_POST['id']."'");
					$req->execute();
				}
			}

			if($_POST['action'] == "deleteuser"){
				$req = $bdd->prepare("UPDATE users SET trash='0' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "restoreuser"){
				$req = $bdd->prepare("UPDATE users SET trash='1' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "deleteuserpermanently"){
				$req = $bdd->prepare("DELETE FROM users WHERE id='".$_POST['id']."'");
				$req->execute();
			}

			if($_POST['action'] == "resetpassword"){
				$req = $bdd->prepare("UPDATE users SET password=email WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "addemails"){
				$back = $bdd->query("SELECT id FROM usersemails WHERE company='".$_POST['company']."' AND user='".$_POST['user']."' AND id NOT IN(".$_POST['id'].")");
				if($back->rowCount() == 0){
					if($_POST['id'] == "0"){
						$req = $bdd->prepare("INSERT INTO usersemails(id,company,user,email,password,host,smtpsecure,port,trash) 
						VALUES ('0','".str_replace("||",",",$_POST['company'])."','".sanitize_vars($_POST['user'])."','".sanitize_vars($_POST['email'])."',
						'".sanitize_vars($_POST['password'])."','".sanitize_vars($_POST['host'])."','".sanitize_vars($_POST['smtpsecure'])."',
						'".sanitize_vars($_POST['port'])."','1')");
						$req->execute();
					}
					else{
						$req = $bdd->prepare("UPDATE usersemails SET email='".sanitize_vars($_POST['email'])."',
																	password='".sanitize_vars($_POST['password'])."',
																	host='".sanitize_vars($_POST['host'])."',
																	smtpsecure='".sanitize_vars($_POST['smtpsecure'])."',
																	port='".sanitize_vars($_POST['port'])."'
																	WHERE id='".$_POST['id']."'");
						$req->execute();
					}
				}
				else{
					echo "Société existe déjà !";
				}
			}
						
			if($_POST['action'] == "loadusers"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Nom et prénom <i class="fa fa-sort" data-sort="fullname"></i></td>
						<td>Fonction <i class="fa fa-sort" data-sort="job"></i></td>
						<td>Téléphone <i class="fa fa-sort" data-sort="phone"></i></td>
						<td>Login <i class="fa fa-sort" data-sort="email"></i></td>
						<td>Permissions</td>
						<td>Sociétés à gérer / E-mail</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM users WHERE trash='".$_POST['state']."'";
					if($_POST['keyword'] != ""){
						$req .= " AND (fullname LIKE '".normalizeString($_POST['keyword'])."' OR phone LIKE '".normalizeString($_POST['keyword'])."' OR email LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['sortby'] != ""){
						$req .= " ORDER BY ".$_POST['sortby'];
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					while($row = $back->fetch()){
						?>
					<tr>
						<td><label><input type="checkbox" name="user" value="<?php echo $row['id'];?>" /><del class="checkmark"></del></label></td>
						<td>
							<?php
							if($row['superadmin'] == "1"){
							?>
							<span style="color:#39add1;">
								<i class="fa fa-shield-alt"></i> <?php echo $row['fullname'];?>
							</span>
							<?php
							}
							else{
							?>
							<span><?php echo $row['fullname'];?></span>
							<?php								
							}
							?>
						</td>
						<td><span><?php echo $row['job'];?></span></td>
						<td><span><?php echo $row['phone'];?></span></td>
						<td><span><?php echo $row['email'];?></span></td>
						<td>
							<div class="lx-upload-files">
								<a href="javascript:;" class="lx-show-roles"><i class="fa fa-plus"></i> Afficher plus</a>
							</div>
							<div class="lx-table lx-table-user-droit">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>Page/Permission</td>
										<td>Consulter</td>
										<td>Ajouter</td>
										<td>Modifier</td>
										<td>Supprimer</td>
										<td>Exporter</td>
										<td>CA, Paiement ...</td>
										<td>Consultation de la journée en cours seulement</td>
									</tr>
									<tr>
										<td style="width:auto;white-space:nowrap;">Tableau de bord</td>
										<td><i class="fa <?php echo preg_match("#Consulter Tableau de bord#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#CA Tableau de bord#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Consultation de la journée en cours seulement Tableau de bord#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
									</tr>
									<tr>
										<td style="width:auto;white-space:nowrap;">Trésorerie</td>
										<td><i class="fa <?php echo preg_match("#Consulter Trésorerie#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Modifier Trésorerie#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Exporter Trésorerie#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Consultation de la journée en cours seulement Trésorerie#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
									</tr>
									<tr>
										<td>Factures clients</td>
										<td><i class="fa <?php echo preg_match("#Consulter Factures clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Factures clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Factures clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Factures clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Factures clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Consultation de la journée en cours seulement Factures clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
									</tr>
									<tr>
										<td style="width:auto;white-space:nowrap;">Modèles de messages factures clients</td>
										<td><i class="fa <?php echo preg_match("#Consulter Modèles de messages factures clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Modèles de messages factures clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Modèles de messages factures clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Modèles de messages factures clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Modèles de messages factures clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td>Notes d'honoraires</td>
										<td><i class="fa <?php echo preg_match("#Consulter Notes honoraires#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Notes honoraires#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Notes honoraires#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Notes honoraires#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Notes honoraires#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Consultation de la journée en cours seulement Notes honoraires#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
									</tr>
									<tr>
										<td>Devis</td>
										<td><i class="fa <?php echo preg_match("#Consulter Devis,#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Devis,#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Devis,#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Devis,#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Devis,#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Consultation de la journée en cours seulement Devis,#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
									</tr>
									<tr>
										<td>Propositions d'honoraires</td>
										<td><i class="fa <?php echo preg_match("#Consulter Propositions honoraires#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Propositions honoraires#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Propositions honoraires#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Propositions honoraires#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Propositions honoraires#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Consultation de la journée en cours seulement Propositions honoraires#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
									</tr>
									<tr>
										<td>Factures proforma</td>
										<td><i class="fa <?php echo preg_match("#Consulter Factures proforma#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Factures proforma#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Factures proforma#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Factures proforma#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Factures proforma#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Consultation de la journée en cours seulement Factures proforma#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
									</tr>
									<tr>
										<td>Bons de livraison</td>
										<td><i class="fa <?php echo preg_match("#Consulter Bons de livraison#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Bons de livraison#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Bons de livraison#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Bons de livraison#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Bons de livraison#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Consultation de la journée en cours seulement Bons de livraison#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
									</tr>
									<tr>
										<td>Bons de sortie</td>
										<td><i class="fa <?php echo preg_match("#Consulter Bons de sortie#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Bons de sortie#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Bons de sortie#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Bons de sortie#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Bons de sortie#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Consultation de la journée en cours seulement Bons de sortie#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
									</tr>
									<tr>
										<td>Bons de retour</td>
										<td><i class="fa <?php echo preg_match("#Consulter Bons de retour#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Bons de retour#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Bons de retour#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Bons de retour#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Bons de retour#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Consultation de la journée en cours seulement Bons de retour#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
									</tr>
									<tr>
										<td style="width:auto;white-space:nowrap;">Tags documents</td>
										<td><i class="fa <?php echo preg_match("#Consulter Tags documents#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Tags documents#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Tags documents#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Tags documents#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td>Clients</td>
										<td><i class="fa <?php echo preg_match("#Consulter Clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#CA Clients#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
									</tr>
									<tr>
										<td>Bons de commande</td>
										<td><i class="fa <?php echo preg_match("#Consulter Bons de commande#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Bons de commande#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Bons de commande#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Bons de commande#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Bons de commande#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td>Bons de réception</td>
										<td><i class="fa <?php echo preg_match("#Consulter Bons de réception#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Bons de réception#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Bons de réception#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Bons de réception#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Bons de réception#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td>Factures fournisseurs</td>
										<td><i class="fa <?php echo preg_match("#Consulter Factures fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Factures fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Factures fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Factures fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Factures fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Consultation de la journée en cours seulement Factures fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
									</tr>
									<tr>
										<td style="width:auto;white-space:nowrap;">Modèles de messages factures fournisseurs</td>
										<td><i class="fa <?php echo preg_match("#Consulter Modèles de messages factures fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Modèles de messages factures fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Modèles de messages factures fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Modèles de messages factures fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Modèles de messages factures fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td>Fournisseurs</td>
										<td><i class="fa <?php echo preg_match("#Consulter Fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#CA Fournisseurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
									</tr>
									<tr>
										<td style="width:auto;white-space:nowrap;">Prospects</td>
										<td><i class="fa <?php echo preg_match("#Consulter Prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td style="width:auto;white-space:nowrap;">Tags prospects</td>
										<td><i class="fa <?php echo preg_match("#Consulter Tags prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Tags prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Tags prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Tags prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td style="width:auto;white-space:nowrap;">Modèles de messages prospects</td>
										<td><i class="fa <?php echo preg_match("#Consulter Modèles de messages prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Modèles de messages prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Modèles de messages prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Modèles de messages prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Modèles de messages prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td style="width:auto;white-space:nowrap;">Modèles de messages d'envoi de documents</td>
										<td><i class="fa <?php echo preg_match("#Consulter Modèles de messages d'envoi de documents#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Modèles de messages d'envoi de documents#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Modèles de messages d'envoi de documents#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Modèles de messages d'envoi de documents#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Modèles de messages d'envoi de documents#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td>Sociétés </td>
										<td><i class="fa <?php echo preg_match("#Consulter Sociétés#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Sociétés#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Sociétés#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Sociétés#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Exporter Sociétés#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#CA Sociétés#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
									</tr>
									<tr>
										<td>Utilisateurs</td>
										<td><i class="fa <?php echo preg_match("#Consulter Utilisateurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Utilisateurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Modifier Utilisateurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Utilisateurs#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td>TVA</td>
										<td><i class="fa <?php echo preg_match("#Consulter TVA#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter TVA#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Supprimer TVA#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td>Devises</td>
										<td><i class="fa <?php echo preg_match("#Consulter Devises#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td><i class="fa <?php echo preg_match("#Ajouter Devises#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td><i class="fa <?php echo preg_match("#Supprimer Devises#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td>Formation</td>
										<td><i class="fa <?php echo preg_match("#Consulter Formation#",$row['roles'])?"fa-check":"fa-times";?>"></i></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>Notifications</td>
									</tr>
									<tr>
										<td>
											<label><i class="fa <?php echo preg_match("#Consultation des notifications#",$row['roles'])?"fa-check":"fa-times";?>"></i> Consultation des notifications</label>
											<label><i class="fa <?php echo preg_match("#Réglage des notifications#",$row['roles'])?"fa-check":"fa-times";?>"></i> Réglage des notifications</label>
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>Modification date opération</td>
									</tr>
									<tr>
										<td>
											<label><i class="fa <?php echo preg_match("#Modification date opération#",$row['roles'])?"fa-check":"fa-times";?>"></i> <?php echo preg_match("#Modification date opération#",$row['roles'])?"Oui":"Non";?></label>
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>Transformation / Dupplication documents</td>
									</tr>
									<tr>
										<td>
											<label><i class="fa <?php echo preg_match("#Transformation / Dupplication documents#",$row['roles'])?"fa-check":"fa-times";?>"></i> <?php echo preg_match("#Transformation / Dupplication documents#",$row['roles'])?"Oui":"Non";?></label>
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>Transformation groupée des documents</td>
									</tr>
									<tr>
										<td>
											<label><i class="fa <?php echo preg_match("#Transformation groupée des documents#",$row['roles'])?"fa-check":"fa-times";?>"></i> <?php echo preg_match("#Transformation groupée des documents#",$row['roles'])?"Oui":"Non";?></label>
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>Modification statut de paiement</td>
									</tr>
									<tr>
										<td>
											<label><i class="fa <?php echo preg_match("#Modification statut de paiement#",$row['roles'])?"fa-check":"fa-times";?>"></i> <?php echo preg_match("#Modification statut de paiement#",$row['roles'])?"Oui":"Non";?></label>
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>Suppression d'historique de paiement</td>
									</tr>
									<tr>
										<td>
											<label><i class="fa <?php echo preg_match("#Suppression historique de paiement#",$row['roles'])?"fa-check":"fa-times";?>"></i> <?php echo preg_match("#Suppression historique de paiement#",$row['roles'])?"Oui":"Non";?></label>
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>Délai de paiement et régularisation de retard</td>
									</tr>
									<tr>
										<td>
											<label><i class="fa <?php echo preg_match("#Modifier date de paiement#",$row['roles'])?"fa-check":"fa-times";?>"></i> Modifier date de paiement</label>&nbsp;
											<label><i class="fa <?php echo preg_match("#Modifier délai de paiement#",$row['roles'])?"fa-check":"fa-times";?>"></i> Modifier délai de paiement</label>&nbsp;
											<label><i class="fa <?php echo preg_match("#Régulariser le retard de paiement#",$row['roles'])?"fa-check":"fa-times";?>"></i> Régulariser le retard de paiement</label>&nbsp;
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>Consulter seulement ses prospects</td>
									</tr>
									<tr>
										<td>
											<label><i class="fa <?php echo preg_match("#Consulter seulement ses prospects#",$row['roles'])?"fa-check":"fa-times";?>"></i> <?php echo preg_match("#Consulter seulement ses prospects#",$row['roles'])?"Oui":"Non";?></label>
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>Télécharger Backup</td>
									</tr>
									<tr>
										<td>
											<label><i class="fa <?php echo preg_match("#Télécharger Backup#",$row['roles'])?"fa-check":"fa-times";?>"></i> <?php echo preg_match("#Télécharger Backup#",$row['roles'])?"Oui":"Non";?></label>
										</td>
									</tr>
								</table>								
							</div>
						</td>
						<td>
							<?php
							$companiesids = array_unique(explode(",",$row['companies']));
							for($i=0;$i<count($companiesids);$i++){
								$back1 = $bdd->query("SELECT rs FROM companies WHERE id='".$companiesids[$i]."'");
								if($back1->rowCount() > 0){
								$row1 = $back1->fetch();
								$company = $row1['rs'];
							?>
							<div class="lx-back-acocunt" style="white-space:nowrap;margin-bottom:10px;padding:10px;background:#fcfdec;border:1px solid #242424;border-radius:6px;">
								<span>Société: <b><?php echo $row1['rs'];?></b></span>
								<?php
								$back1 = $bdd->query("SELECT * FROM usersemails WHERE company='".$companiesids[$i]."' AND user='".$row['id']."'");
								if($back1->rowCount() > 0){
									$row1 = $back1->fetch();
									?>
								<span>E-mail: <b><?php echo $row1['email'];?></b></span>
								<span>Password: <b>****************</b></span>
								<span>Host: <b><?php echo $row1['host'];?></b></span>
								<span>SMTPSecure: <b><?php echo $row1['smtpsecure'];?></b></span>
								<span>Port: <b><?php echo $row1['port'];?></b></span>
									<?php
								}
								?>
								<p style="text-align:right;padding:0px;margin:0px;">
									<a href="javascript:;" class="lx-edit-email-company lx-open-popup" data-header="Modifier"
									data-id="<?php echo isset($row1['id'])?$row1['id']:0;?>"
									data-user="<?php echo $row['id'];?>"
									data-company="<?php echo $companiesids[$i];?>"
									data-companytxt="<?php echo $company;?>"
									data-email="<?php echo isset($row1['email'])?$row1['email']:'';?>"
									data-password="<?php echo isset($row1['password'])?$row1['password']:'';;?>"
									data-host="<?php echo isset($row1['host'])?$row1['host']:'';;?>"
									data-smtpsecure="<?php echo isset($row1['smtpsecure'])?$row1['smtpsecure']:'';;?>"
									data-port="<?php echo isset($row1['port'])?$row1['port']:'';;?>"
									data-title="emails" style="display:inline-block;margin-left:10px;color:#39add1;font-weight:bold;"><i class="fa fa-envelope"></i> Configuration d'E-mail</a>
								</p>
							</div>
							<?php
								}
							}
							?>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<?php
								if(preg_match("#Modifier Utilisateurs#",$_SESSION['easybm_roles']) OR $_SESSION['easybm_superadmin'] == "1"){
									?>
							<a href="javascript:;" class="lx-edit lx-edit-user lx-open-popup" data-header="Modifier un"
								data-id="<?php echo $row['id'];?>"
								data-fullname="<?php echo $row['fullname'];?>"
								data-email="<?php echo $row['email'];?>"
								data-password="<?php echo $row['password'];?>"
								data-phone="<?php echo $row['phone'];?>"
								data-job="<?php echo $row['job'];?>"
								data-roles=",<?php echo $row['roles'];?>"
								data-companies=",<?php echo $row['companies'];?>" data-title="user">Modifier</a>
							<a href="javascript:;" class="lx-edit lx-reset-user lx-open-popup" data-id="<?php echo $row['id'];?>" data-title="resetpassword">Réinitialiser mot de passe</a>
								<?php
								}
								if(preg_match("#Supprimer Utilisateurs#",$_SESSION['easybm_roles']) AND $row['superadmin'] != "1"){
								?>
							<a href="javascript:;" class="lx-delete lx-delete-user lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Nom et prénom <i class="fa fa-sort" data-sort="fullname"></i></td>
						<td>Téléphone <i class="fa fa-sort" data-sort="job"></i></td>
						<td>Fonction <i class="fa fa-sort" data-sort="phone"></i></td>
						<td>Login <i class="fa fa-sort" data-sort="email"></i></td>
						<td>Permissions</td>
						<td>Sociétés</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> utilisateur(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> utilisateur(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}
			
			if($_POST['action'] == "editaccount"){
				$back = $bdd->query("SELECT picture FROM users WHERE id='".$_SESSION['easybm_id']."'");
				$row = $back->fetch();
				if($row['picture'] != $_POST['picture'] AND $row['picture'] != "avatar.png"){
					if(file_exists("uploads/".$row['picture'])){
						unlink("uploads/".$row['picture']);
					}
					if(file_exists("uploads/micro_".$row['picture'])){
						unlink("uploads/micro_".$row['picture']);
					}
					if(file_exists("uploads/cropped_".$row['picture'])){
						unlink("uploads/cropped_".$row['picture']);
					}
					if(file_exists("uploads/small_".$row['picture'])){
						unlink("uploads/small_".$row['picture']);
					}
					if(file_exists("uploads/large_".$row['picture'])){
						unlink("uploads/large_".$row['picture']);
					}
				}
				$req = $bdd->prepare("UPDATE users SET fullname='".sanitize_vars($_POST['fullname'])."',picture='".sanitize_vars($_POST['picture'])."',phone='".sanitize_vars($_POST['phone'])."' WHERE id='".$_SESSION['easybm_id']."'");
				$req->execute();
			}

			if($_POST['action'] == "editpassword"){
				if($_POST['oldpassword'] == "" OR $_POST['newpassword1'] == "" OR $_POST['newpassword2'] == ""){
					echo '2';
				}
				else{
					$back = $bdd->query("SELECT * FROM users WHERE id='".$_SESSION['easybm_id']."' AND password='".$_POST['oldpassword']."'");
					if($back->rowCount() == 0){
						echo '3';
					}
					elseif($_POST['newpassword1'] != $_POST['newpassword2']){
						echo '4';
					}
					else{
						$req = $bdd->prepare("UPDATE users SET password='".sanitize_vars($_POST['newpassword1'])."' WHERE id='".$_SESSION['easybm_id']."'");
						$req->execute();
						echo '1';
					}
				}
			}
			
			if($_POST['action'] == "addprospect"){
					if($_POST['id'] == "0"){
						$req = $bdd->prepare("INSERT INTO prospects(id,fullname,phone,whatsapp,email,ice,address,gender,company1,source,labels,note,company,user,dateadd,dateupdate,trash) 
						VALUES ('0','".sanitize_vars($_POST['title'])."','".sanitize_vars($_POST['phone'])."','".sanitize_vars($_POST['whatsapp'])."',
						'".sanitize_vars($_POST['email'])."','".sanitize_vars($_POST['ice'])."','".sanitize_vars($_POST['address'])."',
						'".sanitize_vars($_POST['gender'])."','".sanitize_vars($_POST['company1'])."','".sanitize_vars($_POST['source'])."',
						'".sanitize_vars($_POST['labels'])."','','".str_replace("||",",",$_POST['company'])."','".$_SESSION['easybm_id']."','".time()."','".time()."','1')");
						$req->execute();
					}
					else{
						$req = $bdd->prepare("UPDATE prospects SET fullname='".sanitize_vars($_POST['title'])."',
																	phone='".sanitize_vars($_POST['phone'])."',
																	whatsapp='".sanitize_vars($_POST['whatsapp'])."',
																	email='".sanitize_vars($_POST['email'])."',
																	gender='".sanitize_vars($_POST['gender'])."',
																	company1='".sanitize_vars($_POST['company1'])."',
																	source='".sanitize_vars($_POST['source'])."',
																	company='".str_replace("||",",",$_POST['company'])."',
																	ice='".sanitize_vars($_POST['ice'])."',
																	address='".sanitize_vars($_POST['address'])."',
																	labels='".sanitize_vars($_POST['labels'])."',
																	dateupdate='".time()."'
																	WHERE id='".$_POST['id']."'");
						$req->execute();
					}
			}

			if($_POST['action'] == "deleteprospect"){
				$back = $bdd->query("SELECT id,attachments FROM documents WHERE prospect='".$_POST['id']."'");
				if($back->rowCount() == 0){
					$req = $bdd->prepare("DELETE FROM prospects WHERE id='".$_POST['id']."'");
					$req->execute();
				}
				else{
					echo "Nous avons detecté des documents (devis, ...) liés à ce prospect, vous ne pouvez pas le supprimer !";
				}
			}
			
			if($_POST['action'] == "restoreprospect"){
				$req = $bdd->prepare("UPDATE prospects SET trash='1' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "deleteprospectpermanently"){
				$req = $bdd->prepare("DELETE FROM prospects WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "addprospectnote"){
				$req = $bdd->prepare("UPDATE prospects SET note='".addslashes($_POST['note'])."' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "addrecallnote"){
				$recalldate = "";
				if($_POST['recalldate'] != "" AND preg_match("#^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/\d{4}$#",$_POST['recalldate'])){
					$recalldate = getDateNow($_POST['recalldate']);
				}
				$req = $bdd->prepare("UPDATE `".$_POST['table']."` SET recallnote='".addslashes($_POST['recallnote'])."',recalldate='".$recalldate."' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "loadprospectsrecalls"){
				if($_POST['table'] == "prospects"){
					if(preg_match("#Consulter seulement ses prospects#",$_SESSION['easybm_roles'])){
						$multicompanies .= " AND user='".$_SESSION['easybm_id']."'";
					}
				}
					
				if($_POST['table'] == "documents"){
					$back = $bdd->query("SELECT id FROM `".$_POST['table']."` WHERE type='".$_POST['type']."' AND (recalldate < ".(strtotime(date("d-m-Y"))+(60*60*24))." AND recalldate<>'')".$multicompanies);
				}
				else{
					$back = $bdd->query("SELECT id FROM `".$_POST['table']."` WHERE (recalldate < ".(strtotime(date("d-m-Y"))+(60*60*24))." AND recalldate<>'')".$multicompanies);
				}
				if($back->rowCount() > 0){
					?>
				<a href="javascript:;" class="lx-reported-blink blink" style="position:relative;left:10px;" data-datestartrecall="<?php echo date("d/m/Y",(time()-(60*60*24*180)));?>" data-dateendrecall="<?php echo date("d/m/Y");?>"><?php echo $back->rowCount();?> Rappel(s)</a>
					<?php
				}
			}
			
			if($_POST['action'] == "transformprospect"){
				$rand = "CL".date("ym")."-0001";
				$back1 = $bdd->query("SELECT code FROM clients ORDER BY id DESC LIMIT 0,1");
				if($back1->rowCount() > 0){
					$row1 = $back1->fetch();
					$rand = "CL".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
				}
				$req = $bdd->prepare("INSERT INTO clients(code,fullname,phone,email,note,company,user,dateadd,trash) SELECT '".$rand."',fullname,phone,email,labels,company,user,'".time()."','1' FROM prospects WHERE id='".$_POST['id']."'");
				$req->execute();
				
				$back = $bdd->query("SELECT id FROM clients WHERE code='".$rand."' ORDER BY id DESC LIMIT 0,1");
				$row = $back->fetch();

				$req = $bdd->prepare("UPDATE documents SET prospect='0',client='".$row['id']."' WHERE prospect='".$_POST['id']."'");
				$req->execute();
				
				$req = $bdd->prepare("UPDATE prospects SET note=CONCAT(note,'\nTransformé en client') WHERE id='".$_POST['id']."' AND note NOT LIKE '%Transformé en client%'");
				$req->execute();
			}
			
			if($_POST['action'] == "loadprospects"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Nom et prénom</td>
						<td>Société prospet</td>
						<td>ICE</td>
						<td>Adresse</td>
						<td>Contact</td>
						<td>Source <i class="fa fa-sort" data-sort="source"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:200px;">Note</td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Affecté à la société</td>
						<td>Date ajout <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Date mise à jour <i class="fa fa-sort" data-sort="dateupdate"></i></td>
						<td>Utilisateur</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM prospects p WHERE trash='".$_POST['state']."'".$multicompanies;
					if(preg_match("#Consulter seulement ses prospects#",$_SESSION['easybm_roles'])){
						$req .= " AND user='".$_SESSION['easybm_id']."'";
					}
					if($_POST['keyword'] != ""){
						$req .= " AND (fullname LIKE '".normalizeString($_POST['keyword'])."' 
										OR phone LIKE '".normalizeString($_POST['keyword'])."'
										OR whatsapp LIKE '".normalizeString($_POST['keyword'])."'
										OR email LIKE '".normalizeString($_POST['keyword'])."'
										OR gender LIKE '".normalizeString($_POST['keyword'])."'
										OR source LIKE '".normalizeString($_POST['keyword'])."'
										OR company1 LIKE '".normalizeString($_POST['keyword'])."'
										OR labels LIKE '".normalizeString($_POST['keyword'])."'										
										OR recallnote LIKE '".normalizeString($_POST['keyword'])."'										
										OR note LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['worker'] != ""){
						$req .= " AND user IN(".$_POST['worker'].")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartupdate'] != "" AND $_POST['dateendupdate'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartupdate']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendupdate'])) + (60*60*24) - 1;
						$req .= " AND (dateupdate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						$req .= " ORDER BY ".$_POST['sortby'];
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					while($row = $back->fetch()){
						$companytxt = "";
						$back1 = $bdd->query("SELECT rs FROM companies WHERE id IN(".($row['company']!=""?$row['company']:0).")".$companiesid);
						while($company = $back1->fetch()){
							$companytxt .= ",".$company['rs'];
						}
						$companytxt = substr($companytxt,1);
						
						$back1 = $bdd->query("SELECT * FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();

						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();
						
						
						?>
					<tr>
						<td><label><input type="checkbox" name="prospect" value="<?php echo $row['id'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo $row['gender']." ".$row['fullname'];?></span></td>
						<td><span><?php echo $row['company1'];?></span></td>
						<td><span><?php echo $row['ice'];?></span></td>
						<td><span><?php echo $row['address'];?></span></td>
						<td style="white-space:nowrap;">
							<span>Téléphone: <a href="tel:<?php echo $row['phone'];?>" style="color:#39add1;"><?php echo $row['phone'];?></a></span>
							<span>WhatsApp: <a href="https://wa.me/<?php echo $row['whatsapp'];?>" style="color:#39add1;" target="_blank"><?php echo $row['whatsapp'];?></a></span>
							<span>E-mail: <a href="mailto:<?php echo $row['email'];?>" style="color:#39add1;"><?php echo $row['email'];?></a></span>
							<div style="margin-top:5px;white-space:nowrap;" class="lx-upload-files">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-phone="<?php echo $row['phone'];?>"
									data-client="<?php echo $row['fullname'];?>"
									data-email="<?php echo $row['email'];?>"
									data-email1="<?php echo $row['email'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Prospect"
									data-societe="<?php echo str_replace(","," et ",$companytxt);?>"
									data-whatsapp="<?php echo $row['whatsapp'];?>"
									data-gender="<?php echo $row['gender'];?>"
									data-button="Envoyer un message"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer un message</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="prospect"
									data-button="Envoyer un message" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>	
						</td>
						<td><span><?php echo $row['source'];?></span></td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM labels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="prospects"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td><span><?php echo nl2br($row['note']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-prospect-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-note="<?php echo addslashes($row['note']);?>" data-title="prospectnote">Modifier</a>
							</div>
						</td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td style="white-space:nowrap;">
							<?php
							echo '<span>'.str_replace(',','</span><span>',$companytxt).'</span>'
							?>
						</td>
						<td><span><?php echo date("d/m/Y",$row['dateadd']);?></span></td>
						<td><span><?php echo $row['dateupdate']!=""?date("d/m/Y",$row['dateupdate']):"";?></span></td>
						<td style="white-space:nowrap;">
							<span><?php echo isset($user['fullname'])?$user['fullname']:"";?></span>
						</td>						
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<?php
								if(preg_match("#Modifier Prospects#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit lx-edit-prospect lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-titl="<?php echo $row['fullname'];?>"
								data-phone="<?php echo $row['phone'];?>"
								data-whatsapp="<?php echo $row['whatsapp'];?>"
								data-email="<?php echo $row['email'];?>"
								data-gender="<?php echo $row['gender'];?>"
								data-source="<?php echo $row['source'];?>"
								data-company1="<?php echo $row['company1'];?>"
								data-ice="<?php echo $row['ice'];?>"
								data-address="<?php echo $row['address'];?>"
								data-company="<?php echo $row['company'];?>"
								data-companytxt="<?php echo $companytxt;?>"
								data-labels="<?php echo $row['labels'];?>"
								data-title="prospect">Modifier</a>
								<a href="javascript:;" class="lx-edit lx-transform-prospect lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"							
								data-title="prospecttoclient">Transformer en client</a>
								<?php
								}
								if(preg_match("#Supprimer Prospects#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-prospect lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Nom et prénom</td>
						<td>Société prospet</td>
						<td>ICE</td>
						<td>Adresse</td>
						<td>Contact</td>
						<td>Source <i class="fa fa-sort" data-sort="source"></i></td>
						<td>Tags</td>
						<td><div style="width:200px;">Note</div></td>
						<td>Rappel</td>
						<td>Affecté à la société</td>
						<td>Date ajout <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Date mise à jour <i class="fa fa-sort" data-sort="dateupdate"></i></td>
						<td>Utilisateur</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> prospect(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> prospect(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}
			
			if($_POST['action'] == "addlabel"){
				$back = $bdd->query("SELECT id FROM labels WHERE label='".sanitize_vars($_POST['label'])."' AND id NOT IN(".$_POST['id'].")".$multicompanies);
				if($back->rowCount() == 0){
					if($_POST['id'] == "0"){
						$req = $bdd->prepare("INSERT INTO labels(id,label,color,company,trash) 
						VALUES ('0','".sanitize_vars($_POST['label'])."','".sanitize_vars($_POST['color'])."','".str_replace("||",",",$_POST['company'])."','1')");
						$req->execute();
					}
					else{
						$back = $bdd->query("SELECT label FROM labels WHERE id IN(".$_POST['id'].")");
						$row1 = $back->fetch();
						
						$req = $bdd->prepare("UPDATE labels SET label='".sanitize_vars($_POST['label'])."',
																color='".sanitize_vars($_POST['color'])."',
																company='".str_replace("||",",",$_POST['company'])."'
																WHERE id='".$_POST['id']."'");
						$req->execute();
						
						$back = $bdd->query("SELECT id,labels FROM prospects WHERE FIND_IN_SET('".$row1['label']."',labels)");
						while($row = $back->fetch()){
							$labels = ",".trim($row['labels'],",").",";
							$newlabels = str_replace(",".$row1['label'].",",",".sanitize_vars($_POST['label']).",",$labels);
						
							$req = $bdd->prepare("UPDATE prospects SET labels='".trim($newlabels,",")."' WHERE id='".$row['id']."'");
							$req->execute();
						}
					}
				}
				else{
					echo "Tag existe déjà";
				}
			}

			if($_POST['action'] == "deletelabel"){
				$back = $bdd->query("SELECT label FROM labels WHERE id='".$_POST['id']."'");
				$row = $back->fetch();
				$back = $bdd->query("SELECT id FROM prospects WHERE FIND_IN_SET('".$row['label']."',labels)");
				if($back->rowCount() == 0){
					$req = $bdd->prepare("DELETE FROM labels WHERE id='".$_POST['id']."'");
					$req->execute();
				}
				else{
					echo "Vous ne pouvez pas supprimer un tag affecté à des prospects";
				}
			}
			
			if($_POST['action'] == "restorelabel"){
				$req = $bdd->prepare("UPDATE labels SET trash='1' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "deletelabelpermanently"){
				$req = $bdd->prepare("DELETE FROM labels WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "loadlabels"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Tag</td>
						<td>Affecté à la société</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM labels p WHERE trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (label LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['sortby'] != ""){
						$req .= " ORDER BY ".$_POST['sortby'];
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					while($row = $back->fetch()){
						?>
					<tr>
						<td><label><input type="checkbox" name="label" value="<?php echo $row['id'];?>" /><del class="checkmark"></del></label></td>
						<td>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0, 
									calc(100% - 18px) 0, 
									100% 18px, 
									100% calc(100% - 18px), 
									calc(100% - 18px) 100%, 
									0 100%
								  );	background:<?php echo $row['color'];?>;color:#FFFFFF;"><?php echo $row['label'];?></span>
						</td>
						<td style="white-space:nowrap;">
							<?php
							$companytxt = "";
							$back1 = $bdd->query("SELECT rs FROM companies WHERE id IN(".($row['company']!=""?$row['company']:0).")".$companiesid);
							while($company = $back1->fetch()){
								$companytxt .= ",".$company['rs'];
								?>
							<span><?php echo $company['rs'];?></span>
								<?php
							}
							$companytxt = substr($companytxt,1);
							?>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<?php
								if(preg_match("#Modifier Tags prospects#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit lx-edit-label lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-label="<?php echo $row['label'];?>"
								data-color="<?php echo $row['color'];?>"	
								data-company="<?php echo str_replace(",","||",$row['company']);?>"
								data-companytxt="<?php echo $companytxt;?>"								
								data-title="label">Modifier</a>
								<?php
								}
								if(preg_match("#Supprimer Tags prospects#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-label lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Tag</td>
						<td>Affecté à la société</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> tag(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> tag(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}

			if($_POST['action'] == "adddoclabel"){
				$back = $bdd->query("SELECT id FROM doclabels WHERE label='".sanitize_vars($_POST['label'])."' AND id NOT IN(".$_POST['id'].")".$multicompanies);
				if($back->rowCount() == 0){
					if($_POST['id'] == "0"){
						$req = $bdd->prepare("INSERT INTO doclabels(id,label,color,company,type,trash) 
						VALUES ('0','".sanitize_vars($_POST['label'])."','".sanitize_vars($_POST['color'])."','".str_replace("||",",",$_POST['company'])."','".sanitize_vars($_POST['type'])."','1')");
						$req->execute();
					}
					else{
						$back = $bdd->query("SELECT label FROM doclabels WHERE id IN(".$_POST['id'].")");
						$row1 = $back->fetch();
						
						$req = $bdd->prepare("UPDATE doclabels SET label='".sanitize_vars($_POST['label'])."',
																color='".sanitize_vars($_POST['color'])."',
																type='".sanitize_vars($_POST['type'])."',
																company='".str_replace("||",",",$_POST['company'])."'
																WHERE id='".$_POST['id']."'");
						$req->execute();
						
						$back = $bdd->query("SELECT id,labels FROM documents WHERE FIND_IN_SET('".$row1['label']."',labels)");
						while($row = $back->fetch()){
							$labels = ",".trim($row['labels'],",").",";
							$newlabels = str_replace(",".$row1['label'].",",",".sanitize_vars($_POST['label']).",",$labels);
						
							$req = $bdd->prepare("UPDATE documents SET labels='".trim($newlabels,",")."' WHERE id='".$row['id']."'");
							$req->execute();
						}
					}
				}
				else{
					echo "Tag existe déjà";
				}
			}

			if($_POST['action'] == "deletedoclabel"){
				$back = $bdd->query("SELECT label FROM doclabels WHERE id='".$_POST['id']."'");
				$row = $back->fetch();
				$back = $bdd->query("SELECT id FROM documents WHERE FIND_IN_SET('".$row['label']."',labels)");
				$back1 = $bdd->query("SELECT id FROM facturesuppliers WHERE FIND_IN_SET('".$row['label']."',labels)");
				if($back->rowCount() == 0 AND $back1->rowCount() == 0){
					$req = $bdd->prepare("DELETE FROM doclabels WHERE id='".$_POST['id']."'");
					$req->execute();
				}
				else{
					echo "Vous ne pouvez pas supprimer un tag affecté à des documents";
				}
			}
			
			if($_POST['action'] == "restoredoclabel"){
				$req = $bdd->prepare("UPDATE doclabels SET trash='1' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "deletedoclabelpermanently"){
				$req = $bdd->prepare("DELETE FROM doclabels WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "loaddoclabels"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Tag</td>
						<td>Affecté à la société</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM doclabels p WHERE trash='".$_POST['state']."' AND type='".$_POST['type']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (label LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['sortby'] != ""){
						$req .= " ORDER BY ".$_POST['sortby'];
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					while($row = $back->fetch()){
						?>
					<tr>
						<td><label><input type="checkbox" name="doclabel" value="<?php echo $row['id'];?>" /><del class="checkmark"></del></label></td>
						<td>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0, 
									calc(100% - 18px) 0, 
									100% 18px, 
									100% calc(100% - 18px), 
									calc(100% - 18px) 100%, 
									0 100%
								  );	background:<?php echo $row['color'];?>;color:#FFFFFF;"><?php echo $row['label'];?></span>
						</td>
						<td style="white-space:nowrap;">
							<?php
							$companytxt = "";
							$back1 = $bdd->query("SELECT rs FROM companies WHERE id IN(".($row['company']!=""?$row['company']:0).")".$companiesid);
							while($company = $back1->fetch()){
								$companytxt .= ",".$company['rs'];
								?>
							<span><?php echo $company['rs'];?></span>
								<?php
							}
							$companytxt = substr($companytxt,1);
							?>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<?php
								if(preg_match("#Modifier Tags documents#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit lx-edit-doclabel lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-label="<?php echo $row['label'];?>"
								data-color="<?php echo $row['color'];?>"	
								data-company="<?php echo str_replace(",","||",$row['company']);?>"
								data-companytxt="<?php echo $companytxt;?>"								
								data-title="doclabel">Modifier</a>
								<?php
								}
								if(preg_match("#Supprimer Tags documents#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-doclabel lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Tag</td>
						<td>Affecté à la société</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> tag(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> tag(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}

			if($_POST['action'] == "savemessage"){
				$message = addslashes(whatsappToHtml($_POST['message']));
				$subject = addslashes($_POST['subject']);
				$req = $bdd->prepare("INSERT INTO `messageshistory`(`id`, `subject`, `message`, `sender`, `receiver`, `idpage`, `button`, `type`, `page`, `dateadd`) 
				VALUES ('0','".$subject."','".$message."','".$_POST['from']."','".$_POST['email']."','".$_POST['id']."','".$_POST['button']."','".$_POST['type']."','".$_POST['page']."','".time()."')");
				$req->execute();
				$back = $bdd->query("SELECT id FROM messageshistory WHERE idpage='".$_POST['id']."' ORDER BY id DESC LIMIT 1");
				$row = $back->fetch();
				if($_POST['type'] == "E-mail"){
					if($_POST['button'] != "Envoyer le document"){
						sendmail($_SESSION['easybm_id'],$_SESSION['easybm_fullname'],$_POST['email'],$subject,$message,$_POST['company'],$row['id'],'','',$bdd);
					}
					else{
						sendmail($_SESSION['easybm_id'],$_SESSION['easybm_fullname'],$_POST['email'],$subject,$message,$_POST['company'],$row['id'],$_POST['id'],$_POST['params'],$bdd);
					}
				}
			}
			
			if($_POST['action'] == "deletemessagehistory"){
				$req = $bdd->prepare("DELETE FROM messageshistory WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "loadmessageshistory"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Message</td>
						<td>Canal</td>
						<td>Date d'envoi</td>
						<td>Supprimer</td>
					</tr>
					<?php
					$req = "SELECT * FROM messageshistory WHERE idpage='".$_POST['id']."' AND page='".$_POST['page']."' AND button='".$_POST['button']."' ORDER BY id DESC";
					$back = $bdd->query($req);
					while($row = $back->fetch()){
						?>
					<tr>
						<td><label><input type="checkbox" name="messagehistory" value="<?php echo $row['id'];?>" /><del class="checkmark"></del></label></td>
						<td>
							<?php
							if($row['type'] == "E-mail"){
							?>
							<span><b>Envoyé de: </b><?php echo $row['sender'];?></span>
							<?php
							}
							?>
							<span><b>Envoyé vers: </b><?php echo $row['receiver'];?></span>
							<?php
							if($row['type'] == "E-mail"){
							?>
							<span><b>Objet: </b><?php echo $row['subject'];?></span>
							<?php
							}
							?>	
							<span><b>Message: </b><?php echo $row['message'];?></span>
						</td>
						<td><span><?php echo $row['type'];?></span></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<td style="position:relative;">
							<?php
							if(preg_match("#Suppression historique des messages#",$_SESSION['easybm_roles'])){
							?>
							<a href="javascript:;" class="lx-delete lx-delete-payment" >Supprimer</a>
							<div class="lx-delete-payment-choice">
								<a href="javascript:;" class="lx-yes-delete-message-history" data-id="<?php echo $row['id']?>">Oui</a>
								<a href="javascript:;" class="lx-no-delete-payment">Non</a>
							</div>
							<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
				</table>
				<?php
			}

			if($_POST['action'] == "addmessage"){
					$subject = "";
					if($_POST['type'] == "E-mail"){
						$subject = addslashes($_POST['subject']);
					}
					$type = "E-mail";
					if($_POST['button'] != "Envoyer le document"){
						$type = addslashes($_POST['type']);
					}
					if($_POST['id'] == "0"){
						$req = $bdd->prepare("INSERT INTO messages(id,title,subject,message,type,company,buttons,page,trash) 
						VALUES ('0','".addslashes($_POST['title'])."','".$subject."','".addslashes($_POST['message'])."','".$type."','".str_replace("||",",",$_POST['company'])."','".addslashes($_POST['button'])."','".addslashes($_POST['page'])."','1')");
						$req->execute();
					}
					else{
						$req = $bdd->prepare("UPDATE messages SET title='".addslashes($_POST['title'])."',
																subject='".$subject."',
																message='".addslashes($_POST['message'])."',
																type='".$type."',
																company='".str_replace("||",",",$_POST['company'])."',
																buttons='".addslashes($_POST['button'])."',
																page='".addslashes($_POST['page'])."'
																WHERE id='".$_POST['id']."'");
						$req->execute();
					}
			}

			if($_POST['action'] == "deletemessage"){
				$req = $bdd->prepare("DELETE FROM messages WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "restoremessage"){
				$req = $bdd->prepare("UPDATE messages SET trash='1' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "deletemessagepermanently"){
				$req = $bdd->prepare("DELETE FROM messages WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "loadmessages"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Titre</td>
						<td>Type</td>
						<td>Botton</td>
						<td>Objet</td>
						<td>Message</td>
						<td>Affecté à la société</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM messages p WHERE trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (buttons LIKE '".normalizeString($_POST['keyword'])."' OR subject LIKE '".normalizeString($_POST['keyword'])."' OR message LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['type'] != ""){
						$req .= " AND type='".sanitize_vars($_POST['type'])."'";
					}
					if($_POST['page'] != ""){
						$req .= " AND page='".sanitize_vars($_POST['page'])."'";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['sortby'] != ""){
						$req .= " ORDER BY ".$_POST['sortby'];
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					while($row = $back->fetch()){
						?>
					<tr>
						<td><label><input type="checkbox" name="message" value="<?php echo $row['id'];?>" /><del class="checkmark"></del></label></td>
						<td style="white-space:nowrap;"><span><?php echo $row['title'];?></span></td>
						<td style="white-space:nowrap;"><span><?php echo $row['type'];?></span></td>
						<td><span><?php echo $row['buttons'];?></span></td>
						<td><span><?php echo $row['subject'];?></span></td>
						<td><span><?php echo nl2br($row['message']);?></span></td>
						<td style="white-space:nowrap;">
							<?php
							$companytxt = "";
							$back1 = $bdd->query("SELECT rs FROM companies WHERE id IN(".($row['company']!=""?$row['company']:0).")".$companiesid);
							while($company = $back1->fetch()){
								$companytxt .= ",".$company['rs'];
								?>
							<span><?php echo $company['rs'];?></span>
								<?php
							}
							$companytxt = substr($companytxt,1);
							?>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<?php
								if(preg_match("#Modifier Modèles de messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit lx-edit-message lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-titl="<?php echo $row['title'];?>"
								data-type="<?php echo $row['type'];?>"
								data-button="<?php echo $row['buttons'];?>"
								data-subject="<?php echo $row['subject'];?>"						
								data-message="<?php echo $row['message'];?>"	
								data-company="<?php echo str_replace(",","||",$row['company']);?>"
								// data-companytxt="<?php echo $companytxt;?>"									
								data-title="message">Modifier</a>
								<?php
								}
								if(preg_match("#Supprimer Modèles de messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-message lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Titre</td>
						<td>Type</td>
						<td>Botton</td>
						<td>Objet</td>
						<td>Message</td>
						<td>Affecté à la société</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> modèle(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> modèle(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}
			
			if($_POST['action'] == "loadbankaccountslist"){
				?>
				<option value="0">Choisissez un compte banquaire</option>
				<?php
				$req = "SELECT * FROM bankaccounts WHERE company='".$_POST['id']."'";
				$back = $bdd->query($req);
				while($row = $back->fetch()){
					?>
				<option value="<?php echo $row['id'];?>"><?php echo $row['bank']." | ".$row['rib'];?></option>
					<?php 
				}
			}

			if($_POST['action'] == "addcompany"){
				$back = $bdd->query("SELECT id FROM companies WHERE ((rs='".sanitize_vars($_POST['rs'])."' AND rs<>'')
																	OR (rc='".sanitize_vars($_POST['rc'])."' AND rc<>'') 
																	OR (patente='".sanitize_vars($_POST['patente'])."' AND patente<>'')
																	OR (iff='".sanitize_vars($_POST['iff'])."' AND iff<>'')
																	OR (cnss='".sanitize_vars($_POST['cnss'])."' AND cnss<>'')
																	OR (ice='".sanitize_vars($_POST['ice'])."' AND ice<>'')) 
																	AND id NOT IN(".$_POST['id'].")");
				if($back->rowCount() == 0){
					if($_POST['id'] == "0"){
						$req = $bdd->prepare("INSERT INTO companies(id,rs,phone,address,email,website,capital,rc,patente,iff,cnss,ice,facture,devis,nh,nf,ph,avoir,br,factureproforma,bl,bs,bc,bre,headerheight,footerheight,user,trash) 
						VALUES ('0','".sanitize_vars($_POST['rs'])."','".sanitize_vars($_POST['phone'])."','".sanitize_vars($_POST['address'])."','".sanitize_vars($_POST['email'])."','".sanitize_vars($_POST['website'])."','".sanitize_vars($_POST['capital'])."','".sanitize_vars($_POST['rc'])."','".sanitize_vars($_POST['patente'])."','".sanitize_vars($_POST['iff'])."','".sanitize_vars($_POST['cnss'])."','".sanitize_vars($_POST['ice'])."','".sanitize_vars($_POST['facture'])."','".sanitize_vars($_POST['devis'])."','".sanitize_vars($_POST['nh'])."','".sanitize_vars($_POST['nf'])."','".sanitize_vars($_POST['ph'])."','".sanitize_vars($_POST['avoir'])."','".sanitize_vars($_POST['br'])."','".sanitize_vars($_POST['factureproforma'])."','".sanitize_vars($_POST['bl'])."','".sanitize_vars($_POST['bs'])."','".sanitize_vars($_POST['blcommand'])."','".sanitize_vars($_POST['brcommand'])."','".sanitize_vars($_POST['headerheight'])."','".sanitize_vars($_POST['footerheight'])."','".$_SESSION['easybm_id']."','1')");
						$req->execute();
						$back = $bdd->query("SELECT id FROM companies WHERE trash='1' ORDER BY id DESC LIMIT 0,1");
						$row = $back->fetch();
						$req = $bdd->prepare("UPDATE users SET companies=CONCAT(companies,',','".$row['id']."') WHERE (superadmin='1' OR id='".$_SESSION['easybm_id']."')");
						$req->execute();
					}
					else{
						$req = $bdd->prepare("UPDATE companies SET rs='".sanitize_vars($_POST['rs'])."',
																	phone='".sanitize_vars($_POST['phone'])."',
																	address='".sanitize_vars($_POST['address'])."',
																	email='".sanitize_vars($_POST['email'])."',
																	website='".sanitize_vars($_POST['website'])."',
																	capital='".sanitize_vars($_POST['capital'])."',
																	rc='".sanitize_vars($_POST['rc'])."',
																	patente='".sanitize_vars($_POST['patente'])."',
																	iff='".sanitize_vars($_POST['iff'])."',
																	cnss='".sanitize_vars($_POST['cnss'])."',
																	ice='".sanitize_vars($_POST['ice'])."',
																	facture='".sanitize_vars($_POST['facture'])."',
																	nh='".sanitize_vars($_POST['nh'])."',
																	nf='".sanitize_vars($_POST['nf'])."',
																	devis='".sanitize_vars($_POST['devis'])."',
																	ph='".sanitize_vars($_POST['ph'])."',
																	avoir='".sanitize_vars($_POST['avoir'])."',
																	br='".sanitize_vars($_POST['br'])."',
																	factureproforma='".sanitize_vars($_POST['factureproforma'])."',
																	bl='".sanitize_vars($_POST['bl'])."',
																	bs='".sanitize_vars($_POST['bs'])."',
																	bc='".sanitize_vars($_POST['blcommand'])."',
																	bre='".sanitize_vars($_POST['brcommand'])."',
																	headerheight='".sanitize_vars($_POST['headerheight'])."',
																	footerheight='".sanitize_vars($_POST['footerheight'])."'
																	WHERE id='".$_POST['id']."'");
						$req->execute();
					}
				}
				else{
					echo "Société existe déjà !";
				}
			}

			if($_POST['action'] == "deletecompany"){
				$back = $bdd->query("SELECT id FROM documents WHERE FIND_IN_SET(".$_POST['id'].",company)");
				if($back->rowCount() == 0){
					$back = $bdd->query("SELECT logo1,signature FROM companies WHERE id='".$_POST['id']."'");
					$row = $back->fetch();
					if($row['logo1'] != ""){
						if(file_exists("uploads/".$row['logo1'])){
							unlink("uploads/".$row['logo1']);
						}
						if(file_exists("uploads/micro_".$row['logo1'])){
							unlink("uploads/micro_".$row['logo1']);
						}
						if(file_exists("uploads/small_".$row['logo1'])){
							unlink("uploads/small_".$row['logo1']);
						}
						if(file_exists("uploads/cropped_".$row['logo1'])){
							unlink("uploads/cropped_".$row['logo1']);
						}
						if(file_exists("uploads/large_".$row['logo1'])){
							unlink("uploads/large_".$row['logo1']);
						}
					}
					if($row['signature'] != ""){
						if(file_exists("uploads/".$row['signature'])){
							unlink("uploads/".$row['signature']);
						}
						if(file_exists("uploads/micro_".$row['signature'])){
							unlink("uploads/micro_".$row['signature']);
						}
						if(file_exists("uploads/small_".$row['signature'])){
							unlink("uploads/small_".$row['signature']);
						}
						if(file_exists("uploads/cropped_".$row['signature'])){
							unlink("uploads/cropped_".$row['signature']);
						}
						if(file_exists("uploads/large_".$row['signature'])){
							unlink("uploads/large_".$row['signature']);
						}
					}
					$req = $bdd->prepare("DELETE FROM companies WHERE id='".$_POST['id']."'");
					$req->execute();
					$req = $bdd->prepare("DELETE FROM bankaccounts WHERE company='".$_POST['id']."'");
					$req->execute();		
					$back = $bdd->query("SELECT * FROM myregularity WHERE company='".$_POST['id']."'");
					if($back->rowCount() > 0){
						$row = $back->fetch();
						if($row['attachments'] != ""){
							if(file_exists("uploads/".$row['attachments'])){
								unlink("uploads/".$row['attachments']);
							}
						}
					}
					$req = $bdd->prepare("DELETE FROM myregularity WHERE company='".$_POST['id']."'");
					$req->execute();
					$req = $bdd->prepare("DELETE FROM infodocs WHERE company='".$_POST['id']."'");
					$req->execute();
				}
				else{
					echo "Vous ne pouvez pas supprimer une société mouvementée !";
				}
			}
			
			if($_POST['action'] == "deleteproductpicture"){
				$allpics = str_replace(",".$_POST['pic'],"",$_POST['allpics']);
				$allpics = str_replace($_POST['pic'],"",$allpics);
				if($_POST['table'] == "companies" AND $_POST['column'] == "logo1"){
					$req = $bdd->prepare("UPDATE companies SET logo1='' WHERE id='".$_POST['id']."'");
				}
				elseif($_POST['table'] == "companies" AND $_POST['column'] == "signature"){
					$req = $bdd->prepare("UPDATE companies SET signature='' WHERE id='".$_POST['id']."'");
				}
				elseif($_POST['table'] == "documents" AND $_POST['column'] == "picture"){
					$req = $bdd->prepare("UPDATE documents SET picture='' WHERE id='".$_POST['id']."'");
				}
				$req->execute();
				if(file_exists("uploads/".$_POST['pic'])){
					unlink("uploads/".$_POST['pic']);
				}
				if(file_exists("uploads/micro_".$_POST['pic'])){
					unlink("uploads/micro_".$_POST['pic']);
				}
				if(file_exists("uploads/small_".$_POST['pic'])){
					unlink("uploads/small_".$_POST['pic']);
				}
				if(file_exists("uploads/cropped_".$_POST['pic'])){
					unlink("uploads/cropped_".$_POST['pic']);
				}
				if(file_exists("uploads/large_".$_POST['pic'])){
					unlink("uploads/large_".$_POST['pic']);
				}
			}
			
			if($_POST['action'] == "restorecompany"){
				$req = $bdd->prepare("UPDATE companies SET trash='1' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "deletecompanypermanently"){
				$back = $bdd->query("SELECT id FROM products WHERE company='".$_POST['id']."'");
				if($back->rowCount() == 0){
					$req = $bdd->prepare("DELETE FROM companies WHERE id='".$_POST['id']."'");
					$req->execute();
				}
				else{
					echo "Cet enregistrement est lié à d'autres enregistrements, vous devez les supprimer avant de le supprimer !";
				}
			}
			
			if($_POST['action'] == "addbankaccount"){
				$back = $bdd->query("SELECT id FROM bankaccounts WHERE rib='".sanitize_vars($_POST['rib'])."' AND company='".str_replace("||",",",$_POST['company'])."'  AND id NOT IN(".$_POST['id'].")");
				if($back->rowCount() == 0){
					if($_POST['id'] == "0"){
						$req = $bdd->prepare("INSERT INTO bankaccounts(id,company,rib,bank,agency,city,trash) 
						VALUES ('0','".str_replace("||",",",$_POST['company'])."','".sanitize_vars($_POST['rib'])."','".sanitize_vars($_POST['bank'])."','".sanitize_vars($_POST['agency'])."','".sanitize_vars($_POST['city'])."','1')");
						$req->execute();
					}
					else{
						$req = $bdd->prepare("UPDATE bankaccounts SET rib='".sanitize_vars($_POST['rib'])."',
																	bank='".sanitize_vars($_POST['bank'])."',
																	agency='".sanitize_vars($_POST['agency'])."',
																	city='".sanitize_vars($_POST['city'])."'
																	WHERE id='".$_POST['id']."'");
						$req->execute();
					}
				}
				else{
					echo "Compte banquaire existe déjà !";
				}
			}

			if($_POST['action'] == "deletebankaccount"){
				$back = $bdd->query("SELECT id FROM payments WHERE FIND_IN_SET(".$_POST['id'].",rib)");
				if($back->rowCount() == 0){
					$req = $bdd->prepare("DELETE FROM bankaccounts WHERE id='".$_POST['id']."'");
					$req->execute();
				}
				else{
					echo "Vous ne pouvez pas supprimer un compte banquaire mouvementé !";
				}
			}

			if($_POST['action'] == "addmyregularity"){
				if($_POST['appras'] == "Non"){
					$_POST['doc'] = "";
					$_POST['attachments'] = "";
					$_POST['datedoc'] = "";
					$_POST['jdoc'] = "";
					$_POST['percentage'] = "";
				}
				if($_POST['doc'] == "Sans"){
					$_POST['attachments'] = "";
					$_POST['datedoc'] = "";
					$_POST['jdoc'] = "";
				}
				$datedoc = "";
				if($_POST['datedoc'] != ""){
					$datedoc = getDateNow($_POST['datedoc']);
				}
				$back = $bdd->query("SELECT id FROM myregularity WHERE company='".$_POST['company']."'");
				if($back->rowCount() == 0){
					$req = $bdd->prepare("INSERT INTO `myregularity`(`id`, `company`, `state`, `type`, `appras`, `doc`, `attachments`, `datedoc`, `jdoc`, `percentage`) 
					VALUES ('','".$_POST['company']."','".$_POST['status']."','".$_POST['type']."','".$_POST['appras']."','".$_POST['doc']."','".$_POST['attachments']."','".$datedoc."','".$_POST['jdoc']."','".$_POST['percentage']."')");
					$req->execute();
				}
				else{
					$req = $bdd->prepare("UPDATE `myregularity` SET state='".$_POST['status']."',
																	type='".$_POST['type']."',
																	appras='".$_POST['appras']."',
																	doc='".$_POST['doc']."',
																	attachments='".$_POST['attachments']."',
																	datedoc='".$datedoc."',
																	jdoc='".$_POST['jdoc']."',
																	percentage='".$_POST['percentage']."' WHERE company='".$_POST['company']."'");
					$req->execute();
				}
			}
			
			if($_POST['action'] == "loadcompanies"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>ID</td>
						<td>Raison social</td>
						<td>Logo</td>
						<td>Cachet / Signature</td>
						<td>Contacts société</td>
						<td>Informations société</td>
						<td>Comptes banquaires</td>
						<td>Régularité fiscale</td>
						<td>Début de numérotation</td>
						<td>Options d'impression</td>
						<td>Informations supplémentaires sur le document</td>
						<?php
						if(preg_match("#CA Sociétés#",$_SESSION['easybm_roles'])){
						?>
						<td>Chiffre d'affaires <i class="fa fa-sort" data-sort="ca"></i></td>
						<td>Payé <i class="fa fa-sort" data-sort="paid"></i></td>
						<td>En cours <i class="fa fa-sort" data-sort="encours"></i></td>
						<?php
						}
						?>
						<td>Utilisateur</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM companies p WHERE trash='".$_POST['state']."'".$companiesid;
					if($_POST['keyword'] != ""){
						$req .= " AND (rs LIKE '".normalizeString($_POST['keyword'])."' 
										OR phone LIKE '".normalizeString($_POST['keyword'])."' 
										OR address LIKE '".normalizeString($_POST['keyword'])."' 
										OR email LIKE '".normalizeString($_POST['keyword'])."' 
										OR website LIKE '".normalizeString($_POST['keyword'])."'
										OR rc LIKE '".normalizeString($_POST['keyword'])."'
										OR patente LIKE '".normalizeString($_POST['keyword'])."'
										OR iff LIKE '".normalizeString($_POST['keyword'])."'
										OR cnss LIKE '".normalizeString($_POST['keyword'])."'
										OR ice LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "ca"){
							$req .= " ORDER BY ca";
						}
						elseif($_POST['sortby'] == "paid"){
							$req .= " ORDER BY payments";
						}
						elseif($_POST['sortby'] == "encours"){
							$req .= " ORDER BY ca-payments";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$ca = 0;
					$paid = 0;
					$rest = 0;
					while($row = $back->fetch()){
						?>
					<tr>
						<td><label><input type="checkbox" name="company" value="<?php echo $row['id'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo $row['id'];?></span></td>
						<td><span><?php echo $row['rs'];?></span></td>
						<td>
							<?php
							if($row['logo1'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['logo1'];?>" data-pic="<?php echo $row['logo1'];?>" data-table="companies" data-column="logo1" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['logo1'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="companies" data-column="logo1" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<td>
							<?php
							if($row['signature'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['signature'];?>" data-pic="<?php echo $row['signature'];?>" data-table="companies" data-column="signature" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['signature'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos0<?php echo $row['id'];?>" data-table="companies" data-column="signature" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>						
						<td style="white-space:nowrap;">
							<span>Téléphone: <b><?php echo $row['phone'];?></b></span>
							<span>Adresse: <b><?php echo $row['address'];?></b></span>
							<span>Email: <b><?php echo $row['email'];?></b></span>
							<span>Siteweb: <b><?php echo $row['website'];?></b></span>
						</td>
						<td style="white-space:nowrap;">
							<span>Capital: <b><?php echo $row['capital'];?></b></span>
							<span>RC: <b><?php echo $row['rc'];?></b></span>
							<span>Patente: <b><?php echo $row['patente'];?></b></span>
							<span>IF: <b><?php echo $row['iff'];?></b></span>
							<span>CNSS: <b><?php echo $row['cnss'];?></b></span>
							<span>ICE: <b><?php echo $row['ice'];?></b></span>
						</td>
						<td style="white-space:nowrap;">
							<?php
							$back1 = $bdd->query("SELECT * FROM bankaccounts WHERE company='".$row['id']."'");
							while($row1 = $back1->fetch()){
								?>
							<div class="lx-back-acocunt" style="white-space:nowrap;margin-bottom:10px;padding:10px;background:#fcfdec;border:1px solid #242424;border-radius:6px;">
								<span>N° compte / RIB: <b><?php echo $row1['rib'];?></b></span>
								<span>La banque: <b><?php echo $row1['bank'];?></b></span>
								<span>Agence: <b><?php echo $row1['agency'];?></b></span>
								<span>Ville: <b><?php echo $row1['city'];?></b></span>
								<p style="text-align:right;padding:0px;margin:0px;">
								<a href="javascript:;" class="lx-edit-bankaccount lx-open-popup" data-header="Modifier un" title="Modifier"
								data-id="<?php echo $row1['id'];?>"
								data-rib="<?php echo $row1['rib'];?>"
								data-bank="<?php echo $row1['bank'];?>"
								data-agency="<?php echo $row1['agency'];?>"
								data-city="<?php echo $row1['city'];?>"
								data-company="<?php echo $row['id'];?>"
								data-title="bankaccount"><i class="fa fa-edit"></i></a>
								<a href="javascript:;" class="lx-delete lx-delete-bankaccount lx-open-popup" data-title="deleterecord1" title="Supprimer" style="position:relative;top:2px;font-size:16px;" 
								data-id="<?php echo $row1['id'];?>"><i class="fa fa-times"></i></a>
								</p>
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<a href="javascript:;" class="lx-new-bankaccount lx-open-popup" data-header="Ajouter un nouveau"
								data-id="0"
								data-company="<?php echo $row['id'];?>"
								data-title="bankaccount">Ajouter compte</a>
							</div>
						</div>
						<td style="white-space:nowrap;">
							<?php
							$back1 = $bdd->query("SELECT * FROM myregularity WHERE company='".$row['id']."'");
							$row1 = $back1->fetch();
							if(isset($row1['state'])){
							?>
							<span>Statut: <b><?php echo $row1['state'];?></b></span>
							<?php
							}
							if(isset($row1['type'])){
								if($row1['type'] != ""){
							?>
							<span>Type d'activité: <b><?php echo $row1['type'];?></b></span>
							<?php
								}
							}
							if(isset($row1['state'])){
								if($row1['state'] != ""){
							?>
							<span>RAS applicable: <b><?php echo $row1['appras'];?></b></span>
							<?php
								}
							}
							if(isset($row1['doc'])){
								if($row1['doc'] != ""){
								?>
							<span>Régularité fiscale: <b><?php echo $row1['doc'];?></b></span>
								<?php
								}
								if($row1['doc'] == "Avec"){
								?>
							<fieldset style="margin:5px 0px;padding:10px;padding-top:5px;">
								<legend><b>Document régularité fiscale:</b></legend>
									<?php
									if(isset($row1['attachments'])){
										if($row1['attachments'] != ""){
									?>
								<span><b><a href="uploads/<?php echo $row1['attachments'];?>" target="_blank" style="color:#39add1;"><i class="fa fa-download"></i> Téléchager</a></b></span>
									<?php
										}
									}
									if(isset($row1['datedoc'])){
										if($row1['datedoc'] != ""){
									?>
								<span>Date demande: <b><?php echo date("d/m/Y",$row1['datedoc']);?></b></span>
									<?php
										}
									}
									if(isset($row1['jdoc'])){
										if($row1['jdoc'] != ""){
									?>
								<span>Période de validité en jours: <b><?php echo $row1['jdoc'];?> jours</b></span>
									<?php
										}
									}
								}
								?>
							</fieldset>
								<?php
							}
							if(isset($row1['percentage'])){
								if($row1['percentage'] != ""){
								?>
							<span>% TVA à retenir par le client: <b><?php echo $row1['percentage'];?>%</b></span>
								<?php
								}
							}
							if($back1->rowCount() > 0){
							?>
							<br />
							<?php
							}
							?>
							<div class="lx-upload-photos">
								<a href="javascript:;" class="lx-edit-myregularity lx-open-popup" data-header="Mise a jour"
								data-company="<?php echo $row['id'];?>"
								data-status="<?php echo isset($row1['state'])?$row1['state']:"";?>"
								data-type="<?php echo isset($row1['type'])?$row1['type']:"";?>"
								data-appras="<?php echo isset($row1['appras'])?$row1['appras']:"";?>"
								data-doc="<?php echo isset($row1['doc'])?$row1['doc']:"";?>"
								data-attachments="<?php echo isset($row1['attachments'])?$row1['attachments']:"";?>"
								data-datedoc="<?php echo isset($row1['datedoc'])?($row1['datedoc']!=""?date("d/m/Y",$row1['datedoc']):""):"";?>"
								data-jdoc="<?php echo isset($row1['jdoc'])?$row1['jdoc']:"";?>"
								data-percentage="<?php echo isset($row1['percentage'])?$row1['percentage']:"";?>"
								data-title="myregularity"><i class="fa fa-edit"></i> Régularité fiscale</a>
							</div>
						</td>
						<td style="white-space:nowrap;">
							<?php
							if(preg_match("#Consulter Factures clients#",$_SESSION['easybm_roles'])){
							?>
							<span>Factures: <b><?php echo $row['facture'];?></b></span>
							<?php
							}
							if(preg_match("#Consulter Notes honoraire#",$_SESSION['easybm_roles'])){
							?>
							<span>Notes d'honoraires: <b><?php echo $row['nh'];?></b></span>
							<?php
							}
							if(preg_match("#Consulter Notes de frais#",$_SESSION['easybm_roles'])){
							?>
							<span>Notes de frais: <b><?php echo $row['nf'];?></b></span>
							<?php
							}
							if(preg_match("#Consulter Devis,#",$_SESSION['easybm_roles'])){
							?>
							<span>Devis: <b><?php echo $row['devis'];?></b></span>
							<?php
							}
							if(preg_match("#Consulter Propositions honoraires#",$_SESSION['easybm_roles'])){
							?>
							<span>Propositions d'honoraires: <b><?php echo $row['ph'];?></b></span>
							<?php
							}
							if(preg_match("#Consulter Factures avoir#",$_SESSION['easybm_roles'])){
							?>
							<span>Factures avoir: <b><?php echo $row['avoir'];?></b></span>
							<?php
							}
							if(preg_match("#Consulter Bons de retour#",$_SESSION['easybm_roles'])){
							?>
							<span>Bons de retour: <b><?php echo $row['br'];?></b></span>
							<?php
							}
							if(preg_match("#Consulter Factures proforma#",$_SESSION['easybm_roles'])){
							?>
							<span>Factures proforma: <b><?php echo $row['factureproforma'];?></b></span>
							<?php
							}
							if(preg_match("#Consulter Bons de livraison#",$_SESSION['easybm_roles'])){
							?>
							<span>Bons de livraison: <b><?php echo $row['bl'];?></b></span>
							<?php
							}
							if(preg_match("#Consulter Bons de sortie#",$_SESSION['easybm_roles'])){
							?>
							<span>Bons de sortie: <b><?php echo $row['bs'];?></b></span>
							<?php
							}
							if(preg_match("#Consulter Bons de commande#",$_SESSION['easybm_roles'])){
							?>
							<span>Bons de commande: <b><?php echo $row['bc'];?></b></span>
							<?php
							}
							if(preg_match("#Consulter Bons de réception#",$_SESSION['easybm_roles'])){
							?>
							<span>Bons de réception : <b><?php echo $row['bre'];?></b></span>
							<?php
							}
							?>
						</td>
						<td style="white-space:nowrap;">
							<span>Hauteur d'entête de page: <b><?php echo $row['headerheight'];?>cm</b></span>
							<span>Hauteur de pied de page: <b><?php echo $row['footerheight'];?>cm</b></span>
						</td>
						<td>
							<?php
							$info = 0;
							if(preg_match("#Consulter Factures clients#",$_SESSION['easybm_roles'])){
							$check = ' <i class="fa fa-times" style="font-weight:bold;color:#FF0000;"></i>';
							$back1 = $bdd->query("SELECT id FROM infodocs WHERE company='".$row['id']."' AND document='facture' AND (modepayment<>'' OR abovetable<>'')");
							if($back1->rowCount() > 0){
								$check = ' <i class="fa fa-check" style="font-weight:bold;color:green;"></i>';
							}
							?>
							<span>Factures clients<?php echo $check;?></span>
							<?php
							$info++;
							}
							if(preg_match("#Consulter Notes d'honoraires#",$_SESSION['easybm_roles'])){
							$check = ' <i class="fa fa-times" style="font-weight:bold;color:#FF0000;"></i>';
							$back1 = $bdd->query("SELECT id FROM infodocs WHERE company='".$row['id']."' AND document='nh' AND (modepayment<>'' OR abovetable<>'')");
							if($back1->rowCount() > 0){
								$check = ' <i class="fa fa-check" style="font-weight:bold;color:green;"></i>';
							}
							?>
							<span>Notes d'honoraires<?php echo $check;?></span>
							<?php
							$info++;
							}
							if(preg_match("#Consulter Notes de frais#",$_SESSION['easybm_roles'])){
							$check = ' <i class="fa fa-times" style="font-weight:bold;color:#FF0000;"></i>';
							$back1 = $bdd->query("SELECT id FROM infodocs WHERE company='".$row['id']."' AND document='nf' AND (modepayment<>'' OR abovetable<>'')");
							if($back1->rowCount() > 0){
								$check = ' <i class="fa fa-check" style="font-weight:bold;color:green;"></i>';
							}
							?>
							<span>Notes de frais<?php echo $check;?></span>
							<?php
							$info++;
							}
							if(preg_match("#Consulter Devis,#",$_SESSION['easybm_roles'])){
							$check = ' <i class="fa fa-times" style="font-weight:bold;color:#FF0000;"></i>';
							$back1 = $bdd->query("SELECT id FROM infodocs WHERE company='".$row['id']."' AND document='devis' AND (modepayment<>'' OR abovetable<>'')");
							if($back1->rowCount() > 0){
								$check = ' <i class="fa fa-check" style="font-weight:bold;color:green;"></i>';
							}
							?>
							<span>Devis<?php echo $check;?></span>
							<?php
							$info++;
							}
							if(preg_match("#Consulter Propositions d'honoraires#",$_SESSION['easybm_roles'])){
							$check = ' <i class="fa fa-times" style="font-weight:bold;color:#FF0000;"></i>';
							$back1 = $bdd->query("SELECT id FROM infodocs WHERE company='".$row['id']."' AND document='ph' AND (modepayment<>'' OR abovetable<>'')");
							if($back1->rowCount() > 0){
								$check = ' <i class="fa fa-check" style="font-weight:bold;color:green;"></i>';
							}
							?>
							<span>Propositions d'honoraires<?php echo $check;?></span>
							<?php
							$info++;
							}
							if(preg_match("#Consulter Factures avoir#",$_SESSION['easybm_roles'])){
							$check = ' <i class="fa fa-times" style="font-weight:bold;color:#FF0000;"></i>';
							$back1 = $bdd->query("SELECT id FROM infodocs WHERE company='".$row['id']."' AND document='avoir' AND (modepayment<>'' OR abovetable<>'')");
							if($back1->rowCount() > 0){
								$check = ' <i class="fa fa-check" style="font-weight:bold;color:green;"></i>';
							}
							?>
							<span>Factures avoir<?php echo $check;?></span>
							<?php
							$info++;
							}
							if(preg_match("#Consulter Bons de retour#",$_SESSION['easybm_roles'])){
							$check = ' <i class="fa fa-times" style="font-weight:bold;color:#FF0000;"></i>';
							$back1 = $bdd->query("SELECT id FROM infodocs WHERE company='".$row['id']."' AND document='br' AND (modepayment<>'' OR abovetable<>'')");
							if($back1->rowCount() > 0){
								$check = ' <i class="fa fa-check" style="font-weight:bold;color:green;"></i>';
							}
							?>
							<span>Bons de retour<?php echo $check;?></span>
							<?php
							$info++;
							}
							if(preg_match("#Consulter Factures proforma#",$_SESSION['easybm_roles'])){
							$check = ' <i class="fa fa-times" style="font-weight:bold;color:#FF0000;"></i>';
							$back1 = $bdd->query("SELECT id FROM infodocs WHERE company='".$row['id']."' AND document='factureproforma' AND (modepayment<>'' OR abovetable<>'')");
							if($back1->rowCount() > 0){
								$check = ' <i class="fa fa-check" style="font-weight:bold;color:green;"></i>';
							}
							?>
							<span>Factures proforma<?php echo $check;?></span>
							<?php
							$info++;
							}
							if(preg_match("#Consulter Bons de livraison#",$_SESSION['easybm_roles'])){
							$check = ' <i class="fa fa-times" style="font-weight:bold;color:#FF0000;"></i>';
							$back1 = $bdd->query("SELECT id FROM infodocs WHERE company='".$row['id']."' AND document='bl' AND (modepayment<>'' OR abovetable<>'')");
							if($back1->rowCount() > 0){
								$check = ' <i class="fa fa-check" style="font-weight:bold;color:green;"></i>';
							}
							?>
							<span>Bons de livraison<?php echo $check;?></span>
							<?php
							$info++;
							}
							if(preg_match("#Consulter Bons de sortie#",$_SESSION['easybm_roles'])){
							$check = ' <i class="fa fa-times" style="font-weight:bold;color:#FF0000;"></i>';
							$back1 = $bdd->query("SELECT id FROM infodocs WHERE company='".$row['id']."' AND document='bs' AND (modepayment<>'' OR abovetable<>'')");
							if($back1->rowCount() > 0){
								$check = ' <i class="fa fa-check" style="font-weight:bold;color:green;"></i>';
							}
							?>
							<span>Bons de sortie<?php echo $check;?></span>
							<?php
							$info++;
							}
							if(preg_match("#Consulter Bons de commande#",$_SESSION['easybm_roles'])){
							$check = ' <i class="fa fa-times" style="font-weight:bold;color:#FF0000;"></i>';
							$back1 = $bdd->query("SELECT id FROM infodocs WHERE company='".$row['id']."' AND document='bc' AND (modepayment<>'' OR abovetable<>'')");
							if($back1->rowCount() > 0){
								$check = ' <i class="fa fa-check" style="font-weight:bold;color:green;"></i>';
							}
							?>
							<span>Bons de commande<?php echo $check;?></span>
							<?php
							$info++;
							}
							if(preg_match("#Consulter Bons de réception#",$_SESSION['easybm_roles'])){
							$check = ' <i class="fa fa-times" style="font-weight:bold;color:#FF0000;"></i>';
							$back1 = $bdd->query("SELECT id FROM infodocs WHERE company='".$row['id']."' AND document='bre' AND (modepayment<>'' OR abovetable<>'')");
							if($back1->rowCount() > 0){
								$check = ' <i class="fa fa-check" style="font-weight:bold;color:green;"></i>';
							}
							?>
							<span>Bons de réception<?php echo $check;?></span>
							<?php
							$info++;
							}
							if($info > 0){
							?>
							<div class="lx-upload-photos">
								<a href="javascript:;" class="lx-new-extrainfo lx-open-popup" data-header="Ajouter un nouveau"
									data-company="<?php echo $row['id'];?>"
									data-title="costumizeextrainfo">Personnaliser</a>							
							</div>
							<?php
							}
							?>
						</td>
						<td><span style="white-space:nowrap;text-align:right;"><?php echo number_format((float)($row['ca']!=""?$row['ca']:"0"),2,'.',' ');?> <?php echo $settings['currency'];?> TTC</span></td>
						<td><span style="white-space:nowrap;text-align:right;"><?php echo number_format((float)($row['payments']),2,'.',' ');?> <?php echo $settings['currency'];?>  TTC</span></td>
						<td><span style="white-space:nowrap;text-align:right;"><?php echo number_format((float)($row['ca'] - $row['payments']),2,'.',' ');?> <?php echo $settings['currency'];?>  TTC</span></td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
							$user = $back1->fetch();
							?>
							<span><?php echo $user['fullname'];?></span>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<?php
								if(preg_match("#Modifier Sociétés#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit lx-edit-company lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-rs="<?php echo $row['rs'];?>"
								data-phone="<?php echo $row['phone'];?>"
								data-address="<?php echo $row['address'];?>"
								data-email="<?php echo $row['email'];?>"
								data-website="<?php echo $row['website'];?>"
								data-capital="<?php echo $row['capital'];?>"
								data-rc="<?php echo $row['rc'];?>"
								data-patente="<?php echo $row['patente'];?>"
								data-iff="<?php echo $row['iff'];?>"
								data-cnss="<?php echo $row['cnss'];?>"
								data-ice="<?php echo $row['ice'];?>"
								data-headerheight="<?php echo $row['headerheight'];?>"
								data-footerheight="<?php echo $row['footerheight'];?>"
								<?php
								$back1 = $bdd->query("SELECT id FROM documents WHERE type='facture' AND company='".$row['id']."' LIMIT 0,1");
								?>
								data-facturereadonly="<?php echo $back1->rowCount()>0?"true":"false";?>"	
								data-facture="<?php echo $row['facture'];?>"	
								<?php
								$back1 = $bdd->query("SELECT id FROM documents WHERE type='nh' AND company='".$row['id']."' LIMIT 0,1");
								?>
								data-nhreadonly="<?php echo $back1->rowCount()>0?"true":"false";?>"	
								data-nh="<?php echo $row['nh'];?>"								
								<?php
								$back1 = $bdd->query("SELECT id FROM documents WHERE type='nf' AND company='".$row['id']."' LIMIT 0,1");
								?>
								data-nfreadonly="<?php echo $back1->rowCount()>0?"true":"false";?>"	
								data-nf="<?php echo $row['nf'];?>"	
								<?php
								$back1 = $bdd->query("SELECT id FROM documents WHERE type='devis' AND company='".$row['id']."' LIMIT 0,1");
								?>
								data-devisreadonly="<?php echo $back1->rowCount()>0?"true":"false";?>"	
								data-devis="<?php echo $row['devis'];?>"
								<?php
								$back1 = $bdd->query("SELECT id FROM documents WHERE type='ph' AND company='".$row['id']."' LIMIT 0,1");
								?>
								data-phreadonly="<?php echo $back1->rowCount()>0?"true":"false";?>"	
								data-ph="<?php echo $row['ph'];?>"	
								<?php
								$back1 = $bdd->query("SELECT id FROM documents WHERE type='avoir' AND company='".$row['id']."' LIMIT 0,1");
								?>
								data-avoirreadonly="<?php echo $back1->rowCount()>0?"true":"false";?>"	
								data-avoir="<?php echo $row['avoir'];?>"	
								<?php
								$back1 = $bdd->query("SELECT id FROM documents WHERE type='br' AND company='".$row['id']."' LIMIT 0,1");
								?>
								data-brreadonly="<?php echo $back1->rowCount()>0?"true":"false";?>"	
								data-br="<?php echo $row['br'];?>"								
								<?php
								$back1 = $bdd->query("SELECT id FROM documents WHERE type='factureproforma' AND company='".$row['id']."' LIMIT 0,1");
								?>
								data-factureproformareadonly="<?php echo $back1->rowCount()>0?"true":"false";?>"	
								data-factureproforma="<?php echo $row['factureproforma'];?>"							
								<?php
								$back1 = $bdd->query("SELECT id FROM documents WHERE type='bl' AND company='".$row['id']."' LIMIT 0,1");
								?>
								data-blreadonly="<?php echo $back1->rowCount()>0?"true":"false";?>"	
								data-bl="<?php echo $row['bl'];?>"								
								<?php
								$back1 = $bdd->query("SELECT id FROM documents WHERE type='bs' AND company='".$row['id']."' LIMIT 0,1");
								?>
								data-bsreadonly="<?php echo $back1->rowCount()>0?"true":"false";?>"	
								data-bs="<?php echo $row['bs'];?>"								
								<?php
								$back1 = $bdd->query("SELECT id FROM documents WHERE type='bc' AND company='".$row['id']."' LIMIT 0,1");
								?>
								data-blcommandreadonly="<?php echo $back1->rowCount()>0?"true":"false";?>"	
								data-blcommand="<?php echo $row['bc'];?>"							
								<?php
								$back1 = $bdd->query("SELECT id FROM documents WHERE type='bre' AND company='".$row['id']."' LIMIT 0,1");
								?>
								data-brcommandreadonly="<?php echo $back1->rowCount()>0?"true":"false";?>"	
								data-brcommand="<?php echo $row['bre'];?>"								
								data-title="company">Modifier</a>
								<?php
								}
								if(preg_match("#Supprimer Sociétés#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-company lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>ID</td>
						<td>Raison social</td>
						<td>Logo</td>
						<td>Cachet / Signature</td>
						<td>Contacts société</td>
						<td>Informations société</td>
						<td>Comptes banquaires</td>
						<td>Régularité fiscale</td>
						<td>Début de numérotation</td>
						<td>Options d'impression</td>
						<td>Informations supplémentaires sur le document</td>
						<?php
						if(preg_match("#CA Sociétés#",$_SESSION['easybm_roles'])){
						?>
						<td>Chiffre d'affaires <i class="fa fa-sort" data-sort="ca"></i></td>
						<td>Payé <i class="fa fa-sort" data-sort="paid"></i></td>
						<td>En cours <i class="fa fa-sort" data-sort="encours"></i></td>
						<?php
						}
						?>
						<td>Utilisateur</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> société(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> société(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}

			if($_POST['action'] == "loaddocsextrainfo"){
				$back = $bdd->query("SELECT * FROM infodocs WHERE company='".$_POST['company']."' AND document='".$_POST['document']."'");
				$row = $back->fetch();
				?>
				<div class="lx-textfield lx-g1 lx-pb-0">
					<label><span>Note au-dessus du tableau: </span><textarea name="abovetable" id="abovetable" /></textarea></label>
				</div>
				<div class="lx-clear-fix"></div>
				<div class="lx-textfield lx-g1 lx-pb-0">
					<label><span>Mode au-dessous du tableau: </span><textarea name="modepayment" id="modepayment" /></textarea></label>
				</div>
				<div class="lx-clear-fix"></div>
				<div class="lx-textfield lx-g1 lx-pb-0" style="display:none;">
					<label><span>Conditions (au-dessous du tableau): </span><textarea name="conditions" id="conditions" /></textarea></label>
				</div>
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
					window.setTimeout(function(){
						tinyMCE.get("modepayment").setContent(`<?php echo isset($row['modepayment'])?$row['modepayment']:'';?>`);
						tinyMCE.get("conditions").setContent(`<?php echo isset($row['conditions'])?$row['conditions']:'';?>`);
						tinyMCE.get("abovetable").setContent(`<?php echo isset($row['abovetable'])?$row['abovetable']:'';?>`);						
					},1000);
				</script>
				<?php
			}

			if($_POST['action'] == "savedocinfo"){
				$back = $bdd->query("SELECT id FROM infodocs WHERE company='".$_POST['company']."' AND document='".$_POST['document']."'");
				if($back->rowCount() > 0){
					$row = $back->fetch();
					$req = $bdd->prepare("UPDATE infodocs SET modepayment='".addslashes($_POST['modepayment'])."',conditions='".addslashes($_POST['conditions'])."',abovetable='".addslashes($_POST['abovetable'])."' WHERE company='".$_POST['company']."' AND document='".$_POST['document']."'");
					$req->execute();
				}
				else{
					$req = $bdd->prepare("INSERT INTO infodocs(id,company,document,modepayment,conditions,abovetable) VALUES(0,'".$_POST['company']."','".$_POST['document']."','".addslashes($_POST['modepayment'])."','".addslashes($_POST['conditions'])."','".addslashes($_POST['abovetable'])."')");
					$req->execute();					
				}
			}
			
			if($_POST['action'] == "addsupplier"){
				$back = $bdd->query("SELECT id FROM suppliers WHERE (title='".sanitize_vars($_POST['title'])."') AND id NOT IN(".$_POST['id'].")");
				if($back->rowCount() == 0){
					if($_POST['id'] == "0"){
						$rand = "FR".date("ym")."-0001";
						$back1 = $bdd->query("SELECT code FROM suppliers ORDER BY id DESC LIMIT 0,1");
						if($back1->rowCount() > 0){
							$row1 = $back1->fetch();
							$rand = "FR".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
						}
						$req = $bdd->prepare("INSERT INTO suppliers(id,code,codefo,title,respname,respphone,whatsapp,respemail,respfax,ice,iff,rc,address,note,company,user,dateadd,trash) 
						VALUES ('0','".$rand."','".sanitize_vars($_POST['codefo'])."','".sanitize_vars($_POST['title'])."','".sanitize_vars($_POST['respname'])."','".sanitize_vars($_POST['respphone'])."','".sanitize_vars($_POST['whatsapp'])."','".sanitize_vars($_POST['respemail'])."','".sanitize_vars($_POST['respfax'])."','".sanitize_vars($_POST['ice'])."','".sanitize_vars($_POST['iff'])."','".sanitize_vars($_POST['rc'])."','".sanitize_vars($_POST['address'])."','".addslashes($_POST['note'])."','".str_replace("||",",",$_POST['company'])."','".$_SESSION['easybm_id']."','".time()."','1')");
						$req->execute();
					}
					else{
						$req = $bdd->prepare("UPDATE suppliers SET codefo='".sanitize_vars($_POST['codefo'])."',title='".sanitize_vars($_POST['title'])."',respname='".sanitize_vars($_POST['respname'])."',respphone='".sanitize_vars($_POST['respphone'])."',whatsapp='".sanitize_vars($_POST['whatsapp'])."',respemail='".sanitize_vars($_POST['respemail'])."',respfax='".sanitize_vars($_POST['respfax'])."',note='".addslashes($_POST['note'])."',ice='".addslashes($_POST['ice'])."',iff='".addslashes($_POST['iff'])."',rc='".addslashes($_POST['rc'])."',address='".addslashes($_POST['address'])."',company='".str_replace("||",",",$_POST['company'])."' WHERE id='".$_POST['id']."'");
						$req->execute();
					}
				}
				else{
					echo "Fournisseur existe déjà !";
				}
			}

			if($_POST['action'] == "deletesupplier"){
				$back = $bdd->query("SELECT id FROM documents WHERE supplier='".$_POST['id']."'");
				$back1 = $bdd->query("SELECT id FROM facturesuppliers WHERE supplier='".$_POST['id']."'");
				$back2 = $bdd->query("SELECT id FROM payments WHERE supplier='".$_POST['id']."'");
				if($back->rowCount() == 0 AND $back1->rowCount() == 0 AND $back2->rowCount() == 0){
					$req = $bdd->prepare("DELETE FROM suppliers WHERE id='".$_POST['id']."'");
					$req->execute();
					
					$req = $bdd->prepare("DELETE FROM suppliersbankaccounts WHERE supplier='".$_POST['id']."'");
					$req->execute();		
					$back = $bdd->query("SELECT * FROM regularity WHERE supplier='".$_POST['id']."'");
					if($back->rowCount() > 0){
						$row = $back->fetch();
						if(isset($row['attachments'])){
							if(file_exists("uploads/".$row['attachments'])){
								unlink("uploads/".$row['attachments']);
							}
						}
					}
					$req = $bdd->prepare("DELETE FROM regularity WHERE supplier='".$_POST['id']."'");
					$req->execute();
				}
				else{
					echo "Nous avons détecté des factures, des documents fournisseur ou des mouvements de trésorerie liés à ce fournisseur. Vous ne pouvez donc pas le supprimer.";
				}
			}
			
			if($_POST['action'] == "restoresupplier"){
				$req = $bdd->prepare("UPDATE suppliers SET trash='1' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "deletesupplierpermanently"){
				$back = $bdd->query("SELECT id,supplier FROM (SELECT id,supplier FROM products UNION SELECT id,supplier FROM commandsuppliers) AS t WHERE supplier='".$_POST['id']."'");
				if($back->rowCount() == 0){
					$req = $bdd->prepare("DELETE FROM suppliers WHERE id='".$_POST['id']."'");
					$req->execute();
				}
				else{
					echo "Cet enregistrement est lié à d'autres enregistrements, vous devez les supprimer avant de le supprimer !";
				}
			}

			if($_POST['action'] == "addsupplierbankaccount"){
				$back = $bdd->query("SELECT id FROM suppliersbankaccounts WHERE rib='".sanitize_vars($_POST['rib'])."' AND iban='".sanitize_vars($_POST['iban'])."' AND supplier='".sanitize_vars($_POST['supplier'])."'  AND id NOT IN(".$_POST['id'].")");
				if($back->rowCount() == 0){
					if($_POST['id'] == "0"){
						$req = $bdd->prepare("INSERT INTO suppliersbankaccounts(id,supplier,location,rib,iban,swift,address,bank,agency,trash) 
						VALUES ('0','".sanitize_vars($_POST['supplier'])."','".sanitize_vars($_POST['type'])."','".sanitize_vars($_POST['rib'])."','".sanitize_vars($_POST['iban'])."','".sanitize_vars($_POST['swift'])."','".sanitize_vars($_POST['address'])."','".sanitize_vars($_POST['bank'])."','".sanitize_vars($_POST['agency'])."','1')");
						$req->execute();
					}
					else{
						$req = $bdd->prepare("UPDATE suppliersbankaccounts SET location='".sanitize_vars($_POST['type'])."',
																				rib='".sanitize_vars($_POST['rib'])."',
																				iban='".sanitize_vars($_POST['iban'])."',
																				swift='".sanitize_vars($_POST['swift'])."',
																				address='".sanitize_vars($_POST['address'])."',
																				bank='".sanitize_vars($_POST['bank'])."',
																				agency='".sanitize_vars($_POST['agency'])."'
																				WHERE id='".$_POST['id']."'");
						$req->execute();
					}
				}
				else{
					echo "Compte banquaire existe déjà !";
				}
			}

			if($_POST['action'] == "deletesupplierbankaccount"){
				$back = $bdd->query("SELECT id FROM payments WHERE FIND_IN_SET(".$_POST['id'].",rib2)");
				if($back->rowCount() == 0){
					$req = $bdd->prepare("DELETE FROM suppliersbankaccounts WHERE id='".$_POST['id']."'");
					$req->execute();
				}
				else{
					echo "Vous ne pouvez pas supprimer un compte banquaire mouvementé !";
				}
			}

			if($_POST['action'] == "adddelay"){
				$req = $bdd->prepare("UPDATE ".$_POST['table']." SET delay='".sanitize_vars($_POST['delay'])."' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "addregularity"){
				if($_POST['appras'] == "Non"){
					$_POST['doc'] = "";
					$_POST['attachments'] = "";
					$_POST['datedoc'] = "";
					$_POST['jdoc'] = "";
					$_POST['percentage'] = "";
				}
				if($_POST['doc'] == "Sans"){
					$_POST['attachments'] = "";
					$_POST['datedoc'] = "";
					$_POST['jdoc'] = "";
				}
				$datedoc = "";
				if($_POST['datedoc'] != ""){
					$datedoc = getDateNow($_POST['datedoc']);
				}
				$back = $bdd->query("SELECT id FROM regularity WHERE supplier='".$_POST['supplier']."'");
				if($back->rowCount() == 0){
					$req = $bdd->prepare("INSERT INTO `regularity`(`id`, `supplier`, `state`, `type`, `appras`, `doc`, `attachments`, `datedoc`, `jdoc`, `percentage`) 
					VALUES ('','".$_POST['supplier']."','".$_POST['status']."','".$_POST['type']."','".$_POST['appras']."','".$_POST['doc']."','".$_POST['attachments']."','".$datedoc."','".$_POST['jdoc']."','".$_POST['percentage']."')");
					$req->execute();
				}
				else{
					$req = $bdd->prepare("UPDATE `regularity` SET state='".$_POST['status']."',
																	type='".$_POST['type']."',
																	appras='".$_POST['appras']."',
																	doc='".$_POST['doc']."',
																	attachments='".$_POST['attachments']."',
																	datedoc='".$datedoc."',
																	jdoc='".$_POST['jdoc']."',
																	percentage='".$_POST['percentage']."' WHERE supplier='".$_POST['supplier']."'");
					$req->execute();
				}
			}
			
			if($_POST['action'] == "loadsuppliers"){
				if(preg_match("#CA Fournisseurs#",$_SESSION['easybm_roles'])){
					$req = "SELECT SUM(ca) AS ca,SUM(payments) AS payments FROM suppliers s WHERE trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' OR codefo LIKE '".normalizeString($_POST['keyword'])."' OR respemail LIKE '".normalizeString($_POST['keyword'])."' OR note LIKE '".normalizeString($_POST['keyword'])."' OR title LIKE '".normalizeString($_POST['keyword'])."' OR respname LIKE '".normalizeString($_POST['keyword'])."' OR respphone LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['paid'] == "paid"){
						$req .= " AND ca-payments <= 0";
					}
					elseif($_POST['paid'] == "encours"){
						$req .= " AND ca-payments > 0";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					$back = $bdd->query($req);
					$row = $back->fetch();
					$ca = isset($row['ca'])?$row['ca']:0;
					$paid = isset($row['payments'])?$row['payments']:0;
					$rest = $ca - $paid;			
					$ca1 = $ca!=0?$ca:1;
					?>
				<div class="lx-caisse-total lx-caisse-total1">
					<h3 style="background:#d9534f;">En cours</h3>
					<p><?php echo number_format((float)(isset($rest)?$rest:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$rest*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $rest*100/$ca1?>%;background:#d9534f;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total2">
					<h3 style="background:#5cb85c;">Payé</h3>
					<p><?php echo number_format((float)(isset($paid)?$paid:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$paid*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $paid*100/$ca1?>%;background:#5cb85c;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total3">
					<h3 style="background:#337ab7;">Chiffre d'affaires</h3>
					<p><?php echo number_format((float)(isset($ca)?$ca:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$ca*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $ca*100/$ca1?>%;background:#337ab7;"></ins>
					</h4>
				</div>
				<?php
				}
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Fournisseur <i class="fa fa-sort" data-sort="title"></i></td>
						<td>Responsable <i class="fa fa-sort" data-sort="respname"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Adresse <i class="fa fa-sort" data-sort="address"></i></td>
						<td>ICE <i class="fa fa-sort" data-sort="ice"></i></td>
						<td>IF <i class="fa fa-sort" data-sort="iff"></i></td>
						<td>RC <i class="fa fa-sort" data-sort="rc"></i></td>
						<td>Téléphone <i class="fa fa-sort" data-sort="respphone"></i></td>
						<td>WhatsApp <i class="fa fa-sort" data-sort="whatsapp"></i></td>
						<td>Email <i class="fa fa-sort" data-sort="respemail"></i></td>
						<td>Fax <i class="fa fa-sort" data-sort="respfax"></i></td>
						<td>Délai de paiement</td>
						<td>Régularité fiscale</td>
						<td>Comptes banquaires</td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<?php
						if(preg_match("#CA Fournisseurs#",$_SESSION['easybm_roles'])){
						?>
						<td>Chiffre d'affaires <i class="fa fa-sort" data-sort="ca"></i></td>
						<td>Payé <i class="fa fa-sort" data-sort="paid"></i></td>
						<td>En cours <i class="fa fa-sort" data-sort="encours"></i></td>
						<?php
						}
						?>
						<td>Utilisateur</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM suppliers s WHERE trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' OR codefo LIKE '".normalizeString($_POST['keyword'])."' OR respemail LIKE '".normalizeString($_POST['keyword'])."' OR note LIKE '".normalizeString($_POST['keyword'])."' OR title LIKE '".normalizeString($_POST['keyword'])."' OR respname LIKE '".normalizeString($_POST['keyword'])."' OR respphone LIKE '".normalizeString($_POST['keyword'])."' OR whatsapp LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['paid'] == "paid"){
						$req .= " AND ca-payments <= 0";
					}
					elseif($_POST['paid'] == "encours"){
						$req .= " AND ca-payments > 0";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "ca"){
							$req .= " ORDER BY ca";
						}
						elseif($_POST['sortby'] == "paid"){
							$req .= " ORDER BY payments";
						}
						elseif($_POST['sortby'] == "encours"){
							$req .= " ORDER BY ca-payments";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$ca = 0;
					$paid = 0;
					$rest = 0;
					$back = $bdd->query($req);
					while($row = $back->fetch()){
						?>
					<tr>
						<td><label><input type="checkbox" name="supplier" value="<?php echo $row['id'];?>" /><del class="checkmark"></del></label></td>
						<td>
							<span style="white-space:nowrap;"><?php echo $row['code'];?></span>
							<?php
							if($row['codefo'] != ""){
							?>
							<br />
							Code fournisseur:
							<br />
							<span style="white-space:nowrap;font-weight:500;"><?php echo $row['codefo'];?></span>
							<?php
							}
							?>
						</td>
						<td><span><?php echo $row['title'];?></span></td>
						<td><span><?php echo $row['respname'];?></span></td>
						<td style="white-space:nowrap;">
							<?php
							$companytxt = "";
							$back1 = $bdd->query("SELECT rs FROM companies WHERE id IN(".($row['company']!=""?$row['company']:0).")".$companiesid);
							while($company = $back1->fetch()){
								$companytxt .= ",".$company['rs'];
								?>
							<span><?php echo $company['rs'];?></span>
								<?php
							}
							$companytxt = substr($companytxt,1);
							?>
						</td>							
						<td><span><?php echo $row['address'];?></span></td>
						<td><span><?php echo $row['ice'];?></span></td>
						<td><span><?php echo $row['iff'];?></span></td>
						<td><span><?php echo $row['rc'];?></span></td>
						<td><span><?php echo $row['respphone'];?></span></td>
						<td><span><?php echo $row['whatsapp'];?></span></td>
						<td><span><?php echo $row['respemail'];?></span></td>
						<td><span><?php echo $row['respfax'];?></span></td>
						<td>
							<?php
							if($row['delay'] != ""){
								?>
							<span>Délai: <strong><?php echo $row['delay'];?> Jours</strong> <a href="javascript:;" class="lx-edit-delay lx-open-popup" data-header="Modifier délai"
								data-id="<?php echo $row['id'];?>"
								data-delay="<?php echo $row['delay'];?>"
								data-table="suppliers"
								data-title="delay" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a></span>
								<?php
							}
							?>
							<br />
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="suppliers" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files" style="width:200px;">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="suppliers" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-paperclip"></i> Convention</a>
							</div>
						</td>
						<td style="white-space:nowrap;">
							<?php
							$back1 = $bdd->query("SELECT * FROM regularity WHERE supplier='".$row['id']."'");
							$row1 = $back1->fetch();
							if(isset($row1['state'])){
							?>
							<span>Statut: <b><?php echo $row1['state'];?></b></span>
							<?php
							}
							if(isset($row1['type'])){
								if($row1['type'] != ""){
							?>
							<span>Type d'activité: <b><?php echo $row1['type'];?></b></span>
							<?php
								}
							}
							if(isset($row1['state'])){
								if($row1['state'] != ""){
							?>
							<span>RAS applicable: <b><?php echo $row1['appras'];?></b></span>
							<?php
								}
							}
							if(isset($row1['doc'])){
								if($row1['doc'] != ""){
								?>
							<span>Régularité fiscale: <b><?php echo $row1['doc'];?></b></span>
								<?php
								}
								if($row1['doc'] == "Avec"){
								?>
							<fieldset style="margin:5px 0px;padding:10px;padding-top:5px;">
								<legend><b>Document régularité fiscale:</b></legend>
									<?php
									if(isset($row1['attachments'])){
										if($row1['attachments'] != ""){
									?>
								<span><b><a href="uploads/<?php echo $row1['attachments'];?>" target="_blank" style="color:#39add1;"><i class="fa fa-download"></i> Téléchager</a></b></span>
									<?php
										}
									}
									if(isset($row1['datedoc'])){
										if($row1['datedoc'] != ""){
									?>
								<span>Date demande: <b><?php echo date("d/m/Y",$row1['datedoc']);?></b></span>
									<?php
										}
									}
									if(isset($row1['jdoc'])){
										if($row1['jdoc'] != ""){
									?>
								<span>Période de validité en jours: <b><?php echo $row1['jdoc'];?> jours</b></span>
									<?php
										}
									}
								}
								?>
							</fieldset>
								<?php
							}
							if(isset($row1['percentage'])){
								if($row1['percentage'] != ""){
								?>
							<span>% TVA à retenir: <b><?php echo $row1['percentage'];?>%</b></span>
								<?php
								}
							}
							if($back1->rowCount() > 0){
							?>
							<br />
							<?php
							}
							?>
							<div class="lx-upload-photos">
								<a href="javascript:;" class="lx-edit-regularity lx-open-popup" data-header="Mise a jour"
								data-supplier="<?php echo $row['id'];?>"
								data-status="<?php echo isset($row1['state'])?$row1['state']:"";?>"
								data-type="<?php echo isset($row1['type'])?$row1['type']:"";?>"
								data-appras="<?php echo isset($row1['appras'])?$row1['appras']:"";?>"
								data-doc="<?php echo isset($row1['doc'])?$row1['doc']:"";?>"
								data-attachments="<?php echo isset($row1['attachments'])?$row1['attachments']:"";?>"
								data-datedoc="<?php echo isset($row1['datedoc'])?($row1['datedoc']!=""?date("d/m/Y",$row1['datedoc']):""):"";?>"
								data-jdoc="<?php echo isset($row1['jdoc'])?$row1['jdoc']:"";?>"
								data-percentage="<?php echo isset($row1['percentage'])?$row1['percentage']:"";?>"
								data-title="regularity"><i class="fa fa-edit"></i> Régularité fiscale</a>
							</div>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM suppliersbankaccounts WHERE supplier='".$row['id']."'");
							while($row1 = $back1->fetch()){
								?>
							<div class="lx-back-acocunt" style="white-space:nowrap;margin-bottom:10px;padding:10px;background:#fcfdec;border:1px solid #242424;border-radius:6px;">
								<?php
								if($row1['location'] == "in"){
									?>
								<span>RIB: <b><?php echo $row1['rib'];?></b></span>
								<span>La banque: <b><?php echo $row1['bank'];?></b></span>
								<span>Agence: <b><?php echo $row1['agency'];?></b></span>
									<?php
								}
								elseif($row1['location'] == "out"){
									?>
								<span>IBAN: <b><?php echo $row1['iban'];?></b></span>
								<span>SWIFT: <b><?php echo $row1['swift'];?></b></span>
								<span>Adresse banque: <b><?php echo $row1['address'];?></b></span>
								<span>La banque: <b><?php echo $row1['bank'];?></b></span>
									<?php
								}
								?>
								<p style="text-align:right;padding:0px;margin:0px;">
								<a href="javascript:;" class="lx-edit-supplierbankaccount lx-open-popup" data-header="Modifier un" title="Modifier"
								data-id="<?php echo $row1['id'];?>"
								data-location="<?php echo $row1['location'];?>"
								data-rib="<?php echo $row1['rib'];?>"
								data-iban="<?php echo $row1['iban'];?>"
								data-swift="<?php echo $row1['swift'];?>"
								data-address="<?php echo $row1['address'];?>"
								data-bank="<?php echo $row1['bank'];?>"
								data-agency="<?php echo $row1['agency'];?>"
								data-supplier="<?php echo $row['id'];?>"
								data-title="supplierbankaccount"><i class="fa fa-edit"></i></a>
								<a href="javascript:;" class="lx-delete lx-delete-supplierbankaccount lx-open-popup" data-title="deleterecord1" title="Supprimer" style="position:relative;top:2px;font-size:16px;" 
								data-id="<?php echo $row1['id'];?>"><i class="fa fa-times"></i></a>
								</p>
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<a href="javascript:;" class="lx-new-supplierbankaccount lx-open-popup" data-header="Ajouter un nouveau"
								data-id="0"
								data-supplier="<?php echo $row['id'];?>"
								data-title="supplierbankaccount">Ajouter compte</a>
							</div>
						</td>
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<?php
						if(preg_match("#CA Fournisseurs#",$_SESSION['easybm_roles'])){
						?>
						<td><span style="white-space:nowrap;text-align:right;"><?php echo number_format((float)($row['ca']!=""?$row['ca']:"0"),2,'.',' ');$ca+=$row['ca'];?> <?php echo $settings['currency'];?> TTC</span></td>
						<td><span style="white-space:nowrap;text-align:right;"><?php echo number_format((float)($row['payments']),2,'.',' ');$paid+=$row['payments'];?> <?php echo $settings['currency'];?>  TTC</span></td>
						<td><span style="white-space:nowrap;text-align:right;"><?php echo number_format((float)($row['ca'] - $row['payments']),2,'.',' ');$rest+=($row['ca'] - $row['payments']);?> <?php echo $settings['currency'];?>  TTC</span></td>
						<?php
						}
						?>
						<td>
							<?php
							$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
							if($back1->rowCount() > 0){
							$user = $back1->fetch();
							?>
							<span><?php echo $user['fullname'];?></span>
							<?php
							}
							?>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<?php
								if(preg_match("#Modifier Fournisseurs#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit lx-edit-supplier lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-codefo="<?php echo $row['codefo'];?>"
								data-company="<?php echo $row['company'];?>"
								data-companytxt="<?php echo $companytxt;?>"
								data-titl="<?php echo $row['title'];?>"
								data-respname="<?php echo $row['respname'];?>"
								data-respphone="<?php echo $row['respphone'];?>"
								data-whatsapp="<?php echo $row['whatsapp'];?>"
								data-respemail="<?php echo $row['respemail'];?>" 
								data-respfax="<?php echo $row['respfax'];?>"
								data-ice="<?php echo $row['ice'];?>"
								data-iff="<?php echo $row['iff'];?>"
								data-rc="<?php echo $row['rc'];?>"
								data-address="<?php echo $row['address'];?>"
								data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="supplier">Modifier</a>
								<?php
								}
								if(preg_match("#Supprimer Fournisseurs#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-supplier lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Fournisseur <i class="fa fa-sort" data-sort="title"></i></td>
						<td>Responsable <i class="fa fa-sort" data-sort="respname"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Adresse <i class="fa fa-sort" data-sort="address"></i></td>
						<td>ICE <i class="fa fa-sort" data-sort="ice"></i></td>
						<td>IF <i class="fa fa-sort" data-sort="iff"></i></td>
						<td>RC <i class="fa fa-sort" data-sort="rc"></i></td>
						<td>Téléphone <i class="fa fa-sort" data-sort="respphone"></i></td>
						<td>WhatsApp <i class="fa fa-sort" data-sort="whatsapp"></i></td>
						<td>Email <i class="fa fa-sort" data-sort="respemail"></i></td>
						<td>Fax <i class="fa fa-sort" data-sort="respfax"></i></td>
						<td>Délai de paiement</td>
						<td>Régularité fiscale</td>
						<td>Comptes banquaires</td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<?php
						if(preg_match("#CA Fournisseurs#",$_SESSION['easybm_roles'])){
						?>
						<td style="text-align:right;"><?php echo number_format((float)($ca),2,'.',' ');?> <?php echo $settings['currency'];?> TTC</td>
						<td style="text-align:right;"><?php echo number_format((float)($paid),2,'.',' ');?> <?php echo $settings['currency'];?> TTC</td>
						<td style="text-align:right;"><?php echo number_format((float)abs($rest),2,'.',' ');?> <?php echo $settings['currency'];?> TTC</td>
						<?php
						}
						?>
						<td>Utilisateur</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> fournisseur(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> fournisseur(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}

			if($_POST['action'] == "addfacturesupplier"){
				$back = $bdd->query("SELECT delay FROM suppliers WHERE id='".$_POST['client']."'");
				$row = $back->fetch();
				$dupsupplier = 0;
				if($_POST['exist'] == "1"){
					$req = $bdd->prepare("UPDATE suppliers SET ice='".sanitize_vars($_POST['ice'])."',respphone='".sanitize_vars($_POST['phone'])."',address='".sanitize_vars($_POST['address'])."',respemail='".sanitize_vars($_POST['email'])."' WHERE id='".$_POST['client']."'");
					$req->execute();
				}
				else{
					if($_POST['fullname'] != ""){
						$back = $bdd->query("SELECT id FROM suppliers WHERE (title='".sanitize_vars($_POST['fullname'])."')");
						if($back->rowCount() == 0){
							$rand = "FR".date("ym")."-0001";
							$back1 = $bdd->query("SELECT code FROM suppliers ORDER BY id DESC LIMIT 0,1");
							if($back1->rowCount() > 0){
								$row1 = $back1->fetch();
								$rand = "FR".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
							}
							$req = $bdd->prepare("INSERT INTO suppliers(id,code,title,respname,respphone,respemail,respfax,ice,address,note,company,dateadd,trash) 
							VALUES ('0','".$rand."','".sanitize_vars($_POST['fullname'])."','','".sanitize_vars($_POST['phone'])."','".sanitize_vars($_POST['email'])."','','".sanitize_vars($_POST['ice'])."','".sanitize_vars($_POST['address'])."','','".str_replace("||",",",$_POST['company'])."','".time()."','1')");
							$req->execute();
							$back = $bdd->query("SELECT id FROM suppliers WHERE ice='".sanitize_vars($_POST['ice'])."' AND respphone='".sanitize_vars($_POST['phone'])."' ORDER BY id DESC LIMIT 0,1");
							$row1 = $back->fetch();
							$_POST['client'] = $row1['id'];
						}
						else{
							$dupsupplier = 1;
							echo "Fournisseur existe déjà!";
						}
					}
				}
				if($dupsupplier == 0){
					$supplier = $_POST['client'];	
					if((int)$supplier > 0){
						$back = $bdd->query("SELECT id,supplier FROM facturesuppliers WHERE id IN('".$_POST['id']."')");
						if($back->rowCount() > 0){
							$rowcom = $back->fetch();
						}
						else{
							$rowcom['client'] = 0;
							$rowcom['supplier'] = 0;
						}
						$price2 = "";
						$tva2 = "";
						$currency = $settings['currency'];
						if($_POST['type'] == "1"){
							$price2 = $_POST['price2'];
							$tva2 = $_POST['tva2'];
							$currency = $_POST['currency'];
						}
						$dateadd = getDateNow($_POST['dateadd']);
						if($_POST['id'] == "0"){
							$req = $bdd->prepare("INSERT INTO `facturesuppliers`(`id`, `company`, `code`, `supplier`, `refbc`, `price`, `tva`, `price2`, `tva2`, `currency`, `note`, `delay`, `attachments`, `user`, `dateadd`, `trash`) 
							VALUES ('0','".str_replace("||",",",$_POST['company'])."','".sanitize_vars($_POST['code'])."','".sanitize_vars($supplier)."','".sanitize_vars($_POST['refbc'])."','".sanitize_vars($_POST['price'])."','".sanitize_vars($_POST['tva'])."','".sanitize_vars((float)$_POST['price2'])."','".sanitize_vars((float)$_POST['tva2'])."','".sanitize_vars($_POST['currency'])."','".addslashes($_POST['note'])."','".addslashes(isset($row['delay'])?$row['delay']:60)."','','".$_SESSION['easybm_id']."','".$dateadd."','1')");
							$req->execute();
							$back = $bdd->query("SELECT id FROM facturesuppliers WHERE code='".sanitize_vars($_POST['code'])."' ORDER BY id DESC");
							$row = $back->fetch();
							$_POST['id'] = $row['id'];
						}
						else{
							$req = $bdd->prepare("UPDATE facturesuppliers SET code='".sanitize_vars($_POST['code'])."',
																				supplier='".sanitize_vars($supplier)."',
																				refbc='".sanitize_vars($_POST['refbc'])."',
																				price='".sanitize_vars($_POST['price'])."',
																				tva='".sanitize_vars($_POST['tva'])."',
																				price2='".$price2."',
																				tva2='".$tva2."',
																				currency='".$currency."',
																				note='".addslashes($_POST['note'])."',
																				company='".str_replace("||",",",$_POST['company'])."',
																				dateadd='".$dateadd."' WHERE id='".$_POST['id']."'");
							$req->execute();
							$req = $bdd->prepare("UPDATE payments SET supplier='".sanitize_vars($supplier)."',currency='".$currency."' WHERE doc='".$_POST['id']."' AND typedoc='facturesupplier'");
							$req->execute();
						}
						updatePrevDocKPI(0,$rowcom['supplier']);
						updateFSKPI($_POST['id']);
					}
					else{
						echo "Choisissez ou saisissez un fournisseur";
					}
				}
			}

			if($_POST['action'] == "adddatepaid"){
				$datapaid = getDateNow($_POST['datepaid']);
				$datedue = strtotime(date('20-m-Y', strtotime('first day of next month',$datapaid)));
				$req = $bdd->prepare("UPDATE ".$_POST['table']." SET datepaid='".$datapaid."',datedue='".$datedue."' WHERE id='".$_POST['id']."'");
				$req->execute();
			}

			if($_POST['action'] == "addretard"){
				$dateretard = "";
				if($_POST['retardpaid'] == "Régularisé"){
					$dateretard = getDateNow($_POST['dateretard']);
				}
				$req = $bdd->prepare("UPDATE facturesuppliers SET retardpaid='".$_POST['retardpaid']."',dateretard='".$dateretard."' WHERE id='".$_POST['id']."'");
				$req->execute();
			}

			if($_POST['action'] == "deletefacturesupplier"){
				$back = $bdd->query("SELECT attachments,attachmentras,supplier FROM facturesuppliers WHERE id='".$_POST['id']."'");
				$row = $back->fetch();

				$req = $bdd->prepare("DELETE FROM payments WHERE doc='".$_POST['id']."' AND typedoc='facturesupplier'");
				$req->execute();
				
				$attachments = explode(",",$row['attachments']);
				for($i=1;$i<count($attachments);$i++){
					if(file_exists("uploads/".$attachments[$i])){
						unlink("uploads/".$attachments[$i]);
					}
				}
				$attachments = explode(",",$row['attachmentras']);
				for($i=0;$i<count($attachments);$i++){
					if(file_exists("uploads/".$attachments[$i])){
						unlink("uploads/".$attachments[$i]);
					}
				}
								
				$req = $bdd->prepare("DELETE FROM facturesuppliers WHERE id='".$_POST['id']."'");
				$req->execute();
				updatePrevDocKPI(0,$row['supplier']);
			}
			
			if($_POST['action'] == "restorefacturesupplier"){
				$req = $bdd->prepare("UPDATE facturesuppliers SET trash='1' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "deletefacturesupplierpermanently"){
				$req = $bdd->prepare("DELETE FROM facturesuppliers WHERE id='".$_POST['id']."'");
				$req->execute();
			}

			if($_POST['action'] == "addpaymentras"){
				$datepaidras = "";
				$attachmentras = "";
				if($_POST['stateras'] == "Payée"){
					$datepaidras = getDateNow($_POST['datepaidras']);
					$attachmentras = $_POST['attachmentras'];
				}
				else{
					$attachments = explode(",",$_POST['attachmentras']);
					for($i=0;$i<count($attachments);$i++){
						if(file_exists("uploads/".$attachments[$i])){
							unlink("uploads/".$attachments[$i]);
						}
					}
				}
				
				if($_POST['page'] == "fc"){
					$req = $bdd->prepare("UPDATE documents SET stateras='".$_POST['stateras']."',datepaidras='".$datepaidras."',attachmentras='".$attachmentras."' WHERE id='".$_POST['id']."'");
				}
				else{
					$req = $bdd->prepare("UPDATE facturesuppliers SET stateras='".$_POST['stateras']."',datepaidras='".$datepaidras."',attachmentras='".$attachmentras."' WHERE id='".$_POST['id']."'");
				}
				$req->execute();
			}
			
			if($_POST['action'] == "loadfacturesuppliers"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Numéro <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Fournisseur <i class="fa fa-sort" data-sort="supplier"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td>Réf. commande <i class="fa fa-sort" data-sort="refbc"></i></td>
						<td>Montant total <i class="fa fa-sort" data-sort="price"></i></td>
						<td>Délai de paiement <i class="fa fa-sort" data-sort="delay"></i></td>
						<td>Statut et historique des paiements <i class="fa fa-sort" data-sort="rest"></i></td>
						<td>RAS TVA <i class="fa fa-sort" data-sort="delayras"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachement</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM facturesuppliers cl WHERE trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' OR refbc LIKE '".normalizeString($_POST['keyword'])."' OR note LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['supplier'] != ""){
						$req .= " AND supplier IN(".str_replace("||",",",$_POST['supplier']).")";
					}
					if($_POST['type'] != ""){
						if($_POST['type'] == "1"){
							$req .= " AND currency NOT IN('DH','MAD','')";
						}
						else{
							$req .= " AND currency IN('DH','MAD','')";
						}
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['retard'] != ""){
						$req .= " AND retard > 0";
						if($_POST['retard'] == "Régularisé"){
							$req .= " AND retardpaid='Régularisé'";
						}
						elseif($_POST['retard'] == "Non régularisé"){
							$req .= " AND (retardpaid='Non Régularisé' OR retardpaid='')";
						}
					}
					if($_POST['stateras'] != ""){
						if($_POST['stateras'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND stateras NOT IN('Payée') AND ".time()." > datedue AND rastva > 0";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['stateras'])){
								$req .= " AND (stateras IN ('".str_replace("||","','",$_POST['stateras'])."') OR (stateras NOT IN('Payée') AND ".time()." > datedue)) AND rastva > 0";
							}
							else{
								$req .= " AND stateras IN ('".str_replace("||","','",$_POST['stateras'])."') AND rastva > 0";
							}
						}
					}
					if($_POST['statee'] != ""){
						if($_POST['statee'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['statee'])){
								$req .= " AND (state IN ('".str_replace("||","','",$_POST['statee'])."') OR (state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")))";
							}
							else{
								$req .= " AND state IN ('".str_replace("||","','",$_POST['statee'])."')";
							}
						}
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND ((price+(tva-rastva)) BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimit'] != "" AND $_POST['dateendlimit'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimit']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimit'])) + (60*60*24) - 1;
						$req .= " AND ((dateadd+(delay*60*60*24)) BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimitras'] != "" AND $_POST['dateendlimitras'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimitras']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimitras'])) + (60*60*24) - 1;
						$req .= " AND (datedue BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "delay"){
							$req .= " ORDER BY (dateadd + (delay * 60 * 60 * 24))";
						}
						elseif($_POST['sortby'] == "delayras"){
							$req .= " ORDER BY (datepaid + ".secondsUntilNext20th().")";
						}
						elseif($_POST['sortby'] == "rest"){
							$req .= " ORDER BY ((price + (tva-rastva)) - payments)";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$ca = 0;
					$paid = 0;
					$rest = 0;
					$back = $bdd->query($req);
					while($row = $back->fetch()){
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];

						$back1 = $bdd->query("SELECT * FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facturesupplier" value="<?php echo $row['id'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y",$row['dateadd']);?></span></td>						
						<td><span><?php echo $row['code'];?></span></td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM suppliers WHERE id='".$row['supplier']."'");
							$supplier = $back1->fetch();
							$suppliertxt = $supplier['title'];
							?>
							<span style="white-space:nowrap;"><?php echo $supplier['title']!=""?$supplier['title']." (".($supplier['codefo']!=""?$supplier['codefo']:$supplier['code']).")":"";?></span>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td><span><?php echo $row['refbc'];?></span></td>
						<td>
							<?php
							$smpaid = $row['payments'];
							$smpaid2 = $row['devispayments'];
							if($row['currency'] != "DH" AND $row['currency'] != "MAD" AND $row['currency'] != ""){
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price2'],2,"."," ");?> <?php echo $row['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['tva2'],2,"."," ");?> <?php echo $row['currency'];?></b></span>
							<span style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px dashed #BEBEBE;"><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price2']+$row['tva2']),2,"."," ");?> <?php echo $row['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['tva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] != "0"){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+($row['tva']-$row['rastva'])),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
							<?php
							$phtable = "";
							
							$bankaccounts = "<option value=''>Choisissez un compte banquaire</option>";
							$back1 = $bdd->query("SELECT * FROM bankaccounts WHERE company='".$row['company']."'");
							while($row1 = $back1->fetch()){
								$bankaccounts .= "<option value='".$row1['id']."'>".$row1['bank']." | ".$row1['rib']."</option>";
							}
							
							$supplierbankaccounts = "<option value=''>Choisissez un compte fournisseur</option>";
							$back1 = $bdd->query("SELECT * FROM suppliersbankaccounts WHERE supplier='".$row['supplier']."'");
							while($row1 = $back1->fetch()){
								if($row1['location'] == "in"){
									$supplierbankaccounts .= "<option value='".$row1['id']."' data-location='in'>".$row1['bank']." | ".$row1['rib']."</option>";
								}
								if($row1['location'] == "out"){
									$supplierbankaccounts .= "<option value='".$row1['id']."' data-location='out'>".$row1['bank']." | ".$row1['iban']." | ".$row1['swift']."</option>";
								}
							}
							
							// Limits
							$datelimit = time();
							if($row['datepaid'] != ""){
								$datelimit = $row['datepaid'];
							}
							$timetodelay = $row['dateadd']+($row['delay']*24*60*60);
							$resttodelay = ($row['dateadd']+($row['delay']*24*60*60) - $datelimit) / 86400;
							$resttodelay = date_diff(date_create(date("Y-m-d",$timetodelay)),date_create(date("Y-m-d")));
							$color = "#71b44c";
							$background = "transparent";
							$days = $resttodelay->format("%a");
							if($datelimit > $timetodelay){
								$days *= (-1);
							}
							if($days < 0){
								$color = "#FF0000";
							}
							else{
								$days = "+".$days;
							}
							
							// Paid
							if($row['currency'] != "DH" AND $row['currency'] != "MAD" AND $row['currency'] != ""){
								if((($row['price2']+($row['tva2']))-$smpaid) == ($row['price2']+($row['tva2']))){
									$row['datepaid'] = '';
								}
								elseif((($row['price2']+($row['tva2']))-$smpaid) > 0){
									$row['datepaid'] = '';
								}
							}
							else{
								if((($row['price']+($row['tva']-$row['rastva']))-$smpaid) == ($row['price']+($row['tva']-$row['rastva']))){
									$row['datepaid'] = '';
								}
								elseif((($row['price']+($row['tva']-$row['rastva']))-$smpaid) > 0){
									$row['datepaid'] = '';
								}
								
							}
							
							$retard = 0;
							if($row['datepaid'] != ""){
								$retard = date_diff(date_create(date("Y-m-d",$timetodelay)),date_create(date("Y-m-d",$row['datepaid'])));
								$retard = $retard->format("%a");
								$req = $bdd->prepare("UPDATE facturesuppliers SET retard='".$retard."' WHERE id='".$row['id']."'");
								$req->execute();
							}
							
							if($row['state'] == 'Payée' AND $row['datepaid'] == ""){
								$row['datepaid'] = time();
								$retard = date_diff(date_create(date("Y-m-d",$timetodelay)),date_create(date("Y-m-d",$row['datepaid'])));
								$retard = $retard->format("%a");
								$datedue = strtotime(date('20-m-Y', strtotime('first day of next month',$row['datepaid'])));
								$req = $bdd->prepare("UPDATE facturesuppliers SET state='".$row['state']."',datepaid='".time()."',datedue='".$datedue."',retard='".$retard."',retardpaid='',dateretard='' WHERE id='".$row['id']."'");
								$req->execute();
								$row['datepaid'] = time();
							}
							
							if($days < 0 AND $row['state'] != "Payée"){
								$background = "#FFC966";
							}
							?>
						<td style="white-space:nowrap;background:<?php echo $background;?>;">
							<span>Délai: <b><?php echo $row['delay'];?> jours</b>
								<?php
								if(preg_match("#Mise à jour délai de paiement factures fournisseurs#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit-delay lx-open-popup" data-header="Modifier délai"
								data-id="<?php echo $row['id'];?>"
								data-delay="<?php echo $row['delay'];?>"
								data-table="facturesuppliers"
								data-title="delay" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a>
								<?php
								}
								?>
							</span>
							<span>Date d'échéance: <br /><b><?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?></b> <strong style="<?php echo $row['state']=="Payée"?"display:none;":"";?>color:<?php echo $color;?>;font-weight:bold;">(<?php echo $days;?>)</strong></span>
						</td> 
						<td>
							<span style="display:inline-block;padding:2px 5px;font-weight:500;background:<?php echo $row['state']=='Payée'?"#71b44c":($row['state']=='Non payée'?"#CC0000":"orange");?>;color:#FFFFFF;border-radius:4px;cursor:pointer;" 
								data-id="<?php echo $row['id'];?>" 
								data-rastva="<?php echo $row['rastva'];?>" 
								data-paid="<?php echo $row['state'];?>" 
								data-rest="<?php echo number_format((float)abs(($row['price']+($row['tva']-$row['rastva']))-$smpaid),2,".","");?>" 
								data-price="<?php echo number_format((float)abs(($row['price']+($row['tva']))),2,".","");?>" 
								data-rest2="<?php echo number_format((float)abs(($row['price2']+($row['tva2']))-$smpaid2),2,".","");?>" 
								data-price2="<?php echo number_format((float)abs(($row['price2']+($row['tva2']))),2,".","");?>" 
								data-currency="<?php echo $row['currency'];?>" 
								data-company="<?php echo $companytxt;?>" 
								data-supplier="<?php echo $suppliertxt;?>" 
								data-supplierid="<?php echo $row['supplier'];?>" 
								data-bankaccounts="<?php echo $bankaccounts;?>" 
								data-supplierbankaccount="<?php echo $supplierbankaccounts;?>" 
								data-table="<?php echo str_replace("\"","'",$phtable);?>" class="<?php echo preg_match("#Mise à jour paiement factures fournisseurs#",$_SESSION['easybm_roles'])?"lx-edit-payment lx-open-popup":"";?>" 
								data-title="payment"><?php echo $row['state'];?></span>
							<?php
							if($row['currency'] != "DH" AND $row['currency'] != "MAD" AND $row['currency'] != ""){
							?>
							<span style="<?php echo (($row['price2'] + $row['tva2']) - $row['devispayments']) == 0?"display:none;":"";?>">Reste en <?php echo $row['currency'];?>: <b><?php echo number_format((float)abs(($row['price2']+($row['tva2']))-$smpaid2),2,"."," ");?></b></span>
							<?php
							}
							?>
							<span style="<?php echo (($row['price'] + ($row['tva'] - $row['rastva'])) - $row['payments']) == 0?"display:none;":"";?>">Reste en DH: <b><?php echo number_format((float)abs(($row['price']+($row['tva']-$row['rastva']))-$smpaid),2,"."," ");?></b></span>
							<?php
							if($row['datepaid'] != ""){
							?>
							<span>Payé le: <b><?php echo date("d/m/Y",$row['datepaid']);?></b>
								<?php
								if(preg_match("#Mise à jour paiement factures fournisseurs#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit-datepaid lx-open-popup" data-header="Modifier délai"
								data-id="<?php echo $row['id'];?>"
								data-datepaid="<?php echo date("d/m/Y",$row['datepaid']);?>"
								data-table="facturesuppliers"
								data-title="datepaid" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a>
								<?php
								}
								?>
							</span>
							<?php 
							if($retard != 0 AND ($timetodelay < $row['datepaid'])){
							?>
							<br />
							<span style="color:#FF0000;font-weight:bold;">Avec <?php echo $retard;?> jours de retard
								<?php
								if(preg_match("#Mise à jour paiement factures fournisseurs#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit-retard lx-open-popup" data-header="Modifier délai"
								data-id="<?php echo $row['id'];?>"
								data-retardpaid="<?php echo $row['retardpaid'];?>"
								data-dateretard="<?php echo $row['dateretard']!=""?date("d/m/Y",$row['dateretard']):'';?>"
								data-title="retard" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a>
								<?php
								}
								?>
							</span>
								<?php
								if($row['retardpaid'] == "Régularisé"){
									?>
							<span style="color:#71b44c;font-weight:bold;">Régularisé le <?php echo date("d/m/Y",$row['dateretard']);?></span>
							<br />
									<?php
									if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
									?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fs"
									data-button="Rappel encaissement" style="display:block;"><i class="fa fa-eye"></i> Rappel encaissement</a>
							</div>
									<?php
									}
								}
							}
							}
							if($row['retardpaid'] != "Régularisé" AND $row['state'] == 'Payée' AND preg_match("#Mise à jour paiement factures fournisseurs#",$_SESSION['easybm_roles'])){
							?>
							<br />
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-function="<?php echo $user['job'];?>"
									data-client="<?php echo $supplier['title'];?>"
									data-email1="<?php echo $supplier['respemail'];?>"
									data-email="<?php echo $supplier['respemail'];?>"
									data-phone="<?php echo $supplier['respphone'];?>"
									data-whatsapp="<?php echo $supplier['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Facture fournisseur"
									data-numero_document="<?php echo $row['code'];?>"
									data-ref_commande="<?php echo $row['refbc'];?>"
									data-montant="<?php echo $row['price']+$row['tva'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Rappel encaissement"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Rappel encaissement</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fs"
									data-button="Rappel encaissement" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
							<?php
							}
							?>
						</td>
						<td style="white-space:nowrap;">
							<?php
							if($row['rastva'] > 0 AND $row['datepaid'] != ""){
									?>
							<span style="display:inline-block;padding:2px 5px;font-weight:500;background:<?php echo $row['stateras']=='Payée'?"#71b44c":($row['stateras']=='Non payée'?"#CC0000":"CC0000");?>;color:#FFFFFF;border-radius:4px;cursor:pointer;" 
								data-id="<?php echo $row['id'];?>" 
								data-stateras="<?php echo $row['stateras'];?>" 
								data-datepaidras="<?php echo $row['datepaidras']!=""?date("d/m/Y",$row['datepaidras']):"";?>" 
								data-attachmentras="<?php echo $row['attachmentras'];?>"
								class="<?php echo preg_match("#Mise à jour RAS TVA factures fournisseurs#",$_SESSION['easybm_roles'])?"lx-edit-payment-ras lx-open-popup":"";?>" 
								data-title="rastva"><?php echo $row['stateras'];?></span>
								<?php
								if($row['stateras'] == "Non payée"){
								?>
							<span>RAS TAV: <b style="color:#FF0000;"><?php echo $row['rastva'];?> <?php echo $settings['currency'];?></b></span>
								<?php
								}
								?>
							<span>Date d'écheance: <b><?php echo date('20/m/Y', strtotime('first day of next month',$row['datepaid']));?></b></span>
								<?php
								if($row['datepaidras'] != ""){
									$date1 = strtotime(date('20-m-Y', strtotime('first day of next month',$row['datepaid'])));
									$date2 = $row['datepaidras'];
									$resttodelay = date_diff(date_create(date("Y-m-d",$date1)),date_create(date("Y-m-d",$date2)));
									$days = $resttodelay->format("%a");
									?>
							<span>Payé le: <b><?php echo date("d/m/Y",$row['datepaidras']);?></b><a href="javascript:;" 
								data-id="<?php echo $row['id'];?>" 
								data-stateras="<?php echo $row['stateras'];?>" 
								data-datepaidras="<?php echo $row['datepaidras']!=""?date("d/m/Y",$row['datepaidras']):"";?>" 
								data-attachmentras="<?php echo $row['attachmentras'];?>"
								class="<?php echo preg_match("#Mise à jour RAS TVA factures fournisseurs#",$_SESSION['easybm_roles'])?"lx-edit-payment-ras lx-open-popup":"";?>" 
								data-title="rastva" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a></span>
									<?php
									if($date2 > $date1){
										$req = $bdd->prepare("UPDATE facturesuppliers SET retardras='".$days."' WHERE id='".$row['id']."'");
										$req->execute();
										?>
							<span style="color:#FF0000;font-weight:bold;">Avec <?php echo $days;?> jours de retard</span>
										<?php
									}
									if($row['attachmentras'] != ""){
										?>
							<span><b><a href="uploads/<?php echo $row['attachmentras'];?>" target="_blank" style="color:#39add1;"><i class="fa fa-download"></i> Téléchager</a></b></span>
										<?php
									}
								}
							}
							?>
						</td>						
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>	
						<td><span style="white-space:nowrap"><?php echo $user['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="facturesuppliers" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="facturesuppliers" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<?php
								if(preg_match("#Modifier Factures fournisseurs#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit lx-edit-facturesupplier lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-code="<?php echo $row['code'];?>"
								data-company="<?php echo $row['company'];?>"
								data-dateaddcommand="<?php echo date("d/m/Y",$row['dateadd']);?>"
								data-refbc="<?php echo $row['refbc'];?>"
								data-exist="1"
								data-supplier="<?php echo $row['supplier'];?>"
								data-price="<?php echo $row['price'];?>"
								data-tva="<?php echo $row['tva'];?>"
								data-price2="<?php echo $row['price2']!=0?$row['price2']:'';?>"
								data-tva2="<?php echo $row['tva2']!=0?$row['tva2']:'';;?>"
								data-currency="<?php echo $row['currency'];?>"
								data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="command">Modifier</a>
								<?php
								}
								if(preg_match("#Supprimer Factures fournisseurs#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-facturesupplier lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Numéro <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Fournisseur <i class="fa fa-sort" data-sort="supplier"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td>Réf. commande <i class="fa fa-sort" data-sort="refbc"></i></td>
						<td>Montant total <i class="fa fa-sort" data-sort="price"></i></td>
						<td>Délai de paiement <i class="fa fa-sort" data-sort="delay"></i></td>
						<td>Statut et historique des paiements <i class="fa fa-sort" data-sort="rest"></i></td>
						<td>RAS TVA <i class="fa fa-sort" data-sort="delayras"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachement</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<?php
				$req1 = "SELECT COUNT(id) AS nb,SUM(price + (tva - rastva)) AS ca,SUM(payments) AS paid FROM facturesuppliers c WHERE trash='1'".$multicompanies;
				$req = "";
				if($_POST['keyword'] != ""){
					$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' OR refbc LIKE '".normalizeString($_POST['keyword'])."' OR note LIKE '".normalizeString($_POST['keyword'])."')";
				}
				if($_POST['company'] != ""){
					$comp = explode("||",$_POST['company']);
					$req .= " AND (";
					for($j=0;$j<count($comp);$j++){
						if($j==0){
							$req .= "FIND_IN_SET(".$comp[$j].", company)";
						}
						else{
							$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
						}
					}
					$req .= ")";
				}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
				if($_POST['supplier'] != ""){
					$req .= " AND supplier IN(".str_replace("||",",",$_POST['supplier']).")";
				}
				if($_POST['type'] != ""){
					if($_POST['type'] == "1"){
						$req .= " AND currency NOT IN('MAD','DH','')";
					}
					else{
						$req .= " AND currency IN('MAD','DH','')";
					}
				}
				if($_POST['user'] != ""){
					$req .= " AND user IN(".$_POST['user'].")";
				}
				if($_POST['retard'] != ""){
					$req .= " AND retard > 0";
					if($_POST['retard'] == "Régularisé"){
						$req .= " AND retardpaid='Régularisé'";
					}
					elseif($_POST['retard'] == "Non régularisé"){
						$req .= " AND (retardpaid='Non Régularisé' OR retardpaid='')";
					}
				}
				if($_POST['stateras'] != ""){
					if($_POST['stateras'] == "Non payée avec dépassement de délai de paiement"){
						$req .= " AND stateras NOT IN('Payée') AND ".time()." > datedue AND rastva > 0";
					}
					else{
						if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['stateras'])){
							$req .= " AND (stateras IN ('".str_replace("||","','",$_POST['stateras'])."') OR (stateras NOT IN('Payée') AND ".time()." > datedue)) AND rastva > 0";
						}
						else{
							$req .= " AND stateras IN ('".str_replace("||","','",$_POST['stateras'])."') AND rastva > 0";
						}
					}
				}
				if($_POST['statee'] != ""){
					if($_POST['statee'] == "Non payée avec dépassement de délai de paiement"){
						$req .= " AND state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24))";
					}
					else{
						if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['statee'])){
							$req .= " AND (state IN ('".str_replace("||","','",$_POST['statee'])."') OR (state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24))))";
						}
						else{
							$req .= " AND state IN ('".str_replace("||","','",$_POST['statee'])."')";
						}
					}
				}
				if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
					$req .= " AND ((price+(tva-rastva)) BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
				}
				if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
					$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
					$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
					$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
				}
				if($_POST['datestartlimit'] != "" AND $_POST['dateendlimit'] != ""){
					$datestart = strtotime(str_replace("/","-",$_POST['datestartlimit']));
					$dateend = strtotime(str_replace("/","-",$_POST['dateendlimit'])) + (60*60*24) - 1;
					$req .= " AND ((dateadd+(delay*60*60*24)) BETWEEN ".$datestart." AND ".$dateend.")";
				}
				if($_POST['datestartlimitras'] != "" AND $_POST['dateendlimitras'] != ""){
					$datestart = strtotime(str_replace("/","-",$_POST['datestartlimitras']));
					$dateend = strtotime(str_replace("/","-",$_POST['dateendlimitras'])) + (60*60*24) - 1;
					$req .= " AND (datedue BETWEEN ".$datestart." AND ".$dateend.")";
				}	
				if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
					$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
					$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
					$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
				}				
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$ca = $row['ca'];
				$ca1 = $row['ca'];
				$paid = $row['paid'];
				
				$rest = number_format((float)(isset($ca)?$ca:0),2,".","") - number_format((float)(isset($paid)?$paid:0),2,".","");
				if($ca1 == 0){
					$ca1 = 1;
				}
				?>
				<div class="lx-caisse-total lx-caisse-total1">
					<h3 style="background:#d9534f;">En cours</h3>
					<p><?php echo number_format((float)(isset($rest)?$rest:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$rest*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $rest*100/$ca1?>%;background:#d9534f;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total2">
					<h3 style="background:#5cb85c;">Payé</h3>
					<p><?php echo number_format((float)(isset($paid)?$paid:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$paid*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $paid*100/$ca1?>%;background:#5cb85c;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total3">
					<h3 style="background:#337ab7;">Chiffre d'affaires</h3>
					<p><?php echo number_format((float)(isset($ca)?$ca:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$ca*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $ca*100/$ca1?>%;background:#337ab7;"></ins>
					</h4>
				</div>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> facture(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> facture(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
				$req1 = "SELECT SUM(price) AS price,SUM(tva) AS tva,SUM(rastva) AS rastva FROM facturesuppliers c WHERE trash='1'".$multicompanies;
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				?>
				<div class="lx-caisse-total4">
					<div style="float:left;margin-right:60px;">
						<p>* Total Montant HT: <b><?php echo number_format((float)$row['price'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total TVA: <b><?php echo number_format((float)$row['tva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total RAS TVA: <b style="color:#FF0000;"><?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total Montant TTC: <b><?php echo number_format((float)$row['price']+($row['tva']-$row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></p>
					</div>
				</div>
				<div class="lx-clear-fix"><div>
				<?php
			}

			if($_POST['action'] == "addtva"){
				$back = $bdd->query("SELECT id FROM tvas WHERE tva='".sanitize_vars($_POST['tva'])."' AND id NOT IN(".$_POST['id'].")");
				if($back->rowCount() == 0){
					if($_POST['id'] == "0"){
						$req = $bdd->prepare("INSERT INTO tvas(id,tva,trash) VALUES ('0','".sanitize_vars($_POST['tva'])."','1')");
						$req->execute();
					}
					else{
						$req = $bdd->prepare("UPDATE tvas SET tva='".sanitize_vars($_POST['tva'])."' WHERE id='".$_POST['id']."'");
						$req->execute();
					}
				}
				else{
					echo "TVA existe déjà !";
				}
			}

			if($_POST['action'] == "deletetva"){
				$req = $bdd->prepare("DELETE FROM tvas WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "restoretva"){
				$req = $bdd->prepare("UPDATE tvas SET trash='1' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "deletetvapermanently"){
				$req = $bdd->prepare("DELETE FROM tvas WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "loadtvas"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>TVA <i class="fa fa-sort" data-sort="title"></i></td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM tvas WHERE trash='1' ORDER BY tva";
					$back = $bdd->query($req);
					while($row = $back->fetch()){
						?>
					<tr>
						<td><label><input type="checkbox" name="tva" value="<?php echo $row['id'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo $row['tva'];?></span></td>
						<td style="position:relative;">
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<?php
								if(preg_match("#Supprimer TVA#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-tva lx-open-popup" data-title="deleterecord1" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>TVA <i class="fa fa-sort" data-sort="title"></i></td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<?php
			}

			if($_POST['action'] == "addcurrency"){
				$back = $bdd->query("SELECT id FROM currencys WHERE title='".sanitize_vars($_POST['title'])."' AND id NOT IN(".$_POST['id'].")");
				if($back->rowCount() == 0){
					if($_POST['id'] == "0"){
						$req = $bdd->prepare("INSERT INTO currencys(id,title,trash) VALUES ('0','".sanitize_vars($_POST['title'])."','1')");
						$req->execute();
					}
					else{
						$req = $bdd->prepare("UPDATE currencys SET title='".sanitize_vars($_POST['title'])."' WHERE id='".$_POST['id']."'");
						$req->execute();
					}
				}
				else{
					echo "Devise existe déjà !";
				}
			}

			if($_POST['action'] == "deletecurrency"){
				$req = $bdd->prepare("DELETE FROM currencys WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "restorecurrency"){
				$req = $bdd->prepare("UPDATE currencys SET trash='1' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "deletecurrencypermanently"){
				$req = $bdd->prepare("DELETE FROM currencys WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "loadcurrencys"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Devise <i class="fa fa-sort" data-sort="title"></i></td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM currencys WHERE trash='1' ORDER BY title";
					$back = $bdd->query($req);
					while($row = $back->fetch()){
						?>
					<tr>
						<td><label><input type="checkbox" name="currency" value="<?php echo $row['id'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo $row['title'];?></span></td>
						<td style="position:relative;">
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<?php
								if(preg_match("#Supprimer Devises#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-currency lx-open-popup" data-title="deleterecord2" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Devise <i class="fa fa-sort" data-sort="title"></i></td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<?php
			}
			
			if($_POST['action'] == "addclient"){
				$back = $bdd->query("SELECT id FROM clients WHERE ((ice='".sanitize_vars($_POST['ice'])."' AND ice<>'') OR (iff='".sanitize_vars($_POST['iff'])."' AND iff<>'') OR (rc='".sanitize_vars($_POST['rc'])."' AND iff<>'') OR (codecl='".sanitize_vars($_POST['codecl'])."' AND codecl<>'')) AND id NOT IN(".$_POST['id'].")");
				if($back->rowCount() == 0){
					if($_POST['id'] == "0"){
						$rand = "CL".date("ym")."-0001";
						$back1 = $bdd->query("SELECT code FROM clients ORDER BY id DESC LIMIT 0,1");
						if($back1->rowCount() > 0){
							$row1 = $back1->fetch();
							$rand = "CL".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
						}
						$req = $bdd->prepare("INSERT INTO clients(id,code,codecl,ice,iff,rc,fullname,phone,whatsapp,address,email,note,company,user,dateadd,trash) 
						VALUES ('0','".$rand."','".sanitize_vars($_POST['codecl'])."','".sanitize_vars($_POST['ice'])."','".sanitize_vars($_POST['iff'])."','".sanitize_vars($_POST['rc'])."','".sanitize_vars(removeParentheses($_POST['fullname']))."','".sanitize_vars($_POST['phone'])."','".sanitize_vars($_POST['whatsapp'])."','".sanitize_vars($_POST['address'])."','".sanitize_vars($_POST['email'])."','".addslashes($_POST['note'])."','".str_replace("||",",",$_POST['company'])."','".$_SESSION['easybm_id']."','".time()."','1')");
						$req->execute();
					}
					else{
						$req = $bdd->prepare("UPDATE clients SET codecl='".sanitize_vars($_POST['codecl'])."',ice='".sanitize_vars($_POST['ice'])."',iff='".sanitize_vars($_POST['iff'])."',rc='".sanitize_vars($_POST['rc'])."',fullname='".sanitize_vars(removeParentheses($_POST['fullname']))."',phone='".sanitize_vars($_POST['phone'])."',whatsapp='".sanitize_vars($_POST['whatsapp'])."',address='".sanitize_vars($_POST['address'])."',email='".sanitize_vars($_POST['email'])."',note='".addslashes($_POST['note'])."',company='".str_replace("||",",",$_POST['company'])."' WHERE id='".$_POST['id']."'");
						$req->execute();
					}
				}
				else{
					echo "Code client, ICE, IF Ou RC existe déjà !";
				}
			}

			if($_POST['action'] == "deleteclient"){
				$back = $bdd->query("SELECT id,attachments FROM documents WHERE client='".$_POST['id']."'");
				$back1 = $bdd->query("SELECT id FROM payments WHERE client='".$_POST['id']."'");
				if($back->rowCount() == 0 AND $back1->rowCount() == 0){
					$back = $bdd->query("SELECT attachments FROM clients WHERE id='".$_POST['id']."'");
					$row = $back->fetch();
					$attachments = explode(",",$row['attachments']);
					for($i=1;$i<count($attachments);$i++){
						if(file_exists("uploads/".$attachments[$i])){
							unlink("uploads/".$attachments[$i]);
						}
					}
					$req = $bdd->prepare("DELETE FROM clients WHERE id='".$_POST['id']."'");
					$req->execute();
				}
				else{
					echo "Nous avons detecté des documents ou des mouvements de trésorerie liés à ce clients vous ne pouvez pas le supprimer !";
				}
			}
			
			if($_POST['action'] == "restoreclient"){
				$req = $bdd->prepare("UPDATE clients SET trash='1' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "deleteclientpermanently"){
				$req = $bdd->prepare("DELETE FROM clients WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "loadclientslist"){
				if($_POST['category'] == "client"){
					?>
				<option value="">Choisissez un client</option>
					<?php
					$req = "0=1";
					if($_POST['company'] != ""){
						$req = " AND FIND_IN_SET(".$_POST['company'].",company)";
					}
					$back = $bdd->query("SELECT id,code,ice,fullname,phone,address,email FROM clients WHERE fullname<>''".$multicompanies.$req." ORDER BY fullname");
					while($row = $back->fetch()){
						?>
				<option value="<?php echo $row['id'];?>"
					data-id="<?php echo $row['id'];?>"
					data-ice="<?php echo $row['ice'];?>"
					data-name="<?php echo $row['fullname'];?>"
					data-phone="<?php echo $row['phone'];?>"
					data-address="<?php echo $row['address'];?>"
					data-email="<?php echo $row['email'];?>" <?php echo $row['id']==$_POST['id']?"selected":"";?>><?php echo $row['fullname']." (".$row['code'].")";?></option>
						<?php 
					}
				}
				else{
					?>
				<option value="">Choisissez un fournisseur</option>
					<?php
					$req = "0=1";
					if($_POST['company'] != ""){
						$req = " AND FIND_IN_SET(".$_POST['company'].",company)";
					}
					$back = $bdd->query("SELECT * FROM suppliers WHERE title<>''".$multicompanies.$req." ORDER BY title");
					while($row = $back->fetch()){
						?>
				<option value="<?php echo $row['id'];?>"
					data-id="<?php echo $row['id'];?>"
					data-ice="<?php echo $row['respname'];?>"
					data-name="<?php echo $row['title'];?>"
					data-phone="<?php echo $row['respphone'];?>"
					data-address="<?php echo $row['address'];?>"
					data-email="<?php echo $row['respemail'];?>" <?php echo $row['id']==$_POST['id']?"selected":"";?>><?php echo $row['title']." (".$row['code'].")";?></option>
						<?php 
					}
				}
			}
			
			if($_POST['action'] == "loadclients"){
				if(preg_match("#CA Clients#",$_SESSION['easybm_roles'])){
					$req = "SELECT SUM(ca) AS ca,SUM(payments) AS payments FROM clients cl WHERE trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' OR codecl LIKE '".normalizeString($_POST['keyword'])."' OR iff LIKE '".normalizeString($_POST['keyword'])."' OR note LIKE '".normalizeString($_POST['keyword'])."' OR ice LIKE '".normalizeString($_POST['keyword'])."' OR fullname LIKE '".normalizeString($_POST['keyword'])."' OR phone LIKE '".normalizeString($_POST['keyword'])."' OR address LIKE '".normalizeString($_POST['keyword'])."' OR email LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['paid'] == "paid"){
						$req .= " AND ca-payments <= 0";
					}
					elseif($_POST['paid'] == "encours"){
						$req .= " AND ca-payments > 0";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					$back = $bdd->query($req);
					$row = $back->fetch();
					$ca = $row['ca'];
					$paid = $row['payments'];
					$rest = $row['ca'] - $row['payments'];			
					$ca1 = $row['ca']!=0?$row['ca']:1;
					?>
				<div class="lx-caisse-total lx-caisse-total1">
					<h3 style="background:#d9534f;">En cours</h3>
					<p><?php echo number_format((float)(isset($rest)?$rest:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$rest*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $rest*100/$ca1?>%;background:#d9534f;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total2">
					<h3 style="background:#5cb85c;">Payé</h3>
					<p><?php echo number_format((float)(isset($paid)?$paid:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$paid*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $paid*100/$ca1?>%;background:#5cb85c;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total3">
					<h3 style="background:#337ab7;">Chiffre d'affaires</h3>
					<p><?php echo number_format((float)(isset($ca)?$ca:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$ca*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $ca*100/$ca1?>%;background:#337ab7;"></ins>
					</h4>
				</div>
					<?php
				}
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Client <i class="fa fa-sort" data-sort="fullname"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>ICE <i class="fa fa-sort" data-sort="ice"></i></td>
						<td>IF <i class="fa fa-sort" data-sort="iff"></i></td>
						<td>RC <i class="fa fa-sort" data-sort="rc"></i></td>
						<td>Téléphone <i class="fa fa-sort" data-sort="phone"></i></td>
						<td>WhatsApp <i class="fa fa-sort" data-sort="phone"></i></td>
						<td>Adresse <i class="fa fa-sort" data-sort="address"></i></td>
						<td>Email <i class="fa fa-sort" data-sort="email"></i></td>
						<td>Délai de paiement</td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<?php
						if(preg_match("#CA Clients#",$_SESSION['easybm_roles'])){
						?>
						<td>Chiffre d'affaires <i class="fa fa-sort" data-sort="ca"></i></td>
						<td>Payé <i class="fa fa-sort" data-sort="paid"></i></td>
						<td>En cours <i class="fa fa-sort" data-sort="encours"></i></td>
						<?php
						}
						?>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Utilisateur</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM clients cl WHERE trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (whatsapp LIKE '".normalizeString($_POST['keyword'])."' OR code LIKE '".normalizeString($_POST['keyword'])."' OR codecl LIKE '".normalizeString($_POST['keyword'])."' OR iff LIKE '".normalizeString($_POST['keyword'])."' OR note LIKE '".normalizeString($_POST['keyword'])."' OR ice LIKE '".normalizeString($_POST['keyword'])."' OR fullname LIKE '".normalizeString($_POST['keyword'])."' OR phone LIKE '".normalizeString($_POST['keyword'])."' OR address LIKE '".normalizeString($_POST['keyword'])."' OR email LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['paid'] == "paid"){
						$req .= " AND ca-payments <= 0";
					}
					elseif($_POST['paid'] == "encours"){
						$req .= " AND ca-payments > 0";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "ca"){
							$req .= " ORDER BY ca";
						}
						elseif($_POST['sortby'] == "paid"){
							$req .= " ORDER BY payments";
						}
						elseif($_POST['sortby'] == "encours"){
							$req .= " ORDER BY ca-payments";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$ca = 0;
					$paid = 0;
					$rest = 0;
					$back = $bdd->query($req);
					while($row = $back->fetch()){
						?>
					<tr>
						<td><label><input type="checkbox" name="client" value="<?php echo $row['id'];?>" /><del class="checkmark"></del></label></td>
						<td>
							<span style="white-space:nowrap;"><?php echo $row['code'];?></span>
							<?php
							if($row['codecl'] != ""){
							?>
							<br />
							Code client:
							<br />
							<span style="white-space:nowrap;font-weight:500;"><?php echo $row['codecl'];?></span>
							<?php
							}
							?>
						</td>
						<td><span><?php echo $row['fullname'];?></span></td>
						<td style="white-space:nowrap;">
							<?php
							$companytxt = "";
							$back1 = $bdd->query("SELECT rs FROM companies WHERE id IN(".($row['company']!=""?$row['company']:0).")".$companiesid);
							while($company = $back1->fetch()){
								$companytxt .= ",".$company['rs'];
								?>
							<span><?php echo $company['rs'];?></span>
								<?php
							}
							$companytxt = substr($companytxt,1);
							?>
						</td>							
						<td><span><?php echo $row['ice'];?></span></td>
						<td><span><?php echo $row['iff'];?></span></td>
						<td><span><?php echo $row['rc'];?></span></td>
						<td><span><?php echo $row['phone'];?></span></td>
						<td><span><?php echo $row['whatsapp'];?></span></td>
						<td><span><?php echo $row['address'];?></span></td>
						<td><span><?php echo $row['email'];?></span></td>
						<td>
							<?php
							if($row['delay'] != ""){
								?>
							<span>Délai: <strong><?php echo $row['delay'];?> Jours</strong> <a href="javascript:;" class="lx-edit-delay lx-open-popup" data-header="Modifier délai"
								data-id="<?php echo $row['id'];?>"
								data-delay="<?php echo $row['delay'];?>"
								data-table="clients"
								data-title="delay" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a></span>
								<?php
							}
							?>
							<br />
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="clients" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files" style="width:200px;">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="clients" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-paperclip"></i> Convention</a>
							</div>
						</td>
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<?php
						if(preg_match("#CA Clients#",$_SESSION['easybm_roles'])){
						?>
						<td><span style="white-space:nowrap;text-align:right;"><?php echo number_format((float)($row['ca']!=""?$row['ca']:"0"),2,'.',' ');?> <?php echo $settings['currency'];?> TTC</span></td>
						<td><span style="white-space:nowrap;text-align:right;"><?php echo number_format((float)($row['payments']),2,'.',' ');?> <?php echo $settings['currency'];?>  TTC</span></td>
						<td><span style="white-space:nowrap;text-align:right;"><?php echo number_format((float)($row['ca'] - $row['payments']),2,'.',' ');?> <?php echo $settings['currency'];?>  TTC</span></td>
						<?php
						}
						?>
						<td><span><?php echo date("d/m/Y",$row['dateadd']);?></span></td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
							if($back1->rowCount() > 0){
							$user = $back1->fetch();
							?>
							<span><?php echo $user['fullname'];?></span>
							<?php
							}
							?>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<?php
								if(preg_match("#Modifier Clients#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit lx-edit-client lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-codecl="<?php echo $row['codecl'];?>"
								data-company="<?php echo $row['company'];?>"
								data-companytxt="<?php echo $companytxt;?>"
								data-ice="<?php echo $row['ice'];?>"
								data-iff="<?php echo $row['iff'];?>"
								data-rc="<?php echo $row['rc'];?>"
								data-fullname="<?php echo $row['fullname'];?>"
								data-phone="<?php echo $row['phone'];?>"
								data-whatsapp="<?php echo $row['whatsapp'];?>"
								data-address="<?php echo $row['address'];?>"
								data-email="<?php echo $row['email'];?>"
								data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="client">Modifier</a>
								<?php
								}
								if(preg_match("#Supprimer Clients#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-client lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Client <i class="fa fa-sort" data-sort="title"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>ICE <i class="fa fa-sort" data-sort="ice"></i></td>
						<td>IF <i class="fa fa-sort" data-sort="iff"></i></td>
						<td>RC <i class="fa fa-sort" data-sort="rc"></i></td>
						<td>Téléphone <i class="fa fa-sort" data-sort="phone"></i></td>
						<td>WhatsApp <i class="fa fa-sort" data-sort="phone"></i></td>
						<td>Adresse <i class="fa fa-sort" data-sort="address"></i></td>
						<td>Email <i class="fa fa-sort" data-sort="email"></i></td>
						<td>Délai de paiement</td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<?php
						if(preg_match("#CA Clients#",$_SESSION['easybm_roles'])){
						?>
						<td style="text-align:right;"><?php echo number_format((float)($ca),2,'.',' ');?> <?php echo $settings['currency'];?> TTC</td>
						<td style="text-align:right;"><?php echo number_format((float)($paid),2,'.',' ');?> <?php echo $settings['currency'];?> TTC</td>
						<td style="text-align:right;"><?php echo number_format((float)abs($rest),2,'.',' ');?> <?php echo $settings['currency'];?> TTC</td>
						<?php
						}
						?>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Utilisateur</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> client(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> client(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}
			
			if($_POST['action'] == "loadpricerange"){
				$function = "";
				if($_POST['page'] == "facture"){
					$back = $bdd->query("SELECT MAX(price-rastva) mx,MIN(price-rastva) AS mn FROM documents WHERE trash='1' AND type='facture'".$multicompanies);
					$function = "loadFactures('1')";
				}
				if($_POST['page'] == "devis"){
					$back = $bdd->query("SELECT MAX(price-rastva) mx,MIN(price-rastva) AS mn FROM documents WHERE trash='1' AND type='devis'".$multicompanies);
					$function = "loadDevis('1')";
				}
				if($_POST['page'] == "factureproforma"){
					$back = $bdd->query("SELECT MAX(price-rastva) mx,MIN(price-rastva) AS mn FROM documents WHERE trash='1' AND type='factureproforma'".$multicompanies);
					$function = "loadFacturesProforma('1')";
				}
				if($_POST['page'] == "bl"){
					$back = $bdd->query("SELECT MAX(price-rastva) mx,MIN(price-rastva) AS mn FROM documents WHERE trash='1' AND type='bl'".$multicompanies);
					$function = "loadBL('1')";
				}
				if($_POST['page'] == "bs"){
					$back = $bdd->query("SELECT MAX(price-rastva) mx,MIN(price-rastva) AS mn FROM documents WHERE trash='1' AND type='bs'".$multicompanies);
					$function = "loadBS('1')";
				}
				if($_POST['page'] == "br"){
					$back = $bdd->query("SELECT MAX(price-rastva) mx,MIN(price-rastva) AS mn FROM documents WHERE trash='1' AND type='br'".$multicompanies);
					$function = "loadBR('1')";
				}
				if($_POST['page'] == "avoir"){
					$back = $bdd->query("SELECT MAX(price-rastva) mx,MIN(price-rastva) AS mn FROM documents WHERE trash='1' AND type='avoir'".$multicompanies);
					$function = "loadAvoirs('1')";
				}
				if($_POST['page'] == "bc"){
					$back = $bdd->query("SELECT MAX(price-rastva) mx,MIN(price-rastva) AS mn FROM documents WHERE trash='1' AND type='bc'".$multicompanies);
					$function = "loadBC('1')";
				}
				if($_POST['page'] == "bre"){
					$back = $bdd->query("SELECT MAX(price-rastva) mx,MIN(price-rastva) AS mn FROM documents WHERE trash='1' AND type='bre'".$multicompanies);
					$function = "loadBRC('1')";
				}
				if($_POST['page'] == "nh"){
					$back = $bdd->query("SELECT MAX(price-rastva) mx,MIN(price-rastva) AS mn FROM documents WHERE trash='1' AND type='nh'".$multicompanies);
					$function = "loadNH('1')";
				}
				if($_POST['page'] == "nf"){
					$back = $bdd->query("SELECT MAX(price-rastva) mx,MIN(price-rastva) AS mn FROM documents WHERE trash='1' AND type='nf'".$multicompanies);
					$function = "loadNH('1')";
				}
				if($_POST['page'] == "ph"){
					$back = $bdd->query("SELECT MAX(price-rastva) mx,MIN(price-rastva) AS mn FROM documents WHERE trash='1' AND type='ph'".$multicompanies);
					$function = "loadPH('1')";
				}
				if($_POST['page'] == "payments"){
					$back = $bdd->query("SELECT MAX(price) mx,MIN(price) AS mn FROM payments WHERE trash='1'".$multicompanies);
					$function = "loadCaisse('1')";
				}
				if($_POST['page'] == "facturesuppliers"){
					$back = $bdd->query("SELECT MAX(price+(tva-rastva)) mx,MIN(price+(tva-rastva)) AS mn FROM facturesuppliers WHERE trash='1'".$multicompanies);
					$function = "loadFactureSuppliers('1')";
				}
				$row = $back->fetch();
				?>
				<input type="text" class="js-range-slider" name="my_range" value="" />
				<input type="hidden" id="pricemin" />
				<input type="hidden" id="pricemax" />
				<a href="javascript:;" onclick="<?php echo $function;?>" class="lx-price-filter">Appliquer</a>
				<script>
				$(function() {
					$(".js-range-slider").ionRangeSlider({
						type: "double",
						min: <?php echo $row['mn']!=""?$row['mn']:0;?>,
						max: <?php echo $row['mx']!=""?$row['mx']:0;?>,
						from: <?php echo $row['mn']!=""?$row['mn']:0;?>,
						to: <?php echo $row['mx']!=""?$row['mx']:0;?>,
						grid: true,
						onChange: function (data) {
							$("#pricemin").val(data.from);
							$("#pricemax").val(data.to);
						}
					});
				});
				</script>
				<?php
			}
			
			if($_POST['action'] == "reloadclientslist"){
				?>
				<option value="">Choisissez un client</option>
				<?php
				$back = $bdd->query("SELECT * FROM clients WHERE fullname<>''".$multicompanies." AND trash='1' AND FIND_IN_SET(".$_POST['company'].",company) ORDER BY fullname");
				while($row = $back->fetch()){
					?>
				<option value="<?php echo $row['id'];?>"
					data-id="<?php echo $row['id'];?>"
					data-type="client"
					data-ice="<?php echo $row['ice'];?>"
					data-name="<?php echo $row['fullname'];?>"
					data-phone="<?php echo $row['phone'];?>"
					data-address="<?php echo $row['address'];?>"
					data-email="<?php echo $row['email'];?>" <?php echo $_POST['idclient']==$row['id']?"selected":"";?>><?php echo $row['fullname']." (".$row['code'].")";?></option>
					<?php 
				}	
				if($_POST['page'] == "ph" OR $_POST['page'] == "devis"){
					$back = $bdd->query("SELECT * FROM prospects WHERE fullname<>''".$multicompanies." ORDER BY fullname");
					while($row = $back->fetch()){
						?>
					<option value="<?php echo $row['id'];?>"
						data-id="<?php echo $row['id'];?>"
						data-type="prospect"
						data-ice="<?php echo $row['ice'];?>"
						data-name="<?php echo $row['fullname'];?>"
						data-phone="<?php echo $row['phone'];?>"
						data-address="<?php echo $row['address'];?>"
						data-email="<?php echo $row['email'];?>" <?php echo $_POST['idprospect']==$row['id']?"selected":"";?>><?php echo $row['fullname']." (Prospect)";?></option>
						<?php 
					}	
				}				
			}

			if($_POST['action'] == "reloadsupplierslist"){
				?>
				<option value="">Choisissez un fournisseur</option>
				<?php
				$back = $bdd->query("SELECT * FROM suppliers WHERE title<>''".$multicompanies." AND trash='1' AND FIND_IN_SET(".$_POST['company'].",company) ORDER BY title");
				while($row = $back->fetch()){
					?>
				<option value="<?php echo $row['id'];?>"
					data-id="<?php echo $row['id'];?>"
					data-type="supplier"
					data-ice="<?php echo $row['ice'];?>"
					data-name="<?php echo $row['title'];?>"
					data-phone="<?php echo $row['respphone'];?>"
					data-address="<?php echo $row['address'];?>"
					data-email="<?php echo $row['respemail'];?>" <?php echo $_POST['idclient']==$row['id']?"selected":"";?>><?php echo $row['title']." (".$row['code'].")";?></option>
					<?php 
				}
			}

			if($_POST['action'] == "addcommand"){
				$randcode = $_POST['prefix'].date("ym",getDateNow($_POST['dateadd']))."-".sprintf('%06d',$_POST['code']);
				
				$back = $bdd->query("SELECT id FROM documents WHERE code='".$randcode."' AND company='".$_POST['company']."' AND id NOT IN('".$_POST['id']."')");
				if($back->rowCount() === 0){
					// Save Client
					$client = 0;
					$supplier = 0;
					$prospect = 0;
					$dupsupplier = 0;
					if($_POST['prefix'] == "BC" OR $_POST['prefix'] == "BRC"){
						if($_POST['exist'] == "1"){
							$req = $bdd->prepare("UPDATE suppliers SET ice='".sanitize_vars($_POST['ice'])."',respphone='".sanitize_vars($_POST['phone'])."',address='".sanitize_vars($_POST['address'])."',respemail='".sanitize_vars($_POST['email'])."' WHERE id='".$_POST['client']."'");
							$req->execute();
						}
						else{
							if($_POST['fullname'] != ""){
								$back = $bdd->query("SELECT id FROM suppliers WHERE (title='".sanitize_vars($_POST['fullname'])."')");
								if($back->rowCount() == 0){
									$rand = "FR".date("ym")."-0001";
									$back1 = $bdd->query("SELECT code FROM suppliers ORDER BY id DESC LIMIT 0,1");
									if($back1->rowCount() > 0){
										$row1 = $back1->fetch();
										$rand = "FR".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
									}
									$req = $bdd->prepare("INSERT INTO suppliers(id,code,title,respname,respphone,respemail,respfax,ice,address,note,company,user,dateadd,trash) 
									VALUES ('0','".$rand."','".sanitize_vars($_POST['fullname'])."','','".sanitize_vars($_POST['phone'])."','".sanitize_vars($_POST['email'])."','','".sanitize_vars($_POST['ice'])."','".sanitize_vars($_POST['address'])."','','".str_replace("||",",",$_POST['company'])."','".$_SESSION['easybm_id']."','".time()."','1')");
									$req->execute();
									$back = $bdd->query("SELECT id FROM suppliers WHERE ice='".sanitize_vars($_POST['ice'])."' AND respphone='".sanitize_vars($_POST['phone'])."' ORDER BY id DESC LIMIT 0,1");
									$row = $back->fetch();
									$_POST['client'] = $row['id'];
								}
								else{
									$dupsupplier = 1;
									echo "Fournisseur existe déjà!";
								}
							}
						}
						$supplier = $_POST['client'];						
					}
					else{
						if($_POST['exist'] == "1"){
							if($_POST['typeclient'] == "client"){
								$req = $bdd->prepare("UPDATE clients SET ice='".sanitize_vars($_POST['ice'])."',fullname='".sanitize_vars($_POST['fullname'])."',phone='".sanitize_vars($_POST['phone'])."',address='".sanitize_vars($_POST['address'])."',email='".sanitize_vars($_POST['email'])."' WHERE id='".$_POST['client']."'");
								$req->execute();
								$client = $_POST['client'];
							}
							if($_POST['typeclient'] == "prospect"){
								$req = $bdd->prepare("UPDATE prospects SET ice='".sanitize_vars($_POST['ice'])."',fullname='".sanitize_vars($_POST['fullname'])."',phone='".sanitize_vars($_POST['phone'])."',address='".sanitize_vars($_POST['address'])."',email='".sanitize_vars($_POST['email'])."' WHERE id='".$_POST['client']."'");
								$req->execute();								
								$prospect = $_POST['client'];
								if($_POST['id'] == "0" AND $_POST['type'] != "devis" AND $_POST['type'] != "ph"){
									$rand = "CL".date("ym")."-0001";
									$back1 = $bdd->query("SELECT code FROM clients ORDER BY id DESC LIMIT 0,1");
									if($back1->rowCount() > 0){
										$row1 = $back1->fetch();
										$rand = "CL".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
									}
									$req = $bdd->prepare("INSERT INTO clients(code,fullname,phone,email,ice,address,note,company,user,dateadd,trash) 
									SELECT '".$rand."',fullname,phone,email,ice,address,labels,company,user,'".time()."','1' FROM prospects WHERE id='".$_POST['client']."'");
									$req->execute();
									
									$req = $bdd->prepare("UPDATE prospects SET note=CONCAT(note,'\nTransformé en client') WHERE id='".$_POST['client']."' AND note NOT LIKE '%Transformé en client%'");
									$req->execute();								
									
									$back = $bdd->query("SELECT id FROM clients WHERE fullname='".sanitize_vars($_POST['fullname'])."' AND phone='".sanitize_vars($_POST['phone'])."' ORDER BY id DESC LIMIT 0,1");
									$row = $back->fetch();
				
									$req = $bdd->prepare("UPDATE documents SET prospect='0',client='".$row['id']."' WHERE prospect='".$_POST['client']."'");
									$req->execute();
			
									$_POST['client'] = $row['id'];
									$client = $_POST['client'];
								}
								else{
									$prospect = $_POST['client'];
								}								
							}
						}
						elseif($_POST['exist'] == "2"){
							if($_POST['fullname'] != ""){
								$req = $bdd->prepare("INSERT INTO prospects(id,ice,fullname,phone,address,email,company,user,dateadd,dateupdate,trash) VALUES ('0','".sanitize_vars($_POST['ice'])."','".sanitize_vars($_POST['fullname'])."','".sanitize_vars($_POST['phone'])."','".sanitize_vars($_POST['address'])."','".sanitize_vars($_POST['email'])."','".str_replace("||",",",$_POST['company'])."','".$_SESSION['easybm_id']."','".time()."','".time()."','1')");
								$req->execute();
								
								$back = $bdd->query("SELECT id FROM prospects WHERE fullname='".sanitize_vars($_POST['fullname'])."' AND phone='".sanitize_vars($_POST['phone'])."' ORDER BY id DESC LIMIT 0,1");
								$row = $back->fetch();
								$_POST['client'] = $row['id'];
								$prospect = $_POST['client'];
							}							
						}	
						else{
							if($_POST['fullname'] != ""){
								$rand = "CL".date("ym")."-0001";
								$back1 = $bdd->query("SELECT code FROM clients ORDER BY id DESC LIMIT 0,1");
								if($back1->rowCount() > 0){
									$row1 = $back1->fetch();
									$rand = "CL".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
								}
								$req = $bdd->prepare("INSERT INTO clients(id,code,ice,fullname,phone,address,email,company,user,dateadd,trash) VALUES ('0','".$rand."','".sanitize_vars($_POST['ice'])."','".sanitize_vars($_POST['fullname'])."','".sanitize_vars($_POST['phone'])."','".sanitize_vars($_POST['address'])."','".sanitize_vars($_POST['email'])."','".str_replace("||",",",$_POST['company'])."','".$_SESSION['easybm_id']."','".time()."','1')");
								$req->execute();
									
								$back = $bdd->query("SELECT id FROM clients WHERE fullname='".sanitize_vars($_POST['fullname'])."' AND phone='".sanitize_vars($_POST['phone'])."' ORDER BY id DESC LIMIT 0,1");
								$row = $back->fetch();
								$_POST['client'] = $row['id'];
								$client = $_POST['client'];
							}
						}
					}
					if($dupsupplier == 0){
						if((int)$client > 0 OR (int)$supplier > 0 OR (int)$prospect > 0){	
							if($_POST['id'] == "-1"){
								$_POST['qtys'] = $_POST['qtyback'];
							}
							
							$back = $bdd->query("SELECT id,client,supplier FROM documents WHERE id IN('".$_POST['id']."')");
							if($back->rowCount() > 0){
								$rowcom = $back->fetch();
							}
							else{
								$rowcom['client'] = 0;
								$rowcom['supplier'] = 0;
							}
							$dateorder = getDateNow($_POST['dateadd']);
							$commandid = $_POST['id'];
							if($_POST['id'] == "0" OR $_POST['id'] == "-1"){
								if($_POST['type'] == "avoir" OR $_POST['type'] == "br"){
									$paid = "Non remboursée";
									if($_POST['cash'] != "" AND $_POST['cash'] != "0"){
										$paid = "Partiellement remboursée";
										if($_POST['rest'] == "0"){
											$paid = "Remboursée";
										}
									}
								}
								else{
									$paid = "Non payée";
									if($_POST['cash'] != "" AND $_POST['cash'] != "0"){
										$paid = "Partiellement payée";
										if($_POST['rest'] == "0"){
											$paid = "Payée";
										}
									}
								}
								$back = $bdd->query("SELECT * FROM infodocs WHERE company='".$_POST['company']."' AND document='".$_POST['type']."'");
								$extrainfos = $back->fetch();
								if(addslashes($_POST['modepayment']) == ""){
									$_POST['modepayment'] = isset($extrainfos['modepayment'])?$extrainfos['modepayment']:'';
								}
								if(addslashes($_POST['conditions']) == ""){
									$_POST['conditions'] = isset($extrainfos['conditions'])?$extrainfos['conditions']:'';
								}
								if(addslashes($_POST['abovetable']) == ""){
									$_POST['abovetable'] = isset($extrainfos['abovetable'])?$extrainfos['abovetable']:'';
								}
								$req = $bdd->prepare("INSERT INTO `documents`(`id`, `code`, `price`, `state`, `type`, `category`, `client`, `supplier`, `prospect`, `company`, `note`, `correctdoc`, `modepayment`, `conditions`, `abovetable`, `user`, `dateadd`, `trash`, `title`, `abovenote`, `belownote`, `unit`, `qty`, `uprice`, `ptva`, `discounttype`, `discount`, `tprice`) 
								VALUES ('0','".$randcode."','0','".$paid."','".$_POST['type']."','".$_POST['category']."','".$client."','".$supplier."','".$prospect."','".$_POST['company']."',
								'".addslashes($_POST['note'])."','".addslashes($_POST['correctdoc'])."','".addslashes($_POST['modepayment'])."','".addslashes($_POST['conditions'])."',
								'".addslashes($_POST['abovetable'])."','".$_SESSION['easybm_id']."','".$dateorder."','1','".addslashes(substr($_POST['products'],2))."','".addslashes(substr($_POST['abovenotes'],2))."','".addslashes(substr($_POST['belownotes'],2))."',
								'".addslashes(substr($_POST['units'],2))."','".addslashes(substr($_POST['qtys'],2))."','".addslashes(substr($_POST['uprices'],2))."','".addslashes(substr($_POST['tvas'],2))."','".addslashes(substr($_POST['discounttypes'],2))."',
								'".addslashes(substr($_POST['discounts'],2))."','".addslashes(substr($_POST['tprices'],2))."')");
								$req->execute();
								$back = $bdd->query("SELECT id FROM documents ORDER BY id DESC LIMIT 0,1");
								$command = $back->fetch();
								$commandid = $command['id'];
								
								if($_POST['cash'] != "" AND $_POST['cash'] != "0"){
									$type = "Sortie";
									if($_POST['type'] == "facture" OR $_POST['type'] == "nh" OR $_POST['type'] == "nf"){
										$type = "Entrée";
									}

									$rand = substr($type,0,1).date("ym")."-000001";
									$back = $bdd->query("SELECT code,dateadd FROM payments WHERE code LIKE '".substr($rand,0,1)."%' AND company='".$_POST['company']."' ORDER BY id DESC LIMIT 0,1");
									if($back->rowCount() > 0){
										$row1 = $back->fetch();
										if(date("Y") == date("Y",$row1['dateadd'])){
											$rand = substr($rand,0,1).date("ym")."-".sprintf('%06d',intval(substr($row1['code'],6))+1);
										}
									}

									$title = "Paiement facture N° ".$randcode;
									if($_POST['type'] == "avoir"){
										$title = "Remboursement facture avoir N° ".$randcode;
									}
									elseif($_POST['type'] == "bc"){
										$title = "Paiement bon de commande N° ".$randcode;
									}
									elseif($_POST['type'] == "nh"){
										$title = "Paiement note d\'honoraire N° ".$randcode;
									}
									if($_POST['id'] == "-1"){
										$_POST['qtys'] = $_POST['qtyback'];
									}
									$req = $bdd->prepare("INSERT INTO `payments`(`id`, `code`, `doc`, `client`, `supplier`, `type`, `title`, `price`, `modepayment`, `imputation`, `rib`, `paid`, `remis`, `dateremis`, `company`, `note`, `typedoc`, `category`, `dateadd`, `datedue`, `datepaid`, `user`, `trash`) 
									VALUES ('0','".$rand."','".$commandid."','".$client."','".$supplier."','".$type."','".$title."','".$_POST['cash']."','".$_POST['modepayment1']."',
									'".sanitize_vars($_POST['imputation'])."','".$_POST['rib']."','0','0','','".$_POST['company']."','','".$_POST['type']."','".$_POST['category']."',
									'".$dateorder."','','','".$_SESSION['easybm_id']."','1')");
									$req->execute();	
								}
							}
							else{
								$req = $bdd->prepare("UPDATE documents SET code='".$randcode."',
																		client='".sanitize_vars($client)."',
																		supplier='".sanitize_vars($supplier)."',
																		prospect='".sanitize_vars($prospect)."',
																		company='".str_replace("||",",",$_POST['company'])."',
																		note='".addslashes($_POST['note'])."',
																		correctdoc='".sanitize_vars($_POST['correctdoc'])."',
																		user='".sanitize_vars($_SESSION['easybm_id'])."',
																		title='".addslashes(substr($_POST['products'],2))."',
																		abovenote='".addslashes(substr($_POST['abovenotes'],2))."',
																		belownote='".addslashes(substr($_POST['belownotes'],2))."',
																		unit='".addslashes(substr($_POST['units'],2))."',
																		qty='".addslashes(substr($_POST['qtys'],2))."',
																		uprice='".addslashes(substr($_POST['uprices'],2))."',
																		ptva='".addslashes(substr($_POST['tvas'],2))."',
																		discounttype='".addslashes(substr($_POST['discounttypes'],2))."',
																		discount='".addslashes(substr($_POST['discounts'],2))."',
																		tprice='".addslashes(substr($_POST['tprices'],2))."',
																		dateadd='".$dateorder."' WHERE id='".$_POST['id']."'");
								$req->execute();
								
								$req = $bdd->prepare("UPDATE payments SET client='".sanitize_vars($client)."',supplier='".sanitize_vars($supplier)."' WHERE doc='".$_POST['id']."' AND typedoc NOT IN('facturesupplier')");
								$req->execute();
								
								$back = $bdd->query("SELECT * FROM documents WHERE id='".$_POST['id']."' ORDER BY id DESC LIMIT 0,1");
								$row = $back->fetch();
								$randcode = $row['code'];
								$req = $bdd->prepare("UPDATE payments SET price2='0',currency='' WHERE doc='".$_POST['id']."' AND typedoc='".$_POST['type']."'");
								$req->execute();
								if($row['avoirs'] != ""){
									$avoirs = explode("|",$row['avoirs']);
									for($i=0;$i<count($avoirs);$i++){
										$avoirdetails = explode("/",$avoirs[$i]);
										$req = $bdd->prepare("UPDATE documents SET client='".$client."',supplier='".$supplier."' WHERE code='".$avoirdetails[1]."'");
										$req->execute();
										
										$req = $bdd->prepare("UPDATE payments SET client='".sanitize_vars($client)."',supplier='".sanitize_vars($supplier)."' WHERE doc='".$avoirdetails[0]."' AND typedoc NOT IN('facturesupplier')");
										$req->execute();
									}
								}
								
								if($row['brs'] != ""){
									$avoirs = explode("|",$row['brs']);
									for($i=0;$i<count($avoirs);$i++){
										$avoirdetails = explode("/",$avoirs[$i]);
										$req = $bdd->prepare("UPDATE documents SET client='".$client."',supplier='".$supplier."' WHERE code='".$avoirdetails[1]."'");
										$req->execute();
									}
								}
							}
							
							// Save Order
							$products = explode("||",$_POST['products']);
							$qtys = explode("||",$_POST['qtys']);
							$oqtys = explode("||",$_POST['qtys']);
							$tprices = explode("||",$_POST['tprices']);
							$tvas = explode("||",$_POST['tvas']);
							$qtyfine = 0;
							$tprice = 0;
							for($i=1;$i<count($products);$i++){
								if($qtys[$i] > 0){
									$qtyfine++;
								}
								if($oqtys[$i] == 0 OR $oqtys[$i] == ""){
									$oqtys[$i] = 1;
								}
								if(isset($qtys[$i])){
									if($qtys[$i] != 0){
										if($_POST['id'] == "-1"){
											$tprices[$i] = number_format((float)(((float)$tprices[$i] / (float)$oqtys[$i]) * (float)$qtys[$i]),2,".","");
										}
										$tprice += $tprices[$i] * (1+($tvas[$i]/100));
									}
								}
							}
							
							$req = $bdd->prepare("UPDATE documents SET price='".number_format((float)$tprice,2,".","")."' WHERE id='".$commandid."'");
							$req->execute();
							
							updatePrevDocKPI($rowcom['client'],$rowcom['supplier']);
							updateDocKPI($commandid);
							if($tprice > 0){
								updateDocAvoir($_POST['correctdoc']);
							}
						}
						else{
							if($_POST['prefix'] == "BC" OR $_POST['prefix'] == "BRC"){
								echo "Choisissez ou saisissez un fournisseur";
							}
							else{
								echo "Choisissez ou saisissez un client";
							}
						}
					}
				}
				else{
					echo "Numéro de document existe déjà";
				}
			}
			
			if($_POST['action'] == "mergedocs"){
				$randcode = $_POST['prefix'].date("ym",getDateNow($_POST['dateadd']))."-".sprintf('%06d',$_POST['code']);
				
				$back = $bdd->query("SELECT id FROM documents WHERE code='".$randcode."' AND company='".$_POST['company']."'");
				if($back->rowCount() === 0){
					
					$ids = isset($_POST['ids']) ? explode(',', $_POST['ids']) : [];
					$ids = array_filter($ids, 'is_numeric');
					if (empty($ids)) die("Invalid IDs");

					$sql = "SELECT * FROM documents WHERE id IN (" . implode(',', $ids) . ")";
					$req = $bdd->query($sql);

					$merged = []; // store merged products
					$foundDiscountTypes = []; // track discount types used

					while ($row = $req->fetch(PDO::FETCH_ASSOC)) {
						// Split columns by '||'
						$titles        = explode('||', $row['title']);
						$units         = explode('||', $row['unit']);
						$qtys          = explode('||', $row['qty']);
						$uprices       = explode('||', $row['uprice']);
						$tprices       = explode('||', $row['tprice']);
						$tvas          = explode('||', $row['ptva']);
						$abovenotes    = explode('||', $row['abovenote']);
						$belownotes    = explode('||', $row['belownote']);
						$discounttypes = explode('||', $row['discounttype']);
						$discounts     = explode('||', $row['discount']);

						// Track non-empty discount types
						foreach ($discounttypes as $dt) {
							if (trim($dt) !== '') $foundDiscountTypes[] = trim($dt);
						}

						$count = count($titles);
						for ($i = 0; $i < $count; $i++) {
							$key = $titles[$i] . '|' . $units[$i] . '|' . $uprices[$i] . '|' . $tvas[$i] . '|' . $discounttypes[$i];

							if (!isset($merged[$key])) {
								$merged[$key] = [
									'title'        => $titles[$i],
									'unit'         => $units[$i],
									'qty'          => (float)$qtys[$i],
									'uprice'       => (float)$uprices[$i],
									'tprice'       => (float)$tprices[$i],
									'ptva'         => $tvas[$i],
									'abovenote'    => $abovenotes[$i],
									'belownote'    => $belownotes[$i],
									'discounttype' => $discounttypes[$i],
									'discount'     => (float)$discounts[$i],
								];
							} else {
								// Merge quantities & totals
								$merged[$key]['qty'] += (float)$qtys[$i];
								$merged[$key]['tprice'] += (float)$tprices[$i];
								if ($discounttypes[$i] != '%') {
									$merged[$key]['discount'] += (float)$discounts[$i];
								}
							}
						}
					}

					// 🧮 Step 2: Detect mixed % and DH types (ignore empty ones)
					$foundDiscountTypes = array_unique($foundDiscountTypes);
					$hasMixedDiscounts = (in_array('%', $foundDiscountTypes) && in_array('DH', $foundDiscountTypes));

					// 🧮 Step 3: If mixed, convert all % to DH based on tprice
					if ($hasMixedDiscounts) {
						foreach ($merged as &$item) {
							if ($item['discounttype'] == '%') {
								$item['discount'] = number_format((float)($item['discount'] / 100) * ($item['uprice']*$item['qty']), 2,".","");
								$item['discounttype'] = 'DH';
							}
						}
						unset($item); // best practice
					}

					// 🧱 Step 4: Rebuild concatenated fields
					$_POST['products']      = implode('||', array_column($merged, 'title'));
					$_POST['units']         = implode('||', array_column($merged, 'unit'));
					$_POST['qtys']          = implode('||', array_column($merged, 'qty'));
					$_POST['uprices']       = implode('||', array_column($merged, 'uprice'));
					$_POST['tprices']       = implode('||', array_column($merged, 'tprice'));
					$_POST['tvas']          = implode('||', array_column($merged, 'ptva'));
					$_POST['abovenotes']    = implode('||', array_column($merged, 'abovenote'));
					$_POST['belownotes']    = implode('||', array_column($merged, 'belownote'));
					$_POST['discounttype']  = buildValueString(implode('||', array_column($merged, 'discounttype')), $_POST['qtys']);
					$_POST['discounts']     = implode('||', array_column($merged, 'discount'));
					
					// Save Client
					$client = 0;
					$supplier = 0;
					$prospect = 0;
					$dupsupplier = 0;
					if($_POST['prefix'] == "BC" OR $_POST['prefix'] == "BRC"){
						if($_POST['exist'] == "1"){
							$req = $bdd->prepare("UPDATE suppliers SET ice='".sanitize_vars($_POST['ice'])."',respphone='".sanitize_vars($_POST['phone'])."',address='".sanitize_vars($_POST['address'])."',respemail='".sanitize_vars($_POST['email'])."' WHERE id='".$_POST['client']."'");
							$req->execute();
						}
						else{
							if($_POST['fullname'] != ""){
								$back = $bdd->query("SELECT id FROM suppliers WHERE (title='".sanitize_vars($_POST['fullname'])."')");
								if($back->rowCount() == 0){
									$rand = "FR".date("ym")."-0001";
									$back1 = $bdd->query("SELECT code FROM suppliers ORDER BY id DESC LIMIT 0,1");
									if($back1->rowCount() > 0){
										$row1 = $back1->fetch();
										$rand = "FR".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
									}
									$req = $bdd->prepare("INSERT INTO suppliers(id,code,title,respname,respphone,respemail,respfax,ice,address,note,company,user,dateadd,trash) 
									VALUES ('0','".$rand."','".sanitize_vars($_POST['fullname'])."','','".sanitize_vars($_POST['phone'])."','".sanitize_vars($_POST['email'])."','','".sanitize_vars($_POST['ice'])."','".sanitize_vars($_POST['address'])."','','".str_replace("||",",",$_POST['company'])."','".$_SESSION['easybm_id']."','".time()."','1')");
									$req->execute();
									$back = $bdd->query("SELECT id FROM suppliers WHERE ice='".sanitize_vars($_POST['ice'])."' AND respphone='".sanitize_vars($_POST['phone'])."' ORDER BY id DESC LIMIT 0,1");
									$row = $back->fetch();
									$_POST['client'] = $row['id'];
								}
								else{
									$dupsupplier = 1;
									echo "Fournisseur existe déjà!";
								}
							}
						}
						$supplier = $_POST['client'];						
					}
					else{
						if($_POST['exist'] == "1"){
							if($_POST['typeclient'] == "client"){
								$req = $bdd->prepare("UPDATE clients SET ice='".sanitize_vars($_POST['ice'])."',fullname='".sanitize_vars($_POST['fullname'])."',phone='".sanitize_vars($_POST['phone'])."',address='".sanitize_vars($_POST['address'])."',email='".sanitize_vars($_POST['email'])."' WHERE id='".$_POST['client']."'");
								$req->execute();
								$client = $_POST['client'];
							}
							if($_POST['typeclient'] == "prospect"){
								$req = $bdd->prepare("UPDATE prospects SET ice='".sanitize_vars($_POST['ice'])."',fullname='".sanitize_vars($_POST['fullname'])."',phone='".sanitize_vars($_POST['phone'])."',address='".sanitize_vars($_POST['address'])."',email='".sanitize_vars($_POST['email'])."' WHERE id='".$_POST['client']."'");
								$req->execute();								
								$prospect = $_POST['client'];
								if($_POST['type'] != "devis" AND $_POST['type'] != "ph"){
									$rand = "CL".date("ym")."-0001";
									$back1 = $bdd->query("SELECT code FROM clients ORDER BY id DESC LIMIT 0,1");
									if($back1->rowCount() > 0){
										$row1 = $back1->fetch();
										$rand = "CL".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
									}
									$req = $bdd->prepare("INSERT INTO clients(code,fullname,phone,email,ice,address,note,company,user,dateadd,trash) 
									SELECT '".$rand."',fullname,phone,email,ice,address,labels,company,user,'".time()."','1' FROM prospects WHERE id='".$_POST['client']."'");
									$req->execute();
									
									$req = $bdd->prepare("UPDATE prospects SET note=CONCAT(note,'\nTransformé en client') WHERE id='".$_POST['client']."' AND note NOT LIKE '%Transformé en client%'");
									$req->execute();								
									
									$back = $bdd->query("SELECT id FROM clients WHERE fullname='".sanitize_vars($_POST['fullname'])."' AND phone='".sanitize_vars($_POST['phone'])."' ORDER BY id DESC LIMIT 0,1");
									$row = $back->fetch();
				
									$req = $bdd->prepare("UPDATE documents SET prospect='0',client='".$row['id']."' WHERE prospect='".$_POST['client']."'");
									$req->execute();
			
									$_POST['client'] = $row['id'];
									$client = $_POST['client'];
								}
								else{
									$prospect = $_POST['client'];
								}
							}
						}
						elseif($_POST['exist'] == "2"){
							if($_POST['fullname'] != ""){
								$req = $bdd->prepare("INSERT INTO prospects(id,ice,fullname,phone,address,email,company,user,dateadd,dateupdate,trash) VALUES ('0','".sanitize_vars($_POST['ice'])."','".sanitize_vars($_POST['fullname'])."','".sanitize_vars($_POST['phone'])."','".sanitize_vars($_POST['address'])."','".sanitize_vars($_POST['email'])."','".str_replace("||",",",$_POST['company'])."','".$_SESSION['easybm_id']."','".time()."','".time()."','1')");
								$req->execute();
								
								$back = $bdd->query("SELECT id FROM prospects WHERE fullname='".sanitize_vars($_POST['fullname'])."' AND phone='".sanitize_vars($_POST['phone'])."' ORDER BY id DESC LIMIT 0,1");
								$row = $back->fetch();
								$_POST['client'] = $row['id'];
								$prospect = $_POST['client'];
							}							
						}	
						else{
							if($_POST['fullname'] != ""){
								$rand = "CL".date("ym")."-0001";
								$back1 = $bdd->query("SELECT code FROM clients ORDER BY id DESC LIMIT 0,1");
								if($back1->rowCount() > 0){
									$row1 = $back1->fetch();
									$rand = "CL".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
								}
								$req = $bdd->prepare("INSERT INTO clients(id,code,ice,fullname,phone,address,email,company,dateadd,trash) VALUES ('0','".$rand."','".sanitize_vars($_POST['ice'])."','".sanitize_vars($_POST['fullname'])."','".sanitize_vars($_POST['phone'])."','".sanitize_vars($_POST['address'])."','".sanitize_vars($_POST['email'])."','".str_replace("||",",",$_POST['company'])."','".time()."','1')");
								$req->execute();
									
								$back = $bdd->query("SELECT id FROM clients WHERE fullname='".sanitize_vars($_POST['fullname'])."' AND phone='".sanitize_vars($_POST['phone'])."' ORDER BY id DESC LIMIT 0,1");
								$row = $back->fetch();
								$_POST['client'] = $row['id'];
								$client = $_POST['client'];
							}
						}
					}
						
					$dateorder = getDateNow($_POST['dateadd']);
					$paid = "Non payée";
					if($_POST['prefix'] == "AV"){
						$paid = "Non Remboursée";
					}
					$back = $bdd->query("SELECT * FROM infodocs WHERE company='".$_POST['company']."' AND document='".$_POST['type']."'");
					$extrainfos = $back->fetch();
					$_POST['modepayment'] = "";
					if(isset($extrainfos['modepayment'])){
						$_POST['modepayment'] = isset($extrainfos['modepayment'])?$extrainfos['modepayment']:'';
					}
					$_POST['conditions'] = "";
					if(isset($extrainfos['conditions'])){
						$_POST['conditions'] = isset($extrainfos['conditions'])?$extrainfos['conditions']:'';
					}
					$_POST['abovetable'] = "";
					if(isset($extrainfos['abovetable'])){
						$_POST['abovetable'] = isset($extrainfos['abovetable'])?$extrainfos['abovetable']:'';
					}
					$req = $bdd->prepare("INSERT INTO `documents`(`id`, `code`, `price`, `state`, `type`, `category`, `client`, `supplier`, `prospect`, `company`, `note`, `correctdoc`, `modepayment`, `conditions`, `abovetable`, `user`, `dateadd`, `trash`, `title`, `abovenote`, `belownote`, `unit`, `qty`, `uprice`, `ptva`, `discounttype`, `discount`, `tprice`) 
					VALUES ('0','".$randcode."','0','".$paid."','".$_POST['type']."','".$_POST['category']."','".$client."','".$supplier."','".$prospect."','".$_POST['company']."',
					'".addslashes($_POST['note'])."','','".addslashes($_POST['modepayment'])."','".addslashes($_POST['conditions'])."',
					'".addslashes($_POST['abovetable'])."','".$_SESSION['easybm_id']."','".$dateorder."','1','".addslashes($_POST['products'])."','".addslashes($_POST['abovenotes'])."','".addslashes($_POST['belownotes'])."',
					'".addslashes($_POST['units'])."','".addslashes($_POST['qtys'])."','".addslashes($_POST['uprices'])."','".addslashes($_POST['tvas'])."','".addslashes($_POST['discounttype'])."',
					'".addslashes($_POST['discounts'])."','".addslashes($_POST['tprices'])."')");
					$req->execute();
					$back = $bdd->query("SELECT id FROM documents ORDER BY id DESC LIMIT 0,1");
					$command = $back->fetch();
					$commandid = $command['id'];
					
					// Save Order
					$products = explode("||",$_POST['products']);
					$abovenotes = explode("||",$_POST['abovenotes']);
					$belownotes = explode("||",$_POST['belownotes']);
					$qtys = explode("||",$_POST['qtys']);
					$units = explode("||",$_POST['units']);
					$uprices = explode("||",$_POST['uprices']);
					$tprices = explode("||",$_POST['tprices']);
					$tvas = explode("||",$_POST['tvas']);
					$discounts = explode("||",$_POST['discounts']);
					
					$tprice = 0;
					for($i=0;$i<count($products);$i++){
						if(isset($qtys[$i])){
							if($qtys[$i] != 0){
								$tprice += $tprices[$i] * (1+($tvas[$i]/100));
							}
						}
					}
					
					$req = $bdd->prepare("UPDATE documents SET price='".number_format((float)$tprice,2,".","")."' WHERE id='".$commandid."'");
					$req->execute();
					
					updateDocKPI($commandid);
				}
				else{
					echo "Numéro de document existe déjà";
				}
			}

			if($_POST['action'] == "addpayment"){
				if((float)$_POST['cash'] != "0" OR (float)$_POST['price2'] != "0"){
					$back = $bdd->query("SELECT * FROM documents d WHERE id='".$_POST['id']."'");
					if($_POST['type'] == "facturesupplier"){
						$back = $bdd->query("SELECT * FROM facturesuppliers d WHERE id='".$_POST['id']."'");
					}
					$document = $back->fetch();
					
					$type = "Sortie";
					if($_POST['type'] == "facture" OR $_POST['type'] == "nh" OR $_POST['type'] == "nf"){
						$type = "Entrée";
					}

					$rand = substr($type,0,1).date("ym")."-000001";
					$back = $bdd->query("SELECT code,dateadd FROM payments WHERE code LIKE '".substr($rand,0,1)."%' AND company='".$document['company']."' ORDER BY id DESC LIMIT 0,1");
					if($back->rowCount() > 0){
						$row1 = $back->fetch();
						if(date("Y") == date("Y",$row1['dateadd'])){
							$rand = substr($rand,0,1).date("ym")."-".sprintf('%06d',intval(substr($row1['code'],6))+1);
						}
					}
					
					$title = "Paiement facture N° ".$document['code'];
					if($_POST['type'] == "avoir"){
						$title = "Remboursement facture avoir N° ".$document['code'];
					}
					elseif($_POST['type'] == "facturesupplier"){
						$title = "Paiement facture fournisseur N° ".$document['code'];
					}
					elseif($_POST['type'] == "nh"){
						$title = "Paiement note d\'honoraires N° ".$document['code'];
					}
					elseif($_POST['type'] == "nf"){
						$title = "Paiement note de frais N° ".$document['code'];
					}
					
					$datedue = "";
					$datepaid = "";
					if($_POST['modepayment'] == "Espèce"){
						$datedue = time();
						$datepaid = "";
					}
					elseif($_POST['modepayment'] == "Virement" OR $_POST['modepayment'] == "TPE"){
						$datedue = time();
						$datepaid = time();					
					}
					else{
						if($_POST['datedue'] != ""){
							$datedue = getDateNow($_POST['datedue']);
						}
						if($_POST['datepaid'] != ""){
							$datepaid = getDateNow($_POST['datepaid']);
						}
					}
					$req = $bdd->prepare("INSERT INTO `payments`(`id`, `code`, `doc`, `client`, `supplier`, `type`, `title`, `price`, `price2`, `currency`, `modepayment`, `imputation`, `rib`, `rib2`, `paid`, `remis`, `dateremis`, `company`, `note`, `typedoc`, `category`, `dateadd`, `datedue`, `datepaid`, `user`, `trash`) 
					VALUES ('0', '".$rand."','".$_POST['id']."','".$document['client']."','".$document['supplier']."','".$type."','".$title."','".$_POST['cash']."','".((float)$_POST['price2'])."','".$_POST['currency']."','".$_POST['modepayment']."','".sanitize_vars($_POST['imputation'])."','".((float)$_POST['rib'])."','".((float)$_POST['rib2'])."','0','0','','".$document['company']."','".addslashes($_POST['note'])."','".$_POST['type']."','".$_POST['category']."','".time()."','".$datedue."','".$datepaid."','".$_SESSION['easybm_id']."','1')");
					$req->execute();
				}
				
				if($_POST['type'] == "facturesupplier"){
					$req = $bdd->prepare("UPDATE facturesuppliers SET rastva='".$_POST['rastva']."' WHERE id='".$_POST['id']."'");
					$req->execute();
					updateFSKPI($_POST['id']);
				}
				else{
					updateDocKPI($_POST['id']);
				}
				updatePrevDocKPI($document['client'],$document['supplier']);
				if($_POST['type'] == "facture" OR $_POST['type'] == "nh" OR $_POST['type'] == "nf"){
					$req = $bdd->prepare("UPDATE documents SET rastva='".$_POST['rastva']."',stateras='Non payée' WHERE id='".$_POST['id']."'");
					$req->execute();
				}
				
			}
			
			if($_POST['action'] == "deletepaymenthistory"){
				$back = $bdd->query("SELECT doc,typedoc,attachments FROM payments WHERE id='".$_POST['id']."'");
				$row = $back->fetch();

				$req = $bdd->prepare("DELETE FROM payments WHERE id='".$_POST['id']."'");
				$req->execute();

				$back = $bdd->query("SELECT SUM(price) AS sm FROM payments WHERE doc='".$row['doc']."'");
				$payment = $back->fetch();
				
				if($row['typedoc'] != "facturesupplier"){
					$back = $bdd->query("SELECT * FROM documents d WHERE id='".$row['doc']."'");
					$document = $back->fetch();
					
					if($document['type'] == "avoir" OR $document['type'] == "br"){
						$state = "Non remboursée";
						if($payment['sm'] > 0){
							if($document['price'] - $payment['sm'] > 0){
								$state = "Partiellement remboursée";
							}
							else{
								$state = "Remboursée";
							}
						}
					}
					else{
						$state = "Non payée";
						if($payment['sm'] > 0){
							if($document['price'] + ($document['tva'] - $document['rastva']) - $payment['sm'] > 0){
								$state = "Partiellement payée";
							}
							else{
								$state = "Payée";
							}
						}
					}
					$req = $bdd->prepare("UPDATE documents SET state='".$state."',rastva=0 WHERE id='".$row['doc']."'");
					$req->execute();
					updateDocKPI($document['id']);
				}
				else{
					$back = $bdd->query("SELECT * FROM facturesuppliers d WHERE id='".$row['doc']."'");
					$document = $back->fetch();
					
					$state = "Non payée";
					if($payment['sm'] > 0){
						if($document['price'] + ($document['tva'] - $document['rastva']) - $payment['sm'] > 0){
							$state = "Partiellement payée";
						}
						else{
							$state = "Payée";
						}
					}

					$req = $bdd->prepare("UPDATE facturesuppliers SET state='".$state."',rastva=0 WHERE id='".$row['doc']."'");
					$req->execute();
					updateFSKPI($document['id']);
				}
				updatePrevDocKPI($document['client'],$document['supplier']);
				$attachments = explode(",",$row['attachments']);
				for($i=1;$i<count($attachments);$i++){
					if(file_exists("uploads/".$attachments[$i])){
						unlink("uploads/".$attachments[$i]);
					}
				}
			}
			
			if($_POST['action'] == "deletefiles"){
				$files = str_replace(",".$_POST['file'],"",$_POST['files']);
				$files = str_replace($_POST['file'],"",$files);
				$req = $bdd->prepare("UPDATE ".$_POST['table']." SET attachments='".$files."' WHERE id='".$_POST['id']."'");
				$req->execute();
				if(file_exists("uploads/".$_POST['file'])){
					unlink("uploads/".$_POST['file']);
				}
			}
			
			if($_POST['action'] == "loadproductslist"){
				?>
				<option value="">Chercher un produit / service</option>
				<?php
				$req = " AND company='0'";
				if($_POST['company'] != ""){
					$req = " AND company='".$_POST['company']."'";
				}
				
				$rowsproducts = [];
				$rowsunits = [];
				$back = $bdd->query("SELECT title,unit FROM documents WHERE title<>'' AND trash='1'".$req.$multicompanies." ORDER BY id");
				while($row = $back->fetch()){
					$products = explode("||",$row['title']);
					$units = explode("||",$row['unit']);
					for($i=0;$i<count($products);$i++){
						$rowsproducts[] = $products[$i];
						$rowsunits[] = $units[$i];
					}
				}

				for($i=0;$i<count($rowsproducts);$i++){
					if(cleanString($rowsproducts[$i]) != ""){
					?>
				<option value="<?php echo cleanString($rowsproducts[$i]);?>" data-unit="<?php echo $rowsunits[$i];?>"><?php echo $rowsproducts[$i];?></option>
					<?php
					}
				}	
			}

			if($_POST['action'] == "loadunitslist"){
				?>
				<option value=""></option>
				<?php
				$req = " AND company='0'";
				if($_POST['company'] != ""){
					$req = " AND company='".$_POST['company']."'";
				}
				
				$rowsunits = [];
				$back = $bdd->query("SELECT unit FROM documents WHERE unit<>'' AND trash='1'".$req.$multicompanies." ORDER BY id");
				while($row = $back->fetch()){
					$units = explode("||",$row['unit']);
					for($i=0;$i<count($units);$i++){
						$rowsunits[] = $units[$i];
					}
				}

				for($i=0;$i<count($rowsunits);$i++){
					if($rowsunits[$i] != ""){
					?>
				<option value="<?php echo $rowsunits[$i];?>" data-unit="<?php echo $rowsunits[$i];?>"><?php echo $rowsunits[$i];?></option>
					<?php
					}
				}
			}

			if($_POST['action'] == "loadfactureslist"){
				?>
				<option value="">Saisissez ou choisissez un numéro facture</option>
				<?php
				$req = "";
				if($_POST['company'] != ""){
					$req .= " AND company='".$_POST['company']."'";
				}
				$back = $bdd->query("SELECT code FROM documents WHERE trash='1' AND type='facture'".$req.$multicompanies." ORDER BY code");
				while($row = $back->fetch()){
					?>
				<option value="<?php echo $row['code'];?>"><?php echo $row['code'];?></option>
					<?php
				}
			}

			if($_POST['action'] == "loadcode"){
				$back1 = $bdd->query("SELECT ".$_POST['type']." FROM companies WHERE id='".$_POST['company']."'");
				$row1 = $back1->fetch();
				$code = sprintf('%06d',(isset($row1[$_POST['type']])?$row1[$_POST['type']]:0));
				$date = strtotime("31-12-".date("Y",strtotime(str_replace("/","-",$_POST['dateadd'])))) + (60*60*24);
				$back1 = $bdd->query("SELECT code,dateadd FROM documents WHERE company='".$_POST['company']."' AND type='".$_POST['type']."' AND dateadd < ".$date." ORDER BY code DESC LIMIT 0,1");
				if($back1->rowCount() > 0){
					$row1 = $back1->fetch();
					if(date("Y",strtotime(str_replace("/","-",$_POST['dateadd']))) == date("Y",$row1['dateadd'])){
						$code = sprintf('%06d',intval(substr($row1['code'],($_POST['type']=="bre"?8:7)))+1);
					}
					else{
						$code = "000001";
					}
				}
				echo $code;
			}

			if($_POST['action'] == "deletedocument"){
				$back = $bdd->query("SELECT type,code,correctdoc,company,attachments,client,supplier FROM documents WHERE id='".$_POST['id']."'");
				$row = $back->fetch();
				
				$back1 = $bdd->query("SELECT id FROM documents WHERE correctdoc='".$row['code']."' AND company='".$row['company']."'");
				if($back1->rowCount() == 0){
					$req = $bdd->prepare("DELETE FROM payments WHERE doc='".$_POST['id']."' AND typedoc='".$row['type']."'");
					$req->execute();
					$req = $bdd->prepare("DELETE FROM detailsdocuments WHERE doc='".$_POST['id']."'");
					$req->execute();
					
					$attachments = explode(",",$row['attachments']);
					for($i=1;$i<count($attachments);$i++){
						if(file_exists("uploads/".$attachments[$i])){
							unlink("uploads/".$attachments[$i]);
						}
					}

					
					$req = $bdd->prepare("DELETE FROM documents WHERE id='".$_POST['id']."'");
					$req->execute();
					
					updateDocAvoir($row['correctdoc']);
					updatePrevDocKPI($row['client'],$row['supplier']);
				}
				else{
					echo "Vous ne pouvez pas supprimer une facture liée à des avoirs / bons de retour";
				}
			}

			if($_POST['action'] == "editextrainfo"){
				$req = $bdd->prepare("UPDATE documents SET modepayment='".addslashes($_POST['modepayment'])."',conditions='".addslashes($_POST['conditions'])."',abovetable='".addslashes($_POST['abovetable'])."' WHERE id='".$_POST['id']."'");
				$req->execute();
			}

			if($_POST['action'] == "addtags"){
				$req = $bdd->prepare("UPDATE `".$_POST['table']."` SET labels='".sanitize_vars($_POST['labels'])."' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "loadsupplierras"){
				if($_POST['page'] == "fc"){
					$back1 = $bdd->query("SELECT * FROM myregularity WHERE company='".$_POST['company']."'");
				}
				else{
					$back1 = $bdd->query("SELECT * FROM regularity WHERE supplier='".$_POST['supplier']."'");
				}
				if($back1->rowCount() > 0){
				$row1 = $back1->fetch();
				?>
				<div class="lx-table" style="overflow:visible;">
				<table cellpadding="0" cellspacing="0">
				<?php
				if(isset($row1['state'])){
					?>
					<tr>
						<td style="background:#F8F8F8;color:#858585;font-weight:normal;border-bottom:0px;border-bottom:1px solid #EEEEEE;"><span>Statut: </span></td>
						<td style="background:#FFFFFF;border-bottom:0px;border-bottom:1px solid #EEEEEE;"><span><b><?php echo $row1['state'];?></b></span></td>
					</tr>
					<?php
				}
				if(isset($row1['type'])){
					if($row1['type'] != ""){
						?>
					<tr>
						<td style="background:#F8F8F8;border-bottom:1px solid #EEEEEE;"><span>Type d'activité: </span></td>
						<td style="background:#FFFFFF;border-bottom:1px solid #EEEEEE;"><span><b><?php echo $row1['type'];?></b></span></td>
					</tr>
						<?php
					}
				}
				if(isset($row1['state'])){
					if($row1['state'] != ""){
						$color = "#FFFFFF";
						if($row1['appras'] == "Oui"){
							$color = "#e5f4dd";
						}
						if($row1['appras'] == "Non"){
							$color = "#fbe1e1";
						}
						?>
					<tr>
						<td style="background:#F8F8F8;border-bottom:1px solid #EEEEEE;"><span>RAS applicable: </span></td>
						<td style="background:<?php echo $color;?>;border-bottom:1px solid #EEEEEE;"><span><b><?php echo $row1['appras'];?></b></span></td>
					</tr>
						<?php
					}
				}
				if(isset($row1['doc'])){
					if($row1['doc'] != ""){
						$color = "#FFFFFF";
						if($row1['doc'] == "Avec"){
							$color = "#e5f4dd";
						}
						if($row1['doc'] == "Sans"){
							$color = "#fbe1e1";
						}
					?>
					<tr>
						<td style="background:#F8F8F8;border-bottom:1px solid #EEEEEE;"><span>Régularité fiscale: </span></td>
						<td style="background:<?php echo $color;?>;border-bottom:1px solid #EEEEEE;"><span><b><?php echo $row1['doc'];?></b></span></td>
					</tr>
					<?php
					}
					if($row1['doc'] == "Avec"){
						if(isset($row1['attachments'])){
							if($row1['attachments'] != ""){
						?>
					<tr>
						<td style="background:#F8F8F8;border-bottom:1px solid #EEEEEE;"><span>Document régularité fiscale: </span></td>
						<?php
						$color = "#FFFFFF";
						$datevalid = $row1['datedoc'] + ($row1['jdoc'] * 60 * 60 * 24);
						if($datevalid > time()){
							$color = "#e5f4dd";
						}
						if($datevalid < time()){
							$color = "#fbe1e1";
						}
						?>
						<td style="background:<?php echo $color;?>;border-bottom:1px solid #EEEEEE;">
							<?php
							if(isset($row1['doc'])){
								if($row1['doc'] == "Avec"){
									if(isset($row1['attachments'])){
										if($row1['attachments'] != ""){
									?>
							<span><b><a href="uploads/<?php echo $row1['attachments'];?>" target="_blank" style="padding:0px;color:#39add1;background:none;"><i class="fa fa-download"></i> Téléchager</a></b></span>
									<?php
										}
									}
									if(isset($row1['datedoc'])){
										if($row1['datedoc'] != ""){
									?>
							<span>Date demande: <b><?php echo date("d/m/Y",$row1['datedoc']);?></b></span>
									<?php
										}
									}
									if(isset($row1['jdoc'])){
										if($row1['jdoc'] != ""){
									?>
							<span>Période de validité en jours: <b><?php echo $row1['jdoc'];?> jours</b></span>
									<?php
										}
									}
								}
							}
							?>
						</td>
					</tr>
						<?php
							}
						}
					}
				}
				$percentage = $row1['percentage'];
				if($_POST['page'] == "fc"){
					$back1 = $bdd->query("SELECT (price) AS price,tva,rastva FROM documents WHERE id='".$_POST['id']."'");
				}
				else{
					$back1 = $bdd->query("SELECT (price) AS price,tva,rastva FROM facturesuppliers WHERE id='".$_POST['id']."'");
				}
				$row1 = $back1->fetch();
				$rastva = $row1['rastva'];
				
				$color = "#FFFFFF";
				if($row1['price'] > 5000){
					$color = "#e5f4dd";
				}
				if($row1['price'] <= 5000){
					$color = "#fbe1e1";
				}
				$price = $row1['price'];
				$tva = $row1['tva'];
				if($_POST['page'] == "fc"){
					$name = "société";
				}
				else{
					$name = "fournisseur";
				}
				?>
					<tr>
						<td style="background:#F8F8F8;"><span class="infobul" data-title="Opération exclue de la RAS: Services dont le montant est ≤ 5 000 MAD HT, dans la limite de 50 000 MAD HT par mois et par <?php echo $name;?>.">Montant de la facture: </span></td>
						<td style="background:<?php echo $color;?>;"><span><b><?php echo number_format((float)$row1['price'],2,","," ");?> HT</b></span></td>
					</tr>
				<?php
				$datestart = strtotime(date("01-m-Y"));
				$dateend = strtotime(date("t-m-Y"));
				if($_POST['page'] == "fc"){
					$back1 = $bdd->query("SELECT SUM(price) AS price FROM documents WHERE state='Payée' AND company='".$_POST['company']."' AND type='facture' AND datepaid BETWEEN ".$datestart." AND ".$dateend);
				}
				else{
					$back1 = $bdd->query("SELECT SUM(price) AS price FROM facturesuppliers WHERE state='Payée' AND supplier='".$_POST['supplier']."' AND datepaid BETWEEN ".$datestart." AND ".$dateend);
				}
				$row1 = $back1->fetch();
				$color = "#FFFFFF";
				if($row1['price']+$price > 50000){
					$color = "#e5f4dd";
				}
				if($row1['price']+$price <= 50000){
					$color = "#fbe1e1";
				}
				?>
					<tr>
						<td style="background:#F8F8F8;"><span class="infobul" data-title="Opération exclue de la RAS: Services dont le montant est ≤ 5 000 MAD HT, dans la limite de 50 000 MAD HT par mois et par <?php echo $name;?>.">Total facture en <?php echo date("m/Y");?>:</span></td>
						<td style="background:<?php echo $color;?>;"><span><b><?php echo number_format((float)$row1['price']+$price,2,","," ");?> HT</b></span></td>
					</tr>
					<tr>
						<td style="background:#F8F8F8;"><span>Vérifier les opérations exclues de la RAS:</span></td>
						<td style="background:#FFFFFF;">
						<span><b>- La vente d’énergie électrique.</b></span>
						<span><b>- Les prestations d’assainissement.</b></span>
						<span><b>- Les services de télécommunication.</b></span>
						<span><b>- Les courtiers en assurances.</b></span>
						</td>
					</tr>
					<?php
				if(isset($percentage)){
					if($percentage != ""){
					?>
					<tr>
						<td style="background:#F8F8F8;"><span>% TVA à retenir si la RAS est appliquée: </span></td>
						<td style="background:#FFFFFF;"><span><b><?php echo $percentage;?>% <ins style="display:inline-block;color:#FF0000;">(<?php echo number_format((float)($tva*($percentage/100)),2,".","");?> <?php echo $settings['currency'];?>)</ins></b></span></td>
					</tr>
						<?php
					}
				}
					?>
				</table>
				<input type="hidden" id="ras" value="<?php echo number_format((float)($tva*($percentage/100)),2,".","");?>" />
				<input type="hidden" id="percentage" value="<?php echo $percentage;?>" />
				</div>
				<div>
					<a href="javascript:;" class="lx-apply" style="<?php echo $rastva == 0?"display:inline-block;":"display:none;";?>">Appliquer la RAS TVA</a>
					<a href="javascript:;" class="lx-cancel" style="<?php echo $rastva == 0?"display:none;":"display:inline-block;";?>">Annuler la RAS TVA</a>
				</div>
				<?php
				}
				else{
					if($_POST['page'] == "fc"){
						$back = $bdd->query("SELECT rs AS title FROM companies WHERE id='".$_POST['company']."'");
						$name = "la société";
					}
					else{
						$back = $bdd->query("SELECT title FROM suppliers WHERE id='".$_POST['supplier']."'");
						$name = "le fournisseur";
					}
					$row = $back->fetch();
					?>
				<p><i class="fa fa-info-circle"></i> Veuillez paramétrer les informations de RAS TVA pour <?php echo $name." : ".$row['title']?></p>
					<?php
				}
			}
			
			if($_POST['action'] == "loadpayments"){
				$phtable = "";
							if($_POST['type'] == "facturesupplier"){
								$back = $bdd->query("SELECT * FROM facturesuppliers WHERE id='".$_POST['id']."'");
							}
							else{
								$back = $bdd->query("SELECT * FROM documents WHERE id='".$_POST['id']."'");
							}
							$row = $back->fetch();
							$title = "facture fournisseur";
							if(isset($row['type'])){
								if($row['type'] == "avoir"){
									$title = "facture";
								}
								elseif($row['type'] == "avoir"){
									$title = "facture avoir";
								}
								elseif($row['type'] == "nh"){
									$title = "note d\'honoraires";
								}
								elseif($row['type'] == "nf"){
									$title = "note de frais";
								}
							}
							$back1 = $bdd->query("SELECT * FROM payments WHERE doc='".$_POST['id']."' AND typedoc='".$_POST['type']."'");
							if($back1->rowCount() > 0){
								$phtable .= '<table><tr class="lx-first-tr">';
								$phtable .= '<td>Montant payé</td>';
								$phtable .= '<td>Mode de paiement</td>';
								$phtable .= '<td>Imputation</td>';
								$phtable .= '<td>Compte banquaire</td>';
								$phtable .= '<td width="240"><div style="width:200px;">Note</div></td>';
								$phtable .= '<td>Date</td>';
								if(preg_match("#Suppression historique de paiement#",$_SESSION['easybm_roles'])){
									$phtable .= '<td>Supprimer</td>';
								}
								$phtable .= '</tr>';
							}
							while($row1 = $back1->fetch()){
								$back2 = $bdd->query("SELECT * FROM bankaccounts WHERE id='".$row1['rib']."'");
								$row2 = $back2->fetch();
								$phtable .= '<tr style="background:'.($row1['paid']=="1"?'#fbe1e1':'').';">';
								$phtable .= '<td style="text-align:right;">'.($row1['price2'] != "0"?'<span>'.number_format((float)$row1['price2'],2,"."," ")." ".$row1['currency'].'</span>':'').($row1['price'] != "0"?'<span>'.number_format((float)$row1['price'],2,","," ").' '.$settings['currency'].'</span>':'').($row1['paid']=='1'?'<strong style="color:#FF0000;font-weight:bold;font-style:italic;">Impayé</strong>':'').'</td>';
								$phtable .= '<td><span>'.$row1['modepayment'].'</span></td>';
								$phtable .= '<td><span>'.$row1['imputation'].'</span></td>';
								$phtable .= '<td><span>'.(isset($row2['bank'])?$row2['bank'].':'.$row2['rib']:'').'</span></td>';
								$phtable .= '<td><span>'.$row1['note'].'</span></td>';
								$phtable .= '<td><span>'.($row1['dateadd']!=""?date("d/m/Y H:i",$row1['dateadd']):"").'</span></td>';
								if(preg_match("#Suppression historique de paiement#",$_SESSION['easybm_roles'])){
									$phtable .= '<td>
													<a href="javascript:;" class="lx-delete lx-delete-payment"
														data-id="'.$row['id'].'"
														data-company="'.$row['company'].'"
														data-modepayment="'.$row1['modepayment'].'"
														data-rib="'.$row1['rib'].'"
														data-imputation="'.$row1['imputation'].'"
														data-libelle="Suppression historique paiement '.$title.' N° '.$row['code'].'"
														data-type="Entrée"
														data-price="'.$row1['price'].'">Supprimer</a>
													<div class="lx-delete-payment-choice">
														<a href="javascript:;" class="lx-yes-delete-payment" data-paid="'.$row1['price'].'" data-id="'.$row1['id'].'">Oui</a>
														<a href="javascript:;" class="lx-no-delete-payment">Non</a>
													</div>
												</td>';
								}
								$phtable .= '</tr>';
							}
							if($back1->rowCount() > 0){
								$phtable .= '</table>';
							}
				echo $phtable;
			}
			
			if($_POST['action'] == "loadfactures"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Factures avoir / Bons de retour N°</td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails facture</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td>Délai de paiement <i class="fa fa-sort" data-sort="delay"></i></td>
						<td>Statut et historique des paiements <i class="fa fa-sort" data-sort="rest"></i></td>
						<td>RAS TVA <i class="fa fa-sort" data-sort="delayras"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM documents d WHERE category='client' AND type='facture' AND trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['stateras'] != ""){
						if($_POST['stateras'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND stateras NOT IN('Payée') AND ".time()." > datedue AND rastva > 0";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['stateras'])){
								$req .= " AND (stateras IN ('".str_replace("||","','",$_POST['stateras'])."') OR (stateras NOT IN('Payée') AND ".time()." > datedue)) AND rastva > 0";
							}
							else{
								$req .= " AND stateras IN ('".str_replace("||","','",$_POST['stateras'])."') AND rastva > 0";
							}
						}
					}
					if($_POST['statee'] != ""){
						if($_POST['statee'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['statee'])){
								$req .= " AND (state IN ('".str_replace("||","','",$_POST['statee'])."') OR (state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")))";
							}
							else{
								$req .= " AND state IN ('".str_replace("||","','",$_POST['statee'])."')";
							}
						}
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['avoir'] == "1"){
						$req .= " AND (avoirs<>'' OR brs<>'')";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimit'] != "" AND $_POST['dateendlimit'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimit']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimit'])) + (60*60*24) - 1;
						$req .= " AND ((dateadd+(delay*60*60*24)) BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimitras'] != "" AND $_POST['dateendlimitras'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimitras']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimitras'])) + (60*60*24) - 1;
						$req .= " AND (datedue BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "delay"){
							$req .= " ORDER BY (dateadd + (delay * 60 * 60 * 24))";
						}
						elseif($_POST['sortby'] == "delayras"){
							$req .= " ORDER BY (datepaid + ".secondsUntilNext20th().")";
						}
						elseif($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$montant = 0;
					while($row = $back->fetch()){
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();

						$back1 = $bdd->query("SELECT * FROM prospects WHERE id='".$row['prospect']."'");
						$prospect = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facture" value="<?php echo $row['id'];?>" 
						data-client="<?php echo $row['client'];?>" 
						data-prospect="<?php echo $row['prospect'];?>" 
						data-code="<?php echo $row['code'];?>" 
						data-company="<?php echo $row['company'];?>" 
						data-discounttype="<?php echo $row['discounttype'];?>" 
						data-price="<?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ")." ".$settings['currency'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<?php
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];
						?>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>
						<td><a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Facture" class="lx-download-file1 lx-print-f lx-print-fc lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['code'];?></a></td>
						<td>
							<?php
							$smavoir = 0;
							if($row['avoirs'] != ""){
								?>
							<span>Avoirs:</span>
								<?php
								$avoirs = explode("|",$row['avoirs']);
								for($i=0;$i<count($avoirs);$i++){
									$avoirdetails = explode("/",$avoirs[$i]);
									if($avoirdetails[2] > 0){
										?>
							<ins style="white-space:nowrap;"><a href="javascript:;" data-id="<?php echo $avoirdetails[0];?>" data-href="printclientdocs.php?id=<?php echo $avoirdetails[0];?>&title=Avoir" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature" style="color:#39add1;white-space:nowrap;"><?php echo $avoirdetails[1];?></a> (<?php echo $avoirdetails[2]." ".$settings['currency'];?> TTC)<ins><br />
										<?php
										$smavoir += $avoirdetails[2];
									}
								}
							}
							if($row['brs'] != ""){
								?>
							<br /><span>Bons de retour:</span>
								<?php
								$brs = explode("|",$row['brs']);
								for($i=0;$i<count($brs);$i++){
									$brdetails = explode("/",$brs[$i]);
									if($brdetails[2] > 0){
										?>
							<ins style="white-space:nowrap;"><a href="javascript:;" data-id="<?php echo $brdetails[0];?>" data-href="printclientdocs.php?id=<?php echo $brdetails[0];?>&title=Bon de retour" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature" style="color:#39add1;white-space:nowrap;"><?php echo $brdetails[1];?></a> (<?php echo $brdetails[2]." ".$settings['currency'];?> TTC)<ins><br />
										<?php
										$smavoir += $brdetails[2];
									}
								}
							}
							$back1 = $bdd->query("SELECT SUM(payments) AS sm FROM documents WHERE correctdoc='".$row['code']."'");
							$row1 = $back1->fetch();	
							if($row['avoirs'] != "" OR $row['brs'] != ""){
								$smavoir -= $row1['sm'];
							}
							?>	
						</td>
						<td>
							<span style="white-space:nowrap;"><?php echo $clients[$row['client']]['fullname']." (".($clients[$row['client']]['codecl']!=""?$clients[$row['client']]['codecl']:$clients[$row['client']]['code']).")";?></span>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td>
							<?php
							$products = '<table>
											<tr class="lx-firstrow">
												<td style="width:500px;">Designation</td>
												<td>Qté</td>
												<td style="width:100px;">P.U. HT</td>
												<td class="lx-discount">Remise totale HT</td>
												<td>TVA</td>
												<td>Total HT</td>
												<td class="lx-qty-back">Qté à retourner</td>
												<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
												<td class="lx-note-cell" style="display:none;">Description</td>
												<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
											</tr>
											<tr class="lx-secondrow" style="display:none;">
												<td colspan="10">
													<center><i>0 produits / services</i></center>
												</td>
											</tr>';
							$price1 = 0;
							$price2 = 0;
							$discount = 0;
							$discounttype = "";
							$ttva = 0;
							$titles = explode("||",$row['title']);
							$units = explode("||",$row['unit']);
							$qtys = array_map('floatval',explode("||",$row['qty']));
							$uprices = array_map('floatval',explode("||",$row['uprice']));
							$tprices = array_map('floatval',explode("||",$row['tprice']));
							$ptvas = array_map('floatval',explode("||",$row['ptva']));
							$abovenotes = explode("||",$row['abovenote']);
							$belownotes = explode("||",$row['belownote']);
							$discounttypes = explode("||",$row['discounttype']);
							$discounts = array_map('floatval',explode("||",$row['discount']));
							$tqtyavoir = 0;
							for($i=0;$i<count($titles);$i++){
								$qtyavoir = 0;
								if($row['avoirs'] != ""){
									$avoirs = explode("|",$row['avoirs']);
									for($j=0;$j<count($avoirs);$j++){
										$avoirdetails = explode("/",$avoirs[$j]);
										if($avoirdetails[2] > 0){
											$back1 = $bdd->query("SELECT title,qty FROM documents WHERE id='".$avoirdetails[0]."'");
											$row1 = $back1->fetch();
											$avoirqtys = explode("||",$row1['qty']);
											$avoirproducts = explode("||",$row1['title']);
											for($k=0;$k<count($avoirproducts);$k++){
												if($titles[$i] == $avoirproducts[$k]){
													$qtyavoir += (float)$avoirqtys[$k];
												}
											}
										}
									}
								}
								if($row['brs'] != ""){
									$brs = explode("|",$row['brs']);
									for($j=0;$j<count($brs);$j++){
										$brdetails = explode("/",$brs[$j]);
										if($brdetails[2] > 0){
											$back1 = $bdd->query("SELECT title,qty FROM documents WHERE id='".$brdetails[0]."'");
											$row1 = $back1->fetch();
											$brqtys = explode("||",$row1['qty']);
											$brproducts = explode("||",$row1['title']);
											for($k=0;$k<count($brproducts);$k++){
												if($titles[$i] == $brproducts[$k]){
													$qtyavoir += (float)$brqtys[$k];
												}
											}
										}
									}
								}
								$tqtyavoir += ($qtys[$i]-$qtyavoir);
								$d = 0;
								if(isset($discounttypes[$i])){
									if($discounttypes[$i] == $settings['currency']){
										$discount += $discounts[$i];
										$d = $discounts[$i];
									}
									else{
										$discount += (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
										$d = (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
									}
								}
								?>
							<span style="padding-bottom:8px;border-bottom:1px solid #ccc;"><?php echo $titles[$i]."<br />Prix unitaire: <b>".number_format((float)($uprices[$i]*(1+($ptvas[$i]/100))),2,"."," ")." ".$settings['currency']."</b> TTC <br />Quantité: <b>".$qtys[$i]." ".$units[$i]."</b>".($d!="0"?"<br />Remise totale HT: <b>".number_format((float)$d,2,"."," ")." ".$settings['currency']."</b>":"");?></span>
								<?php
								$price1 += (($tprices[$i]+$d)*(1+($ptvas[$i]/100)));
								$price2 += ($tprices[$i]*(1+($ptvas[$i]/100)));
								$ttva += ($tprices[$i]*($ptvas[$i]/100));
								$discounttype = $discounttypes[$i];
								$products .= '<tr class="lx-otherrow" data-title="" data-abovenote="'.htmlspecialchars($abovenotes[$i]).'" data-belownote="'.htmlspecialchars($belownotes[$i]).'" data-qty="'.$qtys[$i].'" data-qtyavoir="'.$qtyavoir.'" data-unit="'.$units[$i].'" data-uprice="'.$uprices[$i].'" data-remise="'.$d.'" data-oremise="'.$d.'" data-tva="'.$ptvas[$i].'" data-tprice="'.($uprices[$i]*$qtys[$i]).'" data-ttprice="'.$tprices[$i].'">
												<td style="width:auto;" class="lx-product-title">'.$titles[$i].'</td>
												<td><span>'.$qtys[$i].' '.$units[$i].'</span></td>
												<td><span>'.number_format((float)$uprices[$i],2,"."," ").'</span></td>
												<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" value="'.$discounts[$i].'" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;">'.$discounttype.'</u></label></div></td>
												<td><span>'.$ptvas[$i].'%</span></td>
												<td><span>'.number_format((float)$tprices[$i],2,"."," ").'</span></td>
												<td class="lx-qty-back"><div class="lx-textfield"><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>
												<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>
												<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>
												<td class="lx-delete-cell">
												<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>
												<div class="lx-delete-payment-choice">
												<a href="javascript:;" class="lx-delete-this">Oui</a>
												<a href="javascript:;" class="lx-no-delete-payment">Non</a>
												</div>
												</td>
											</tr>';
							}
							$products .= '</table>';
							$montant += $price2;
							?>				
						</td>
						<td>
							<?php
							if($discount > 0){
							?>
							<span><ins style="white-space:nowrap;">Montant sans remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+$discount)-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$discount,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] > 0){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
							<?php
							$smpaid = $row['payments'];
							$phtable = "";

							$bankaccounts = "<option value=''>Choisissez un compte banquaire</option>";
							$back1 = $bdd->query("SELECT * FROM bankaccounts WHERE company='".$row['company']."'");
							while($row1 = $back1->fetch()){
								$bankaccounts .= "<option value='".$row1['id']."'>".$row1['bank']." | ".$row1['rib']."</option>";
							}

							// Limits
							$datelimit = time();
							if($row['datepaid'] != ""){
								$datelimit = $row['datepaid'];
							}
							$timetodelay = $row['dateadd']+($row['delay']*24*60*60);
							$resttodelay = ($row['dateadd']+($row['delay']*24*60*60) - $datelimit) / 86400;
							$resttodelay = date_diff(date_create(date("Y-m-d",$timetodelay)),date_create(date("Y-m-d")));
							$color = "#71b44c";
							$background = "transparent";
							$days = $resttodelay->format("%a");
							if($datelimit > $timetodelay){
								$days *= (-1);
							}
							if($days < 0){
								$color = "#FF0000";
							}
							else{
								$days = "+".$days;
							}
							
							if(($row['price']-($row['rastva']+$smpaid+$smavoir)) == $row['price']){
								$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."',datepaid='',datedue='',retardpaid='',dateretard='' WHERE id='".$row['id']."'");
								$req->execute();
								$row['datepaid'] = '';
							}
							elseif(($row['price']-($row['rastva']+$smpaid+$smavoir)) > 0){
								$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."',datepaid='',datedue='',retardpaid='',dateretard='' WHERE id='".$row['id']."'");
								$req->execute();
								$row['datepaid'] = '';
							}
							else{
								$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."' WHERE id='".$row['id']."'");
								$req->execute();
							}
							
							if(($row['price']-($row['rastva']+$smpaid+$smavoir)) == $row['price']){
								$row['state'] = "Non payée";
							}
							elseif(($row['price']-($row['rastva']+$smpaid+$smavoir)) > 0){
								$row['state'] = "Partiellement payée";
							}
							else{
								$row['state'] = 'Payée';
							}
							$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."' WHERE id='".$row['id']."'");
							$req->execute();
							
							$retard = 0;
							if($row['datepaid'] != ""){
								$retard = date_diff(date_create(date("Y-m-d",$timetodelay)),date_create(date("Y-m-d",$row['datepaid'])));
								$retard = $retard->format("%a");
								$req = $bdd->prepare("UPDATE documents SET retard='".$retard."',tva='".$ttva."' WHERE id='".$row['id']."'");
								$req->execute();
							}
							
							if($row['state'] == 'Payée' AND $row['datepaid'] == ""){
								$row['datepaid'] = time();
								$retard = date_diff(date_create(date("Y-m-d",$timetodelay)),date_create(date("Y-m-d",$row['datepaid'])));
								$retard = $retard->format("%a");
								$datedue = strtotime(date('20-m-Y', strtotime('first day of next month',$row['datepaid'])));
								$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."',datepaid='".time()."',datedue='".$datedue."',retard='".$retard."',retardpaid='',dateretard='' WHERE id='".$row['id']."'");
								$req->execute();
								$row['datepaid'] = time();
							}
							
							if($days < 0 AND $row['state'] != "Payée"){
								$background = "#FFC966";
							}
							?>
						<td style="white-space:nowrap;background:<?php echo $background;?>;">
							<span>Délai: <b><?php echo $row['delay'];?> jours</b>
								<?php
								if(preg_match("#Mise à jour délai de paiement factures clients#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit-delay lx-open-popup" data-header="Modifier délai"
								data-id="<?php echo $row['id'];?>"
								data-delay="<?php echo $row['delay'];?>"
								data-table="documents"
								data-title="delay" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a>
								<?php
								}
								?>
							</span>
							<span>Date d'échéance: <br /><b><?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?></b> <strong style="<?php echo $row['state']=="Payée"?"display:none;":"";?>color:<?php echo $color;?>;font-weight:bold;">(<?php echo $days;?>)</strong></span>
						</td>	
						<td>
							<span style="display:inline-block;padding:2px 5px;font-weight:500;background:<?php echo $row['state']=='Payée'?"#71b44c":($row['state']=='Non payée'?"#CC0000":"orange");?>;color:#FFFFFF;border-radius:4px;cursor:pointer;"  
								data-id="<?php echo $row['id'];?>" 
								data-rastva="<?php echo $row['rastva'];?>" 
								data-paid="<?php echo $row['state'];?>" 
								data-rest="<?php echo ($row['price']-($row['rastva']+$smpaid+$smavoir)) > 0 ? number_format((float)($row['price']-($row['rastva']+$smpaid+$smavoir)),2,".","") : "0.00";?>" 
								data-price="<?php echo number_format((float)abs($row['price']),2,".","");?>" 
								data-company="<?php echo $companytxt;?>" 
								data-companyid="<?php echo $row['company'];?>" 
								data-bankaccounts="<?php echo $bankaccounts;?>" 
								data-table="<?php echo str_replace("\"","'",$phtable);?>" class="<?php echo preg_match("#Mise à jour paiement factures clients#",$_SESSION['easybm_roles'])?"lx-edit-payment lx-open-popup":"";?>" 
								data-title="payment"><?php echo $row['state'];?></span>
							<span style="<?php echo ($row['price']-($row['rastva']+$smpaid+$smavoir)) == 0?"display:none;":"";?>"><?php echo ($row['price']-($row['rastva']+$smpaid+$smavoir)) < 0?"À rembourser":"Reste";?>: <b><?php echo number_format((float)($row['price']-($row['rastva']+$smpaid+$smavoir)),2,"."," ")." ".$row['currency'];?></b></span>
							<?php
							if($row['datepaid'] != ""){
							?>
							<span>Payé le: <b><?php echo date("d/m/Y",$row['datepaid']);?></b>
								<?php
								if(preg_match("#Mise à jour paiement factures clients#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit-datepaid lx-open-popup" data-header="Modifier délai"
								data-id="<?php echo $row['id'];?>"
								data-datepaid="<?php echo date("d/m/Y",$row['datepaid']);?>"
								data-table="documents"
								data-title="datepaid" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a>
								<?php
								}
								?>
							</span>
							<?php
							}
							if($row['state']!='Payée' AND preg_match("#Mise à jour paiement factures clients#",$_SESSION['easybm_roles'])){
							?>
							<br />
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Facture"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Recouvrement"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Recouvrement</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Recouvrement" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
							<?php
							}
							if($row['state']=='Payée' AND preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
							?>
							<br />
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Recouvrement" style="display:block;"><i class="fa fa-eye"></i> Recouvrement</a>
							</div>
							<?php							
							}
							?>
						</td>
						<td style="white-space:nowrap;">
							<?php
							if($row['rastva'] > 0 AND $row['datepaid'] != ""){
									?>
							<span style="display:inline-block;padding:2px 5px;font-weight:500;background:<?php echo $row['stateras']=='Payée'?"#71b44c":($row['stateras']=='Non payée'?"#CC0000":"CC0000");?>;color:#FFFFFF;border-radius:4px;cursor:pointer;" 
								data-id="<?php echo $row['id'];?>" 
								data-stateras="<?php echo $row['stateras'];?>" 
								data-datepaidras="<?php echo $row['datepaidras']!=""?date("d/m/Y",$row['datepaidras']):"";?>" 
								data-attachmentras="<?php echo $row['attachmentras'];?>"
								class="<?php echo preg_match("#Mise à jour RAS TVA factures clients#",$_SESSION['easybm_roles'])?"lx-edit-payment-ras lx-open-popup":"";?>" 
								data-title="rastva"><?php echo $row['stateras'];?></span>
								<?php
								if($row['stateras'] == "Non payée"){
								?>
							<span>RAS TAV: <b style="color:#FF0000;"><?php echo $row['rastva'];?> <?php echo $settings['currency'];?></b></span>
								<?php
								}
								?>
							<span>Date d'écheance: <b><?php echo date('20/m/Y', strtotime('first day of next month',$row['datepaid']));?></b></span>
								<?php
								if($row['datepaidras'] != ""){
									$date1 = strtotime(date('20-m-Y', strtotime('first day of next month',$row['datepaid'])));
									$date2 = $row['datepaidras'];
									$resttodelay = date_diff(date_create(date("Y-m-d",$date1)),date_create(date("Y-m-d",$date2)));
									$days = $resttodelay->format("%a");
									?>
							<span>Payé le: <b><?php echo date("d/m/Y",$row['datepaidras']);?></b><a href="javascript:;" 
								data-id="<?php echo $row['id'];?>" 
								data-stateras="<?php echo $row['stateras'];?>" 
								data-datepaidras="<?php echo $row['datepaidras']!=""?date("d/m/Y",$row['datepaidras']):"";?>" 
								data-attachmentras="<?php echo $row['attachmentras'];?>"
								class="<?php echo preg_match("#Mise à jour RAS TVA factures clients#",$_SESSION['easybm_roles'])?"lx-edit-payment-ras lx-open-popup":"";?>" 
								data-title="rastva" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a></span>
									<?php
									if($date2 > $date1){
										$req = $bdd->prepare("UPDATE documents SET retardras='".$days."' WHERE id='".$row['id']."'");
										$req->execute();
										?>
							<span style="color:#FF0000;font-weight:bold;">Avec <?php echo $days;?> jours de retard</span>
										<?php
									}
									if($row['attachmentras'] != ""){
										?>
							<span><b><a href="uploads/<?php echo $row['attachmentras'];?>" target="_blank" style="color:#39add1;"><i class="fa fa-download"></i> Téléchager</a></b></span>
										<?php
									}
								}
								if($row['stateras']!='Payée' AND preg_match("#Mise à jour RAS TVA factures clients#",$_SESSION['easybm_roles'])){
								?>
								<br />
								<div class="lx-upload-photos" style="white-space:nowrap;">
									<?php
									if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
									?>
									<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
										data-id="<?php echo $row['id'];?>"
										data-client="<?php echo $clients[$row['client']]['fullname'];?>"
										data-email1="<?php echo $clients[$row['client']]['email'];?>"
										data-email="<?php echo $clients[$row['client']]['email'];?>"
										data-phone="<?php echo $clients[$row['client']]['phone'];?>"
										data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
										data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
										data-senderphone="<?php echo $user['phone'];?>"
										data-typedoc="Facture"
										data-numero_document="<?php echo $row['code'];?>"
										data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
										data-societe="<?php echo $companytxt;?>"
										data-delai_paiement="<?php echo $row['delay'];?>"
										data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
										data-button="Rappel paiement RAS"
										data-function="<?php echo $user['job'];?>"
										data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Rappel paiement RAS</a>
									<?php
									}
									if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
									?>
									<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
										data-id="<?php echo $row['id'];?>"
										data-page="fc"
										data-button="Rappel paiement RAS" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
									<?php
									}
									?>
								</div>
								<?php
								}
								if($row['stateras']=='Payée' AND preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<br />
								<div class="lx-upload-photos" style="white-space:nowrap;">
									<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
										data-id="<?php echo $row['id'];?>"
										data-page="fc"
										data-button="Rappel paiement RAS" style="display:block;"><i class="fa fa-eye"></i> Rappel paiement RAS</a>
								</div>
								<?php									
								}
							}
							?>
						</td>						
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td>
							<?php
							if($row['abovetable'] != ""){
							?>
							<span style="white-space:nowrap;width:300px;"><strong>Note au-dessus de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['abovetable']);?></span>
							<?php
							}
							if($row['modepayment'] != ""){
							?>
							<span><strong>Note au-dessous de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['modepayment']);?></span>
							<?php
							}
							if($row['conditions'] != ""){
							?>
							<span><strong>Conditions (au-dessous de tableau) :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['conditions']);?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['picture'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['picture'];?>" data-pic="<?php echo $row['picture'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['picture'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<td><span style="white-space:nowrap"><?php echo $user['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="documents" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="documents" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Facture"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-company="<?php echo $row['company'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Envoyer le document"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer le document</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Envoyer le document" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Facture" class="lx-download-file1 lx-print-f lx-print-fc lx-open-popup" data-title="signature">Imprimer</a>
								<?php
								if(preg_match("#Modifier Factures clients#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Modifier" 
									data-id="<?php echo $row['id'];?>"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="command">Modifier</a>
									<?php
								}
								if(preg_match("#Transformation / Dupplication documents#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Transformer / Dupliquer" 
									data-id="0"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",($row['note']));?>"
									data-modepayment="<?php echo str_replace("\"","'",($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",($row['abovetable']));?>" data-title="command">Transformer / Duppliquer</a>
									<?php
								}
								if(((preg_match("#Consulter Factures avoir#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Factures avoir#",$_SESSION['easybm_roles'])) OR (preg_match("#Consulter Bons de retour#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Bons de retour#",$_SESSION['easybm_roles']))) AND $tqtyavoir > 0){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" style="color:#FFA500;" data-table="commands" data-header="Avoir / bon de retour" 
									data-id="-1"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['code'];?>"
									data-note="<?php echo str_replace("\"","'",($row['note']));?>" data-title="command">Avoir / bon de retour</a>
									<?php
								}
								?>
								<a href="javascript:;" class="lx-edit lx-edit-extrainfo lx-open-popup" data-table="extrainfo" data-header="Informations supplémentaires sur le document" 
									data-id="<?php echo $row['id'];?>"
									data-modepayment="<?php echo str_replace("\"","'",($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",($row['abovetable']));?>" data-title="extrainfo">Informations supplémentaires sur le document</a>
								<?php
								if(preg_match("#Supprimer Factures clients#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-command lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Factures avoir / Bons de retour N°</td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails facture</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td>Délai de paiement <i class="fa fa-sort" data-sort="delay"></i></td>
						<td>Statut et historique des paiements <i class="fa fa-sort" data-sort="rest"></i></td>
						<td>RAS TVA <i class="fa fa-sort" data-sort="delayras"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<?php
				$req1 = "SELECT COUNT(id) AS nb FROM documents c WHERE trash='1' AND type='facture'".$multicompanies;
				$req = "";
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['stateras'] != ""){
						if($_POST['stateras'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND stateras NOT IN('Payée') AND ".time()." > datedue AND rastva > 0";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['stateras'])){
								$req .= " AND (stateras IN ('".str_replace("||","','",$_POST['stateras'])."') OR (stateras NOT IN('Payée') AND ".time()." > datedue)) AND rastva > 0";
							}
							else{
								$req .= " AND stateras IN ('".str_replace("||","','",$_POST['stateras'])."') AND rastva > 0";
							}
						}
					}
					if($_POST['statee'] != ""){
						if($_POST['statee'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['statee'])){
								$req .= " AND (state IN ('".str_replace("||","','",$_POST['statee'])."') OR (state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")))";
							}
							else{
								$req .= " AND state IN ('".str_replace("||","','",$_POST['statee'])."')";
							}
						}
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['avoir'] == "1"){
						$req .= " AND (avoirs<>'' OR brs<>'')";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimit'] != "" AND $_POST['dateendlimit'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimit']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimit'])) + (60*60*24) - 1;
						$req .= " AND ((dateadd+(delay*60*60*24)) BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimitras'] != "" AND $_POST['dateendlimitras'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimitras']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimitras'])) + (60*60*24) - 1;
						$req .= " AND (datedue BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$nbcmds = 0.1;
				if($row['nb'] != 0){
					$nbcmds = $row['nb'];
				}
							
				$req1 = "SELECT SUM(price - rastva) AS ca,SUM(payments) AS paid FROM documents c WHERE type='facture'".$multicompanies;
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$ca = $row['ca'];
				$ca1 = $row['ca'];
				$paid = $row['paid'];
					
				$req1 = "SELECT SUM(price - rastva) AS ca,SUM(payments) AS paid FROM documents c WHERE type IN('avoir','br') AND correctdoc IN(SELECT code FROM documents WHERE type='facture'".$multicompanies.")";
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$avoirca = $row['ca'];
				$avoirca1 = $row['ca'];
				$avoirpaid = $row['paid'];
				
				$rest = number_format((float)(isset($ca)?$ca-$avoirca:0),2,".","") - number_format((float)(isset($paid)?$paid-$avoirpaid:0),2,".","");
				if($ca1 == 0){
					$ca1 = 1;
				}
				
				?>
				<div class="lx-caisse-total lx-caisse-total1">
					<h3 style="background:#d9534f;">En cours</h3>
					<p style="padding-bottom:35px;"><?php echo number_format((float)(isset($rest)?$rest:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$rest*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $rest*100/$ca1?>%;background:#d9534f;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total2">
					<h3 style="background:#5cb85c;">Payé</h3>
					<p style="padding-bottom:<?php echo $avoirpaid>0?"0px":"35px";?>;"><?php echo number_format((float)(isset($paid)?$paid:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<p style="display:<?php echo $avoirpaid>0?"block":"none";?>;padding:0px;padding-bottom:20px;maring:0px;margin-top:5px;text-align:center;font-size:11px;font-style:italic;font-weight:normal;">-<?php echo number_format((float)(isset($avoirpaid)?$avoirpaid:0),2,"."," ")." ".$settings['currency'];?> TTC (AV/BR)</p>
					<ins><?php echo number_format((float)$paid*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $paid*100/$ca1?>%;background:#5cb85c;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total3">
					<h3 style="background:#337ab7;">Chiffre d'affaires</h3>
					<p style="padding-bottom:<?php echo $avoirca>0?"0px":"35px";?>;"><?php echo number_format((float)(isset($ca)?$ca:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<p style="display:<?php echo $avoirca>0?"block":"none";?>;padding:0px;padding-bottom:20px;maring:0px;margin-top:5px;text-align:center;font-size:11px;font-style:italic;font-weight:normal;">-<?php echo number_format((float)(isset($avoirca)?$avoirca:0),2,"."," ")." ".$settings['currency'];?> TTC (AV/BR)</p>
					<ins><?php echo number_format((float)$ca*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $ca*100/$ca1?>%;background:#337ab7;"></ins>
					</h4>
				</div>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> facture(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> facture(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
				$req1 = "SELECT SUM(price) AS price,SUM(tva) AS tva,SUM(rastva) AS rastva FROM documents c WHERE type='facture' AND trash='1'".$multicompanies;
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				?>
				<div class="lx-caisse-total4">
					<div style="float:left;margin-right:60px;">
						<p>* Total Montant HT: <b><?php echo number_format((float)$row['price']-$row['tva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total TVA: <b><?php echo number_format((float)$row['tva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total RAS TVA: <b style="color:#FF0000;"><?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total Montant TTC: <b><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></p>
					</div>
				</div>
				<?php
			}
			
			if($_POST['action'] == "loadavoirs"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Corrige la facture N°</td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails avoir</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td>Statut et historique des remboursement <i class="fa fa-sort" data-sort="rest"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = $bdd->prepare("DELETE FROM documents WHERE price=0");
					$req->execute();
					$req = "SELECT * FROM documents d WHERE category='client' AND type='avoir' AND price>0 AND trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR correctdoc LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['statee'] != ""){
						$req .= " AND state IN ('".str_replace("||","','",$_POST['statee'])."')";
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$montant = 0;
					$factures = [];
					while($row = $back->fetch()){
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();	

						$back1 = $bdd->query("SELECT * FROM prospects WHERE id='".$row['prospect']."'");
						$prospect = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facture" value="<?php echo $row['id'];?>" 
						data-client="<?php echo $row['client'];?>" 
						data-prospect="<?php echo $row['prospect'];?>" 
						data-code="<?php echo $row['code'];?>" 
						data-company="<?php echo $row['company'];?>" 
						data-discounttype="<?php echo $row['discounttype'];?>" 
						data-price="<?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ")." ".$settings['currency'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<?php
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];
						?>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>
						<td><a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Facture avoir" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['code'];?></a></td>
						<td>
							<?php
							$facturepaid = 0;
							$avoirnative = 1;
							$back1 = $bdd->query("SELECT id,code,price,payments,type FROM documents WHERE code='".$row['correctdoc']."' AND company='".$row['company']."'");
							if($back1->rowCount() > 0){
								$rowfactures = $back1->fetch();
								?>
							<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $rowfactures['id'];?>&title=<?php echo $rowfactures['type']=="facture"?"Facture":"Note d\'honoraire";?>" class="lx-download-file1 lx-print-f lx-print-fc lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['correctdoc'];?></a>
								<?php
								$restfacture = $rowfactures['price'] - $rowfactures['payments'];
								if(!isset($factures[$rowfactures['code']])){
									$factures[$rowfactures['code']] = $restfacture;
								}
								$facturepaid = $factures[$rowfactures['code']] > 0?$factures[$rowfactures['code']]:0;
							}
							else{
								$rowfactures['code'] = "nofacture";
								$restfacture = 0;
								$avoirnative = 0;
								?>
							<span><?php echo $row['correctdoc'];?></span>
								<?php								
							}
							?>
						</td>
						<td>
							<span style="white-space:nowrap;"><?php echo $clients[$row['client']]['fullname']." (".($clients[$row['client']]['codecl']!=""?$clients[$row['client']]['codecl']:$clients[$row['client']]['code']).")";?></span>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td>
							<?php
							$products = '<table>
											<tr class="lx-firstrow">
												<td style="width:500px;">Designation</td>
												<td>Qté</td>
												<td style="width:100px;">P.U. HT</td>
												<td class="lx-discount">Remise totale HT</td>
												<td>TVA</td>
												<td>Total HT</td>
												<td class="lx-qty-back">Qté à retourner</td>
												<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
												<td class="lx-note-cell" style="display:none;">Description</td>
												<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
											</tr>
											<tr class="lx-secondrow" style="display:none;">
												<td colspan="10">
													<center><i>0 produits / services</i></center>
												</td>
											</tr>';
							$price1 = 0;
							$price2 = 0;
							$discount = 0;
							$discounttype = "";
							$ttva = 0;
							$titles = explode("||",$row['title']);
							$units = explode("||",$row['unit']);
							$qtys = array_map('floatval',explode("||",$row['qty']));
							$uprices = array_map('floatval',explode("||",$row['uprice']));
							$tprices = array_map('floatval',explode("||",$row['tprice']));
							$ptvas = array_map('floatval',explode("||",$row['ptva']));
							$abovenotes = explode("||",$row['abovenote']);
							$belownotes = explode("||",$row['belownote']);
							$discounttypes = explode("||",$row['discounttype']);
							$discounts = array_map('floatval',explode("||",$row['discount']));
							for($i=0;$i<count($titles);$i++){
								if($qtys[$i] > 0){
								$d = 0;
								if($discounttypes[$i] == $settings['currency']){
									$discount += $discounts[$i];
									$d = $discounts[$i];
								}
								else{
									$discount += (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
									$d = (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
								}
								?>
							<span style="padding-bottom:8px;border-bottom:1px solid #ccc;"><?php echo $titles[$i]."<br />Prix unitaire: <b>".number_format((float)($uprices[$i]*(1+($ptvas[$i]/100))),2,"."," ")." ".$settings['currency']."</b> TTC <br />Quantité: <b>".$qtys[$i]." ".$units[$i]."</b>".($d!="0"?"<br />Remise totale HT: <b>".number_format((float)$d,2,"."," ")." ".$settings['currency']."</b>":"");?></span>
								<?php
								$price1 += (($tprices[$i]+$d)*(1+($ptvas[$i]/100)));
								$price2 += ($tprices[$i]*(1+($ptvas[$i]/100)));
								$ttva += ($tprices[$i]*($ptvas[$i]/100));
								$discounttype = $discounttypes[$i];
								$products .= '<tr class="lx-otherrow" data-title="" data-abovenote="'.htmlspecialchars($abovenotes[$i]).'" data-belownote="'.htmlspecialchars($belownotes[$i]).'" data-qty="'.$qtys[$i].'" data-unit="'.$units[$i].'" data-uprice="'.$uprices[$i].'" data-remise="'.$d.'" data-oremise="'.$d.'" data-tva="'.$ptvas[$i].'" data-tprice="'.($uprices[$i]*$qtys[$i]).'" data-ttprice="'.$tprices[$i].'">
												<td style="width:auto;" class="lx-product-title">'.$titles[$i].'</td>
												<td><span>'.$qtys[$i].' '.$units[$i].'</span></td>
												<td><span>'.number_format((float)$uprices[$i],2,"."," ").'</span></td>
												<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" value="'.$discounts[$i].'" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;">'.$discounttype.'</u></label></div></td>
												<td><span>'.$ptvas[$i].'%</span></td>
												<td><span>'.number_format((float)$tprices[$i],2,"."," ").'</span></td>
												<td class="lx-qty-back"><div class="lx-textfield"><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>
												<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>
												<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>
												<td class="lx-delete-cell">
												<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>
												<div class="lx-delete-payment-choice">
												<a href="javascript:;" class="lx-delete-this">Oui</a>
												<a href="javascript:;" class="lx-no-delete-payment">Non</a>
												</div>
												</td>
											</tr>';
								}
							}
							$products .= '</table>';
							$montant += $price2;
							?>				
						</td>
						<td>
							<?php
							if($discount > 0){
							?>
							<span><ins style="white-space:nowrap;">Montant sans remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+$discount)-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$discount,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] > 0){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
						<td>
							<?php
							$smpaid = $row['payments'];
							$phtable = "";
							
							$bankaccounts = "<option value=''>Choisissez un compte banquaire</option>";
							$back1 = $bdd->query("SELECT * FROM bankaccounts WHERE company='".$row['company']."'");
							while($row1 = $back1->fetch()){
								$bankaccounts .= "<option value='".$row1['id']."'>".$row1['bank']." | ".$row1['rib']."</option>";
							}
							
							if(($row['price']-($smpaid+$facturepaid)) == $row['price']){
								$row['state'] = "Non remboursée";
							}
							elseif(($row['price']-($smpaid+$facturepaid)) > 0){
								$row['state'] = "Partiellement remboursée";
							}
							else{
								$row['state'] = 'Remboursée';
							}
							$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."' WHERE id='".$row['id']."'");
							$req->execute();
							?>
							<span style="display:inline-block;padding:2px 5px;font-weight:500;background:<?php echo $row['state']=='Remboursée'?"#71b44c":($row['state']=='Non remboursée'?"#CC0000":"orange");?>;color:#FFFFFF;border-radius:4px;cursor:pointer;" 
								data-id="<?php echo $row['id'];?>" 
								data-paid="<?php echo $row['state'];?>" 
								data-rest="<?php echo (($row['price'] - ($smpaid + $facturepaid)) > 0)?number_format((float)$row['price'] - ($smpaid + $facturepaid),2,".",""):0;?>" 
								data-price="<?php echo number_format((float)$row['price'],2,".","");?>" 
								data-company="<?php echo $companytxt;?>" 
								data-companyid="<?php echo $company;?>" 
								data-bankaccounts="<?php echo $bankaccounts;?>" 
								data-table="<?php echo str_replace("\"","'",$phtable);?>" class="<?php echo preg_match("#Mise à jour paiement factures avoirs \(remboursement\)#",$_SESSION['easybm_roles'])?"lx-edit-payment lx-open-popup":"";?>" 
								data-title="payment"><?php echo $row['state'];?></span>
								<?php
							if($row['price'] - ($smpaid + $facturepaid) > 0){
								?>
							<span>Reste: <b><?php echo number_format((float)$row['price']-($smpaid+$facturepaid),2,"."," ")." ".$settings['currency'];?></b></span>
								<?php
							}
							$factures[$rowfactures['code']] = isset($factures[$rowfactures['code']])?$factures[$rowfactures['code']] - $row['price']:$restfacture;
							?>
						</td>
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td>
							<?php
							if($row['abovetable'] != ""){
							?>
							<span style="white-space:nowrap;width:300px;"><strong>Note au-dessus de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['abovetable']);?></span>
							<?php
							}
							if($row['modepayment'] != ""){
							?>
							<span><strong>Note au-dessous de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['modepayment']);?></span>
							<?php
							}
							if($row['conditions'] != ""){
							?>
							<span><strong>Conditions (au-dessous de tableau) :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['conditions']);?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['picture'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['picture'];?>" data-pic="<?php echo $row['picture'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['picture'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<?php
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$row1 = $back1->fetch();
						?>
						<td><span style="white-space:nowrap"><?php echo $row1['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="documents" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="documents" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Facture avoir"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-company="<?php echo $row['company'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Envoyer le document"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer le document</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Envoyer le document" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Facture avoir" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature">Imprimer</a>
								<?php
								if(preg_match("#Modifier Factures avoir#",$_SESSION['easybm_roles'])){
									if($avoirnative == 0){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Modifier" 
									data-id="<?php echo $row['id'];?>"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-avoirnative="<?php echo $avoirnative;?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="<?php echo $avoirnative==1?'':'command';?>">Modifier</a>
									<?php
									}
									else{
									?>
								<a href="javascript:;" class="lx-edit lx-open-popup" data-title="avoirnative">Modifier</a>
									<?php
									}
								}
								if(preg_match("#Transformation / Dupplication documents#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Transformer / Dupliquer" 
									data-id="0"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-avoirnative="0"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="command">Transformer / Duppliquer</a>
									<?php
								}
								?>
								<a href="javascript:;" class="lx-edit lx-edit-extrainfo lx-open-popup" data-table="extrainfo" data-header="Informations supplémentaires sur le document" 
									data-id="<?php echo $row['id'];?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="extrainfo">Informations supplémentaires sur le document</a>
								<?php
								if(preg_match("#Supprimer Factures avoir#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-command lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Corrige la facture N°</td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails avoir</td>
						<td style="text-align:right;"><?php echo number_format((float)$montant,2,"."," ")." ".$settings['currency'];?> TTC <i class="fa fa-sort" data-sort="price"></i></td>
						<td>Statut et historique des remboursements <i class="fa fa-sort" data-sort="rest"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<?php
				$req1 = "SELECT COUNT(id) AS nb FROM documents c WHERE trash='1' AND type='avoir'".$multicompanies;
				$req = "";
				if($_POST['keyword'] != ""){
					$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
									OR note LIKE '".normalizeString($_POST['keyword'])."' 
									OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
									OR conditions LIKE '".normalizeString($_POST['keyword'])."'
									OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
				}
				if($_POST['company'] != ""){
					$comp = explode("||",$_POST['company']);
					$req .= " AND (";
					for($j=0;$j<count($comp);$j++){
						if($j==0){
							$req .= "FIND_IN_SET(".$comp[$j].", company)";
						}
						else{
							$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
						}
					}
					$req .= ")";
				}
				if($_POST['client'] != ""){
					$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
				}
				if($_POST['user'] != ""){
					$req .= " AND user IN(".$_POST['user'].")";
				}
				if($_POST['product'] != ""){
					$comp = explode("||",$_POST['product']);
					$req .= " AND (";
					for($j=0;$j<count($comp);$j++){
						if($j==0){
							$req .= "title LIKE '%".addslashes($comp[$j])."%'";
						}
						else{
							$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
						}
					}
					$req .= ")";
				}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
				if($_POST['statee'] != ""){
					$req .= " AND state IN ('".str_replace("||","','",$_POST['statee'])."')";
				}
				if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
					$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
				}
				if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
					$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
					$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
					$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
				}
				if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
					$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
					$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
					$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
				}
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$nbcmds = 0.1;
				if($row['nb'] != 0){
					$nbcmds = $row['nb'];
				}
								
				$req1 = "SELECT SUM(price - rastva) AS ca,SUM(payments) AS paid FROM documents c WHERE type='avoir'".$multicompanies;
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$ca = $row['ca'];
				$ca1 = $row['ca'];
				$paid = $row['paid'];

				$req1 = "SELECT SUM(price - rastva) AS ca,SUM(payments) AS paid FROM documents c WHERE type IN('facture','nh','nf') AND code IN(SELECT correctdoc FROM documents WHERE type='avoir'".$multicompanies.")";
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$factureca = $row['ca'];
				$factureca1 = $row['ca'];
				$facturepaid = $row['paid'];
				
				$rest = $ca - ($factureca - $facturepaid) - $paid;
				if($ca1 == 0){
					$ca1 = 1;
				}
				?>
				<div class="lx-caisse-total lx-caisse-total1">
					<h3 style="background:#d9534f;">À rembourser</h3>
					<p><?php echo number_format((float)($rest>0?$rest:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)($rest>0?$rest:0)*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $rest*100/$ca1?>%;background:#d9534f;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total3">
					<h3 style="background:#337ab7;">Total</h3>
					<p><?php echo number_format((float)(isset($ca)?$ca:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$ca*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $ca*100/$ca1?>%;background:#337ab7;"></ins>
					</h4>
				</div>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> facture(s) avoir de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> facture(s) avoir de <?php echo $back4->rowCount();?></p>
				<?php			
				}
				$req1 = "SELECT SUM(price) AS price,SUM(tva) AS tva,SUM(rastva) AS rastva FROM documents c WHERE type='avoir' AND trash='1'".$multicompanies;
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				?>
				<div class="lx-caisse-total4">
					<div style="float:left;margin-right:60px;">
						<p>* Total Montant HT: <b><?php echo number_format((float)$row['price']-$row['tva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total TVA: <b><?php echo number_format((float)$row['tva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total Montant TTC: <b><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></p>
					</div>
				</div>
				<?php
			}

			if($_POST['action'] == "loadbr"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Corrige la facture N°</td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails bon de retour</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM documents d WHERE category='client' AND type='br' AND trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR correctdoc LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$montant = 0;
					while($row = $back->fetch()){
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();	

						$back1 = $bdd->query("SELECT * FROM prospects WHERE id='".$row['prospect']."'");
						$prospect = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facture" value="<?php echo $row['id'];?>" 
						data-client="<?php echo $row['client'];?>" 
						data-prospect="<?php echo $row['prospect'];?>"
						data-code="<?php echo $row['code'];?>" 
						data-company="<?php echo $row['company'];?>" 
						data-discounttype="<?php echo $row['discounttype'];?>" 
						data-price="<?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ")." ".$settings['currency'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<?php
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];
						?>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>
						<td><a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Bon de retour" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['code'];?></a></td>
						<td>
							<?php
							$avoirnative = 1;
							$back1 = $bdd->query("SELECT id,code FROM documents WHERE code='".$row['correctdoc']."' AND company='".$row['company']."'");
							if($back1->rowCount() > 0){
								$row1 = $back1->fetch();
								?>
							<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Facture" class="lx-download-file1 lx-print-f lx-print-fc lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['correctdoc'];?></a>
								<?php
							}
							else{
								$avoirnative = 0;
								?>
							<span><?php echo $row['correctdoc'];?></span>
								<?php								
							}
							?>						
						</td>
						<td>
							<span style="white-space:nowrap;"><?php echo $clients[$row['client']]['fullname']." (".($clients[$row['client']]['codecl']!=""?$clients[$row['client']]['codecl']:$clients[$row['client']]['code']).")";?></span>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td>
							<?php
							$products = '<table>
											<tr class="lx-firstrow">
												<td style="width:500px;">Designation</td>
												<td>Qté</td>
												<td style="width:100px;">P.U. HT</td>
												<td class="lx-discount">Remise totale HT</td>
												<td>TVA</td>
												<td>Total HT</td>
												<td class="lx-qty-back">Qté à retourner</td>
												<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
												<td class="lx-note-cell" style="display:none;">Description</td>
												<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
											</tr>
											<tr class="lx-secondrow" style="display:none;">
												<td colspan="10">
													<center><i>0 produits / services</i></center>
												</td>
											</tr>';
							$price1 = 0;
							$price2 = 0;
							$discount = 0;
							$discounttype = "";
							$ttva = 0;
							$titles = explode("||",$row['title']);
							$units = explode("||",$row['unit']);
							$qtys = array_map('floatval',explode("||",$row['qty']));
							$uprices = array_map('floatval',explode("||",$row['uprice']));
							$tprices = array_map('floatval',explode("||",$row['tprice']));
							$ptvas = array_map('floatval',explode("||",$row['ptva']));
							$abovenotes = explode("||",$row['abovenote']);
							$belownotes = explode("||",$row['belownote']);
							$discounttypes = explode("||",$row['discounttype']);
							$discounts = array_map('floatval',explode("||",$row['discount']));
							for($i=0;$i<count($titles);$i++){
								if($qtys[$i] > 0){
								$d = 0;
								if($discounttypes[$i] == $settings['currency']){
									$discount += $discounts[$i];
									$d = $discounts[$i];
								}
								else{
									$discount += (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
									$d = (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
								}
								?>
							<span style="padding-bottom:8px;border-bottom:1px solid #ccc;"><?php echo $titles[$i]."<br />Prix unitaire: <b>".number_format((float)($uprices[$i]*(1+($ptvas[$i]/100))),2,"."," ")." ".$settings['currency']."</b> TTC <br />Quantité: <b>".$qtys[$i]." ".$units[$i]."</b>".($d!="0"?"<br />Remise totale HT: <b>".number_format((float)$d,2,"."," ")." ".$settings['currency']."</b>":"");?></span>
								<?php
								$price1 += (($tprices[$i]+$d)*(1+($ptvas[$i]/100)));
								$price2 += ($tprices[$i]*(1+($ptvas[$i]/100)));
								$ttva += ($tprices[$i]*($ptvas[$i]/100));
								$discounttype = $discounttypes[$i];
								$products .= '<tr class="lx-otherrow" data-title="" data-abovenote="'.htmlspecialchars($abovenotes[$i]).'" data-belownote="'.htmlspecialchars($belownotes[$i]).'" data-qty="'.$qtys[$i].'" data-unit="'.$units[$i].'" data-uprice="'.$uprices[$i].'" data-remise="'.$d.'" data-oremise="'.$d.'" data-tva="'.$ptvas[$i].'" data-tprice="'.($uprices[$i]*$qtys[$i]).'" data-ttprice="'.$tprices[$i].'">
												<td style="width:auto;" class="lx-product-title">'.$titles[$i].'</td>
												<td><span>'.$qtys[$i].' '.$units[$i].'</span></td>
												<td><span>'.number_format((float)$uprices[$i],2,"."," ").'</span></td>
												<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" value="'.$discounts[$i].'" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;">'.$discounttype.'</u></label></div></td>
												<td><span>'.$ptvas[$i].'%</span></td>
												<td><span>'.number_format((float)$tprices[$i],2,"."," ").'</span></td>
												<td class="lx-qty-back"><div class="lx-textfield"><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>
												<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>
												<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>
												<td class="lx-delete-cell">
												<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>
												<div class="lx-delete-payment-choice">
												<a href="javascript:;" class="lx-delete-this">Oui</a>
												<a href="javascript:;" class="lx-no-delete-payment">Non</a>
												</div>
												</td>
											</tr>';
								}
							}
							$products .= '</table>';
							$montant += $price2;
							?>				
						</td>
						<td>
							<?php
							if($discount > 0){
							?>
							<span><ins style="white-space:nowrap;">Montant sans remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+$discount)-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$discount,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] > 0){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td>
							<?php
							if($row['abovetable'] != ""){
							?>
							<span style="white-space:nowrap;width:300px;"><strong>Note au-dessus de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['abovetable']);?></span>
							<?php
							}
							if($row['modepayment'] != ""){
							?>
							<span><strong>Note au-dessous de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['modepayment']);?></span>
							<?php
							}
							if($row['conditions'] != ""){
							?>
							<span><strong>Conditions (au-dessous de tableau) :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['conditions']);?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['picture'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['picture'];?>" data-pic="<?php echo $row['picture'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['picture'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<?php
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$row1 = $back1->fetch();
						?>
						<td><span style="white-space:nowrap"><?php echo $row1['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="documents" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="documents" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Bon de retour"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-company="<?php echo $row['company'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Envoyer le document"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer le document</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Envoyer le document" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Bon de retour" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature">Imprimer</a>
								<?php
								if(preg_match("#Modifier Factures avoir#",$_SESSION['easybm_roles'])){
									if($avoirnative == 0){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Modifier" 
									data-id="<?php echo $row['id'];?>"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-avoirnative="<?php echo $avoirnative;?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="<?php echo $avoirnative==1?'':'command';?>">Modifier</a>
									<?php
									}
									else{
									?>
								<a href="javascript:;" class="lx-edit lx-open-popup" data-title="avoirnative">Modifier</a>
									<?php
									}
								}
								if(preg_match("#Transformation / Dupplication documents#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Transformer / Dupliquer" 
									data-id="0"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="command">Transformer / Duppliquer</a>
									<?php
								}
								?>
								<a href="javascript:;" class="lx-edit lx-edit-extrainfo lx-open-popup" data-table="extrainfo" data-header="Informations supplémentaires sur le document" 
									data-id="<?php echo $row['id'];?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="extrainfo">Informations supplémentaires sur le document</a>
								<?php
								if(preg_match("#Supprimer Factures avoir#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-command lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Corrige la facture N°</td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails bon de retour</td>
						<td style="text-align:right;"><?php echo number_format((float)$montant,2,"."," ")." ".$settings['currency'];?> TTC <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> facture(s) avoir de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> facture(s) avoir de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}

			if($_POST['action'] == "loaddevis"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Client / Prospect<i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails devis</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM documents d WHERE category='client' AND type='devis' AND trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['prospect'] != ""){
						$req .= " AND prospect IN(".str_replace("||",",",$_POST['prospect']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$montant = 0;
					while($row = $back->fetch()){
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();	

						$back1 = $bdd->query("SELECT * FROM prospects WHERE id='".$row['prospect']."'");
						$prospect = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facture" value="<?php echo $row['id'];?>" 
						data-client="<?php echo $row['client'];?>" 
						data-prospect="<?php echo $row['prospect'];?>" 
						data-code="<?php echo $row['code'];?>" 
						data-company="<?php echo $row['company'];?>" 
						data-discounttype="<?php echo $row['discounttype'];?>" 
						data-price="<?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ")." ".$settings['currency'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<?php
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];
						?>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>
						<td><a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Devis" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['code'];?></a></td>
						<td>
							<?php
							if($row['client'] > 0){
								?>
							<span style="white-space:nowrap;"><?php echo $clients[$row['client']]['fullname']." (".($clients[$row['client']]['codecl']!=""?$clients[$row['client']]['codecl']:$clients[$row['client']]['code']).")";?></span>
								<?php
							}
							elseif($row['prospect'] > 0){
								?>
							<span style="white-space:nowrap;"><?php echo $prospect['fullname']." (Prospect)";?></span>
								<?php
							}
							?>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td>
							<?php
							$products = '<table>
											<tr class="lx-firstrow">
												<td style="width:500px;">Designation</td>
												<td>Qté</td>
												<td style="width:100px;">P.U. HT</td>
												<td class="lx-discount">Remise totale HT</td>
												<td>TVA</td>
												<td>Total HT</td>
												<td class="lx-qty-back">Qté à retourner</td>
												<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
												<td class="lx-note-cell" style="display:none;">Description</td>
												<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
											</tr>
											<tr class="lx-secondrow" style="display:none;">
												<td colspan="10">
													<center><i>0 produits / services</i></center>
												</td>
											</tr>';
							$price1 = 0;
							$price2 = 0;
							$discount = 0;
							$discounttype = "";
							$ttva = 0;
							$titles = explode("||",$row['title']);
							$units = explode("||",$row['unit']);
							$qtys = array_map('floatval',explode("||",$row['qty']));
							$uprices = array_map('floatval',explode("||",$row['uprice']));
							$tprices = array_map('floatval',explode("||",$row['tprice']));
							$ptvas = array_map('floatval',explode("||",$row['ptva']));
							$abovenotes = explode("||",$row['abovenote']);
							$belownotes = explode("||",$row['belownote']);
							$discounttypes = explode("||",$row['discounttype']);
							$discounts = array_map('floatval',explode("||",$row['discount']));
							for($i=0;$i<count($titles);$i++){
								$d = 0;
								if($discounttypes[$i] == $settings['currency']){
									$discount += $discounts[$i];
									$d = $discounts[$i];
								}
								else{
									$discount += (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
									$d = (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
								}
								?>
							<span style="padding-bottom:8px;border-bottom:1px solid #ccc;"><?php echo $titles[$i]."<br />Prix unitaire: <b>".number_format((float)($uprices[$i]*(1+($ptvas[$i]/100))),2,"."," ")." ".$settings['currency']."</b> TTC <br />Quantité: <b>".$qtys[$i]." ".$units[$i]."</b>".($d!="0"?"<br />Remise totale HT: <b>".number_format((float)$d,2,"."," ")." ".$settings['currency']."</b>":"");?></span>
								<?php
								$price1 += (($tprices[$i]+$d)*(1+($ptvas[$i]/100)));
								$price2 += ($tprices[$i]*(1+($ptvas[$i]/100)));
								$ttva += ($tprices[$i]*($ptvas[$i]/100));
								$discounttype = $discounttypes[$i];
								$products .= '<tr class="lx-otherrow" data-title="" data-abovenote="'.htmlspecialchars($abovenotes[$i]).'" data-belownote="'.htmlspecialchars($belownotes[$i]).'" data-qty="'.$qtys[$i].'" data-unit="'.$units[$i].'" data-uprice="'.$uprices[$i].'" data-remise="'.$d.'" data-oremise="'.$d.'" data-tva="'.$ptvas[$i].'" data-tprice="'.($uprices[$i]*$qtys[$i]).'" data-ttprice="'.$tprices[$i].'">
												<td style="width:auto;" class="lx-product-title">'.$titles[$i].'</td>
												<td><span>'.$qtys[$i].' '.$units[$i].'</span></td>
												<td><span>'.number_format((float)$uprices[$i],2,"."," ").'</span></td>
												<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" value="'.$discounts[$i].'" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;">'.$discounttype.'</u></label></div></td>
												<td><span>'.$ptvas[$i].'%</span></td>
												<td><span>'.number_format((float)$tprices[$i],2,"."," ").'</span></td>
												<td class="lx-qty-back"><div class="lx-textfield"><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>
												<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>
												<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>
												<td class="lx-delete-cell">
												<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>
												<div class="lx-delete-payment-choice">
												<a href="javascript:;" class="lx-delete-this">Oui</a>
												<a href="javascript:;" class="lx-no-delete-payment">Non</a>
												</div>
												</td>
											</tr>';
							}
							$products .= '</table>';
							$montant += $price2;
							?>				
						</td>
						<td>
							<?php
							if($discount > 0){
							?>
							<span><ins style="white-space:nowrap;">Montant sans remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+$discount)-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$discount,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] > 0){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td>
							<?php
							if($row['abovetable'] != ""){
							?>
							<span style="white-space:nowrap;width:300px;"><strong>Note au-dessus de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['abovetable']);?></span>
							<?php
							}
							if($row['modepayment'] != ""){
							?>
							<span><strong>Note au-dessous de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['modepayment']);?></span>
							<?php
							}
							if($row['conditions'] != ""){
							?>
							<span><strong>Conditions (au-dessous de tableau) :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['conditions']);?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['picture'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['picture'];?>" data-pic="<?php echo $row['picture'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['picture'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<?php
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$row1 = $back1->fetch();
						?>
						<td><span style="white-space:nowrap"><?php echo $row1['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="documents" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="documents" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									<?php
									if($row['client'] > 0){
										?>
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
										<?php
									}									
									elseif($row['prospect'] > 0){
										?>
									data-client="<?php echo $prospect['fullname'];?>"
									data-email1="<?php echo $prospect['email'];?>"
									data-email="<?php echo $prospect['email'];?>"
									data-phone="<?php echo $prospect['phone'];?>"
									data-whatsapp="<?php echo $prospect['whatsapp'];?>"
										<?php
									}
									?>
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Devis"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-company="<?php echo $row['company'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Envoyer le document"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer le document</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Envoyer le document" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Devis" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature">Imprimer</a>
								<?php
								if(preg_match("#Modifier Devis,#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Modifier" 
									data-id="<?php echo $row['id'];?>"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="command">Modifier</a>
									<?php
								}
								if(preg_match("#Transformation / Dupplication documents#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Transformer / Dupliquer" 
									data-id="0"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="command">Transformer / Duppliquer</a>
									<?php
								}
								?>
								<a href="javascript:;" class="lx-edit lx-edit-extrainfo lx-open-popup" data-table="extrainfo" data-header="Informations supplémentaires sur le document" 
									data-id="<?php echo $row['id'];?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="extrainfo">Informations supplémentaires sur le document</a>
								<?php
								if(preg_match("#Supprimer Devis,#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-command lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Client / Prospect<i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails devis</td>
						<td style="text-align:right;"><?php echo number_format((float)$montant,2,"."," ")." ".$settings['currency'];?> TTC <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> devis(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> devis(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}
		
			if($_POST['action'] == "loadnh"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Factures avoir / Bons de retour N°</td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails note d'honoraire</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td>Délai de paiement <i class="fa fa-sort" data-sort="delay"></i></td>
						<td>Statut et historique des paiements <i class="fa fa-sort" data-sort="rest"></i></td>
						<td>RAS TVA <i class="fa fa-sort" data-sort="delayras"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM documents d WHERE category='client' AND type='nh' AND trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['stateras'] != ""){
						if($_POST['stateras'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND stateras NOT IN('Payée') AND ".time()." > datedue AND rastva > 0";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['stateras'])){
								$req .= " AND (stateras IN ('".str_replace("||","','",$_POST['stateras'])."') OR (stateras NOT IN('Payée') AND ".time()." > datedue)) AND rastva > 0";
							}
							else{
								$req .= " AND stateras IN ('".str_replace("||","','",$_POST['stateras'])."') AND rastva > 0";
							}
						}
					}
					if($_POST['statee'] != ""){
						if($_POST['statee'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['statee'])){
								$req .= " AND (state IN ('".str_replace("||","','",$_POST['statee'])."') OR (state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")))";
							}
							else{
								$req .= " AND state IN ('".str_replace("||","','",$_POST['statee'])."')";
							}
						}
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['avoir'] == "1"){
						$req .= " AND (avoirs<>'' OR brs<>'')";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimit'] != "" AND $_POST['dateendlimit'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimit']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimit'])) + (60*60*24) - 1;
						$req .= " AND ((dateadd+(delay*60*60*24)) BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimitras'] != "" AND $_POST['dateendlimitras'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimitras']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimitras'])) + (60*60*24) - 1;
						$req .= " AND (datedue BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "delay"){
							$req .= " ORDER BY (dateadd + (delay * 60 * 60 * 24))";
						}
						elseif($_POST['sortby'] == "delayras"){
							$req .= " ORDER BY (datepaid + ".secondsUntilNext20th().")";
						}
						elseif($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$montant = 0;
					while($row = $back->fetch()){
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();

						$back1 = $bdd->query("SELECT * FROM prospects WHERE id='".$row['prospect']."'");
						$prospect = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facture" value="<?php echo $row['id'];?>" 
						data-client="<?php echo $row['client'];?>" 
						data-prospect="<?php echo $row['prospect'];?>" 
						data-code="<?php echo $row['code'];?>" 
						data-company="<?php echo $row['company'];?>" 
						data-discounttype="<?php echo $row['discounttype'];?>" 
						data-price="<?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ")." ".$settings['currency'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<?php
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];
						?>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>
						<td><a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Note d'honoraire" class="lx-download-file1 lx-print-f lx-print-fc lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['code'];?></a></td>
						<td>
							<?php
							$smavoir = 0;
							if($row['avoirs'] != ""){
								?>
							<span>Avoirs:</span>
								<?php
								$avoirs = explode("|",$row['avoirs']);
								for($i=0;$i<count($avoirs);$i++){
									$avoirdetails = explode("/",$avoirs[$i]);
									if($avoirdetails[2] > 0){
										?>
							<ins style="white-space:nowrap;"><a href="javascript:;" data-id="<?php echo $avoirdetails[0];?>" data-href="printclientdocs.php?id=<?php echo $avoirdetails[0];?>&title=Avoir" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature" style="color:#39add1;white-space:nowrap;"><?php echo $avoirdetails[1];?></a> (<?php echo $avoirdetails[2]." ".$settings['currency'];?> TTC)<ins><br />
										<?php
										$smavoir += $avoirdetails[2];
									}
								}
							}
							if($row['brs'] != ""){
								?>
							<br /><span>Bons de retour:</span>
								<?php
								$brs = explode("|",$row['brs']);
								for($i=0;$i<count($brs);$i++){
									$brdetails = explode("/",$brs[$i]);
									if($brdetails[2] > 0){
										?>
							<ins style="white-space:nowrap;"><a href="javascript:;" data-id="<?php echo $brdetails[0];?>" data-href="printclientdocs.php?id=<?php echo $brdetails[0];?>&title=Bon de retour" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature" style="color:#39add1;white-space:nowrap;"><?php echo $brdetails[1];?></a> (<?php echo $brdetails[2]." ".$settings['currency'];?> TTC)<ins><br />
										<?php
										$smavoir += $brdetails[2];
									}
								}
							}
							$back1 = $bdd->query("SELECT SUM(payments) AS sm FROM documents WHERE correctdoc='".$row['code']."'");
							$row1 = $back1->fetch();	
							if($row['avoirs'] != "" OR $row['brs'] != ""){
								$smavoir -= $row1['sm'];
							}
							?>	
						</td>
						<td>
							<span style="white-space:nowrap;"><?php echo $clients[$row['client']]['fullname']." (".($clients[$row['client']]['codecl']!=""?$clients[$row['client']]['codecl']:$clients[$row['client']]['code']).")";?></span>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td>
							<?php
							$products = '<table>
											<tr class="lx-firstrow">
												<td style="width:500px;">Designation</td>
												<td>Qté</td>
												<td style="width:100px;">P.U. HT</td>
												<td class="lx-discount">Remise totale HT</td>
												<td>TVA</td>
												<td>Total HT</td>
												<td class="lx-qty-back">Qté à retourner</td>
												<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
												<td class="lx-note-cell" style="display:none;">Description</td>
												<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
											</tr>
											<tr class="lx-secondrow" style="display:none;">
												<td colspan="10">
													<center><i>0 produits / services</i></center>
												</td>
											</tr>';
							$price1 = 0;
							$price2 = 0;
							$discount = 0;
							$discounttype = "";
							$ttva = 0;
							$titles = explode("||",$row['title']);
							$units = explode("||",$row['unit']);
							$qtys = array_map('floatval',explode("||",$row['qty']));
							$uprices = array_map('floatval',explode("||",$row['uprice']));
							$tprices = array_map('floatval',explode("||",$row['tprice']));
							$ptvas = array_map('floatval',explode("||",$row['ptva']));
							$abovenotes = explode("||",$row['abovenote']);
							$belownotes = explode("||",$row['belownote']);
							$discounttypes = explode("||",$row['discounttype']);
							$discounts = array_map('floatval',explode("||",$row['discount']));
							$tqtyavoir = 0;
							for($i=0;$i<count($titles);$i++){
								$qtyavoir = 0;
								if($row['avoirs'] != ""){
									$avoirs = explode("|",$row['avoirs']);
									for($j=0;$j<count($avoirs);$j++){
										$avoirdetails = explode("/",$avoirs[$j]);
										if($avoirdetails[2] > 0){
											$back1 = $bdd->query("SELECT title,qty FROM documents WHERE id='".$avoirdetails[0]."'");
											$row1 = $back1->fetch();
											$avoirqtys = explode("||",$row1['qty']);
											$avoirproducts = explode("||",$row1['title']);
											for($k=0;$k<count($avoirproducts);$k++){
												if($titles[$i] == $avoirproducts[$k]){
													$qtyavoir += (float)$avoirqtys[$k];
												}
											}
										}
									}
								}
								if($row['brs'] != ""){
									$brs = explode("|",$row['brs']);
									for($j=0;$j<count($brs);$j++){
										$brdetails = explode("/",$brs[$j]);
										if($brdetails[2] > 0){
											$back1 = $bdd->query("SELECT title,qty FROM documents WHERE id='".$brdetails[0]."'");
											$row1 = $back1->fetch();
											$brqtys = explode("||",$row1['qty']);
											$brproducts = explode("||",$row1['title']);
											for($k=0;$k<count($brproducts);$k++){
												if($titles[$i] == $brproducts[$k]){
													$qtyavoir += (float)$brqtys[$k];
												}
											}
										}
									}
								}
								$tqtyavoir += ($qtys[$i]-$qtyavoir);
								$d = 0;
								if(isset($discounttypes[$i])){
									if($discounttypes[$i] == $settings['currency']){
										$discount += $discounts[$i];
										$d = $discounts[$i];
									}
									else{
										$discount += (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
										$d = (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
									}
								}
								?>
							<span style="padding-bottom:8px;border-bottom:1px solid #ccc;"><?php echo $titles[$i]."<br />Prix unitaire: <b>".number_format((float)($uprices[$i]*(1+($ptvas[$i]/100))),2,"."," ")." ".$settings['currency']."</b> TTC <br />Quantité: <b>".$qtys[$i]." ".$units[$i]."</b>".($d!="0"?"<br />Remise totale HT: <b>".number_format((float)$d,2,"."," ")." ".$settings['currency']."</b>":"");?></span>
								<?php
								$price1 += (($tprices[$i]+$d)*(1+($ptvas[$i]/100)));
								$price2 += ($tprices[$i]*(1+($ptvas[$i]/100)));
								$ttva += ($tprices[$i]*($ptvas[$i]/100));
								$discounttype = $discounttypes[$i];
								$products .= '<tr class="lx-otherrow" data-title="" data-abovenote="'.htmlspecialchars($abovenotes[$i]).'" data-belownote="'.htmlspecialchars($belownotes[$i]).'" data-qty="'.$qtys[$i].'" data-qtyavoir="'.$qtyavoir.'" data-unit="'.$units[$i].'" data-uprice="'.$uprices[$i].'" data-remise="'.$d.'" data-oremise="'.$d.'" data-tva="'.$ptvas[$i].'" data-tprice="'.($uprices[$i]*$qtys[$i]).'" data-ttprice="'.$tprices[$i].'">
												<td style="width:auto;" class="lx-product-title">'.$titles[$i].'</td>
												<td><span>'.$qtys[$i].' '.$units[$i].'</span></td>
												<td><span>'.number_format((float)$uprices[$i],2,"."," ").'</span></td>
												<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" value="'.$discounts[$i].'" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;">'.$discounttype.'</u></label></div></td>
												<td><span>'.$ptvas[$i].'%</span></td>
												<td><span>'.number_format((float)$tprices[$i],2,"."," ").'</span></td>
												<td class="lx-qty-back"><div class="lx-textfield"><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>
												<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>
												<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>
												<td class="lx-delete-cell">
												<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>
												<div class="lx-delete-payment-choice">
												<a href="javascript:;" class="lx-delete-this">Oui</a>
												<a href="javascript:;" class="lx-no-delete-payment">Non</a>
												</div>
												</td>
											</tr>';
							}
							$products .= '</table>';
							$montant += $price2;
							?>				
						</td>
						<td>
							<?php
							if($discount > 0){
							?>
							<span><ins style="white-space:nowrap;">Montant sans remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+$discount)-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$discount,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] > 0){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
							<?php
							$smpaid = $row['payments'];
							$phtable = "";

							$bankaccounts = "<option value=''>Choisissez un compte banquaire</option>";
							$back1 = $bdd->query("SELECT * FROM bankaccounts WHERE company='".$row['company']."'");
							while($row1 = $back1->fetch()){
								$bankaccounts .= "<option value='".$row1['id']."'>".$row1['bank']." | ".$row1['rib']."</option>";
							}

							// Limits
							$datelimit = time();
							if($row['datepaid'] != ""){
								$datelimit = $row['datepaid'];
							}
							$timetodelay = $row['dateadd']+($row['delay']*24*60*60);
							$resttodelay = ($row['dateadd']+($row['delay']*24*60*60) - $datelimit) / 86400;
							$resttodelay = date_diff(date_create(date("Y-m-d",$timetodelay)),date_create(date("Y-m-d")));
							$color = "#71b44c";
							$background = "transparent";
							$days = $resttodelay->format("%a");
							if($datelimit > $timetodelay){
								$days *= (-1);
							}
							if($days < 0){
								$color = "#FF0000";
							}
							else{
								$days = "+".$days;
							}
							
							if(($row['price']-($row['rastva']+$smpaid+$smavoir)) == $row['price']){
								$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."',datepaid='',datedue='',retardpaid='',dateretard='' WHERE id='".$row['id']."'");
								$req->execute();
								$row['datepaid'] = '';
							}
							elseif(($row['price']-($row['rastva']+$smpaid+$smavoir)) > 0){
								$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."',datepaid='',datedue='',retardpaid='',dateretard='' WHERE id='".$row['id']."'");
								$req->execute();
								$row['datepaid'] = '';
							}
							else{
								$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."' WHERE id='".$row['id']."'");
								$req->execute();
							}
							
							if(($row['price']-($row['rastva']+$smpaid+$smavoir)) == $row['price']){
								$row['state'] = "Non payée";
							}
							elseif(($row['price']-($row['rastva']+$smpaid+$smavoir)) > 0){
								$row['state'] = "Partiellement payée";
							}
							else{
								$row['state'] = 'Payée';
							}
							$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."' WHERE id='".$row['id']."'");
							$req->execute();
							
							$retard = 0;
							if($row['datepaid'] != ""){
								$retard = date_diff(date_create(date("Y-m-d",$timetodelay)),date_create(date("Y-m-d",$row['datepaid'])));
								$retard = $retard->format("%a");
								$req = $bdd->prepare("UPDATE documents SET retard='".$retard."',tva='".$ttva."' WHERE id='".$row['id']."'");
								$req->execute();
							}
							
							if($row['state'] == 'Payée' AND $row['datepaid'] == ""){
								$row['datepaid'] = time();
								$retard = date_diff(date_create(date("Y-m-d",$timetodelay)),date_create(date("Y-m-d",$row['datepaid'])));
								$retard = $retard->format("%a");
								$datedue = strtotime(date('20-m-Y', strtotime('first day of next month',$row['datepaid'])));
								$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."',datepaid='".time()."',datedue='".$datedue."',retard='".$retard."',retardpaid='',dateretard='' WHERE id='".$row['id']."'");
								$req->execute();
								$row['datepaid'] = time();
							}
							
							if($days < 0 AND $row['state'] != "Payée"){
								$background = "#FFC966";
							}
							?>
						<td style="white-space:nowrap;background:<?php echo $background;?>;">
							<span>Délai: <b><?php echo $row['delay'];?> jours</b>
								<?php
								if(preg_match("#Mise à jour délai de paiement factures clients#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit-delay lx-open-popup" data-header="Modifier délai"
								data-id="<?php echo $row['id'];?>"
								data-delay="<?php echo $row['delay'];?>"
								data-table="documents"
								data-title="delay" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a>
								<?php
								}
								?>
							</span>
							<span>Date d'échéance: <br /><b><?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?></b> <strong style="<?php echo $row['state']=="Payée"?"display:none;":"";?>color:<?php echo $color;?>;font-weight:bold;">(<?php echo $days;?>)</strong></span>
						</td>	
						<td>
							<span style="display:inline-block;padding:2px 5px;font-weight:500;background:<?php echo $row['state']=='Payée'?"#71b44c":($row['state']=='Non payée'?"#CC0000":"orange");?>;color:#FFFFFF;border-radius:4px;cursor:pointer;"  
								data-id="<?php echo $row['id'];?>" 
								data-rastva="<?php echo $row['rastva'];?>" 
								data-paid="<?php echo $row['state'];?>" 
								data-rest="<?php echo ($row['price']-($row['rastva']+$smpaid+$smavoir)) > 0 ? number_format((float)($row['price']-($row['rastva']+$smpaid+$smavoir)),2,".","") : "0.00";?>" 
								data-price="<?php echo number_format((float)abs($row['price']),2,".","");?>" 
								data-company="<?php echo $companytxt;?>" 
								data-companyid="<?php echo $row['company'];?>" 
								data-bankaccounts="<?php echo $bankaccounts;?>" 
								data-table="<?php echo str_replace("\"","'",$phtable);?>" class="<?php echo preg_match("#Mise à jour paiement factures clients#",$_SESSION['easybm_roles'])?"lx-edit-payment lx-open-popup":"";?>" 
								data-title="payment"><?php echo $row['state'];?></span>
							<span style="<?php echo ($row['price']-($row['rastva']+$smpaid+$smavoir)) == 0?"display:none;":"";?>"><?php echo ($row['price']-($row['rastva']+$smpaid+$smavoir)) < 0?"À rembourser":"Reste";?>: <b><?php echo number_format((float)($row['price']-($row['rastva']+$smpaid+$smavoir)),2,"."," ")." ".$row['currency'];?></b></span>
							<?php
							if($row['datepaid'] != ""){
							?>
							<span>Payé le: <b><?php echo date("d/m/Y",$row['datepaid']);?></b>
								<?php
								if(preg_match("#Mise à jour paiement factures clients#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit-datepaid lx-open-popup" data-header="Modifier délai"
								data-id="<?php echo $row['id'];?>"
								data-datepaid="<?php echo date("d/m/Y",$row['datepaid']);?>"
								data-table="documents"
								data-title="datepaid" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a>
								<?php
								}
								?>
							</span>
							<?php
							}
							if($row['state']!='Payée' AND preg_match("#Mise à jour paiement factures clients#",$_SESSION['easybm_roles'])){
							?>
							<br />
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Note d'honoraire"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Recouvrement"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Recouvrement</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Recouvrement" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
							<?php
							}
							if($row['state']=='Payée' AND preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
							?>
							<br />
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Recouvrement" style="display:block;"><i class="fa fa-eye"></i> Recouvrement</a>
							</div>
							<?php							
							}
							?>
						</td>
						<td style="white-space:nowrap;">
							<?php
							if($row['rastva'] > 0 AND $row['datepaid'] != ""){
									?>
							<span style="display:inline-block;padding:2px 5px;font-weight:500;background:<?php echo $row['stateras']=='Payée'?"#71b44c":($row['stateras']=='Non payée'?"#CC0000":"CC0000");?>;color:#FFFFFF;border-radius:4px;cursor:pointer;" 
								data-id="<?php echo $row['id'];?>" 
								data-stateras="<?php echo $row['stateras'];?>" 
								data-datepaidras="<?php echo $row['datepaidras']!=""?date("d/m/Y",$row['datepaidras']):"";?>" 
								data-attachmentras="<?php echo $row['attachmentras'];?>"
								class="<?php echo preg_match("#Mise à jour RAS TVA factures clients#",$_SESSION['easybm_roles'])?"lx-edit-payment-ras lx-open-popup":"";?>" 
								data-title="rastva"><?php echo $row['stateras'];?></span>
								<?php
								if($row['stateras'] == "Non payée"){
								?>
							<span>RAS TAV: <b style="color:#FF0000;"><?php echo $row['rastva'];?> <?php echo $settings['currency'];?></b></span>
								<?php
								}
								?>
							<span>Date d'écheance: <b><?php echo date('20/m/Y', strtotime('first day of next month',$row['datepaid']));?></b></span>
								<?php
								if($row['datepaidras'] != ""){
									$date1 = strtotime(date('20-m-Y', strtotime('first day of next month',$row['datepaid'])));
									$date2 = $row['datepaidras'];
									$resttodelay = date_diff(date_create(date("Y-m-d",$date1)),date_create(date("Y-m-d",$date2)));
									$days = $resttodelay->format("%a");
									?>
							<span>Payé le: <b><?php echo date("d/m/Y",$row['datepaidras']);?></b><a href="javascript:;" 
								data-id="<?php echo $row['id'];?>" 
								data-stateras="<?php echo $row['stateras'];?>" 
								data-datepaidras="<?php echo $row['datepaidras']!=""?date("d/m/Y",$row['datepaidras']):"";?>" 
								data-attachmentras="<?php echo $row['attachmentras'];?>"
								class="<?php echo preg_match("#Mise à jour RAS TVA factures clients#",$_SESSION['easybm_roles'])?"lx-edit-payment-ras lx-open-popup":"";?>" 
								data-title="rastva" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a></span>
									<?php
									if($date2 > $date1){
										$req = $bdd->prepare("UPDATE documents SET retardras='".$days."' WHERE id='".$row['id']."'");
										$req->execute();
										?>
							<span style="color:#FF0000;font-weight:bold;">Avec <?php echo $days;?> jours de retard</span>
										<?php
									}
									if($row['attachmentras'] != ""){
										?>
							<span><b><a href="uploads/<?php echo $row['attachmentras'];?>" target="_blank" style="color:#39add1;"><i class="fa fa-download"></i> Téléchager</a></b></span>
										<?php
									}
								}
								if($row['stateras']!='Payée' AND preg_match("#Mise à jour RAS TVA factures clients#",$_SESSION['easybm_roles'])){
								?>
								<br />
								<div class="lx-upload-photos" style="white-space:nowrap;">
									<?php
									if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
									?>
									<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
										data-id="<?php echo $row['id'];?>"
										data-client="<?php echo $clients[$row['client']]['fullname'];?>"
										data-email1="<?php echo $clients[$row['client']]['email'];?>"
										data-email="<?php echo $clients[$row['client']]['email'];?>"
										data-phone="<?php echo $clients[$row['client']]['phone'];?>"
										data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
										data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
										data-senderphone="<?php echo $user['phone'];?>"
										data-typedoc="Facture"
										data-numero_document="<?php echo $row['code'];?>"
										data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
										data-societe="<?php echo $companytxt;?>"
										data-delai_paiement="<?php echo $row['delay'];?>"
										data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
										data-button="Rappel paiement RAS"
										data-function="<?php echo $user['job'];?>"
										data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Rappel paiement RAS</a>
									<?php
									}
									if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
									?>
									<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
										data-id="<?php echo $row['id'];?>"
										data-page="fc"
										data-button="Rappel paiement RAS" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
									<?php
									}
									?>
								</div>
								<?php
								}
								if($row['stateras']=='Payée' AND preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<br />
								<div class="lx-upload-photos" style="white-space:nowrap;">
									<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
										data-id="<?php echo $row['id'];?>"
										data-page="fc"
										data-button="Rappel paiement RAS" style="display:block;"><i class="fa fa-eye"></i> Rappel paiement RAS</a>
								</div>
								<?php									
								}
							}
							?>
						</td>						
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td>
							<?php
							if($row['abovetable'] != ""){
							?>
							<span style="white-space:nowrap;width:300px;"><strong>Note au-dessus de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['abovetable']);?></span>
							<?php
							}
							if($row['modepayment'] != ""){
							?>
							<span><strong>Note au-dessous de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['modepayment']);?></span>
							<?php
							}
							if($row['conditions'] != ""){
							?>
							<span><strong>Conditions (au-dessous de tableau) :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['conditions']);?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['picture'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['picture'];?>" data-pic="<?php echo $row['picture'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['picture'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<td><span style="white-space:nowrap"><?php echo $user['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="documents" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="documents" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Note d'honoraire"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-company="<?php echo $row['company'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Envoyer le document"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer le document</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Envoyer le document" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Note d'honoraire" class="lx-download-file1 lx-print-f lx-print-fc lx-open-popup" data-title="signature">Imprimer</a>
								<?php
								if(preg_match("#Modifier Notes honoraires#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Modifier" 
									data-id="<?php echo $row['id'];?>"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="command">Modifier</a>
									<?php
								}
								if(preg_match("#Transformation / Dupplication documents#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Transformer / Dupliquer" 
									data-id="0"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",($row['note']));?>"
									data-modepayment="<?php echo str_replace("\"","'",($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",($row['abovetable']));?>" data-title="command">Transformer / Duppliquer</a>
									<?php
								}
								if(((preg_match("#Consulter Factures avoir#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Factures avoir#",$_SESSION['easybm_roles'])) OR (preg_match("#Consulter Bons de retour#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Bons de retour#",$_SESSION['easybm_roles']))) AND $tqtyavoir > 0){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" style="color:#FFA500;" data-table="commands" data-header="Avoir / bon de retour" 
									data-id="-1"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['code'];?>"
									data-note="<?php echo str_replace("\"","'",($row['note']));?>" data-title="command">Avoir / bon de retour</a>
									<?php
								}
								?>
								<a href="javascript:;" class="lx-edit lx-edit-extrainfo lx-open-popup" data-table="extrainfo" data-header="Informations supplémentaires sur le document" 
									data-id="<?php echo $row['id'];?>"
									data-modepayment="<?php echo str_replace("\"","'",($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",($row['abovetable']));?>" data-title="extrainfo">Informations supplémentaires sur le document</a>
								<?php
								if(preg_match("#Supprimer Notes honoraires#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-command lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Factures avoir / Bons de retour N°</td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails note d'honoraire</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td>Délai de paiement <i class="fa fa-sort" data-sort="delay"></i></td>
						<td>Statut et historique des paiements <i class="fa fa-sort" data-sort="rest"></i></td>
						<td>RAS TVA <i class="fa fa-sort" data-sort="delayras"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<?php
				$req1 = "SELECT COUNT(id) AS nb FROM documents c WHERE trash='1' AND type='nh'".$multicompanies;
				$req = "";
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['stateras'] != ""){
						if($_POST['stateras'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND stateras NOT IN('Payée') AND ".time()." > datedue AND rastva > 0";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['stateras'])){
								$req .= " AND (stateras IN ('".str_replace("||","','",$_POST['stateras'])."') OR (stateras NOT IN('Payée') AND ".time()." > datedue)) AND rastva > 0";
							}
							else{
								$req .= " AND stateras IN ('".str_replace("||","','",$_POST['stateras'])."') AND rastva > 0";
							}
						}
					}
					if($_POST['statee'] != ""){
						if($_POST['statee'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['statee'])){
								$req .= " AND (state IN ('".str_replace("||","','",$_POST['statee'])."') OR (state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")))";
							}
							else{
								$req .= " AND state IN ('".str_replace("||","','",$_POST['statee'])."')";
							}
						}
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['avoir'] == "1"){
						$req .= " AND (avoirs<>'' OR brs<>'')";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimit'] != "" AND $_POST['dateendlimit'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimit']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimit'])) + (60*60*24) - 1;
						$req .= " AND ((dateadd+(delay*60*60*24)) BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimitras'] != "" AND $_POST['dateendlimitras'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimitras']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimitras'])) + (60*60*24) - 1;
						$req .= " AND (datedue BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$nbcmds = 0.1;
				if($row['nb'] != 0){
					$nbcmds = $row['nb'];
				}
							
				$req1 = "SELECT SUM(price - rastva) AS ca,SUM(payments) AS paid FROM documents c WHERE type='nh'".$multicompanies;
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$ca = $row['ca'];
				$ca1 = $row['ca'];
				$paid = $row['paid'];
				

							
				$req1 = "SELECT SUM(price - rastva) AS ca,SUM(payments) AS paid FROM documents c WHERE type IN('avoir','br') AND correctdoc IN(SELECT code FROM documents WHERE type='nh'".$multicompanies.")";
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$avoirca = $row['ca'];
				$avoirca1 = $row['ca'];
				$avoirpaid = $row['paid'];
				
				$rest = number_format((float)(isset($ca)?$ca-$avoirca:0),2,".","") - number_format((float)(isset($paid)?$paid-$avoirpaid:0),2,".","");
				if($ca1 == 0){
					$ca1 = 1;
				}
				
				?>
				<div class="lx-caisse-total lx-caisse-total1">
					<h3 style="background:#d9534f;">En cours</h3>
					<p style="padding-bottom:35px;"><?php echo number_format((float)(isset($rest)?$rest:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$rest*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $rest*100/$ca1?>%;background:#d9534f;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total2">
					<h3 style="background:#5cb85c;">Payé</h3>
					<p style="padding-bottom:<?php echo $avoirpaid>0?"0px":"35px";?>;"><?php echo number_format((float)(isset($paid)?$paid:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<p style="display:<?php echo $avoirpaid>0?"block":"none";?>;padding:0px;padding-bottom:20px;maring:0px;margin-top:5px;text-align:center;font-size:11px;font-style:italic;font-weight:normal;">-<?php echo number_format((float)(isset($avoirpaid)?$avoirpaid:0),2,"."," ")." ".$settings['currency'];?> TTC (AV/BR)</p>
					<ins><?php echo number_format((float)$paid*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $paid*100/$ca1?>%;background:#5cb85c;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total3">
					<h3 style="background:#337ab7;">Chiffre d'affaires</h3>
					<p style="padding-bottom:<?php echo $avoirca>0?"0px":"35px";?>;"><?php echo number_format((float)(isset($ca)?$ca:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<p style="display:<?php echo $avoirca>0?"block":"none";?>;padding:0px;padding-bottom:20px;maring:0px;margin-top:5px;text-align:center;font-size:11px;font-style:italic;font-weight:normal;">-<?php echo number_format((float)(isset($avoirca)?$avoirca:0),2,"."," ")." ".$settings['currency'];?> TTC (AV/BR)</p>
					<ins><?php echo number_format((float)$ca*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $ca*100/$ca1?>%;background:#337ab7;"></ins>
					</h4>
				</div>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> note(s) d'honoraire(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> note(s) d'honoraire(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
				$req1 = "SELECT SUM(price) AS price,SUM(tva) AS tva,SUM(rastva) AS rastva FROM documents c WHERE type='nh' AND trash='1'".$multicompanies;
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				?>
				<div class="lx-caisse-total4">
					<div style="float:left;margin-right:60px;">
						<p>* Total Montant HT: <b><?php echo number_format((float)$row['price']-$row['tva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total TVA: <b><?php echo number_format((float)$row['tva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total RAS TVA: <b style="color:#FF0000;"><?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total Montant TTC: <b><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></p>
					</div>
				</div>
				<?php
			}

			if($_POST['action'] == "loadnf"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Factures avoir / Bons de retour N°</td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails note de frais</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td>Délai de paiement <i class="fa fa-sort" data-sort="delay"></i></td>
						<td>Statut et historique des paiements <i class="fa fa-sort" data-sort="rest"></i></td>
						<td>RAS TVA <i class="fa fa-sort" data-sort="delayras"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM documents d WHERE category='client' AND type='nf' AND trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['stateras'] != ""){
						if($_POST['stateras'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND stateras NOT IN('Payée') AND ".time()." > datedue AND rastva > 0";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['stateras'])){
								$req .= " AND (stateras IN ('".str_replace("||","','",$_POST['stateras'])."') OR (stateras NOT IN('Payée') AND ".time()." > datedue)) AND rastva > 0";
							}
							else{
								$req .= " AND stateras IN ('".str_replace("||","','",$_POST['stateras'])."') AND rastva > 0";
							}
						}
					}
					if($_POST['statee'] != ""){
						if($_POST['statee'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['statee'])){
								$req .= " AND (state IN ('".str_replace("||","','",$_POST['statee'])."') OR (state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")))";
							}
							else{
								$req .= " AND state IN ('".str_replace("||","','",$_POST['statee'])."')";
							}
						}
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['avoir'] == "1"){
						$req .= " AND (avoirs<>'' OR brs<>'')";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimit'] != "" AND $_POST['dateendlimit'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimit']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimit'])) + (60*60*24) - 1;
						$req .= " AND ((dateadd+(delay*60*60*24)) BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimitras'] != "" AND $_POST['dateendlimitras'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimitras']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimitras'])) + (60*60*24) - 1;
						$req .= " AND (datedue BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "delay"){
							$req .= " ORDER BY (dateadd + (delay * 60 * 60 * 24))";
						}
						elseif($_POST['sortby'] == "delayras"){
							$req .= " ORDER BY (datepaid + ".secondsUntilNext20th().")";
						}
						elseif($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$montant = 0;
					while($row = $back->fetch()){
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();

						$back1 = $bdd->query("SELECT * FROM prospects WHERE id='".$row['prospect']."'");
						$prospect = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facture" value="<?php echo $row['id'];?>" 
						data-client="<?php echo $row['client'];?>" 
						data-prospect="<?php echo $row['prospect'];?>" 
						data-code="<?php echo $row['code'];?>" 
						data-company="<?php echo $row['company'];?>" 
						data-discounttype="<?php echo $row['discounttype'];?>" 
						data-price="<?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ")." ".$settings['currency'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<?php
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];
						?>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>
						<td><a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Note de frais" class="lx-download-file1 lx-print-f lx-print-fc lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['code'];?></a></td>
						<td>
							<?php
							$smavoir = 0;
							if($row['avoirs'] != ""){
								?>
							<span>Avoirs:</span>
								<?php
								$avoirs = explode("|",$row['avoirs']);
								for($i=0;$i<count($avoirs);$i++){
									$avoirdetails = explode("/",$avoirs[$i]);
									if($avoirdetails[2] > 0){
										?>
							<ins style="white-space:nowrap;"><a href="javascript:;" data-id="<?php echo $avoirdetails[0];?>" data-href="printclientdocs.php?id=<?php echo $avoirdetails[0];?>&title=Avoir" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature" style="color:#39add1;white-space:nowrap;"><?php echo $avoirdetails[1];?></a> (<?php echo $avoirdetails[2]." ".$settings['currency'];?> TTC)<ins><br />
										<?php
										$smavoir += $avoirdetails[2];
									}
								}
							}
							if($row['brs'] != ""){
								?>
							<br /><span>Bons de retour:</span>
								<?php
								$brs = explode("|",$row['brs']);
								for($i=0;$i<count($brs);$i++){
									$brdetails = explode("/",$brs[$i]);
									if($brdetails[2] > 0){
										?>
							<ins style="white-space:nowrap;"><a href="javascript:;" data-id="<?php echo $brdetails[0];?>" data-href="printclientdocs.php?id=<?php echo $brdetails[0];?>&title=Bon de retour" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature" style="color:#39add1;white-space:nowrap;"><?php echo $brdetails[1];?></a> (<?php echo $brdetails[2]." ".$settings['currency'];?> TTC)<ins><br />
										<?php
										$smavoir += $brdetails[2];
									}
								}
							}
							$back1 = $bdd->query("SELECT SUM(payments) AS sm FROM documents WHERE correctdoc='".$row['code']."'");
							$row1 = $back1->fetch();	
							if($row['avoirs'] != "" OR $row['brs'] != ""){
								$smavoir -= $row1['sm'];
							}
							?>	
						</td>
						<td>
							<span style="white-space:nowrap;"><?php echo $clients[$row['client']]['fullname']." (".($clients[$row['client']]['codecl']!=""?$clients[$row['client']]['codecl']:$clients[$row['client']]['code']).")";?></span>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td>
							<?php
							$products = '<table>
											<tr class="lx-firstrow">
												<td style="width:500px;">Designation</td>
												<td>Qté</td>
												<td style="width:100px;">P.U. HT</td>
												<td class="lx-discount">Remise totale HT</td>
												<td>TVA</td>
												<td>Total HT</td>
												<td class="lx-qty-back">Qté à retourner</td>
												<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
												<td class="lx-note-cell" style="display:none;">Description</td>
												<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
											</tr>
											<tr class="lx-secondrow" style="display:none;">
												<td colspan="10">
													<center><i>0 produits / services</i></center>
												</td>
											</tr>';
							$price1 = 0;
							$price2 = 0;
							$discount = 0;
							$discounttype = "";
							$ttva = 0;
							$titles = explode("||",$row['title']);
							$units = explode("||",$row['unit']);
							$qtys = array_map('floatval',explode("||",$row['qty']));
							$uprices = array_map('floatval',explode("||",$row['uprice']));
							$tprices = array_map('floatval',explode("||",$row['tprice']));
							$ptvas = array_map('floatval',explode("||",$row['ptva']));
							$abovenotes = explode("||",$row['abovenote']);
							$belownotes = explode("||",$row['belownote']);
							$discounttypes = explode("||",$row['discounttype']);
							$discounts = array_map('floatval',explode("||",$row['discount']));
							$tqtyavoir = 0;
							for($i=0;$i<count($titles);$i++){
								$qtyavoir = 0;
								if($row['avoirs'] != ""){
									$avoirs = explode("|",$row['avoirs']);
									for($j=0;$j<count($avoirs);$j++){
										$avoirdetails = explode("/",$avoirs[$j]);
										if($avoirdetails[2] > 0){
											$back1 = $bdd->query("SELECT title,qty FROM documents WHERE id='".$avoirdetails[0]."'");
											$row1 = $back1->fetch();
											$avoirqtys = explode("||",$row1['qty']);
											$avoirproducts = explode("||",$row1['title']);
											for($k=0;$k<count($avoirproducts);$k++){
												if($titles[$i] == $avoirproducts[$k]){
													$qtyavoir += (float)$avoirqtys[$k];
												}
											}
										}
									}
								}
								if($row['brs'] != ""){
									$brs = explode("|",$row['brs']);
									for($j=0;$j<count($brs);$j++){
										$brdetails = explode("/",$brs[$j]);
										if($brdetails[2] > 0){
											$back1 = $bdd->query("SELECT title,qty FROM documents WHERE id='".$brdetails[0]."'");
											$row1 = $back1->fetch();
											$brqtys = explode("||",$row1['qty']);
											$brproducts = explode("||",$row1['title']);
											for($k=0;$k<count($brproducts);$k++){
												if($titles[$i] == $brproducts[$k]){
													$qtyavoir += (float)$brqtys[$k];
												}
											}
										}
									}
								}
								$tqtyavoir += ($qtys[$i]-$qtyavoir);
								$d = 0;
								if(isset($discounttypes[$i])){
									if($discounttypes[$i] == $settings['currency']){
										$discount += $discounts[$i];
										$d = $discounts[$i];
									}
									else{
										$discount += (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
										$d = (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
									}
								}
								?>
							<span style="padding-bottom:8px;border-bottom:1px solid #ccc;"><?php echo $titles[$i]."<br />Prix unitaire: <b>".number_format((float)($uprices[$i]*(1+($ptvas[$i]/100))),2,"."," ")." ".$settings['currency']."</b> TTC <br />Quantité: <b>".$qtys[$i]." ".$units[$i]."</b>".($d!="0"?"<br />Remise totale HT: <b>".number_format((float)$d,2,"."," ")." ".$settings['currency']."</b>":"");?></span>
								<?php
								$price1 += (($tprices[$i]+$d)*(1+($ptvas[$i]/100)));
								$price2 += ($tprices[$i]*(1+($ptvas[$i]/100)));
								$ttva += ($tprices[$i]*($ptvas[$i]/100));
								$discounttype = $discounttypes[$i];
								$products .= '<tr class="lx-otherrow" data-title="" data-abovenote="'.htmlspecialchars($abovenotes[$i]).'" data-belownote="'.htmlspecialchars($belownotes[$i]).'" data-qty="'.$qtys[$i].'" data-qtyavoir="'.$qtyavoir.'" data-unit="'.$units[$i].'" data-uprice="'.$uprices[$i].'" data-remise="'.$d.'" data-oremise="'.$d.'" data-tva="'.$ptvas[$i].'" data-tprice="'.($uprices[$i]*$qtys[$i]).'" data-ttprice="'.$tprices[$i].'">
												<td style="width:auto;" class="lx-product-title">'.$titles[$i].'</td>
												<td><span>'.$qtys[$i].' '.$units[$i].'</span></td>
												<td><span>'.number_format((float)$uprices[$i],2,"."," ").'</span></td>
												<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" value="'.$discounts[$i].'" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;">'.$discounttype.'</u></label></div></td>
												<td><span>'.$ptvas[$i].'%</span></td>
												<td><span>'.number_format((float)$tprices[$i],2,"."," ").'</span></td>
												<td class="lx-qty-back"><div class="lx-textfield"><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>
												<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>
												<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>
												<td class="lx-delete-cell">
												<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>
												<div class="lx-delete-payment-choice">
												<a href="javascript:;" class="lx-delete-this">Oui</a>
												<a href="javascript:;" class="lx-no-delete-payment">Non</a>
												</div>
												</td>
											</tr>';
							}
							$products .= '</table>';
							$montant += $price2;
							?>				
						</td>
						<td>
							<?php
							if($discount > 0){
							?>
							<span><ins style="white-space:nowrap;">Montant sans remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+$discount)-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$discount,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] > 0){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
							<?php
							$smpaid = $row['payments'];
							$phtable = "";

							$bankaccounts = "<option value=''>Choisissez un compte banquaire</option>";
							$back1 = $bdd->query("SELECT * FROM bankaccounts WHERE company='".$row['company']."'");
							while($row1 = $back1->fetch()){
								$bankaccounts .= "<option value='".$row1['id']."'>".$row1['bank']." | ".$row1['rib']."</option>";
							}

							// Limits
							$datelimit = time();
							if($row['datepaid'] != ""){
								$datelimit = $row['datepaid'];
							}
							$timetodelay = $row['dateadd']+($row['delay']*24*60*60);
							$resttodelay = ($row['dateadd']+($row['delay']*24*60*60) - $datelimit) / 86400;
							$resttodelay = date_diff(date_create(date("Y-m-d",$timetodelay)),date_create(date("Y-m-d")));
							$color = "#71b44c";
							$background = "transparent";
							$days = $resttodelay->format("%a");
							if($datelimit > $timetodelay){
								$days *= (-1);
							}
							if($days < 0){
								$color = "#FF0000";
							}
							else{
								$days = "+".$days;
							}
							
							if(($row['price']-($row['rastva']+$smpaid+$smavoir)) == $row['price']){
								$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."',datepaid='',datedue='',retardpaid='',dateretard='' WHERE id='".$row['id']."'");
								$req->execute();
								$row['datepaid'] = '';
							}
							elseif(($row['price']-($row['rastva']+$smpaid+$smavoir)) > 0){
								$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."',datepaid='',datedue='',retardpaid='',dateretard='' WHERE id='".$row['id']."'");
								$req->execute();
								$row['datepaid'] = '';
							}
							else{
								$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."' WHERE id='".$row['id']."'");
								$req->execute();
							}
							
							if(($row['price']-($row['rastva']+$smpaid+$smavoir)) == $row['price']){
								$row['state'] = "Non payée";
							}
							elseif(($row['price']-($row['rastva']+$smpaid+$smavoir)) > 0){
								$row['state'] = "Partiellement payée";
							}
							else{
								$row['state'] = 'Payée';
							}
							$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."' WHERE id='".$row['id']."'");
							$req->execute();
							
							$retard = 0;
							if($row['datepaid'] != ""){
								$retard = date_diff(date_create(date("Y-m-d",$timetodelay)),date_create(date("Y-m-d",$row['datepaid'])));
								$retard = $retard->format("%a");
								$req = $bdd->prepare("UPDATE documents SET retard='".$retard."',tva='".$ttva."' WHERE id='".$row['id']."'");
								$req->execute();
							}
							
							if($row['state'] == 'Payée' AND $row['datepaid'] == ""){
								$row['datepaid'] = time();
								$retard = date_diff(date_create(date("Y-m-d",$timetodelay)),date_create(date("Y-m-d",$row['datepaid'])));
								$retard = $retard->format("%a");
								$datedue = strtotime(date('20-m-Y', strtotime('first day of next month',$row['datepaid'])));
								$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."',tva='".$ttva."',datepaid='".time()."',datedue='".$datedue."',retard='".$retard."',retardpaid='',dateretard='' WHERE id='".$row['id']."'");
								$req->execute();
								$row['datepaid'] = time();
							}
							
							if($days < 0 AND $row['state'] != "Payée"){
								$background = "#FFC966";
							}
							?>
						<td style="white-space:nowrap;background:<?php echo $background;?>;">
							<span>Délai: <b><?php echo $row['delay'];?> jours</b>
								<?php
								if(preg_match("#Mise à jour délai de paiement factures clients#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit-delay lx-open-popup" data-header="Modifier délai"
								data-id="<?php echo $row['id'];?>"
								data-delay="<?php echo $row['delay'];?>"
								data-table="documents"
								data-title="delay" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a>
								<?php
								}
								?>
							</span>
							<span>Date d'échéance: <br /><b><?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?></b> <strong style="<?php echo $row['state']=="Payée"?"display:none;":"";?>color:<?php echo $color;?>;font-weight:bold;">(<?php echo $days;?>)</strong></span>
						</td>	
						<td>
							<span style="display:inline-block;padding:2px 5px;font-weight:500;background:<?php echo $row['state']=='Payée'?"#71b44c":($row['state']=='Non payée'?"#CC0000":"orange");?>;color:#FFFFFF;border-radius:4px;cursor:pointer;"  
								data-id="<?php echo $row['id'];?>" 
								data-rastva="<?php echo $row['rastva'];?>" 
								data-paid="<?php echo $row['state'];?>" 
								data-rest="<?php echo ($row['price']-($row['rastva']+$smpaid+$smavoir)) > 0 ? number_format((float)($row['price']-($row['rastva']+$smpaid+$smavoir)),2,".","") : "0.00";?>" 
								data-price="<?php echo number_format((float)abs($row['price']),2,".","");?>" 
								data-company="<?php echo $companytxt;?>" 
								data-companyid="<?php echo $row['company'];?>" 
								data-bankaccounts="<?php echo $bankaccounts;?>" 
								data-table="<?php echo str_replace("\"","'",$phtable);?>" class="<?php echo preg_match("#Mise à jour paiement factures clients#",$_SESSION['easybm_roles'])?"lx-edit-payment lx-open-popup":"";?>" 
								data-title="payment"><?php echo $row['state'];?></span>
							<span style="<?php echo ($row['price']-($row['rastva']+$smpaid+$smavoir)) == 0?"display:none;":"";?>"><?php echo ($row['price']-($row['rastva']+$smpaid+$smavoir)) < 0?"À rembourser":"Reste";?>: <b><?php echo number_format((float)($row['price']-($row['rastva']+$smpaid+$smavoir)),2,"."," ")." ".$row['currency'];?></b></span>
							<?php
							if($row['datepaid'] != ""){
							?>
							<span>Payé le: <b><?php echo date("d/m/Y",$row['datepaid']);?></b>
								<?php
								if(preg_match("#Mise à jour paiement factures clients#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-edit-datepaid lx-open-popup" data-header="Modifier délai"
								data-id="<?php echo $row['id'];?>"
								data-datepaid="<?php echo date("d/m/Y",$row['datepaid']);?>"
								data-table="documents"
								data-title="datepaid" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a>
								<?php
								}
								?>
							</span>
							<?php
							}
							if($row['state']!='Payée' AND preg_match("#Mise à jour paiement factures clients#",$_SESSION['easybm_roles'])){
							?>
							<br />
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Note de frais"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Recouvrement"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Recouvrement</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Recouvrement" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
							<?php
							}
							if($row['state']=='Payée' AND preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
							?>
							<br />
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Recouvrement" style="display:block;"><i class="fa fa-eye"></i> Recouvrement</a>
							</div>
							<?php							
							}
							?>
						</td>
						<td style="white-space:nowrap;">
							<?php
							if($row['rastva'] > 0 AND $row['datepaid'] != ""){
									?>
							<span style="display:inline-block;padding:2px 5px;font-weight:500;background:<?php echo $row['stateras']=='Payée'?"#71b44c":($row['stateras']=='Non payée'?"#CC0000":"CC0000");?>;color:#FFFFFF;border-radius:4px;cursor:pointer;" 
								data-id="<?php echo $row['id'];?>" 
								data-stateras="<?php echo $row['stateras'];?>" 
								data-datepaidras="<?php echo $row['datepaidras']!=""?date("d/m/Y",$row['datepaidras']):"";?>" 
								data-attachmentras="<?php echo $row['attachmentras'];?>"
								class="<?php echo preg_match("#Mise à jour RAS TVA factures clients#",$_SESSION['easybm_roles'])?"lx-edit-payment-ras lx-open-popup":"";?>" 
								data-title="rastva"><?php echo $row['stateras'];?></span>
								<?php
								if($row['stateras'] == "Non payée"){
								?>
							<span>RAS TAV: <b style="color:#FF0000;"><?php echo $row['rastva'];?> <?php echo $settings['currency'];?></b></span>
								<?php
								}
								?>
							<span>Date d'écheance: <b><?php echo date('20/m/Y', strtotime('first day of next month',$row['datepaid']));?></b></span>
								<?php
								if($row['datepaidras'] != ""){
									$date1 = strtotime(date('20-m-Y', strtotime('first day of next month',$row['datepaid'])));
									$date2 = $row['datepaidras'];
									$resttodelay = date_diff(date_create(date("Y-m-d",$date1)),date_create(date("Y-m-d",$date2)));
									$days = $resttodelay->format("%a");
									?>
							<span>Payé le: <b><?php echo date("d/m/Y",$row['datepaidras']);?></b><a href="javascript:;" 
								data-id="<?php echo $row['id'];?>" 
								data-stateras="<?php echo $row['stateras'];?>" 
								data-datepaidras="<?php echo $row['datepaidras']!=""?date("d/m/Y",$row['datepaidras']):"";?>" 
								data-attachmentras="<?php echo $row['attachmentras'];?>"
								class="<?php echo preg_match("#Mise à jour RAS TVA factures clients#",$_SESSION['easybm_roles'])?"lx-edit-payment-ras lx-open-popup":"";?>" 
								data-title="rastva" style="display:inline-block;margin-left:10px;color:#39add1;"><i class="fa fa-edit"></i></a></span>
									<?php
									if($date2 > $date1){
										$req = $bdd->prepare("UPDATE documents SET retardras='".$days."' WHERE id='".$row['id']."'");
										$req->execute();
										?>
							<span style="color:#FF0000;font-weight:bold;">Avec <?php echo $days;?> jours de retard</span>
										<?php
									}
									if($row['attachmentras'] != ""){
										?>
							<span><b><a href="uploads/<?php echo $row['attachmentras'];?>" target="_blank" style="color:#39add1;"><i class="fa fa-download"></i> Téléchager</a></b></span>
										<?php
									}
								}
								if($row['stateras']!='Payée' AND preg_match("#Mise à jour RAS TVA factures clients#",$_SESSION['easybm_roles'])){
								?>
								<br />
								<div class="lx-upload-photos" style="white-space:nowrap;">
									<?php
									if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
									?>
									<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
										data-id="<?php echo $row['id'];?>"
										data-client="<?php echo $clients[$row['client']]['fullname'];?>"
										data-email1="<?php echo $clients[$row['client']]['email'];?>"
										data-email="<?php echo $clients[$row['client']]['email'];?>"
										data-phone="<?php echo $clients[$row['client']]['phone'];?>"
										data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
										data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
										data-senderphone="<?php echo $user['phone'];?>"
										data-typedoc="Facture"
										data-numero_document="<?php echo $row['code'];?>"
										data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
										data-societe="<?php echo $companytxt;?>"
										data-delai_paiement="<?php echo $row['delay'];?>"
										data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
										data-button="Rappel paiement RAS"
										data-function="<?php echo $user['job'];?>"
										data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Rappel paiement RAS</a>
									<?php
									}
									if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
									?>
									<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
										data-id="<?php echo $row['id'];?>"
										data-page="fc"
										data-button="Rappel paiement RAS" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
									<?php
									}
									?>
								</div>
								<?php
								}
								if($row['stateras']=='Payée' AND preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<br />
								<div class="lx-upload-photos" style="white-space:nowrap;">
									<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
										data-id="<?php echo $row['id'];?>"
										data-page="fc"
										data-button="Rappel paiement RAS" style="display:block;"><i class="fa fa-eye"></i> Rappel paiement RAS</a>
								</div>
								<?php									
								}
							}
							?>
						</td>						
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td>
							<?php
							if($row['abovetable'] != ""){
							?>
							<span style="white-space:nowrap;width:300px;"><strong>Note au-dessus de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['abovetable']);?></span>
							<?php
							}
							if($row['modepayment'] != ""){
							?>
							<span><strong>Note au-dessous de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['modepayment']);?></span>
							<?php
							}
							if($row['conditions'] != ""){
							?>
							<span><strong>Conditions (au-dessous de tableau) :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['conditions']);?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['picture'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['picture'];?>" data-pic="<?php echo $row['picture'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['picture'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<td><span style="white-space:nowrap"><?php echo $user['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="documents" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="documents" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Note de frais"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-company="<?php echo $row['company'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Envoyer le document"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer le document</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Envoyer le document" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Note de frais" class="lx-download-file1 lx-print-f lx-print-fc lx-open-popup" data-title="signature">Imprimer</a>
								<?php
								if(preg_match("#Modifier Notes honoraires#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Modifier" 
									data-id="<?php echo $row['id'];?>"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="command">Modifier</a>
									<?php
								}
								if(preg_match("#Transformation / Dupplication documents#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Transformer / Dupliquer" 
									data-id="0"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",($row['note']));?>"
									data-modepayment="<?php echo str_replace("\"","'",($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",($row['abovetable']));?>" data-title="command">Transformer / Duppliquer</a>
									<?php
								}
								if(((preg_match("#Consulter Factures avoir#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Factures avoir#",$_SESSION['easybm_roles'])) OR (preg_match("#Consulter Bons de retour#",$_SESSION['easybm_roles']) AND preg_match("#Ajouter Bons de retour#",$_SESSION['easybm_roles']))) AND $tqtyavoir > 0){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" style="color:#FFA500;" data-table="commands" data-header="Avoir / bon de retour" 
									data-id="-1"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['code'];?>"
									data-note="<?php echo str_replace("\"","'",($row['note']));?>" data-title="command">Avoir / bon de retour</a>
									<?php
								}
								?>
								<a href="javascript:;" class="lx-edit lx-edit-extrainfo lx-open-popup" data-table="extrainfo" data-header="Informations supplémentaires sur le document" 
									data-id="<?php echo $row['id'];?>"
									data-modepayment="<?php echo str_replace("\"","'",($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",($row['abovetable']));?>" data-title="extrainfo">Informations supplémentaires sur le document</a>
								<?php
								if(preg_match("#Supprimer Notes honoraires#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-command lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Factures avoir / Bons de retour N°</td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails note de frais</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td>Délai de paiement <i class="fa fa-sort" data-sort="delay"></i></td>
						<td>Statut et historique des paiements <i class="fa fa-sort" data-sort="rest"></i></td>
						<td>RAS TVA <i class="fa fa-sort" data-sort="delayras"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<?php
				$req1 = "SELECT COUNT(id) AS nb FROM documents c WHERE trash='1' AND type='nf'".$multicompanies;
				$req = "";
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['stateras'] != ""){
						if($_POST['stateras'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND stateras NOT IN('Payée') AND ".time()." > datedue AND rastva > 0";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['stateras'])){
								$req .= " AND (stateras IN ('".str_replace("||","','",$_POST['stateras'])."') OR (stateras NOT IN('Payée') AND ".time()." > datedue)) AND rastva > 0";
							}
							else{
								$req .= " AND stateras IN ('".str_replace("||","','",$_POST['stateras'])."') AND rastva > 0";
							}
						}
					}
					if($_POST['statee'] != ""){
						if($_POST['statee'] == "Non payée avec dépassement de délai de paiement"){
							$req .= " AND state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")";
						}
						else{
							if(preg_match("#Non payée avec dépassement de délai de paiement#",$_POST['statee'])){
								$req .= " AND (state IN ('".str_replace("||","','",$_POST['statee'])."') OR (state NOT IN('Payée') AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")))";
							}
							else{
								$req .= " AND state IN ('".str_replace("||","','",$_POST['statee'])."')";
							}
						}
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['avoir'] == "1"){
						$req .= " AND (avoirs<>'' OR brs<>'')";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimit'] != "" AND $_POST['dateendlimit'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimit']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimit'])) + (60*60*24) - 1;
						$req .= " AND ((dateadd+(delay*60*60*24)) BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartlimitras'] != "" AND $_POST['dateendlimitras'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartlimitras']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendlimitras'])) + (60*60*24) - 1;
						$req .= " AND (datedue BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$nbcmds = 0.1;
				if($row['nb'] != 0){
					$nbcmds = $row['nb'];
				}
							
				$req1 = "SELECT SUM(price - rastva) AS ca,SUM(payments) AS paid FROM documents c WHERE type='nf'".$multicompanies;
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$ca = $row['ca'];
				$ca1 = $row['ca'];
				$paid = $row['paid'];
				

							
				$req1 = "SELECT SUM(price - rastva) AS ca,SUM(payments) AS paid FROM documents c WHERE type IN('avoir','br') AND correctdoc IN(SELECT code FROM documents WHERE type='nf'".$multicompanies.")";
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				$avoirca = $row['ca'];
				$avoirca1 = $row['ca'];
				$avoirpaid = $row['paid'];
				
				$rest = number_format((float)(isset($ca)?$ca-$avoirca:0),2,".","") - number_format((float)(isset($paid)?$paid-$avoirpaid:0),2,".","");
				if($ca1 == 0){
					$ca1 = 1;
				}
				
				?>
				<div class="lx-caisse-total lx-caisse-total1">
					<h3 style="background:#d9534f;">En cours</h3>
					<p style="padding-bottom:35px;"><?php echo number_format((float)(isset($rest)?$rest:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<ins><?php echo number_format((float)$rest*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $rest*100/$ca1?>%;background:#d9534f;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total2">
					<h3 style="background:#5cb85c;">Payé</h3>
					<p style="padding-bottom:<?php echo $avoirpaid>0?"0px":"35px";?>;"><?php echo number_format((float)(isset($paid)?$paid:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<p style="display:<?php echo $avoirpaid>0?"block":"none";?>;padding:0px;padding-bottom:20px;maring:0px;margin-top:5px;text-align:center;font-size:11px;font-style:italic;font-weight:normal;">-<?php echo number_format((float)(isset($avoirpaid)?$avoirpaid:0),2,"."," ")." ".$settings['currency'];?> TTC (AV/BR)</p>
					<ins><?php echo number_format((float)$paid*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $paid*100/$ca1?>%;background:#5cb85c;"></ins>
					</h4>
				</div>
				<div class="lx-caisse-total lx-caisse-total3">
					<h3 style="background:#337ab7;">Chiffre d'affaires</h3>
					<p style="padding-bottom:<?php echo $avoirca>0?"0px":"35px";?>;"><?php echo number_format((float)(isset($ca)?$ca:0),2,"."," ")." ".$settings['currency'];?> TTC</p>
					<p style="display:<?php echo $avoirca>0?"block":"none";?>;padding:0px;padding-bottom:20px;maring:0px;margin-top:5px;text-align:center;font-size:11px;font-style:italic;font-weight:normal;">-<?php echo number_format((float)(isset($avoirca)?$avoirca:0),2,"."," ")." ".$settings['currency'];?> TTC (AV/BR)</p>
					<ins><?php echo number_format((float)$ca*100/$ca1,2,"."," ")?>%</ins>
					<h4 class="lx-progress-container">
						<ins class="lx-progress-bar" style="width:<?php echo $ca*100/$ca1?>%;background:#337ab7;"></ins>
					</h4>
				</div>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> note(s) de frais(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> note(s) de frais(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
				$req1 = "SELECT SUM(price) AS price,SUM(tva) AS tva,SUM(rastva) AS rastva FROM documents c WHERE type='nf' AND trash='1'".$multicompanies;
				$back = $bdd->query($req1.$req);
				$row = $back->fetch();
				?>
				<div class="lx-caisse-total4">
					<div style="float:left;margin-right:60px;">
						<p>* Total Montant HT: <b><?php echo number_format((float)$row['price']-$row['tva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total TVA: <b><?php echo number_format((float)$row['tva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total RAS TVA: <b style="color:#FF0000;"><?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></p>
						<p>* Total Montant TTC: <b><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></p>
					</div>
				</div>
				<?php
			}

			if($_POST['action'] == "loadph"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Client / Prospect<i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails Proposition d'honoraires</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM documents d WHERE category='client' AND type='ph' AND trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['prospect'] != ""){
						$req .= " AND prospect IN(".str_replace("||",",",$_POST['prospect']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$montant = 0;
					while($row = $back->fetch()){
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();	

						$back1 = $bdd->query("SELECT * FROM prospects WHERE id='".$row['prospect']."'");
						$prospect = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facture" value="<?php echo $row['id'];?>" 
						data-client="<?php echo $row['client'];?>" 
						data-prospect="<?php echo $row['prospect'];?>" 
						data-code="<?php echo $row['code'];?>" 
						data-company="<?php echo $row['company'];?>" 
						data-discounttype="<?php echo $row['discounttype'];?>" 
						data-price="<?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ")." ".$settings['currency'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<?php
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];
						?>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>
						<td><a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Proposition d'honoraire" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['code'];?></a></td>
						<td>
							<?php
							if($row['client'] > 0){
								?>
							<span style="white-space:nowrap;"><?php echo $clients[$row['client']]['fullname']." (".($clients[$row['client']]['codecl']!=""?$clients[$row['client']]['codecl']:$clients[$row['client']]['code']).")";?></span>
								<?php
							}
							elseif($row['prospect'] > 0){
								?>
							<span style="white-space:nowrap;"><?php echo $prospect['fullname']." (Prospect)";?></span>
								<?php
							}
							?>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td>
							<?php
							$products = '<table>
											<tr class="lx-firstrow">
												<td style="width:500px;">Designation</td>
												<td>Qté</td>
												<td style="width:100px;">P.U. HT</td>
												<td class="lx-discount">Remise totale HT</td>
												<td>TVA</td>
												<td>Total HT</td>
												<td class="lx-qty-back">Qté à retourner</td>
												<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
												<td class="lx-note-cell" style="display:none;">Description</td>
												<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
											</tr>
											<tr class="lx-secondrow" style="display:none;">
												<td colspan="10">
													<center><i>0 produits / services</i></center>
												</td>
											</tr>';
							$price1 = 0;
							$price2 = 0;
							$discount = 0;
							$discounttype = "";
							$ttva = 0;
							$titles = explode("||",$row['title']);
							$units = explode("||",$row['unit']);
							$qtys = array_map('floatval',explode("||",$row['qty']));
							$uprices = array_map('floatval',explode("||",$row['uprice']));
							$tprices = array_map('floatval',explode("||",$row['tprice']));
							$ptvas = array_map('floatval',explode("||",$row['ptva']));
							$abovenotes = explode("||",$row['abovenote']);
							$belownotes = explode("||",$row['belownote']);
							$discounttypes = explode("||",$row['discounttype']);
							$discounts = array_map('floatval',explode("||",$row['discount']));
							for($i=0;$i<count($titles);$i++){
								$d = 0;
								if($discounttypes[$i] == $settings['currency']){
									$discount += $discounts[$i];
									$d = $discounts[$i];
								}
								else{
									$discount += (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
									$d = (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
								}
								?>
							<span style="padding-bottom:8px;border-bottom:1px solid #ccc;"><?php echo $titles[$i]."<br />Prix unitaire: <b>".number_format((float)($uprices[$i]*(1+($ptvas[$i]/100))),2,"."," ")." ".$settings['currency']."</b> TTC <br />Quantité: <b>".$qtys[$i]." ".$units[$i]."</b>".($d!="0"?"<br />Remise totale HT: <b>".number_format((float)$d,2,"."," ")." ".$settings['currency']."</b>":"");?></span>
								<?php
								$price1 += (($tprices[$i]+$d)*(1+($ptvas[$i]/100)));
								$price2 += ($tprices[$i]*(1+($ptvas[$i]/100)));
								$ttva += ($tprices[$i]*($ptvas[$i]/100));
								$discounttype = $discounttypes[$i];
								$products .= '<tr class="lx-otherrow" data-title="" data-abovenote="'.htmlspecialchars($abovenotes[$i]).'" data-belownote="'.htmlspecialchars($belownotes[$i]).'" data-qty="'.$qtys[$i].'" data-unit="'.$units[$i].'" data-uprice="'.$uprices[$i].'" data-remise="'.$d.'" data-oremise="'.$d.'" data-tva="'.$ptvas[$i].'" data-tprice="'.($uprices[$i]*$qtys[$i]).'" data-ttprice="'.$tprices[$i].'">
												<td style="width:auto;" class="lx-product-title">'.$titles[$i].'</td>
												<td><span>'.$qtys[$i].' '.$units[$i].'</span></td>
												<td><span>'.number_format((float)$uprices[$i],2,"."," ").'</span></td>
												<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" value="'.$discounts[$i].'" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;">'.$discounttype.'</u></label></div></td>
												<td><span>'.$ptvas[$i].'%</span></td>
												<td><span>'.number_format((float)$tprices[$i],2,"."," ").'</span></td>
												<td class="lx-qty-back"><div class="lx-textfield"><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>
												<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>
												<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>
												<td class="lx-delete-cell">
												<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>
												<div class="lx-delete-payment-choice">
												<a href="javascript:;" class="lx-delete-this">Oui</a>
												<a href="javascript:;" class="lx-no-delete-payment">Non</a>
												</div>
												</td>
											</tr>';
							}
							$products .= '</table>';
							$montant += $price2;
							?>				
						</td>
						<td>
							<?php
							if($discount > 0){
							?>
							<span><ins style="white-space:nowrap;">Montant sans remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+$discount)-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$discount,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] > 0){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td>
							<?php
							if($row['abovetable'] != ""){
							?>
							<span style="white-space:nowrap;width:300px;"><strong>Note au-dessus de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['abovetable']);?></span>
							<?php
							}
							if($row['modepayment'] != ""){
							?>
							<span><strong>Note au-dessous de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['modepayment']);?></span>
							<?php
							}
							if($row['conditions'] != ""){
							?>
							<span><strong>Conditions (au-dessous de tableau) :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['conditions']);?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['picture'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['picture'];?>" data-pic="<?php echo $row['picture'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['picture'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<?php
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$row1 = $back1->fetch();
						?>
						<td><span style="white-space:nowrap"><?php echo $row1['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="documents" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="documents" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									<?php
									if($row['client'] > 0){
										?>
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
										<?php
									}
									elseif($row['prospect'] > 0){
										?>
									data-client="<?php echo $prospect['fullname'];?>"
									data-email1="<?php echo $prospect['email'];?>"
									data-email="<?php echo $prospect['email'];?>"
									data-phone="<?php echo $prospect['phone'];?>"
									data-whatsapp="<?php echo $prospect['whatsapp'];?>"
										<?php
									}
									?>
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Proposition d'honoraire"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-company="<?php echo $row['company'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Envoyer le document"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer le document</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Envoyer le document" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Proposition d'honoraire" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature">Imprimer</a>
								<?php
								if(preg_match("#Modifier Devis,#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Modifier" 
									data-id="<?php echo $row['id'];?>"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="command">Modifier</a>
									<?php
								}
								if(preg_match("#Transformation / Dupplication documents#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Transformer / Dupliquer" 
									data-id="0"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="command">Transformer / Duppliquer</a>
									<?php
								}
								?>
								<a href="javascript:;" class="lx-edit lx-edit-extrainfo lx-open-popup" data-table="extrainfo" data-header="Informations supplémentaires sur le document" 
									data-id="<?php echo $row['id'];?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="extrainfo">Informations supplémentaires sur le document</a>
								<?php
								if(preg_match("#Supprimer Devis,#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-command lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Client / Prospect<i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails Proposition d'honoraires</td>
						<td style="text-align:right;"><?php echo number_format((float)$montant,2,"."," ")." ".$settings['currency'];?> TTC <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> note(s) d'honoraires de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> note(s) d'honoraires de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}

			if($_POST['action'] == "loadfacturesproforma"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails facture proforma</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM documents d WHERE category='client' AND type='factureproforma' AND trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$montant = 0;
					while($row = $back->fetch()){
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();	

						$back1 = $bdd->query("SELECT * FROM prospects WHERE id='".$row['prospect']."'");
						$prospect = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facture" value="<?php echo $row['id'];?>" 
						data-client="<?php echo $row['client'];?>" 
						data-prospect="<?php echo $row['prospect'];?>" 
						data-code="<?php echo $row['code'];?>" 
						data-company="<?php echo $row['company'];?>" 
						data-discounttype="<?php echo $row['discounttype'];?>" 
						data-price="<?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ")." ".$settings['currency'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<?php
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];
						?>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>
						<td><a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Facture proforma" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['code'];?></a></td>
						<td>
							<span style="white-space:nowrap;"><?php echo $clients[$row['client']]['fullname']." (".($clients[$row['client']]['codecl']!=""?$clients[$row['client']]['codecl']:$clients[$row['client']]['code']).")";?></span>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td>
							<?php
							$products = '<table>
											<tr class="lx-firstrow">
												<td style="width:500px;">Designation</td>
												<td>Qté</td>
												<td style="width:100px;">P.U. HT</td>
												<td class="lx-discount">Remise totale HT</td>
												<td>TVA</td>
												<td>Total HT</td>
												<td class="lx-qty-back">Qté à retourner</td>
												<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
												<td class="lx-note-cell" style="display:none;">Description</td>
												<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
											</tr>
											<tr class="lx-secondrow" style="display:none;">
												<td colspan="10">
													<center><i>0 produits / services</i></center>
												</td>
											</tr>';
							$price1 = 0;
							$price2 = 0;
							$discount = 0;
							$discounttype = "";
							$ttva = 0;
							$titles = explode("||",$row['title']);
							$units = explode("||",$row['unit']);
							$qtys = array_map('floatval',explode("||",$row['qty']));
							$uprices = array_map('floatval',explode("||",$row['uprice']));
							$tprices = array_map('floatval',explode("||",$row['tprice']));
							$ptvas = array_map('floatval',explode("||",$row['ptva']));
							$abovenotes = explode("||",$row['abovenote']);
							$belownotes = explode("||",$row['belownote']);
							$discounttypes = explode("||",$row['discounttype']);
							$discounts = array_map('floatval',explode("||",$row['discount']));
							for($i=0;$i<count($titles);$i++){
								$d = 0;
								if($discounttypes[$i] == $settings['currency']){
									$discount += $discounts[$i];
									$d = $discounts[$i];
								}
								else{
									$discount += (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
									$d = (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
								}
								?>
							<span style="padding-bottom:8px;border-bottom:1px solid #ccc;"><?php echo $titles[$i]."<br />Prix unitaire: <b>".number_format((float)($uprices[$i]*(1+($ptvas[$i]/100))),2,"."," ")." ".$settings['currency']."</b> TTC <br />Quantité: <b>".$qtys[$i]." ".$units[$i]."</b>".($d!="0"?"<br />Remise totale HT: <b>".number_format((float)$d,2,"."," ")." ".$settings['currency']."</b>":"");?></span>
								<?php
								$price1 += (($tprices[$i]+$d)*(1+($ptvas[$i]/100)));
								$price2 += ($tprices[$i]*(1+($ptvas[$i]/100)));
								$ttva += ($tprices[$i]*($ptvas[$i]/100));
								$discounttype = $discounttypes[$i];
								$products .= '<tr class="lx-otherrow" data-title="" data-abovenote="'.htmlspecialchars($abovenotes[$i]).'" data-belownote="'.htmlspecialchars($belownotes[$i]).'" data-qty="'.$qtys[$i].'" data-unit="'.$units[$i].'" data-uprice="'.$uprices[$i].'" data-remise="'.$d.'" data-oremise="'.$d.'" data-tva="'.$ptvas[$i].'" data-tprice="'.($uprices[$i]*$qtys[$i]).'" data-ttprice="'.$tprices[$i].'">
												<td style="width:auto;" class="lx-product-title">'.$titles[$i].'</td>
												<td><span>'.$qtys[$i].' '.$units[$i].'</span></td>
												<td><span>'.number_format((float)$uprices[$i],2,"."," ").'</span></td>
												<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" value="'.$discounts[$i].'" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;">'.$discounttype.'</u></label></div></td>
												<td><span>'.$ptvas[$i].'%</span></td>
												<td><span>'.number_format((float)$tprices[$i],2,"."," ").'</span></td>
												<td class="lx-qty-back"><div class="lx-textfield"><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>
												<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>
												<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>
												<td class="lx-delete-cell">
												<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>
												<div class="lx-delete-payment-choice">
												<a href="javascript:;" class="lx-delete-this">Oui</a>
												<a href="javascript:;" class="lx-no-delete-payment">Non</a>
												</div>
												</td>
											</tr>';
							}
							$products .= '</table>';
							$montant += $price2;
							?>				
						</td>
						<td>
							<?php
							if($discount > 0){
							?>
							<span><ins style="white-space:nowrap;">Montant sans remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+$discount)-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$discount,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] > 0){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td>
							<?php
							if($row['abovetable'] != ""){
							?>
							<span style="white-space:nowrap;width:300px;"><strong>Note au-dessus de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['abovetable']);?></span>
							<?php
							}
							if($row['modepayment'] != ""){
							?>
							<span><strong>Note au-dessous de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['modepayment']);?></span>
							<?php
							}
							if($row['conditions'] != ""){
							?>
							<span><strong>Conditions (au-dessous de tableau) :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['conditions']);?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['picture'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['picture'];?>" data-pic="<?php echo $row['picture'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['picture'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<?php
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$row1 = $back1->fetch();
						?>
						<td><span style="white-space:nowrap"><?php echo $row1['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="documents" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="documents" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Facture proforma"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-company="<?php echo $row['company'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Envoyer le document"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer le document</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Envoyer le document" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Facture proforma" class="lx-download-file1 lx-print-f lx-open-popup" data-title="signature">Imprimer</a>
								<?php
								if(preg_match("#Modifier Factures proforma#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Modifier" 
									data-id="<?php echo $row['id'];?>"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="command">Modifier</a>
									<?php
								}
								if(preg_match("#Transformation / Dupplication documents#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Transformer / Dupliquer" 
									data-id="0"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="command">Transformer / Duppliquer</a>
									<?php
								}
								?>
								<a href="javascript:;" class="lx-edit lx-edit-extrainfo lx-open-popup" data-table="extrainfo" data-header="Informations supplémentaires sur le document" 
									data-id="<?php echo $row['id'];?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="extrainfo">Informations supplémentaires sur le document</a>
								<?php
								if(preg_match("#Supprimer Factures proforma#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-command lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails facture proforma</td>
						<td style="text-align:right;"><?php echo number_format((float)$montant,2,"."," ")." ".$settings['currency'];?> TTC <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> facture(s) proforma de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> facture(s) proforma de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}

			if($_POST['action'] == "loadbl"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails bon de livraison</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM documents d WHERE category='client' AND type='bl' AND trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$montant = 0;
					while($row = $back->fetch()){
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();	

						$back1 = $bdd->query("SELECT * FROM prospects WHERE id='".$row['prospect']."'");
						$prospect = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facture" value="<?php echo $row['id'];?>" 
						data-client="<?php echo $row['client'];?>" 
						data-prospect="<?php echo $row['prospect'];?>" 
						data-code="<?php echo $row['code'];?>" 
						data-company="<?php echo $row['company'];?>" 
						data-discounttype="<?php echo $row['discounttype'];?>" 
						data-price="<?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ")." ".$settings['currency'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<?php
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];
						?>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>
						<td><a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Bon de livraison" class="lx-download-file1 lx-print-f lx-print-bl lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['code'];?></a></td>
						<td>
							<span style="white-space:nowrap;"><?php echo $clients[$row['client']]['fullname']." (".($clients[$row['client']]['codecl']!=""?$clients[$row['client']]['codecl']:$clients[$row['client']]['code']).")";?></span>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td>
							<?php
							$products = '<table>
											<tr class="lx-firstrow">
												<td style="width:500px;">Designation</td>
												<td>Qté</td>
												<td style="width:100px;">P.U. HT</td>
												<td class="lx-discount">Remise totale HT</td>
												<td>TVA</td>
												<td>Total HT</td>
												<td class="lx-qty-back">Qté à retourner</td>
												<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
												<td class="lx-note-cell" style="display:none;">Description</td>
												<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
											</tr>
											<tr class="lx-secondrow" style="display:none;">
												<td colspan="10">
													<center><i>0 produits / services</i></center>
												</td>
											</tr>';
							$price1 = 0;
							$price2 = 0;
							$discount = 0;
							$discounttype = "";
							$ttva = 0;
							$titles = explode("||",$row['title']);
							$units = explode("||",$row['unit']);
							$qtys = array_map('floatval',explode("||",$row['qty']));
							$uprices = array_map('floatval',explode("||",$row['uprice']));
							$tprices = array_map('floatval',explode("||",$row['tprice']));
							$ptvas = array_map('floatval',explode("||",$row['ptva']));
							$abovenotes = explode("||",$row['abovenote']);
							$belownotes = explode("||",$row['belownote']);
							$discounttypes = explode("||",$row['discounttype']);
							$discounts = array_map('floatval',explode("||",$row['discount']));
							for($i=0;$i<count($titles);$i++){
								$d = 0;
								if($discounttypes[$i] == $settings['currency']){
									$discount += $discounts[$i];
									$d = $discounts[$i];
								}
								else{
									$discount += (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
									$d = (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
								}
								?>
							<span style="padding-bottom:8px;border-bottom:1px solid #ccc;"><?php echo $titles[$i]."<br />Prix unitaire: <b>".number_format((float)($uprices[$i]*(1+($ptvas[$i]/100))),2,"."," ")." ".$settings['currency']."</b> TTC <br />Quantité: <b>".$qtys[$i]." ".$units[$i]."</b>".($d!="0"?"<br />Remise totale HT: <b>".number_format((float)$d,2,"."," ")." ".$settings['currency']."</b>":"");?></span>
								<?php
								$price1 += (($tprices[$i]+$d)*(1+($ptvas[$i]/100)));
								$price2 += ($tprices[$i]*(1+($ptvas[$i]/100)));
								$ttva += ($tprices[$i]*($ptvas[$i]/100));
								$discounttype = $discounttypes[$i];
								$products .= '<tr class="lx-otherrow" data-title="" data-abovenote="'.htmlspecialchars($abovenotes[$i]).'" data-belownote="'.htmlspecialchars($belownotes[$i]).'" data-qty="'.$qtys[$i].'" data-unit="'.$units[$i].'" data-uprice="'.$uprices[$i].'" data-remise="'.$d.'" data-oremise="'.$d.'" data-tva="'.$ptvas[$i].'" data-tprice="'.($uprices[$i]*$qtys[$i]).'" data-ttprice="'.$tprices[$i].'">
												<td style="width:auto;" class="lx-product-title">'.$titles[$i].'</td>
												<td><span>'.$qtys[$i].' '.$units[$i].'</span></td>
												<td><span>'.number_format((float)$uprices[$i],2,"."," ").'</span></td>
												<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" value="'.$discounts[$i].'" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;">'.$discounttype.'</u></label></div></td>
												<td><span>'.$ptvas[$i].'%</span></td>
												<td><span>'.number_format((float)$tprices[$i],2,"."," ").'</span></td>
												<td class="lx-qty-back"><div class="lx-textfield"><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>
												<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>
												<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>
												<td class="lx-delete-cell">
												<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>
												<div class="lx-delete-payment-choice">
												<a href="javascript:;" class="lx-delete-this">Oui</a>
												<a href="javascript:;" class="lx-no-delete-payment">Non</a>
												</div>
												</td>
											</tr>';
							}
							$products .= '</table>';
							$montant += $price2;
							?>				
						</td>
						<td>
							<?php
							if($discount > 0){
							?>
							<span><ins style="white-space:nowrap;">Montant sans remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+$discount)-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$discount,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] > 0){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td>
							<?php
							if($row['abovetable'] != ""){
							?>
							<span style="white-space:nowrap;width:300px;"><strong>Note au-dessus de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['abovetable']);?></span>
							<?php
							}
							if($row['modepayment'] != ""){
							?>
							<span><strong>Note au-dessous de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['modepayment']);?></span>
							<?php
							}
							if($row['conditions'] != ""){
							?>
							<span><strong>Conditions (au-dessous de tableau) :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['conditions']);?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['picture'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['picture'];?>" data-pic="<?php echo $row['picture'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['picture'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<?php
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$row1 = $back1->fetch();
						?>
						<td><span style="white-space:nowrap"><?php echo $row1['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="documents" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="documents" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Bon de livraison"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-company="<?php echo $row['company'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Envoyer le document"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer le document</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Envoyer le document" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Bon de livraison" class="lx-download-file1 lx-print-f lx-print-bl lx-open-popup" data-title="signature">Imprimer</a>
								<?php
								if(preg_match("#Modifier Bons de livraison#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Modifier" 
									data-id="<?php echo $row['id'];?>"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="command">Modifier</a>
									<?php
								}
								if(preg_match("#Transformation / Dupplication documents#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Transformer / Dupliquer" 
									data-id="0"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="command">Transformer / Duppliquer</a>
									<?php
								}
								?>
								<a href="javascript:;" class="lx-edit lx-edit-extrainfo lx-open-popup" data-table="extrainfo" data-header="Informations supplémentaires sur le document" 
									data-id="<?php echo $row['id'];?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="extrainfo">Informations supplémentaires sur le document</a>
								<?php
								if(preg_match("#Supprimer Bons de livraison#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-command lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails bon de livraison</td>
						<td style="text-align:right;"><?php echo number_format((float)$montant,2,"."," ")." ".$settings['currency'];?> TTC <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> bon(s) de livraison de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> bon(s) de livraison de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}
			
			if($_POST['action'] == "loadbs"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails bon de sortie</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM documents d WHERE category='client' AND type='bs' AND trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$montant = 0;
					while($row = $back->fetch()){
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();	

						$back1 = $bdd->query("SELECT * FROM prospects WHERE id='".$row['prospect']."'");
						$prospect = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facture" value="<?php echo $row['id'];?>" 
						data-client="<?php echo $row['client'];?>" 
						data-prospect="<?php echo $row['prospect'];?>" 
						data-code="<?php echo $row['code'];?>" 
						data-company="<?php echo $row['company'];?>" 
						data-discounttype="<?php echo $row['discounttype'];?>" 
						data-price="<?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ")." ".$settings['currency'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<?php
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];
						?>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>
						<td><a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Bon de sortie" class="lx-download-file1 lx-print-f lx-print-bl lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['code'];?></a></td>
						<td>
							<span style="white-space:nowrap;"><?php echo $clients[$row['client']]['fullname']." (".($clients[$row['client']]['codecl']!=""?$clients[$row['client']]['codecl']:$clients[$row['client']]['code']).")";?></span>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td>
							<?php
							$products = '<table>
											<tr class="lx-firstrow">
												<td style="width:500px;">Designation</td>
												<td>Qté</td>
												<td style="width:100px;">P.U. HT</td>
												<td class="lx-discount">Remise totale HT</td>
												<td>TVA</td>
												<td>Total HT</td>
												<td class="lx-qty-back">Qté à retourner</td>
												<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
												<td class="lx-note-cell" style="display:none;">Description</td>
												<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
											</tr>
											<tr class="lx-secondrow" style="display:none;">
												<td colspan="10">
													<center><i>0 produits / services</i></center>
												</td>
											</tr>';
							$price1 = 0;
							$price2 = 0;
							$discount = 0;
							$discounttype = "";
							$ttva = 0;
							$titles = explode("||",$row['title']);
							$units = explode("||",$row['unit']);
							$qtys = array_map('floatval',explode("||",$row['qty']));
							$uprices = array_map('floatval',explode("||",$row['uprice']));
							$tprices = array_map('floatval',explode("||",$row['tprice']));
							$ptvas = array_map('floatval',explode("||",$row['ptva']));
							$abovenotes = explode("||",$row['abovenote']);
							$belownotes = explode("||",$row['belownote']);
							$discounttypes = explode("||",$row['discounttype']);
							$discounts = array_map('floatval',explode("||",$row['discount']));
							for($i=0;$i<count($titles);$i++){
								$d = 0;
								if($discounttypes[$i] == $settings['currency']){
									$discount += $discounts[$i];
									$d = $discounts[$i];
								}
								else{
									$discount += (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
									$d = (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
								}
								?>
							<span style="padding-bottom:8px;border-bottom:1px solid #ccc;"><?php echo $titles[$i]."<br />Prix unitaire: <b>".number_format((float)($uprices[$i]*(1+($ptvas[$i]/100))),2,"."," ")." ".$settings['currency']."</b> TTC <br />Quantité: <b>".$qtys[$i]." ".$units[$i]."</b>".($d!="0"?"<br />Remise totale HT: <b>".number_format((float)$d,2,"."," ")." ".$settings['currency']."</b>":"");?></span>
								<?php
								$price1 += (($tprices[$i]+$d)*(1+($ptvas[$i]/100)));
								$price2 += ($tprices[$i]*(1+($ptvas[$i]/100)));
								$ttva += ($tprices[$i]*($ptvas[$i]/100));
								$discounttype = $discounttypes[$i];
								$products .= '<tr class="lx-otherrow" data-title="" data-abovenote="'.htmlspecialchars($abovenotes[$i]).'" data-belownote="'.htmlspecialchars($belownotes[$i]).'" data-qty="'.$qtys[$i].'" data-unit="'.$units[$i].'" data-uprice="'.$uprices[$i].'" data-remise="'.$d.'" data-oremise="'.$d.'" data-tva="'.$ptvas[$i].'" data-tprice="'.($uprices[$i]*$qtys[$i]).'" data-ttprice="'.$tprices[$i].'">
												<td style="width:auto;" class="lx-product-title">'.$titles[$i].'</td>
												<td><span>'.$qtys[$i].' '.$units[$i].'</span></td>
												<td><span>'.number_format((float)$uprices[$i],2,"."," ").'</span></td>
												<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" value="'.$discounts[$i].'" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;">'.$discounttype.'</u></label></div></td>
												<td><span>'.$ptvas[$i].'%</span></td>
												<td><span>'.number_format((float)$tprices[$i],2,"."," ").'</span></td>
												<td class="lx-qty-back"><div class="lx-textfield"><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>
												<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>
												<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>
												<td class="lx-delete-cell">
												<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>
												<div class="lx-delete-payment-choice">
												<a href="javascript:;" class="lx-delete-this">Oui</a>
												<a href="javascript:;" class="lx-no-delete-payment">Non</a>
												</div>
												</td>
											</tr>';
							}
							$products .= '</table>';
							$montant += $price2;
							?>				
						</td>
						<td>
							<?php
							if($discount > 0){
							?>
							<span><ins style="white-space:nowrap;">Montant sans remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+$discount)-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$discount,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] > 0){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td>
							<?php
							if($row['abovetable'] != ""){
							?>
							<span style="white-space:nowrap;width:300px;"><strong>Note au-dessus de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['abovetable']);?></span>
							<?php
							}
							if($row['modepayment'] != ""){
							?>
							<span><strong>Note au-dessous de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['modepayment']);?></span>
							<?php
							}
							if($row['conditions'] != ""){
							?>
							<span><strong>Conditions (au-dessous de tableau) :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['conditions']);?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['picture'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['picture'];?>" data-pic="<?php echo $row['picture'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['picture'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<?php
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$row1 = $back1->fetch();
						?>
						<td><span style="white-space:nowrap"><?php echo $row1['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="documents" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="documents" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $clients[$row['client']]['fullname'];?>"
									data-email1="<?php echo $clients[$row['client']]['email'];?>"
									data-email="<?php echo $clients[$row['client']]['email'];?>"
									data-phone="<?php echo $clients[$row['client']]['phone'];?>"
									data-whatsapp="<?php echo $clients[$row['client']]['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Bon de sortie"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-company="<?php echo $row['company'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Envoyer le document"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer le document</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Envoyer le document" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printclientdocs.php?id=<?php echo $row['id'];?>&title=Bon de sortie" class="lx-download-file1 lx-print-f lx-print-bl lx-open-popup" data-title="signature">Imprimer</a>
								<?php
								if(preg_match("#Modifier Bons de sortie#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Modifier" 
									data-id="<?php echo $row['id'];?>"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="command">Modifier</a>
									<?php
								}
								if(preg_match("#Transformation / Dupplication documents#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Transformer / Dupliquer" 
									data-id="0"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['client'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="command">Transformer / Duppliquer</a>
									<?php
								}
								?>
								<a href="javascript:;" class="lx-edit lx-edit-extrainfo lx-open-popup" data-table="extrainfo" data-header="Informations supplémentaires sur le document" 
									data-id="<?php echo $row['id'];?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="extrainfo">Informations supplémentaires sur le document</a>
								<?php
								if(preg_match("#Supprimer Bons de sortie#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-command lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails bon de sortie</td>
						<td style="text-align:right;"><?php echo number_format((float)$montant,2,"."," ")." ".$settings['currency'];?> TTC <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> bon(s) de sortie de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> bon(s) de sortie de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}
			
			if($_POST['action'] == "loadbc"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Fournisseur <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails bon de commande</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<!--<td>Statut et historique des paiements <i class="fa fa-sort" data-sort="rest"></i></td>-->
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM documents d WHERE category='supplier' AND type='bc' AND trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['supplier'] != ""){
						$req .= " AND supplier IN(".str_replace("||",",",$_POST['supplier']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$montant = 0;
					while($row = $back->fetch()){
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();	

						$back1 = $bdd->query("SELECT * FROM prospects WHERE id='".$row['prospect']."'");
						$prospect = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facture" value="<?php echo $row['id'];?>" 
						data-client="<?php echo $row['supplier'];?>" 
						data-prospect="<?php echo $row['prospect'];?>" 
						data-code="<?php echo $row['code'];?>" 
						data-company="<?php echo $row['company'];?>" 
						data-discounttype="<?php echo $row['discounttype'];?>" 
						data-price="<?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ")." ".$settings['currency'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<?php
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];
						?>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>
						<td><a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printsupplierdocs.php?id=<?php echo $row['id'];?>&title=Bon de commande" class="lx-download-file1 lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['code'];?></a></td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM suppliers WHERE id='".$row['supplier']."'");
							$supplier = $back1->fetch();
							?>
							<span style="white-space:nowrap;"><?php echo $supplier['title']!=""?$supplier['title']." (".($supplier['codefo']!=""?$supplier['codefo']:$supplier['code']).")":"";?></span>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td>
							<?php
							$products = '<table>
											<tr class="lx-firstrow">
												<td style="width:500px;">Designation</td>
												<td>Qté</td>
												<td style="width:100px;">P.U. HT</td>
												<td class="lx-discount">Remise totale HT</td>
												<td>TVA</td>
												<td>Total HT</td>
												<td class="lx-qty-back">Qté à retourner</td>
												<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
												<td class="lx-note-cell" style="display:none;">Description</td>
												<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
											</tr>
											<tr class="lx-secondrow" style="display:none;">
												<td colspan="10">
													<center><i>0 produits / services</i></center>
												</td>
											</tr>';
							$price1 = 0;
							$price2 = 0;
							$discount = 0;
							$discounttype = "";
							$ttva = 0;
							$titles = explode("||",$row['title']);
							$units = explode("||",$row['unit']);
							$qtys = array_map('floatval',explode("||",$row['qty']));
							$uprices = array_map('floatval',explode("||",$row['uprice']));
							$tprices = array_map('floatval',explode("||",$row['tprice']));
							$ptvas = array_map('floatval',explode("||",$row['ptva']));
							$abovenotes = explode("||",$row['abovenote']);
							$belownotes = explode("||",$row['belownote']);
							$discounttypes = explode("||",$row['discounttype']);
							$discounts = array_map('floatval',explode("||",$row['discount']));
							for($i=0;$i<count($titles);$i++){
								$d = 0;
								if($discounttypes[$i] == $settings['currency']){
									$discount += $discounts[$i];
									$d = $discounts[$i];
								}
								else{
									$discount += (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
									$d = (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
								}
								?>
							<span style="padding-bottom:8px;border-bottom:1px solid #ccc;"><?php echo $titles[$i]."<br />Prix unitaire: <b>".number_format((float)($uprices[$i]*(1+($ptvas[$i]/100))),2,"."," ")." ".$settings['currency']."</b> TTC <br />Quantité: <b>".$qtys[$i]." ".$units[$i]."</b>".($d!="0"?"<br />Remise totale HT: <b>".number_format((float)$d,2,"."," ")." ".$settings['currency']."</b>":"");?></span>
								<?php
								$price1 += (($tprices[$i]+$d)*(1+($ptvas[$i]/100)));
								$price2 += ($tprices[$i]*(1+($ptvas[$i]/100)));
								$ttva += ($tprices[$i]*($ptvas[$i]/100));
								$discounttype = $discounttypes[$i];
								$products .= '<tr class="lx-otherrow" data-title="" data-abovenote="'.htmlspecialchars($abovenotes[$i]).'" data-belownote="'.htmlspecialchars($belownotes[$i]).'" data-qty="'.$qtys[$i].'" data-unit="'.$units[$i].'" data-uprice="'.$uprices[$i].'" data-remise="'.$d.'" data-oremise="'.$d.'" data-tva="'.$ptvas[$i].'" data-tprice="'.($uprices[$i]*$qtys[$i]).'" data-ttprice="'.$tprices[$i].'">
												<td style="width:auto;" class="lx-product-title">'.$titles[$i].'</td>
												<td><span>'.$qtys[$i].' '.$units[$i].'</span></td>
												<td><span>'.number_format((float)$uprices[$i],2,"."," ").'</span></td>
												<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" value="'.$discounts[$i].'" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;">'.$discounttype.'</u></label></div></td>
												<td><span>'.$ptvas[$i].'%</span></td>
												<td><span>'.number_format((float)$tprices[$i],2,"."," ").'</span></td>
												<td class="lx-qty-back"><div class="lx-textfield"><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>
												<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>
												<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>
												<td class="lx-delete-cell">
												<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>
												<div class="lx-delete-payment-choice">
												<a href="javascript:;" class="lx-delete-this">Oui</a>
												<a href="javascript:;" class="lx-no-delete-payment">Non</a>
												</div>
												</td>
											</tr>';
							}
							$products .= '</table>';
							$montant += $price2;
							?>				
						</td>
						<td>
							<?php
							if($discount > 0){
							?>
							<span><ins style="white-space:nowrap;">Montant sans remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+$discount)-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$discount,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] > 0){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
						<!--<td>
							<?php
							$smpaid = 0;
							$phtable = "";
							$back1 = $bdd->query("SELECT * FROM payments WHERE doc='".$row['id']."'");
							if($back1->rowCount() > 0){
								$phtable .= '<table><tr class="lx-first-tr">';
								$phtable .= '<td>Montant payé</td>';
								$phtable .= '<td>Mode de paiement</td>';
								$phtable .= '<td>Imputation</td>';
								$phtable .= '<td>Compte banquaire</td>';
								$phtable .= '<td width="240"><div style="width:200px;">Note</div></td>';
								$phtable .= '<td>Date</td>';
								if(preg_match("#Suppression historique de paiement#",$_SESSION['easybm_roles'])){
									$phtable .= '<td>Supprimer</td>';
								}
								$phtable .= '</tr>';
							}
							while($row1 = $back1->fetch()){
								$back2 = $bdd->query("SELECT * FROM bankaccounts WHERE id='".$row1['rib']."'");
								$row2 = $back2->fetch();
								if($row1['paid'] == "0"){
									$smpaid += $row1['price'];
								}
								$phtable .= '<tr style="background:'.($row1['paid']=="1"?'#fbe1e1':'').';">';
								$phtable .= '<td><span>'.number_format((float)$row1['price'],2,","," ").' '.$settings['currency'].'</span>'.($row1['paid']=='1'?'<strong style="color:#FF0000;font-weight:bold;font-style:italic;">Impayé</strong>':'').'</td>';
								$phtable .= '<td><span>'.$row1['modepayment'].'</span></td>';
								$phtable .= '<td><span>'.$row1['imputation'].'</span></td>';
								$phtable .= '<td><span>'.$row2['bank'].':'.$row2['rib'].'</span></td>';
								$phtable .= '<td><span>'.$row1['note'].'</span></td>';
								$phtable .= '<td><span>'.($row1['dateadd']!=""?date("d/m/Y H:i",$row1['dateadd']):"").'</span></td>';
								if(preg_match("#Suppression historique de paiement#",$_SESSION['easybm_roles'])){
									$phtable .= '<td>
													<a href="javascript:;" class="lx-delete lx-delete-payment"
														data-id="'.$row['id'].'"
														data-company="'.$row['company'].'"
														data-modepayment="'.$row1['modepayment'].'"
														data-rib="'.$row1['rib'].'"
														data-imputation="'.$row1['imputation'].'"
														data-libelle="Suppression historique paiement bon de commande N° '.$row['code'].'"
														data-type="Entrée"
														data-price="'.$row1['price'].'">Supprimer</a>
													<div class="lx-delete-payment-choice">
														<a href="javascript:;" class="lx-yes-delete-payment" data-paid="'.$row1['price'].'" data-id="'.$row1['id'].'">Oui</a>
														<a href="javascript:;" class="lx-no-delete-payment">Non</a>
													</div>
												</td>';
								}
								$phtable .= '</tr>';
							}
							if($back1->rowCount() > 0){
								$phtable .= '</table>';
							}
							
							$bankaccounts = "<option value=''>Choisissez un compte banquaire</option>";
							$back1 = $bdd->query("SELECT * FROM bankaccounts WHERE company='".$row['company']."'");
							while($row1 = $back1->fetch()){
								$bankaccounts .= "<option value='".$row1['id']."'>".$row1['bank']." | ".$row1['rib']."</option>";
							}
							
							if(($price2-$smpaid-$smavoir) == $row['price']){
								$row['state'] = "Non payée";
							}
							elseif(($price2-$smpaid-$smavoir) > 0){
								$row['state'] = "Partiellement payée";
							}
							else{
								$row['state'] = 'Payée';
							}
							$req = $bdd->prepare("UPDATE documents SET state='".$row['state']."' WHERE id='".$row['id']."'");
							$req->execute();
							?>
							<span style="display:inline-block;padding:2px 5px;font-weight:500;background:<?php echo $row['state']=='Payée'?"#71b44c":($row['state']=='Non payée'?"#CC0000":"orange");?>;color:#FFFFFF;border-radius:4px;cursor:pointer;" 
								data-id="<?php echo $row['id'];?>" 
								data-paid="<?php echo $row['state'];?>" 
								data-rest="<?php echo number_format((float)abs($price2-$smpaid),2,".","");?>" 
								data-price="<?php echo number_format((float)abs($price2),2,".","");?>" 
								data-company="<?php echo $company;?>" 
								data-bankaccounts="<?php echo $bankaccounts;?>" 
								data-table="<?php echo str_replace("\"","'",$phtable);?>" class="<?php echo preg_match("#Modification statut de paiement#",$_SESSION['easybm_roles'])?"lx-edit-payment lx-open-popup":"";?>" 
								data-title="payment"><?php echo $row['state'];?></span>
							<span>Reste: <b><?php echo number_format((float)abs($price2-$smpaid),2,"."," ")." ".$settings['currency'];?></b></span>
						</td>-->
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td>
							<?php
							if($row['abovetable'] != ""){
							?>
							<span style="white-space:nowrap;width:300px;"><strong>Note au-dessus de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['abovetable']);?></span>
							<?php
							}
							if($row['modepayment'] != ""){
							?>
							<span><strong>Note au-dessous de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['modepayment']);?></span>
							<?php
							}
							if($row['conditions'] != ""){
							?>
							<span><strong>Conditions (au-dessous de tableau) :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['conditions']);?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['picture'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['picture'];?>" data-pic="<?php echo $row['picture'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['picture'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<?php
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$row1 = $back1->fetch();
						?>
						<td><span style="white-space:nowrap"><?php echo $row1['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="documents" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="documents" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $supplier['title'];?>"
									data-email1="<?php echo $supplier['respemail'];?>"
									data-whatsapp="<?php echo $supplier['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Bon de commande"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-company="<?php echo $row['company'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Envoyer le document"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer le document</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Envoyer le document" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printsupplierdocs.php?id=<?php echo $row['id'];?>&title=Bon de commande" class="lx-download-file1 lx-open-popup" data-title="signature">Imprimer</a>
								<?php
								if(preg_match("#Modifier Bons de sortie#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Modifier" 
									data-id="<?php echo $row['id'];?>"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['supplier'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="command">Modifier</a>
									<?php
								}
								if(preg_match("#Transformation / Dupplication documents#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Transformer / Dupliquer" 
									data-id="0"
									data-code="<?php echo substr($row['code'],7);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['supplier'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="command">Transformer / Duppliquer</a>
									<?php
								}
								?>
								<a href="javascript:;" class="lx-edit lx-edit-extrainfo lx-open-popup" data-table="extrainfo" data-header="Informations supplémentaires sur le document" 
									data-id="<?php echo $row['id'];?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="extrainfo">Informations supplémentaires sur le document</a>
								<?php
								if(preg_match("#Supprimer Bons de sortie#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-command lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Fournisseur <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails bon de commande</td>
						<td style="text-align:right;"><?php echo number_format((float)$montant,2,"."," ")." ".$settings['currency'];?> TTC <i class="fa fa-sort" data-sort="price"></i></td>
						<!--<td>Statut et historique des paiements <i class="fa fa-sort" data-sort="rest"></i></td>-->
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> bon(s) de commande de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> bon(s) de commande de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}

			if($_POST['action'] == "loadbre"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Fournisseur <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails bon de réception</td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM documents d WHERE category='supplier' AND type='bre' AND trash='".$_POST['state']."'".$multicompanies;
					if($_POST['keyword'] != ""){
						$req .= " AND (code LIKE '".normalizeString($_POST['keyword'])."' 
										OR note LIKE '".normalizeString($_POST['keyword'])."' 
										OR modepayment LIKE '".normalizeString($_POST['keyword'])."'
										OR conditions LIKE '".normalizeString($_POST['keyword'])."'
										OR abovetable LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['supplier'] != ""){
						$req .= " AND supplier IN(".str_replace("||",",",$_POST['supplier']).")";
					}
					if($_POST['user'] != ""){
						$req .= " AND user IN(".$_POST['user'].")";
					}
					if($_POST['product'] != ""){
						$comp = explode("||",$_POST['product']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "title LIKE '%".addslashes($comp[$j])."%'";
							}
							else{
								$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
							}
						}
						$req .= ")";
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datestartrecall'] != "" AND $_POST['dateendrecall'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestartrecall']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateendrecall'])) + (60*60*24) - 1;
						$req .= " AND (recalldate BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$montant = 0;
					while($row = $back->fetch()){
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$user = $back1->fetch();
						
						$back1 = $bdd->query("SELECT email FROM usersemails WHERE user='".$row['user']."' OR company='".$row['company']."'");
						$sender = $back1->fetch();	

						$back1 = $bdd->query("SELECT * FROM prospects WHERE id='".$row['prospect']."'");
						$prospect = $back1->fetch();
						?>
					<tr>
						<td><label><input type="checkbox" name="facture" value="<?php echo $row['id'];?>" 
						data-client="<?php echo $row['supplier'];?>" 
						data-prospect="<?php echo $row['prospect'];?>" 
						data-code="<?php echo $row['code'];?>" 
						data-company="<?php echo $row['company'];?>" 
						data-discounttype="<?php echo $row['discounttype'];?>" 
						data-price="<?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ")." ".$settings['currency'];?>" /><del class="checkmark"></del></label></td>
						<td><span><?php echo date("d/m/Y H:i",$row['dateadd']);?></span></td>
						<?php
						$company = $row['company'];
						$companytxt = $listcompanies[$row['company']];
						?>
						<td><span style="white-space:nowrap"><?php echo $companytxt;?></span></td>
						<td><a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printsupplierdocs.php?id=<?php echo $row['id'];?>&title=Bon de réception" class="lx-download-file1 lx-print-bl lx-open-popup" data-title="signature" style="white-space:nowrap;"><?php echo $row['code'];?></a></td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM suppliers WHERE id='".$row['supplier']."'");
							$supplier = $back1->fetch();
							?>
							<span style="white-space:nowrap;"><?php echo $supplier['title']!=""?$supplier['title']." (".($supplier['codefo']!=""?$supplier['codefo']:$supplier['code']).")":"";?></span>
						</td>
						<td>
							<?php
							$back1 = $bdd->query("SELECT * FROM doclabels WHERE label IN('".str_replace(",","','",ltrim($row['labels'],","))."')".$multicompanies);
							while($row1 = $back1->fetch()){
							?>
							<span style="display:inline-block;padding:10px 20px 10px 10px;font-weight:500;
								margin-right:5px;
								border-radius:6px 0px 0px 6px;
								  clip-path: polygon(
									0 0,
									calc(100% - 18px) 0,
									100% 18px,
									100% calc(100% - 18px),
									calc(100% - 18px) 100%,
									0 100%
								  );	background:<?php echo $row1['color'];?>;color:#FFFFFF;white-space:nowrap;"><?php echo $row1['label'];?></span>
							<?php
							}
							?>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<a href="javascript:;" class="lx-open-popup lx-add-tags" data-title="addtags" 
									data-id="<?php echo $row['id'];?>"
									data-table="documents"
									data-labels="<?php echo $row['labels'];?>" style="display:block;"><i class="fa fa-plus"></i> Affecter des tags</a>
							</div>
						</td>
						<td>
							<?php
							$products = '<table>
											<tr class="lx-firstrow">
												<td style="width:500px;">Designation</td>
												<td>Qté</td>
												<td style="width:100px;">P.U. HT</td>
												<td class="lx-discount">Remise totale HT</td>
												<td>TVA</td>
												<td>Total HT</td>
												<td class="lx-qty-back">Qté à retourner</td>
												<td class="lx-edit-cell"><i class="fa fa-edit"></i></td>
												<td class="lx-note-cell" style="display:none;">Description</td>
												<td class="lx-delete-cell"><i class="fa fa-trash-alt"></i></td>
											</tr>
											<tr class="lx-secondrow" style="display:none;">
												<td colspan="10">
													<center><i>0 produits / services</i></center>
												</td>
											</tr>';
							$price1 = 0;
							$price2 = 0;
							$discount = 0;
							$discounttype = "";
							$ttva = 0;
							$titles = explode("||",$row['title']);
							$units = explode("||",$row['unit']);
							$qtys = array_map('floatval',explode("||",$row['qty']));
							$uprices = array_map('floatval',explode("||",$row['uprice']));
							$tprices = array_map('floatval',explode("||",$row['tprice']));
							$ptvas = array_map('floatval',explode("||",$row['ptva']));
							$abovenotes = explode("||",$row['abovenote']);
							$belownotes = explode("||",$row['belownote']);
							$discounttypes = explode("||",$row['discounttype']);
							$discounts = array_map('floatval',explode("||",$row['discount']));
							for($i=0;$i<count($titles);$i++){
								$d = 0;
								if($discounttypes[$i] == $settings['currency']){
									$discount += $discounts[$i];
									$d = $discounts[$i];
								}
								else{
									$discount += (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
									$d = (($uprices[$i]*$qtys[$i])*($discounts[$i]/100));
								}
								?>
							<span style="padding-bottom:8px;border-bottom:1px solid #ccc;"><?php echo $titles[$i]."<br />Prix unitaire: <b>".number_format((float)($uprices[$i]*(1+($ptvas[$i]/100))),2,"."," ")." ".$settings['currency']."</b> TTC <br />Quantité: <b>".$qtys[$i]." ".$units[$i]."</b>".($d!="0"?"<br />Remise totale HT: <b>".number_format((float)$d,2,"."," ")." ".$settings['currency']."</b>":"");?></span>
								<?php
								$price1 += (($tprices[$i]+$d)*(1+($ptvas[$i]/100)));
								$price2 += ($tprices[$i]*(1+($ptvas[$i]/100)));
								$ttva += ($tprices[$i]*($ptvas[$i]/100));
								$discounttype = $discounttypes[$i];
								$products .= '<tr class="lx-otherrow" data-title="" data-abovenote="'.htmlspecialchars($abovenotes[$i]).'" data-belownote="'.htmlspecialchars($belownotes[$i]).'" data-qty="'.$qtys[$i].'" data-unit="'.$units[$i].'" data-uprice="'.$uprices[$i].'" data-remise="'.$d.'" data-oremise="'.$d.'" data-tva="'.$ptvas[$i].'" data-tprice="'.($uprices[$i]*$qtys[$i]).'" data-ttprice="'.$tprices[$i].'">
												<td style="width:auto;" class="lx-product-title">'.$titles[$i].'</td>
												<td><span>'.$qtys[$i].' '.$units[$i].'</span></td>
												<td><span>'.number_format((float)$uprices[$i],2,"."," ").'</span></td>
												<td class="lx-discount"><div class="lx-textfield"><label><span class="remiseht" style="display:none;">Remise totale HT</span><input type="number" name="discount" value="'.$discounts[$i].'" /><u class="discounttype" style="position:relative;top:0px;right:0px;margin-left:5px;">'.$discounttype.'</u></label></div></td>
												<td><span>'.$ptvas[$i].'%</span></td>
												<td><span>'.number_format((float)$tprices[$i],2,"."," ").'</span></td>
												<td class="lx-qty-back"><div class="lx-textfield"><label><span>Qté à retourner</span><input type="number" name="qtyback" min="1"data-isnumber="" data-message="Saisissez une quantité !" /></label></div></td>
												<td class="lx-edit-cell"><a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit" style="background:none;padding:0px;box-shadow:0px 0px 0px;color:#39add1;"></i></a></td>
												<td class="lx-note-cell" style="display:none;"><a href="javascript:;" class="lx-note-this" onclick="openPopup()" style="font-weight:500;color:#39add1;text-decoration:underline;"><i class="fa fa-pen" style="font-size:10px;"></i> Description</a></td>
												<td class="lx-delete-cell">
												<a href="javascript:;" class="lx-delete lx-delete-payment"><i class="fa fa-trash-alt"></i></a>
												<div class="lx-delete-payment-choice">
												<a href="javascript:;" class="lx-delete-this">Oui</a>
												<a href="javascript:;" class="lx-no-delete-payment">Non</a>
												</div>
												</td>
											</tr>';
							}
							$products .= '</table>';
							$montant += $price2;
							?>				
						</td>
						<td>
							<?php
							if($discount > 0){
							?>
							<span><ins style="white-space:nowrap;">Montant sans remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)($row['price']+$discount)-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Remise HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$discount,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant HT:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<span><ins style="white-space:nowrap;">Montant TVA:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$ttva,2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							if($row['rastva'] > 0){
							?>
							<span><ins style="white-space:nowrap;">RAS TVA:</ins><br /><b style="color:#FF0000;">-<?php echo number_format((float)$row['rastva'],2,"."," ");?> <?php echo $settings['currency'];?></b></span>
							<?php
							}
							?>
							<span><ins style="white-space:nowrap;">Montant TTC:</ins><br /><b style="white-space:nowrap;"><?php echo number_format((float)$row['price']-($row['rastva']),2,"."," ");?> <?php echo $settings['currency'];?></b></span>
						</td>
						<td><span><?php echo $row['note'];?></span></td>
						<?php
						$class = "";
						if($row['recalldate']!="" AND $row['recalldate'] < (strtotime(date("d-m-Y"))+(60*60*24))){
							$class = "lx-reported-blink blink";
						}
						?>
						<td><span class="<?php echo $class;?>" style="display:inline-block;color:#FFA500;"><b><?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?></b></span>
							<span style="margin-top:5px;"><?php echo nl2br($row['recallnote']);?></span>
							<div style="margin-top:5px;" class="lx-upload-files">
								<a href="javascript:;" class="lx-edit lx-edit-recall-note lx-open-popup" data-header="Modifier un" 
								data-id="<?php echo $row['id'];?>"
								data-recalldate="<?php echo $row['recalldate']!=""?date("d/m/Y",$row['recalldate']):"";?>"
								data-note="<?php echo addslashes($row['recallnote']);?>" data-title="recall">Rappel</a>
							</div>						
						</td>
						<td>
							<?php
							if($row['abovetable'] != ""){
							?>
							<span style="white-space:nowrap;width:300px;"><strong>Note au-dessus de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['abovetable']);?></span>
							<?php
							}
							if($row['modepayment'] != ""){
							?>
							<span><strong>Note au-dessous de tableau :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['modepayment']);?></span>
							<?php
							}
							if($row['conditions'] != ""){
							?>
							<span><strong>Conditions (au-dessous de tableau) :</strong></span>
							<span style="margin-bottom:10px;"><?php echo str_replace("<p>&nbsp;</p>","",$row['conditions']);?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['picture'] != ""){
								?>
							<div class="lx-photos-preview">
								<?php
								if($_SESSION['easybm_type'] == "moderator"){
								?>
								<a href="javascript:;" class="lx-delete-images"><i class="material-icons"><img src="images/close.svg" style="width:22px;padding:3px;background:#EEEEEE;border-radius:6px;" /></i></a>
								<div class="lx-delete-image-choice">
									<a href="javascript:;" class="lx-yes-delete-image" data-allpics="<?php echo $row['picture'];?>" data-pic="<?php echo $row['picture'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-image">Non</a>
								</div>		
								<?php
								}
								?>
								<img src="uploads/<?php echo $row['picture'];?>" class="lx-open-popup lx-open-zoombox" data-title="zoombox" />
							</div>
								<?php
							}
							?>
							<div class="lx-upload-photos">
								<input type="file" name="uploadphotos[]" id="uploadphotos<?php echo $row['id'];?>" data-table="documents" data-column="picture" data-id="<?php echo $row['id'];?>" accept="image/x-png,image/jpeg" />
								<a href="javascript:;" class="">Ajouter photo</a>
							</div>
						</td>
						<?php
						$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
						$row1 = $back1->fetch();
						?>
						<td><span style="white-space:nowrap"><?php echo $row1['fullname'];?></span></td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="documents" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="documents" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td>
							<div class="lx-upload-photos" style="white-space:nowrap;">
								<?php
								if(preg_match("#Envoi des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-send-message" data-title="message" 
									data-id="<?php echo $row['id'];?>"
									data-client="<?php echo $supplier['title'];?>"
									data-email1="<?php echo $supplier['respemail'];?>"
									data-whatsapp="<?php echo $supplier['whatsapp'];?>"
									data-sender="<?php echo isset($sender['email'])?$sender['email']:'';?>"
									data-senderphone="<?php echo $user['phone'];?>"
									data-typedoc="Bon de réception"
									data-numero_document="<?php echo $row['code'];?>"
									data-montant="<?php echo number_format((float)$price2,2,"."," ");?>"
									data-company="<?php echo $row['company'];?>"
									data-societe="<?php echo $companytxt;?>"
									data-delai_paiement="<?php echo $row['delay'];?>"
									data-echeance_paiement="<?php echo isset($timetodelay)?date("d/m/Y",$timetodelay):'';?>"
									data-button="Envoyer le document"
									data-function="<?php echo $user['job'];?>"
									data-utilisateur="<?php echo $user['fullname'];?>" style="display:inline-block;width:auto;"><i class="fa fa-paper-plane"></i> Envoyer le document</a>
								<?php
								}
								if(preg_match("#Consultation historique des messages#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-open-popup lx-message-history" data-title="messagehistory" 
									data-id="<?php echo $row['id'];?>"
									data-page="fc"
									data-button="Envoyer le document" style="display:inline-block;width:auto;"><i class="fa fa-eye"></i></a>
								<?php
								}
								?>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<a href="javascript:;" data-id="<?php echo $row['id'];?>" data-href="printsupplierdocs.php?id=<?php echo $row['id'];?>&title=Bon de réception" class="lx-download-file1 lx-print-bl lx-open-popup" data-title="signature">Imprimer</a>
								<?php
								if(preg_match("#Modifier Bons de réception#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Modifier" 
									data-id="<?php echo $row['id'];?>"
									data-code="<?php echo substr($row['code'],8);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['supplier'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>" data-title="command">Modifier</a>
									<?php
								}
								if(preg_match("#Transformation / Dupplication documents#",$_SESSION['easybm_roles'])){
									?>
								<a href="javascript:;" class="lx-edit lx-edit-command lx-open-popup" data-table="commands" data-header="Transformer / Dupliquer" 
									data-id="0"
									data-code="<?php echo substr($row['code'],8);?>"
									data-dateadd="<?php echo date("d/m/Y",$row['dateadd']);?>"
									data-company="<?php echo $row['company'];?>"
									data-exist="1"
									data-client="<?php echo $row['supplier'];?>"
									data-prospect="<?php echo $row['prospect'];?>"
									data-products="<?php echo htmlspecialchars($products, ENT_QUOTES);?>"
									data-discounttype="<?php echo $discounttype;?>"
									data-correctdoc="<?php echo $row['correctdoc'];?>"
									data-note="<?php echo str_replace("\"","'",sanitize_vars($row['note']));?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="command">Transformer / Duppliquer</a>
									<?php
								}
								?>
								<a href="javascript:;" class="lx-edit lx-edit-extrainfo lx-open-popup" data-table="extrainfo" data-header="Informations supplémentaires sur le document" 
									data-id="<?php echo $row['id'];?>"
									data-modepayment="<?php echo str_replace("\"","'",sanitize_vars($row['modepayment']));?>"
									data-conditions="<?php echo str_replace("\"","'",sanitize_vars($row['conditions']));?>"
									data-abovetable="<?php echo str_replace("\"","'",sanitize_vars($row['abovetable']));?>" data-title="extrainfo">Informations supplémentaires sur le document</a>
								<?php
								if(preg_match("#Supprimer Bons de réception#",$_SESSION['easybm_roles'])){
								?>
								<a href="javascript:;" class="lx-delete lx-delete-command lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
								<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Fournisseur <i class="fa fa-sort" data-sort="client"></i></td>
						<td style="min-width:200px;">Tags</td>
						<td style="min-width:300px;">Détails bon de réception</td>
						<td style="text-align:right;"><?php echo number_format((float)$montant,2,"."," ")." ".$settings['currency'];?> TTC <i class="fa fa-sort" data-sort="price"></i></td>
						<td><div style="width:200px;">Note</div></td>
						<td style="min-width:200px;">Rappel <i class="fa fa-sort" data-sort="recalldate"></i></td>
						<td>Informations supplémentaires sur le document</td>
						<td>Image sur le document</td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="user"></i></td>
						<td>Attachements</td>
						<td>Envoyer le document</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> bon(s) de réception de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> bon(s) de réception de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}

			if($_POST['action'] == "addexpense"){
				$date = getDateNow($_POST['dateadd']);
				if($_POST['datedue'] != "" AND preg_match("#^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/\d{4}$#",$_POST['datedue'])){
					$datedue = getDateNow($_POST['datedue']);
				}
				else{
					$datedue = "";
				}
				if($_POST['paid'] != "1" AND $_POST['datepaid'] != "" AND preg_match("#^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/\d{4}$#",$_POST['datepaid'])){
					$datepaid = getDateNow($_POST['datepaid']);
				}
				else{
					$datepaid = "";
				}
				if($_POST['remis'] == "1" AND $_POST['dateremis'] != "" AND preg_match("#^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/\d{4}$#",$_POST['dateremis'])){
					$dateremis = getDateNow($_POST['dateremis']);
				}
				else{
					$dateremis = "";
				}
				if($_POST['type'] == "Sortie"){
					$_POST['remis'] = "0";
					$dateremis = "";
				}
				if($_POST['modepayment'] != "Virement" OR $_POST['type'] != "Sortie"){
					$_POST['rib2'] = 0;
					$_POST['price2'] = 0;
					$_POST['currency'] = "";
				}
				if(isset($_POST['location'])){
					if($_POST['location'] == "in"){
						$_POST['price2'] = 0;
						$_POST['currency'] = "";
					}
				}
				$dupsupplier = 0;
				if($_POST['type'] == "Entrée"){
					if($_POST['existclient'] == "0"){
						$rand = "CL".date("ym")."-0001";
						$back1 = $bdd->query("SELECT code FROM clients ORDER BY id DESC LIMIT 0,1");
						if($back1->rowCount() > 0){
							$row1 = $back1->fetch();
							$rand = "CL".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
						}
						$req = $bdd->prepare("INSERT INTO clients(id,code,ice,fullname,phone,address,email,company,dateadd,trash) 
						VALUES ('0','".$rand."','','".sanitize_vars($_POST['client'])."','".sanitize_vars($_POST['phoneclient'])."','','".sanitize_vars($_POST['emailclient'])."','".str_replace("||",",",$_POST['company'])."','".time()."','1')");
						$req->execute();
						$back = $bdd->query("SELECT id FROM clients WHERE fullname='".$_POST['client']."' ORDER BY id DESC LIMIT 0,1");
						$row = $back->fetch();
						$_POST['clientid'] = $row['id'];
					}
					else{
						$req = $bdd->prepare("UPDATE clients SET phone='".sanitize_vars($_POST['phoneclient'])."',email='".sanitize_vars($_POST['emailclient'])."' WHERE id='".$_POST['clientid']."'");
						$req->execute();
					}
				}
				else{
					if($_POST['existsupplier'] == "0"){
						$back = $bdd->query("SELECT id FROM suppliers WHERE (title='".sanitize_vars($_POST['supplier'])."')");
						if($back->rowCount() == 0){
							$rand = "FR".date("ym")."-0001";
							$back1 = $bdd->query("SELECT code FROM suppliers ORDER BY id DESC LIMIT 0,1");
							if($back1->rowCount() > 0){
								$row1 = $back1->fetch();
								$rand = "FR".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
							}
							$req = $bdd->prepare("INSERT INTO suppliers(id,code,title,respname,respphone,respemail,respfax,ice,address,note,company,dateadd,trash) 
							VALUES ('0','".$rand."','".sanitize_vars($_POST['supplier'])."','','".sanitize_vars($_POST['phonesupplier'])."','".sanitize_vars($_POST['emailsupplier'])."','','','','','".str_replace("||",",",$_POST['company'])."','".time()."','1')");
							$req->execute();
							$back = $bdd->query("SELECT id FROM suppliers WHERE title='".$_POST['supplier']."' ORDER BY id DESC LIMIT 0,1");
							$row = $back->fetch();
							$_POST['supplierid'] = $row['id'];
						}
						else{
							$dupsupplier = 1;
							echo "Fournisseur existe déjà!";
						}
					}
					else{
						$req = $bdd->prepare("UPDATE suppliers SET respphone='".sanitize_vars($_POST['phonesupplier'])."',respemail='".sanitize_vars($_POST['emailsupplier'])."' WHERE id='".$_POST['supplierid']."'");
						$req->execute();
					}
				}
				if($dupsupplier == 0){
					if($_POST['id'] == "0"){
						$rand = substr($_POST['type'],0,1).date("ym")."-000001";
						$back = $bdd->query("SELECT code,dateadd FROM payments WHERE code LIKE '".substr($rand,0,1)."%' AND company='".$_POST['company']."' ORDER BY id DESC LIMIT 0,1");
						if($back->rowCount() > 0){
							$row1 = $back->fetch();
							if(date("Y") == date("Y",$row1['dateadd'])){
								$rand = substr($rand,0,1).date("ym")."-".sprintf('%06d',intval(substr($row1['code'],6))+1);
							}
						}
						
						$req = $bdd->prepare("INSERT INTO payments(id,code,worker,user,company,doc,type,nature,title,note,price,invoiced,modepayment,remis,dateremis,nremise,paid,tva,client,supplier,rib,rib2,price2,currency,imputation,datedue,datepaid,dateadd,trash) 
						VALUES ('0','".$rand."','".$_SESSION['easybm_id']."','".$_SESSION['easybm_id']."','".str_replace("||",",",$_POST['company'])."','0','".sanitize_vars($_POST['type'])."','".sanitize_vars($_POST['nature'])."','".sanitize_vars($_POST['title'])."','".addslashes($_POST['description'])."','".sanitize_vars($_POST['price'])."','".sanitize_vars($_POST['invoiced'])."','".sanitize_vars($_POST['modepayment'])."','".sanitize_vars($_POST['remis'])."','".$dateremis."','".sanitize_vars($_POST['nremise'])."','".sanitize_vars($_POST['paid'])."','".sanitize_vars((float)$_POST['tva'])."','".sanitize_vars((float)$_POST['clientid'])."','".sanitize_vars((float)$_POST['supplierid'])."','".sanitize_vars((float)$_POST['rib'])."','".sanitize_vars((float)$_POST['rib2'])."','".sanitize_vars((float)$_POST['price2'])."','".sanitize_vars($_POST['currency'])."','".sanitize_vars($_POST['imputation'])."','".$datedue."','".$datepaid."','".$date."','1')");
						$req->execute();
					}
					else{
						$req = $bdd->prepare("UPDATE payments SET company='".str_replace("||",",",$_POST['company'])."',
																	type='".sanitize_vars($_POST['type'])."',
																	nature='".sanitize_vars($_POST['nature'])."',
																	title='".sanitize_vars($_POST['title'])."',
																	note='".addslashes($_POST['description'])."',
																	price='".sanitize_vars($_POST['price'])."',
																	price2='".sanitize_vars($_POST['price2'])."',
																	currency='".sanitize_vars($_POST['currency'])."',
																	invoiced='".sanitize_vars($_POST['invoiced'])."',
																	modepayment='".sanitize_vars($_POST['modepayment'])."',
																	remis='".sanitize_vars($_POST['remis'])."',
																	dateremis='".$dateremis."',
																	nremise='".sanitize_vars($_POST['nremise'])."',
																	paid='".sanitize_vars($_POST['paid'])."',
																	tva='".sanitize_vars($_POST['tva'])."',
																	client='".sanitize_vars($_POST['clientid'])."',
																	supplier='".sanitize_vars($_POST['supplierid'])."',
																	rib='".sanitize_vars($_POST['rib'])."',
																	rib2='".sanitize_vars($_POST['rib2'])."',
																	imputation='".sanitize_vars($_POST['imputation'])."',
																	dateadd='".$date."',
																	datedue='".$datedue."',
																	datepaid='".$datepaid."' WHERE id='".$_POST['id']."'");
						$req->execute();
					}
					updatePrevDocKPI($_POST['clientid'],$_POST['supplierid']);
				}
			}

			if($_POST['action'] == "addcaisse"){
				$dateremis = "";
				$datedue = "";
				$datepaid = "";
				if($_POST['dateremis'] != ""){
					$dateremis = getDateNow($_POST['dateremis']);
				}
				if($_POST['datedue'] != ""){
					$datedue = getDateNow($_POST['datedue']);
				}
				if($_POST['datepaid'] != "" AND $_POST['paid'] == "0"){
					$datepaid = getDateNow($_POST['datepaid']);
				}
				$req = $bdd->prepare("UPDATE payments SET imputation='".sanitize_vars($_POST['imputation'])."',rib2='".sanitize_vars($_POST['rib2'])."',rib='".sanitize_vars($_POST['rib'])."',datedue='".$datedue."',datepaid='".$datepaid."',paid='".$_POST['paid']."',remis='".$_POST['remis']."',dateremis='".$dateremis."',nremise='".sanitize_vars($_POST['nremise'])."',note='".addslashes($_POST['note'])."' WHERE id='".$_POST['id']."'");
				$req->execute();
			}
			
			if($_POST['action'] == "deletecaisse"){
				$back = $bdd->query("SELECT id,doc,typedoc,attachments,client,supplier FROM payments WHERE id='".$_POST['id']."'");
				$command = $back->fetch();
				$pictures = explode(",",$command['attachments']);
				for($i=1;$i<count($pictures);$i++){
					if(file_exists("uploads/".$pictures[$i])){
						unlink("uploads/".$pictures[$i]);
					}
				}
				$req = $bdd->prepare("DELETE FROM payments WHERE id='".$_POST['id']."'");
				$req->execute();
				if($command['typedoc'] == "facturesupplier"){
					$req = $bdd->prepare("UPDATE facturesuppliers SET rastva='0' WHERE id='".$command['doc']."'");
					$req->execute();	
					updateFSKPI($command['doc']);
				}
				else{
					$req = $bdd->prepare("UPDATE documents SET rastva='0' WHERE id='".$command['doc']."'");
					$req->execute();						
					updateDocKPI($command['doc']);
				}
				updatePrevDocKPI($command['client'],$command['supplier']);
			}

			if($_POST['action'] == "editcaissebulk"){
				if($_POST['datepaid'] != "" AND preg_match("#^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/\d{4}$#",$_POST['datepaid'])){
					$datepaid = getDateNow($_POST['datepaid']);
				}
				else{
					$datepaid = "";
				}
				$req = $bdd->prepare("UPDATE payments SET rib='".$_POST['rib']."',datepaid='".$datepaid."' WHERE id IN(".$_POST['ids'].")");
				$req->execute();
			}

			if($_POST['action'] == "editremisebulk"){
				if($_POST['dateremis'] != "" AND preg_match("#^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/\d{4}$#",$_POST['dateremis'])){
					$datepaid = getDateNow($_POST['dateremis']);
				}
				else{
					$datepaid = "";
				}
				$req = $bdd->prepare("UPDATE payments SET remis='1',dateremis='".$datepaid."',nremise='".$_POST['nremise']."' WHERE id IN(".$_POST['ids'].")");
				$req->execute();
			}
			
			if($_POST['action'] == "loadcaisse"){
				?>
				<table cellpadding="0" cellspacing="0">
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Nature <i class="fa fa-sort" data-sort="type"></i></td>
						<td>Mode de paiement <i class="fa fa-sort" data-sort="modepayment"></i></td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td>Libellé <i class="fa fa-sort" data-sort="title"></i></td>
						<?php
						if(preg_match("#Consulter Clients|Consulter Factures clients|Consulter Notes honoraires|Consulter Propositions honoraires|Consulter Devis|Consulter Factures proforma|Consulter Bons de livraison|Consulter Bons de sortie|Consulter Bons de retour|Consulter Factures avoir#",$_SESSION['easybm_roles'])){
						?>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<?php
						}
						if(preg_match("#Consulter Fournisseurs|Consulter Factures fournisseurs|Consulter Bons de commande|Consulter Bons de réception#",$_SESSION['easybm_roles'])){
						?>
						<td>Fournisseur <i class="fa fa-sort" data-sort="supplier"></i></td>
						<?php
						}
						?>
						<td>Note <i class="fa fa-sort" data-sort="note"></i></td>
						<td>Date d'écheance <i class="fa fa-sort" data-sort="datedue"></i></td>
						<td>Imputation comptable <i class="fa fa-sort" data-sort="imputation"></i></td>
						<td>Compte banquaire encaissement / décaissement <i class="fa fa-sort" data-sort="rib"></i></td>
						<td>Date valeur (Rapprochement): <i class="fa fa-sort" data-sort="datepaid"></i></td>
						<td>Remis <i class="fa fa-sort" data-sort="remis"></i></td>
						<td>Impayé <i class="fa fa-sort" data-sort="paid"></i></td>
						<td>À comptabiliser <i class="fa fa-sort" data-sort="invoiced"></i></td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="worker"></i></td>
						<td>Attachements</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
					<?php
					$req = "SELECT * FROM payments WHERE trash='".$_POST['state']."'".$companies;
					if($_POST['keyword'] != ""){
						$req .= " AND (nremise LIKE '".normalizeString($_POST['keyword'])."' OR code LIKE '".normalizeString($_POST['keyword'])."' OR title LIKE '".normalizeString($_POST['keyword'])."' OR note LIKE '%".addslashes($_POST['keyword'])."%' OR type LIKE '".normalizeString($_POST['keyword'])."' OR price LIKE '".normalizeString($_POST['keyword'])."' OR imputation LIKE '".normalizeString($_POST['keyword'])."')";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", company)";
							}
						}
						$req .= ")";
					}
					if($_POST['status'] != ""){
						if($_POST['status'] != "a11a"){
							$req .= " AND (1=0 ";
						}
						else{
							$req .= " AND (1=1 ";
						}
						if(preg_match("#a1a#",$_POST['status'])){
							$req .= " OR (type='Entrée' AND datepaid='')";
						}
						if(preg_match("#a2a#",$_POST['status'])){
							$req .= " OR (type='Sortie' AND datepaid='')";
						}
						if(preg_match("#a3a#",$_POST['status'])){
							$req .= " OR (modepayment IN('Chèque','Effet') AND type='Entrée' AND datedue<>'' AND datepaid='' AND datedue<".time()." AND dateremis='')";
						}
						if(preg_match("#a14a#",$_POST['status'])){
							$req .= " OR (type='Entrée' AND datepaid='' AND dateremis='')";
						}
						if(preg_match("#a4a#",$_POST['status'])){
							$req .= " OR (type='Entrée' AND datedue<>'' AND datedue<".time()." AND datepaid='')";
						}
						if(preg_match("#a5a#",$_POST['status'])){
							$req .= " OR (type='Sortie' AND datedue<>'' AND datedue<".time()." AND datepaid='')";
						}
						if(preg_match("#a6a#",$_POST['status'])){
							$req .= " OR (dateremis<>'')";
						}
						if(preg_match("#a7a#",$_POST['status'])){
							$req .= " OR (type='Entrée' AND datepaid<>'')";
						}
						if(preg_match("#a12a#",$_POST['status'])){
							$req .= " OR (type='Sortie' AND datepaid<>'')";
						}
						if(preg_match("#a8a#",$_POST['status'])){
							$req .= " OR (paid='1')";
						}
						if(preg_match("#a9a#",$_POST['status'])){
							$req .= " OR (type='Entrée' AND datedue>".time().")";
						}
						if(preg_match("#a13a#",$_POST['status'])){
							$req .= " OR (type='Sortie' AND datedue>".time().")";
						}
						if(preg_match("#a10a#",$_POST['status'])){
							$req .= " OR (datedue='')";
						}
						if(preg_match("#a15a#",$_POST['status'])){
							$req .= " OR (datepaid<>'')";
						}
						if(preg_match("#a16a#",$_POST['status'])){
							$req .= " OR (datepaid='')";
						}
						if(preg_match("#a11a#",$_POST['status'])){
							$req .= ") AND paid='0'";
						}
						else{
							$req .= ")";
						}
					}
					if($_POST['worker'] != ""){
						$req .= " AND user IN (".$_POST['worker'].")";
					}
					if($_POST['client'] != ""){
						$req .= " AND client IN ('".str_replace("||","','",$_POST['client'])."')";
					}
					if($_POST['imputation'] != ""){
						$req .= " AND imputation IN('".str_replace("||","','",$_POST['imputation'])."')";
					}
					if($_POST['rib'] != ""){
						$req .= " AND rib IN (".$_POST['rib'].")";
					}
					if($_POST['supplier'] != ""){
						$req .= " AND supplier IN ('".str_replace("||","','",$_POST['supplier'])."')";
					}
					if($_POST['type'] != ""){
						$req .= " AND (1=2";
						if(preg_match("#Entrée manuelle#",$_POST['type'])){
							$req .= " OR (type='Entrée' AND typedoc='')";
						}
						if(preg_match("#Entrée depuis facture#",$_POST['type'])){
							$req .= " OR (type='Entrée' AND typedoc<>'')";
						}
						if(preg_match("#Sortie manuelle#",$_POST['type'])){
							$req .= " OR (type='Sortie' AND typedoc='')";
						}
						if(preg_match("#Sortie depuis facture#",$_POST['type'])){
							$req .= " OR (type='Sortie' AND typedoc<>'')";
						}
						$req .= ")";
					}
					if($_POST['invoiced'] != ""){
						$req .= " AND invoiced IN('".str_replace("||","','",$_POST['invoiced'])."')";
					}
					if($_POST['modepayment'] != ""){
						$req .= " AND modepayment IN('".str_replace("||","','",$_POST['modepayment'])."')";
					}
					if($_POST['pricemin'] != "" AND $_POST['pricemax'] != ""){
						$req .= " AND (price BETWEEN ".$_POST['pricemin']." AND ".$_POST['pricemax'].")";
					}
					if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24) - 1;
						$req .= " AND (dateadd BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['dateduestart'] != "" AND $_POST['datedueend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['dateduestart']));
						$dateend = strtotime(str_replace("/","-",$_POST['datedueend'])) + (60*60*24) - 1;
						$req .= " AND (datedue BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['datepaidstart'] != "" AND $_POST['datepaidend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['datepaidstart']));
						$dateend = strtotime(str_replace("/","-",$_POST['datepaidend'])) + (60*60*24) - 1;
						$req .= " AND (datepaid BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['dateremisstart'] != "" AND $_POST['dateremisend'] != ""){
						$datestart = strtotime(str_replace("/","-",$_POST['dateremisstart']));
						$dateend = strtotime(str_replace("/","-",$_POST['dateremisend'])) + (60*60*24) - 1;
						$req .= " AND (dateremis BETWEEN ".$datestart." AND ".$dateend.")";
					}
					if($_POST['sortby'] != ""){
						if($_POST['sortby'] == "rest"){
							$req .= " ORDER BY (price - COALESCE((SELECT SUM(price) FROM payments WHERE doc=d.id),0))";
						}
						else{
							$req .= " ORDER BY ".$_POST['sortby'];
						}
					}
					else{
						$req .= " ORDER BY id";
					}
					$req .= " ".$_POST['orderby'];
					$back4 = $bdd->query($req);$req2 = $req;
					$req .= " LIMIT ".$_POST['start'].",".$_POST['nbpage'];
					$back = $bdd->query($req);
					$nbin = 0;
					$totalin = 0;
					$totalinunpaid = 0;
					$nbout = 0;
					$totalout = 0;
					$totaloutunpaid = 0;
					$tvain = 0;
					$tvaout = 0;
					while($row = $back->fetch()){
						$background = "";
						if($row['paid'] == "1"){
							$background = "background:#fbe1e1;";
						}
						if($row['datepaid'] != ""){
							$background = "background:rgba(0,128,0,0.1);";
						}
						?>
					<tr style="<?php echo $background;?>" data-umpaid="<?php echo $background;?>">
						<td><label><input type="checkbox" name="caisse" value="<?php echo $row['id'];?>" data-company="<?php echo $row['company'];?>" data-type="<?php echo $row['type'];?>" data-mode="<?php echo preg_match("#Chèque|Effet|Espèce#",$row['modepayment'])?"Chèques":"";?>" /><del class="checkmark"></del></label></td>
						<td><span><ins><?php echo ($row['dateadd']!=""?date("d/m/Y H:i",$row['dateadd']):"&mdash;");?></ins></span></td>
						<?php
						$back1 = $bdd->query("SELECT rs FROM companies WHERE id='".$row['company']."'");
						$row1 = $back1->fetch();
						$companytxt = $row1['rs'];
						?>
						<td style="white-space:nowrap;"><span style="white-space:nowrap;"><?php echo $row1['rs'];?></span></td>						
						<td><span style="white-space:nowrap;"><?php echo $row['code'];?></span></td>
						<?php
						if($row['type'] == "Entrée"){
							$nbin++;
							if($row['paid'] == "0"){
								$totalin += $row['price'];								
							}
							else{
								$totalinunpaid += $row['price'];	
							}
							$color = "color:#a2d695;";
							$background = "background:#a2d695;";
							$tvain += number_format((float)$row['price']-($row['price']/(1+($row['tva']/100))),2,".","");
						}
						else{
							$nbout++;
							if($row['paid'] == "0"){
								$totalout += $row['price'];							
							}
							else{
								$totaloutunpaid += $row['price'];
							}
							$color = "color:#ff7373;";
							$background = "background:#ff7373;";
							$tvaout += number_format((float)$row['price']-($row['price']/(1+($row['tva']/100))),2,".","");
						}
						?>
						<td><span style="font-weight:600;<?php echo $color;?>"><?php echo $row['type']=="Entrée"?"Encaissement":"Dépense";?></span></td>
						<td><span><?php echo $row['modepayment'];?></span></td>
						<td style="<?php echo $background;?>;white-space:nowrap;text-align:right;">
							<?php
							if($row['price2'] != "0"){
								?>
							<span><?php echo number_format((float)$row['price2'],2,"."," ")." ".$row['currency'];?></span>
								<?php								
							}
							if($row['price'] != "0"){
							?>
							<span><?php echo number_format((float)$row['price'],2,"."," ")." ".$settings['currency'];?> TTC</span>
							<?php
							}
							if($row['invoiced'] == "Oui" AND $row['doc'] == "0"){
								?>
							<span>TVA: <?php echo $row['tva']."% (".number_format((float)$row['price']-($row['price']/(1+($row['tva']/100))),2,","," ")." ".$settings['currency'].")";?></span>
								<?php
							}
							?>							
						</td>
						<td><span style="width:170px;"><?php echo $row['title'];?></span></td>
						<?php
						if(preg_match("#Consulter Clients|Consulter Factures clients|Consulter Notes honoraires|Consulter Propositions honoraires|Consulter Devis|Consulter Factures proforma|Consulter Bons de livraison|Consulter Bons de sortie|Consulter Bons de retour|Consulter Factures avoir#",$_SESSION['easybm_roles'])){
							?>
						<td>
							<?php
							$back1 = $bdd->query("SELECT fullname,code,codecl FROM clients WHERE id='".$row['client']."'");
							if($back1->rowCount() > 0){
							$row1 = $back1->fetch();
							?>						
							<span style="white-space:nowrap;"><?php echo ($row1['fullname']!=""?($row1['fullname']." (".($row1['codecl']!=""?$row1['codecl']:$row1['code']).")"):"");?></span>
							<?php
							}
							?>
						</td>
							<?php
						}
						if(preg_match("#Consulter Fournisseurs|Consulter Factures fournisseurs|Consulter Bons de commande|Consulter Bons de réception#",$_SESSION['easybm_roles'])){
						?>
						<td>
							<?php
							$back1 = $bdd->query("SELECT title,code,codefo FROM suppliers WHERE id='".$row['supplier']."'");
							if($back1->rowCount() > 0){
							$row1 = $back1->fetch();
							?>
							<span style="white-space:nowrap;"><?php echo ($row1['title']!=""?($row1['title']." (".($row1['codefo']!=""?$row1['codefo']:$row1['code']).")"):"");?></span>
							<?php
							}
							if($row['rib2'] != "0"){
								$back1 = $bdd->query("SELECT * FROM suppliersbankaccounts WHERE id='".$row['rib2']."'");
								$rib = $back1->fetch();
								if($rib['iban'] != ""){
									?>
							<span><?php echo $rib['bank']." | ".$rib['iban']." | ".$rib['swift'];?></span>
									<?php
								}
								else{
									?>
							<span><?php echo $rib['bank']." | ".$rib['rib'];?></span>
									<?php									
								}
							}
							?>
						</td>
						<?php
						}
						?>
						<td><span style="width:200px;"><?php echo $row['note'];?></span></td>
						<?php
						$days = "";
						$color = "";
						$starttime = time();
						$endtime = $row['datedue']!=""?$row['datedue']:"";
						if($row['datedue'] != "" AND $row['datepaid'] == ""){
							$resttodelay = date_diff(date_create(date("Y-m-d",$endtime)),date_create(date("Y-m-d")));
							$color = "#71b44c";
							$days = $resttodelay->format("%a");
							if($starttime > $endtime){
								$days *= (-1);
							}
							if($days < 0){
								$color = "#FF0000";
							}
							else{
								$days = "+".$days;
							}	
						}						
						?>
						<td style="white-space:nowrap;">
							<span style="display:inline-block;padding:2px 5px;font-weight:500;<?php echo (($row['datepaid']=="" AND $row['datedue']!="" AND $row['datedue']<=time())?"background:orange;color:#FFFFFF;":"");?>border-radius:4px;"><ins><?php echo ($row['datedue']!=""?date("d/m/Y",$row['datedue']):"&mdash;");?></ins></span>
							<strong style="<?php echo $days==""?"display:none;":"";?>color:<?php echo $color;?>;font-weight:bold;">(<?php echo $days;?>)</strong>
						</td>	
						<td><span><?php echo $row['imputation'];?></span></td>
						<td>	
							<?php
							if($row['rib'] != "0"){
								$back1 = $bdd->query("SELECT * FROM bankaccounts WHERE id='".$row['rib']."'");
								$rib = $back1->fetch();
								?>
							<span><?php echo $rib['bank']." | ".$rib['rib'];?></span>
								<?php
							}
							?>
						</td>
						<td><span style="display:inline-block;padding:2px 5px;font-weight:500;<?php echo ($row['datepaid']!=""?"background:#7EC855;color:#FFFFFF;":"");?>border-radius:4px;"><ins><?php echo ($row['datepaid']!=""?date("d/m/Y",$row['datepaid']):"&mdash;");?></ins></span></td>
						<td>
							<?php
							if($row['remis'] == '1'){
								?>
						<span style="display:inline-block;padding:2px 5px;font-weight:500;background:#39add1;color:#FFFFFF;border-radius:4px;">Remis</span>
						<span style="white-space:nowrap;">Le: <?php echo date("d/m/Y",$row['dateremis']);?></span>
								<?php
								if($row['nremise'] != ""){
								?>
						<span>N° de remise: <?php echo $row['nremise'];?></span>
								<?php
								}
							}
							?>
						</td>
						<td>
							<?php
							if($row['paid'] == '1'){
								?>
						<span style="display:inline-block;padding:2px 5px;font-weight:500;background:#FF0000;color:#FFFFFF;border-radius:4px;">Impayé</span>
								<?php
							}
							?>
						</td>
						<td style="white-space:nowrap;">
							<?php
							if($row['invoiced'] == 'Non'){
								?>
							<span style="display:inline-block;padding:2px 5px;font-weight:500;color:orange;font-size:14px;border-radius:4px;white-space:nowrap;">Non Comptabilisée</span>
								<?php
							}
							else{
								?>
							<span style="display:inline-block;padding:2px 5px;font-weight:500;color:#71b44c;font-size:14px;border-radius:4px;">Comptabilisée</span>
								<?php
							}
							?>
						</td>
						<td style="white-space:nowrap;">
							<?php
							$back1 = $bdd->query("SELECT fullname,job,phone FROM users WHERE id='".$row['user']."'");
							if($back1->rowCount() > 0){
							$user = $back1->fetch();
							?>
							<span><?php echo $user['fullname'];?></span>
							<?php
							}
							?>
						</td>
						<td>
							<?php
							if($row['attachments'] != ""){
								$attachments = explode(",",$row['attachments']);
								for($i=1;$i<count($attachments);$i++){
									?>
							<span style="position:relative;white-space:nowrap;margin-bottom:5px;">
								<a href="javascript:;" class="lx-delete-file"><i class="fa fa-times"></i></a>
								<div class="lx-delete-file-choice">
									<a href="javascript:;" class="lx-yes-delete-file" data-table="payments" data-id="<?php echo $row['id'];?>" data-file="<?php echo $attachments[$i];?>" data-files="<?php echo $row['attachments'];?>">Oui</a>
									<a href="javascript:;" class="lx-no-delete-file">Non</a>
								</div>
								<a href="uploads/<?php echo $attachments[$i];?>" target="_blank" style="white-space:nowrap;"><?php echo $attachments[$i];?></a>
							</span>
									<?php
								}
							}
							?>
							<div class="lx-upload-files">
								<input type="file" name="uploadfiles[]" id="uploadfiles<?php echo $row['id'];?>" data-table="payments" data-id="<?php echo $row['id'];?>" multiple="multiple" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" />
								<a href="javascript:;" class=""><i class="fa fa-plus"></i> fichier</a>
							</div>
						</td>
						<td style="position:relative;">
							<?php
							if($_POST['state'] == 1){
								?>
							<a href="javascript:;" class="lx-open-edit-menu"><i class="fa fa-ellipsis-v"></i></a>
							<div class="lx-edit-menu">
								<?php
								if(preg_match("#Modifier Trésorerie#",$_SESSION['easybm_roles'])){
									$supplierbankaccounts = '<option value="">Choisissez un compte banquaire</option>';
									$req = "SELECT * FROM suppliersbankaccounts WHERE supplier='".$row['supplier']."'";
									$back1 = $bdd->query($req);
									while($row1 = $back1->fetch()){
										if($row1['location'] == "in"){
											$supplierbankaccounts .= '<option value="'.$row1['id'].'" data-location="in" data-supplier="'.$row1['supplier'].'">'.$row1['bank'].' | '.$row1['rib'].'</option>';
										}
										if($row1['location'] == "out"){
											$supplierbankaccounts .= '<option value="'.$row1['id'].'" data-location="out" data-supplier="'.$row1['supplier'].'">'.$row1['bank']." | ".$row1['iban']." | ".$row1['swift'].'</option>';
										}
									}
									$supplierbankaccounts .= '</select>';
								?>
							<a href="javascript:;" class="lx-edit <?php echo $row['doc']!="0"?"lx-edit-caisse":"lx-edit-expense";?> lx-open-popup" data-header="Modifier une" 
								data-id="<?php echo $row['id'];?>"
								data-code="<?php echo $row['code'];?>"
								data-client="<?php echo $row['client'];?>"
								data-supplier="<?php echo $row['supplier'];?>"
								data-type="<?php echo $row['type'];?>"
								data-titl="<?php echo $row['title'];?>"
								data-price="<?php echo $row['price'];?>"
								data-tva="<?php echo $row['tva'];?>"
								data-companytxt="<?php echo $companytxt;?>"
								data-modepayment="<?php echo $row['modepayment'];?>"
								data-company="<?php echo $row['company'];?>"
								data-dateadd="<?php echo ($row['dateadd']!=""?date("d/m/Y",$row['dateadd']):"");?>" 
								data-datedue="<?php echo ($row['datedue']!=""?date("d/m/Y",$row['datedue']):"");?>" 
								data-datepaid="<?php echo ($row['datepaid']!=""?date("d/m/Y",$row['datepaid']):"");?>" 
								data-nature="<?php echo $row['nature'];?>"
								data-rib="<?php echo $row['rib'];?>"
								data-rib2="<?php echo $row['rib2'];?>"
								data-price2="<?php echo $row['price2'];?>"
								data-currency="<?php echo $row['currency'];?>"
								data-invoiced="<?php echo $row['invoiced'];?>"
								data-imputation="<?php echo $row['imputation'];?>"
								data-description="<?php echo str_replace("\"","'",$row['note']);?>"
								data-supplierbankaccounts="<?php echo str_replace("\"","'",$supplierbankaccounts);?>"
								data-remis="<?php echo $row['remis']=="1"?true:false;?>"
								data-dateremis="<?php echo ($row['dateremis']!=""?date("d/m/Y",$row['dateremis']):"");?>" 
								data-nremise="<?php echo $row['nremise'];?>"
								data-paid="<?php echo $row['paid']=="1"?true:false;?>" data-title="<?php echo $row['doc']!="0"?"caisse":"expense";?>">Modifier</a>
								<?php
								}
								if($row['modepayment'] == "Espèce" AND $row['type'] == "Sortie"){
									$bankaccounts = "<option value=''>Choisissez un compte banquaire</option>";
									$back1 = $bdd->query("SELECT * FROM bankaccounts WHERE company='".$row['company']."'");
									while($row1 = $back1->fetch()){
										$bankaccounts .= "<option value='".$row1['id']."' ".($row1['id']==$row['rib']?'selected':'').">".$row1['bank']." | ".$row1['rib']."</option>";
									}
								?>
							<a href="printcaisse.php?id=<?php echo $row['id'];?>" target="_blank">Bon de caisse</a>
							<a href="javascript:;" class="lx-disposition lx-open-popup" data-title="disposition" 
								data-id="<?php echo $row['id'];?>"								
								data-companytxt="<?php echo $companytxt;?>"
								data-bankaccounts="<?php echo $bankaccounts;?>">Mise à disposition</a>
								<?php
								}
								if($row['modepayment'] == "Virement" AND $row['type'] == "Sortie" AND ($row['typedoc'] == "facturesupplier" OR $row['typedoc'] == "")){
								?>
							<a href="javascript:;" class="lx-order-virement lx-open-popup" data-title="ordervirement" data-id="<?php echo $row['id'];?>" data-currency="<?php echo $row['currency'];?>">Ordre de virement</a>
								<?php
								}
								if(preg_match("#Supprimer Trésorerie#",$_SESSION['easybm_roles'])){
										?>
							<a href="javascript:;" class="lx-delete lx-delete-caisse lx-open-popup" data-title="deleterecord" data-id="<?php echo $row['id'];?>">Supprimer</a>
										<?php
								}
								?>
							</div>
								<?php
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
					<tr class="lx-first-tr">
						<td><label><input type="checkbox" name="selectall" value="selectall" /><del class="checkmark"></del></label></td>
						<td>Date création <i class="fa fa-sort" data-sort="dateadd"></i></td>
						<td>Affecté à la société <i class="fa fa-sort" data-sort="company"></i></td>
						<td>Référence <i class="fa fa-sort" data-sort="code"></i></td>
						<td>Nature <i class="fa fa-sort" data-sort="type"></i></td>
						<td>Mode de paiement <i class="fa fa-sort" data-sort="modepayment"></i></td>
						<td>Montant <i class="fa fa-sort" data-sort="price"></i></td>
						<td>Libellé <i class="fa fa-sort" data-sort="title"></i></td>
						<?php
						if(preg_match("#Consulter Clients|Consulter Factures clients|Consulter Notes honoraires|Consulter Propositions honoraires|Consulter Devis|Consulter Factures proforma|Consulter Bons de livraison|Consulter Bons de sortie|Consulter Bons de retour|Consulter Factures avoir#",$_SESSION['easybm_roles'])){
						?>
						<td>Client <i class="fa fa-sort" data-sort="client"></i></td>
						<?php
						}
						if(preg_match("#Consulter Fournisseurs|Consulter Factures fournisseurs|Consulter Bons de commande|Consulter Bons de réception#",$_SESSION['easybm_roles'])){
						?>
						<td>Fournisseur <i class="fa fa-sort" data-sort="supplier"></i></td>
						<?php
						}
						?>
						<td>Note <i class="fa fa-sort" data-sort="note"></i></td>
						<td>Date d'écheance <i class="fa fa-sort" data-sort="datedue"></i></td>
						<td>Imputation comptable <i class="fa fa-sort" data-sort="imputation"></i></td>
						<td>Compte banquaire encaissement / décaissement <i class="fa fa-sort" data-sort="rib"></i></td>
						<td>Date valeur (Rapprochement): <i class="fa fa-sort" data-sort="datepaid"></i></td>
						<td>Remis <i class="fa fa-sort" data-sort="remis"></i></td>
						<td>Impayé <i class="fa fa-sort" data-sort="paid"></i></td>
						<td>À comptabiliser <i class="fa fa-sort" data-sort="invoiced"></i></td>
						<td>Utilisateur <i class="fa fa-sort" data-sort="worker"></i></td>
						<td>Attachements</td>
						<td><i class="fa fa-ellipsis-v"></i></td>
					</tr>
				</table>
				<div class="lx-caisse-total3">
					<div style="float:left;margin-right:60px;">
						<p>* Nb entrée: <b><?php echo $nbin;?></b></p>
						<p>* Total entrée: <b><?php echo number_format((float)$totalin,2,"."," ").' '.$settings['currency'];?></b></p>
						<p>* Total tva collecté: <b><?php echo number_format((float)$tvain,2,"."," ").' '.$settings['currency'];?></b></p>
						<p style="color:#ff7373;">* Total entrée impayé: <b><?php echo number_format((float)$totalinunpaid,2,"."," ").' '.$settings['currency'];?></b></p>
					</div>
					<div>
						<p>* Nb sortie: <b><?php echo $nbout;?></b></p>
						<p>* Total sortie: <b><?php echo number_format((float)$totalout,2,"."," ").' '.$settings['currency'];?></b></p>
						<p>* Total tva payée: <b><?php echo number_format((float)$tvaout,2,"."," ").' '.$settings['currency'];?></b></p>
						<p style="color:#ff7373;">* Total sortie impayé: <b><?php echo number_format((float)$totaloutunpaid,2,"."," ").' '.$settings['currency'];?></b></p>
					</div>
					<div class="lx-clear-fix"></div>
					<div class="lx-action-bulk">
						<?php
						if(preg_match("#Exporter Trésorerie#",$_SESSION['easybm_roles'])){
						?>
						<a href="downloadcaisse.php?req=<?php echo $req2;?>" class="lx-download-file"><i class="fa fa-download"></i> Téléchager PDF</a>
						<a href="javascript:;" class="lx-new lx-open-popup" data-title="export"><i class="fa fa-download"></i> Exporter Excel</a>
						<?php
						}
						?>					
					</div>
					<div class="lx-clear-fix"></div>
				</div>
				<input type="hidden" id="posts" value="<?php echo $back4->rowCount();?>" /><input type="hidden" id="req" value="<?php echo $req2;?>" />
				<?php
				if($back4->rowCount() > ($_POST['start'] + $_POST['nbpage'])){
				?>
				<p><?php echo ($_POST['start'] + $_POST['nbpage']);?> entrée(s) / sortie(s) de <?php echo $back4->rowCount();?></p>
				<?php
				}
				else{
				?>
				<p><?php echo $back4->rowCount();?> entrée(s) / sortie(s) de <?php echo $back4->rowCount();?></p>
				<?php			
				}
			}

			if($_POST['action'] == "loadkpi"){
				$req = "";
				$datestart = time() - (60*60*24*30);
				$dateend = time();
				if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
					$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
					$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24);
				}
				$req .= " AND (d.dateadd BETWEEN ".$datestart." AND ".$dateend.")";
				if($_POST['company'] != ""){
					$comp = explode("||",$_POST['company']);
					$req .= " AND (";
					for($j=0;$j<count($comp);$j++){
						if($j==0){
							$req .= "FIND_IN_SET(".$comp[$j].", d.company)";
						}
						else{
							$req .= " OR FIND_IN_SET(".$comp[$j].", d.company)";
						}
					}
					$req .= ")";
				}
				if($_POST['client'] != "" AND $_POST['supplier'] != ""){
					$req .= " AND (d.client IN(".str_replace("||",",",$_POST['client']).") OR d.supplier IN(".str_replace("||",",",$_POST['supplier'])."))";
				}
				else{
					if($_POST['client'] != ""){
						$req .= " AND d.client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['supplier'] != ""){
						$req .= " AND d.supplier IN(".str_replace("||",",",$_POST['supplier']).")";
					}
				}
				$req .= $multicompanies;
				$back = $bdd->query("SELECT SUM((CASE WHEN type='facture' OR type='nh' OR type='nf' THEN price-rastva WHEN type='avoir' THEN price*(-1) ELSE 0 END)) AS sm,
											SUM((CASE WHEN type='facture' OR type='nh' OR type='nf' THEN tva WHEN type='avoir' THEN tva*(-1) ELSE 0 END)) AS stva FROM documents d WHERE type IN('facture','nh','nf','avoir') AND trash='1'".$req);
				$ca = $back->fetch();
				$back = $bdd->query("SELECT SUM(price) AS sm FROM payments d WHERE type='Entrée' AND invoiced IN('Oui','') AND doc='0'".$req);
				$entree = $back->fetch();
				?>
				<div class="lx-g4 lx-pr-0 lx-pb-0 lx-plr-0-mob">
					<div class="lx-state-count" style="background:<?php echo ($ca['sm']+$entree['sm'])>0?"#5cb85c":(($ca['sm']+$entree['sm'])<0?"#CC0000":"#BEBEBE");?>;">
						<a href="javascript:;">
							<i class="fa fa-hand-holding-usd"></i>
							<br />
							<span style="color:#FFFFFF;"><strong><?php echo number_format((float)($ca['sm']+$entree['sm']),2,"."," ")." ".$settings['currency'];?></strong><br /><del class="infobul" data-title="Chiffre d'affaires = (valeur factures + encaissements manuels) - valeur avoirs">Chiffre d'affaires TTC</del></span>
							<div class="lx-clear-fix"></div>
							<?php
							$back = $bdd->query("SELECT SUM(price) AS sm FROM payments d WHERE type='Entrée' AND invoiced='Non' AND doc='0'".$req);
							$entree = $back->fetch();
							?>
							<p><b><?php echo number_format((float)($entree['sm']),2,"."," ")." ".$settings['currency'];?></b> Non comptabilisé</p>
						</a>
					</div>
				</div>
				<?php
				$back = $bdd->query("SELECT SUM(price) AS sm FROM payments d WHERE type='Entrée' AND paid='0' AND invoiced IN('Oui','') AND trash='1'".$req);
				$entree = $back->fetch();
				?>
				<div class="lx-g4 lx-pb-0 lx-plr-0-mob">
					<div class="lx-state-count" style="background:#a2d695;">
						<a href="javascript:;">
							<i class="fas fa-money-bill-wave"></i>
							<br />
							<span style="color:#FFFFFF;"><strong><?php echo number_format((float)$entree['sm'],2,"."," ")." ".$settings['currency'];?></strong><br />Encaissements TTC</span>
							<div class="lx-clear-fix"></div>
							<?php
							$back = $bdd->query("SELECT SUM(price) AS sm FROM payments d WHERE type='Entrée' AND paid='0' AND invoiced='Non' AND doc='0'".$req);
							$entree = $back->fetch();
							?>
							<p><b><?php echo number_format((float)($entree['sm']),2,"."," ")." ".$settings['currency'];?></b> Non comptabilisé</p>
						</a>
					</div>
				</div>
				<?php
				$back = $bdd->query("SELECT SUM(price) AS sm FROM payments d WHERE type='Sortie' AND paid='0' AND invoiced IN('Oui','') AND trash='1'".$req);
				$sortie = $back->fetch();
				?>
				<div class="lx-g4 lx-pl-0 lx-pb-0 lx-plr-0-mob">
					<div class="lx-state-count" style="background:#ff7373;">
						<a href="javascript:;">
							<i class="fas fa-money-bill-wave"></i>
							<br />
							<span style="color:#FFFFFF;"><strong><?php echo number_format((float)$sortie['sm'],2,"."," ")." ".$settings['currency'];?></strong><br />Dépenses TTC</span>
							<div class="lx-clear-fix"></div>
							<?php
							$back = $bdd->query("SELECT SUM(price) AS sm FROM payments d WHERE type='Sortie' AND paid='0' AND invoiced='Non' AND doc='0'".$req);
							$sortie = $back->fetch();
							?>
							<p><b><?php echo number_format((float)($sortie['sm']),2,"."," ")." ".$settings['currency'];?></b> Non comptabilisé</p>
						</a>
					</div>
				</div>
				<?php
				$back = $bdd->query("SELECT SUM(ROUND((price - (price / (1 + (tva / 100)))),2)) AS sm FROM payments d WHERE type='Entrée'".$req);
				$tvaentree = $back->fetch();
				$back = $bdd->query("SELECT SUM(ROUND((price - (price / (1 + (tva / 100)))),2)) AS sm FROM payments d WHERE type='Sortie'".$req);
				$tvasortie = $back->fetch();
				$back = $bdd->query("SELECT SUM(tva) AS stva FROM facturesuppliers d WHERE trash='1'".$req);
				$tva = $back->fetch();
				$back = $bdd->query("SELECT SUM(rastva) AS rastva FROM documents d WHERE type IN('facture','nh','nf') AND trash='1'".$req);
				$rastvaclient = $back->fetch();
				$back = $bdd->query("SELECT SUM(rastva) AS rastva FROM facturesuppliers d WHERE trash='1'".$req);
				$rastvasupplier = $back->fetch();
				?>
				<div class="lx-g4 lx-pl-0 lx-pb-0 lx-plr-0-mob">
					<div class="lx-state-count" style="padding:2px 10px 1px;text-align:left;background:#90b5c1;">
						<a href="javascript:;">
							<i class="fa fa-file-invoice"></i>
							<span style="margin-bottom:15px;color:#FFFFFF;text-align:right;"><strong style="font-size:14px;"><?php echo number_format((float)$ca['stva']+$tvaentree['sm'],2,"."," ")." ".$settings['currency']." / ".number_format((float)$tva['stva']+$tvasortie['sm'],2,"."," ")." ".$settings['currency'];?></strong><br />TVA Collectée / Payée</span>
							<span style="color:#FFFFFF;text-align:right;">TVA RAS par clients:&nbsp;<strong style="font-size:14px;"><?php echo number_format((float)$rastvaclient['rastva'],2,"."," ")." ".$settings['currency'];?></strong></span>
							<span style="color:#FFFFFF;text-align:right;">TVA RAS sur founisseurs:&nbsp;<strong style="font-size:14px;"><?php echo number_format((float)$rastvasupplier['rastva'],2,"."," ")." ".$settings['currency'];?></strong></span>
							<div class="lx-clear-fix"></div>
						</a>
					</div>
				</div>
				<div class="lx-clear-fix"></div>
				<?php
			}

			if($_POST['action'] == "loaddocuments"){
				$req = "";
				$datestart = time() - (60*60*24*30);
				$dateend = time();
				if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
					$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
					$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24);
				}
				$req .= " AND (d.dateadd BETWEEN ".$datestart." AND ".$dateend.")";
				if($_POST['company'] != ""){
					$comp = explode("||",$_POST['company']);
					$req .= " AND (";
					for($j=0;$j<count($comp);$j++){
						if($j==0){
							$req .= "FIND_IN_SET(".$comp[$j].", d.company)";
						}
						else{
							$req .= " OR FIND_IN_SET(".$comp[$j].", d.company)";
						}
					}
					$req .= ")";
				}
				if($_POST['client'] != "" AND $_POST['supplier'] != ""){
					$req .= " AND (d.client IN(".str_replace("||",",",$_POST['client']).") OR d.supplier IN(".str_replace("||",",",$_POST['supplier'])."))";
				}
				else{
					if($_POST['client'] != ""){
						$req .= " AND d.client IN(".str_replace("||",",",$_POST['client']).")";
					}
					if($_POST['supplier'] != ""){
						$req .= " AND d.supplier IN(".str_replace("||",",",$_POST['supplier']).")";
					}
				}
				$req .= $multicompanies;
				if(preg_match("#Consulter Factures clients#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price-rastva) AS sm FROM documents d WHERE type='facture' AND trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Factures clients</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				if(preg_match("#Consulter Factures fournisseurs#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price+(tva-rastva)) AS sm FROM facturesuppliers d WHERE trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Factures fournisseurs</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				if(preg_match("#Consulter Notes honoraires#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price-rastva) AS sm FROM documents d WHERE type='nh' AND trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Notes d'honoraires</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				if(preg_match("#Consulter Notes de frais#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price-rastva) AS sm FROM documents d WHERE type='nf' AND trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Notes de frais</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				if(preg_match("#Consulter Devis,#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price) AS sm FROM documents d WHERE type='devis' AND trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Devis</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				if(preg_match("#Consulter Propositions honoraires#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price) AS sm FROM documents d WHERE type='ph' AND trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Propositions d'honoraires</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				if(preg_match("#Consulter Factures avoir#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price) AS sm FROM documents d WHERE type='avoir' AND trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Factures avoir</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				if(preg_match("#Consulter Bons de retour#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price) AS sm FROM documents d WHERE type='br' AND trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Bons de retour</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				if(preg_match("#Consulter Factures proforma#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price) AS sm FROM documents d WHERE type='factureproforma' AND trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Factures proforma</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				if(preg_match("#Consulter Bons de livraison#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price) AS sm FROM documents d WHERE type='bl' AND trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Bons de livraison</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				if(preg_match("#Consulter Bons de sortie#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price) AS sm FROM documents d WHERE type='bs' AND trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Bons de sortie</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				if(preg_match("#Consulter Bons de commande#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price) AS sm FROM documents d WHERE type='bc' AND trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Bons de commande</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				if(preg_match("#Consulter Bons de réception#",$_SESSION['easybm_roles'])){
					$back = $bdd->query("SELECT COUNT(id) AS nb,SUM(price) AS sm FROM documents d WHERE type='bre' AND trash='1'".$req);
					$row = $back->fetch();
					?>
				<a href="javascript:;">
					<strong>Bons de réception</strong>
					<span>Nb: <b><?php echo $row['nb'];?></b></span>&nbsp;&nbsp; | &nbsp;&nbsp;<span>Valeur: <b><?php echo number_format((float)$row['sm'],2,"."," ");?> <?php echo $settings['currency'];?></b></span> 
				</a>
					<?php
				}
				?>
				<div class="lx-clear-fix"></div>
				<?php
			}
			
			if($_POST['action'] == "loadca"){
				$multicompanies = str_replace("company","d.company",$multicompanies);
				$datestart = time() - (60*60*24*30);
				$dateend = time();
				if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
					$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
					$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24);
				}
				$dates = "";
				$nbdocs = "";
				$value = "";
				$limit = ($dateend-$datestart)/$_POST['period'];
				for($i=0;$i<=ceil($limit);$i++){
					$dates .= ",".date("d/m/Y",($datestart+($i*$_POST['period'])));
					if($_POST['typedoc'] == "facturesupplier"){
						$req = "SELECT COUNT(id) AS nb,SUM(price + (tva - rastva)) AS sm FROM facturesuppliers d WHERE trash='1'";
					}
					else{
						$req = "SELECT COUNT(DISTINCT d.id) AS nb,SUM(d.price - rastva) AS sm FROM documents d WHERE trash='1'";
					}
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", d.company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", d.company)";
							}
						}
						$req .= ")";
					}
					if($_POST['typedoc'] != ""){
						if($_POST['typedoc'] != "facturesupplier"){
							$req .= " AND d.type IN('".str_replace("||","','",$_POST['typedoc'])."')";
						}
					}
					if($_POST['client'] != "" AND $_POST['supplier'] != ""){
						$req .= " AND (d.client IN(".str_replace("||",",",$_POST['client']).") OR d.supplier IN(".str_replace("||",",",$_POST['supplier'])."))";
					}
					else{
						if($_POST['client'] != ""){
							$req .= " AND d.client IN(".str_replace("||",",",$_POST['client']).")";
						}
						if($_POST['supplier'] != ""){
							$req .= " AND d.supplier IN(".str_replace("||",",",$_POST['supplier']).")";
						}
					}
					if($_POST['product'] != ""){
						if($_POST['typedoc'] != "facturesupplier"){
							$comp = explode("||",$_POST['product']);
							$req .= " AND (";
							for($j=0;$j<count($comp);$j++){
								if($j==0){
									$req .= "title LIKE '%".addslashes($comp[$j])."%'";
								}
								else{
									$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
								}
							}
							$req .= ")";
						}
					}
					if($_POST['tags'] != ""){
						$comp = explode("||",$_POST['tags']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET('".$comp[$j]."', labels)";
							}
							else{
								$req .= " OR FIND_IN_SET('".$comp[$j]."', labels)";
							}
						}
						$req .= ")";
					}										
					$req .= " AND (d.dateadd BETWEEN '".($datestart+($i*$_POST['period']))."' AND '".($datestart+(($i+1)*$_POST['period']))."')";
					$req .= $multicompanies;
					$back = $bdd->query($req);
					$row = $back->fetch();
					$nbdocs .= ",".$row['nb'];
					$value .= ",".number_format((float)($row['sm']!=""?$row['sm']:0),2,".","");
				}
				$dates = substr($dates,1);
				$nbdocs = substr($nbdocs,1);
				$value = substr($value,1);
				echo $dates."|".$nbdocs."|".$value;
			}
			
			if($_POST['action'] == "loadtop"){
				$multicompanies = str_replace("company","d.company",$multicompanies);
				$datestart = time() - (60*60*24*30);
				$dateend = time();
				if($_POST['datestart'] != "" AND $_POST['dateend'] != ""){
					$datestart = strtotime(str_replace("/","-",$_POST['datestart']));
					$dateend = strtotime(str_replace("/","-",$_POST['dateend'])) + (60*60*24);
				}
				$topname = "";
				$topvalue = "";
				$topvalue2 = "";
					$gb = "d.client";
					if($_POST['topwho'] == "products"){
						$gb = "d.title";
					}
					elseif($_POST['topwho'] == "suppliers"){
						$gb = "d.supplier";
					}
					$req = "";
					if($_POST['company'] != ""){
						$comp = explode("||",$_POST['company']);
						$req .= " AND (";
						for($j=0;$j<count($comp);$j++){
							if($j==0){
								$req .= "FIND_IN_SET(".$comp[$j].", d.company)";
							}
							else{
								$req .= " OR FIND_IN_SET(".$comp[$j].", d.company)";
							}
						}
						$req .= ")";
					}
					if($_POST['typedoc'] != ""){
						if($_POST['topwho'] == "clients"){
							$req .= " AND d.type IN('".str_replace("||","','",$_POST['typedoc'])."')";
						}
						elseif($_POST['topwho'] == "products"){
							$req .= " AND d.typedoc IN('".str_replace("||","','",$_POST['typedoc'])."')";
						}
					}
					if($_POST['client'] != "" AND $_POST['supplier'] != ""){
						$req .= " AND (d.client IN(".str_replace("||",",",$_POST['client']).") OR d.supplier IN(".str_replace("||",",",$_POST['supplier'])."))";
					}
					else{
						if($_POST['client'] != ""){
							$req .= " AND d.client IN(".str_replace("||",",",$_POST['client']).")";
						}
						if($_POST['supplier'] != ""){
							$req .= " AND d.supplier IN(".str_replace("||",",",$_POST['supplier']).")";
						}
					}
					if($_POST['product'] != ""){
						if($_POST['topwho'] == "clients"){
							$comp = explode("||",$_POST['product']);
							$req .= " AND (";
							for($j=0;$j<count($comp);$j++){
								if($j==0){
									$req .= "title LIKE '%".addslashes($comp[$j])."%'";
								}
								else{
									$req .= " OR title LIKE '%".addslashes($comp[$j])."%'";
								}
							}
							$req .= ")";
						}
						if($_POST['topwho'] == "products"){
							$req .= " AND title IN('".str_replace("||","','",addslashes($_POST['product']))."')";
						}
					}
					$req .= $multicompanies;
					if($_POST['topwho'] == "clients"){
						$req .= " AND d.client<>'0'";
					}
					elseif($_POST['topwho'] == "suppliers"){
						$req .= " AND d.supplier<>'0'";
					}
					if($_POST['topwho'] == "clients"){
						$req = "SELECT SUM(d.price - rastva) AS ca,COUNT(DISTINCT d.id) as nb,".$gb." FROM documents d WHERE (d.dateadd BETWEEN ".$datestart." AND ".$dateend.")".$req." GROUP BY ".$gb." ORDER BY ca DESC LIMIT 0,10";
					}
					elseif($_POST['topwho'] == "products"){
						$req = "SELECT SUM(ROUND((d.tprice * (1 + (tva / 100))),2)) AS ca,COUNT(DISTINCT d.id) as nb,".$gb." FROM detailsdocuments d WHERE (d.dateadd BETWEEN ".$datestart." AND ".$dateend.")".$req." GROUP BY ".$gb." ORDER BY ca DESC LIMIT 0,10";
					}
					else{
						$req = "SELECT SUM(d.price + (tva - rastva)) AS ca,COUNT(DISTINCT d.id) as nb,".$gb." FROM facturesuppliers d WHERE (d.dateadd BETWEEN ".$datestart." AND ".$dateend.")".$req." GROUP BY ".$gb." ORDER BY ca DESC LIMIT 0,10";
					}
					$back = $bdd->query($req);
					while($row = $back->fetch()){
						$column = "fullname";
						if($_POST['topwho'] != "clients"){
							$column = "title";
						}
						$where = "";
						if($_POST['topwho'] == "clients"){
							$where = " AND id='".$row['client']."'";
						}
						if($_POST['topwho'] == "suppliers"){
							$where = " AND id='".$row['supplier']."'";
						}
						if($_POST['topwho'] == "products"){
							$_POST['topwho'] = "detailsdocuments";
						}
						$back1 = $bdd->query("SELECT ".$column." AS title FROM ".$_POST['topwho']." WHERE 1=1".$where);
						$row1 = $back1->fetch();
						$topname .= ",".($_POST['topwho']=="detailsdocuments"?$row['title']:$row1['title']);
						$topvalue .= ",".number_format((float)($row['nb']),2,".","");
						$topvalue2 .= ",".number_format((float)($row['ca']),2,".","");
					}

				$topname = substr($topname,1);
				$topvalue = substr($topvalue,1);
				$topvalue2 = substr($topvalue2,1);
				echo $topname."|".$topvalue."|".$topvalue2;
			}
			
			if($_POST['action'] == "changestate"){
				if($_POST['table'] == "projects"){
					if($_POST['state'] == "off"){
						$req = $bdd->prepare("UPDATE projects SET dateend='' WHERE id='".$_POST['id']."'");
						$req->execute();
					}
				}
				$req = $bdd->prepare("UPDATE `".$_POST['table']."` SET `".$_POST['column']."`='".$_POST['state']."' WHERE id='".$_POST['id']."'");
				$req->execute();
			}

			if($_POST['action'] == "updatebulk"){
				if($_POST['state'] == "delete"){
					$req = $bdd->prepare("UPDATE `".$_POST['table']."` SET trash='0' WHERE `".$_POST['column']."` IN(".$_POST['ids'].")");
					$req->execute();
				}
				elseif($_POST['state'] == "deletepermenantly"){
					$req = $bdd->prepare("DELETE FROM `".$_POST['table']."` WHERE `".$_POST['column']."` IN(".$_POST['ids'].")");
					$req->execute();
				}
				elseif($_POST['state'] == "restore"){
					$req = $bdd->prepare("UPDATE `".$_POST['table']."` SET trash='1' WHERE `".$_POST['column']."` IN(".$_POST['ids'].")");
					$req->execute();
				}
				else{
					$backcommand = $bdd->query("SELECT id,code FROM commands WHERE id IN(".$_POST['ids'].")");
					while($command = $backcommand->fetch()){
						$req = $bdd->prepare("UPDATE commands SET state='".sanitize_vars($_POST['state'])."',dateupdate='".time()."' WHERE id='".$command['id']."'");
						$req->execute();
						$req = $bdd->prepare("INSERT INTO commandshistory(id,command,state,agent,dateadd) VALUES ('0','".$command['code']."','".sanitize_vars($_POST['state'])."','".$_SESSION['easybm_fullname']."','".time()."')");
						$req->execute();
					}
				}
			}
			
			if($_POST['action'] == "advancedsearch"){
				if($_POST['keyword'] != ""){
					if($_POST['table'] != "products"){
						$back = $bdd->query("SELECT * FROM ".$_POST['table']." WHERE ".$_POST['value']." LIKE '%".addslashes($_POST['keyword'])."%' LIMIT 0,25");
						while($row = $back->fetch()){
							if($_POST['table'] == "clients"){
								$text = $row['fullname']." (".$row['code'].")";
							}
							elseif($_POST['table'] == "suppliers"){
								$text = $row['title']." (".$row['code'].")";
							}
							else{
								$text = $row[$_POST['text']];
							}
							?>
				<li><label><input type="checkbox" value="<?php echo $text;?>" data-ids="<?php echo $row[$_POST['ids']];?>" /> <?php echo $text;?><del class="checkmark"></del></label></li>
							<?php
						}
					}
					else{
						$rowsproducts = [];
						$back = $bdd->query("SELECT title FROM documents WHERE title<>'' AND REGEXP_REPLACE(title, '<[^>]+>', '') LIKE '%".addslashes($_POST['keyword'])."%' AND trash='1'".$multicompanies." ORDER BY title LIMIT 0,25");
						while($row = $back->fetch()){
							$products = explode("||",$row['title']);
							for($i=0;$i<count($products);$i++){
								$rowsproducts[] = $products[$i];
							}
						}
						$rowsproducts = array_unique($rowsproducts);
						sort($rowsproducts); 
						for($i=0;$i<count($rowsproducts);$i++){
							?>
				<li><label><input type="checkbox" value="<?php echo trim(strip_tags($rowsproducts[$i]));?>" data-ids='<?php echo htmlspecialchars($rowsproducts[$i]);?>' /> <?php echo $rowsproducts[$i];?><del class="checkmark"></del></label></li>
							<?php
						}							
					}
				}
			}
			
			if($_POST['action'] == "loadnotification"){
					$nbnotif = 0;
					
					$types = "facture,nh,nf,devis,ph,avoir,br,factureproforma,bl,bs,bc,bre";
					$types = explode(",",$types);
					$access = "Factures clients,Notes honoraires,Notes de frais,Devis,Propositions honoraires,Factures avoir,Bons de retour,Factures proforma,Bons de livraison,Bons de sortie,Bons de commande,Bons de réception";
					$access = explode(",",$access);
					$pages = "factures,nh,nf,devis,ph,avoirs,br,facturesproforma,bl,bs,bc,bre";
					$pages = explode(",",$pages);	
					$titles = "facture(s) client(s),Note(s) d'honoraire(s),Note(s) de frai(s),Devis,Proposition(s) d'honoraire(s),Facture(s) avoir,Bon(s) de retour,Facture(s) proforma,Bon(s) de livraison,Bon(s) de sortie,Bon(s) de commande,Bon(s) de réception";
					$titles = explode(",",$titles);
					
					for($i=0;$i<count($types);$i++){
						if(preg_match("#Consulter ".$access[$i]."#",$_SESSION['easybm_roles'])){
							$back = $bdd->query("SELECT id FROM documents WHERE type='".$types[$i]."' AND (recalldate < ".(strtotime(date("d-m-Y"))+(60*60*24))." AND recalldate<>'')".$multicompanies);
							if($back->rowCount() > 0){
								?>
					<p style="background:#ffc966;"><a href="<?php echo $pages[$i];?>.php?datestartrecall=<?php echo date("d/m/Y",(time()-(60*60*24*180)));?>&dateendrecall=<?php echo date("d/m/Y");?>"><b><?php echo $back->rowCount();?></b> Rappel(s) <?php echo $titles[$i];?></ins></a></p>
								<?php
								$nbnotif++;
							}
						}
					}

					if(preg_match("#Consulter Factures fournisseurs#",$_SESSION['easybm_roles'])){
						$back = $bdd->query("SELECT id FROM facturesuppliers WHERE (recalldate < ".(strtotime(date("d-m-Y"))+(60*60*24))." AND recalldate<>'')".$multicompanies);
						if($back->rowCount() > 0){
							?>
					<p style="background:#ffc966;"><a href="facturesuppliers.php?datestartrecall=<?php echo date("d/m/Y",(time()-(60*60*24*180)));?>&dateendrecall=<?php echo date("d/m/Y");?>"><b><?php echo $back->rowCount();?></b> Rappel(s) facture(s) fournisseur(s)</ins></a></p>
							<?php
							$nbnotif++;
						}
					}
					
					if(preg_match("#Consulter Clients#",$_SESSION['easybm_roles'])){
						$back = $bdd->query("SELECT id FROM clients WHERE (recalldate < ".(strtotime(date("d-m-Y"))+(60*60*24))." AND recalldate<>'')".$multicompanies);
						if($back->rowCount() > 0){
							?>
					<p style="background:#ffc966;"><a href="clients.php?datestartrecall=<?php echo date("d/m/Y",(time()-(60*60*24*180)));?>&dateendrecall=<?php echo date("d/m/Y");?>"><b><?php echo $back->rowCount();?></b> Rappel(s) client(s)</ins></a></p>
							<?php
							$nbnotif++;
						}
					}
					
					if(preg_match("#Consulter Fournisseurs#",$_SESSION['easybm_roles'])){
						$back = $bdd->query("SELECT id FROM suppliers WHERE (recalldate < ".(strtotime(date("d-m-Y"))+(60*60*24))." AND recalldate<>'')".$multicompanies);
						if($back->rowCount() > 0){
							?>
					<p style="background:#ffc966;"><a href="suppliers.php?datestartrecall=<?php echo date("d/m/Y",(time()-(60*60*24*180)));?>&dateendrecall=<?php echo date("d/m/Y");?>"><b><?php echo $back->rowCount();?></b> Rappel(s) fournisseur(s)</ins></a></p>
							<?php
							$nbnotif++;
						}
					}
					
					if(preg_match("#Consulter Prospects#",$_SESSION['easybm_roles'])){
						if(preg_match("#Consulter seulement ses prospects#",$_SESSION['easybm_roles'])){
							$multicompanies .= " AND user='".$_SESSION['easybm_id']."'";
						}
						$back = $bdd->query("SELECT id FROM prospects WHERE (recalldate < ".(strtotime(date("d-m-Y"))+(60*60*24))." AND recalldate<>'')".$multicompanies);
						if($back->rowCount() > 0){
							?>
					<p style="background:#ffc966;"><a href="prospects.php?datestartrecall=<?php echo date("d/m/Y",(time()-(60*60*24*180)));?>&dateendrecall=<?php echo date("d/m/Y");?>"><b><?php echo $back->rowCount();?></b> Rappel(s) prospect(s)</ins></a></p>
							<?php
							$nbnotif++;
						}
					}
					
					if(preg_match("#Factures clients#",$_SESSION['easybm_roles'])){
						$value1 = intval(explode("-",$notifications['fafter'])[1])*24*60*60;
						$checked = explode("-",$notifications['fafter'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='facture' AND state NOT IN('Payée') AND trash='1'".$companies." AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM documents WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();	
							?>
					<p style="background:#ffe5e5;"><a href="factures.php?paid=Non payée avec dépassement de délai de paiement"><b><?php echo $back->rowCount();?></b> Factures clients non payées avec un dépassement de délai de paiement</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
						$value = intval(explode("-",$notifications['fbefore'])[1])*24*60*60;
						$checked = explode("-",$notifications['fbefore'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='facture' AND state NOT IN('Payée') AND trash='1'".$companies." AND (".time()." BETWEEN ((dateadd+(delay*60*60*24))-". $value.") AND (dateadd+(delay*60*60*24)+".secondsToMidnight()."))");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM documents WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();
							?>
					<p style="background:#fff6e5;"><a href="factures.php?paid=Non payée||Partiellement payée&datelimitstart=<?php echo date("d/m/Y",time());?>&datelimitend=<?php echo date("d/m/Y",(time() + $value));?>"><b><?php echo $back->rowCount();?></b> Factures clients non payées avec délai de paiement restant de <ins>(≤ <?php echo explode("-",$notifications['fbefore'])[1];?> Jours)</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
						
						$value1 = intval(explode("-",$notifications['rasclientafter'])[1])*24*60*60;
						$checked = explode("-",$notifications['rasclientafter'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='facture' AND stateras IN('Non payée') AND rastva > 0 AND trash='1'".$companies." AND ".time()." > datedue");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM documents WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();	
							?>
					<p style="background:#ffe5e5;"><a href="factures.php?stateras=Non payée avec dépassement de délai de paiement"><b><?php echo $back->rowCount();?></b> RAS TVA factures clients non payées avec un dépassement de délai de paiement</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
						$value = intval(explode("-",$notifications['rasclientbefore'])[1])*24*60*60;
						$checked = explode("-",$notifications['rasclientbefore'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='facture' AND stateras IN('Non payée') AND rastva > 0 AND trash='1'".$companies." AND (datedue BETWEEN ".time()." AND ".(time()+$value).")");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM documents WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();
							?>
					<p style="background:#fff6e5;"><a href="factures.php?stateras=Non payée&datelimitrasstart=<?php echo date("d/m/Y",time());?>&datelimitrasend=<?php echo date("d/m/Y",(time() + $value));?>"><b><?php echo $back->rowCount();?></b> RAS TVA factures clients non payées avec délai de paiement restant de <ins>(≤ <?php echo explode("-",$notifications['rasclientbefore'])[1];?> Jours)</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
					}
					
					if(preg_match("#Notes honoraires#",$_SESSION['easybm_roles'])){
						$value1 = intval(explode("-",$notifications['fafter'])[1])*24*60*60;
						$checked = explode("-",$notifications['fafter'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='nh' AND state NOT IN('Payée') AND trash='1'".$companies." AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM documents WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();	
							?>
					<p style="background:#ffe5e5;"><a href="nh.php?paid=Non payée avec dépassement de délai de paiement"><b><?php echo $back->rowCount();?></b> Notes d'honoraires non payées avec un dépassement de délai de paiement</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
						$value = intval(explode("-",$notifications['fbefore'])[1])*24*60*60;
						$checked = explode("-",$notifications['fbefore'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='nh' AND state NOT IN('Payée') AND trash='1'".$companies." AND (".time()." BETWEEN ((dateadd+(delay*60*60*24))-". $value.") AND (dateadd+(delay*60*60*24)+".secondsToMidnight()."))");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM documents WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();
							?>
					<p style="background:#fff6e5;"><a href="nh.php?paid=Non payée||Partiellement payée&datelimitstart=<?php echo date("d/m/Y",time());?>&datelimitend=<?php echo date("d/m/Y",(time() + $value));?>"><b><?php echo $back->rowCount();?></b> Notes d'honoraires non payées avec délai de paiement restant de <ins>(≤ <?php echo explode("-",$notifications['fbefore'])[1];?> Jours)</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
						
						$value1 = intval(explode("-",$notifications['rasclientafter'])[1])*24*60*60;
						$checked = explode("-",$notifications['rasclientafter'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='nh' AND stateras IN('Non payée') AND rastva > 0 AND trash='1'".$companies." AND ".time()." > datedue");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM documents WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();	
							?>
					<p style="background:#ffe5e5;"><a href="nh.php?stateras=Non payée avec dépassement de délai de paiement"><b><?php echo $back->rowCount();?></b> RAS TVA Notes d'honoraires non payées avec un dépassement de délai de paiement</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
						$value = intval(explode("-",$notifications['rasclientbefore'])[1])*24*60*60;
						$checked = explode("-",$notifications['rasclientbefore'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='nh' AND stateras IN('Non payée') AND rastva > 0 AND trash='1'".$companies." AND (datedue BETWEEN ".time()." AND ".(time()+$value).")");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM documents WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();
							?>
					<p style="background:#fff6e5;"><a href="nh.php?stateras=Non payée&datelimitrasstart=<?php echo date("d/m/Y",time());?>&datelimitrasend=<?php echo date("d/m/Y",(time() + $value));?>"><b><?php echo $back->rowCount();?></b> RAS TVA Notes d'honoraires non payées avec délai de paiement restant de <ins>(≤ <?php echo explode("-",$notifications['rasclientbefore'])[1];?> Jours)</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
					}
					
					if(preg_match("#Notes de frais#",$_SESSION['easybm_roles'])){
						$value1 = intval(explode("-",$notifications['fafter'])[1])*24*60*60;
						$checked = explode("-",$notifications['fafter'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='nf' AND state NOT IN('Payée') AND trash='1'".$companies." AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM documents WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();	
							?>
					<p style="background:#ffe5e5;"><a href="nf.php?paid=Non payée avec dépassement de délai de paiement"><b><?php echo $back->rowCount();?></b> Notes de frais non payées avec un dépassement de délai de paiement</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
						$value = intval(explode("-",$notifications['fbefore'])[1])*24*60*60;
						$checked = explode("-",$notifications['fbefore'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='nf' AND state NOT IN('Payée') AND trash='1'".$companies." AND (".time()." BETWEEN ((dateadd+(delay*60*60*24))-". $value.") AND (dateadd+(delay*60*60*24)+".secondsToMidnight()."))");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM documents WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();
							?>
					<p style="background:#fff6e5;"><a href="nf.php?paid=Non payée||Partiellement payée&datelimitstart=<?php echo date("d/m/Y",time());?>&datelimitend=<?php echo date("d/m/Y",(time() + $value));?>"><b><?php echo $back->rowCount();?></b> Notes de frais non payées avec délai de paiement restant de <ins>(≤ <?php echo explode("-",$notifications['fbefore'])[1];?> Jours)</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
						
						$value1 = intval(explode("-",$notifications['rasclientafter'])[1])*24*60*60;
						$checked = explode("-",$notifications['rasclientafter'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='nf' AND stateras IN('Non payée') AND rastva > 0 AND trash='1'".$companies." AND ".time()." > datedue");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM documents WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();	
							?>
					<p style="background:#ffe5e5;"><a href="nf.php?stateras=Non payée avec dépassement de délai de paiement"><b><?php echo $back->rowCount();?></b> RAS TVA Notes de frais non payées avec un dépassement de délai de paiement</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
						$value = intval(explode("-",$notifications['rasclientbefore'])[1])*24*60*60;
						$checked = explode("-",$notifications['rasclientbefore'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='nf' AND stateras IN('Non payée') AND rastva > 0 AND trash='1'".$companies." AND (datedue BETWEEN ".time()." AND ".(time()+$value).")");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM documents WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();
							?>
					<p style="background:#fff6e5;"><a href="nf.php?stateras=Non payée&datelimitrasstart=<?php echo date("d/m/Y",time());?>&datelimitrasend=<?php echo date("d/m/Y",(time() + $value));?>"><b><?php echo $back->rowCount();?></b> RAS TVA Notes de frais non payées avec délai de paiement restant de <ins>(≤ <?php echo explode("-",$notifications['rasclientbefore'])[1];?> Jours)</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
					}

					if(preg_match("#Consulter Factures avoir#",$_SESSION['easybm_roles'])){
						$value1 = intval(explode("-",$notifications['avoir'])[1])*24*60*60;
						$checked = explode("-",$notifications['avoir'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM documents WHERE type='avoir' AND state IN('Non remboursée','Partiellement remboursée') AND trash='1'".$multicompanies." AND dateadd < ".(time() - $value1));
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd FROM documents WHERE type='avoir' AND dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();
							?>
					<p style="background:#ffe5e5;"><a href="avoirs.php?paid=Non remboursée||Partiellement remboursée&datestart=<?php echo date("d/m/Y",$row1['dateadd']);?>&dateend=<?php echo date("d/m/Y",(time() - $value1));?>"><b><?php echo $back->rowCount();?></b> Avoirs non remboursés avec un dépassement de <ins>(≥ <?php echo explode("-",$notifications['avoir'])[1];?> J passés)</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
					}

					if(preg_match("#Factures fournisseurs#",$_SESSION['easybm_roles'])){
						$value1 = intval(explode("-",$notifications['fsafter'])[1])*24*60*60;
						$checked = explode("-",$notifications['fsafter'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM facturesuppliers WHERE state NOT IN('Payée') AND trash='1'".$companies." AND ".time()." > (dateadd+(delay*60*60*24)+".secondsToMidnight().")");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM facturesuppliers WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();	
								?>
					<p style="background:#ffe5e5;"><a href="facturesuppliers.php?paid=Non payée avec dépassement de délai de paiement"><b><?php echo $back->rowCount();?></b> Factures fournisseurs non payées avec un dépassement de délai de paiement</ins></a></p>
								<?php
							$nbnotif++;
							}
						}
						$value = intval(explode("-",$notifications['fsbefore'])[1])*24*60*60;
						$checked = explode("-",$notifications['fsbefore'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM facturesuppliers WHERE state NOT IN('Payée') AND trash='1'".$companies." AND (".time()." BETWEEN ((dateadd+(delay*60*60*24))-". $value.") AND (dateadd+(delay*60*60*24)+".secondsToMidnight()."))");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM facturesuppliers WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();
								?>
					<p style="background:#fff6e5;"><a href="facturesuppliers.php?paid=Non payée||Partiellement payée&datelimitstart=<?php echo date("d/m/Y",time());?>&datelimitend=<?php echo date("d/m/Y",(time() + $value));?>"><b><?php echo $back->rowCount();?></b> Factures fournisseurs non payées avec délai de paiement restant de <ins>(≤ <?php echo explode("-",$notifications['fsbefore'])[1];?> Jours)</ins></a></p>
								<?php
							$nbnotif++;
							}
						}
						
						$value1 = intval(explode("-",$notifications['rassupplierafter'])[1])*24*60*60;
						$checked = explode("-",$notifications['rassupplierafter'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM facturesuppliers WHERE stateras IN('Non payée') AND rastva > 0 AND trash='1'".$companies." AND ".time()." > datedue");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM facturesuppliers WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();	
								?>
					<p style="background:#ffe5e5;"><a href="facturesuppliers.php?stateras=Non payée avec dépassement de délai de paiement"><b><?php echo $back->rowCount();?></b> RAS TVA fournisseurs non payées avec un dépassement de délai de paiement</ins></a></p>
								<?php
							$nbnotif++;
							}
						}
						$value = intval(explode("-",$notifications['rassupplierbefore'])[1])*24*60*60;
						$checked = explode("-",$notifications['rassupplierbefore'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM facturesuppliers WHERE stateras IN('Non payée') AND rastva > 0 AND trash='1'".$companies." AND (datedue BETWEEN ".time()." AND ".(time()+$value).")");
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd,delay FROM facturesuppliers WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();
								?>
					<p style="background:#fff6e5;"><a href="facturesuppliers.php?stateras=Non payée&datelimitrasstart=<?php echo date("d/m/Y",time());?>&datelimitrasend=<?php echo date("d/m/Y",(time() + $value));?>"><b><?php echo $back->rowCount();?></b> RAS TVA fournisseurs non payées avec délai de paiement restant de <ins>(≤ <?php echo explode("-",$notifications['rassupplierbefore'])[1];?> Jours)</ins></a></p>
								<?php
							$nbnotif++;
							}
						}
					}
						
					if(preg_match("#Consulter Trésorerie#",$_SESSION['easybm_roles'])){
						$value1 = intval(explode("-",$notifications['caissein'])[1])*24*60*60;
						$checked = explode("-",$notifications['caissein'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM payments WHERE type='Entrée' AND trash='1' AND datepaid=''".$companies." AND datedue<>'' AND datedue < ".(time() - $value1));
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd FROM payments WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();	
							?>
					<p style="background:#ffe5e5;"><a href="payments.php?statusid=a4a&status=Réglements reçus échus non encaissés&dateduestart=<?php echo date("d/m/Y",$row1['dateadd']);?>&datedueend=<?php echo date("d/m/Y",(time() - $value1) + 86400);?>"><b><?php echo $back->rowCount();?></b> "Réglements reçus" échus non encaissés avec un dépassement de <ins>(≥ <?php echo explode("-",$notifications['caissein'])[1];?> Jours)</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
						$value = intval(explode("-",$notifications['rcaissein'])[1])*24*60*60;
						$checked = explode("-",$notifications['rcaissein'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM payments WHERE type='Entrée' AND trash='1' AND datepaid=''".$companies." AND datedue<>'' AND datedue > ".strtotime(date("d-m-Y",time()))." AND datedue < ".(time() + $value - 86400));
							if($back->rowCount() > 0){
							?>
					<p style="background:#fff6e5;"><a href="payments.php?statusid=a1a&status=Réglements reçus non échus&dateduestart=<?php echo date("d/m/Y",time());?>&datedueend=<?php echo date("d/m/Y",(time() + $value - 86400));?>"><b><?php echo $back->rowCount();?></b> "Réglements reçus" seront échues dans <ins>(≤ <?php echo explode("-",$notifications['rcaissein'])[1];?> J restants)</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
					}

					if(preg_match("#Consulter Trésorerie#",$_SESSION['easybm_roles'])){
						$value1 = intval(explode("-",$notifications['caisseout'])[1])*24*60*60;
						$checked = explode("-",$notifications['caisseout'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM payments WHERE type='Sortie' AND trash='1' AND datepaid=''".$companies." AND datedue<>'' AND datedue < ".(time() - $value1));
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd FROM payments WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();
							?>
					<p style="background:#ffe5e5;"><a href="payments.php?statusid=a5a&status=Réglements émis échus non décaissés&dateduestart=<?php echo date("d/m/Y",$row1['dateadd']);?>&datedueend=<?php echo date("d/m/Y",(time() - $value1) + 86400);?>"><b><?php echo $back->rowCount();?></b> "Réglements émis" échus non décaissés avec un dépassement de <ins>(≥ <?php echo explode("-",$notifications['caisseout'])[1];?> Jours)</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
						$value = intval(explode("-",$notifications['rcaisseout'])[1])*24*60*60;
						$checked = explode("-",$notifications['rcaisseout'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM payments WHERE type='Sortie' AND trash='1' AND datepaid=''".$companies." AND datedue<>'' AND datedue > ".strtotime(date("d-m-Y",time()))." AND datedue < ".(time() + $value - 86400));
							if($back->rowCount() > 0){
							?>
					<p style="background:#fff6e5;"><a href="payments.php?statusid=a2a&status=Réglements émis non échus&dateduestart=<?php echo date("d/m/Y",time());?>&datedueend=<?php echo date("d/m/Y",(time() + $value) - 86400);?>"><b><?php echo $back->rowCount();?></b> "Réglements émis" seront échues dans <ins>(≤ <?php echo explode("-",$notifications['rcaisseout'])[1];?> J restants)</ins></a></p>
							<?php
							$nbnotif++;
							}
						}
					}

					if(preg_match("#Consulter Trésorerie#",$_SESSION['easybm_roles'])){
						$value1 = intval(explode("-",$notifications['recnoenc'])[1])*24*60*60;
						$checked = explode("-",$notifications['recnoenc'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM payments WHERE (type='Entrée' AND datepaid='') AND trash='1'".$companies);
							if($back->rowCount() > 0){
							?>
					<p style="background:#ffe5e5;"><a href="payments.php?statusid=a1a&status=Réglements reçus non encaissés"><b><?php echo $back->rowCount();?></b> Réglements reçus non encaissés</a></p>
							<?php
							$nbnotif++;
							}
						}
					}
					
					if(preg_match("#Consulter Trésorerie#",$_SESSION['easybm_roles'])){
						$value1 = intval(explode("-",$notifications['emisnodec'])[1])*24*60*60;
						$checked = explode("-",$notifications['emisnodec'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM payments WHERE (type='Sortie' AND datepaid='') AND trash='1'".$companies);
							if($back->rowCount() > 0){
							?>
					<p style="background:#ffe5e5;"><a href="payments.php?statusid=a2a&status=Réglements émis non décaissés"><b><?php echo $back->rowCount();?></b> Réglements émis non décaissés</a></p>
							<?php
							$nbnotif++;
							}
						}
					}

					if(preg_match("#Consulter Trésorerie#",$_SESSION['easybm_roles'])){
						$value1 = intval(explode("-",$notifications['remis'])[1])*24*60*60;
						$checked = explode("-",$notifications['remis'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM payments WHERE type='Entrée' AND trash='1' AND modepayment IN('Chèque','Effet') AND dateremis=''".$companies." AND datepaid='' AND datedue<>'' AND datedue < ".(time() - $value1));
							if($back->rowCount() > 0){
								$back1 = $bdd->query("SELECT dateadd FROM payments WHERE dateadd<>'' ORDER BY dateadd ASC");
								$row1 = $back1->fetch();
							?>
					<p style="background:#ffe5e5;"><a href="payments.php?statusid=a3a&status=Chèques / effets échus non remis&dateduestart=<?php echo date("d/m/Y",$row1['dateadd']);?>&datedueend=<?php echo date("d/m/Y",(time() - $value1));?>"><b><?php echo $back->rowCount();?></b> Chèques / effets échus non remis</a></p>
							<?php
							$nbnotif++;
							}
						}
					}
					
					if(preg_match("#Consulter Trésorerie#",$_SESSION['easybm_roles'])){
						$value1 = intval(explode("-",$notifications['unremis'])[1])*24*60*60;
						$checked = explode("-",$notifications['unremis'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM payments WHERE type='Entrée' AND trash='1' AND dateremis=''".$companies." AND datepaid=''");
							if($back->rowCount() > 0){
							?>
					<p style="background:#ffe5e5;"><a href="payments.php?statusid=a14a&status=Chèques / Effets / Espèce (reçus) non remis"><b><?php echo $back->rowCount();?></b> Chèques / Effets / Espèce (reçus) non remis</a></p>
							<?php
							$nbnotif++;
							}
						}
					}
					
					if(preg_match("#Consulter Trésorerie#",$_SESSION['easybm_roles'])){
						$value1 = intval(explode("-",$notifications['unpaid'])[1])*24*60*60;
						$checked = explode("-",$notifications['unpaid'])[0];
						if($checked == "on"){
							$back = $bdd->query("SELECT id FROM payments WHERE paid='1' AND trash='1'".$companies);
							if($back->rowCount() > 0){
							?>
					<p style="background:#ffe5e5;"><a href="payments.php?statusid=a8a&status=Chèques / effets impayés"><b><?php echo $back->rowCount();?></b> Chèques / effets impayés</a></p>
							<?php
							$nbnotif++;
							}
						}
					}
					?>
					<del style="display:none;" class="lx-notifications-nb"><?php echo $nbnotif;?></del>
					<?php
			}
		}
	}
}

function sanitize_vars($var){
	if(preg_match("#script|select|update|delete|concat|create|table|union|length|show_table|mysql_list_tables|mysql_list_fields|mysql_list_dbs#i",$var)){
		$var = "";
	}
	return htmlspecialchars(addslashes(trim($var)));
}

function random(){
	$alphabet = "0123456789";
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($j = 0; $j < 12; $j++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass);
}

function randomfct(){
	$alphabet = "0123456789";
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($j = 0; $j < 4; $j++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass);
}

function removeParentheses($input) {
    // Use a regular expression to remove parentheses and their contents
    $output = preg_replace('/\([^)]*\)/', '', $input);
    
    // Remove any extra spaces that may result from the removal
    $output = trim($output);
    
    return $output;
}

function getDateNow($date){
	if($date === date("d/m/Y")){
		$dateorder = time();
	}
	else{
		$dateorder = strtotime(str_replace("/","-",$date)) + (60*60*8);
	}
	return $dateorder;
}

function getHoursPassedToday() {
    // Create a DateTime object for the current time in the server's timezone
    $now = new DateTime('now');
    
    // Get the current hour in 24-hour format
    $currentHour = (float)$now->format('G');
    
    return $currentHour+1;
}

function daysBetweenTimestamps($timestamp1, $timestamp2) {
    // Ensure both inputs are integers
    $timestamp1 = (float)$timestamp1;
    $timestamp2 = (float)$timestamp2;

    // Difference in seconds (can be negative)
    $diffInSeconds = $timestamp2 - $timestamp1;

    // Floor the result to get full days (can be negative)
    return floor($diffInSeconds / 86400); // 86400 seconds in a day
}

function secondsUntilNext20th(): int {
    $now = new DateTime();

    // Get the 20th of next month
    $next20 = (clone $now)->modify('first day of next month');
    $next20->setDate($next20->format('Y'), $next20->format('m'), 20);
    $next20->setTime(0, 0, 0); // Set to midnight

    return $next20->getTimestamp() - $now->getTimestamp();
}

function whatsappToHtml($text) {
    // Escape HTML to prevent injection
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    
    // Convert WhatsApp markdown to HTML
    // Bold: *text*
    $text = preg_replace('/\*(.*?)\*/s', '<strong>$1</strong>', $text);
    // Italic: _text_
    $text = preg_replace('/_(.*?)_/s', '<em>$1</em>', $text);
    // Strikethrough: ~text~
    $text = preg_replace('/~(.*?)~/s', '<del>$1</del>', $text);
    
    return $text;
}

function sendmail($senderid,$sendername,$to,$subject,$message,$company,$idmessage,$doc,$params,$bdd){
	$back = $bdd->query("SELECT * FROM documents WHERE id='".$doc."'");
	
	if($back->rowCount() > 0){
		$doc = $back->fetch();
		$url = "";
		if($doc['type'] == "facture"){
			$url = "/printclientdocs.php?id=".$doc['id']."&title=Facture";
		}
		if($doc['type'] == "nh"){
			$url = "/printclientdocs.php?id=".$doc['id']."&title=Note d'honoraire";
		}
		if($doc['type'] == "devis"){
			$url = "/printclientdocs.php?id=".$doc['id']."&title=Devis";
		}
		if($doc['type'] == "ph"){
			$url = "/printclientdocs.php?id=".$doc['id']."&title=Proposition d'honoraire";
		}
		if($doc['type'] == "factureproforma"){
			$url = "/printclientdocs.php?id=".$doc['id']."&title=Facture proforma";
		}
		if($doc['type'] == "avoir"){
			$url = "/printclientdocs.php?id=".$doc['id']."&title=Facture avoir";
		}
		if($doc['type'] == "br"){
			$url = "/printclientdocs.php?id=".$doc['id']."&title=Bon de retour";
		}
		if($doc['type'] == "bl"){
			$url = "/printclientdocs.php?id=".$doc['id']."&title=Bon de livraison";
		}
		if($doc['type'] == "bs"){
			$url = "/printclientdocs.php?id=".$doc['id']."&title=Bon de sortie";
		}
		if($doc['type'] == "bc"){
			$url = "/printsupplierdocs.php?id=".$doc['id']."&title=Bon de commande";
		}
		if($doc['type'] == "bre"){
			$url = "/printsupplierdocs.php?id=".$doc['id']."&title=Bon de réception";
		}
		
		global $websiteurl;
		$fullUrl = $websiteurl . $url . $params;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $fullUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // if you’re using a self-signed cert
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$pdfContent = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if (!$pdfContent || $httpCode != 200) {
			throw new Exception("Failed to download PDF from $fullUrl (HTTP $httpCode)");
		}
	}
	
	$back = $bdd->query("SELECT * FROM companies WHERE rs='".$company."'");
	$row = $back->fetch();
	$back = $bdd->query("SELECT * FROM usersemails WHERE user='".$senderid."' AND company='".$row['id']."'");
	$row = $back->fetch();
	
	$mail = new PHPMailer(true);
	$mail->CharSet = 'UTF-8'; // ✅ Important
	
	try {
		// Server settings
		$mail->isSMTP();
		$mail->Host       = isset($row['host'])?$row['host']:'';
		$mail->SMTPAuth   = true;
		$mail->Username   = isset($row['email'])?$row['email']:'';
		$mail->Password   = isset($row['password'])?$row['password']:''; // Gmail App Password
		$mail->SMTPSecure = isset($row['smtpsecure'])?$row['smtpsecure']:'';
		$mail->Port       = isset($row['port'])?$row['port']:'';

		// Recipients
		$mail->setFrom($row['email'], $sendername);
		
		$emails = explode(";",$to);
		for($i=0;$i<count($emails);$i++){
			$mail->addAddress($emails[$i]);
		}
		
		// Content
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $message;
		if(isset($url)){
			$mail->addStringAttachment($pdfContent,$doc['code'].'.pdf');
		}

		$mail->send();
				
		echo 'Message has been sent successfully';
		$req = $bdd->prepare("UPDATE messageshistory SET status='Envoyé' WHERE id='".$idmessage."'");
	} catch (Exception $e) {
		echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		$req = $bdd->prepare("UPDATE messageshistory SET status='Non envoyé' WHERE id='".$idmessage."'");
	}
	$req->execute();
}

function normalizeString($str) {
    $str = trim($str);

    // Check if wrapped in double quotes
    if (strlen($str) >= 2 && $str[0] === '"' && $str[strlen($str)-1] === '"') {
        // Remove the surrounding quotes
        return addslashes(substr($str, 1, -1));
    } else {
        // Wrap in %%
        return "%" . addslashes($str) . "%";
    }
}

function secondsToMidnight($timezone = null) {
    // If no timezone is passed, use server default
    $tz = $timezone ? new DateTimeZone($timezone) : null;

    // Current datetime
    $now = new DateTime("now", $tz);

    // Next midnight (00:00:00 tomorrow)
    $midnight = new DateTime("tomorrow midnight", $tz);

    // Difference in seconds
    return $midnight->getTimestamp() - $now->getTimestamp();
}

function secondsSinceMidnight($timezone = null) {
    // If no timezone is passed, use server default
    $tz = $timezone ? new DateTimeZone($timezone) : null;

    // Current datetime
    $now = new DateTime("now", $tz);

    // Today midnight
    $midnight = new DateTime("today midnight", $tz);

    // Difference in seconds
    return $now->getTimestamp() - $midnight->getTimestamp();
}
					
function updateDocKPI($commandid){
	global $bdd;
	
	$back = $bdd->query("SELECT SUM(price) AS sm FROM payments WHERE doc='".$commandid."' AND typedoc NOT IN('facturesupplier')");
	$payment = $back->fetch();

	$pay = isset($payment['sm'])?$payment['sm']:0;

	$back = $bdd->query("SELECT *,client,company FROM documents d WHERE id='".$commandid."'");
	$row = $back->fetch();

	if(($row['price']-($row['rastva'])) <= $pay){
		$state = "Payée";
	}
	elseif((($row['price']-($row['rastva']))) > $pay AND $pay > 0){
		$state = "Partiellement payée";
	}
	else{
		$state = 'Non payée';
	}
	
	$req = $bdd->prepare("UPDATE documents SET payments='".$pay."',state='".$state."' WHERE id='".$commandid."'");
	$req->execute();
	
	$back = $bdd->query("SELECT SUM(CASE WHEN typedoc='facture' OR typedoc='nh' OR typedoc='nf' THEN price WHEN typedoc='avoir' THEN price*(-1) END) AS sm FROM payments WHERE typedoc IN('facture','nh','nf','avoir') AND client='".$row['client']."'");
	$payments = $back->fetch();	
	
	$back = $bdd->query("SELECT SUM(CASE WHEN type='facture' OR type='nh' OR type='nf' THEN price WHEN type='avoir' THEN price*(-1) END) AS sm FROM documents WHERE type IN('facture','nh','nf','avoir') AND client='".$row['client']."'");
	$ca = $back->fetch();	
	
	$req = $bdd->prepare("UPDATE clients SET ca='".((float)$ca['sm'])."',payments='".((float)$payments['sm'])."' WHERE id='".$row['client']."'");
	$req->execute();
	
	$back = $bdd->query("SELECT SUM(CASE WHEN typedoc='facture' OR typedoc='nh' OR typedoc='nf' THEN price WHEN typedoc='avoir' THEN price*(-1) END) AS sm FROM payments WHERE typedoc IN('facture','nh','nf','avoir') AND company='".$row['company']."'");
	$payments = $back->fetch();	

	$pay = (float)(isset($payments['sm'])?$payments['sm']:0);
	
	$back = $bdd->query("SELECT SUM(CASE WHEN type='facture' OR type='nh' OR type='nf' THEN (price-rastva) WHEN type='avoir' THEN price*(-1) END) AS sm FROM documents WHERE type IN('facture','nh','nf','avoir') AND company='".$row['company']."'");
	$ca = $back->fetch();	

	$ca = (float)(isset($ca['sm'])?$ca['sm']:0);
	
	$req = $bdd->prepare("UPDATE companies SET ca='".$ca."',payments='".$pay."' WHERE id='".$row['company']."'");
	$req->execute();
}

function updateFSKPI($commandid){
	global $bdd;
	
	$back = $bdd->query("SELECT SUM(price) AS sm,SUM(price2) AS sm2,supplier FROM payments WHERE doc='".$commandid."' AND typedoc IN('facturesupplier')");
	$payment = $back->fetch();
	
	$pay = (float)(isset($payment['sm'])?$payment['sm']:0);
	$pay2 = (float)(isset($payment['sm2'])?$payment['sm2']:0);
	
	$back = $bdd->query("SELECT *,supplier FROM facturesuppliers d WHERE id='".$commandid."'");
	$row = $back->fetch();
	
	if($row['currency'] != "MAD" AND $row['currency'] != "DH" AND $row['currency'] != ""){
		if(($row['price2']+($row['tva2'])) <= $pay2){
			$state = "Payée";
		}
		elseif((($row['price2']+($row['tva2']))) > $pay2 AND $pay2 > 0){
			$state = "Partiellement payée";
		}
		else{
			$state = 'Non payée';
		}		
	}
	else{
		if(($row['price']+($row['tva']-$row['rastva'])) <= $pay){
			$state = "Payée";
		}
		elseif((($row['price']+($row['tva']-$row['rastva']))) > $pay AND $pay > 0){
			$state = "Partiellement payée";
		}
		else{
			$state = 'Non payée';
		}				
	}
	
	$req = $bdd->prepare("UPDATE facturesuppliers SET state='".$state."',payments='".$pay."',devispayments='".$pay2."' WHERE id='".$commandid."'");
	$req->execute();
	
	$back = $bdd->query("SELECT SUM(price) AS sm,SUM(price2) AS sm2 FROM payments WHERE typedoc='facturesupplier' AND supplier='".$row['supplier']."'");
	$payments = $back->fetch();	
	
	$pay = (float)(isset($payments['sm'])?$payments['sm']:0);
	$pay2 = (float)(isset($payments['sm2'])?$payments['sm2']:0);
	
	$back = $bdd->query("SELECT SUM(price + (tva - rastva)) AS sm,SUM(price2 + tva2) AS sm2 FROM facturesuppliers WHERE supplier='".$row['supplier']."'");
	$ca = $back->fetch();	

	$ca = (float)(isset($ca['sm'])?$ca['sm']:0);
	$ca2 = (float)(isset($ca['sm2'])?$ca['sm2']:0);
	
	$req = $bdd->prepare("UPDATE suppliers SET ca='".$ca."',payments='".$pay."',ca2='".$ca2."',payments2='".$pay2."' WHERE id='".$row['supplier']."'");
	$req->execute();
}

function updatePrevDocKPI($client,$supplier){
	global $bdd;
	
	if($client > 0){
		$back = $bdd->query("SELECT company FROM clients WHERE id='".$client."'");
		$client1 = $back->fetch();	
		
		$back = $bdd->query("SELECT SUM(CASE WHEN typedoc='facture' OR typedoc='nh' OR typedoc='nf' THEN price WHEN typedoc='avoir' THEN price*(-1) END) AS sm FROM payments WHERE typedoc IN('facture','nh','nf','avoir') AND client='".$client."'");
		$payments = $back->fetch();	
		
		$back = $bdd->query("SELECT SUM(CASE WHEN type='facture' OR type='nh' OR type='nf' THEN price WHEN type='avoir' THEN price*(-1) END) AS sm FROM documents WHERE type IN('facture','nh','nf','avoir') AND client='".$client."'");
		$ca = $back->fetch();	
		
		$req = $bdd->prepare("UPDATE clients SET ca='".((float)$ca['sm'])."',payments='".((float)$payments['sm'])."' WHERE id='".$client."'");
		$req->execute();
		
		$back = $bdd->query("SELECT SUM(CASE WHEN typedoc='facture' OR typedoc='nh' OR typedoc='nf' THEN price WHEN typedoc='avoir' THEN price*(-1) END) AS sm FROM payments WHERE typedoc IN('facture','nh','nf','avoir') AND company='".$client1['company']."'");
		$payments = $back->fetch();	

		$pay = (float)(isset($payments['sm'])?$payments['sm']:0);
		
		$back = $bdd->query("SELECT SUM(CASE WHEN type='facture' OR type='nh' OR type='nf' THEN (price-rastva) WHEN type='avoir' THEN price*(-1) END) AS sm FROM documents WHERE type IN('facture','nh','nf','avoir') AND company='".$client1['company']."'");
		$ca = $back->fetch();	

		$ca = (float)(isset($ca['sm'])?$ca['sm']:0);
		
		$req = $bdd->prepare("UPDATE companies SET ca='".$ca."',payments='".$pay."' WHERE id='".$client1['company']."'");
		$req->execute();		
	}
	if($supplier > 0){
		$back = $bdd->query("SELECT SUM(price) AS sm,SUM(price2) AS sm2 FROM payments WHERE typedoc='facturesupplier' AND supplier='".$supplier."'");
		$payments = $back->fetch();
		
		$pay = (float)(isset($payments['sm'])?$payments['sm']:0);
		$pay2 = (float)(isset($payments['sm2'])?$payments['sm2']:0);
		
		$back = $bdd->query("SELECT SUM(price + (tva - rastva)) AS sm,SUM(price2 + tva2) AS sm2 FROM facturesuppliers WHERE supplier='".$supplier."'");
		$ca = $back->fetch();	

		$ca = (float)(isset($ca['sm'])?$ca['sm']:0);
		$ca2 = (float)(isset($ca['sm2'])?$ca['sm2']:0);

		$pay = $ca>0?$pay:0;
		$pay2 = $ca2>0?$pay2:0;
		
		$req = $bdd->prepare("UPDATE suppliers SET ca='".$ca."',payments='".$pay."',ca2='".$ca2."',payments2='".$pay2."' WHERE id='".$supplier."'");
		$req->execute();
	}
}

function updateDocAvoir($doc){
	global $bdd;
		
	$back = $bdd->query("SELECT GROUP_CONCAT(CONCAT(id,'/',code,'/',price,'/') SEPARATOR '|') AS avoirs FROM documents WHERE type='avoir' AND correctdoc='".$doc."'");
	$avoirs = $back->fetch();
	$back = $bdd->query("SELECT GROUP_CONCAT(CONCAT(id,'/',code,'/',price,'/') SEPARATOR '|') AS brs FROM documents WHERE type='br' AND correctdoc='".$doc."'");
	$brs = $back->fetch();	
	$req = $bdd->prepare("UPDATE documents SET avoirs='".$avoirs['avoirs']."',brs='".$brs['brs']."' WHERE code='".$doc."'");
	$req->execute();
}

function uniquePairs(array $a, array $b): array {
    $resultA = [];
    $resultB = [];
    $seen = [];

    foreach ($a as $i => $val) {
        // Normalize value by stripping HTML and trimming whitespace
        $normalized = trim(strip_tags($val));

        if (!in_array($normalized, $seen, true)) {
            $seen[] = $normalized;
            $resultA[] = $val;   // keep original version (with or without HTML)
            $resultB[] = $b[$i]; // keep matching from $b
        }
    }

    return [$resultA, $resultB];
}

function array_unique_ci(array $array) {
    $lowercased = array_map('mb_strtolower', $array); // lowercase version for comparison
    return array_intersect_key($array, array_unique($lowercased));
}

function cleanString($str) {
    // Remove all HTML tags
    $str = strip_tags($str);

    // Decode HTML entities like &nbsp; or &#10;
    $str = html_entity_decode($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Remove remaining encoded entities (just in case)
    $str = preg_replace('/&[a-zA-Z0-9#]+;/', '', $str);

    // Remove all whitespace (spaces, tabs, newlines, etc.)
    $str = preg_replace('/\s+/', '', $str);

    // Trim final result
    return htmlspecialchars(trim($str));
}

function buildValueString($value, $qtys) {
    // Split qtys by ||
    $qtyArray = explode('||', $qtys);
    $count = count($qtyArray);

    // Extract the base value: first non-empty trimmed segment
    $parts = preg_split('/\|\|/', $value);
    $base = '';
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p !== '') {
            $base = $p;
            break;
        }
    }

    // Repeat the value according to the qty count
    return implode('||', array_fill(0, $count, $base));
}
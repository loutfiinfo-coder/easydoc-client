<div class="lx-header-content">
	<a href="javascript:;" class="lx-mobile-menu"><i class="material-icons">menu</i></a>
	<div class="lx-header-admin">
		<ul>
			<li>
				<?php
				if(preg_match("#Consulter Formation#",$_SESSION['easybm_roles'])){
				?>
				<a href="formation.php" class="lx-formation-button"><i class="fab fa-youtube"></i> Formation</a>
				<?php
				}
				?>
			</li>
			<li>
				<?php
				if(preg_match("#Consultation des notifications#",$_SESSION['easybm_roles'])){
				?>
				<div class="lx-notifications-list">
					<i class="fa fa-caret-up"></i>
					<div>

					</div>
					<?php
					if(preg_match("#Réglage des notifications#",$_SESSION['easybm_roles'])){
					?>
					<a href="javascript:;" class="lx-edit-notifications lx-open-popup" data-title="editnotifications">Réglage notifications</a>
					<?php
					}
					?>
				</div>
				<p>
					<a href="javascript:;" class="lx-show-notifications"><i class="far fa-bell"></i><ins></ins></a>
				</p>
				<?php
				}
				?>
			</li>
			<li>
				<?php
				$picture = "images/avatar.png";
				if($_SESSION['easybm_picture'] != "avatar.png"){
					$picture = "uploads/cropped_".$_SESSION['easybm_picture'];
				}
				?>
				<p class="lx-show-params"><img src="<?php echo $picture;?>" /><i class="fa fa-angle-down"></i></p>
				<div class="lx-account-settings">
					<div>
						<strong><?php echo $_SESSION['easybm_fullname'];?></strong>
						<p><?php echo $_SESSION['easybm_email'];?></p>
					</div>
					<a href="account.php"><i class="fa fa-user"></i> Mon profile</a>
					<a href="settings.php"><i class="fa fa-image"></i> Affichage</a>
					<a href="tvas.php"><i class="fa fa-coins"></i> TVA & Devises</a>
					<?php
					if(preg_match("#Consulter Modèles de messages#",$_SESSION['easybm_roles'])){
					?>
					<a href="messages.php?type=fc"><i class="fa fa-envelope"></i> Modèles de messages</a>
					<?php
					}
					if(preg_match("#Télécharger Backup#",$_SESSION['easybm_roles'])){
					?>
					<a href="downloadbackup.php"><i class="fa fa-download"></i> Backup base de données</a>
					<?php
					}
					?>
					<a href="disconnect.php"><i class="fa fa-power-off"></i> Déconnexion</a>
				</div>
			</li>
			<li>
				<a href="javascript:;" dir="rtl"><img src="images/5.png" width="30px" title="قريبا ..." /></a>
			</li>
			<div class="lx-clear-fix"></div>
		</ul>
		<div class="lx-clear-fix"></div>
	</div>
	<div class="lx-clear-fix"></div>
</div>
<div class="lx-clear-fix"></div>
<input type="hidden" id="appname" value="<?php echo $settings['store'];?>" />
<div class="lx-signature">
	<p><span><a href="javascript:;" class="lx-open-popup" data-title="contactus">Contact</a> &mdash; &copy; copyright</span> <img src="images/2.png" /></p>
</div>
<!-- End Popup -->	
<div tabindex="0" class="lx-popup contactus">
	<div class="lx-popup-inside">
		<div class="lx-popup-content">
			<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
			<div class="lx-popup-details">
				<div class="lx-form">
					<div class="lx-form-title">
						<h3>Contact</h3>
					</div>
					<div class="lx-add-form">
						<div class="lx-delete-box lx-contact-box">
							<p>Vous avez des questions ou demandes? Contactez nous:</p>
							<p><b>E-mail: </b><a href="mailto:contact@easydoc.ma">contact@easydoc.ma</a></p>
							<p><b>Téléphone / WhatsApp: </b><a href="tel:0601810237">0601810237</a></p>
						</div>
						<fieldset style="margin-bottom:20px;padding:10px;background:#F8F8F8;">
							<legend><b>IMPORTANT:</b></legend>
							<p>L'application <b>EasyDoc</b> est uniquement concédée sous licence aux fins d'une utilisation <b>non commerciale</b> pour vos besoins opérationnels internes. Le terme « Utilisation non commerciale » signifie que vous ne pouvez pas vendre, louer, donner à bail, ni prêter ce qui est produit au moyen de l'application. Toute autre forme d'utilisation nécessite l'achat d'une licence <b>EasyDoc</b></p>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Popup -->	
<div tabindex="0" class="lx-popup editnotifications">
	<div class="lx-popup-inside">
		<div class="lx-popup-content">
			<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
			<div class="lx-popup-details">
				<div class="lx-form">
					<div class="lx-form-title">
						<h3>Règlage notifications</h3>
					</div>
					<div class="lx-add-form">
						<form autocomplete="off" action="#" method="post" id="editnotificationsform">
							<div class="lx-table lx-table-notifications lx-g1">
								<fieldset style="padding:0px 15px 15px 15px;">
									<legend>Alertes clients</legend>
									<table>
										<tr>
											<td class="lx-wrap" style="width:auto;">Rappel paiement factures clients</td>
											<td>Activer</td>
										</tr>
										<tr>
											<?php
											$value = explode("-",$notifications['fbefore'])[1];
											$checked = explode("-",$notifications['fbefore'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['fbefore'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#fff6e5;width:auto;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;white-space:nowrap;">Factures clients non payées avec délai de paiement restant de ≤</span>
													<label style="display:block;"><input style="display:block;" type="text" autocomplete="off" name="fbefore" value="<?php echo $readonly!="readonly"?$value:"";?>" data-isnumber="" data-message="Saisissez un numéro !!" <?php echo $readonly;?> /><address>jours</address></label>
												</div>															
											</td>
											<td style="background:#fff6e5;">
												<label><input type="checkbox" name="onfbefore" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>
										</td>
											<?php
											$value = explode("-",$notifications['fafter'])[1];
											$checked = explode("-",$notifications['fafter'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['fafter'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#ffe5e5;width:auto;height:60px;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;white-space:nowrap;">Factures clients non payées avec un dépassement de délai de paiement</span>
												</div>															
											</td>
											<td style="background:#ffe5e5;">
												<label><input type="checkbox" name="onfafter" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>									
									</table>
									<br />
									<table>
										<tr>
											<td class="lx-wrap" style="width:auto;">Rappel paiement RAS TVA clients</td>
											<td>Activer</td>
										</tr>
										<tr>
											<?php
											$value = explode("-",$notifications['rasclientbefore'])[1];
											$checked = explode("-",$notifications['rasclientbefore'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['rasclientbefore'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#fff6e5;width:auto;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;white-space:nowrap;">RAS TVA clients non payées avec délai de paiement restant de ≤</span>
													<label style="display:block;"><input style="display:block;" type="text" autocomplete="off" name="rasclientbefore" value="<?php echo $readonly!="readonly"?$value:"";?>" data-isnumber="" data-message="Saisissez un numéro !!" <?php echo $readonly;?> /><address>jours</address></label>
												</div>															
											</td>
											<td style="background:#fff6e5;">
												<label><input type="checkbox" name="onrasclientbefore" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>
										</td>
											<?php
											$value = explode("-",$notifications['rasclientafter'])[1];
											$checked = explode("-",$notifications['rasclientafter'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['rasclientafter'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#ffe5e5;width:auto;height:60px;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;white-space:nowrap;">RAS TVA clients non payées avec un dépassement de délai de paiement</span>
												</div>															
											</td>
											<td style="background:#ffe5e5;">
												<label><input type="checkbox" name="onrasclientafter" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>									
									</table> 
									<br />
									<table>
										<tr>
											<td class="lx-wrap" style="width:auto;">Rappel remboursement avoir</td>
											<td style="width:1%;">Activer</td>
										</tr>
										<tr>
											<?php
											$value = explode("-",$notifications['avoir'])[1];
											$checked = explode("-",$notifications['avoir'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['avoir'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#ffe5e5;width:auto;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;">Avoirs non remboursés avec un dépassement de ≥</span>
													<label style="display:block;"><input style="display:block;" type="text" autocomplete="off" name="avoir" value="<?php echo $readonly!="readonly"?$value:"";?>" data-isnumber="" data-message="Saisissez un numéro !!" <?php echo $readonly;?> /><address>jours</address></label>
												</div>															
											</td>
											<td style="background:#ffe5e5;">
												<label><input type="checkbox" name="onavoir" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>
									</table>
								</fieldset>
								<br />
								<fieldset style="padding:0px 15px 15px 15px;">
									<legend>Alertes fournisseurs</legend>
									<table>
										<tr>
											<td class="lx-wrap" style="width:auto;">Alert délai de paiement factures fournisseurs</td>
											<td>Activer</td>
										</tr>
										<tr>
											<?php
											$value = explode("-",$notifications['fsbefore'])[1];
											$checked = explode("-",$notifications['fsbefore'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['fsbefore'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#fff6e5;width:auto;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;white-space:nowrap;">Factures fournisseurs non payées avec délai de paiement restant de ≤</span>
													<label style="display:block;"><input style="display:block;" type="text" autocomplete="off" name="fsbefore" value="<?php echo $readonly!="readonly"?$value:"";?>" data-isnumber="" data-message="Saisissez un numéro !!" <?php echo $readonly;?> /><address>jours</address></label>
												</div>															
											</td>
											<td style="background:#fff6e5;">
												<label><input type="checkbox" name="onfsbefore" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>
										</td>
											<?php
											$value = explode("-",$notifications['fsafter'])[1];
											$checked = explode("-",$notifications['fsafter'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['fsafter'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#ffe5e5;width:auto;height:60px;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;white-space:nowrap;">Factures fournisseurs non payées avec un dépassement de délai de paiement</span>
												</div>															
											</td>
											<td style="background:#ffe5e5;">
												<label><input type="checkbox" name="onfsafter" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>									
									</table> 
									<br />
									<table>
										<tr>
											<td class="lx-wrap" style="width:auto;">Rappel paiement RAS TVA fournisseurs</td>
											<td>Activer</td>
										</tr>
										<tr>
											<?php
											$value = explode("-",$notifications['rassupplierbefore'])[1];
											$checked = explode("-",$notifications['rassupplierbefore'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['rassupplierbefore'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#fff6e5;width:auto;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;white-space:nowrap;">RAS TVA clients non payées avec délai de paiement restant de ≤</span>
													<label style="display:block;"><input style="display:block;" type="text" autocomplete="off" name="rassupplierbefore" value="<?php echo $readonly!="readonly"?$value:"";?>" data-isnumber="" data-message="Saisissez un numéro !!" <?php echo $readonly;?> /><address>jours</address></label>
												</div>															
											</td>
											<td style="background:#fff6e5;">
												<label><input type="checkbox" name="onrassupplierbefore" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>
										</td>
											<?php
											$value = explode("-",$notifications['rassupplierafter'])[1];
											$checked = explode("-",$notifications['rassupplierafter'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['rassupplierafter'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#ffe5e5;width:auto;height:60px;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;white-space:nowrap;">RAS TVA clients non payées avec un dépassement de délai de paiement</span>
												</div>															
											</td>
											<td style="background:#ffe5e5;">
												<label><input type="checkbox" name="onrassupplierafter" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>									
									</table>
								</fieldset>
								<br />
								<fieldset style="padding:0px 15px 15px 15px;">
									<legend>Alertes trésorerie</legend>
									<table>
										<tr>
											<td class="lx-wrap">Délai de déclenchement en jours restants</td>
											<td>Activer</td>
											<td class="lx-wrap">Délai de déclenchement en jours passés</td>
											<td>Activer</td>
										</tr>
										<tr>
											<?php
											$value = explode("-",$notifications['rcaissein'])[1];
											$checked = explode("-",$notifications['rcaissein'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['rcaissein'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#fff6e5;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;white-space:nowrap;">"Réglements reçus" seront échues dans ≤</span>
													<label style="display:block;"><input style="display:block;" type="text" autocomplete="off" name="rcaissein" value="<?php echo $readonly!="readonly"?$value:"";?>" data-isnumber="" data-message="Saisissez un numéro !!" <?php echo $readonly;?> /><address>jours</address></label>
												</div>															
											</td>
											<td style="background:#fff6e5;">
												<label><input type="checkbox" name="roncaissein" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
											<?php
											$value = explode("-",$notifications['caissein'])[1];
											$checked = explode("-",$notifications['caissein'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['caissein'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#ffe5e5;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;white-space:nowrap;">"Réglements reçus" échus non encaissés avec un dépassement de ≥</span>
													<label style="display:block;"><input style="display:block;" type="text" autocomplete="off" name="caissein" value="<?php echo $readonly!="readonly"?$value:"";?>" data-isnumber="" data-message="Saisissez un numéro !!" <?php echo $readonly;?> /><address>jours</address></label>
												</div>															
											</td>
											<td style="background:#ffe5e5;">
												<label><input type="checkbox" name="oncaissein" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>									
										<tr>
											<?php
											$value = explode("-",$notifications['rcaisseout'])[1];
											$checked = explode("-",$notifications['rcaisseout'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['rcaisseout'])[0]=="on"?"":"readonly";
											?>	
											<td style="background:#fff6e5;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;white-space:nowrap;">"Réglements émis" seront échues dans ≤</span>
													<label style="display:block;"><input style="display:block;" type="text" autocomplete="off" name="rcaisseout" value="<?php echo $readonly!="readonly"?$value:"";?>" data-isnumber="" data-message="Saisissez un numéro !!" <?php echo $readonly;?> /><address>jours</address></label>
												</div>															
											</td>
											<td style="background:#fff6e5;">
												<label><input type="checkbox" name="roncaisseout" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
											<?php
											$value = explode("-",$notifications['caisseout'])[1];
											$checked = explode("-",$notifications['caisseout'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['caisseout'])[0]=="on"?"":"readonly";
											?>	
											<td style="background:#ffe5e5;">
												<div class="lx-textfield lx-g1-f">
													<span style="font-weight:500;white-space:nowrap;">"Réglements émis" échus non décaissés avec un dépassement de ≥</span>
													<label style="display:block;"><input style="display:block;" type="text" autocomplete="off" name="caisseout" value="<?php echo $readonly!="readonly"?$value:"";?>" data-isnumber="" data-message="Saisissez un numéro !!" <?php echo $readonly;?> /><address>jours</address></label>
												</div>															
											</td>
											<td style="background:#ffe5e5;">
												<label><input type="checkbox" name="oncaisseout" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>
									</table> 
									<br />
									<table>
										<tr>
											<td style="width:auto;">Réglements</td>
											<td style="width:1%">Activer</td>
										</tr>
										<tr>
											<td class="lx-wrap" style="background:#ffe5e5;width:auto;"><span style="font-weight:500;">Réglements reçus non encaissés</span></td>
											<?php
											$value = explode("-",$notifications['recnoenc'])[1];
											$checked = explode("-",$notifications['recnoenc'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['recnoenc'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#ffe5e5;">
												<label><input type="checkbox" name="onrecnoenc" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>	
										<tr>
											<td class="lx-wrap" style="background:#ffe5e5;width:auto;"><span style="font-weight:500;">Réglements émis non décaissés</span></td>
											<?php
											$value = explode("-",$notifications['emisnodec'])[1];
											$checked = explode("-",$notifications['emisnodec'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['emisnodec'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#ffe5e5;">
												<label><input type="checkbox" name="onemisnodec" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>	
										<tr>
											<td class="lx-wrap" style="background:#ffe5e5;width:auto;"><span style="font-weight:500;">Chèques / effets échus non remis</span></td>
											<?php
											$value = explode("-",$notifications['remis'])[1];
											$checked = explode("-",$notifications['remis'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['remis'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#ffe5e5;">
												<label><input type="checkbox" name="onremis" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>	
										<tr>
											<td class="lx-wrap" style="background:#ffe5e5;width:auto;"><span style="font-weight:500;">Chèques / Effets / Espèce (reçus) non remis</span></td>
											<?php
											$value = explode("-",$notifications['unremis'])[1];
											$checked = explode("-",$notifications['unremis'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['unremis'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#ffe5e5;">
												<label><input type="checkbox" name="onunremis" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>	
										<tr>
											<td class="lx-wrap" style="background:#ffe5e5;width:auto;"><span style="font-weight:500;">Chèques / effets impayés</span></td>
											<?php
											$value = explode("-",$notifications['unpaid'])[1];
											$checked = explode("-",$notifications['unpaid'])[0]=="on"?"checked":"";
											$readonly = explode("-",$notifications['unpaid'])[0]=="on"?"":"readonly";
											?>
											<td style="background:#ffe5e5;width:1%">
												<label><input type="checkbox" name="onunpaid" value="1" <?php echo $checked;?> /><del class="checkmark"></del></label>													
											</td>
										</tr>
									</table>
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
<div tabindex="0" class="lx-popup signature">
	<div class="lx-popup-inside">
		<div class="lx-popup-content">
			<a href="javascript:;"><i class="material-icons"><img src="images/close.svg" /></i></a>
			<div class="lx-popup-details">
				<div class="lx-form">
					<div class="lx-form-title">
						<h3>Paramètres d'impression</h3>
					</div>
					<div class="lx-add-form">
						<form autocomplete="off" action="#" method="post" id="printform">
							<div class="lx-print-options" style="position:relative;">

							</div>
							<div class="lx-submit lx-g1 lx-pb-0">
								<input type="hidden" name="href" value="" />
								<a href="javascript:;" class="">Télécharger</a>
							</div>	
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" id="onlyproduct" value="<?php echo $settings['onlyproduct'];?>" />
<input type="hidden" id="onlyservice" value="<?php echo $settings['onlyservice'];?>" />
<input type="hidden" id="currency" value="<?php echo $settings['currency'];?>" />
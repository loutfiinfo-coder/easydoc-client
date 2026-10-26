<?php
include("config.php");
			for($i = 94145;$i < 100000;$i++){
				$randcode = 'FA'.date("ym",time())."-".sprintf('%06d',$i);
				
				// Save Client
				$rand = "CL".date("ym")."-".sprintf('%06d',$i);
				$back1 = $bdd->query("SELECT code FROM clients ORDER BY id DESC LIMIT 0,1");
				$req = $bdd->prepare("INSERT INTO clients(id,code,ice,fullname,phone,address,email,company,dateadd,trash) 
				VALUES ('0','".$rand."','','Hassan','0606060606','','','1','".time()."','1')");
				$req->execute();
				$back = $bdd->query("SELECT id FROM clients ORDER BY id DESC LIMIT 0,1");
				$row = $back->fetch();
				$client = $row['id'];
						
				$dateorder = time();
				$paid = "Non payée";

				$req = $bdd->prepare("INSERT INTO `documents`(`id`, `code`, `price`, `state`, `type`, `category`, `client`, `supplier`, `company`, `note`, `correctdoc`, `modepayment`, `conditions`, `abovetable`, `user`, `dateadd`, `trash`) 
				VALUES ('0','".$randcode."','0','".$paid."','facture','client','".$client."','0','1','','','','','','4','".$dateorder."','1')");
				$req->execute();
				$back = $bdd->query("SELECT id FROM documents ORDER BY id DESC LIMIT 0,1");
				$command = $back->fetch();
				$commandid = $command['id'];

				$req = $bdd->prepare("INSERT INTO `detailsdocuments`(`id`, `doc`, `title`, `abovenote`, `belownote`, `unit`, `qty`, `uprice`, `tva`, `discounttype`, `discount`, `tprice`, `client`, `supplier`, `company`, `category`, `typedoc`, `dateadd`, `trash`) 
				VALUES ('0','".$commandid."','Product TEST','','','U','1','100','20','','','100','".$client."','0','1','client','facture','".$dateorder."','1')");
				$req->execute();
					
				$req = $bdd->prepare("UPDATE documents SET price='10' WHERE id='".$commandid."'");
				$req->execute();
			}
?>
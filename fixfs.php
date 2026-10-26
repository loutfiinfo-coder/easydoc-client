<?php
include("config.php");

$back1 = $bdd->query("SELECT * FROM suppliers");
while($command = $back1->fetch()){
	
	$back = $bdd->query("SELECT SUM(price) AS sm,SUM(price2) AS sm2 FROM payments WHERE typedoc='facturesupplier' AND supplier='".$command['id']."'");
	$payments = $back->fetch();
	
	$pay = (float)(isset($payments['sm'])?$payments['sm']:0);
	$pay2 = (float)(isset($payments['sm2'])?$payments['sm2']:0);
	
	$back = $bdd->query("SELECT SUM(price + (tva - rastva)) AS sm,SUM(price2 + tva2) AS sm2 FROM facturesuppliers WHERE supplier='".$command['id']."'");
	$ca = $back->fetch();	

	$ca = (float)(isset($ca['sm'])?$ca['sm']:0);
	$ca2 = (float)(isset($ca['sm2'])?$ca['sm2']:0);

	$pay = $ca>0?$pay:0;
	$pay2 = $ca2>0?$pay2:0;
	
	$req = $bdd->prepare("UPDATE suppliers SET ca='".$ca."',payments='".$pay."',ca2='".$ca2."',payments2='".$pay2."' WHERE id='".$command['id']."'");
	$req->execute();
}

?>
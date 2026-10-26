<?php
// Show only fatal errors (E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR)
error_reporting(E_ERROR);

// Optional: stop displaying warnings/notices on the page
ini_set('max_execution_time', 0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('timezone', 'Africa/Casablanca');


try{
$bdd = new PDO('mysql:host=db;dbname=easydoc_db;charset=utf8', 'easydoc_user', 'easydocpassword', array(PDO::ATTR_PERSISTENT => true));
}
catch(Exception $e){
	die('Erreur : '.$e->getMessage());
}					

if(isset($_COOKIE['id'])){
	$_SESSION['easybm_id'] = $_COOKIE['id'];
	$_SESSION['easybm_fullname'] = $_COOKIE['fullname'];
	$_SESSION['easybm_picture'] = $_COOKIE['picture'];
	$_SESSION['easybm_phone'] = $_COOKIE['phone'];
	$_SESSION['easybm_email'] = $_COOKIE['email'];
	$_SESSION['easybm_password'] = $_COOKIE['password'];
	$_SESSION['easybm_roles'] = isset($_COOKIE['roles'])?$_COOKIE['roles']:"";
	$_SESSION['easybm_companies'] = $_COOKIE['companies'];
	$_SESSION['easybm_type'] = $_COOKIE['type'];
	$_SESSION['easybm_superadmin'] = $_COOKIE['superadmin'];
}

$websiteurl = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){ 
	$websiteurl = 'https://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
}

$user = "";
$worker = 0;
if(isset($_SESSION['easybm_type'])){
	$back = $bdd->query("SELECT * FROM parametres WHERE user='".$_SESSION['easybm_id']."'");
	$parametres = $back->fetch();	
	$back = $bdd->query("SELECT * FROM users WHERE id='".$_SESSION['easybm_id']."'");
	$row = $back->fetch();
	$_SESSION['easybm_fullname'] = $row['fullname'];
	$_SESSION['easybm_picture'] = $row['picture'];
	$_SESSION['easybm_roles'] = $row['roles'];
	$_SESSION['easybm_companies'] = "0,".$row['companies'];
	$companies = " AND company IN(".(trim($_SESSION['easybm_companies'],",")!=""?trim($_SESSION['easybm_companies'],","):0).")";
	$companiesid = " AND id IN(".(trim($_SESSION['easybm_companies'],",")!=""?trim($_SESSION['easybm_companies'],","):0).")";
	$multicompanies = "";
	if($_SESSION['easybm_companies'] != ""){
		$comp = explode(",",$_SESSION['easybm_companies']);
		$multicompanies .= " AND (";
		for($j=0;$j<count($comp);$j++){
			if($j==0){
				$multicompanies .= "FIND_IN_SET(".$comp[$j].", company)";
			}
			else{
				$multicompanies .= " OR FIND_IN_SET(".$comp[$j].", company)";
			}
		}
		$multicompanies .= ")";
	}

	$stmt = $bdd->query("SELECT id, rs FROM companies WHERE trash='1'".$companiesid);
	$listcompanies = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

	$stmt = $bdd->query("SELECT id,code,codecl,fullname,phone,ice,address,email,whatsapp FROM clients WHERE trash='1'".$multicompanies." ORDER BY fullname");
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$clients = [];
	foreach ($rows as $row) {
		$clients[$row['id']] = [
			'id' => $row['id'],
			'code' => $row['code'],
			'codecl' => $row['codecl'],
			'fullname' => $row['fullname'],
			'phone' => $row['phone'],
			'ice' => $row['ice'],
			'address' => $row['address'],
			'email' => $row['email'],
			'whatsapp' => $row['whatsapp']
		];
	}
}

$back = $bdd->query("SELECT * FROM settings");
$settings = $back->fetch();
$back = $bdd->query("SELECT * FROM notifications");
$notifications = $back->fetch();

function chifre_en_lettre($montant, $devise1 = '', $devise2 = '') {
	global $settings;
    if (empty($devise1)) $dev1 = $settings['currency'];
    else $dev1 = $devise1;

    if (empty($devise2)) $dev2 = 'Centimes';
    else $dev2 = $devise2;

    $valeur_entiere = intval($montant);
    $valeur_decimal = round(($montant - $valeur_entiere) * 100);

    $chif = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
    $diz = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', '', 'quatre-vingt'];

    // Extract triplet digits
    $unit1 = $valeur_entiere % 10;
    $dix1 = intval($valeur_entiere % 100 / 10);
    $cent1 = intval($valeur_entiere % 1000 / 100);
    $unit2 = intval($valeur_entiere % 10000 / 1000);
    $dix2 = intval($valeur_entiere % 100000 / 10000);
    $cent2 = intval($valeur_entiere % 1000000 / 100000);
    $unit3 = intval($valeur_entiere % 10000000 / 1000000);
    $dix3 = intval($valeur_entiere % 100000000 / 10000000);
    $cent3 = intval($valeur_entiere % 1000000000 / 100000000);

    $mil = convert_triplet($cent3, $dix3, $unit3, $chif, $diz);
    $milion = ($mil != '') ? $mil . ' million' . (($mil != 'un') ? 's' : '') : '';
    $mille = convert_triplet($cent2, $dix2, $unit2, $chif, $diz);
	if ($mille != '') {
		// Si exactement 1000 → "mille"
		if (trim($mille) == 'un') {
			$mille_txt = "mille";
		} else {
			// Pour 2000, 3000… → "deux mille", "trois mille"
			$mille_txt = $mille . " mille";
		}
	} else {
		$mille_txt = '';
	}    $centaine = convert_triplet($cent1, $dix1, $unit1, $chif, $diz);

    // Check if it's a rounded million
    $is_rounded_million = (
        $valeur_entiere % 1000000 == 0 &&
        $milion != '' &&
        $mille_txt == '' &&
        $centaine == ''
    );

    $main = trim($milion . ' ' . $mille_txt . ' ' . $centaine);
    $main .= ' ' . ($is_rounded_million ? 'de ' : '') . $dev1;

    // Centimes
    $centimes = '';
    if ($valeur_decimal != 0) {
        $dix_c = intval($valeur_decimal / 10);
        $unit_c = $valeur_decimal % 10;

        if ($valeur_decimal < 10) {
            $centimes = 'zéro ' . $chif[$unit_c];
        } elseif ($dix_c == 1) {
            $centimes = $chif[10 + $unit_c];
        } elseif ($dix_c == 7) {
            if ($unit_c == 1) $centimes = "soixante et onze";
            else $centimes = "soixante-" . $chif[10 + $unit_c];
        } elseif ($dix_c == 9) {
            $centimes = "quatre-vingt-" . $chif[10 + $unit_c];
        } elseif ($dix_c != 0) {
            $centimes = $diz[$dix_c];
            if ($unit_c == 1) $centimes .= " et un";
            elseif ($unit_c > 1) $centimes .= "-" . $chif[$unit_c];
        }

        $main .= ' et ' . trim($centimes) . ' ' . $dev2;
    }

    echo ucwords(trim($main));
}

    function convert_triplet($cent, $dix, $unit, $chif, $diz) {
        $res = '';

		if ($cent != 0) {

			// Case 100 → "cent"
			if ($cent == 1) {
				$res .= "cent";
				// plural only if EXACT 100? No → French never pluralizes "cent" alone.
				if ($dix != 0 || $unit != 0) $res .= " ";
			}

			// Case 200, 300, … → deux cents, trois cents (plural)
			else {
				$res .= $chif[$cent] . " cent";
				// plural only when no tens/units
				$res .= ($dix == 0 && $unit == 0) ? "s " : " ";
			}
		}

        if ($dix == 1) {
            $res .= $chif[10 + $unit];
        } elseif ($dix == 7) {
            if ($unit == 1) $res .= "soixante et onze";
            else $res .= "soixante-" . $chif[10 + $unit];
        } elseif ($dix == 9) {
            $res .= "quatre-vingt-" . $chif[10 + $unit];
        } elseif ($dix != 0) {
            $res .= $diz[$dix];
            if ($unit == 1) $res .= " et un";
            elseif ($unit > 1) $res .= "-" . $chif[$unit];
        } elseif ($unit != 0) {
            $res .= $chif[$unit];
        }

        return trim($res);
    }
	
function simpleEncrypt($string, $key = 'my_secret_key') {
    $method = "AES-256-CBC";
    $iv = substr(hash('sha256', $key), 0, 16); 
    return base64_encode(openssl_encrypt($string, $method, $key, 0, $iv));
}

function simpleDecrypt($encrypted, $key = 'my_secret_key') {
    $method = "AES-256-CBC";
    $iv = substr(hash('sha256', $key), 0, 16); 
    return openssl_decrypt(base64_decode($encrypted), $method, $key, 0, $iv);
}
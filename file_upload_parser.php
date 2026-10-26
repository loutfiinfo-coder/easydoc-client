<?php
session_start();
include("config.php");
include('classes/Utilities.class.php');
include('classes/SimpleImage.class.php');
include('PHPExcel/IOFactory.php');

if(isset($_SESSION['easybm_id']) AND isset($_SESSION['easybm_fullname'])){
	if(isset($_FILES['file0'])){
		$file = Utilities::upload($_FILES['file0'],'uploads/');
		echo $file;	
	}
	elseif(isset($_FILES['file1'])){
		$file = Utilities::upload($_FILES['file1'],'uploads/');
		if($_POST['table'] == "products"){
			$req = $bdd->prepare("UPDATE products SET pictures=CONCAT(pictures,',','".$file."') WHERE id='".$_POST['id']."'");
		}
		elseif($_POST['table'] == "companies" AND $_POST['column'] == "logo1"){
			$back = $bdd->query("SELECT logo1 FROM companies WHERE id='".$_POST['id']."'");
			$row = $back->fetch();
			if(file_exists("uploads/".$row['logo1'])){
				unlink("uploads/".$row['logo1']);
			}
			if(file_exists("uploads/micro_".$row['logo1'])){
				unlink("uploads/micro_".$row['logo1']);
			}
			if(file_exists("uploads/cropped_".$row['logo1'])){
				unlink("uploads/cropped_".$row['logo1']);
			}
			if(file_exists("uploads/small_".$row['logo1'])){
				unlink("uploads/small_".$row['logo1']);
			}
			if(file_exists("uploads/large_".$row['logo1'])){
				unlink("uploads/large_".$row['logo1']);
			}
			$req = $bdd->prepare("UPDATE companies SET logo1='".$file."' WHERE id='".$_POST['id']."'");
		}
		elseif($_POST['table'] == "companies" AND $_POST['column'] == "signature"){
			$back = $bdd->query("SELECT signature FROM companies WHERE id='".$_POST['id']."'");
			$row = $back->fetch();
			if(file_exists("uploads/".$row['signature'])){
				unlink("uploads/".$row['signature']);
			}
			if(file_exists("uploads/micro_".$row['signature'])){
				unlink("uploads/micro_".$row['signature']);
			}
			if(file_exists("uploads/cropped_".$row['signature'])){
				unlink("uploads/cropped_".$row['signature']);
			}
			if(file_exists("uploads/small_".$row['signature'])){
				unlink("uploads/small_".$row['signature']);
			}
			if(file_exists("uploads/large_".$row['signature'])){
				unlink("uploads/large_".$row['signature']);
			}
			$req = $bdd->prepare("UPDATE companies SET signature='".$file."' WHERE id='".$_POST['id']."'");
		}
		elseif($_POST['table'] == "documents" AND $_POST['column'] == "picture"){
			$back = $bdd->query("SELECT picture FROM documents WHERE id='".$_POST['id']."'");
			$row = $back->fetch();
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
			$req = $bdd->prepare("UPDATE documents SET picture='".$file."' WHERE id='".$_POST['id']."'");
		}
		$req->execute();
	}
	elseif(isset($_FILES['file2'])){
		$file = Utilities::uploadFile($_FILES['file2'],'uploads/');
		$req = $bdd->prepare("UPDATE ".$_POST['table']." SET attachments=CONCAT(attachments,',','".$file."') WHERE id='".$_POST['id']."'");
		$req->execute();
	}
	elseif(isset($_FILES['file3'])){
		$inputFileName = 'uploads/'.Utilities::uploadFile($_FILES['file3'],'uploads/');
		//  Read your Excel workbook
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		} 
		catch(Exception $e) {
			// die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();
		//  Loop through each row of the worksheet in turn
		for ($row = 2; $row <= $highestRow; $row++){
			//  Read a row of data into an array	
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
			if($rowData[0][0] != ""){
				$rand = "CL".gmdate("ym")."-0001";
				$back1 = $bdd->query("SELECT code FROM clients ORDER BY id DESC LIMIT 0,1");
				if($back1->rowCount() > 0){
					$row1 = $back1->fetch();
					$rand = "CL".gmdate("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
				}
				$company = str_replace(";",",",($rowData[0][11]));
				$back1 = $bdd->query("SELECT id FROM companies WHERE id IN(".($company!=""?$company:0).")".$companiesid);
				if($back1->rowCount() > 0){
					$req = $bdd->prepare("INSERT INTO clients(id,code,codecl,ice,iff,rc,fullname,phone,whatsapp,address,email,delay,note,company,user,dateadd,trash) 
					VALUES ('0','".$rand."','".sanitize_vars($rowData[0][1])."','".sanitize_vars($rowData[0][2])."','".sanitize_vars($rowData[0][3])."','".sanitize_vars($rowData[0][4])."','".sanitize_vars($rowData[0][0])."','".sanitize_vars($rowData[0][5])."','".sanitize_vars($rowData[0][6])."','".sanitize_vars($rowData[0][7])."','".sanitize_vars($rowData[0][8])."','".addslashes($rowData[0][9])."','".addslashes($rowData[0][10])."','".$company."','".$_SESSION['easybm_id']."','".time()."','1')");
					$req->execute();
				}
			}
		}
		unlink($inputFileName);	
	}
	elseif(isset($_FILES['file4'])){
		echo "toto";
		$inputFileName = 'uploads/'.Utilities::uploadFile($_FILES['file4'],'uploads/');
		//  Read your Excel workbook
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		} 
		catch(Exception $e) {
			// die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();
		//  Loop through each row of the worksheet in turn
		for ($row = 2; $row <= $highestRow; $row++){
			//  Read a row of data into an array
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
			if($rowData[0][0] != ""){
				$back = $bdd->query("SELECT id FROM suppliers WHERE title='".sanitize_vars($rowData[0][0])."'");
				if($back->rowCount() == 0){
					$rand = "FR".date("ym")."-0001";
					$back1 = $bdd->query("SELECT code FROM suppliers WHERE code<>'' ORDER BY id DESC LIMIT 0,1");
					if($back1->rowCount() > 0){
						$row1 = $back1->fetch();
						$rand = "FR".date("ym")."-".sprintf('%04d',intval(substr($row1['code'],7))+1);
					}
					$company = str_replace(";",",",($rowData[0][13]));					
					$back1 = $bdd->query("SELECT id FROM companies WHERE id IN(".($company!=""?$company:0).")".$companiesid);
					if($back1->rowCount() > 0){
						$req = $bdd->prepare("INSERT INTO `suppliers`(`id`, `code`, `codefo`, `ice`, `iff`, `rc`, `title`, `respname`, `respphone`, `whatsapp`, `address`, `respemail`, `respfax`, `delay`, `note`, `company`, `user`, `dateadd`, `trash`)
						VALUES ('','".$rand."','".sanitize_vars($rowData[0][1])."','".sanitize_vars($rowData[0][2])."','".sanitize_vars($rowData[0][3])."','".sanitize_vars($rowData[0][4])."','".sanitize_vars($rowData[0][0])."','".sanitize_vars($rowData[0][5])."','".sanitize_vars($rowData[0][6])."','".sanitize_vars($rowData[0][7])."','".addslashes($rowData[0][8])."','".sanitize_vars($rowData[0][9])."','".sanitize_vars($rowData[0][10])."','".sanitize_vars($rowData[0][11])."','".sanitize_vars($rowData[0][12])."','".$company."','".$_SESSION['easybm_id']."','".time()."','1')");
						$req->execute();
					}
				}				
			}
		}
		unlink($inputFileName);	
	}
	elseif(isset($_FILES['file5'])){
		echo "toto";
		$inputFileName = 'uploads/'.Utilities::uploadFile($_FILES['file5'],'uploads/');
		//  Read your Excel workbook
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		} 
		catch(Exception $e) {
			// die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();
		//  Loop through each row of the worksheet in turn
		for ($row = 2; $row <= $highestRow; $row++){
			//  Read a row of data into an array
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
			if($rowData[0][1] != ""){
				$company = str_replace(";",",",($rowData[0][9]));
				$back1 = $bdd->query("SELECT id FROM companies WHERE id IN(".($company!=""?$company:0).")".$companiesid);
				if($back1->rowCount() > 0){
					$req = $bdd->prepare("INSERT INTO `prospects`(id,fullname,phone,whatsapp,email,gender,company1,source,labels,note,company,user,dateadd,dateupdate,trash)
					VALUES ('','".sanitize_vars($rowData[0][1])."','".sanitize_vars($rowData[0][3])."','".sanitize_vars($rowData[0][4])."','".sanitize_vars($rowData[0][5])."','".sanitize_vars($rowData[0][0])."','".sanitize_vars($rowData[0][2])."','".addslashes($rowData[0][6])."','".sanitize_vars($rowData[0][7])."','".sanitize_vars($rowData[0][8])."','".$company."','".$_SESSION['easybm_id']."','".time()."','".time()."','1')");
					$req->execute();
				}
			}
		}
		unlink($inputFileName);	
	}
	elseif (isset($_FILES['file6'])) {

		$inputFileName = 'uploads/' . Utilities::uploadFile($_FILES['file6'], 'uploads/');

		// ==================================================
		// READ EXCEL
		// ==================================================
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader     = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel   = $objReader->load($inputFileName);
		} catch (Exception $e) {
			die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
		}

		$sheet         = $objPHPExcel->getSheet(0);
		$highestRow    = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

		// ==================================================
		// LOAD VALID TVA VALUES
		// ==================================================
		$tvaValues = [];
		$q = $bdd->query("SELECT tva FROM tvas");
		while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
			$tvaValues[] = (float)$r['tva'];
		}

		// ==================================================
		// ACCUMULATOR
		// ==================================================
		$products = [];

		for ($row = 2; $row <= $highestRow; $row++) {

			$rowData = $sheet->rangeToArray(
				'A' . $row . ':' . $highestColumn . $row,
				NULL,
				TRUE,
				FALSE
			);

			if (trim($rowData[0][0]) === '') {
				continue;
			}

			$product = excelRichTextToHtml($sheet->getCell('A'.$row));
			$unit    = sanitize_vars($rowData[0][2]);

			$format  = $_POST['montantformat'];

			// RAW VALUES (DISPLAY)
			$rawQty   = sanitize_vars($rowData[0][1]);
			$rawPrice = $rowData[0][3];
			$rawBase  = strtolower(trim($rowData[0][4]));
			$rawTva   = sanitize_vars($rowData[0][5]);

			// ==============================================
			// VALIDATIONS
			// ==============================================
			$isQtyValid   = is_numeric($rawQty);
			$isPriceValid= is_numeric(parsePrice($rawPrice, $format));
			$isTvaValid  = is_numeric($rawTva) && in_array((float)$rawTva, $tvaValues, true);
			$isBaseValid = in_array($rawBase, ['ht', 'ttc'], true);

			// DATA VALUES (SAFE)
			$qty    = $isQtyValid ? (float)$rawQty : 0;
			$uprice = $isPriceValid ? (float)parsePrice($rawPrice, $format) : 0;
			$tva    = $isTvaValid ? (float)$rawTva : 0;

			// TTC → HT
			if ($rawBase === 'ttc' && $tva > 0 && $isPriceValid) {
				$uprice = $uprice / (1 + ($tva / 100));
			}

			$uprice = (float) number_format($uprice, 2, '.', '');

			// GROUP KEY
			$key = mb_strtolower($product);

			if (!isset($products[$key])) {

				$products[$key] = [
					'product'       => $product,
					'unit'          => $unit,

					// DISPLAY
					'display_qty'   => $rawQty,
					'display_price' => $rawPrice,
					'display_tva'   => $rawTva,

					// DATA
					'qty'           => $qty,
					'uprice'        => $uprice,
					'tva'           => $tva,

					// FLAGS
					'valid_qty'     => $isQtyValid,
					'valid_price'   => $isPriceValid,
					'valid_tva'     => $isTvaValid
				];

			} else {

				$products[$key]['qty'] += $qty;

				$products[$key]['valid_qty']   &= $isQtyValid;
				$products[$key]['valid_price'] &= $isPriceValid;
				$products[$key]['valid_tva']   &= $isTvaValid;
			}
		}

		// ==================================================
		// GENERATE TABLE
		// ==================================================
		$table = '';
		$errorStyle = 'style="background:#ffe6e6"';

		foreach ($products as $p) {

			$tprice = $p['qty'] * $p['uprice'];

			$table .= '<tr class="lx-otherrow"
				data-qty="'.$p['qty'].'"
				data-unit="'.$p['unit'].'"
				data-uprice="'.$p['uprice'].'"
				data-tva="'.$p['tva'].'"
				data-tprice="'.number_format($tprice,2,".","").'"
				data-ttprice="'.number_format($tprice,2,".","").'">';

			// PRODUCT (NEVER RED)
			$table .= '<td class="lx-product-title">
				<span><p>'.$p['product'].'</p></span>
			</td>';

			// QTY
			$table .= '<td '.(!$p['valid_qty'] ? $errorStyle : '').'>
				<span>'.$p['display_qty'].' '.$p['unit'].'</span>
			</td>';

			// PRICE
			$table .= '<td '.(!$p['valid_price'] ? $errorStyle : '').'>
				<span>'.number_format($p['uprice'],2,","," ").'</span>
			</td>';

			// DISCOUNT
			$table .= '<td class="lx-discount">
				<div class="lx-textfield">
					<label><input type="number" name="discount"></label>
				</div>
			</td>';

			// TVA
			$table .= '<td '.(!$p['valid_tva'] ? $errorStyle : '').'>
				<span>'.$p['display_tva'].'%</span>
			</td>';

			// TOTAL
			$table .= '<td>
				<span>'.number_format($tprice,2,","," ").'</span>
			</td>';

			// QTY BACK
			$table .= '<td class="lx-qty-back">
				<div class="lx-textfield">
					<label><input type="number" name="qtyback" min="1"></label>
				</div>
			</td>';

			// EDIT
			$table .= '<td class="lx-edit-cell">
				<a href="javascript:;" class="lx-edit-this"><i class="fa fa-edit"></i></a>
			</td>';

			// DELETE
			$table .= '<td class="lx-delete-cell">
				<a href="javascript:;" class="lx-delete lx-delete-payment">
					<i class="fa fa-trash-alt"></i>
				</a>
				<div class="lx-delete-payment-choice">
					<a href="javascript:;" class="lx-delete-this">Oui</a>
					<a href="javascript:;" class="lx-no-delete-payment">Non</a>
				</div>
			</td>';

			$table .= '</tr>';
		}

		echo $table;
		unlink($inputFileName);
	}
	elseif(isset($_FILES['file10'])){
		$file = Utilities::uploadFile($_FILES['file10'],'uploads/');
		echo $file;
	}
}

function sanitize_vars($var){
	return htmlspecialchars(addslashes(trim($var)));
}

function parsePrice($value, $format){
    $value = trim($value);

    switch ($format) {

        // Decimal = dot
        case '1001.01':
            break;

        case '1 001.01':
            $value = str_replace(' ', '', $value);
            break;

        case '1,001.01':
            $value = str_replace(',', '', $value);
            break;

        // Decimal = comma
        case '1001,01':
            $value = str_replace(',', '.', $value);
            break;

        case '1 001,01':
            $value = str_replace(' ', '', $value);
            $value = str_replace(',', '.', $value);
            break;

        case '1.001,01':
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
            break;
    }

    return (float)$value;
}

function excelRichTextToHtml(PHPExcel_Cell $cell)
{
	$value = $cell->getValue();

	// Simple text
	if (!$value instanceof PHPExcel_RichText) {
		return nl2br(htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'));
	}

	$html = '';

	foreach ($value->getRichTextElements() as $element) {

		$text = htmlspecialchars($element->getText(), ENT_QUOTES, 'UTF-8');

		if ($element instanceof PHPExcel_RichText_Run) {

			$font = $element->getFont();
			$styles = [];

			if ($font->getBold()) {
				$text = '<strong>'.$text.'</strong>';
			}
			if ($font->getItalic()) {
				$text = '<em>'.$text.'</em>';
			}
			if ($font->getUnderline() !== PHPExcel_Style_Font::UNDERLINE_NONE) {
				$text = '<u>'.$text.'</u>';
			}

			if ($font->getColor() && $font->getColor()->getRGB()) {
				$styles[] = 'color:#'.$font->getColor()->getRGB();
			}

			if (!empty($styles)) {
				$text = '<span style="'.implode(';', $styles).'">'.$text.'</span>';
			}
		}

		$html .= $text;
	}

	// Preserve new lines
	return nl2br($html);
}
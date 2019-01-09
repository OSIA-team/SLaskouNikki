<?php
/**
 * Created by PhpStorm.
 * User: krystofkosut
 * Date: 09.07.18
 * Time: 14:33
 */

if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    die('Bad Request Method');
}
if (!isset($_POST['send'])){
    die('Un-authorized access!');
}
// This is where $name and $price sets up
foreach ($_POST as $key => $value){
    $$key = $value;
}

$nazvy = [  'ebook' => 'Láska z Bali - Elektronická',
            'paper' => 'Láska z Bali - Pevná vazba'];

foreach($name as $key => $item){
    $items[$key]['nazev'] = $nazvy[$item];
    $items[$key]['price'] = $price[$key];
    $items[$key]['pocet'] = $pocet[$key];
}

function render_email() {
    ob_start();
    foreach ($_POST as $key => $value){
        $$key = $value;
    }
    global $cena_celkem;
    $adresa = $ulice.", ".$mesto.", ".$psc;
    $varSym = getVarNum();
    saveNextVarNum($varSym);
    include  'mail-template.phtml';
    return ob_get_contents();
}

function getVarNum($file ='files/varNum.txt' ){
    $varNum = file_get_contents($file);
    return $varNum;
}

function saveNextVarNum($varNum, $file = 'files/varNum.txt'){
    $nextNum = (int)$varNum;
    $nextNum++;
    $result = file_put_contents($file, $nextNum);
    if ($result == false){
        return false;
    } else {
        return true;
    }
}

require_once 'lib/TCPDF/tcpdf.php';
require_once 'lib/PHPMailer/PHPMailerAutoload.php';
// gen faktury
$cena_celkem    = 0;
$balne          = 69;
foreach($items as $item){
    if($item['pocet'] > 0){
        if($item['nazev'] == 'Láska z Bali - Pevná vazba'){
            $cena_celkem += $balne;
        }
    $cena_celkem += $item['price']*$item['pocet'];
    }
}
$varSym = getVarNum();
$kniha  = "Láska z Bali";

ob_start();
require_once 'temp/invoice/proforma.phtml';
$HTMLFaktura = ob_get_contents();
ob_clean();

// echo $HTMLFaktura; die();
//PDF
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf->AddPage();
$pdf->SetFont('freeserif', '', 11, '', true);
$pdf->writeHTML($HTMLFaktura, true, false, true, false, '');

$invoiceFile = 'proforma_invoice_'.getVarNum().'.pdf';
$invoicePath = __DIR__ .'/files/invoice/';
$pdf->Output($invoicePath.$invoiceFile, 'F');
// pdf saved, starting gen mail...
$mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 2;
        $mail->CharSet = 'UTF-8';
        $subject = "Objednávka knihy č.".getVarNum();
        $mail->Subject = $subject;
        $mail->setFrom('noreply@slaskouniki.cz', 'S láskou, Niki');
        $mail->addAddress($email, $jmeno." ".$prijmeni);
        // $mail->addBCC('info@slaskouniki.cz');
        $mail->addBCC('Nicol.Villimova@seznam.cz', 'Nicol');
        $mail->addBCC('info@slaskouniki.cz','Nicol');
        // $mail->addBCC('k.kosut@shockworks.cz','Tofa');

        $htmlBody = render_email();
        $mail->msgHTML($htmlBody);
        $mail->addAttachment($invoicePath.$invoiceFile, $invoiceFile);
        $result = $mail->send();
    } catch (phpmailerException $e) {
        echo $e->errorMessage(); //Pretty error messages from PHPMailer
    } catch (Exception $e) {
        echo $e->getMessage(); //Boring error messages from anything else!
    }

if($result != false){
    ob_get_clean();
    echo "
    <div style='display: inline; text-align: center;'>
   <div>
   <p>Potvrzení objednávky Vám bylo zasláno na Vámi uvedený e-mail</p>
<br>
   <p>Cena celkem: $cena_celkem,- Kč</p>
   <p>Přeji příjemné čtení,</p>
<p>S láskou, Niki</p>
    <img src='img/_20180716_064154.JPG' height='400px' align='middle' /></div>
    </div>";
} else {
    ob_get_clean();
    die('Email se nepodarilo odeslat!');
}
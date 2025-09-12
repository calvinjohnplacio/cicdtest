<?php
require('fpdf/fpdf.php');
require('fpdi2/src/autoload.php'); // FPDI autoloader

use setasign\Fpdi\Fpdi;

class PDF_Formatter extends Fpdi
{
    function Header()
    {
        $this->SetFont('Arial','',8);
        $this->SetXY(10, 8);
        $this->Cell(0, 5, 'College of Informatics and Computing Sciences', 0, 0, 'L');
        $this->SetXY(-80, 8);
        $this->Cell(70, 5, 'Journal of Engineering and Computing Sciences Vol. 6 No. 2 | 2022', 0, 0, 'R');
        $this->Line(10, 15, 200, 15);
    }

    function Footer()
    {
        $this->Line(10, 285, 200, 285);
        $this->SetFont('Arial','',8);
        $this->SetY(-18);
        $this->Cell(0, 5, 'Batangas State University The National Engineering University ARASOF-Nasugbu', 0, 1, 'C');
        $this->Cell(0, 5, $this->PageNo(), 0, 0, 'C');
    }

    function formatPDF($file, $outFile)
    {
        $pageCount = $this->setSourceFile($file);
        $topMargin = 20;    
        $bottomMargin = 25; 
        $usableHeight = 297 - $topMargin - $bottomMargin; 

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplIdx = $this->importPage($pageNo);
            $size = $this->getTemplateSize($tplIdx);
            $this->AddPage('P', 'A4');
            $scale = min(210 / $size['width'], $usableHeight / $size['height']);
            $w = $size['width'] * $scale;
            $h = $size['height'] * $scale;
            $x = (210 - $w) / 2;
            $y = $topMargin;
            $this->useTemplate($tplIdx, $x, $y, $w, $h);
        }
        $this->Output("F", $outFile);
    }
}

// === STEP 1: Format journalcontent.pdf ===
$pdf = new PDF_Formatter();
$pdf->formatPDF("journalcontent.pdf", "formatted1.pdf");

// === STEP 2: Merge journalprelim.pdf + formatted1.pdf ===
$final = new Fpdi();
$pageCount2 = $final->setSourceFile("journalprelim.pdf");
for ($i=1; $i <= $pageCount2; $i++) {
    $tplIdx = $final->importPage($i);
    $final->AddPage('P', 'A4');
    $final->useTemplate($tplIdx, 0, 0, 210, 297);
}
$pageCount1 = $final->setSourceFile("formatted1.pdf");
for ($i=1; $i <= $pageCount1; $i++) {
    $tplIdx = $final->importPage($i);
    $final->AddPage('P', 'A4');
    $final->useTemplate($tplIdx, 0, 0, 210, 297);
}
$final->Output("F", "final.pdf");

echo "✅ Done! Merged PDF saved as final.pdf<br>";

// === STEP 3: Upload to Heyzine ===
$pdfUrl = "https://kaneki.pro/hyflip/final.pdf"; // must be publicly accessible
$client_id = "5ecab4a349a16e37"; // replace with your Heyzine client_id

$apiUrl = "https://heyzine.com/api1?pdf=" . urlencode($pdfUrl) . "&k=" . $client_id;
$response = file_get_contents($apiUrl);
if ($response === FALSE) {
    die("❌ Error connecting to Heyzine API.");
}

$data = json_decode($response, true);

// === STEP 4: Embed Flipbook View ===
if (isset($data['success']) && $data['success'] == true) {
    $flipbookUrl = $data['url'];

    // Convert to "embed" mode
    $embedUrl = str_replace("heyzine.com/flip-book/", "heyzine.com/flip-book/embed/", $flipbookUrl);

    echo "✅ Flipbook created and embedded below:<br><br>";
    echo '<iframe src="' . $embedUrl . '" width="100%" height="600" frameborder="0" allowfullscreen></iframe>';
} else {
    echo "❌ Failed to create flipbook. Response: " . $response;
}

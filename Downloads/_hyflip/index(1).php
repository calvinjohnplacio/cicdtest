<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('fpdf/fpdf.php');
require('fpdi2/src/autoload.php');

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
        $this->Cell(0, 5, 'Batangas State University ARASOF-Nasugbu', 0, 1, 'C');
        $this->Cell(0, 5, $this->PageNo(), 0, 0, 'C');
    }

    function formatPDF($file)
    {
        try {
            $pageCount = $this->setSourceFile($file);
        } catch (\Exception $e) {
            die("Error loading PDF '$file': " . $e->getMessage());
        }

        $pages = [];
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplIdx = $this->importPage($i);
            $size = $this->getTemplateSize($tplIdx);
            $pages[] = ['tpl' => $tplIdx, 'size' => $size];
        }
        return $pages;
    }
}

// --- Database Connection ---
$mysqli = new mysqli("localhost", "u468804886_user", "Shintaro_8", "u468804886_todo");
if($mysqli->connect_errno) die("DB connection failed: ".$mysqli->connect_error);

$flipbookEmbed = "";
$finalPdfPath = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    // --- Prelim PDF ---
    if (!isset($_FILES['prelim']) || !$_FILES['prelim']['tmp_name']) die("Upload a Prelim PDF!");
    $prelimFileName = basename($_FILES['prelim']['name']);
    $prelimFilePath = $uploadDir . $prelimFileName;
    if (!move_uploaded_file($_FILES['prelim']['tmp_name'], $prelimFilePath)) die("Failed to move prelim file!");

    // --- Journals ---
    $journalFiles = $_FILES['journal'];
    $journalTitles = $_POST['journal_title'] ?? [];
    $journalTable = [];

    if (empty($journalFiles['tmp_name'])) die("Upload at least one journal PDF!");

    foreach ($journalFiles['tmp_name'] as $key => $tmpName) {
        if ($tmpName) {
            $name = basename($journalFiles['name'][$key]);
            $title = $journalTitles[$key] ?? pathinfo($name, PATHINFO_FILENAME);
            $dest = $uploadDir . $name;
            if (!move_uploaded_file($tmpName, $dest)) die("Failed to move journal '$name'!");

            // Page count
            $pdfTemp = new FPDI();
            try {
                $pageCount = $pdfTemp->setSourceFile($dest);
            } catch (\Exception $e) {
                die("Error reading PDF '$dest': ".$e->getMessage());
            }

            // Insert into DB
            $stmt = $mysqli->prepare("INSERT INTO journal_uploads (title, path, page_count) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $title, $dest, $pageCount);
            $stmt->execute();
            $journalTable[] = [
                'id' => $stmt->insert_id,
                'title' => $title,
                'path' => $dest,
                'page_count' => $pageCount
            ];
            $stmt->close();
        }
    }

    $pdfFormatter = new PDF_Formatter();
    $final = new FPDI();

    // --- Add prelim pages ---
    $prelimPages = $pdfFormatter->formatPDF($prelimFilePath);
    foreach ($prelimPages as $page) {
        $final->AddPage('P','A4');
        $final->useTemplate($page['tpl'],0,0,210,297);
    }

    // --- Generate TOC ---
    $tocFile = $uploadDir . 'toc.pdf';
    $toc = new PDF_Formatter();
    $toc->AddPage();
    $toc->SetFont('Arial','B',14);
    $toc->Cell(0,10,'Table of Contents',0,1,'C');
    $toc->Ln(5);
    $toc->SetFont('Arial','',12);

    $pageNumber = count($prelimPages) + 1;

    foreach ($journalTable as $journal) {
        $stmt = $mysqli->prepare("UPDATE journal_uploads SET start_page=? WHERE id=?");
        $stmt->bind_param("ii", $pageNumber, $journal['id']);
        $stmt->execute();
        $stmt->close();

        for ($i = 1; $i <= $journal['page_count']; $i++) {
            $toc->Cell(0,8,"{$journal['title']} - Page $i ........................................ Page $pageNumber",0,1,'L');
            $pageNumber++;
        }
    }
    $toc->Output('F', $tocFile);

    // --- Merge final PDF ---
    $allFiles = array_merge([$prelimFilePath, $tocFile], array_column($journalTable, 'path'));
    foreach ($allFiles as $file) {
        $pages = $pdfFormatter->formatPDF($file);
        foreach ($pages as $page) {
            $final->AddPage('P','A4');
            $final->useTemplate($page['tpl'],0,0,210,297);
        }
    }

    $finalFileName = "final.pdf";
    $finalPdfPath = $uploadDir . $finalFileName;
    $final->Output('F', $finalPdfPath);

    echo "<h3>Final PDF:</h3><p>$finalPdfPath</p>";
}
?>

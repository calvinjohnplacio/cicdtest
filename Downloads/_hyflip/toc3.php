<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('fpdf/fpdf.php');
require('fpdi2/src/autoload.php');
use setasign\Fpdi\Fpdi;

// === DB connection ===
$conn = new mysqli("localhost", "u468804886_user", "Shintaro_8", "u468804886_todo");
if ($conn->connect_error) {
    die("❌ DB Connection failed: " . $conn->connect_error);
}

// === PDF Formatter ===
class PDF_Formatter extends Fpdi {
    public $section = "prelim";   
    public $journalPageNo = 0;

    // === Header (only for TOC + Journal) ===
    function Header() {
        if ($this->section == "toc" || $this->section == "journal") {
            $this->SetFont('Arial','',8);
            $this->SetXY(10, 8);
            $this->Cell(0, 5, 'College of Informatics and Computing Sciences', 0, 0, 'L');
            $this->SetXY(-80, 8);
            $this->Cell(70, 5, 'Journal of Engineering and Computing Sciences Vol. 6 No. 2 | 2022', 0, 0, 'R');
            $this->Line(10, 15, 200, 15);
        }
    }

    // === Footer (only for TOC + Journal) ===
    function Footer() {
        if ($this->section == "toc" || $this->section == "journal") {
            $this->SetFont('Arial', '', 7);
            $pageHeight = $this->GetPageHeight();

            // Line slightly above bottom
            $yLine = $pageHeight - 15;
            $this->Line(10, $yLine, 200, $yLine);

            // University text
            $this->SetY($yLine + 1.5);
            $this->Cell(0, 4, 'Batangas State University The National Engineering University ARASOF-Nasugbu', 0, 1, 'C');

            // Page number (journal only)
            if ($this->section == "journal" && $this->journalPageNo > 0) {
                $this->SetY($yLine + 6);
                $this->Cell(0, 4, $this->journalPageNo, 0, 0, 'C');
            }
        }
    }

    function AddJournalPage() {
        $this->AddPage();
        if ($this->section == "journal") {
            $this->journalPageNo++;
        }
    }

    function StartJournalNumbering() {
        $this->journalPageNo = 0; 
        $this->section = "journal";
    }

    // === TOC entry with wrapping + dotted leaders ===
    function TOCEntry($title, $pageRange, $lineHeight = 8) {
        $this->SetFont('Arial','',11);

        $leftMarginX  = 20;
        $rightMarginX = 190;
        $maxWidth     = $rightMarginX - $leftMarginX;

        // Wrap long titles
        $titleLines = $this->WordWrap($title, $maxWidth - 25);
        foreach ($titleLines as $i => $line) {
            $this->SetX($leftMarginX);

            if ($i == count($titleLines) - 1) {
                // Last line with dots + page range
                $titleWidth = $this->GetStringWidth($line);
                $pageWidth  = $this->GetStringWidth($pageRange);
                $dotWidth   = $this->GetStringWidth('.');

                $available = $maxWidth - $titleWidth - $pageWidth - 6;
                $dotCount  = max(3, floor($available / $dotWidth));

                $this->Cell($titleWidth + 2, $lineHeight, $line, 0, 0, 'L');
                $this->Cell($available, $lineHeight, str_repeat('.', $dotCount), 0, 0, 'C');
                $this->Cell($pageWidth + 2, $lineHeight, $pageRange, 0, 1, 'R');
            } else {
                // Normal wrapped line
                $this->Cell(0, $lineHeight, $line, 0, 1, 'L');
            }
        }

        $this->Ln(2);
    }

    // === Helper: Word wrap for TOC ===
    function WordWrap($text, $maxWidth) {
        $lines = [];
        $words = explode(' ', $text);
        $line = '';

        foreach ($words as $word) {
            $testLine = $line ? $line . ' ' . $word : $word;
            if ($this->GetStringWidth($testLine) <= $maxWidth) {
                $line = $testLine;
            } else {
                if ($line) $lines[] = $line;
                $line = $word;
            }
        }
        if ($line) $lines[] = $line;
        return $lines;
    }
}

// === Handle Upload ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['pdf_files']) && isset($_POST['flipbook_name'])) {
    $uploadDir = "upload/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $flipbookName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $_POST['flipbook_name']); 
    $finalPdfPath = $uploadDir . $flipbookName . ".pdf";

    $totalFiles = count($_FILES['pdf_files']['name']);
    $uploadedFiles = [];

    // === Upload + DB Save ===
    for ($i = 0; $i < $totalFiles; $i++) {
        $fileName = basename($_FILES['pdf_files']['name'][$i]);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['pdf_files']['tmp_name'][$i], $filePath)) {
            if (!file_exists($filePath) || filesize($filePath) === 0) {
                die("❌ File is invalid or empty: $fileName");
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $filePath);
            finfo_close($finfo);
            if ($mime !== 'application/pdf') die("❌ Invalid file type ($mime) for $fileName.");

            try {
                $pdf = new Fpdi();
                $pageCount = $pdf->setSourceFile($filePath);
            } catch (Exception $e) {
                die("❌ FPDI failed to read $fileName: " . $e->getMessage());
            }

            $stmt = $conn->prepare("INSERT INTO pdf_files (pdfname, pagenumber, path) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $fileName, $pageCount, $filePath);
            $stmt->execute();
            $stmt->close();

            $uploadedFiles[] = [
                'name' => $fileName,
                'path' => $filePath,
                'pages' => $pageCount
            ];
        }
    }

    if (count($uploadedFiles) > 0) {
        // === Generate TOC ===
        $toc = new PDF_Formatter();
        $toc->section = "toc";
        $toc->AddPage();
        $toc->SetFont('Arial','B',14);
        $toc->SetY(30);
        $toc->Cell(0, 10, "Table of Contents", 0, 1, 'C');
        $toc->Ln(6);

        $currentPageStart = 1; 
        foreach ($uploadedFiles as $file) {
            $currentPageEnd = $currentPageStart + $file['pages'] - 1;
            if ($toc->GetY() > 270) $toc->AddPage();
            $entryTitle = pathinfo($file['name'], PATHINFO_FILENAME);
            $pageRange = ($currentPageStart == $currentPageEnd) ? "$currentPageStart" : "$currentPageStart - $currentPageEnd";
            $toc->TOCEntry($entryTitle, $pageRange, 8);
            $currentPageStart = $currentPageEnd + 1;
        }
        $toc->Output("F", $uploadDir . "toc.pdf");

        // === Merge Final PDF ===
        $final = new PDF_Formatter();
        $final->section = "prelim"; // prelim = no header/footer

        $prelimFile = "prlm.pdf";
        if (file_exists($prelimFile)) {
            $pages = $final->setSourceFile($prelimFile);
            for ($i=1; $i <= $pages; $i++) {
                $tplIdx = $final->importPage($i);
                $final->AddPage();
                $final->useTemplate($tplIdx, 0, 0, 210, 297);
            }
        }

        // TOC section
        $final->section = "toc";
        $pages = $final->setSourceFile($uploadDir . "toc.pdf");
        for ($i=1; $i <= $pages; $i++) {
            $tplIdx = $final->importPage($i);
            $final->AddPage();
            $final->useTemplate($tplIdx, 0, 0, 210, 297);
        }

        // Journal section
        $final->StartJournalNumbering();
        foreach ($uploadedFiles as $file) {
            $pages = $final->setSourceFile($file['path']);
            for ($i=1; $i <= $pages; $i++) {
                $tplIdx = $final->importPage($i);
                $size = $final->getTemplateSize($tplIdx);

                $pageWidth = 210;  
                $pageHeight = 297; 

                $final->AddJournalPage();

                if ($final->journalPageNo == 1) {
                    $final->useTemplate($tplIdx, 0, 0, $pageWidth, $pageHeight);
                } else {
                    $ratio = min($pageWidth / $size['width'], $pageHeight / $size['height']);
                    $newWidth  = $size['width'] * $ratio;
                    $newHeight = $size['height'] * $ratio;
                    $x = ($pageWidth - $newWidth) / 2;
                    $y = ($pageHeight - $newHeight) / 2;
                    $final->useTemplate($tplIdx, $x, $y, $newWidth, $newHeight);
                }
            }
        }

        $final->Output("F", $finalPdfPath);

        // === Heyzine API ===
        $pdfUrl = "https://kaneki.pro/hyflip/" . $finalPdfPath; 
        $client_id = "5ecab4a349a16e37"; 
        $apiUrl = "https://heyzine.com/api1/rest";

        $payload = [
            'pdf' => $pdfUrl,
            'client_id' => $client_id,
            'title' => $flipbookName,
            'sound' => true
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200 && $response !== false) {
            $data = json_decode($response, true);

            if (isset($data['url'])) {
                $flipbookUrl = $data['url'];

                $stmt = $conn->prepare("INSERT INTO flipbooks (name, pdf_path, embed_url) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $flipbookName, $finalPdfPath, $flipbookUrl);
                $stmt->execute();
                $lastId = $stmt->insert_id;
                $stmt->close();

                header("Location: view.php?id=" . $lastId);
                exit;
            } else {
                die("❌ Heyzine API did not return a flipbook URL: " . htmlspecialchars($response));
            }
        } else {
            die("❌ Heyzine REST API failed. HTTP Code: $httpCode Response: " . htmlspecialchars($response));
        }
    }
}
?>

<!-- === Upload Form === -->
<h2>📚 Upload PDFs to Create Flipbook</h2>
<form action="toc3.php" method="post" enctype="multipart/form-data">
    <label>Flipbook Name:</label><br>
    <input type="text" name="flipbook_name" required><br><br>
    <label>Select PDFs:</label><br>
    <input type="file" name="pdf_files[]" multiple required><br><br>
    <button type="submit">Upload & Merge</button>
</form>

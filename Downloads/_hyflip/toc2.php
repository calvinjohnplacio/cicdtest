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
function Footer() {
    if ($this->section == "toc" || $this->section == "journal") {
        // Draw horizontal line at the bottom
        $this->Line(10, 285, 200, 285);

        // Set font
        $this->SetFont('Arial', '', 8);

        // University text centered below the line
        $this->SetY(-15);
        $this->Cell(0, 5, 'Batangas State University The National Engineering University ARASOF-Nasugbu', 0, 1, 'C');

        // Journal page number (bottom right)
        if ($this->section == "journal" && $this->journalPageNo > 0) {
            $this->SetY(-15);  
            $this->Cell(0, 5, $this->journalPageNo, 0, 0, 'R');
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

    function TOCEntry($title, $pageRange, $lineHeight = 8) {
        $this->SetFont('Arial','',11);
        $maxWidth = 190;

        $titleWidth = $this->GetStringWidth($title);
        $pageWidth  = $this->GetStringWidth($pageRange);
        $dotWidth   = $this->GetStringWidth('.');

        $available = $maxWidth - ($titleWidth + $pageWidth + 4);
        $dotCount  = ($dotWidth > 0) ? floor($available / $dotWidth) : 0;

        $this->Cell($titleWidth + 2, $lineHeight, $title, 0, 0, 'L');
        $this->Cell($available, $lineHeight, str_repeat('.', $dotCount), 0, 0, 'C');
        $this->Cell($pageWidth + 2, $lineHeight, $pageRange, 0, 1, 'R');
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
        // === TOC generation ===
        $toc = new PDF_Formatter();
        $toc->section = "toc";
        $toc->AddPage();
        $toc->SetFont('Arial','B',14);
        $title = "Table of Contents";
        $toc->SetY(30);
        $toc->SetX(($toc->GetPageWidth() - $toc->GetStringWidth($title)) / 2);
        $toc->Cell($toc->GetStringWidth($title), 10, $title, 0, 1, 'C');
        $toc->Ln(10);

        $currentPageStart = 1; 
        foreach ($uploadedFiles as $file) {
            $currentPageEnd = $currentPageStart + $file['pages'] - 1;
            if ($toc->GetY() > 270) $toc->AddPage();
            $title = pathinfo($file['name'], PATHINFO_FILENAME);
            $pageRange = ($currentPageStart == $currentPageEnd) ? "$currentPageStart" : "$currentPageStart - $currentPageEnd";
            $toc->TOCEntry($title, $pageRange, 8);
            $currentPageStart = $currentPageEnd + 1;
        }
        $toc->Output("F", $uploadDir . "toc.pdf");

        // === Merge final ===
        $final = new PDF_Formatter();
        $final->section = "prelim"; // prelim (no header/footer)

        $prelimFile = "prlm.pdf";
        if (file_exists($prelimFile)) {
            $pages = $final->setSourceFile($prelimFile);
            for ($i=1; $i <= $pages; $i++) {
                $tplIdx = $final->importPage($i);
                $final->AddPage();
                // force full A4 stretch for prelim pages
                $final->useTemplate($tplIdx, 0, 0, 210, 297);
            }
        }

        // TOC section → footer starts here
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

                if ($final->journalPageNo == 0) {
                    // First journal page → stretch full A4
                    $final->AddJournalPage();
                    $final->useTemplate($tplIdx, 0, 0, $pageWidth, $pageHeight);
                } else {
                    // Other pages → proportional fit
                    $final->AddJournalPage();
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
<form action="toc2.php" method="post" enctype="multipart/form-data">
    <label>Flipbook Name:</label><br>
    <input type="text" name="flipbook_name" required><br><br>
    <label>Select PDFs:</label><br>
    <input type="file" name="pdf_files[]" multiple required><br><br>
    <button type="submit">Upload & Merge</button>
</form>

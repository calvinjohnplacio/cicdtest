
<?php
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
    public $section = "prelim";   // prelim, toc, journal
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
            $this->Line(10, 285, 200, 285);
            $this->SetFont('Arial','',8);
            $this->SetY(-18);
            $this->Cell(0, 5, 'Batangas State University The National Engineering University ARASOF-Nasugbu', 0, 1, 'C');

            if ($this->section == "journal" && $this->journalPageNo > 0) {
                $this->Cell(0, 5, $this->journalPageNo, 0, 0, 'C');
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

// === Handle Multi Upload ===
if (isset($_FILES['pdf_files'])) {
    $uploadDir = "upload/";
    $totalFiles = count($_FILES['pdf_files']['name']);
    $uploadedFiles = [];

    for ($i = 0; $i < $totalFiles; $i++) {
        $fileName = basename($_FILES['pdf_files']['name'][$i]);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['pdf_files']['tmp_name'][$i], $filePath)) {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($filePath);

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
        // === STEP 1: Generate TOC ===
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
        $lineHeight = 8; 

        foreach ($uploadedFiles as $file) {
            $currentPageEnd = $currentPageStart + $file['pages'] - 1;
            if ($toc->GetY() > 270) {
                $toc->AddPage();
            }
            $title = pathinfo($file['name'], PATHINFO_FILENAME);
            $pageRange = ($currentPageStart == $currentPageEnd) 
                        ? "$currentPageStart"
                        : "$currentPageStart - $currentPageEnd";
            $toc->TOCEntry($title, $pageRange, $lineHeight);
            $currentPageStart = $currentPageEnd + 1;
        }

        $toc->Output("F", "toc.pdf");

        // === STEP 2: Merge Prelim + TOC + Journals ===
        $final = new PDF_Formatter();
        $final->section = "prelim";
        $prelimFile = "journalprelim.pdf";
        if (file_exists($prelimFile)) {
            $pages = $final->setSourceFile($prelimFile);
            for ($i=1; $i <= $pages; $i++) {
                $tplIdx = $final->importPage($i);
                $final->AddPage();
                $final->useTemplate($tplIdx, 0, 0, 210, 297);
            }
        }

        $final->section = "toc";
        $pages = $final->setSourceFile("toc.pdf");
        for ($i=1; $i <= $pages; $i++) {
            $tplIdx = $final->importPage($i);
            $final->AddPage();
            $final->useTemplate($tplIdx, 0, 0, 210, 297);
        }

        $final->StartJournalNumbering();
        foreach ($uploadedFiles as $file) {
            $pages = $final->setSourceFile($file['path']);
            for ($i=1; $i <= $pages; $i++) {
                $tplIdx = $final->importPage($i);
                $final->AddJournalPage();
                $final->useTemplate($tplIdx, 0, 0, 210, 297);
            }
        }

        $finalPath = "fs.pdf";
        $final->Output("F", $finalPath);

        // === STEP 3: Send to Heyzine API ===
        // === STEP 3: Send to Heyzine API ===
$pdfUrl = "https://kaneki.pro/hyflip/f.pdf"; // must be publicly accessible
$client_id = "5ecab4a349a16e37"; // replace with your Heyzine client_id

// Added "&sound=1" to enable flip sound at creation
$apiUrl = "https://heyzine.com/api1?pdf=" . urlencode($pdfUrl) . "&k=" . $client_id . "&sound=1";
$response = file_get_contents($apiUrl);

if ($response === FALSE) {
    die("❌ Error connecting to Heyzine API.");
}

$data = json_decode($response, true);

if (isset($data['success']) && $data['success'] == true) {
    $flipbookUrl = $data['url'];
    $embedUrl = str_replace("heyzine.com/flip-book/", "heyzine.com/flip-book/embed/", $flipbookUrl);

    // 🔊 Force sound ON by default
    $embedUrl .= (strpos($embedUrl, '?') === false ? '?' : '&') . "sound=true";

    echo "✅ Flipbook created with sound ON and embedded below:<br><br>";
    echo '<iframe src="' . $embedUrl . '" width="100%" height="600" frameborder="0" allowfullscreen></iframe>';
} else {
    echo "❌ Failed to create flipbook. Response: " . $response;
}

    }
}
?>

<!-- === Multi Upload Form === -->
<form action="toc.php" method="post" enctype="multipart/form-data">
    <label>Select PDFs:</label>
    <input type="file" name="pdf_files[]" multiple required>
    <button type="submit">Upload & Merge</button>
</form>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// === DB connection ===
$conn = new mysqli("localhost", "u468804886_user", "Shintaro_8", "u468804886_todo");
if ($conn->connect_error) {
    die("❌ DB Connection failed: " . $conn->connect_error);
}

// === Fetch All Flipbooks ===
$result = $conn->query("SELECT id, name, embed_url, pdf_path FROM flipbooks");
$flipbooks = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // === Generate thumbnail for first page ===
        $pdfPath = $row['pdf_path'];
        $pdfFilename = pathinfo($pdfPath, PATHINFO_FILENAME);
        $thumbnailPath = "thumbnails/{$pdfFilename}.jpg";

        if (!file_exists($thumbnailPath) && file_exists($pdfPath)) {
            try {
                $imagick = new Imagick();
                $imagick->setResolution(150, 150);
                $imagick->readImage("{$pdfPath}[0]"); // first page only
                $imagick->setImageFormat("jpeg");
                $imagick->writeImage($thumbnailPath);
                $imagick->clear();
                $imagick->destroy();
            } catch (Exception $e) {
                error_log("❌ Error creating thumbnail for {$pdfPath}: " . $e->getMessage());
                $thumbnailPath = ''; // fallback
            }
        }

        $row['thumbnail'] = file_exists($thumbnailPath) ? $thumbnailPath : '';
        $flipbooks[] = $row;
    }
    $result->free();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📚 All Flipbooks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 40px;
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
        }

        .grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            padding: 25px;
            width: 350px;
            text-align: center;
        }

        .card img {
            max-width: 100%;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .card h2 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 500;
            color: #444;
        }

        .view-btn {
            margin-top: 15px;
            padding: 10px 18px;
            background: #0073e6;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .view-btn:hover {
            background: #005bb5;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal {
            background: #fff;
            width: 100%;
            height: 100%;
            border-radius: 0;
            position: relative;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        .modal iframe {
            width: 100%;
            height: 100%;
            flex-grow: 1;
            border: none;
        }

        .modal .actions {
            padding: 10px 20px;
            background: #f5f5f5;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal .actions a {
            padding: 8px 12px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .modal .actions a:hover {
            background: #218838;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            background: #e74c3c;
            color: white;
            border: none;
            font-size: 18px;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            z-index: 10000;
        }

        .close-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>

<h1>📚 All Flipbooks</h1>

<div class="grid">
    <?php foreach ($flipbooks as $flipbook): ?>
        <div class="card">
            <?php if (!empty($flipbook['thumbnail'])): ?>
                <img src="<?php echo htmlspecialchars($flipbook['thumbnail']); ?>" alt="Cover Image">
            <?php endif; ?>
            <h2><?php echo htmlspecialchars($flipbook['name']); ?></h2>
            <button class="view-btn" onclick="openModal('<?php echo htmlspecialchars($flipbook['embed_url']); ?>', '<?php echo htmlspecialchars($flipbook['pdf_path']); ?>')">👁️ View Flipbook</button>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal Overlay -->
<div class="modal-overlay" id="flipbookModal">
    <div class="modal">
        <button class="close-btn" onclick="closeModal()">❌ Close</button>
        <iframe id="modalIframe" src="" allowfullscreen></iframe>
        <div class="actions">
            <a href="#" id="modalViewLink" target="_blank">🔗 Open in New Tab</a>
            <a href="#" id="modalDownloadLink" target="_blank">⬇️ Download PDF</a>
        </div>
    </div>
</div>

<script>
    function openModal(embedUrl, pdfPath) {
        document.getElementById('modalIframe').src = embedUrl;
        document.getElementById('modalViewLink').href = embedUrl;
        document.getElementById('modalDownloadLink').href = pdfPath;
        document.getElementById('flipbookModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('flipbookModal').style.display = 'none';
        document.getElementById('modalIframe').src = '';
        document.body.style.overflow = 'auto';
    }
</script>

</body>
</html>

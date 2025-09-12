<?php
$conn = new mysqli("localhost", "u468804886_user", "Shintaro_8", "u468804886_todo");
if ($conn->connect_error) {
    die("❌ DB Connection failed: " . $conn->connect_error);
}
$result = $conn->query("SELECT * FROM flipbooks ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Flipbooks</title>
    <style>
        .flipbook-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .flipbook {
            width: 48%;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        iframe {
            width: 100%;
            height: 400px;
            border: none;
        }
    </style>
</head>
<body>
    <h1>Generated Flipbooks</h1>
    <div class="flipbook-container">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="flipbook">
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <iframe src="<?php echo htmlspecialchars($row['embed_url']); ?>" allowfullscreen></iframe>
                <p><a href="<?php echo htmlspecialchars($row['pdf_path']); ?>" target="_blank">Download PDF</a></p>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>

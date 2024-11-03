<?php

function uploadFile($file)
{
    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error.'];
    }

    // Allowed image MIME types
    $allowedMimeTypes = ['image/jpeg', 'image/png'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    // Check if file type is allowed
    if (!in_array($mimeType, $allowedMimeTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.'];
    }

    $uploadDir = __DIR__ . '/file';

    // Create directory structure year/month/day
    $year = date('Y');
    $month = date('m');
    $day = date('d');

    $yearDir = $uploadDir . '/' . $year;
    $monthDir = $yearDir . '/' . $month;
    $dayDir = $monthDir . '/' . $day;

    // Check and create directories if they do not exist
    if (!is_dir($yearDir)) {
        mkdir($yearDir, 0777, true);
    }

    if (!is_dir($monthDir)) {
        mkdir($monthDir, 0777, true);
    }

    if (!is_dir($dayDir)) {
        mkdir($dayDir, 0777, true);
    }

    // Generate a 7-character alphanumeric file name
    $fileName = bin2hex(random_bytes(4));
    $fileName = substr($fileName, 0, 7);

    // Get file extension only if it's allowed
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension);

    // Full path for file upload
    $fullPath = $dayDir . '/' . $fileName . '.' . $extension;

    // Move uploaded file to final destination
    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        // Construct the URL to access the file
        $fileUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/file/' . $year . '/' . $month . '/' . $day . '/' . $fileName . '.' . $extension;
        
        return [
            'success' => true,
            'url' => $fileUrl
        ];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file.'];
    }
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $result = uploadFile($_FILES['file']);
    echo json_encode($result);
}

?>

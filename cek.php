<?php
$filename = $_GET['filename'];

// Define the base directory
$baseDir = 'file/';

// Initialize a flag to indicate if the file is found
$fileFound = false;

// Extract the file extension and base name
$fileInfo = pathinfo($filename);
$baseName = $fileInfo['filename']; // Get the name without the extension
$extension = strtolower($fileInfo['extension']); // Get the extension and convert to lowercase

// Validate the base name to ensure it is exactly 7 alphanumeric characters
if (!preg_match('/^[a-zA-Z0-9]{7}$/', $baseName)) {
    header("HTTP/1.0 400 Bad Request");
    echo "Invalid filename oi";
    exit;
}

// Create an array of allowed file extensions
$allowedExtensions = ['jpg', 'jpeg', 'png'];

// Check if the extension is allowed
if (!in_array($extension, $allowedExtensions)) {
    header("HTTP/1.0 400 Bad Request");
    echo "Invalid file extension oi";
    exit;
}

// Use recursive directory iterator to search for the file
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir)
);

// Loop through the files
foreach ($iterator as $file) {
    if ($file->isFile()) {
        // Check if the filename matches and if the extension is allowed
        if ($file->getFilename() === $filename) {
            // File found
            $fileFound = true;
            header('Content-Type: image/' . $extension); // Set the correct content type
            readfile($file->getPathname()); // Serve the file
            exit;
        }
    }
}

// If the file was not found, return a 404 response
if (!$fileFound) {
    header("HTTP/1.0 404 Not Found");
    echo "ndak ketemu";
}
?>
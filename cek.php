<?php
// Get the request URI, e.g., "/b6c46df.png"
$requestUri = $_SERVER['REQUEST_URI'];

// Extract the filename and extension, e.g., "b6c46df" and "png" from "/b6c46df.png"
$idWithExtension = basename($requestUri);
$id = pathinfo($idWithExtension, PATHINFO_FILENAME);
$extension = strtolower(pathinfo($idWithExtension, PATHINFO_EXTENSION));

// Validate ID and extension (allow alphanumeric for ID, specific extensions)
if (!$id || !preg_match('/^[a-zA-Z0-9]+$/', $id) || !in_array($extension, ['jpg', 'jpeg', 'png'])) {
    http_response_code(400);
    echo "Invalid request.";
    exit;
}

// Dynamically calculate the file path based on today's date
$year = date('Y');
$month = date('m');
$day = date('d');

// Construct the dynamic file path
$filePath = __DIR__ . "/file/$year/$month/$day/$id.$extension";

// Check if the file exists
if (file_exists($filePath)) {
    // Get the MIME type of the file
    $mimeType = mime_content_type($filePath);

    // Send appropriate headers
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($filePath));

    // Output the file content
    readfile($filePath);
    exit;
} else {
    // File not found
    http_response_code(404);
    echo "File not found.";
}
// Cek apakah URL sesuai dengan pola "/filename.ext" di mana ext adalah jpg, jpeg, atau png
// if (preg_match('#^/([\w\d]+)\.(jpg|jpeg|png)$#i', $requestUri, $matches)) {
//     // Ambil nama file dan ekstensi dari hasil regex
//     $filename = $matches[1];
//     $extension = $matches[2];

//     // Tentukan path dinamis untuk file (misalnya, menggunakan tanggal hari ini)
//     $year = date("Y");
//     $month = date("m");
//     $day = date("d");
//     $originalPath = "file/$year/$month/$day/" . $filename . '.' . $extension;

//     // Cek apakah file ada di path yang ditentukan
//     if (file_exists(__DIR__ . $originalPath)) {
//         // Tentukan header Content-Type sesuai ekstensi
//         switch ($extension) {
//             case 'jpg':
//             case 'jpeg':
//                 header('Content-Type: image/jpeg');
//                 break;
//             case 'png':
//                 header('Content-Type: image/png');
//                 break;
//         }
//         // Kirim konten file
//         $img_info = getimagesize($originalPath);
//         header('Content-type: ' . $img_info['mime']);
//         readfile(__DIR__ . $originalPath);
//         exit;
//     } else {
//         // Jika file tidak ditemukan, tampilkan 404
//         http_response_code(404);
//         echo "File not found";
//         exit;
//     }
// }


// Jika URL tidak sesuai pola, tampilkan pesan default atau halaman 404
// http_response_code(404);
// echo "Invalid URL";
?>
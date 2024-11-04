<?php
// Get the request URI, e.g., "/b6c46df.png"

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
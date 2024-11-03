<!DOCTYPE html>
<?php
// Ambil path dari URL yang diminta
$requestUri = $_SERVER['REQUEST_URI'];

// Cek apakah URL sesuai dengan pola "/filename.ext" di mana ext adalah jpg, jpeg, atau png
if (preg_match('#^/([\w\d]+)\.(jpg|jpeg|png)$#i', $requestUri, $matches)) {
    // Ambil nama file dan ekstensi dari hasil regex
    $filename = $matches[1];
    $extension = $matches[2];

    // Tentukan path dinamis untuk file (misalnya, menggunakan tanggal hari ini)
    $year = date("Y");
    $month = date("m");
    $day = date("d");
    $originalPath = "/file/$year/$month/$day/" . $filename . '.' . $extension;

    // Cek apakah file ada di path yang ditentukan
    if (file_exists(__DIR__ . $originalPath)) {
        // Tentukan header Content-Type sesuai ekstensi
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                header('Content-Type: image/jpeg');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
        }
        // Kirim konten file
        readfile(__DIR__ . $originalPath);
        exit;
    } else {
        // Jika file tidak ditemukan, tampilkan 404
        http_response_code(404);
        echo "File not found";
        exit;
    }
}

// Jika URL tidak sesuai pola, tampilkan pesan default atau halaman 404
http_response_code(404);
echo "Invalid URL";
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload with Theme Switch</title>
    <link href="/public/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 dark:bg-gray-800 flex justify-center h-screen transition-colors duration-300 ">
    <!-- Theme Toggle Icon in the top right corner -->
    <button id="theme-toggle" class="absolute top-2 right-2 text-indigo-600 dark:text-indigo-300 hover:text-indigo-800 dark:hover:text-indigo-400 focus:outline-none">
            <i id="theme-icon" class="fas   text-2xl"></i>
        </button>
    <div id="upload-container" class="p-10 w-[28rem]">
        <h2 class="text-2xl font-semibold mb-4 text-center text-black dark:text-gray-200">Upload File</h2>
        <form id="upload-form" action="up.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div id="drop-zone" class="p-10 max-w-md flex items-center justify-center h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-md text-gray-500 dark:text-gray-300 cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-700">
                <span>Drag & Drop or Click to Select File</span>
            </div>
        <input type="file" name="file" id="file-input" class="hidden" accept="image/*" />
        </form>
        <div id="result" class="mt-4 hidden max-w-md">
            <!-- <div> -->
            <!-- Wrapped thumbnail in an anchor tag to make it clickable -->
            <a id="thumbnail-link" href="#" target="_blank">
            <img id="thumbnail" class="w-full h-auto rounded-md" src="#" alt="Image thumbnail" />
            </a>
            
            <div class="mt-2  text-gray-700 dark:text-gray-300">
                <div class="flex border border-gray-300 dark:border-gray-600 rounded-md">
                    <div class="bg-gray-200 p-2 rounded-l-md dark:bg-gray-600 content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                        </svg>
                    </div>
                    <div class="bg-blue-100 dark:bg-gray-100 rounded-r-md w-full content-center p-2">
                        <input id="file-url" type="text" readonly class="w-full bg-transparent text-sm outline-none cursor-pointer dark:text-gray-800" />
                    </div>
                </div>
                <button id="copy-btn" class="mt-2 w-full py-2 px-4 bg-indigo-600 dark:bg-indigo-500 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">Copy URL</button>
    </div>
    </div>
        <div id="message" class="mt-4 text-center text-red-500 hidden"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const html = document.documentElement;
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');

            // Toggle dark mode
            function toggleTheme() {
                if (html.classList.toggle('dark')) {
                    themeIcon.innerHTML=`<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                    `;
                } else {
                    themeIcon.innerHTML=`<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    `;
                }
            }

            // Initially set theme based on user preference or default
            const currentTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (currentTheme === 'dark') {
                html.classList.add('dark');
                themeIcon.innerHTML=`<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                    `;
            }
            themeToggle.addEventListener('click', () => {
                toggleTheme();
                localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
            });

            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('file-input');
            const uploadForm = document.getElementById('upload-form');
            const resultDiv = document.getElementById('result');
            const thumbnail = document.getElementById('thumbnail');
            const thumbnailLink = document.getElementById('thumbnail-link');
            const fileUrlInput = document.getElementById('file-url');
            const copyBtn = document.getElementById('copy-btn');
            const messageDiv = document.getElementById('message');

            const showMessage = (message, isError = false) => {
                messageDiv.textContent = message;
                messageDiv.classList.toggle('hidden', !message);
                messageDiv.style.color = isError ? 'red' : 'green';
            };

            const submitForm = (file) => {
                const formData = new FormData();
                formData.append('file', file);

                fetch('up.php', {
                    method: 'POST',
                    body: formData,
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          thumbnail.src = data.url;
                          thumbnailLink.href = data.url; // Set link to the file URL
                          fileUrlInput.value = data.url;
                          thumbnail.classList.remove('hidden');
                          resultDiv.classList.remove('hidden');
                          showMessage('File uploaded successfully!', false);
                      } else {
                          showMessage(data.message, true);
                      }
                  }).catch(error => showMessage('Error: ' + error, true));
            };

            dropZone.addEventListener('click', () => fileInput.click());

            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('drag-over');
            });

            dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('drag-over');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    submitForm(files[0]);
                }
        });

            fileInput.addEventListener('change', () => {
                const files = fileInput.files;
                if (files.length > 0) {
                    submitForm(files[0]);
                }
            });

            copyBtn.addEventListener('click', () => {
                fileUrlInput.select();
                document.execCommand('copy');
                showMessage('URL copied to clipboard!', false);
        });
        });
    </script>
</body>
</html>

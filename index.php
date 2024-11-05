<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPIC is FUN - by Jetorbit</title>
    <link href="css/styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link rel="icon" href="fav.png" type="image/x-icon">

</head>

<body class="bg-gray-100 dark:bg-gray-800  justify-center h-screen transition-colors duration-300 ">
    <!-- Theme Toggle Icon in the top right corner -->
    <button id="theme-toggle" class="absolute top-2 right-2 text-gray-600 dark:text-gray-400 p-2 rounded-full border-gray-300 border dark:border-gray-500 dark:hover:bg-gray-500 hover:bg-gray-300 text-center justify-center">
        <i id="theme-icon" class="text-2xl block">
        </i>

    </button>
    <div id="upload-container" class="p-10 md:w-[28rem] m-auto">
        <a href="/">
            <h1 class="text-2xl font-bold mb-4 text-center bg-gradient-to-r from-gray-700 via-blue-800  text-transparent bg-clip-text dark:text-gray-300">Upload Image </h1>
        </a>
        <form id="upload-form" action="up.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div id="drop-zone" class="p-10 max-w-md flex items-center justify-center text-center h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-md text-gray-500 dark:text-gray-300 cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-700">
                <span class="text-sm">Drag & Drop or Click to Select File ⚡</span>
            </div>
            <input multiple type="file" name="file" id="file-input" class="hidden" accept="image/*" />
        </form>

    </div>
    <div id="result" class="">
    </div>

    <div id="message" class="mt-4 text-sm text-center text-red-500 hidden transition-opacity duration-1000 ease-out "></div>
    <div id="CopyMsg" class="mt-4 text-sm text-center text-sky-700 dark:text-yellow-400 hidden transition-opacity duration-1000 ease-out "></div>

    <div class=" bottom-0  left-0 right-0 text-xs text-gray-500 dark:text-gray-400 px-4 py-2 text-center">
        <span class="italic">Built with AiLove</span> ❤️ <span class="italic">using <a href="https://www.jetorbit.com" title="Cloud VPS">Jetorbit</a></span>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const html = document.documentElement;
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('file-input');
            const resultDiv = document.getElementById('result');
            const thumbnail = document.getElementById('thumbnail');
            const thumbnailLink = document.getElementById('thumbnail-link');
            const fileUrlInput = document.getElementById('file-url');
            const copyBtn = document.getElementById('copy-btn');
            const messageDiv = document.getElementById('message');
            const copyMessage = document.getElementById('CopyMsg');
            const resultContainer = document.getElementById('result');

            const icons = {
                dark: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>`,
                light: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>`
            };

            function updateThemeIcon() {
                themeIcon.innerHTML = html.classList.contains('dark') ? icons.dark : icons.light;
            }

            function toggleTheme() {
                html.classList.toggle('dark');
                updateThemeIcon();
                localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
            }

            updateThemeIcon();
            if ((localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')) === 'dark') {
                html.classList.add('dark');
            }

            themeToggle.addEventListener('click', toggleTheme);

            const fadeOutMessage = (element) => {
                element.classList.add('opacity-0');
                setTimeout(() => {
                    element.classList.add('hidden');
                    element.classList.remove('opacity-0');
                }, 500);
            };

            const showMessage = (message, isError = false) => {
                messageDiv.textContent = message;
                messageDiv.classList.toggle('hidden', !message);
                messageDiv.classList.toggle('text-red-500', isError);
                messageDiv.classList.toggle('text-green-500', !isError);
                if (message) {
                    setTimeout(() => fadeOutMessage(messageDiv), 1500);
                }
            };

            const showCopyMsg = (message, isError = false) => {
                copyMessage.textContent = message;
                copyMessage.classList.toggle('hidden', !message);
                copyMessage.classList.toggle('block', message);

                if (message) {
                    setTimeout(() => fadeOutMessage(copyMessage), 1500);
                }
            };

            function getMainDomain(url) {
                try {
                    const parsedUrl = new URL(url);
                    return `${parsedUrl.protocol}//${parsedUrl.hostname}`;
                } catch (error) {
                    console.error("Invalid URL:", error);
                    return null;
                }
            }

            const mainDomain = getMainDomain(window.location.href);

            const submitForm = (files) => {
                Array.from(files).forEach((file, index) => {
                    const formData = new FormData();
                    formData.append('file', file);

                    fetch('up.php', {
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(dataArray => {
                            dataArray.forEach(data => {
                                if (data.success) {
                                    // Determine the ID based on the number of files
                                    const isSingleFile = files.length === 1;
                                    const fileComponent = document.createElement('div');
                                    fileComponent.classList.add('mt-4', 'max-w-md');

                                    // If single file, use a different ID
                                    if (isSingleFile) {
                                        fileComponent.classList.add('w-full', 'm-auto');
                                    } else {
                                        fileComponent.id = `result-${index}`;
                                        fileComponent.classList.add('w-full', 'm-auto');
                                    }

                                    // Create the thumbnail link and image
                                    const thumbnailLink = document.createElement('a');
                                    thumbnailLink.href = `${mainDomain}/f/${data.filename}`;
                                    thumbnailLink.target = '_blank';

                                    const thumbnail = document.createElement('img');
                                    thumbnail.src = data.url;
                                    thumbnail.alt = 'Image thumbnail';
                                    thumbnail.classList.add('w-full', 'h-auto', 'rounded-md');
                                    thumbnailLink.appendChild(thumbnail);
                                    fileComponent.appendChild(thumbnailLink);

                                    // Create the Direct Link section
                                    const directLinkSection = document.createElement('div');
                                    directLinkSection.classList.add('mt-2', 'text-gray-700', 'dark:text-gray-300');

                                    const linkLabel = document.createElement('span');
                                    linkLabel.classList.add('text-sm');
                                    linkLabel.textContent = 'Direct Link';
                                    directLinkSection.appendChild(linkLabel);

                                    const flexContainer = document.createElement('div');
                                    flexContainer.classList.add('flex', 'border', 'border-gray-300', 'dark:border-gray-600', 'rounded-md');

                                    const iconContainer = document.createElement('div');
                                    iconContainer.classList.add('bg-gray-200', 'p-2', 'rounded-l-md', 'dark:bg-gray-600', 'content-center');
                                    iconContainer.innerHTML = `
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                    </svg>`;
                                    flexContainer.appendChild(iconContainer);

                                    const inputContainer = document.createElement('div');
                                    inputContainer.classList.add('bg-blue-100', 'dark:bg-gray-100', 'rounded-r-md', 'w-full', 'content-center', 'p-2');
                                    const fileUrlInput = document.createElement('input');
                                    fileUrlInput.type = 'text';
                                    fileUrlInput.value = `${mainDomain}/f/${data.filename}`;
                                    fileUrlInput.readOnly = true;
                                    fileUrlInput.classList.add('w-full', 'bg-transparent', 'text-sm', 'outline-none', 'cursor-pointer', 'dark:text-gray-800');
                                    inputContainer.appendChild(fileUrlInput);
                                    flexContainer.appendChild(inputContainer);
                                    directLinkSection.appendChild(flexContainer);

                                    // Create the copy button
                                    const copyButton = document.createElement('button');
                                    copyButton.classList.add('mt-2', 'w-full', 'py-2', 'px-4', 'bg-indigo-600', 'dark:bg-indigo-500', 'text-white', 'rounded-md', 'hover:bg-indigo-700', 'dark:hover:bg-indigo-600', 'focus:outline-none', 'focus:ring-2', 'focus:ring-indigo-500');
                                    copyButton.textContent = 'Copy URL';
                                    copyButton.addEventListener('click', () => {
                                        navigator.clipboard.writeText(fileUrlInput.value);
                                        showMessage('URL copied to clipboard!', false);
                                    });
                                    directLinkSection.appendChild(copyButton);

                                    // Append all components to the fileComponent
                                    fileComponent.appendChild(directLinkSection);

                                    // Append fileComponent to the resultContainer
                                    resultContainer.appendChild(fileComponent);

                                    // Show the result container if it was hidden
                                    resultContainer.classList.remove('hidden');
                                } else {
                                    showMessage(data.message, true);
                                }
                            });
                            showMessage('Files uploaded successfully!', false);
                        })
                        .catch(error => showMessage('Error: ' + error, true));
                });
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
                    submitForm(files);
                }
            });

            fileInput.addEventListener('change', () => {
                const files = fileInput.files;
                if (files.length > 0) {
                    submitForm(files);
                }
            });

        });
    </script>
</body>

</html>
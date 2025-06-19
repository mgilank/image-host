<script>
// @ts-nocheck

  import { onMount } from 'svelte';

  let dropZone;
  let fileInput;
  let thumbnail;
  let thumbnailLink;
  let fileUrl = '';
  let message = '';
  let copyMessage = '';
  let showResult = false;

  function getMainDomain(url) {
    try {
      const parsedUrl = new URL(url);
      return `${parsedUrl.protocol}//${parsedUrl.hostname}`;
    } catch (error) {
      console.error("Invalid URL:", error);
      return null;
    }
  }

  async function submitForm(file) {
    const formData = new FormData();
    formData.append('file', file);

    try {
      const response = await fetch('/api/upload', {
        method: 'POST',
        body: formData,
      });
      const data = await response.json();

      if (data.success) {
        const mainDomain = getMainDomain(window.location.href);
        if (thumbnail) thumbnail.src = data.url;
        if (thumbnailLink) thumbnailLink.href = `${mainDomain}/f/${data.filename}`;
        fileUrl = `${mainDomain}/f/${data.filename}`;
        showResult = true;
        showMessage('File uploaded successfully!', false);
      } else {
        showMessage(data.message, true);
      }
    } catch (error) {
      showMessage('Error: ' + error, true);
    }
  }

  function showMessage(text, isError = false) {
    message = text;
    setTimeout(() => {
      message = '';
    }, 1500);
  }

  function showCopyMessage(text) {
    copyMessage = text;
    setTimeout(() => {
      copyMessage = '';
    }, 1500);
  }

  function copyUrl() {
    navigator.clipboard.writeText(fileUrl);
    showCopyMessage('URL copied to clipboard!');
  }

  function handleDrop(e) {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
      submitForm(files[0]);
    }
  }
</script>

<div id="upload-container" class="p-10 md:w-[28rem] m-auto">
  <a href="/">
    <h1 class="text-2xl font-bold mb-4 text-center bg-gradient-to-r from-gray-700 via-blue-800 text-transparent bg-clip-text dark:text-gray-300">
      Upload Image
    </h1>
  </a>

  <form
    on:submit|preventDefault
    class="space-y-4"
    autocomplete="off"
    aria-label="Upload image form"
  >
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <div
      bind:this={dropZone}
      on:click={() => fileInput.click()}
      on:dragover|preventDefault
      on:dragenter={() => dropZone.classList.add('drag-over')}
      on:dragleave={() => dropZone.classList.remove('drag-over')}
      on:drop={handleDrop}
      class="p-10 max-w-md flex items-center justify-center text-center h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-md text-gray-500 dark:text-gray-300 cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-700"
    >
      <span class="text-sm">Drag & Drop or Click to Select File ⚡</span>
    </div>

    <input
      bind:this={fileInput}
      type="file"
      name="file"
      accept="image/*"
      on:change={(e) => e.target.files[0] && submitForm(e.target.files[0])}
      class="hidden"
    />
  </form>

  {#if showResult}
    <div class="mt-4 max-w-md">
      <a bind:this={thumbnailLink} href="#" target="_blank" aria-label="Open uploaded image in new tab">
        <img
          bind:this={thumbnail}
          class="w-full h-auto rounded-md"
          src={fileUrl || "#"}
          alt={fileUrl ? "Uploaded image thumbnail" : "Image thumbnail placeholder"}
          loading="lazy"
        />
      </a>

      <div class="mt-2 text-gray-700 dark:text-gray-300">
        <span class="text-sm">Direct Link</span>
        <div class="flex border border-gray-300 dark:border-gray-600 rounded-md">
          <div class="bg-gray-200 p-2 rounded-l-md dark:bg-gray-600 content-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-5"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"
              />
            </svg>
          </div>
          <div class="bg-blue-100 dark:bg-gray-100 rounded-r-md w-full content-center p-2">
            <input
              type="text"
              readonly
              value={fileUrl}
              class="w-full bg-transparent text-sm outline-none cursor-pointer dark:text-gray-800"
              aria-label="Direct link to uploaded image"
            />
          </div>
        </div>
        <button
          on:click={copyUrl}
          class="mt-2 w-full py-2 px-4 bg-indigo-600 dark:bg-indigo-500 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          aria-label="Copy image URL to clipboard"
          type="button"
        >
          Copy URL
        </button>
      </div>
    </div>
  {/if}

  {#if message}
    <div class="mt-4 text-sm text-center {message.includes('Error') ? 'text-red-500' : 'text-green-500'} transition-opacity duration-1000 ease-out">
      {message}
    </div>
  {/if}

  {#if copyMessage}
    <div class="mt-4 text-sm text-center text-sky-700 dark:text-yellow-400 transition-opacity duration-1000 ease-out">
      {copyMessage}
    </div>
  {/if}
</div>

<script>
  import '../app.css';
  import { browser } from '$app/environment';
  import { onMount } from 'svelte';

  let darkMode = false;

  onMount(() => {
    if (browser) {
      const theme = localStorage.getItem('theme') || 
        (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      darkMode = theme === 'dark';
      if (darkMode) document.documentElement.classList.add('dark');
    }
  });

  function toggleTheme() {
    darkMode = !darkMode;
    document.documentElement.classList.toggle('dark');
    if (browser) {
      localStorage.setItem('theme', darkMode ? 'dark' : 'light');
    }
  }
</script>

<div class="bg-gray-100 dark:bg-gray-800 min-h-screen transition-colors duration-300">
  <button 
    on:click={toggleTheme}
    class="absolute top-2 right-2 text-gray-600 dark:text-gray-400 p-2 rounded-full border-gray-300 border dark:border-gray-500 dark:hover:bg-gray-500 hover:bg-gray-300 text-center justify-center"
  >
    {#if darkMode}
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
      </svg>
    {:else}
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
      </svg>
    {/if}
  </button>

  <slot />

  <div class="bottom-0 left-0 right-0 text-xs text-gray-500 dark:text-gray-400 px-4 py-2 text-center">
    <span class="italic">Built with AiLove</span> ❤️ <span class="italic">using <a href="https://www.jetorbit.com" title="Cloud VPS">Jetorbit</a></span>
  </div>
</div>
import { join } from 'path';
import { createReadStream, existsSync } from 'fs';
import { fileURLToPath } from 'url';
import { dirname } from 'path';

const __dirname = dirname(fileURLToPath(import.meta.url));

/** @type {import('@sveltejs/kit').Handle} */
export async function handle({ event, resolve }) {
  const { pathname } = event.url;

  // Handle /f/ routes for serving images
  if (pathname.startsWith('/f/')) {
    const filename = pathname.slice(3); // Remove /f/

    // Validate filename (7 alphanumeric characters + extension)
    if (!filename.match(/^[a-zA-Z0-9]{7}\.(jpg|jpeg|png)$/i)) {
      return new Response('Invalid filename', { status: 400 });
    }

    // Search for the file in the uploads directory
    const uploadsDir = join(__dirname, '..', 'static', 'uploads');
    let filePath = null;

    // Recursively search for the file
    const searchResult = await searchFile(uploadsDir, filename);
    if (searchResult) {
      filePath = searchResult;
    }

    if (!filePath || !existsSync(filePath)) {
      return new Response('File not found', { status: 404 });
    }

    // Determine content type
    const ext = filename.split('.').pop().toLowerCase();
    const contentType = ext === 'png' ? 'image/png' : 'image/jpeg';

    // Create readable stream
    const stream = createReadStream(filePath);

    return new Response(stream, {
      headers: {
        'Content-Type': contentType,
        'Cache-Control': 'public, max-age=31536000' // Cache for 1 year
      }
    });
  }

  return resolve(event);
}

async function searchFile(dir, filename) {
  const { readdir, stat } = await import('fs/promises');
  const { join } = await import('path');

  try {
    const files = await readdir(dir);

    for (const file of files) {
      const filePath = join(dir, file);
      const stats = await stat(filePath);

      if (stats.isDirectory()) {
        const result = await searchFile(filePath, filename);
        if (result) return result;
      } else if (file === filename) {
        return filePath;
      }
    }
  } catch (error) {
    console.error('Error searching file:', error);
  }

  return null;
}
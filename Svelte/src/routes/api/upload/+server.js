import { json } from '@sveltejs/kit';
import { writeFile } from 'fs/promises';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';
import { randomBytes } from 'crypto';

const __dirname = dirname(fileURLToPath(import.meta.url));

/** @type {import('./$types').RequestHandler} */
export async function POST({ request }) {
  try {
    const data = await request.formData();
    const file = data.get('file');

    if (!file) {
      return json({ success: false, message: 'No file uploaded.' });
    }

    // Check file type
    const allowedTypes = ['image/jpeg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
      return json({
        success: false,
        message: 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.'
      });
    }

    // Create directory structure year/month/day
    const now = new Date();
    const year = now.getFullYear().toString();
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const day = now.getDate().toString().padStart(2, '0');

    const uploadDir = join(__dirname, '..', '..', '..', '..', 'static', 'uploads', year, month, day);
    await createDirRecursive(uploadDir);

    // Generate a 7-character random filename
    const fileName = randomBytes(4).toString('hex').slice(0, 7);
    const fileExtension = file.type === 'image/png' ? 'png' : 'jpg';
    const fullFileName = `${fileName}.${fileExtension}`;
    const filePath = join(uploadDir, fullFileName);

    // Convert file to ArrayBuffer and save it
    const arrayBuffer = await file.arrayBuffer();
    const buffer = Buffer.from(arrayBuffer);
    await writeFile(filePath, buffer);

    // Construct the URL
    const fileUrl = `/uploads/${year}/${month}/${day}/${fullFileName}`;

    return json({
      success: true,
      url: fileUrl,
      filename: fullFileName
    });
  } catch (error) {
    console.error('Upload error:', error);
    return json({
      success: false,
      message: 'Failed to upload file.'
    });
  }
}

async function createDirRecursive(dir) {
  const { mkdir } = await import('fs/promises');
  await mkdir(dir, { recursive: true });
}
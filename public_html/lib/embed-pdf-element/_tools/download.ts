import { ensureFile } from "https://deno.land/std@0.176.0/fs/ensure_file.ts";
import JSZip from "https://esm.sh/jszip@3.10.1";

const PDFJS_DOWNLOAD_URL =
  "https://github.com/mozilla/pdf.js/releases/download/v3.3.122/pdfjs-3.3.122-dist.zip";

const res = await fetch(PDFJS_DOWNLOAD_URL);
const buf = await res.arrayBuffer();

const zip = new JSZip();
await zip.loadAsync(buf);

await Promise.all(
  Object.entries(zip.files).map(async ([name, file]) => {
    if (file.dir) {
      return;
    }
    const fileUrl = new URL(import.meta.resolve(`../vendor/pdfjs/${name}`));
    await ensureFile(fileUrl);
    await Deno.writeFile(fileUrl, await file.async("uint8array"));
  }),
);

Deno.exit(0);

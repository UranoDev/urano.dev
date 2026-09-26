// Exporta docs/marca/logo-urano-dev.html a PDF con Playwright (ya es
// dependencia del proyecto) — sin agregar dompdf, snappy ni otra librería.
//
// Uso: node scripts/build-brand-pdf.mjs

import { chromium } from 'playwright';
import path from 'node:path';
import { pathToFileURL } from 'node:url';
import { fileURLToPath } from 'node:url';

const ROOT = path.dirname(path.dirname(fileURLToPath(import.meta.url)));
const HTML_PATH = path.join(ROOT, 'docs', 'marca', 'logo-urano-dev.html');
const PDF_PATH = path.join(ROOT, 'docs', 'marca', 'logo-urano-dev.pdf');

async function main() {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  await page.goto(pathToFileURL(HTML_PATH).href, { waitUntil: 'networkidle' });
  await page.pdf({
    path: PDF_PATH,
    format: 'Letter',
    printBackground: true,
    margin: { top: 0, bottom: 0, left: 0, right: 0 },
  });
  await browser.close();
  console.log(`✓ ${path.relative(ROOT, PDF_PATH)}`);
}

main();

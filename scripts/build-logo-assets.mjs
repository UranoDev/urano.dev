// Genera favicon.ico (multi-resolución 16/32/48), favicon.svg y
// apple-touch-icon.png a partir de los SVG fuente en resources/images/logo/.
//
// favicon.ico se arma a mano (cabecera ICO + PNGs embebidos) en vez de con una
// librería — el formato ICO moderno (Vista+) acepta PNG crudo por entrada, así
// que no hace falta convertir a bitmap: solo empacar los bytes con la cabecera
// correcta. Ver construirIco() abajo.
//
// Uso: node scripts/build-logo-assets.mjs

import { chromium } from 'playwright';
import { readFile, writeFile, copyFile, mkdir } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.dirname(path.dirname(fileURLToPath(import.meta.url)));
const LOGO_SRC = path.join(ROOT, 'resources', 'images', 'logo');
const PUBLIC = path.join(ROOT, 'public');

// Escalado del trazo a tamaños chicos (mismos valores que UDEV-21 y el canvas
// de diseño): a menor tamaño, más grueso, o el trazo se pierde.
const FAVICON_SIZES = [
  { px: 48, orbitStroke: 4.5, dash: '8 7', dotR: 8, ringR: 9, ringStroke: 6, knockoutR: 12 },
  { px: 32, orbitStroke: 6.5, dash: '9 7', dotR: 9, ringR: 10.5, ringStroke: 8, knockoutR: 14.5 },
  { px: 16, orbitStroke: 8, dash: '11 8', dotR: 11, ringR: 13, ringStroke: 10, knockoutR: 18 },
];

function markSvg({ orbitStroke, dash, dotR, ringR, ringStroke, knockoutR }, { ink = '#111111', paper = '#FAFAFA', accent = '#213A9A' } = {}) {
  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="none">
    <circle cx="50" cy="50" r="36" stroke="${ink}" stroke-width="${orbitStroke}" stroke-dasharray="${dash}"/>
    <circle cx="50" cy="86" r="${dotR}" fill="${ink}"/>
    <circle cx="18.8" cy="32" r="${dotR}" fill="${ink}"/>
    <circle cx="81.2" cy="32" r="${knockoutR}" fill="${paper}"/>
    <circle cx="81.2" cy="32" r="${ringR}" stroke="${accent}" stroke-width="${ringStroke}"/>
  </svg>`;
}

async function screenshotSvg(page, svg, size, background = 'transparent') {
  await page.setViewportSize({ width: size, height: size });
  await page.setContent(
    `<!doctype html><html><body style="margin:0;width:${size}px;height:${size}px;background:${background}">${svg}</body></html>`
  );
  return page.screenshot({ omitBackground: background === 'transparent' });
}

/**
 * Arma un .ico multi-resolución a partir de buffers PNG.
 * Formato: ICONDIR (6 bytes) + ICONDIRENTRY (16 bytes) por imagen + los PNG
 * concatenados. Windows Vista+ acepta PNG crudo por entrada (bitCount=32).
 */
function construirIco(pngs) {
  const count = pngs.length;
  const headerSize = 6 + 16 * count;
  let offset = headerSize;
  const header = Buffer.alloc(6);
  header.writeUInt16LE(0, 0); // reserved
  header.writeUInt16LE(1, 2); // type: 1 = icon
  header.writeUInt16LE(count, 4);

  const entries = [];
  for (const { size, buffer } of pngs) {
    const entry = Buffer.alloc(16);
    entry.writeUInt8(size >= 256 ? 0 : size, 0); // width (0 = 256)
    entry.writeUInt8(size >= 256 ? 0 : size, 1); // height
    entry.writeUInt8(0, 2); // color count
    entry.writeUInt8(0, 3); // reserved
    entry.writeUInt16LE(1, 4); // planes
    entry.writeUInt16LE(32, 6); // bit count
    entry.writeUInt32LE(buffer.length, 8); // bytes in resource
    entry.writeUInt32LE(offset, 12); // offset
    offset += buffer.length;
    entries.push(entry);
  }

  return Buffer.concat([header, ...entries, ...pngs.map((p) => p.buffer)]);
}

async function main() {
  await mkdir(PUBLIC, { recursive: true });
  const browser = await chromium.launch();
  const page = await browser.newPage();

  console.log('Generando favicon.ico (16/32/48, multi-resolución)...');
  const pngs = [];
  for (const spec of FAVICON_SIZES) {
    const svg = markSvg(spec);
    const buffer = await screenshotSvg(page, svg, spec.px);
    pngs.push({ size: spec.px, buffer });
  }
  const ico = construirIco(pngs.sort((a, b) => a.size - b.size));
  await writeFile(path.join(PUBLIC, 'favicon.ico'), ico);
  console.log(`  ✓ favicon.ico (${ico.length} bytes, ${pngs.map((p) => p.size).join('/')} px)`);

  console.log('Copiando favicon.svg...');
  await copyFile(path.join(LOGO_SRC, 'mark-primary.svg'), path.join(PUBLIC, 'favicon.svg'));
  console.log('  ✓ favicon.svg');

  console.log('Generando apple-touch-icon.png (180×180, fondo oscuro)...');
  const darkMarkSvg = await readFile(path.join(LOGO_SRC, 'mark-dark.svg'), 'utf8');
  // El símbolo ocupa ~72% del lienzo, centrado, con margen — igual que
  // cualquier apple-touch-icon (iOS recorta las esquinas solo).
  const wrapped = `<div style="width:180px;height:180px;display:flex;align-items:center;justify-content:center;">
    <div style="width:130px;height:130px;">${darkMarkSvg}</div>
  </div>`;
  const touchIcon = await screenshotSvg(page, wrapped, 180, '#111111');
  await writeFile(path.join(PUBLIC, 'apple-touch-icon.png'), touchIcon);
  console.log('  ✓ apple-touch-icon.png');

  await browser.close();
  console.log('Listo.');
}

main();

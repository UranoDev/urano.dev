// Captura las pantallas de cada proyecto del portafolio con un viewport fijo,
// sin importar la resolución real del monitor de quien lo ejecuta — así la
// composición (2fr/1.1fr/1.1fr en la tarjeta) sale igual siempre.
//
// Uso:
//   node scripts/capture-portfolio-screenshots.mjs <slug>
//   node scripts/capture-portfolio-screenshots.mjs --all
//
// Los archivos salen a public/images/portfolio/<slug>/<shot>.png — se
// commitean con el repo, no son contenido subido en runtime (por eso no van
// a storage/app/public, que .gitignore excluye por completo).

import { chromium } from 'playwright';
import { mkdir } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.dirname(path.dirname(fileURLToPath(import.meta.url)));
const OUT_ROOT = path.join(ROOT, 'public', 'images', 'portfolio');

// El viewport de la destacada (columna ancha de la tarjeta) y el de las
// secundarias (casi cuadradas). No es un recorte del capture ancho: se narra
// la página a este ancho y su propio layout responsivo decide qué mostrar.
// Recortar el capture ancho en cambio deja fuera contenido que sí cabe en el
// layout angosto (probado con la lista de precios de CalzaClean: a 1280px el
// precio queda a la derecha, fuera de cualquier recorte razonable; a 760px el
// mismo contenedor lo reacomoda dentro del ancho).
const FEATURED_VIEWPORT = { width: 1280, height: 800 };
const SECONDARY_VIEWPORT = { width: 760, height: 660 };

/**
 * Cada `shot` describe una vista dentro de config.baseUrl.
 *   path      ruta a visitar
 *   featured  true usa FEATURED_VIEWPORT; si no, SECONDARY_VIEWPORT
 * Para posicionar el scroll, a lo más uno de:
 *   scrollToText texto de un heading/elemento a llevar a la vista
 *   scrollBy     píxeles adicionales de scroll tras localizar el texto
 *                (o desde el tope, si no hay scrollToText)
 *
 * Nota: esta app no pinta secciones diferidas si el scroll salta de golpe
 * (p. ej. scrollIntoView directo a un elemento lejano). Por eso el scroll
 * siempre avanza en pasos cortos con espera entre cada uno.
 */
const PROJECTS = {
  'mi-biblioteca': {
    baseUrl: 'https://biblio.urano.dev',
    shots: [
      {
        name: 'hero',
        path: '/',
        featured: true,
        // Tope de la página: título + "En mi lista".
      },
      {
        name: 'acervo',
        path: '/',
        scrollToText: 'Los que tengo',
        scrollBy: 80,
      },
      {
        name: 'ficha',
        // "The Reckoning" — portada real y estado "Leído".
        path: '/book/42',
      },
    ],
  },
  calzaclean: {
    baseUrl: 'https://calzaclean.com',
    shots: [
      {
        name: 'hero',
        path: '/',
        featured: true,
        // Tope de la página: título + botones de WhatsApp / ver precios.
      },
      {
        name: 'resultados',
        path: '/resultados',
      },
      {
        name: 'precios',
        path: '/precios',
      },
    ],
  },
  'mas-reviews': {
    baseUrl: 'https://masreviews.mx',
    shots: [
      {
        name: 'hero',
        path: '/',
        featured: true,
        // Tope: "Reseñas de Google con un toque" + la pieza NFC/QR.
      },
      {
        name: 'como-funciona',
        path: '/',
        scrollToText: 'Lo que pasa, paso por paso',
        scrollBy: -40,
      },
      {
        name: 'estilo',
        path: '/estilo',
        // Guía de estilo viva del proyecto — sirve como muestra de dirección visual.
      },
    ],
  },
};

async function settleScroll(page) {
  // Avanza en pasos cortos en vez de saltar: esta app no dibuja el contenido
  // diferido cuando el scroll llega de golpe a la posición final.
  for (let i = 0; i < 4; i++) {
    await page.mouse.wheel(0, 250);
    await page.waitForTimeout(250);
  }
}

async function captureProject(slug, config) {
  const outDir = path.join(OUT_ROOT, slug);
  await mkdir(outDir, { recursive: true });

  const browser = await chromium.launch();

  for (const shot of config.shots) {
    const viewport = shot.featured ? FEATURED_VIEWPORT : SECONDARY_VIEWPORT;
    const page = await browser.newPage({ viewport });

    await page.goto(config.baseUrl + shot.path, { waitUntil: 'networkidle' });
    await page.waitForTimeout(300);

    if (shot.scrollToText) {
      await settleScroll(page);
      const target = page.getByText(shot.scrollToText, { exact: false }).first();
      await target.scrollIntoViewIfNeeded();
      await page.waitForTimeout(300);
    }

    if (shot.scrollBy) {
      await page.mouse.wheel(0, shot.scrollBy);
      await page.waitForTimeout(300);
    }

    const outPath = path.join(outDir, `${shot.name}.png`);
    await page.screenshot({ path: outPath });
    console.log(`  ✓ ${slug}/${shot.name}.png (${viewport.width}x${viewport.height})`);
    await page.close();
  }

  await browser.close();
}

async function main() {
  const arg = process.argv[2];
  const slugs = arg === '--all' ? Object.keys(PROJECTS) : [arg];

  if (!arg || (arg !== '--all' && !PROJECTS[arg])) {
    console.error('Uso: node scripts/capture-portfolio-screenshots.mjs <slug> | --all');
    console.error(`Proyectos disponibles: ${Object.keys(PROJECTS).join(', ')}`);
    process.exit(1);
  }

  for (const slug of slugs) {
    console.log(`Capturando ${slug}...`);
    await captureProject(slug, PROJECTS[slug]);
  }
}

main();

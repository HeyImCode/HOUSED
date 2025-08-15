<?php /* app/views/pages/comprar.php */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>HOUSED – Comprar</title>

  <link rel="stylesheet" href="css/stylesComponentes.css">
  <link rel="stylesheet" href="css/comprar.css">

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
</head>
<body>

<?php include __DIR__ . '/../componentes/navbar.php'; ?>

<!-- Espacio leve bajo el navbar -->
<div class="top-spacer"></div>

<section class="chips">
  <div class="chips__scroll">
    <button class="chip chip--primary" data-prov="ALL">Costa Rica</button>
    <button class="chip" data-prov="San José">San José</button>
    <button class="chip" data-prov="Alajuela">Alajuela</button>
    <button class="chip" data-prov="Cartago">Cartago</button>
    <button class="chip" data-prov="Heredia">Heredia</button>
    <button class="chip" data-prov="Guanacaste">Guanacaste</button>
    <button class="chip" data-prov="Puntarenas">Puntarenas</button>
    <button class="chip" data-prov="Limón">Limón</button>
    <button class="chip chip--ghost" id="btn-clear">Quitar límite</button>
  </div>
</section>

<main class="maplayout">
  <div id="map"></div>

  <aside class="list">
    <div class="list__head">
      <h2 id="list-title">Casas en Costa Rica</h2>
      <small id="list-subtitle">Selecciona una provincia</small>
    </div>

    <!-- NUEVO: barra de búsqueda por provincia -->
    <div class="list__search">
      <input
        type="search"
        id="prov-search"
        placeholder="Buscar por provincia (San José, Alajuela, Cartago, Heredia, Guanacaste, Puntarenas, Limón)"
        autocomplete="off"
      />
      <button id="prov-search-btn" aria-label="Buscar">Buscar</button>
      <button id="prov-clear" class="ghost" aria-label="Limpiar">Limpiar</button>
    </div>

    <div id="list-results" class="list__results"></div>
  </aside>
</main>

<script>
  // ===== DATOS (IDs = nombre de archivo en /public/img/casas/<id>.[jpg|jpeg|png|webp]) =====
  const PROPERTIES = [
    // San José
    { id:'sanjose1', title:'Casa céntrica en San José', price:180000, beds:3, baths:2, area:120, addr:'Avenida 4, San José', prov:'San José', lat:9.932, lng:-84.08 },
    { id:'sanjose2', title:'Casa en Desamparados',     price:155000, beds:2, baths:2, area:95,  addr:'Desamparados',        prov:'San José', lat:9.90,  lng:-84.06 },
    // Alajuela
    { id:'alajuela1', title:'Casa en Alajuela centro', price:165000, beds:3, baths:2, area:130, addr:'Alajuela centro',     prov:'Alajuela', lat:10.016, lng:-84.211 },
    { id:'alajuela2', title:'Casa en Grecia',          price:145000, beds:2, baths:1, area:90,  addr:'Grecia',             prov:'Alajuela', lat:10.068, lng:-84.314 },
    // Cartago
    { id:'cartago1', title:'Casa en Cartago centro',   price:150000, beds:3, baths:2, area:110, addr:'Cartago',            prov:'Cartago', lat:9.86,   lng:-83.91 },
    { id:'cartago2', title:'Casa en Turrialba',        price:135000, beds:2, baths:1, area:88,  addr:'Turrialba',          prov:'Cartago', lat:9.90,   lng:-83.68 },
    // Heredia
    { id:'heredia1', title:'Casa en Heredia',          price:170000, beds:3, baths:2, area:125, addr:'Heredia',            prov:'Heredia', lat:10.002, lng:-84.116 },
    { id:'heredia2', title:'Casa en Belén',            price:210000, beds:4, baths:3, area:160, addr:'Belén',              prov:'Heredia', lat:9.97,   lng:-84.20 },
    // Guanacaste
    { id:'guanacaste1', title:'Casa en Liberia',       price:190000, beds:3, baths:2, area:140, addr:'Liberia',            prov:'Guanacaste', lat:10.634, lng:-85.437 },
    { id:'guanacaste2', title:'Casa en Tamarindo',     price:320000, beds:3, baths:3, area:180, addr:'Tamarindo',          prov:'Guanacaste', lat:10.299, lng:-85.84 },
    // Puntarenas
    { id:'puntarenas1', title:'Casa en Puntarenas',    price:175000, beds:3, baths:2, area:120, addr:'Puntarenas',         prov:'Puntarenas', lat:9.976, lng:-84.833 },
    { id:'puntarenas2', title:'Casa en Quepos',        price:260000, beds:4, baths:3, area:170, addr:'Quepos',             prov:'Puntarenas', lat:9.431, lng:-84.162 },
    // Limón
    { id:'limon1', title:'Casa en Limón',              price:140000, beds:3, baths:2, area:115, addr:'Limón',              prov:'Limón',    lat:9.99,  lng:-83.03 },
    { id:'limon2', title:'Casa en Puerto Viejo',       price:230000, beds:3, baths:2, area:150, addr:'Puerto Viejo',       prov:'Limón',    lat:9.66,  lng:-82.76 },
  ];

  // Provincias válidas para la búsqueda (sin usar rectángulos)
  const PROVINCES = ['San José','Alajuela','Cartago','Heredia','Guanacaste','Puntarenas','Limón'];

  // ===== Mapa (solo puntos) =====
  const CSS_PRIM = getComputedStyle(document.documentElement)
                   .getPropertyValue('--color-primario') || '#6786ea';

  const map = L.map('map', { zoomControl: true });
  map.setView([9.93, -84.08], 7);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18, attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  const allMarkers = [];
  const markerLayer = L.layerGroup().addTo(map);
  const priceFmt = n => '$' + Number(n).toLocaleString('en-US');

  function drawMarkers(list) {
    markerLayer.clearLayers();
    allMarkers.length = 0;
    list.forEach(p => {
      const m = L.circleMarker([p.lat, p.lng], {
        radius: 7, weight: 2, color: CSS_PRIM.trim() || '#6786ea', fillOpacity: 0.7
      }).addTo(markerLayer);

      m.bindPopup(
        `<strong>${p.title}</strong><br>
         ${priceFmt(p.price)} • ${p.beds} hab • ${p.baths} baños • ${p.area} m²<br>
         <small>${p.addr} – ${p.prov}</small>`
      );

      m.on('click', () => {
        const card = document.querySelector(`.card[data-id="${p.id}"]`);
        if (card) {
          card.scrollIntoView({ behavior: 'smooth', block: 'center' });
          card.classList.add('card--pulse');
          setTimeout(() => card.classList.remove('card--pulse'), 1000);
        }
      });

      allMarkers.push(m);
    });
  }

  // Helper para normalizar (ignorar acentos y mayúsculas)
  const norm = s => s.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim();

  // ===== Render inicial =====
  drawMarkers(PROPERTIES);
  renderList(PROPERTIES);
  document.getElementById('list-subtitle').textContent = `${PROPERTIES.length} resultados`;

  // ===== Filtro por provincia =====
  function applyProvince(prov) {
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('chip--primary'));
    const btn = document.querySelector(`.chip[data-prov="${prov}"]`);
    if (btn) btn.classList.add('chip--primary');

    const filtered = PROPERTIES.filter(p => p.prov === prov);
    renderList(filtered);
    drawMarkers(filtered);

    if (filtered.length) {
      const bounds = L.latLngBounds(filtered.map(p => [p.lat, p.lng]));
      map.fitBounds(bounds, { padding: [28, 28] });
    }
    document.getElementById('list-title').textContent = `Casas en ${prov}`;
    document.getElementById('list-subtitle').textContent = `${filtered.length} resultados`;
  }

  function clearProvince() {
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('chip--primary'));
    document.querySelector('.chip[data-prov="ALL"]').classList.add('chip--primary');
    renderList(PROPERTIES);
    drawMarkers(PROPERTIES);
    document.getElementById('list-title').textContent = 'Casas en Costa Rica';
    document.getElementById('list-subtitle').textContent = `${PROPERTIES.length} resultados`;
    map.setView([9.93, -84.08], 7);
  }

  // Chips
  document.querySelectorAll('.chip[data-prov]').forEach(chip => {
    chip.addEventListener('click', () => {
      const p = chip.getAttribute('data-prov');
      if (p === 'ALL') clearProvince(); else applyProvince(p);
    });
  });
  document.getElementById('btn-clear').addEventListener('click', clearProvince);

  // ===== Búsqueda por provincia =====
  const inp = document.getElementById('prov-search');
  const btn = document.getElementById('prov-search-btn');
  const btnClear = document.getElementById('prov-clear');

  function searchProvince() {
    const q = norm(inp.value);
    if (!q) { clearProvince(); return; }

    // coincidencia exacta o que empiece igual
    const match = PROVINCES.find(p => norm(p) === q) ||
                  PROVINCES.find(p => norm(p).startsWith(q));

    if (match) {
      applyProvince(match);
    } else {
      // sin coincidencia clara: mostrar 0 resultados
      document.querySelectorAll('.chip').forEach(c => c.classList.remove('chip--primary'));
      const filtered = PROPERTIES.filter(p => norm(p.prov).includes(q));
      renderList(filtered);
      drawMarkers(filtered);
      document.getElementById('list-title').textContent = `Casas en “${inp.value}”`;
      document.getElementById('list-subtitle').textContent = `${filtered.length} resultados`;
      if (filtered.length) {
        const bounds = L.latLngBounds(filtered.map(p => [p.lat, p.lng]));
        map.fitBounds(bounds, { padding: [28, 28] });
      }
    }
  }

  inp.addEventListener('keydown', e => { if (e.key === 'Enter') searchProvince(); });
  inp.addEventListener('input', () => {
    // autocompletar suave: si queda una coincidencia única, aplicar
    const q = norm(inp.value);
    if (!q) { clearProvince(); return; }
    const candidates = PROVINCES.filter(p => norm(p).startsWith(q));
    if (candidates.length === 1) applyProvince(candidates[0]);
  });
  btn.addEventListener('click', searchProvince);
  btnClear.addEventListener('click', () => { inp.value = ''; clearProvince(); });

  // ===== Lista con imagen =====
  function createThumb(p) {
    const base = 'img/casas/' + p.id.toLowerCase();
    const exts = ['.jpg', '.jpeg', '.png', '.webp'];
    const img = document.createElement('img');
    img.className = 'card__thumb';
    img.alt = 'Foto de ' + p.title;
    let i = 0;
    img.src = base + exts[i++];
    img.onerror = () => { if (i < exts.length) img.src = base + exts[i++]; else img.src = 'https://placehold.co/200x150?text=Hogar'; };
    return img;
  }

  function renderList(list) {
    const wrap = document.getElementById('list-results');
    wrap.innerHTML = '';
    list.forEach(p => {
      const el = document.createElement('article');
      el.className = 'card';
      el.setAttribute('data-id', p.id);

      const thumb = createThumb(p);

      const body = document.createElement('div');
      body.className = 'card__body';
      body.innerHTML = `
        <div class="card__price">${priceFmt(p.price)}</div>
        <div class="card__meta">
          <span>${p.beds} hab</span>
          <span>${p.baths} baños</span>
          <span>${p.area} m²</span>
        </div>
        <h3 class="card__title">${p.title}</h3>
        <div class="card__addr">${p.addr} — <strong>${p.prov}</strong></div>
        <button class="card__btn" data-focus="${p.id}">Ver en mapa</button>
      `;

      el.appendChild(thumb);
      el.appendChild(body);
      wrap.appendChild(el);
    });

    // Botón "Ver en mapa"
    wrap.querySelectorAll('.card__btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-focus');
        const prop = PROPERTIES.find(x => x.id === id);
        if (!prop) return;
        map.setView([prop.lat, prop.lng], 14, { animate: true });

        const found = allMarkers.find(m => {
          const ll = m.getLatLng();
          return Math.abs(ll.lat - prop.lat) < 1e-6 && Math.abs(ll.lng - prop.lng) < 1e-6;
        });
        if (found) found.openPopup();
      });
    });
  }

  // ===== Altura dinámica + fix Leaflet =====
  function resizeLayout() {
    const nav = document.querySelector('nav');
    const chips = document.querySelector('.chips');
    const footer = document.querySelector('footer');
    const navH = nav ? nav.offsetHeight : 0;
    const chipsH = chips ? chips.offsetHeight : 0;
    let footerExtra = 0;
    if (footer && getComputedStyle(footer).position === 'fixed') footerExtra = footer.offsetHeight;
    const target = Math.max(320, window.innerHeight - navH - chipsH - footerExtra - 8 /* extra espacio */);
    const layout = document.querySelector('.maplayout');
    if (layout) layout.style.height = target + 'px';
    if (typeof map !== 'undefined') map.invalidateSize();
  }

  window.addEventListener('DOMContentLoaded', () => { resizeLayout(); setTimeout(() => map.invalidateSize(), 50); });
  window.addEventListener('load',         () => { resizeLayout(); setTimeout(() => map.invalidateSize(), 50); });
  window.addEventListener('resize',       () => { resizeLayout(); map.invalidateSize(); });

  // (Opcional) soporte ?prov=Guanacaste
  const params = new URLSearchParams(window.location.search);
  const qprov = params.get('prov');
  if (qprov) {
    const match = PROPERTIES.filter(p => p.prov === qprov);
    if (match.length) applyProvince(qprov);
  }
</script>

<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>
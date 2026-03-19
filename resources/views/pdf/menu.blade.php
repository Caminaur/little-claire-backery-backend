<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Menú - {{ $data['name'] }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Inter:wght@400;500;600;700;800&family=Oooh+Baby&display=swap" rel="stylesheet">
  <style>
    :root {
      --paper: #fbf8f1;
      --gold: #b98d2d;
      --gold-soft: #cfad5c;
      --line: #ddc790;
      --muted: #676055;
      --teal: #1d5864;
      --ink: #171717;
    }

    * { box-sizing: border-box; }

    html, body {
      margin: 0;
      padding: 0;
      background: #ece7dc;
      color: var(--ink);
      font-family: "Inter", system-ui, sans-serif;
      font-size: 16px;
    }

    img { max-width: 100%; display: block; }

    /* ── DOCUMENT ── */
    .menu-document {
      width: min(100%, 980px);
      margin: 0 auto;
      padding: 24px 12px 60px;
    }

    /* ── PAGE ── */
    .menu-page {
      width: 210mm;
      height: 297mm;
      margin: 0 auto 24px;
      background: linear-gradient(180deg, rgba(255,255,255,0.97), rgba(255,249,240,0.98));
      box-shadow: 0 22px 50px rgba(0,0,0,0.12);
      position: relative;
      overflow: hidden;
    }

    .page-frame {
      height: 100%;
      padding: 13mm;
      position: relative;
      display: flex;
      flex-direction: column;
    }

    /* Double border frame */
    .page-frame::before {
      content: "";
      position: absolute;
      inset: 7mm;
      border: 3px solid var(--gold);
      pointer-events: none;
    }
    .page-frame::after {
      content: "";
      position: absolute;
      inset: 10mm;
      border: 1.5px solid var(--gold-soft);
      pointer-events: none;
    }

    /* Corner accents */
    .menu-page::before,
    .menu-page::after {
      content: "";
      position: absolute;
      width: 46px;
      height: 46px;
      border-color: var(--gold);
      border-style: solid;
      pointer-events: none;
    }
    .menu-page::before { top: 18mm; left: 18mm;  border-width: 3px 0 0 3px; border-radius: 12px 0 0 0; }
    .menu-page::after  { top: 18mm; right: 18mm; border-width: 3px 3px 0 0; border-radius: 0 12px 0 0; }

    /* ── HEADER ── */
    .page-header { position: relative; z-index: 1; }

    .document-title {
      margin:0;
      text-align: center;
      color: var(--gold);
      font-size: 2.4rem;
      letter-spacing: 0.35rem;
      font-family: "Cinzel", serif;
      font-weight: 600;
    }

    .logo-band {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      align-items: center;
      gap: 16px;
    }
    .logo { width: 220px; max-width: 100%; justify-self: center; }
    .logo-line { width: 100%; height: auto; display: block; }
    .logo-line--flip { transform: scaleX(-1); }

    /* ── SECTIONS ── */
    .menu-section { position: relative; z-index: 1; }

    .section-title {
      margin:5px 0;
      color: #8d6320;
      font-family: "Cinzel", serif;
      font-size: 1.1rem;
      letter-spacing: 0.02rem;
      font-weight: 700;
    }
    .section-title small {
      font-size: 0.8rem;
      font-family: "Inter", sans-serif;
      letter-spacing: 0;
      color: var(--muted);
    }
    .section-accent { color: #75a6d9; font-size: 1rem; vertical-align: middle; }
    .divider { display: inline-block; padding: 0 6px; }

    /* ── GRID / COLUMNS ── */
    .columns { display: grid; gap: 24px; }
    .two-columns   { grid-template-columns: 1fr 1fr; }
    .three-columns { grid-template-columns: repeat(3, 1fr); }
    .equal-gap     { gap: 28px; }

    .catalog-grid         { display: grid; gap: 18px; }
    .catalog-grid--three  { grid-template-columns: repeat(3, 1fr); }
    .catalog-grid--prices { gap: 20px; }

    .item-list, .simple-list, .plain-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .compact-list { gap: 14px; }
    .paired-list  { gap: 14px; }

    /* ── MENU ITEM ── */
    .menu-item {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 14px;
    }
    .menu-item--single-line { align-items: center; }
    .no-icon { gap: 0; }

    .item-main {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      min-width: 0;
      flex: 1;
    }
    .item-copy { min-width: 0; }

    .item-copy h3,
    .menu-item > h3,
    .item-main h3 {
      margin: 0;
      font-size: 0.80rem;
      line-height: 1.10;
      font-weight: 800;
      letter-spacing: 0.01rem;
    }

    .item-copy p,
    .section-note,
    .plain-list small {
      margin: 2px 0 0;
      font-size: 0.65rem;
      line-height: 1.25;
      color: var(--teal);
      font-weight: 600;
    }

    /* ── ICON ── */
    .item-icon {
      width: 32px;
      height: 32px;
      flex: 0 0 32px;
      display: grid;
      place-items: center;
    }
    .item-icon img { width: 32px; height: 32px; object-fit: contain; }

    /* ── PRICE TAG ── */
    .price-tag {
      flex: 0 0 auto;
      min-width: 78px;
      text-align: right;
      font-family: "Oooh Baby", cursive;
      font-size: 1.25rem;
      font-weight: 400;
      line-height: 1;
      color: #111;
      background: rgba(255,255,255,0.78);
      padding: 2px 8px 0;
      border-radius: 6px;
      box-shadow: 0 0 0 1px rgba(230,225,213,0.7) inset;
    }

    /* ── SIZE PRICES (Cafés mediana/grande) ── */
    .size-prices {
      flex: 0 0 auto;
      display: flex;
      flex-direction: column;
      gap: 5px;
      min-width: 100px;
      margin-top: 1px;
    }
    .size-prices span {
      display: flex;
      justify-content: space-between;
      gap: 8px;
      color: #977539;
      font-size: 0.7rem;
      font-weight: 700;
    }
    .size-prices strong {
      color: #111;
      font-family: "Oooh Baby", cursive;
      font-weight: 400;
      font-size: 1.15rem;
      line-height: 0.8;
    }

    /* ── UNIFORM PRICE BOX (Bebidas, Tés) ── */
    .section-row, .drinks-row {
      display: flex;
      gap: 20px;
      align-items: flex-start;
    }
    .grow { flex: 1; }
    .price-box {
      flex: 0 0 160px;
      margin-top: 18px;
      border-left: 2px solid var(--gold);
      border-right: 2px solid var(--gold);
      padding: 18px 16px;
      text-align: center;
      color: #8d6320;
      font-family: "Cinzel", serif;
    }
    .price-box span   { display: block; font-size: 1rem; font-weight: 700; letter-spacing: 0.04rem; }
    .price-box strong { display: block; margin-top: 4px; font-size: 1.7rem; font-weight: 600; }
    .price-box--tall  { align-self: center; min-height: 180px; display: grid; align-content: center; }

    /* Plain text list (Bebidas, Tés) */
    .plain-list p, .inline-banner span, .section-note {
      margin: 0;
      color: #111;
      font-size: 0.86rem;
      font-weight: 800;
      line-height: 1.3;
    }
    .plain-list--spacious { gap: 12px; }
    .plain-list--spacious p { font-size: 0.9rem; }

    /* ── LICUADOS INLINE BANNER ── */
    .inline-banner {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      gap: 16px;
    }
    .inline-banner strong {
      font-family: "Oooh Baby", cursive;
      font-size: 1.4rem;
      color: #111;
      font-weight: 400;
    }

    /* ── PASTELERÍA HEADER ── */
    .section-flex-header {
      display: grid;
      grid-template-columns: auto 1fr;
      gap: 10px 24px;
      align-items: baseline;
    }
    .section-flex-header .catalog-grid { grid-column: 1 / -1; }
    .section-note {
      color: #8d6320;
      font-family: "Cinzel", serif;
      font-size: 0.86rem;
      font-weight: 600;
    }

    /* ── BEBIDAS CALIENTES ── */
    .hot-drinks .price-tag { min-width: 72px; }

    /* ── BEBIDAS HELADAS ── */
    .centered-price { align-items: center; }

    /* ── FOOTER ── */
    .page-footer {
      margin-top: auto;
      padding-top: 10px;
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      align-items: center;
      gap: 16px;
    }
    .footer-line {
      height: 4px;
      background: linear-gradient(90deg, transparent 0%, var(--gold) 14%, var(--gold) 86%, transparent 100%);
    }
    .footer-social { display: flex; align-items: center; gap: 12px; }
    .qr-code {
      width: 62px;
      height: 62px;
      object-fit: cover;
      background: white;
      padding: 4px;
      border: 1px solid rgba(0,0,0,0.12);
    }
    .social-copy p {
      margin: 0;
      color: #9c7225;
      font-family: "Inter", sans-serif;
      font-weight: 800;
      font-size: 0.95rem;
      letter-spacing: 0.04rem;
    }
    .social-copy strong { color: #8f6821; font-size: 1.1rem; font-weight: 800; }

    em {
      font-style: italic;
      font-family: "Oooh Baby", cursive;
      font-size: 1.05rem;
      letter-spacing: 0.02rem;
    }

    /* ── SIMPLE GRID ── */
    .simple-grid .simple-list { gap: 12px; }

    /* ── PDF / PRINT ── */
   @page {
      size: A4;
      margin: 0;
    }

    @media print {
      html, body {
        width: 210mm;
        height: auto;
        margin: 0;
        padding: 0;
        background: white;
      }

      .menu-document {
        width: 210mm;
        margin: 0;
        padding: 0;
      }

      .menu-page {
        width: 210mm;
        height: 297mm;
        margin: 0;
        box-shadow: none;
        page-break-after: always;
        break-after: page;
        overflow: hidden;
      }

      .menu-page:last-child {
        page-break-after: auto;
        break-after: auto;
      }

      .page-frame {
        height: 100%;
        padding: 13mm;
      }
    }
  </style>
</head>
<body>
<main class="menu-document">

@php
/**
 * Grupos de categorías por página (basado en el diseño HTML original):
 *   Página 1 (índices 0-2): Taza Pequeña, Cafés Mediana/Grande, Cafés Fríos
 *   Página 2 (índices 3-7): Bebidas, Bebidas Calientes, Bebidas Heladas, Té en Hebras, Jugos
 *   Página 3 (índices 8+):  Pastelería, Salado
 */
$allCategories = $data['categories'];
$pages = [
    array_slice($allCategories, 0, 3),
    array_slice($allCategories, 3, 5),
    array_slice($allCategories, 8),
];
@endphp

@foreach($pages as $pageIndex => $pageCategories)
@if(!empty($pageCategories))
<section class="menu-page">
<div class="page-frame">

  {{-- ── HEADER ── --}}
  <header class="page-header">
    @if($pageIndex === 0)
    <h1 class="document-title">MENÚ</h1>
    @endif
    <div class="logo-band">
      @if(!empty($data['assets']['divider']))
      <img class="logo-line" src="{{ $data['assets']['divider'] }}" alt="" />
      @else
      <div></div>
      @endif

      @if(!empty($data['assets']['logo']))
      <img class="logo" src="{{ $data['assets']['logo'] }}" alt="Little Claire" />
      @endif

      @if(!empty($data['assets']['divider']))
      <img class="logo-line logo-line--flip" src="{{ $data['assets']['divider'] }}" alt="" />
      @else
      <div></div>
      @endif
    </div>
  </header>

  {{-- ── SECTIONS ── --}}
  @foreach($pageCategories as $category)
  @php
    $products = $category['products'];
    $catLower = mb_strtolower($category['name']);
    $catNorm  = strtr($catLower, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','ü'=>'u']);

    // Layout detection
    $isSized      = !empty($products)
                    && count($products[0]['variants']) > 1
                    && $products[0]['variants'][0]['label'] !== null;

    $allPrices    = array_unique(array_map(fn($p) => $p['variants'][0]['price'] ?? 0, $products));
    $uniformPrice = (count($allPrices) === 1 && count($products) >= 5)
                    ? number_format((float) array_values($allPrices)[0], 0, ',', '.')
                    : null;
    $uniformHasDesc   = $uniformPrice && !empty(array_filter($products, fn($p) => !empty($p['description'])));
    $uniformLabel     = str_contains($catNorm, 'bebida') ? 'TODAS' : 'TODOS';

    $isCalientes  = str_contains($catNorm, 'caliente');
    $isHeladas    = str_contains($catNorm, 'helada');
    $isJugos      = str_contains($catNorm, 'jugos');
    $isFrios      = str_contains($catNorm, 'frios') || str_contains($catNorm, 'frio');
    $isPasteleria = str_contains($catNorm, 'pasteleria');
    $isCold       = $isFrios || $isHeladas;

    // Icons only on page 1
    $showIcon = ($pageIndex === 0);

    // Columns: 3 for Cafés Fríos and Pastelería; 2 for everything else
    $cols = ($isFrios || $isPasteleria) ? 3 : 2;

    // Extract Licuados from Jugos category
    $licuado      = null;
    $mainProducts = $products;
    if ($isJugos) {
        $licuado = array_values(array_filter($products, function ($p) {
            $n = strtr(mb_strtolower($p['name']), ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u']);
            return str_contains($n, 'licuado');
        }))[0] ?? null;
        $mainProducts = array_values(array_filter($products, function ($p) {
            $n = strtr(mb_strtolower($p['name']), ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u']);
            return !str_contains($n, 'licuado');
        }));
    }
  @endphp

  {{-- ════════════════════════════════════════════════
       JUGOS NATURALES + LICUADOS banner
  ════════════════════════════════════════════════ --}}
  @if($isJugos)

  <section class="menu-section">
    <h2 class="section-title">{{ strtoupper($category['name']) }}</h2>
    @php $half = (int) ceil(count($mainProducts) / 2); @endphp
    <div class="columns two-columns equal-gap">
      @foreach([array_slice($mainProducts,0,$half), array_slice($mainProducts,$half)] as $col)
      <div class="simple-list">
        @foreach($col as $product)
          @if(!empty($product['description']))
          <article class="menu-item menu-item--with-description no-icon">
            <div class="item-copy">
              <h3>{{ strtoupper($product['name']) }}</h3>
              <p>{{ $product['description'] }}</p>
            </div>
            <div class="price-tag">{{ number_format((float)$product['variants'][0]['price'], 0, ',', '.') }}</div>
          </article>
          @else
          <article class="menu-item menu-item--single-line no-icon">
            <h3>{{ strtoupper($product['name']) }}</h3>
            <div class="price-tag">{{ number_format((float)$product['variants'][0]['price'], 0, ',', '.') }}</div>
          </article>
          @endif
        @endforeach
      </div>
      @endforeach
    </div>
  </section>

  @if($licuado)
  <section class="menu-section">
    <h2 class="section-title">LICUADOS</h2>
    <div class="inline-banner">
      <span>{{ strtoupper($licuado['description'] ?? 'HECHO CON FRUTAS DEL DÍA. CONSULTÁ CUÁLES TENEMOS HOY') }}</span>
      <strong>{{ number_format((float)$licuado['variants'][0]['price'], 0, ',', '.') }}</strong>
    </div>
  </section>
  @endif

  {{-- ════════════════════════════════════════════════
       CAFÉS MEDIANA / GRANDE — size-prices layout
  ════════════════════════════════════════════════ --}}
  @elseif($isSized)

  <section class="menu-section">
    <h2 class="section-title">
      {!! str_replace('/', '<span class="divider">|</span>', e($category['name'])) !!}
      @if(!empty($category['description']))
      <small>({{ $category['description'] }})</small>
      @endif
    </h2>
    @php
      $half     = (int) ceil(count($products) / 2);
      $colsSized = [array_slice($products, 0, $half), array_slice($products, $half)];
    @endphp
    <div class="columns two-columns">
      @foreach($colsSized as $col)
      <div class="item-list paired-list">
        @foreach($col as $product)
        <article class="menu-item menu-item--sized menu-item--with-description">
          <div class="item-main">
            @if($showIcon)
            @php $firstImg = $product['variants'][0]['images'][0] ?? null; @endphp
            <div class="item-icon">
              @if($firstImg)
              <img src="{{ $firstImg['pdf_src'] ?? $firstImg['url'] }}" alt="" />
              @endif
            </div>
            @endif
            <div class="item-copy">
              <h3>{{ strtoupper($product['name']) }}</h3>
              @if(!empty($product['description']))
              <p>{{ $product['description'] }}</p>
              @endif
            </div>
          </div>
          <div class="size-prices">
            @foreach($product['variants'] as $variant)
            <span>{{ $variant['label'] }} <strong>{{ number_format((float)$variant['price'], 0, ',', '.') }}</strong></span>
            @endforeach
          </div>
        </article>
        @endforeach
      </div>
      @endforeach
    </div>
  </section>

  {{-- ════════════════════════════════════════════════
       BEBIDAS — uniform price, grid layout (no descriptions)
  ════════════════════════════════════════════════ --}}
  @elseif($uniformPrice && !$uniformHasDesc)

  <section class="menu-section">
    <div class="section-row drinks-row">
      <div class="grow">
        <h2 class="section-title">{{ strtoupper($category['name']) }}</h2>
        @php $thirdCount = (int) ceil(count($products) / 3); @endphp
        <div class="catalog-grid catalog-grid--three">
          @foreach(array_chunk($products, $thirdCount) as $chunk)
          <div class="plain-list">
            @foreach($chunk as $product)
            <p>{{ strtoupper($product['name']) }}</p>
            @endforeach
          </div>
          @endforeach
        </div>
      </div>
      <aside class="price-box">
        <span>{{ $uniformLabel }}</span>
        <strong>$ {{ $uniformPrice }}</strong>
      </aside>
    </div>
  </section>

  {{-- ════════════════════════════════════════════════
       TÉ EN HEBRAS — uniform price, spacious list (some descriptions)
  ════════════════════════════════════════════════ --}}
  @elseif($uniformPrice && $uniformHasDesc)

  <section class="menu-section">
    <div class="section-row">
      <div class="grow">
        <h2 class="section-title">{{ strtoupper($category['name']) }}</h2>
        <div class="plain-list plain-list--spacious">
          @foreach($products as $product)
          <div>
            <p>{{ strtoupper($product['name']) }}</p>
            @if(!empty($product['description']))
            <small>{{ $product['description'] }}</small>
            @endif
          </div>
          @endforeach
        </div>
      </div>
      <aside class="price-box price-box--tall">
        <span>{{ $uniformLabel }}</span>
        <strong>$ {{ $uniformPrice }}</strong>
      </aside>
    </div>
  </section>

  {{-- ════════════════════════════════════════════════
       BEBIDAS CALIENTES — 3 columnas horizontales
  ════════════════════════════════════════════════ --}}
  @elseif($isCalientes)

  <section class="menu-section">
    <h2 class="section-title">{{ strtoupper($category['name']) }}</h2>
    <div class="columns three-columns equal-gap hot-drinks">
      @foreach($products as $product)
      <article class="menu-item menu-item--single-line no-icon">
        <h3>{{ strtoupper($product['name']) }}</h3>
        <div class="price-tag">{{ number_format((float)$product['variants'][0]['price'], 0, ',', '.') }}</div>
      </article>
      @endforeach
    </div>
  </section>

  {{-- ════════════════════════════════════════════════
       BEBIDAS HELADAS — 2 columnas, con descripción, sin ícono
  ════════════════════════════════════════════════ --}}
  @elseif($isHeladas)

  <section class="menu-section">
    <h2 class="section-title">{{ strtoupper($category['name']) }} <span class="section-accent">✶</span></h2>
    <div class="columns two-columns equal-gap">
      @foreach($products as $product)
      <article class="menu-item menu-item--with-description no-icon centered-price">
        <div class="item-copy">
          <h3>{{ strtoupper($product['name']) }}</h3>
          @if(!empty($product['description']))
          <p>{{ $product['description'] }}</p>
          @endif
        </div>
        <div class="price-tag">{{ number_format((float)$product['variants'][0]['price'], 0, ',', '.') }}</div>
      </article>
      @endforeach
    </div>
  </section>

  {{-- ════════════════════════════════════════════════
       PASTELERÍA — 3 columnas grid con header especial
  ════════════════════════════════════════════════ --}}
  @elseif($isPasteleria)

  <section class="menu-section section-flex-header">
    <h2 class="section-title">{{ strtoupper($category['name']) }}</h2>
    @if(!empty($category['description']))
    <p class="section-note">{{ $category['description'] }}</p>
    @endif
    @php $thirdCount = (int) ceil(count($products) / 3); @endphp
    <div class="catalog-grid catalog-grid--three catalog-grid--prices">
      @foreach(array_chunk($products, $thirdCount) as $chunk)
      <div class="simple-list">
        @foreach($chunk as $product)
          @if(!empty($product['description']))
          <article class="menu-item menu-item--with-description no-icon">
            <div class="item-copy">
              <h3>{{ strtoupper($product['name']) }}</h3>
              <p>{{ $product['description'] }}</p>
            </div>
            <div class="price-tag">{{ number_format((float)$product['variants'][0]['price'], 0, ',', '.') }}</div>
          </article>
          @else
          <article class="menu-item menu-item--single-line no-icon">
            <h3>{{ strtoupper($product['name']) }}</h3>
            <div class="price-tag">{{ number_format((float)$product['variants'][0]['price'], 0, ',', '.') }}</div>
          </article>
          @endif
        @endforeach
      </div>
      @endforeach
    </div>
  </section>

  {{-- ════════════════════════════════════════════════
       DEFAULT — Taza Pequeña (con ícono + descripción),
                 Cafés Fríos (con ícono sin descripción),
                 Salado (sin ícono, 2 cols)
  ════════════════════════════════════════════════ --}}
  @else

  @php
    $hasDesc  = !empty(array_filter($products, fn($p) => !empty($p['description'])));
    $half     = max(1, (int) ceil(count($products) / $cols));
    $colChunks = array_chunk($products, $half);
  @endphp
  <section class="menu-section">
    <h2 class="section-title">
      {{ strtoupper($category['name']) }}
      @if($isCold) <span class="section-accent">✶</span> @endif
    </h2>
    <div class="columns {{ $cols === 3 ? 'three-columns simple-grid' : 'two-columns' }} {{ $hasDesc ? 'equal-gap' : '' }}">
      @foreach($colChunks as $col)
      <div class="{{ $hasDesc ? 'item-list compact-list' : 'item-list simple-list' }}">
        @foreach($col as $product)

          {{-- Con descripción --}}
          @if(!empty($product['description']))
            @if($showIcon)
            @php $firstImg = $product['variants'][0]['images'][0] ?? null; @endphp
            <article class="menu-item menu-item--with-description">
              <div class="item-main">
                <div class="item-icon">
                  @if($firstImg)
                  <img src="{{ $firstImg['pdf_src'] ?? $firstImg['url'] }}" alt="" />
                  @endif
                </div>
                <div class="item-copy">
                  <h3>{{ strtoupper($product['name']) }}</h3>
                  <p>{{ $product['description'] }}</p>
                </div>
              </div>
              <div class="price-tag">{{ number_format((float)$product['variants'][0]['price'], 0, ',', '.') }}</div>
            </article>
            @else
            <article class="menu-item menu-item--with-description no-icon">
              <div class="item-copy">
                <h3>{{ strtoupper($product['name']) }}</h3>
                <p>{{ $product['description'] }}</p>
              </div>
              <div class="price-tag">{{ number_format((float)$product['variants'][0]['price'], 0, ',', '.') }}</div>
            </article>
            @endif

          {{-- Sin descripción --}}
          @else
            @if($showIcon)
            @php $firstImg = $product['variants'][0]['images'][0] ?? null; @endphp
            <article class="menu-item menu-item--single-line">
              <div class="item-main">
                <div class="item-icon">
                  @if($firstImg)
                  <img src="{{ $firstImg['pdf_src'] ?? $firstImg['url'] }}" alt="" />
                  @endif
                </div>
                <h3>{{ strtoupper($product['name']) }}</h3>
              </div>
              <div class="price-tag">{{ number_format((float)$product['variants'][0]['price'], 0, ',', '.') }}</div>
            </article>
            @else
            <article class="menu-item menu-item--single-line no-icon">
              <h3>{{ strtoupper($product['name']) }}</h3>
              <div class="price-tag">{{ number_format((float)$product['variants'][0]['price'], 0, ',', '.') }}</div>
            </article>
            @endif
          @endif

        @endforeach
      </div>
      @endforeach
    </div>
  </section>

  @endif
  @endforeach

  {{-- ── FOOTER ── --}}
  <footer class="page-footer">
    <div class="footer-line"></div>
    <div class="footer-social">
      @if(!empty($data['assets']['qr_code']))
      <img class="qr-code" src="{{ $data['assets']['qr_code'] }}" alt="QR Instagram" />
      @endif
      <div class="social-copy">
        <p>SEGUINOS EN INSTAGRAM</p>
        <strong>@littleclaire.bakery</strong>
      </div>
    </div>
    <div class="footer-line"></div>
  </footer>

</div>
</section>
@endif
@endforeach

</main>
</body>
</html>

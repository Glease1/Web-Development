<?php
$query    = get('q');
$region   = get('region');
$maxPrice = (float) get('max_price');
$minPrice = (float) get('min_price');
$month    = get('month');
$guests   = max(1, (int) get('guests') ?: 1);

$results = [];
if ($tm) {
    $results = $tm->searchDestinations($query, $region, $maxPrice);
    $regions = $tm->getRegions();
} else {
    $regions = ['Europe','Asia','Africa','Americas','Oceania','Middle East'];
    // Demo data when DB unavailable
    $results = [
        ['id'=>1,'name'=>'Santorini Hideaway','country'=>'Greece','region'=>'Europe','price'=>2450,'duration_days'=>7,'slug'=>'santorini-hideaway','featured'=>1,'description'=>'Whitewashed villas, private plunge pools, caldera sunsets.','image_url'=>'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?auto=format&fit=crop&w=800&q=80'],
        ['id'=>2,'name'=>'Maldives Overwater Escape','country'=>'Maldives','region'=>'Asia','price'=>5990,'duration_days'=>10,'slug'=>'maldives-overwater-escape','featured'=>1,'description'=>'Overwater bungalows, bioluminescent lagoons, manta rays.','image_url'=>'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=800&q=80'],
        ['id'=>3,'name'=>'Japan in Bloom','country'=>'Japan','region'=>'Asia','price'=>3800,'duration_days'=>14,'slug'=>'japan-in-bloom','featured'=>1,'description'=>'Cherry blossom season, ryokans, Fuji at dawn.','image_url'=>'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=800&q=80'],
        ['id'=>4,'name'=>'Marrakech & the Atlas','country'=>'Morocco','region'=>'Africa','price'=>1950,'duration_days'=>8,'slug'=>'marrakech-atlas','featured'=>1,'description'=>'Riad courtyards, souks, Berber camp under the stars.','image_url'=>'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?auto=format&fit=crop&w=800&q=80'],
        ['id'=>5,'name'=>'Iceland Northern Lights','country'=>'Iceland','region'=>'Europe','price'=>3250,'duration_days'=>7,'slug'=>'iceland-northern-lights','featured'=>1,'description'=>'Geothermal pools, glaciers, and the aurora overhead.','image_url'=>'https://images.unsplash.com/photo-1504829857797-ddff29c27927?auto=format&fit=crop&w=800&q=80'],
        ['id'=>6,'name'=>'Amalfi & the Islands','country'=>'Italy','region'=>'Europe','price'=>2890,'duration_days'=>9,'slug'=>'amalfi-islands','featured'=>1,'description'=>'Cliffside groves, private boat days, Positano at golden hour.','image_url'=>'https://images.unsplash.com/photo-1555881400-74d7acaacd8b?auto=format&fit=crop&w=800&q=80'],
        ['id'=>7,'name'=>'Peru & Machu Picchu','country'=>'Peru','region'=>'Americas','price'=>4100,'duration_days'=>12,'slug'=>'peru-machu-picchu','featured'=>0,'description'=>'Private sunrise at Machu Picchu, the Sacred Valley, Peruvian gastronomy.','image_url'=>'https://images.unsplash.com/photo-1526392060635-9d6019884377?auto=format&fit=crop&w=800&q=80'],
        ['id'=>8,'name'=>'Serengeti Safari','country'=>'Tanzania','region'=>'Africa','price'=>6500,'duration_days'=>10,'slug'=>'serengeti-safari','featured'=>1,'description'=>'The Great Migration, tented camps, Big Five by day, stars by night.','image_url'=>'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80'],
        ['id'=>9,'name'=>'Norway Fjord Journey','country'=>'Norway','region'=>'Europe','price'=>3600,'duration_days'=>8,'slug'=>'norway-fjord-journey','featured'=>0,'description'=>'Dramatic fjords, fishing villages, midnight sun or northern lights.','image_url'=>'https://images.unsplash.com/photo-1520769945061-0a448c463865?auto=format&fit=crop&w=800&q=80'],
        // Budget Breaks
        ['id'=>10,'name'=>'Budapest City Break','country'=>'Hungary','region'=>'Europe','price'=>649,'duration_days'=>5,'slug'=>'budapest-city-break','featured'=>0,'budget'=>true,'description'=>'Thermal baths, ruin bars, and a skyline that rewards every early riser.','image_url'=>'https://images.unsplash.com/photo-1647558584325-e4b923021d76?auto=format&fit=crop&w=800&q=80'],
        ['id'=>11,'name'=>'Porto Wine Trail','country'=>'Portugal','region'=>'Europe','price'=>795,'duration_days'=>6,'slug'=>'porto-wine-trail','featured'=>0,'budget'=>true,'description'=>'Azulejo tiles, ancient port wine cellars, and the Douro Valley at harvest time.','image_url'=>'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?auto=format&fit=crop&w=800&q=80'],
        ['id'=>12,'name'=>'Tbilisi & the Caucasus','country'=>'Georgia','region'=>'Europe','price'=>720,'duration_days'=>7,'slug'=>'tbilisi-caucasus','featured'=>0,'budget'=>true,'description'=>'Ancient fortresses, natural wine bars, and the dramatic Greater Caucasus.','image_url'=>'https://images.unsplash.com/photo-1565008576549-57569a49371d?auto=format&fit=crop&w=800&q=80'],
        ['id'=>13,'name'=>'Albania Riviera','country'=>'Albania','region'=>'Europe','price'=>580,'duration_days'=>7,'slug'=>'albania-riviera','featured'=>0,'budget'=>true,'description'=>'Crystal Ionian coves and a coastline that feels like the Med did fifty years ago.','image_url'=>'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=800&q=80'],
        ['id'=>14,'name'=>'Vietnam Highlights','country'=>'Vietnam','region'=>'Asia','price'=>1895,'duration_days'=>10,'slug'=>'vietnam-highlights','featured'=>0,'budget'=>true,'description'=>'Halong Bay at dawn, Hoi An lanterns at dusk, and street food that changes everything.','image_url'=>'https://images.unsplash.com/photo-1528360983277-13d401cdc186?auto=format&fit=crop&w=800&q=80'],
        // Legendary Journeys
        ['id'=>15,'name'=>"Tolkien's Middle-earth: New Zealand",'country'=>'New Zealand','region'=>'Oceania','price'=>6800,'duration_days'=>14,'slug'=>'tolkiens-middle-earth','featured'=>1,'description'=>'Hobbiton, Mordor, Milford Sound. Middle-earth made breathtakingly real.','image_url'=>'https://images.unsplash.com/photo-1507699622108-4be3abd695ad?auto=format&fit=crop&w=800&q=80'],
        ['id'=>16,'name'=>'Jordan: Petra & Wadi Rum','country'=>'Jordan','region'=>'Middle East','price'=>2800,'duration_days'=>9,'slug'=>'jordan-petra-wadi-rum','featured'=>1,'description'=>'A Wonder of the World by candlelight and a Bedouin camp under a billion stars.','image_url'=>'https://images.unsplash.com/photo-1539768942893-daf53e448371?auto=format&fit=crop&w=800&q=80'],
        ['id'=>17,'name'=>'Bhutan: The Thunder Dragon Kingdom','country'=>'Bhutan','region'=>'Asia','price'=>5500,'duration_days'=>10,'slug'=>'bhutan-thunder-dragon','featured'=>1,'description'=>'The last Himalayan kingdom. A visitor cap. A GDP measured in happiness.','image_url'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=800&q=80'],
        ['id'=>18,'name'=>'Antarctica: The Last Wilderness','country'=>'Antarctica','region'=>'Oceania','price'=>14900,'duration_days'=>14,'slug'=>'antarctica-expedition','featured'=>1,'description'=>'Fewer visitors per year than a Premier League match. Icebergs. Penguins. Silence.','image_url'=>'https://images.unsplash.com/photo-1529258283598-8d6fe60b27f4?auto=format&fit=crop&w=800&q=80'],
        ['id'=>19,'name'=>'India: Taj at Sunrise & Golden Triangle','country'=>'India','region'=>'Asia','price'=>2950,'duration_days'=>12,'slug'=>'india-taj-golden-triangle','featured'=>1,'description'=>'The Taj at first light. Jaipur\'s pink palaces. Delhi\'s 3,000 years of history.','image_url'=>'https://images.unsplash.com/photo-1564507592333-c60657eea523?auto=format&fit=crop&w=800&q=80'],
        // Americas
        ['id'=>20,'name'=>'Patagonia: Torres del Paine','country'=>'Chile','region'=>'Americas','price'=>5200,'duration_days'=>10,'slug'=>'patagonia-torres-del-paine','featured'=>1,'description'=>'Granite towers at sunrise, calving glaciers, condors overhead. The earth at its most dramatic.','image_url'=>'https://images.unsplash.com/photo-1658312627438-30d1299683fd?auto=format&fit=crop&w=800&q=80'],
        ['id'=>21,'name'=>'Cuba: Havana & the Oriente','country'=>'Cuba','region'=>'Americas','price'=>1850,'duration_days'=>8,'slug'=>'cuba-havana-oriente','featured'=>0,'description'=>'Pastel facades, revolutionary murals, and a music scene that bleeds through every open window.','image_url'=>'https://images.unsplash.com/photo-1570299437488-d430e1e677c7?auto=format&fit=crop&w=800&q=80'],
        ['id'=>22,'name'=>'Galápagos Islands Expedition','country'=>'Ecuador','region'=>'Americas','price'=>7200,'duration_days'=>10,'slug'=>'galapagos-expedition','featured'=>1,'description'=>'Sea lions that haven\'t learned fear. Blue-footed boobies three feet from your boots. Darwin\'s islands unchanged.','image_url'=>'https://images.unsplash.com/photo-1696772686504-09010046b1d3?auto=format&fit=crop&w=800&q=80'],
        ['id'=>23,'name'=>'Colombia: Cartagena & Coffee Country','country'=>'Colombia','region'=>'Americas','price'=>1750,'duration_days'=>9,'slug'=>'colombia-cartagena-coffee','featured'=>0,'description'=>'Walled city at dusk, cloud-forest coffee farms, and wax-palm valleys in the Zona Cafetera.','image_url'=>'https://images.unsplash.com/photo-1534943441045-1009d7cb0bb9?auto=format&fit=crop&w=800&q=80'],
        // North America
        ['id'=>24,'name'=>'New York City: Icons & Neighbourhoods','country'=>'USA','region'=>'Americas','price'=>2650,'duration_days'=>8,'slug'=>'new-york-city','featured'=>0,'description'=>'Eight nights in the city that never sleeps — Central Park, the High Line, Brooklyn, Broadway, and the best slice of pizza of your life.','image_url'=>'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?auto=format&fit=crop&w=800&q=80'],
        ['id'=>25,'name'=>'Canadian Rockies: Banff to Jasper','country'=>'Canada','region'=>'Americas','price'=>3450,'duration_days'=>10,'slug'=>'canadian-rockies-banff-jasper','featured'=>1,'description'=>'Moraine Lake at sunrise, the Icefields Parkway, grizzlies in the valley, and the most spectacular road on earth.','image_url'=>'https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&w=800&q=80'],
        ['id'=>26,'name'=>'American Southwest: The Big Five Parks','country'=>'USA','region'=>'Americas','price'=>2950,'duration_days'=>10,'slug'=>'american-southwest-parks','featured'=>0,'description'=>'Zion, Bryce, Capitol Reef, Canyonlands, and Arches in ten days — timed perfectly so you hit every park in the right light.','image_url'=>'https://images.unsplash.com/photo-1575408264798-b50b252663e6?auto=format&fit=crop&w=800&q=80'],
    ];
    // Apply basic filter
    if ($query) {
        $q = strtolower($query);
        $results = array_filter($results, fn($d) =>
            str_contains(strtolower($d['name']), $q) ||
            str_contains(strtolower($d['country']), $q)
        );
    }
    if ($region) {
        $results = array_filter($results, fn($d) => strtolower($d['region']) === strtolower($region));
    }
    if ($maxPrice > 0) {
        $results = array_filter($results, fn($d) => (float)$d['price'] <= $maxPrice);
    }
    if ($minPrice > 0) {
        $results = array_filter($results, fn($d) => (float)$d['price'] >= $minPrice);
    }
    $results = array_values($results);
}

$hasFilters = $query || $region || $maxPrice > 0 || $minPrice > 0;
$totalCount = count($results);
?>

<div class="page-header">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="/?page=home">Home</a>
      <span class="breadcrumb__sep" aria-hidden="true">/</span>
      <span class="breadcrumb__current">Destinations</span>
    </nav>
    <span class="eyebrow page-header__eyebrow">Explore</span>
    <h1><?= $query ? 'Results for &ldquo;' . h($query) . '&rdquo;' : ($region ? h($region) : 'All Destinations') ?></h1>
    <p class="lead"><?= $totalCount ?> destination<?= $totalCount !== 1 ? 's' : '' ?> found<?= $region ? ' in ' . h($region) : '' ?></p>
  </div>
</div>

<div class="search-layout">

  <!-- Sidebar filters -->
  <aside class="search-filters" aria-label="Search filters">
    <h3>Refine Results</h3>
    <form method="get" action="/">
      <input type="hidden" name="page" value="search_results">
      <div class="filter-section">
        <h4>Search</h4>
        <input class="form-input" type="text" name="q" placeholder="Destination, country…" value="<?= h($query) ?>">
      </div>
      <div class="filter-section">
        <h4>Region</h4>
        <select class="form-select" name="region">
          <option value="">All Regions</option>
          <?php foreach ($regions as $r): ?>
          <option value="<?= h($r) ?>" <?= $region === $r ? 'selected' : '' ?>><?= h($r) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="filter-section">
        <h4>Max Budget (per person)</h4>
        <select class="form-select" name="max_price">
          <option value="">Any max</option>
          <?php foreach ([1000,2000,3000,5000,8000,10000] as $p): ?>
          <option value="<?= $p ?>" <?= (int)$maxPrice === $p ? 'selected' : '' ?>>Up to <?= formatPrice((float)$p) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="filter-section">
        <h4>Min Budget (per person)</h4>
        <select class="form-select" name="min_price">
          <option value="">No minimum</option>
          <?php foreach ([1000,3000,5000,10000] as $p): ?>
          <option value="<?= $p ?>" <?= (int)$minPrice === $p ? 'selected' : '' ?>><?= formatPrice((float)$p) ?>+</option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn--navy" style="width:100%;justify-content:center;">Apply Filters</button>
      <?php if ($hasFilters): ?>
      <a href="/?page=search_results" class="btn btn--outline" style="width:100%;justify-content:center;margin-top:.5rem;">Clear Filters</a>
      <?php endif; ?>
    </form>
  </aside>

  <!-- Results grid -->
  <div>
    <!-- Quick-filter pills -->
    <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1.25rem;">
      <a href="/?page=search_results" class="btn btn--sm <?= !$region && !$maxPrice && !$minPrice && !$query ? 'btn--navy' : 'btn--outline' ?>" style="font-size:.78rem;">All Trips</a>
      <a href="/?page=search_results&max_price=1000" class="btn btn--sm <?= (int)$maxPrice===1000 && !$minPrice ? 'btn--navy' : 'btn--outline' ?>" style="font-size:.78rem;">Budget <span style="opacity:.7;">(under £1k)</span></a>
      <a href="/?page=search_results&min_price=1000&max_price=4999" class="btn btn--sm <?= (int)$minPrice===1000 && (int)$maxPrice===4999 ? 'btn--navy' : 'btn--outline' ?>" style="font-size:.78rem;">Mid-Range <span style="opacity:.7;">(£1k–£5k)</span></a>
      <a href="/?page=search_results&min_price=5000" class="btn btn--sm <?= (int)$minPrice===5000 && !$maxPrice ? 'btn--navy' : 'btn--outline' ?>" style="font-size:.78rem;">Premium <span style="opacity:.7;">(£5k+)</span></a>
      <a href="/?page=search_results&region=Europe" class="btn btn--sm <?= $region==='Europe' && !$minPrice && !$maxPrice ? 'btn--navy' : 'btn--outline' ?>" style="font-size:.78rem;">Europe</a>
      <a href="/?page=search_results&region=Asia" class="btn btn--sm <?= $region==='Asia' && !$minPrice && !$maxPrice ? 'btn--navy' : 'btn--outline' ?>" style="font-size:.78rem;">Asia</a>
      <a href="/?page=search_results&region=Africa" class="btn btn--sm <?= $region==='Africa' && !$minPrice && !$maxPrice ? 'btn--navy' : 'btn--outline' ?>" style="font-size:.78rem;">Africa</a>
      <a href="/?page=search_results&region=Americas" class="btn btn--sm <?= $region==='Americas' && !$minPrice && !$maxPrice ? 'btn--navy' : 'btn--outline' ?>" style="font-size:.78rem;">Americas</a>
      <a href="/?page=search_results&region=Oceania" class="btn btn--sm <?= $region==='Oceania' && !$minPrice && !$maxPrice ? 'btn--navy' : 'btn--outline' ?>" style="font-size:.78rem;">Oceania</a>
      <a href="/?page=search_results&region=Middle East" class="btn btn--sm <?= $region==='Middle East' && !$minPrice && !$maxPrice ? 'btn--navy' : 'btn--outline' ?>" style="font-size:.78rem;">Middle East</a>
    </div>
    <p class="search-results__count">
      Showing <strong><?= $totalCount ?></strong> destination<?= $totalCount !== 1 ? 's' : '' ?>
      <?= $region ? ' in <strong>' . h($region) . '</strong>' : '' ?>
      <?= $query  ? ' matching &ldquo;<strong>' . h($query) . '</strong>&rdquo;' : '' ?>
    </p>

    <?php if (empty($results)): ?>
    <div class="no-results">
      <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <h3>No destinations found</h3>
      <p>Try adjusting your filters, or <a href="/?page=contact" style="color:var(--navy);text-decoration:underline;">speak to a consultant</a> — we may have something perfect that isn’t listed yet.</p>
    </div>
    <?php else: ?>
    <div class="cards-grid">
      <?php foreach ($results as $dest): ?>
      <article class="card">
        <a href="/?page=booking_details&id=<?= (int)$dest['id'] ?>" class="card__image-wrap" tabindex="-1" aria-hidden="true">
          <img src="<?= h($dest['image_url']) ?>" alt="<?= h($dest['name']) ?>" class="card__image" loading="lazy" width="800" height="600">
          <?php if ($dest['featured']): ?><span class="card__badge">Featured</span><?php endif; ?>
          <?php if (!empty($dest['region'])): ?><span class="card__region"><?= h($dest['region']) ?></span><?php endif; ?>
          <span class="card__duration">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <?= (int)$dest['duration_days'] ?> nights
          </span>
        </a>
        <div class="card__body">
          <div class="card__meta">
            <span class="card__location">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <?= h($dest['country']) ?>
            </span>
          </div>
          <h3 class="card__title"><a href="/?page=booking_details&id=<?= (int)$dest['id'] ?>"><?= h($dest['name']) ?></a></h3>
          <p class="card__excerpt"><?= h($dest['description']) ?></p>
          <div class="card__footer">
            <div class="card__price">
              <span class="card__price-from">from</span>
              <span class="card__price-value"><?= formatPrice((float)$dest['price']) ?></span>
              <span class="card__price-per">pp</span>
            </div>
            <a href="/?page=booking_details&id=<?= (int)$dest['id'] ?>" class="btn btn--sm btn--navy">View Trip</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

</div>

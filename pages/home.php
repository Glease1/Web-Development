<?php
// Handle newsletter signup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'newsletter') {
    verifyCsrf();
    $nlEmail = post('email');
    if (filter_var($nlEmail, FILTER_VALIDATE_EMAIL)) {
        // In production: save to mailing list / send to ESP
        flash('success', 'You\'re on the list! Expect your first dispatch within a week.');
    } else {
        flash('error', 'Please enter a valid email address.');
    }
    redirect('home');
}

// Fetch featured destinations
$featured = $tm ? $tm->getFeaturedDestinations(6) : [];
$regions  = $tm ? $tm->getRegions() : [];

// Demo destinations when DB is unavailable
if (empty($featured)) {
    $featured = [
        ['id'=>1,'name'=>'Santorini Hideaway','country'=>'Greece','region'=>'Europe',
         'price'=>2450,'duration_days'=>7,'slug'=>'santorini-hideaway','featured'=>1,
         'description'=>'Whitewashed villas perched above the caldera, private plunge pools, and sunsets that outlast every expectation.',
         'image_url'=>'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?auto=format&fit=crop&w=800&q=80'],
        ['id'=>2,'name'=>'Maldives Overwater Escape','country'=>'Maldives','region'=>'Asia',
         'price'=>5990,'duration_days'=>10,'slug'=>'maldives-overwater-escape','featured'=>1,
         'description'=>'Drift between overwater bungalows, bioluminescent lagoons, and reefs teeming with manta rays.',
         'image_url'=>'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=800&q=80'],
        ['id'=>3,'name'=>'Japan in Bloom','country'=>'Japan','region'=>'Asia',
         'price'=>3800,'duration_days'=>14,'slug'=>'japan-in-bloom','featured'=>1,
         'description'=>'Cherry blossom season in Kyoto, a dawn ascent of Fuji, and ryokans where time deliberately slows.',
         'image_url'=>'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=800&q=80'],
        ['id'=>4,'name'=>'Marrakech & the Atlas','country'=>'Morocco','region'=>'Africa',
         'price'=>1950,'duration_days'=>8,'slug'=>'marrakech-atlas','featured'=>1,
         'description'=>'Riad courtyards, souks that require no map, and starlit dinners in a Berber camp beneath the High Atlas.',
         'image_url'=>'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?auto=format&fit=crop&w=800&q=80'],
        ['id'=>5,'name'=>'Iceland Northern Lights','country'=>'Iceland','region'=>'Europe',
         'price'=>3250,'duration_days'=>7,'slug'=>'iceland-northern-lights','featured'=>1,
         'description'=>'Geothermal springs, glacier walks, and the aurora borealis dancing overhead on dark winter nights.',
         'image_url'=>'https://images.unsplash.com/photo-1504829857797-ddff29c27927?auto=format&fit=crop&w=800&q=80'],
        ['id'=>6,'name'=>'Amalfi & the Islands','country'=>'Italy','region'=>'Europe',
         'price'=>2890,'duration_days'=>9,'slug'=>'amalfi-islands','featured'=>1,
         'description'=>'Cliffside lemon groves, private boat days to Positano, and tables so close to the sea the spray seasons your wine.',
         'image_url'=>'https://images.unsplash.com/photo-1555881400-74d7acaacd8b?auto=format&fit=crop&w=800&q=80'],
    ];
}

$demoRegions = [
    ['name'=>'Europe',   'img'=>'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?auto=format&fit=crop&w=300&q=75'],
    ['name'=>'Asia',     'img'=>'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=300&q=75'],
    ['name'=>'Africa',   'img'=>'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=300&q=75'],
    ['name'=>'Americas', 'img'=>'https://images.unsplash.com/photo-1526392060635-9d6019884377?auto=format&fit=crop&w=300&q=75'],
    ['name'=>'Oceania',  'img'=>'https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?auto=format&fit=crop&w=300&q=75'],
    ['name'=>'Middle East','img'=>'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=300&q=75'],
];

$budget = [
    ['id'=>10,'name'=>'Budapest City Break','country'=>'Hungary','region'=>'Europe','price'=>649,'duration_days'=>5,
     'description'=>'Thermal baths, ruin bars, and a skyline that rewards every early riser.',
     'image_url'=>'https://images.unsplash.com/photo-1647558584325-e4b923021d76?auto=format&fit=crop&w=800&q=80'],
    ['id'=>11,'name'=>'Porto Wine Trail','country'=>'Portugal','region'=>'Europe','price'=>795,'duration_days'=>6,
     'description'=>'Azulejo tiles, ancient port wine cellars, and the Douro Valley at harvest time.',
     'image_url'=>'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?auto=format&fit=crop&w=800&q=80'],
    ['id'=>12,'name'=>'Tbilisi & the Caucasus','country'=>'Georgia','region'=>'Europe','price'=>720,'duration_days'=>7,
     'description'=>'Ancient fortresses, natural wine bars, and the dramatic Greater Caucasus mountains.',
     'image_url'=>'https://images.unsplash.com/photo-1565008576549-57569a49371d?auto=format&fit=crop&w=800&q=80'],
    ['id'=>13,'name'=>'Albania Riviera','country'=>'Albania','region'=>'Europe','price'=>580,'duration_days'=>7,
     'description'=>'Crystal Ionian coves and a coastline that feels like the Med did fifty years ago.',
     'image_url'=>'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=800&q=80'],
    ['id'=>14,'name'=>'Vietnam Highlights','country'=>'Vietnam','region'=>'Asia','price'=>1895,'duration_days'=>10,
     'description'=>'Halong Bay at dawn, Hoi An lanterns at dusk, and street food that changes everything.',
     'image_url'=>'https://images.unsplash.com/photo-1528360983277-13d401cdc186?auto=format&fit=crop&w=800&q=80'],
];

$luxury = [
    ['id'=>15,'name'=>"Tolkien's Middle-earth: New Zealand",'country'=>'New Zealand','region'=>'Oceania','price'=>6800,'duration_days'=>14,
     'badge'=>'Epic Journey',
     'description'=>'Hobbiton, the volcanic slopes of Mordor, Milford Sound fjords. Middle-earth made breathtakingly real.',
     'image_url'=>'https://images.unsplash.com/photo-1507699622108-4be3abd695ad?auto=format&fit=crop&w=800&q=80'],
    ['id'=>16,'name'=>'Jordan: Petra & Wadi Rum','country'=>'Jordan','region'=>'Middle East','price'=>2800,'duration_days'=>9,
     'badge'=>'7 Wonders',
     'description'=>'A Wonder of the World revealed by candlelight. A Bedouin camp under a billion stars in Wadi Rum.',
     'image_url'=>'https://images.unsplash.com/photo-1539768942893-daf53e448371?auto=format&fit=crop&w=800&q=80'],
    ['id'=>17,'name'=>'Bhutan: The Thunder Dragon Kingdom','country'=>'Bhutan','region'=>'Asia','price'=>5500,'duration_days'=>10,
     'badge'=>'Ultra-Exclusive',
     'description'=>"The last Himalayan kingdom with a constitutional visitor cap. Tiger's Nest at dawn. GDP measured in happiness.",
     'image_url'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=800&q=80'],
    ['id'=>18,'name'=>'Antarctica: The Last Wilderness','country'=>'Antarctica','region'=>'Oceania','price'=>14900,'duration_days'=>14,
     'badge'=>'Bucket List',
     'description'=>'Fewer visitors per year than a single Premier League match. Continent-sized icebergs. 200,000 penguins. Silence.',
     'image_url'=>'https://images.unsplash.com/photo-1529258283598-8d6fe60b27f4?auto=format&fit=crop&w=800&q=80'],
    ['id'=>19,'name'=>'India: Taj at Sunrise & the Golden Triangle','country'=>'India','region'=>'Asia','price'=>2950,'duration_days'=>12,
     'badge'=>'7 Wonders',
     'description'=>'The Taj Mahal at first light — marble so luminous it seems lit from within. Jaipur. Delhi. Three thousand years.',
     'image_url'=>'https://images.unsplash.com/photo-1564507592333-c60657eea523?auto=format&fit=crop&w=800&q=80'],
];
?>

<!-- ===== HERO ===== -->
<?php renderFlash(); ?>
<section class="hero" aria-label="Hero">

  <div class="hero__content">
    <div class="animate-in">
      <div class="hero__badge">
        <span class="hero__badge-dot"></span>
        Award-winning bespoke travel since 2003
      </div>
    </div>

    <h1 class="animate-in animate-in--delay-1">
      Journeys That<br><em>Define You</em>
    </h1>

    <p class="hero__sub animate-in animate-in--delay-2">
      Expertly crafted escapes to over 120 countries. Every detail considered,
      every moment extraordinary.
    </p>

    <!-- Search bar -->
    <form class="search-bar animate-in animate-in--delay-3" method="get" action="/" role="search" aria-label="Search destinations">
      <input type="hidden" name="page" value="search_results">
      <div class="search-bar__field">
        <label class="search-bar__label" for="hero-dest">Destination</label>
        <input class="search-bar__input" id="hero-dest" type="text" name="q" placeholder="Where would you like to go?" autocomplete="off">
      </div>
      <div class="search-bar__field">
        <label class="search-bar__label" for="hero-month">When</label>
        <select class="search-bar__select" id="hero-month" name="month">
          <option value="">Any time</option>
          <?php
          $months = ['January','February','March','April','May','June',
                     'July','August','September','October','November','December'];
          $currentMonth = (int) date('n');
          for ($i = 0; $i < 18; $i++):
            $m = (($currentMonth - 1 + $i) % 12);
            $y = date('Y') + (int)(($currentMonth - 1 + $i) / 12);
            $label = $months[$m] . ' ' . $y;
            $val   = str_pad((string)($m+1), 2, '0', STR_PAD_LEFT) . '-' . $y;
          ?>
          <option value="<?= h($val) ?>"><?= h($label) ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="search-bar__field">
        <label class="search-bar__label" for="hero-guests">Travellers</label>
        <select class="search-bar__select" id="hero-guests" name="guests">
          <option value="1">1 person</option>
          <option value="2" selected>2 people</option>
          <option value="3">3 people</option>
          <option value="4">4 people</option>
          <option value="5">5 people</option>
          <option value="6">6+ people</option>
        </select>
      </div>
      <button type="submit" class="btn btn--gold">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        Search
      </button>
    </form>

    <!-- Trust bar -->
    <div class="hero__trust animate-in animate-in--delay-3">
      <span class="hero__trust-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        ABTA &amp; ATOL Protected
      </span>
      <span class="hero__trust-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        50,000+ Happy Travellers
      </span>
      <span class="hero__trust-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Est. 2003
      </span>
      <span class="hero__trust-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        120+ Countries
      </span>
    </div>
  </div>

  <div class="hero__scroll-hint" aria-hidden="true">
    <span>Scroll</span>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
  </div>
</section>

<!-- ===== FEATURED DESTINATIONS ===== -->
<section class="section" aria-labelledby="destinations-heading">
  <div class="container">
    <div class="section__header">
      <span class="eyebrow">Featured Escapes</span>
      <h2 id="destinations-heading">Hand-Picked for Extraordinary Minds</h2>
      <p>Each itinerary is curated by our in-house specialists who’ve slept in the best rooms, eaten at the right tables, and earned the trust of the finest operators worldwide.</p>
    </div>

    <div class="cards-grid">
      <?php foreach ($featured as $dest): ?>
      <article class="card">
        <a href="/?page=booking_details&id=<?= (int)$dest['id'] ?>" class="card__image-wrap" tabindex="-1" aria-hidden="true">
          <img
            src="<?= h($dest['image_url']) ?>"
            alt="<?= h($dest['name']) ?>, <?= h($dest['country']) ?>"
            class="card__image"
            loading="lazy"
            width="800" height="600"
          >
          <?php if ($dest['featured']): ?>
          <span class="card__badge">Featured</span>
          <?php endif; ?>
          <?php if (!empty($dest['region'])): ?>
          <span class="card__region"><?= h($dest['region']) ?></span>
          <?php endif; ?>
          <span class="card__duration">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
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
          <h3 class="card__title">
            <a href="/?page=booking_details&id=<?= (int)$dest['id'] ?>"><?= h($dest['name']) ?></a>
          </h3>
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

    <div class="text-center mt-12">
      <a href="/?page=search_results" class="btn btn--outline btn--lg">Browse All Destinations</a>
    </div>
  </div>
</section>

<!-- ===== LEGENDARY JOURNEYS ===== -->
<section class="section" style="background:var(--navy);" aria-labelledby="legendary-heading">
  <div class="container">
    <div class="section__header">
      <span class="eyebrow" style="color:var(--gold);">Legendary Journeys</span>
      <h2 id="legendary-heading" style="color:#fff;">The Trips People Spend Decades Dreaming Of</h2>
      <p style="color:rgba(255,255,255,.7);">Seven Wonders. Tolkien's Middle-earth. The last great wilderness. These are the journeys that change people — our specialists have done every one of them. Let us take you.</p>
    </div>
    <div class="cards-grid">
      <?php foreach ($luxury as $dest): ?>
      <article class="card" style="background:#1a3350;border-color:rgba(200,169,110,.2);">
        <a href="/?page=booking_details&id=<?= (int)$dest['id'] ?>" class="card__image-wrap" tabindex="-1" aria-hidden="true">
          <img src="<?= h($dest['image_url']) ?>" alt="<?= h($dest['name']) ?>" class="card__image" loading="lazy" width="800" height="600">
          <span class="card__badge" style="background:var(--gold);color:var(--navy);"><?= h($dest['badge']) ?></span>
          <span class="card__region"><?= h($dest['region']) ?></span>
          <span class="card__duration"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><?= (int)$dest['duration_days'] ?> nights</span>
        </a>
        <div class="card__body">
          <div class="card__meta"><span class="card__location" style="color:rgba(255,255,255,.55);"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><?= h($dest['country']) ?></span></div>
          <h3 class="card__title" style="color:#fff;"><a href="/?page=booking_details&id=<?= (int)$dest['id'] ?>" style="color:#fff;"><?= h($dest['name']) ?></a></h3>
          <p class="card__excerpt" style="color:rgba(255,255,255,.65);"><?= h($dest['description']) ?></p>
          <div class="card__footer">
            <div class="card__price"><span class="card__price-from" style="color:rgba(255,255,255,.5);">from</span><span class="card__price-value" style="color:var(--gold);"><?= formatPrice((float)$dest['price']) ?></span><span class="card__price-per" style="color:rgba(255,255,255,.5);">pp</span></div>
            <a href="/?page=booking_details&id=<?= (int)$dest['id'] ?>" class="btn btn--sm" style="background:var(--gold);color:var(--navy);border-color:var(--gold);">View Trip</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-12">
      <a href="/?page=search_results" class="btn btn--outline btn--lg" style="border-color:var(--gold);color:var(--gold);">Explore All Journeys</a>
    </div>
  </div>
</section>

<!-- ===== BUDGET BREAKS ===== -->
<section class="section section--cream" aria-labelledby="budget-heading">
  <div class="container">
    <div class="section__header">
      <span class="eyebrow" style="color:var(--success-text);">Budget Breaks</span>
      <h2 id="budget-heading">Extraordinary Travel, Exceptional Value</h2>
      <p>Not every dream trip needs to break the bank. These hand-picked breaks deliver the Meridian experience — curation, character, and care — from under £1,000 per person.</p>
    </div>
    <div class="cards-grid">
      <?php foreach ($budget as $dest): ?>
      <article class="card">
        <a href="/?page=booking_details&id=<?= (int)$dest['id'] ?>" class="card__image-wrap" tabindex="-1" aria-hidden="true">
          <img src="<?= h($dest['image_url']) ?>" alt="<?= h($dest['name']) ?>" class="card__image" loading="lazy" width="800" height="600">
          <span class="card__badge" style="background:#2a7a4b;">Budget Break</span>
          <span class="card__region"><?= h($dest['region']) ?></span>
          <span class="card__duration"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><?= (int)$dest['duration_days'] ?> nights</span>
        </a>
        <div class="card__body">
          <div class="card__meta"><span class="card__location"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><?= h($dest['country']) ?></span></div>
          <h3 class="card__title"><a href="/?page=booking_details&id=<?= (int)$dest['id'] ?>"><?= h($dest['name']) ?></a></h3>
          <p class="card__excerpt"><?= h($dest['description']) ?></p>
          <div class="card__footer">
            <div class="card__price"><span class="card__price-from">from</span><span class="card__price-value"><?= formatPrice((float)$dest['price']) ?></span><span class="card__price-per">pp</span></div>
            <a href="/?page=booking_details&id=<?= (int)$dest['id'] ?>" class="btn btn--sm btn--navy">View Trip</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-12">
      <a href="/?page=search_results&max_price=1000" class="btn btn--outline btn--lg">See All Budget Breaks</a>
    </div>
  </div>
</section>
<section class="section section--cream" aria-labelledby="usp-heading">
  <div class="container">
    <div class="section__header center">
      <span class="eyebrow">The Meridian Difference</span>
      <h2 id="usp-heading">More Than a Holiday Company</h2>
    </div>
    <div class="usp-grid">

      <div class="usp-item">
        <div class="usp-item__icon" aria-hidden="true">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <h3>Dedicated Travel Consultant</h3>
        <p>One expert handles your entire journey — from first enquiry to safe return. No call centres, no hand-offs.</p>
      </div>

      <div class="usp-item">
        <div class="usp-item__icon" aria-hidden="true">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
        </div>
        <h3>Fully Protected &amp; Bonded</h3>
        <p>Every booking is covered under ABTA and ATOL. Your money is safe, whatever happens.</p>
      </div>

      <div class="usp-item">
        <div class="usp-item__icon" aria-hidden="true">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/>
            <line x1="2" y1="12" x2="22" y2="12"/>
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
          </svg>
        </div>
        <h3>On-the-Ground Expertise</h3>
        <p>Our team has personally visited every destination we sell. When we recommend a hotel, it’s because we’ve stayed there.</p>
      </div>

      <div class="usp-item">
        <div class="usp-item__icon" aria-hidden="true">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
          </svg>
        </div>
        <h3>Genuinely Bespoke</h3>
        <p>We don’t do off-the-shelf. Every itinerary is built around you — your interests, pace, budget, and those little details that matter most.</p>
      </div>

    </div>
  </div>
</section>

<!-- ===== REGIONS ===== -->
<section class="section" aria-labelledby="regions-heading">
  <div class="container">
    <div class="section__header">
      <span class="eyebrow">Explore the World</span>
      <h2 id="regions-heading">Where Would You Like to Go?</h2>
    </div>
    <div class="regions-strip" role="list">
      <?php foreach ($demoRegions as $region): ?>
      <a href="/?page=search_results&region=<?= urlencode($region['name']) ?>" class="region-pill" role="listitem">
        <div class="region-pill__img">
          <img
            src="<?= h($region['img']) ?>"
            alt="<?= h($region['name']) ?> destinations"
            width="100" height="100"
            loading="lazy"
            style="width:100%;height:100%;object-fit:cover;"
          >
        </div>
        <span class="region-pill__name"><?= h($region['name']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== STATS BAR ===== -->
<div class="stats-bar" aria-label="Company statistics">
  <div class="container">
    <div class="stats-grid">
      <div>
        <span class="stat__value">120<span>+</span></span>
        <span class="stat__label">Countries</span>
      </div>
      <div>
        <span class="stat__value">50<span>k+</span></span>
        <span class="stat__label">Happy Travellers</span>
      </div>
      <div>
        <span class="stat__value">21<span></span></span>
        <span class="stat__label">Years in Business</span>
      </div>
      <div>
        <span class="stat__value">98<span>%</span></span>
        <span class="stat__label">Would Recommend Us</span>
      </div>
    </div>
  </div>
</div>

<!-- ===== TESTIMONIALS ===== -->
<section class="section section--cream" aria-labelledby="testimonials-heading">
  <div class="container">
    <div class="section__header center">
      <span class="eyebrow">Client Stories</span>
      <h2 id="testimonials-heading">Heard From Our Travellers</h2>
    </div>
    <div class="testimonials-grid">

      <div class="testimonial">
        <div class="testimonial__stars" aria-label="5 stars">★★★★★</div>
        <p class="testimonial__quote">
          Meridian arranged our honeymoon in Japan during cherry blossom season
          — Kyoto, Hakone, a private ryokan in the mountains. Every single detail was
          thought of before we even had to ask. We’ve travelled with them twice since.
        </p>
        <div class="testimonial__author">
          <div class="testimonial__avatar">R</div>
          <div>
            <span class="testimonial__name">Rachel &amp; Tom Whitfield</span>
            <span class="testimonial__detail">Bournemouth — Japan, April 2024</span>
          </div>
        </div>
      </div>

      <div class="testimonial">
        <div class="testimonial__stars" aria-label="5 stars">★★★★★</div>
        <p class="testimonial__quote">
          We’d been putting off an African safari for years, assuming it would be
          impossibly complicated to plan. Meridian made it look effortless. The
          tented camp in the Serengeti was unlike anything we’d imagined.
        </p>
        <div class="testimonial__author">
          <div class="testimonial__avatar">J</div>
          <div>
            <span class="testimonial__name">James &amp; Claire Ashworth</span>
            <span class="testimonial__detail">Edinburgh — Tanzania, September 2023</span>
          </div>
        </div>
      </div>

      <div class="testimonial">
        <div class="testimonial__stars" aria-label="5 stars">★★★★★</div>
        <p class="testimonial__quote">
          My 60th birthday trip to Peru exceeded every expectation. The private
          sunrise at Machu Picchu is something I will carry with me for the rest
          of my life. Thank you, Meridian.
        </p>
        <div class="testimonial__author">
          <div class="testimonial__avatar">S</div>
          <div>
            <span class="testimonial__name">Susan Kavanagh</span>
            <span class="testimonial__detail">Bristol — Peru, June 2024</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ===== AWARDS STRIP ===== -->
<section class="section section--sm" style="border-top:1px solid var(--border-light);border-bottom:1px solid var(--border-light);" aria-label="Awards">
  <div class="container">
    <div class="flex-c" style="justify-content:center;gap:clamp(1.5rem,4vw,4rem);flex-wrap:wrap;opacity:.55;">
      <span style="font-size:.75rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;white-space:nowrap;">Which? Travel<br><span style="color:var(--gold);font-family:var(--font-serif);font-size:1.1rem;font-weight:700;letter-spacing:0;">Best Operator 2023</span></span>
      <span style="font-size:.75rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;white-space:nowrap;">Feefo<br><span style="color:var(--gold);font-family:var(--font-serif);font-size:1.1rem;font-weight:700;letter-spacing:0;">Platinum Trusted Service</span></span>
      <span style="font-size:.75rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;white-space:nowrap;">TTG Awards<br><span style="color:var(--gold);font-family:var(--font-serif);font-size:1.1rem;font-weight:700;letter-spacing:0;">Luxury Specialist 2022</span></span>
      <span style="font-size:.75rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;white-space:nowrap;">Conde Nast Traveller<br><span style="color:var(--gold);font-family:var(--font-serif);font-size:1.1rem;font-weight:700;letter-spacing:0;">Top 50 UK Operators</span></span>
    </div>
  </div>
</section>

<!-- ===== NEWSLETTER ===== -->
<section class="newsletter" aria-labelledby="newsletter-heading">
  <div class="container">
    <div class="newsletter__inner">
      <div class="newsletter__text">
        <span class="eyebrow" style="color:var(--gold);display:block;margin-bottom:.75rem;">Stay Inspired</span>
        <h2 id="newsletter-heading">Travel Ideas in Your Inbox</h2>
        <p>Seasonal itineraries, destination guides, exclusive early-bird offers,
        and the occasional insight from our consultants — no more than twice a month.</p>
      </div>
      <div>
        <form class="newsletter__form" method="post" action="/?page=home">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="newsletter">
          <input type="email" name="email" placeholder="Your email address" required autocomplete="email" aria-label="Email address">
          <button type="submit" class="btn btn--gold">Subscribe</button>
        </form>
        <p class="newsletter__disclaimer">No spam. Unsubscribe at any time. We respect your privacy.</p>
      </div>
    </div>
  </div>
</section>

<!-- Page header -->
<div class="page-header">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="/?page=home">Home</a>
      <span class="breadcrumb__sep" aria-hidden="true">/</span>
      <span class="breadcrumb__current">About Us</span>
    </nav>
    <span class="eyebrow page-header__eyebrow">Our Story</span>
    <h1>More Than a Travel Company</h1>
    <p class="lead">For over two decades we’ve been opening doors that most people don’t know exist.</p>
  </div>
</div>

<!-- About intro -->
<section class="section">
  <div class="container">
    <div class="about-intro">
      <div class="about-intro__image">
        <img
          src="https://images.unsplash.com/photo-1488085061387-422e29b40080?auto=format&fit=crop&w=900&q=80"
          alt="Our consultants planning a custom itinerary"
          class="about-intro__img-main"
          loading="lazy"
          width="900" height="1125"
        >
        <img
          src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=450&q=80"
          alt="Happy travellers on a beach"
          class="about-intro__img-accent"
          loading="lazy"
          width="450" height="450"
        >
      </div>
      <div class="about-intro__text">
        <span class="eyebrow">Who We Are</span>
        <h2>Founded on the Belief That Travel Changes Everything</h2>
        <p>Meridian Horizons was founded in 2003 by former expedition guide Caroline Reid, who believed the travel industry had confused ‘packages’ with ‘experiences’. From a small office in Bath, she began crafting journeys that were genuinely tailored — not cobbled together from brochure pages.</p>
        <p>Twenty-one years on, we’re a team of forty specialists, each with a deeply personal knowledge of the regions they represent. We’ve arranged a marriage proposal at Angkor Wat, a family reunion aboard a private Nile dahabiya, and a solo circuit of Japan’s 88 pilgrimage temples for a retired headmistress from Leeds. Every brief is taken seriously.</p>
        <p>We are proudly independent — not owned by an airline, hotel chain, or private equity firm. Our only obligation is to you.</p>
        <div class="flex-c gap-4" style="margin-top:2rem;">
          <a href="/?page=contact" class="btn btn--navy">Speak to a Consultant</a>
          <a href="/?page=search_results" class="btn btn--outline">Browse Destinations</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Values -->
<section class="section section--cream">
  <div class="container">
    <div class="section__header center">
      <span class="eyebrow">Our Values</span>
      <h2>What We Believe In</h2>
    </div>
    <div class="usp-grid">
      <div class="usp-item">
        <div class="usp-item__icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M1 6v16l7-4 8 4 7-4V2l-7 4-8-4-7 4z"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
        </div>
        <h3>Radical Honesty</h3>
        <p>We’ll tell you if a destination isn’t right for you, or if a cheaper option would serve you better. Our reputation matters more than the commission.</p>
      </div>
      <div class="usp-item">
        <div class="usp-item__icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3>Absolute Responsibility</h3>
        <p>Something goes wrong? We fix it. Every client has a 24-hour emergency line that connects to a human being — not a voicemail.</p>
      </div>
      <div class="usp-item">
        <div class="usp-item__icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        </div>
        <h3>Genuine Sustainability</h3>
        <p>We partner with operators who hold meaningful certifications, and we direct 1% of revenue into community-run conservation projects.</p>
      </div>
      <div class="usp-item">
        <div class="usp-item__icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </div>
        <h3>Lasting Relationships</h3>
        <p>Over 70% of our clients have travelled with us more than once. We keep notes, we remember your preferences, and we celebrate when you return.</p>
      </div>
    </div>
  </div>
</section>

<!-- Team -->
<section class="section" id="team" aria-labelledby="team-heading">
  <div class="container">
    <div class="section__header center">
      <span class="eyebrow">The People Behind Your Journey</span>
      <h2 id="team-heading">Meet the Consultants</h2>
      <p>Real people with real passports. Between them our team has visited over 95 countries and counting.</p>
    </div>
    <div class="team-grid">
      <?php
      $team = [
          ['name'=>'Caroline Reid',  'role'=>'Founder &amp; CEO',         'img'=>'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80'],
          ['name'=>'David Okafor',   'role'=>'Africa &amp; Middle East',   'img'=>'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=400&q=80'],
          ['name'=>'Priya Nair',     'role'=>'Asia &amp; Far East',        'img'=>'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=400&q=80'],
          ['name'=>'Marco Bellini',  'role'=>'Europe &amp; Mediterranean', 'img'=>'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80'],
          ['name'=>'Sarah Thornton', 'role'=>'The Americas',               'img'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=400&q=80'],
          ['name'=>'Luca Marsh',     'role'=>'Polar &amp; Adventure',      'img'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80'],
          ['name'=>'Yuki Tanaka',    'role'=>'Japan &amp; Korea Specialist','img'=>'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=400&q=80'],
          ['name'=>'Ben Walsh',      'role'=>'Honeymoon &amp; Luxury',     'img'=>'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=400&q=80'],
      ];
      foreach ($team as $member):
      ?>
      <div class="team-card">
        <img src="<?= h($member['img']) ?>" alt="<?= h($member['name']) ?>" class="team-card__photo" loading="lazy" width="400" height="400">
        <div class="team-card__name"><?= h($member['name']) ?></div>
        <div class="team-card__role"><?= $member['role'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Awards -->
<section class="section section--cream" id="awards" aria-labelledby="awards-heading">
  <div class="container container--narrow text-center">
    <span class="eyebrow">Recognition</span>
    <h2 id="awards-heading" style="margin-bottom:1rem;">Awards &amp; Accreditations</h2>
    <p class="lead" style="margin-inline:auto;margin-bottom:3rem;">We don’t travel for awards. But we’re quietly proud of these.</p>
    <?php
    $awards = [
        ['year'=>'2024','title'=>'Best UK Luxury Operator',   'body'=>'Which? Travel Awards'],
        ['year'=>'2023','title'=>'Platinum Trusted Service',  'body'=>'Feefo'],
        ['year'=>'2023','title'=>'Top 50 UK Travel Operators','body'=>'Cond&eacute; Nast Traveller'],
        ['year'=>'2022','title'=>'Luxury Specialist of the Year','body'=>'TTG Awards'],
        ['year'=>'2022','title'=>'Best for Africa',           'body'=>'Safari Bookings Awards'],
        ['year'=>'2021','title'=>'Independent Operator of the Year','body'=>'British Travel Awards'],
    ];
    ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.25rem;">
      <?php foreach ($awards as $a): ?>
      <div style="background:var(--white);border:1px solid var(--border-light);border-radius:var(--r-lg);padding:1.5rem;box-shadow:var(--shadow-xs);">
        <div style="font-size:.68rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);margin-bottom:.5rem;"><?= h($a['year']) ?></div>
        <div style="font-family:var(--font-serif);font-size:1rem;color:var(--navy);font-weight:700;margin-bottom:.25rem;"><?= h($a['title']) ?></div>
        <div style="font-size:.8rem;color:var(--text-muted);"><?= $a['body'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section style="background:var(--navy);padding-block:clamp(3rem,6vw,5rem);">
  <div class="container text-center">
    <h2 style="color:var(--white);margin-bottom:1rem;">Ready to Start Planning?</h2>
    <p style="color:rgba(255,255,255,.6);margin-bottom:2rem;margin-inline:auto;">Speak to one of our consultants — no obligation, no pushy sales, just honest advice.</p>
    <a href="/?page=contact" class="btn btn--gold btn--lg">Get in Touch</a>
  </div>
</section>

<div class="page-header">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="/?page=home">Home</a>
      <span class="breadcrumb__sep" aria-hidden="true">/</span>
      <span class="breadcrumb__current">FAQs</span>
    </nav>
    <span class="eyebrow page-header__eyebrow">Help Centre</span>
    <h1>Frequently Asked Questions</h1>
    <p class="lead">Everything you need to know before you go. Can't find your answer? <a href="/?page=contact" style="color:var(--gold);text-decoration:underline;">Just ask us.</a></p>
  </div>
</div>

<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:220px 1fr;gap:var(--sp-12);align-items:start;">

      <!-- Sticky sidebar nav -->
      <nav style="position:sticky;top:calc(var(--nav-h) + 1.5rem);display:flex;flex-direction:column;gap:.35rem;" aria-label="FAQ sections">
        <?php
        $sections = [
            'planning'   => 'Planning Your Trip',
            'pricing'    => 'Pricing & Inclusions',
            'booking'    => 'Making a Booking',
            'changes'    => 'Changes & Cancellations',
            'practicalities' => 'Travel Practicalities',
            'insurance'  => 'Insurance & Protection',
            'ontrip'     => 'While You\'re Away',
        ];
        foreach ($sections as $id => $label):
        ?>
        <a href="#<?= $id ?>" style="font-size:.82rem;font-weight:600;color:var(--text-muted);padding:.4rem .75rem;border-radius:var(--r);transition:all var(--t-fast);"
           onmouseover="this.style.background='var(--cream)';this.style.color='var(--navy)';"
           onmouseout="this.style.background='';this.style.color='var(--text-muted)';">
          <?= $label ?>
        </a>
        <?php endforeach; ?>
        <hr style="border:none;border-top:1px solid var(--border-light);margin:.5rem 0;">
        <a href="/?page=contact" class="btn btn--gold btn--sm" style="text-align:center;justify-content:center;">Ask a Question</a>
      </nav>

      <!-- FAQ content -->
      <div>

        <!-- Planning -->
        <div class="faq-group" id="planning">
          <div class="faq-group-title">Planning Your Trip</div>
          <div class="faq-accordion">

            <details class="faq-item">
              <summary>How far in advance should I start planning?</summary>
              <div class="faq-answer">
                <p>For most destinations, we recommend enquiring at least three to four months before your intended departure. Popular itineraries — safaris during the Great Migration, Japan during cherry blossom season, overwater villas in the Maldives over the Christmas period — can require booking six to twelve months in advance.</p>
                <p>That said, we regularly find availability at short notice. If you have flexibility on dates, get in touch and we'll see what we can do.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>Can I speak to a consultant before committing to anything?</summary>
              <div class="faq-answer">
                <p>Yes — and we actively encourage it. Our consultants are available on <strong>0117 900 0000</strong> Monday to Friday 9am–6pm and Saturday 10am–4pm. Alternatively, <a href="/?page=contact">send us an enquiry</a> and we'll call you back at a time that suits.</p>
                <p>There is absolutely no obligation, and no pushy sales process. We'd rather spend an hour on the phone finding out what you actually want than send you the wrong trip.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>Do you cater for solo travellers?</summary>
              <div class="faq-answer">
                <p>Absolutely. Solo travel is one of our fastest-growing areas. We can arrange private-guide itineraries, small-group adventures, and lodge experiences specifically designed for solo guests. We'll also advise on single-supplement arrangements and which destinations feel most comfortable travelling alone.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>Can you arrange travel for families with young children?</summary>
              <div class="faq-answer">
                <p>Yes — family travel is something we genuinely love planning. We'll factor in child-friendly accommodation, manageable flight durations, activities suited to different ages, and the kind of logistical details (cots, car seats, early dinner times) that make the difference between a holiday and an ordeal.</p>
              </div>
            </details>

          </div>
        </div>

        <!-- Pricing -->
        <div class="faq-group" id="pricing">
          <div class="faq-group-title">Pricing & Inclusions</div>
          <div class="faq-accordion">

            <details class="faq-item">
              <summary>Are prices per person or per party?</summary>
              <div class="faq-answer">
                <p>All prices shown on the website are <strong>per person</strong>, based on two adults sharing accommodation. Single occupancy supplements apply where relevant and will be quoted separately.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>What is included in the quoted price?</summary>
              <div class="faq-answer">
                <p>Unless stated otherwise, our prices include:</p>
                <ul>
                  <li>Return flights from a UK airport (economy class as standard; upgrades available)</li>
                  <li>All accommodation as specified in the itinerary</li>
                  <li>Airport transfers and internal transport</li>
                  <li>Activities and excursions listed in the itinerary</li>
                  <li>24/7 in-destination support from Meridian</li>
                </ul>
                <p>Prices do <strong>not</strong> include travel insurance, visa fees, airport parking, tips, or personal spending money.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>Can I upgrade my flights or hotel?</summary>
              <div class="faq-answer">
                <p>Yes — in fact, we can build your itinerary around your preferred airline, class of travel, and hotel category from the start. Business and first class flights, private jet charters, and upgrades from superior rooms to suites are all things we arrange regularly. Just let your consultant know your preferences.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>Why should I book through you rather than directly?</summary>
              <div class="faq-answer">
                <p>Because we know things you can't find on a website. We've visited most of what we sell, we have long-standing relationships with the best operators worldwide, and we often secure rates and access that aren't available to the public. More importantly, if something goes wrong — a strike, a hotel closure, a health emergency — you have a single point of contact who will actually fix it.</p>
                <p>This is a demonstration application; it does not sell travel. It is not ABTA or ATOL protected, and no payment is taken.</p>
              </div>
            </details>

          </div>
        </div>

        <!-- Booking process -->
        <div class="faq-group" id="booking">
          <div class="faq-group-title">Making a Booking</div>
          <div class="faq-accordion">

            <details class="faq-item">
              <summary>How does the booking process work?</summary>
              <div class="faq-answer">
                <p>Our process is straightforward:</p>
                <ol>
                  <li><strong>Browse or enquire</strong> — explore our destinations freely. No account needed.</li>
                  <li><strong>Get in touch</strong> — submit a booking request or call us. We'll pair you with a consultant who specialises in your destination.</li>
                  <li><strong>Tailoring</strong> — your consultant will build an itinerary around your brief, amending until it's exactly right. This is completely free with no obligation.</li>
                  <li><strong>Confirm & deposit</strong> — when you're happy, we'll take a deposit (typically 15–25%) to secure everything.</li>
                  <li><strong>Balance payment</strong> — the remainder is due eight weeks before departure.</li>
                  <li><strong>Travel documents</strong> — we'll send your full document pack (e-tickets, vouchers, itinerary, emergency contacts) two to four weeks before you fly.</li>
                </ol>
              </div>
            </details>

            <details class="faq-item">
              <summary>Do I need to create an account to browse trips?</summary>
              <div class="faq-answer">
                <p>No. You can browse every destination, read full itineraries, and use the search entirely without an account. You only need to create a free account when you're ready to add a trip to your basket and submit a booking request.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>Is my payment secure?</summary>
              <div class="faq-answer">
                <p>No card payments are taken by this demo. A production implementation would use a PCI-DSS-compliant gateway and would not store card details on the application server.</p>
              </div>
            </details>

          </div>
        </div>

        <!-- Changes & Cancellations -->
        <div class="faq-group" id="changes">
          <div class="faq-group-title">Changes & Cancellations</div>
          <div class="faq-accordion">

            <details class="faq-item">
              <summary>What is your cancellation policy?</summary>
              <div class="faq-answer">
                <p>Our standard cancellation charges (measured from the date we receive written notification) are:</p>
                <ul>
                  <li><strong>More than 84 days before departure:</strong> deposit only</li>
                  <li><strong>57–84 days:</strong> 30% of total booking value</li>
                  <li><strong>29–56 days:</strong> 60% of total booking value</li>
                  <li><strong>8–28 days:</strong> 90% of total booking value</li>
                  <li><strong>7 days or less:</strong> 100% of total booking value</li>
                </ul>
                <p>This is why we strongly recommend taking out comprehensive travel insurance at the time of booking.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>Can I change my travel dates after I've booked?</summary>
              <div class="faq-answer">
                <p>In most cases, yes — subject to availability and any amendment fees charged by airlines and accommodation providers. Contact your consultant as early as possible; changes made well in advance are usually much more straightforward to accommodate.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>What if Meridian has to cancel my trip?</summary>
              <div class="faq-answer">
                <p>In the very rare event that we need to cancel your trip (for example, due to an FCDO travel warning), we will offer you a full refund or an alternative itinerary of equivalent value. You will never be left out of pocket.</p>
              </div>
            </details>

          </div>
        </div>

        <!-- Travel Practicalities -->
        <div class="faq-group" id="practicalities">
          <div class="faq-group-title">Travel Practicalities</div>
          <div class="faq-accordion">

            <details class="faq-item">
              <summary>Do I need a visa?</summary>
              <div class="faq-answer">
                <p>Visa requirements vary by destination and by your nationality. Your consultant will advise you on what's required for your specific trip, but it remains your responsibility to hold the correct documentation before you depart. For up-to-date official guidance, visit the <a href="https://www.gov.uk/foreign-travel-advice" target="_blank" rel="noopener">FCDO travel advice pages</a>.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>What vaccinations do I need?</summary>
              <div class="faq-answer">
                <p>Your consultant will flag any commonly required or recommended vaccinations for your destination. For authoritative medical advice, consult your GP or a travel health clinic (such as <a href="https://travelhealthpro.org.uk" target="_blank" rel="noopener">TravelHealthPro</a>) at least 6–8 weeks before departure.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>Which airport will I fly from?</summary>
              <div class="faq-answer">
                <p>We can arrange flights from any major UK airport. Our consultants will discuss options with you and find the most convenient or cost-effective routing. Many of our clients prefer to fly from a regional airport and avoid London connections entirely.</p>
              </div>
            </details>

          </div>
        </div>

        <!-- Insurance & Protection -->
        <div class="faq-group" id="insurance">
          <div class="faq-group-title">Insurance & Protection</div>
          <div class="faq-accordion">

            <details class="faq-item">
              <summary>Do you sell travel insurance?</summary>
              <div class="faq-answer">
                <p>We do not sell travel insurance directly, but we <strong>strongly recommend</strong> that every client takes out a comprehensive policy at the time of booking. Your consultant can suggest trusted specialist providers, including policies that cover pre-existing medical conditions and high-value itineraries.</p>
              </div>
            </details>

            <details class="faq-item" id="atol">
              <summary>What is ATOL protection and how does it apply to me?</summary>
              <div class="faq-answer">
                <p>ATOL (Air Travel Organiser's Licence) is a UK government financial protection scheme administered by the Civil Aviation Authority. If a travel company holding an ATOL certificate ceases trading, customers who have booked a package holiday including flights are protected — their money is refunded, or (if already travelling) arrangements are made to get them home.</p>
                <p>This demo does not hold an ATOL certificate. The page is illustrative only; a real travel business would display its actual ATOL number and certificate terms.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>What is ABTA and what does it mean for me?</summary>
              <div class="faq-answer">
                <p>This demo is not an ABTA member. The page is illustrative only; a real travel business would display its actual membership details.</p>
              </div>
            </details>

          </div>
        </div>

        <!-- While You're Away -->
        <div class="faq-group" id="ontrip">
          <div class="faq-group-title">While You're Away</div>
          <div class="faq-accordion">

            <details class="faq-item">
              <summary>What if something goes wrong while I'm travelling?</summary>
              <div class="faq-answer">
                <p>Every Meridian client has access to our 24-hour emergency line: <strong>+44 (0)117 900 0001</strong>. This number connects you to a human being — one of our consultants, not an automated system. We will do whatever it takes to resolve the situation, whether that means rebooking a missed connection, sourcing a new hotel, or liaising with local services on your behalf.</p>
              </div>
            </details>

            <details class="faq-item">
              <summary>Will I have a guide or someone to meet me on arrival?</summary>
              <div class="faq-answer">
                <p>For most itineraries, yes. Your travel documents will specify exactly what to expect — private drivers, local guides, or meet-and-greet airport transfers. If anything is unclear before you travel, call your consultant.</p>
              </div>
            </details>

          </div>
        </div>

        <div style="background:var(--navy);border-radius:var(--r-xl);padding:2.5rem;margin-top:2rem;text-align:center;">
          <h3 style="color:var(--white);margin-bottom:.75rem;">Still have a question?</h3>
          <p style="color:rgba(255,255,255,.6);margin-bottom:1.5rem;margin-inline:auto;">Our consultants are happy to chat. No scripts, no hold music.</p>
          <div style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap;">
            <a href="tel:+441179000000" class="btn btn--gold">Call 0117 900 0000</a>
            <a href="/?page=contact" class="btn btn--outline-white">Send an Enquiry</a>
          </div>
        </div>

      </div><!-- end faq content -->
    </div>
  </div>
</section>

<?php
// Mobile: hide sidebar on small screens
?>
<style>
@media (max-width: 768px) {
  .faq-sidebar { display: none; }
}
</style>
<script>
// Smooth-scroll for sidebar links
document.querySelectorAll('a[href^="#"]').forEach(function(a) {
  a.addEventListener('click', function(e) {
    var id = this.getAttribute('href').slice(1);
    var el = document.getElementById(id);
    if (el) {
      e.preventDefault();
      var offset = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--nav-h')) || 76;
      window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - offset - 16, behavior: 'smooth' });
    }
  });
});
</script>

<?php
$destId = (int) get('id');
$dest   = null;

if ($tm && $destId > 0) {
    $dest = $tm->getDestinationById($destId);
}

// Demo dataset — always defined so it powers both the fallback and "you may also like"
$demoDestinations = [
    1 => ['id'=>1,'name'=>'Santorini Hideaway','country'=>'Greece','region'=>'Europe','price'=>2450,'duration_days'=>7,'slug'=>'santorini-hideaway','budget'=>false,
          'description'=>'A week above the Aegean in a cliff-hewn cave suite. Private plunge pool, curated wine list, and a table at Lauda reserved for sunset on your last evening.',
          'highlights'=>'Private cave suite with plunge pool|Sunset dinner at Oia restaurant|Catamaran day trip around the caldera|Wine tasting at estate winery|Fast-track airport transfer',
          'image_url'=>'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?auto=format&fit=crop&w=1600&q=85'],
    2 => ['id'=>2,'name'=>'Maldives Overwater Escape','country'=>'Maldives','region'=>'Asia','price'=>5990,'duration_days'=>10,'slug'=>'maldives-overwater-escape','budget'=>false,
          'description'=>'Drift between overwater bungalows, bioluminescent lagoons, and reefs alive with manta rays. Complete seclusion on your own stretch of Indian Ocean.',
          'highlights'=>'Overwater villa with glass floor|Guided manta ray snorkel|Sunset dolphin cruise|In-villa spa treatment|Private sandbank picnic',
          'image_url'=>'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=1600&q=85'],
    3 => ['id'=>3,'name'=>'Japan in Bloom','country'=>'Japan','region'=>'Asia','price'=>3800,'duration_days'=>14,'slug'=>'japan-in-bloom','budget'=>false,
          'description'=>'Cherry blossom season in Kyoto, a private dawn ascent of Fuji, and ryokans where hospitality is an art form and time deliberately slows.',
          'highlights'=>'Private dawn access to Fushimi Inari|Cherry blossom picnic in Maruyama Park|Guided tea ceremony in Uji|Night in a century-old Kyoto machiya|Bullet train pass across Honshu',
          'image_url'=>'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=1600&q=85'],
    4 => ['id'=>4,'name'=>'Marrakech & the Atlas','country'=>'Morocco','region'=>'Africa','price'=>1950,'duration_days'=>8,'slug'=>'marrakech-atlas','budget'=>false,
          'description'=>'Riad courtyards, souks that reward wandering, and starlit dinners in a Berber camp beneath the snow-capped High Atlas.',
          'highlights'=>'Riad rooftop breakfast in the Medina|Private souk tour with a local guide|Berber camp overnight in the Atlas|Camel trek at sunrise|Hammam & argan oil ritual',
          'image_url'=>'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?auto=format&fit=crop&w=1600&q=85'],
    5 => ['id'=>5,'name'=>'Iceland Northern Lights','country'=>'Iceland','region'=>'Europe','price'=>3250,'duration_days'=>7,'slug'=>'iceland-northern-lights','budget'=>false,
          'description'=>'Geothermal springs, midnight glacier walks, and the aurora borealis overhead — best experienced from a heated outdoor tub in total silence.',
          'highlights'=>'Private aurora hunting tour|Blue Lagoon geothermal spa|Langjökull glacier snowmobile|Golden Circle in a small group|Lava tunnel exploration',
          'image_url'=>'https://images.unsplash.com/photo-1504829857797-ddff29c27927?auto=format&fit=crop&w=1600&q=85'],
    6 => ['id'=>6,'name'=>'Amalfi & the Islands','country'=>'Italy','region'=>'Europe','price'=>2890,'duration_days'=>9,'slug'=>'amalfi-islands','budget'=>false,
          'description'=>'Cliffside lemon groves, private boat days to Positano, and tables so close to the sea the spray seasons your wine.',
          'highlights'=>'Private speedboat to Capri|Ravello villa garden tour|Limoncello tasting on the terraces|Positano sunset aperitivo|Pompeii with an archaeologist guide',
          'image_url'=>'https://images.unsplash.com/photo-1555881400-74d7acaacd8b?auto=format&fit=crop&w=1600&q=85'],
    7 => ['id'=>7,'name'=>'Peru & Machu Picchu','country'=>'Peru','region'=>'Americas','price'=>4100,'duration_days'=>12,'slug'=>'peru-machu-picchu','budget'=>false,
          'description'=>'Private sunrise at Machu Picchu before the crowds arrive, the Sacred Valley at altitude, and Lima\'s world-class gastronomy as a final flourish.',
          'highlights'=>'Private sunrise slot at Machu Picchu|Sacred Valley local market tour|Belmond Hiram Bingham train|Cooking class with a Lima chef|Lake Titicaca floating islands',
          'image_url'=>'https://images.unsplash.com/photo-1526392060635-9d6019884377?auto=format&fit=crop&w=1600&q=85'],
    8 => ['id'=>8,'name'=>'Serengeti Safari','country'=>'Tanzania','region'=>'Africa','price'=>6500,'duration_days'=>10,'slug'=>'serengeti-safari','budget'=>false,
          'description'=>'The Great Migration in full thundering motion, tented camps beneath acacia trees, and the Big Five viewed at distance close enough to feel.',
          'highlights'=>'Great Migration river crossing drive|Hot air balloon at dawn|Big Five game drives twice daily|Luxury tented camp with private deck|Ngorongoro Crater descent',
          'image_url'=>'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1600&q=85'],
    9 => ['id'=>9,'name'=>'Norway Fjord Journey','country'=>'Norway','region'=>'Europe','price'=>3600,'duration_days'=>8,'slug'=>'norway-fjord-journey','budget'=>false,
          'description'=>'Dramatic fjords carved deep into the mountainside, fishing villages unchanged for centuries, and the midnight sun gilding everything gold.',
          'highlights'=>'Geirangerfjord private boat cruise|Flåm Railway scenic journey|Kayaking on Nærøyfjord|Bergen fish market breakfast|Northern lights hike (winter departures)',
          'image_url'=>'https://images.unsplash.com/photo-1520769945061-0a448c463865?auto=format&fit=crop&w=1600&q=85'],
    // ── Budget Breaks ────────────────────────────────────────────────────────
    10 => ['id'=>10,'name'=>'Budapest City Break','country'=>'Hungary','region'=>'Europe','price'=>649,'duration_days'=>5,'slug'=>'budapest-city-break','budget'=>true,
           'description'=>'Thermal baths, ruin bars, and a skyline that rewards every early riser. Budapest rewards the curious traveller with culture and cuisine at exceptional value.',
           'highlights'=>'Private thermal bath session at Széchenyi|Ruin bar crawl with a local guide|Hungarian cooking class|Danube sunset river cruise|Buda Castle & Fisherman\'s Bastion tour',
           'image_url'=>'https://images.unsplash.com/photo-1647558584325-e4b923021d76?auto=format&fit=crop&w=1600&q=85'],
    11 => ['id'=>11,'name'=>'Porto Wine Trail','country'=>'Portugal','region'=>'Europe','price'=>795,'duration_days'=>6,'slug'=>'porto-wine-trail','budget'=>true,
           'description'=>'Azulejo-tiled facades, ancient port wine cellars, and the Douro Valley glowing amber at harvest time. Portugal\'s most characterful city at a price that defies belief.',
           'highlights'=>'Private port wine cellar tour & tasting|Douro Valley vineyard day trip|Livraria Lello & Ribeira walking tour|Francesinha dinner at a local favourite|Day trip to Guimarães',
           'image_url'=>'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?auto=format&fit=crop&w=1600&q=85'],
    12 => ['id'=>12,'name'=>'Tbilisi & the Caucasus','country'=>'Georgia','region'=>'Europe','price'=>720,'duration_days'=>7,'slug'=>'tbilisi-caucasus','budget'=>true,
           'description'=>'One of Europe\'s last undiscovered gems: ancient fortresses, natural wine bars in carved cellars, and the dramatic backdrop of the Greater Caucasus.',
           'highlights'=>'Old Tbilisi sulphur bath district|Mtskheta UNESCO heritage site|Kazbegi mountain day trip|Natural wine tasting in Kakheti|Narikala Fortress sunset walk',
           'image_url'=>'https://images.unsplash.com/photo-1565008576549-57569a49371d?auto=format&fit=crop&w=1600&q=85'],
    13 => ['id'=>13,'name'=>'Albania Riviera','country'=>'Albania','region'=>'Europe','price'=>580,'duration_days'=>7,'slug'=>'albania-riviera','budget'=>true,
           'description'=>'Crystal Ionian coves, unspoilt hill villages, and a coastline that feels like the Med did fifty years ago — at a fraction of the neighbouring price.',
           'highlights'=>'Himara private boat trip to secret coves|Gjirokastër UNESCO old town|Ksamil island hop|Fresh seafood on the harbour at Sarandë|Llogara Pass mountain drive',
           'image_url'=>'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1600&q=85'],
    14 => ['id'=>14,'name'=>'Vietnam Highlights','country'=>'Vietnam','region'=>'Asia','price'=>1895,'duration_days'=>10,'slug'=>'vietnam-highlights','budget'=>true,
           'description'=>'Halong Bay at dawn, Hoi An\'s lantern-lit streets at dusk, and street food that makes the food halls of the world feel redundant.',
           'highlights'=>'Halong Bay overnight junk cruise|Hoi An old town bicycle tour|Hanoi street food evening walk|My Son Sanctuary at sunrise|Cooking class in Hoi An',
           'image_url'=>'https://images.unsplash.com/photo-1528360983277-13d401cdc186?auto=format&fit=crop&w=1600&q=85'],
    // ── Legendary Journeys ─────────────────────────────────────────────────
    15 => ['id'=>15,'name'=>"Tolkien's Middle-earth: New Zealand",'country'=>'New Zealand','region'=>'Oceania','price'=>6800,'duration_days'=>14,'slug'=>'tolkiens-middle-earth','budget'=>false,
           'description'=>"From Hobbiton's rolling green hills to Tongariro's volcanic Mordor — Middle-earth made real. For those who believe the world's greatest story deserves to be lived, not just watched.",
           'highlights'=>'Private after-hours Hobbiton movie set tour|Tongariro Alpine Crossing (Mount Doom) with a guide|Weta Workshop behind-the-scenes experience|Milford Sound private sunrise cruise|Queenstown helicopter over the Southern Alps|Wellington film culture & craft beer evening',
           'image_url'=>'https://images.unsplash.com/photo-1507699622108-4be3abd695ad?auto=format&fit=crop&w=1600&q=85'],
    16 => ['id'=>16,'name'=>'Jordan: Petra & Wadi Rum','country'=>'Jordan','region'=>'Middle East','price'=>2800,'duration_days'=>9,'slug'=>'jordan-petra-wadi-rum','budget'=>false,
           'description'=>"A Wonder of the World revealed at candlelight, a Bedouin camp under a billion stars, and the silence of Wadi Rum that every traveller carries home. This is the trip that recalibrates your sense of what travel can be.",
           'highlights'=>'Petra by Night — candles lining the Siq to the Treasury|Private Bedouin camp in Wadi Rum|Dead Sea float at sunset|Wadi Mujib canyon wading trail|Jerash Roman ruins with a local historian|Aqaba Red Sea diving',
           'image_url'=>'https://images.unsplash.com/photo-1539768942893-daf53e448371?auto=format&fit=crop&w=1600&q=85'],
    17 => ['id'=>17,'name'=>'Bhutan: The Thunder Dragon Kingdom','country'=>'Bhutan','region'=>'Asia','price'=>5500,'duration_days'=>10,'slug'=>'bhutan-thunder-dragon','budget'=>false,
           'description'=>"One of the last Himalayan kingdoms, with a constitutional visitor cap and a GDP measured in happiness. Tiger's Nest clings to a 3,000-metre cliff face. The people will disarm you completely.",
           'highlights'=>"Tiger's Nest Monastery dawn hike (Paro Taktsang)|Government visitor permit included|Punakha Dzong riverside fortress|Traditional archery ceremony|Bumthang valley monastery circuit|Private audience with a local monk",
           'image_url'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=1600&q=85'],
    18 => ['id'=>18,'name'=>'Antarctica: The Last Wilderness','country'=>'Antarctica','region'=>'Oceania','price'=>14900,'duration_days'=>14,'slug'=>'antarctica-expedition','budget'=>false,
           'description'=>"Fewer people visit Antarctica each year than attend a Premier League match. Continent-sized icebergs, 200,000-strong penguin colonies, and a sky so dark the Milky Way resolves into something you can barely believe.",
           'highlights'=>'Zodiac landings at penguin rookeries daily|Drake Passage crossing on expedition ship|Polar plunge in Antarctic waters|Glaciologist-led ice hike|Whale watching from the bow|Naturalist lectures every evening at sea',
           'image_url'=>'https://images.unsplash.com/photo-1529258283598-8d6fe60b27f4?auto=format&fit=crop&w=1600&q=85'],
    19 => ['id'=>19,'name'=>'India: Taj at Sunrise & the Golden Triangle','country'=>'India','region'=>'Asia','price'=>2950,'duration_days'=>12,'slug'=>'india-taj-golden-triangle','budget'=>false,
           'description'=>"The Taj Mahal at first light — marble so luminous it seems lit from within. The Golden Triangle adds Jaipur's pink palaces and Delhi's 3,000 years of layered history. One of the Seven Wonders at its most sublime.",
           'highlights'=>'Private Taj Mahal sunrise access before the crowds|Agra Fort & Mehtab Bagh at dusk|Jaipur City Palace rooftop dinner|Amber Fort at dawn|Old Delhi street food tour by rickshaw|Fatehpur Sikri abandoned Mughal city',
           'image_url'=>'https://images.unsplash.com/photo-1564507592333-c60657eea523?auto=format&fit=crop&w=1600&q=85'],    // ── Americas ──────────────────────────────────────────────────────
    20 => ['id'=>20,'name'=>'Patagonia: Torres del Paine','country'=>'Chile','region'=>'Americas','price'=>5200,'duration_days'=>10,'slug'=>'patagonia-torres-del-paine','budget'=>false,
           'description'=>"The granite towers of Torres del Paine at sunrise, Glacier Grey calving into turquoise water, condors drifting overhead. This is the earth at its most dramatic — and most humbling.",
           'highlights'=>'Private sunrise trek to Base Las Torres|Glacier Grey boat trip & ice trekking|Condor watching at Mirador del Condor|Perito Moreno glacier walkway (El Calafate)|Private estancia dinner under Patagonian stars|Torres del Paine W-trek with a mountain guide',
           'image_url'=>'https://images.unsplash.com/photo-1658312627438-30d1299683fd?auto=format&fit=crop&w=1600&q=85'],
    21 => ['id'=>21,'name'=>'Cuba: Havana & the Oriente','country'=>'Cuba','region'=>'Americas','price'=>1850,'duration_days'=>8,'slug'=>'cuba-havana-oriente','budget'=>false,
           'description'=>"Faded grandeur and extraordinary life — Havana\u2019s pastel facades, revolutionary murals, and a music scene that bleeds through every open window. Catch it before it changes entirely.",
           'highlights'=>'Classic car tour of Old Havana with a local historian|Private cigar rolling masterclass at a tobacco farm|Santiago de Cuba live son music evening|Trinidad colonial cobblestones at golden hour|Rum tasting at Havana Club with the master distiller|Bay of Pigs & Cienaga de Zapata wetlands',
           'image_url'=>'https://images.unsplash.com/photo-1570299437488-d430e1e677c7?auto=format&fit=crop&w=1600&q=85'],
    22 => ['id'=>22,'name'=>'Galápagos Islands Expedition','country'=>'Ecuador','region'=>'Americas','price'=>7200,'duration_days'=>10,'slug'=>'galapagos-expedition','budget'=>false,
           'description'=>"Charles Darwin changed his mind here. Sea lions that haven\u2019t learned fear, blue-footed boobies performing mating dances three feet from your boots, and marine iguanas that ignore you entirely. Wildlife at its most extraordinary.",
           'highlights'=>'Snorkel with hammerhead sharks at Kicker Rock|Sea lion encounter on Española Island|Blue-footed booby nesting colony|Giant tortoise reserve in Santa Cruz highlands|Marine iguana colony on Fernandina|Live-aboard expedition yacht with a resident naturalist',
           'image_url'=>'https://images.unsplash.com/photo-1696772686504-09010046b1d3?auto=format&fit=crop&w=1600&q=85'],
    23 => ['id'=>23,'name'=>'Colombia: Cartagena & Coffee Country','country'=>'Colombia','region'=>'Americas','price'=>1750,'duration_days'=>9,'slug'=>'colombia-cartagena-coffee','budget'=>false,
           'description'=>"Cartagena\u2019s walled city blazes with colour at dusk. The Zona Cafetera rewards those who venture inland with cloud-forest coffee farms, wax-palm valleys, and a warmth of welcome that\u2019s impossible to fake.",
           'highlights'=>'Walled city of Cartagena at golden hour with a local guide|Coffee farm stay in Salento (Zona Cafetera)|Cocora Valley wax palm hike|Sunset cocktails on Cartagena\u2019s city walls|Mompox — Colombia\u2019s most beautiful colonial town|Street food tour of Bogotá\u2019s La Candelaria',
           'image_url'=>'https://images.unsplash.com/photo-1534943441045-1009d7cb0bb9?auto=format&fit=crop&w=1600&q=85'],
    // North America
    24 => ['id'=>24,'name'=>'New York City: Icons & Neighbourhoods','country'=>'USA','region'=>'Americas','price'=>2650,'duration_days'=>8,'slug'=>'new-york-city','budget'=>false,
           'description'=>"Eight nights isn\u2019t nearly enough, but it\u2019s a brilliant start. Central Park at dawn, the High Line over Chelsea, a proper New York slice in Little Italy, and a Broadway show on a Wednesday when the tourists are elsewhere. We\u2019ve been sending clients to New York for two decades and it never gets old.",
           'highlights'=>'Private guided walk through Brooklyn Bridge Park & DUMBO|Sunset drinks at 230 Fifth rooftop with Empire State views|Behind-the-scenes Broadway backstage tour|Harlem gospel Sunday brunch|Chelsea gallery district with an art-world guide|Staten Island Ferry at dusk — best skyline shot in the city',
           'image_url'=>'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?auto=format&fit=crop&w=1600&q=85'],
    25 => ['id'=>25,'name'=>'Canadian Rockies: Banff to Jasper','country'=>'Canada','region'=>'Americas','price'=>3450,'duration_days'=>10,'slug'=>'canadian-rockies-banff-jasper','budget'=>false,
           'description'=>"The Icefields Parkway has been called the most spectacular road on earth, and the people who say that aren\u2019t wrong. Moraine Lake at sunrise with nobody else there. Elk wandering through Jasper townsite at dusk. A soak in the Banff Upper Hot Springs with the Rockies turning pink behind you.",
           'highlights'=>'Moraine Lake at sunrise — private vehicle, early access|Athabasca Glacier ice walk with a glaciologist guide|Banff Upper Hot Springs evening session|Wildlife safari along the Icefields Parkway (bear, elk, bighorn sheep)|Whitewater rafting on the Athabasca River|Helicopter flight over the Columbia Icefield',
           'image_url'=>'https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&w=1600&q=85'],
    26 => ['id'=>26,'name'=>'American Southwest: The Big Five Parks','country'=>'USA','region'=>'Americas','price'=>2950,'duration_days'=>10,'slug'=>'american-southwest-parks','budget'=>false,
           'description'=>"Zion, Bryce Canyon, Capitol Reef, Canyonlands, and Arches in ten days with a self-drive 4WD. The light in Utah does things to sandstone that\u2019s impossible to photograph well and impossible to forget. We\u2019ve built this itinerary so you hit each park at the right time of day, every time.",
           'highlights'=>'Angels Landing summit trail at Zion (permit included)|Bryce Canyon hoodoos at golden hour from Inspiration Point|Horseshoe Bend overlook at sunrise|Delicate Arch at sunset with a local guide|Dead Horse Point State Park — arguably better than the Grand Canyon|Stargazing session at Capitol Reef (world-class dark skies)',
           'image_url'=>'https://images.unsplash.com/photo-1575408264798-b50b252663e6?auto=format&fit=crop&w=1600&q=85'],];

if (!$dest) {
    $dest = $demoDestinations[$destId] ?? $demoDestinations[1];
}

// Addon pricing scaled to destination — transfer distance, insurance value, flight class cost
/** Returns inline SVG path data for an addon icon type. */
function getAddonIcon(string $type): string
{
    return match($type) {
        'transfer'    => '<rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'room'        => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v4"/><line x1="3" y1="15" x2="21" y2="15"/>',
        'dining'      => '<path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/>',
        'excursion'   => '<circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>',
        'insurance'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'lounge'      => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        'upgrade'     => '<polyline points="17 11 12 6 7 11"/><polyline points="17 18 12 13 7 18"/>',
        'photography' => '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>',
        default       => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
    };
}

/**
 * Build a destination-aware upsell list.
 * Each entry: ['id', 'name', 'desc', 'price' (int pp), 'from' (bool), 'icon']
 */
function getDestinationAddons(array $dest): array
{
    $p      = (float)  ($dest['price']  ?? 0);
    $slug   = (string) ($dest['slug']   ?? '');
    $region = (string) ($dest['region'] ?? '');
    $list   = [];

    // 1. Private Transfers — always included ──────────────────────
    $list[] = ['id'=>'transfer', 'from'=>false, 'icon'=>'transfer',
        'name' => 'Private Airport Transfers',
        'desc' => 'Meet & greet, luggage assist, door-to-door both ways',
        'price' => match(true) {
            $p < 1000  => 85,  $p < 2200 => 130, $p < 3900 => 175,
            $p < 6200  => 265, $p < 10000 => 350, default   => 450,
        }];

    // 2. Room / accommodation upgrade — mid tier and above ────────
    if ($p >= 1000) {
        [$rn, $rd, $rp, $rf] = match(true) {
            $p < 2200 =>
                ['Superior Room Upgrade',   'Larger room, higher floor or preferred view category',              95, false],
            $p < 3900 =>
                ['Deluxe Room or Sea View', 'Upgraded category with premium views — availability confirmed at booking', 185, true],
            str_contains($slug, 'maldives') =>
                ['Overwater Villa Upgrade', 'Glass-floor overwater villa with your own private ladder into the lagoon', 680, true],
            $p < 6200 =>
                ['Junior Suite Upgrade',    'Separate lounge area, enhanced amenities, priority service throughout', 425, true],
            $p < 10000 =>
                ['Private Villa or Suite',  'Secluded villa or penthouse — the most exclusive accommodation on property', 680, true],
            default =>
                ['Premium Expedition Cabin','Upgraded private cabin — porthole, writing desk, extra storage, quieter deck', 850, true],
        };
        $list[] = ['id'=>'room', 'from'=>$rf, 'price'=>$rp, 'name'=>$rn, 'desc'=>$rd, 'icon'=>'room'];
    }

    // 3. Dining package ───────────────────────────────────────────
    [$dn, $dd, $dp] = match(true) {
        $p < 1500  => ['Breakfast Included',             'Daily breakfast at your hotel — start every morning right',                        49],
        $p < 2500  => ['Half Board Dining',              'Breakfast and dinner daily — full flexibility for lunch',                         129],
        $p < 5000  => ['Full Board Dining Package',      'All meals included — arrive and hand every meal decision to us',                  220],
        $p < 10000 => ['Fine Dining & Wine Pairings',    'Curated tasting menus, sommelier-paired wines, private table reservations',      340],
        default    => ['Private Chef & Exclusive Dining','In-cabin dining, private chef evenings, and expedition celebration dinner',      395],
    };
    $list[] = ['id'=>'dining', 'from'=>false, 'price'=>$dp, 'name'=>$dn, 'desc'=>$dd, 'icon'=>'dining'];

    // 4. Destination-specific excursion ───────────────────────────
    static $excursions = [
        'santorini-hideaway'            => ['Caldera Catamaran & Wine Dinner',       'Sunset sail around the caldera followed by an estate wine dinner at Oia',            245],
        'maldives-overwater-escape'     => ['Marine Discovery Package',              'Manta ray dive, bioluminescent night snorkel, private sandbank picnic',             320],
        'japan-in-bloom'                => ['Shinkansen Day Trip Extension',         'Extra bullet train day — Tokyo to Kyoto, flexible seat reservation included',        195],
        'marrakech-atlas'               => ['Atlas Mountains 4x4 Day',               'Private 4x4, Berber family lunch, Aït Benhaddou at golden hour',                    175],
        'iceland-northern-lights'       => ['Ice Cave & Snowmobile Day',             'Glacial ice cave with a private guide plus snowmobile on Langjökull',               310],
        'amalfi-islands'                => ['Positano & Capri Private Boat Day',     'Skippered tender for the day — swim off the boat, lunch on Capri',                 385],
        'peru-machu-picchu'             => ['Sacred Valley Extension Day',           'Maras salt ponds, Moray terraces, Quechua weaving village with lunch',              215],
        'serengeti-safari'              => ['Extra Full Game Drive Day',             'Additional full day with your private guide and expert tracker',                    290],
        'norway-fjord-journey'          => ['Fjord Kayak & Wild Camp Night',         'Sea kayak across Nærøyfjord and wild camp under the midnight sun',                 265],
        'tolkiens-middle-earth'         => ['Weta Workshop Extended Access',         'Behind-the-scenes workshop tour with a concept artist Q&A',                        195],
        'jordan-petra-wadi-rum'         => ['Wadi Rum Balloon at Dawn',              'Hot air balloon over the red desert at sunrise — Bedouin breakfast follows',        340],
        'bhutan-thunder-dragon'         => ['Private Monastery Circuit Day',         'Off-route dzongs and temples with a local Buddhist guide',                         280],
        'antarctica-expedition'         => ['Sea Kayaking Among Icebergs',           'Dedicated sea kayak session in Antarctic waters — an extraordinary privilege',      450],
        'india-taj-golden-triangle'     => ['Village & Cooking Day',                 'Rural village walk, mustard fields, home cooking class with a local family',       145],
        'patagonia-torres-del-paine'    => ['Full W-Trek Guided Extension',          'Two extra days completing the W circuit with a certified mountain guide',           390],
        'cuba-havana-oriente'           => ['Trinidad & Waterfall Day',              'UNESCO Trinidad, jungle waterfall hike, classic car back at golden hour',           165],
        'galapagos-expedition'          => ['Night Dive & Pelagic Safari',           'After-dark dive for hammerheads and bioluminescent plankton',                      280],
        'colombia-cartagena-coffee'     => ['Coffee Finca Overnight Stay',           'Pick, process, roast and cup your own coffee on a working estate',                 195],
        'new-york-city'                 => ['Broadway Show & Pre-Theatre Dinner',    'Orchestra seats plus a pre-theatre reservation at a New York favourite nearby',    265],
        'canadian-rockies-banff-jasper' => ['Helicopter & Remote Glacier Landing',   'Depart Banff helipad to a remote glacier — 60 minutes above the Rockies',          480],
        'american-southwest-parks'      => ['Dark Sky Photography Workshop',         'Three-hour Capitol Reef session with a professional night photographer',           175],
        'budapest-city-break'           => ['Thermal Spa & Ruin Bar Night',          'All-day Széchenyi access followed by a guided ruin bar evening walk',               95],
        'porto-wine-trail'              => ['Douro Valley Quinta Overnight',         'One night at a working quinta — dinner, harvest tour, barrel tasting',             220],
        'tbilisi-caucasus'              => ['Kakheti Wine Country Day',              'Private driver to Georgia\'s oldest wine estates — three wineries, lunch inc.',    110],
        'albania-riviera'               => ['Boat Charter & Hidden Coves Day',       'Full-day skippered boat, sea cave swimming, fresh catch grilled for lunch',        130],
        'vietnam-highlights'            => ['Halong Bay Kayak & Cave Day',           'Extra kayak day through karst limestone into hidden lagoons',                      145],
    ];
    if (isset($excursions[$slug])) {
        [$en, $ed, $ep] = $excursions[$slug];
        $list[] = ['id'=>'excursion', 'from'=>false, 'price'=>$ep, 'name'=>$en, 'desc'=>$ed, 'icon'=>'excursion'];
    } elseif ($p >= 700) {
        [$en, $ed, $ep] = match($region) {
            'Europe'      => ['Signature Day Excursion',    'Full-day private excursion — guide, transport, and entry fees included',           130],
            'Asia'        => ['Cultural Discovery Day',     'Private guided day combining heritage, markets, and authentic local cuisine',      165],
            'Africa'      => ['Sunrise Nature Experience', 'Guided sunrise activity — wildlife, boat, or walk depending on location',          195],
            'Americas'    => ['Adventure Activity Day',    'Guided outdoor day — hiking, kayaking, or cycling tailored to the destination',   150],
            'Middle East' => ['Desert Sunset Experience',  'Private guided sunset in desert or ancient landscape with local storytelling',    175],
            'Oceania'     => ['Wilderness Discovery Day',  'Full day in the most dramatic scenery with an expert naturalist guide',          220],
            default       => ['Private Guided Day',         'A curated full-day private experience handpicked for this destination',          150],
        };
        $list[] = ['id'=>'excursion', 'from'=>false, 'price'=>$ep, 'name'=>$en, 'desc'=>$ed, 'icon'=>'excursion'];
    }

    // 5. Comprehensive Insurance — always ─────────────────────────
    $list[] = ['id'=>'insurance', 'from'=>false, 'icon'=>'insurance',
        'name' => 'Comprehensive Travel Insurance',
        'desc' => 'Medical, cancellation, baggage & curtailment — fully underwritten cover',
        'price' => match(true) {
            $p < 1000  => 79,  $p < 2200 => 129, $p < 3900 => 169,
            $p < 6200  => 249, $p < 10000 => 325, default   => 595,
        }];

    // 6. Airport Lounge — always ──────────────────────────────────
    $list[] = ['id'=>'lounge', 'from'=>false, 'icon'=>'lounge',
        'name' => 'Airport Lounge Access',
        'desc' => 'Priority Pass both ways — unlimited food, drinks & Wi-Fi before every flight',
        'price' => match(true) {
            $p < 1000  => 55,  $p < 2200 => 65,  $p < 3900 => 85,
            $p < 6200  => 99,  $p < 10000 => 115, default   => 130,
        }];

    // 7. Flight upgrade — medium+ haul ────────────────────────────
    if ($p >= 800) {
        [$un, $ud, $up, $uf] = match(true) {
            $p < 1500  => ['Economy Plus Upgrade',         'Extra legroom, priority boarding, enhanced meal service both ways',                 480, false],
            $p < 3900  => ['Business Class Flight Upgrade','Lie-flat seat, fine dining at altitude, 30kg baggage',                           1850, true],
            $p < 6200  => ['Business Class Flight Upgrade','Lie-flat bed, premium cabin, sommelier service — price varies by route',         3200, true],
            $p < 10000 => ['Business Class Flight Upgrade','Private cabin configuration on select aircraft, confirmed at booking',           4800, true],
            default    => ['Business Class Flight Upgrade','Lie-flat suite — price confirmed per route and aircraft at time of booking',     6800, true],
        };
        $list[] = ['id'=>'upgrade', 'from'=>$uf, 'price'=>$up, 'name'=>$un, 'desc'=>$ud, 'icon'=>'upgrade'];
    }

    // 8. Professional photography — premium+ trips ────────────────
    if ($p >= 3900) {
        $list[] = ['id'=>'photography', 'from'=>false, 'icon'=>'photography',
            'price' => $p >= 10000 ? 395 : ($p >= 6200 ? 320 : 265),
            'name'  => 'Professional Photography Session',
            'desc'  => 'Private photographer for a half-day — 50+ edited high-res images delivered digitally'];
    }

    return $list;
}

$addons = getDestinationAddons($dest);

// Handle add-to-basket — session-based, no login required
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'add_to_basket') {
    verifyCsrf();
    $passengers      = max(1, min(12, (int) post('passengers')));
    $travelDate      = post('travel_date') ?: date('Y-m-d', strtotime('+12 weeks'));
    $notes           = substr(strip_tags(post('notes') ?? ''), 0, 500);
    $daysToDepart    = (int) ceil((strtotime($travelDate) - time()) / 86400);
    if ($daysToDepart < 70) {
        flash('error', 'Online bookings require at least 10 weeks\' notice. Please call us on 0117 900 0000 for last-minute availability.');
        redirect('booking_details?id=' . (int)$dest['id']);
    }
    $weeksOut        = (int) floor($daysToDepart / 7);
    $depositPct      = $weeksOut >= 21 ? 10 : ($weeksOut >= 14 ? 25 : 50);
    $groupSurcharge  = $passengers >= 6 ? round((float)$dest['price'] * 0.10, 2) : 0.0;

    // Validate selected add-ons against the server-side list, never the POSTed price.
    $addonById = [];
    foreach ($addons as $addon) {
        $addonById[(string) $addon['id']] = $addon;
    }
    $selectedAddonIds = [];
    $addonPerPerson   = 0.0;
    foreach ((array) ($_POST['addons'] ?? []) as $requestedId) {
        if (!is_string($requestedId) || !isset($addonById[$requestedId])) {
            continue;
        }
        $selectedAddonIds[] = $requestedId;
        $addonPerPerson += (float) $addonById[$requestedId]['price'];
    }

    if (!isset($_SESSION['basket'])) {
        $_SESSION['basket'] = [];
    }
    $_SESSION['basket'][] = [
        'key'              => uniqid('bk_', true),
        'destination_id'   => (int)$dest['id'],
        'name'             => $dest['name'],
        'country'          => $dest['country'],
        'image_url'        => $dest['image_url'],
        'duration_days'    => (int)$dest['duration_days'],
        'price'            => (float)$dest['price'],
        'group_surcharge'  => $groupSurcharge,
        'addon_ids'        => $selectedAddonIds,
        'addon_per_person' => $addonPerPerson,
        'passengers'       => $passengers,
        'travel_date'      => $travelDate,
        'deposit_pct'      => $depositPct,
        'notes'            => $notes,
    ];
    flash('success', h($dest['name']) . ' added to your basket.');
    redirect('basket');
}

$highlights = [];
if (!empty($dest['highlights'])) {
    $highlights = array_filter(explode('|', $dest['highlights']));
}
?>

<div class="page-header">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="/?page=home">Home</a>
      <span class="breadcrumb__sep" aria-hidden="true">/</span>
      <a href="/?page=search_results">Destinations</a>
      <span class="breadcrumb__sep" aria-hidden="true">/</span>
      <span class="breadcrumb__current"><?= h($dest['name']) ?></span>
    </nav>
    <span class="eyebrow page-header__eyebrow"><?= h($dest['region'] ?? $dest['country']) ?></span>
    <h1><?= h($dest['name']) ?></h1>
    <p class="lead"><?= h($dest['country']) ?> &bull; <?= (int)$dest['duration_days'] ?> nights &bull; from <?= formatPrice((float)$dest['price']) ?> per person</p>
  </div>
</div>

<?php renderFlash(); ?>

<div class="booking-layout">

  <!-- Main content -->
  <div>
    <div class="booking-card">
      <img src="<?= h($dest['image_url']) ?>" alt="<?= h($dest['name']) ?>" class="booking-card__image" loading="lazy" width="1600" height="700">
      <div class="booking-card__body">
        <h2 class="form-section-title" style="font-size:1.4rem;">About This Journey</h2>
        <p style="color:var(--text-muted);line-height:1.8;margin-bottom:1.5rem;"><?= h($dest['description']) ?></p>

        <?php if (!empty($highlights)): ?>
        <h3 style="font-size:1rem;font-family:var(--font-sans);font-weight:700;margin-bottom:1rem;letter-spacing:.04em;">WHAT’S INCLUDED</h3>
        <ul style="display:flex;flex-direction:column;gap:.6rem;margin-bottom:1.5rem;">
          <?php foreach ($highlights as $h_item): ?>
          <li style="display:flex;align-items:flex-start;gap:.75rem;font-size:.92rem;color:var(--text-muted);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success-text)" stroke-width="2.5" style="flex-shrink:0;margin-top:2px;" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            <?= h($h_item) ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <hr class="divider">

        <h3 style="font-size:1rem;font-family:var(--font-sans);font-weight:700;margin-bottom:1.25rem;letter-spacing:.04em;">YOUR BOOKING DETAILS</h3>

        <form method="post" action="/?page=booking_details&id=<?= (int)$dest['id'] ?>" id="booking-form">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="add_to_basket">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="bk-date">Preferred Departure Date</label>
              <input class="form-input" id="bk-date" type="date" name="travel_date"
                     min="<?= date('Y-m-d', strtotime('+70 days')) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="bk-pax">Number of Travellers</label>
              <select class="form-select" id="passengers-select" name="passengers">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i ?>" <?= $i === 2 ? 'selected' : '' ?>><?= $i ?> <?= $i === 1 ? 'person' : 'people' ?></option>
                <?php endfor; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="bk-notes">Special Requests or Notes (optional)</label>
            <textarea class="form-textarea" id="bk-notes" name="notes" rows="3" placeholder="Dietary requirements, room preferences, anniversary celebrations…"></textarea>
          </div>

          <hr class="divider" style="margin:1.75rem 0 1.25rem;">
          <h3 style="font-size:.9rem;font-family:var(--font-sans);font-weight:700;margin-bottom:.3rem;letter-spacing:.04em;">ENHANCE YOUR JOURNEY</h3>
          <p style="font-size:.78rem;color:var(--text-light);margin:0 0 1.1rem;">Optional extras — priced per person, added to your basket.</p>
          <div class="addon-grid">

            <?php foreach ($addons as $a):
              $icon_svg = getAddonIcon($a['icon']);
            ?>
            <div class="addon-item">
              <input type="checkbox" id="addon-<?= h($a['id']) ?>" name="addons[]" value="<?= h($a['id']) ?>" data-price="<?= (int)$a['price'] ?>">
              <label class="addon-label" for="addon-<?= h($a['id']) ?>">
                <span class="addon-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><?= $icon_svg ?></svg>
                </span>
                <span class="addon-text">
                  <span class="addon-name"><?= h($a['name']) ?></span>
                  <span class="addon-desc"><?= h($a['desc']) ?></span>
                  <span class="addon-price"><?= $a['from'] ? 'from ' : '+' ?><?= formatPrice($a['price']) ?> pp</span>
                </span>
                <span class="addon-check"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg></span>
              </label>
            </div>
            <?php endforeach; ?>

          </div>

          <button type="submit" id="booking-submit-btn" class="btn btn--gold btn--lg" style="width:100%;justify-content:center;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            Add to Basket
          </button>
          <p style="text-align:center;font-size:.78rem;color:var(--text-light);margin-top:.75rem;">
            No payment taken yet &bull; Fully refundable for 24 hours
          </p>
        </form>
      </div>
    </div>
  </div>

  <!-- Sticky summary -->
  <aside>
    <div class="booking-summary">
      <h3>Price Summary</h3>
      <div class="summary-line">
        <span class="summary-label">Base price</span>
        <span class="summary-price" id="base-price"><?= formatPrice((float)$dest['price']) ?> pp</span>
      </div>
      <div class="summary-line">
        <span class="summary-label">Duration</span>
        <span class="summary-price"><?= (int)$dest['duration_days'] ?> nights</span>
      </div>
      <div class="summary-line">
        <span class="summary-label">Travellers</span>
        <span class="summary-price" id="pax-display">2</span>
      </div>
      <div class="summary-line" id="surcharge-line" style="display:none;">
        <span class="summary-label" style="color:var(--text-muted);font-size:.82rem;">Group supplement <span style="opacity:.7;">(6+ travellers, +10% pp)</span></span>
        <span class="summary-price" id="surcharge-price" style="color:var(--text-muted);"></span>
      </div>
      <div class="summary-line total">
        <span class="summary-label">Estimated Total</span>
        <span class="summary-price" id="total-price"><?= formatPrice((float)$dest['price'] * 2) ?></span>
      </div>
      <div class="summary-line" id="deposit-line" style="background:rgba(200,169,110,.12);border-radius:var(--r-md);padding:.6rem .5rem;margin-top:.5rem;">
        <span class="summary-label" style="font-weight:700;color:var(--navy);" id="deposit-label">Deposit Due Today</span>
        <span class="summary-price" id="deposit-price" style="color:var(--gold);font-weight:700;">—</span>
      </div>
      <div id="deposit-blocked-msg" style="display:none;background:#fff0f0;border:1px solid #fca5a5;border-radius:var(--r-md);padding:.75rem .875rem;margin-top:.5rem;">
        <p style="font-size:.78rem;color:#991b1b;margin:0;line-height:1.5;">
          <strong>Cannot book online within 10 weeks of departure.</strong><br>
          Call <a href="tel:+441179000000" style="color:#991b1b;font-weight:700;">0117 900 0000</a> for last-minute availability.
        </p>
      </div>
      <p style="font-size:.75rem;color:var(--text-light);margin-top:.75rem;line-height:1.5;" id="deposit-note">Select a departure date to see your payment schedule.</p>
      <details style="margin-top:.625rem;">
        <summary style="font-size:.73rem;color:var(--navy);cursor:pointer;font-weight:600;list-style:none;">&#9660;&nbsp;Payment schedule</summary>
        <table style="width:100%;font-size:.72rem;margin-top:.5rem;border-collapse:collapse;">
          <tr style="border-bottom:1px solid var(--border-light);"><td style="padding:.3rem .25rem;color:var(--text-muted);">21+ weeks away</td><td style="text-align:right;font-weight:600;color:var(--navy);">10% now</td></tr>
          <tr style="border-bottom:1px solid var(--border-light);"><td style="padding:.3rem .25rem;color:var(--text-muted);">14–20 weeks away</td><td style="text-align:right;font-weight:600;color:var(--navy);">25% now</td></tr>
          <tr style="border-bottom:1px solid var(--border-light);"><td style="padding:.3rem .25rem;color:var(--text-muted);">10–13 weeks away</td><td style="text-align:right;font-weight:600;color:var(--navy);">50% now</td></tr>
          <tr><td style="padding:.3rem .25rem;color:var(--text-muted);">Under 10 weeks</td><td style="text-align:right;font-weight:600;color:#991b1b;">Call us</td></tr>
        </table>
        <p style="font-size:.7rem;color:var(--text-light);margin-top:.4rem;">Balance always due 10 weeks before departure.</p>
      </details>
      <hr class="divider">
      <div style="display:flex;flex-direction:column;gap:.6rem;">
        <div style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:var(--text-muted);">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--success-text)" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Demo application — no travel is sold
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:var(--text-muted);">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--success-text)" stroke-width="2" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
          Free cancellation for 24 hrs
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:var(--text-muted);">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--success-text)" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          Dedicated 24/7 support
        </div>
      </div>
    </div>

    <div style="margin-top:1.25rem;background:var(--gold-pale);border:1px solid rgba(200,169,110,.3);border-radius:var(--r-lg);padding:1.25rem;">
      <p style="font-size:.85rem;color:var(--text-muted);margin:0;line-height:1.6;">
        <strong style="color:var(--navy);">Prefer to talk it through?</strong><br>
        Call us on <a href="tel:+441179000000" style="color:var(--navy);font-weight:700;">0117 900 0000</a> or
        <a href="/?page=contact" style="color:var(--navy);text-decoration:underline;">send an enquiry</a>.
      </p>
    </div>
  </aside>

</div>

<?php
// "You may also like" — 3 destinations from the same region, or just other trips
$related = array_filter($demoDestinations, fn($d) => $d['id'] !== (int)$dest['id']);
usort($related, fn($a,$b) => ($a['region']===$dest['region'] ? 0 : 1) <=> ($b['region']===$dest['region'] ? 0 : 1));
$related = array_slice(array_values($related), 0, 3);
?>
<section class="upsell-strip">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">You May Also Like</span>
      <h2 style="font-size:1.6rem;margin-top:.35rem;">Similar Journeys Worth Considering</h2>
    </div>
    <div class="destinations-grid">
      <?php foreach ($related as $r): ?>
      <article class="card">
        <a href="/?page=booking_details&id=<?= (int)$r['id'] ?>" class="card__image-wrap" tabindex="-1" aria-hidden="true">
          <img src="<?= h($r['image_url']) ?>" alt="<?= h($r['name']) ?>" class="card__image" loading="lazy" width="800" height="600">
          <?php if (!empty($r['budget'])): ?><span class="card__badge" style="background:var(--success-text);">Budget Break</span><?php endif; ?>
          <span class="card__region"><?= h($r['region']) ?></span>
          <span class="card__duration"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><?= (int)$r['duration_days'] ?> nights</span>
        </a>
        <div class="card__body">
          <div class="card__meta"><span class="card__location"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><?= h($r['country']) ?></span></div>
          <h3 class="card__title"><a href="/?page=booking_details&id=<?= (int)$r['id'] ?>"><?= h($r['name']) ?></a></h3>
          <p class="card__excerpt"><?= h($r['description']) ?></p>
          <div class="card__footer">
            <div class="card__price"><span class="card__price-from">from</span><span class="card__price-value"><?= formatPrice((float)$r['price']) ?></span><span class="card__price-per">pp</span></div>
            <a href="/?page=booking_details&id=<?= (int)$r['id'] ?>" class="btn btn--sm btn--navy">View Trip</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<script>
(function() {
  var pricePerPerson = <?= (float) $dest['price'] ?>;
  var paxSelect    = document.getElementById('passengers-select');
  var paxDisplay   = document.getElementById('pax-display');
  var totalEl      = document.getElementById('total-price');
  var depositEl    = document.getElementById('deposit-price');
  var depositLabel = document.getElementById('deposit-label');
  var depositNote  = document.getElementById('deposit-note');
  var depositLine  = document.getElementById('deposit-line');
  var blockedMsg   = document.getElementById('deposit-blocked-msg');
  var submitBtn    = document.getElementById('booking-submit-btn');
  var dateInput    = document.getElementById('bk-date');
  var addonBoxes   = document.querySelectorAll('.addon-item input[type=checkbox]');
  var surchargeLine  = document.getElementById('surcharge-line');
  var surchargePrice = document.getElementById('surcharge-price');

  function formatGBP(n) {
    return '\u00a3' + Math.round(n).toLocaleString('en-GB');
  }

  function getDepositTier(dateVal) {
    if (!dateVal) return { pct: 0, label: 'Deposit Due Today', blocked: false, note: 'Select a departure date to see your payment schedule.' };
    var now = new Date(); now.setHours(0, 0, 0, 0);
    var dep = new Date(dateVal);
    var days  = Math.round((dep - now) / 86400000);
    var weeks = Math.floor(days / 7);
    if (weeks < 10) {
      return { pct: 0, label: '', blocked: true, note: '' };
    }
    if (weeks < 14) {
      return { pct: 0.50, label: '50% Deposit', blocked: false,
        note: 'Departure ' + weeks + ' weeks away: 50% due now. Balance due 10 weeks before departure.' };
    }
    if (weeks < 21) {
      return { pct: 0.25, label: '25% Deposit', blocked: false,
        note: 'Departure ' + weeks + ' weeks away: 25% due now. Balance due 10 weeks before departure.' };
    }
    return { pct: 0.10, label: '10% Deposit', blocked: false,
      note: 'Departure ' + weeks + ' weeks away: 10% due now. Balance due 10 weeks before departure.' };
  }

  function update() {
    var pax       = parseInt(paxSelect.value, 10) || 1;
    var surcharge = pax >= 6 ? pricePerPerson * 0.10 : 0;
    var extras    = 0;
    addonBoxes.forEach(function(cb) {
      if (cb.checked) extras += parseFloat(cb.dataset.price || 0);
    });
    var total = (pricePerPerson + surcharge + extras) * pax;
    var tier  = getDepositTier(dateInput ? dateInput.value : null);

    paxDisplay.textContent = pax;
    totalEl.textContent    = formatGBP(total);

    if (pax >= 6) {
      surchargeLine.style.display = '';
      surchargePrice.textContent  = '+' + formatGBP(surcharge) + ' pp';
    } else {
      surchargeLine.style.display = 'none';
    }

    if (tier.blocked) {
      depositLine.style.display  = 'none';
      blockedMsg.style.display   = '';
      depositNote.textContent    = '';
      if (submitBtn) { submitBtn.disabled = true; submitBtn.style.opacity = '.5'; }
    } else {
      depositLine.style.display  = '';
      blockedMsg.style.display   = 'none';
      depositLabel.textContent   = tier.label + ' Due Today';
      depositEl.textContent      = tier.pct > 0 ? formatGBP(total * tier.pct) : '—';
      depositNote.textContent    = tier.note;
      if (submitBtn) { submitBtn.disabled = false; submitBtn.style.opacity = ''; }
    }
  }

  if (paxSelect)  paxSelect.addEventListener('change', update);
  if (dateInput)  dateInput.addEventListener('change', update);
  addonBoxes.forEach(function(cb) { cb.addEventListener('change', update); });
  update();
})();
</script>

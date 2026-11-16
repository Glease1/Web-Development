<?php
declare(strict_types=1);

// SQLite database file. It must live OUTSIDE the web root.
//
// Default: one directory above the application root (for example
// /home/<user>/meridian-data/meridian.db). Override with MERIDIAN_DB_PATH.
// The legacy database/meridian.db is migrated once, on first boot, and then
// the application uses only the new path.
$dbPath = getenv('MERIDIAN_DB_PATH');
if (!is_string($dbPath) || trim($dbPath) === '') {
    $dbPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'meridian-data'
        . DIRECTORY_SEPARATOR . 'meridian.db';
}
define('DB_PATH', $dbPath);

/**
 * Copy the old in-web-root database to the new path, using SQLite's own
 * backup mechanism so a WAL file is included. Never delete the source.
 */
function _dbMigrateLegacyDatabase(): void
{
    $legacy = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database'
        . DIRECTORY_SEPARATOR . 'meridian.db';
    if (file_exists(DB_PATH) || !is_file($legacy)) {
        return;
    }

    $dir = dirname(DB_PATH);
    if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) {
        throw new RuntimeException('Cannot create the database directory: ' . $dir);
    }

    try {
        $source = new PDO('sqlite:' . $legacy, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $source->exec('VACUUM INTO ' . $source->quote(DB_PATH));
        $source = null;
        @chmod(DB_PATH, 0640);
    } catch (Throwable $e) {
        error_log('[Meridian] Database migration failed: ' . $e->getMessage());
        throw new RuntimeException(
            'The database could not be moved outside the web root. '
            . 'Set MERIDIAN_DB_PATH to a writable path outside the document root.',
            0,
            $e
        );
    }
}

/**
 * Returns a singleton PDO connection to the SQLite database.
 * On first call the schema is created and demo data is seeded.
 */
function getDbConnection(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dbDir = dirname(DB_PATH);
    if (!is_dir($dbDir)) {
        if (!mkdir($dbDir, 0750, true) && !is_dir($dbDir)) {
            throw new RuntimeException('Cannot create the database directory: ' . $dbDir);
        }
    }
    @chmod($dbDir, 0750);

    if (!file_exists(DB_PATH)) {
        _dbMigrateLegacyDatabase();
    }
    $isNew = !file_exists(DB_PATH);

    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA journal_mode  = WAL');
    @chmod(DB_PATH, 0640);

    // Always ensure the schema exists; only seed a brand-new database.
    _dbBootstrap($pdo, $isNew);

    return $pdo;
}

// ── Internal: schema + seed (runs once on DB creation) ────────────────────

function _dbBootstrap(PDO $pdo, bool $seed = true): void
{
    // Schema
    $pdo->exec(<<<'SQL'
        CREATE TABLE IF NOT EXISTS users (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            name       TEXT    NOT NULL,
            email      TEXT    NOT NULL UNIQUE,
            password   TEXT    NOT NULL,
            role       TEXT    NOT NULL DEFAULT 'user',
            created_at TEXT    NOT NULL DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS destinations (
            id            INTEGER PRIMARY KEY AUTOINCREMENT,
            name          TEXT    NOT NULL,
            slug          TEXT    NOT NULL UNIQUE,
            country       TEXT    NOT NULL,
            region        TEXT    NOT NULL DEFAULT '',
            description   TEXT,
            highlights    TEXT,
            price         REAL    NOT NULL DEFAULT 0,
            duration_days INTEGER NOT NULL DEFAULT 7,
            image_url     TEXT    NOT NULL DEFAULT '',
            featured      INTEGER NOT NULL DEFAULT 0,
            budget        INTEGER NOT NULL DEFAULT 0,
            created_at    TEXT    NOT NULL DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS bookings (
            id             INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id        INTEGER,
            destination_id INTEGER,
            travel_date    TEXT,
            passengers     INTEGER NOT NULL DEFAULT 1,
            total_price    REAL    NOT NULL DEFAULT 0,
            notes          TEXT    NOT NULL DEFAULT '',
            addons_json    TEXT    NOT NULL DEFAULT '[]',
            group_surcharge REAL   NOT NULL DEFAULT 0,
            deposit_pct    INTEGER NOT NULL DEFAULT 10,
            status         TEXT    NOT NULL DEFAULT 'pending',
            created_at     TEXT    NOT NULL DEFAULT (datetime('now')),
            FOREIGN KEY (user_id)        REFERENCES users(id)        ON DELETE SET NULL,
            FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS basket_items (
            id             INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id        INTEGER NOT NULL,
            destination_id INTEGER NOT NULL,
            passengers     INTEGER NOT NULL DEFAULT 1,
            added_at       TEXT    NOT NULL DEFAULT (datetime('now')),
            UNIQUE(user_id, destination_id),
            FOREIGN KEY (user_id)        REFERENCES users(id)        ON DELETE CASCADE,
            FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
        );
    SQL);

    _dbEnsureBookingColumns($pdo);

    if (!$seed) {
        return;
    }

    // Demo accounts
    $userStmt = $pdo->prepare(
        'INSERT OR IGNORE INTO users (name, email, password, role, created_at) VALUES (?,?,?,?,?)'
    );
    $userStmt->execute(['Admin',     'admin@meridian.test',
        password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]), 'admin', '2023-03-15 09:00:00']);
    $userStmt->execute(['Demo User', 'demo@meridian.test',
        password_hash('demo123',  PASSWORD_BCRYPT, ['cost' => 12]), 'user',  '2024-08-20 14:32:00']);

    // Destinations
    $destStmt = $pdo->prepare(
        'INSERT OR IGNORE INTO destinations
            (id,name,slug,country,region,description,highlights,price,duration_days,image_url,featured,budget)
         VALUES
            (:id,:name,:slug,:country,:region,:description,:highlights,:price,:duration_days,:image_url,:featured,:budget)'
    );
    foreach (_destinationsSeed() as $row) {
        $destStmt->execute($row);
    }

    // Additional demo users — spread across realistic join dates
    foreach ([
        ['Sarah Johnson',   'sarah.johnson@meridian.test',  '2024-09-05 10:22:00'],
        ['James Mitchell',  'j.mitchell@meridian.test',     '2024-10-12 16:45:00'],
        ['Emma Williams',   'emma.williams@meridian.test',  '2024-11-03 09:18:00'],
        ['Oliver Davies',   'oliver.davies@meridian.test',  '2024-11-28 14:05:00'],
        ['Charlotte Brown', 'c.brown@meridian.test',        '2025-01-15 11:30:00'],
        ['Thomas Anderson', 't.anderson@meridian.test',     '2025-02-07 08:54:00'],
        ['Amelia Thompson', 'amelia.t@meridian.test',       '2025-03-19 15:42:00'],
        ['William Clarke',  'will.clarke@meridian.test',    '2025-04-02 10:17:00'],
        ['Isabella Wilson', 'i.wilson@meridian.test',       '2025-05-14 13:29:00'],
        ['George Roberts',  'g.roberts@meridian.test',      '2025-06-08 09:51:00'],
        ['Sophia Evans',    'sophia.evans@meridian.test',   '2025-07-21 17:03:00'],
        ['Henry Turner',    'h.turner@meridian.test',       '2025-08-30 11:44:00'],
        ['Grace Phillips',  'grace.phillips@meridian.test', '2025-10-05 14:22:00'],
        ['Alexander Scott', 'alex.scott@meridian.test',     '2025-11-18 09:08:00'],
        ['Lily Campbell',   'lily.c@meridian.test',         '2026-01-09 16:37:00'],
    ] as [$n, $e, $d]) {
        $userStmt->execute([$n, $e, password_hash('demo2026!', PASSWORD_BCRYPT, ['cost' => 10]), 'user', $d]);
    }

    // Seed realistic bookings
    // user IDs: 1=admin, 2=demo, 3–17=extra users above
    $bookStmt = $pdo->prepare(
        'INSERT INTO bookings (user_id,destination_id,travel_date,passengers,total_price,status,notes,created_at)
         VALUES (?,?,?,?,?,?,?,?)'
    );
    foreach ([
        [2,  3,  '2026-03-15', 2,  7600.00,  'confirmed', 'Anniversary trip — window seats please',          '2025-11-20 09:14:22'],
        [3,  1,  '2026-05-10', 2,  4900.00,  'confirmed', '',                                                 '2026-01-08 14:32:10'],
        [4,  8,  '2026-06-01', 2,  13000.00, 'confirmed', 'Honeymoon — please arrange welcome amenity',      '2026-02-14 11:05:44'],
        [5,  5,  '2026-02-28', 3,  9750.00,  'confirmed', 'Child meal option required for one passenger',    '2025-10-03 16:18:55'],
        [6,  18, '2026-08-15', 1,  14900.00, 'confirmed', '',                                                 '2026-03-22 10:45:00'],
        [7,  15, '2026-09-05', 2,  13600.00, 'confirmed', 'LOTR fans — first visit to New Zealand',          '2026-04-01 08:30:12'],
        [8,  20, '2026-10-12', 2,  10400.00, 'confirmed', 'Celebrating 30th birthday',                      '2026-04-15 12:00:00'],
        [9,  22, '2026-07-20', 2,  14400.00, 'confirmed', '',                                                 '2026-03-10 09:22:33'],
        [10, 2,  '2026-11-01', 2,  11980.00, 'confirmed', 'Overwater bungalow upgrade if available',         '2026-04-28 15:10:44'],
        [2,  14, '2026-04-22', 2,  3790.00,  'confirmed', '',                                                 '2026-01-30 13:45:00'],
        [2,  11, '2026-05-03', 1,  795.00,   'confirmed', '',                                                 '2026-02-20 10:20:00'],
        [11, 6,  '2026-09-18', 2,  5780.00,  'pending',   '',                                                 '2026-06-01 10:05:00'],
        [12, 7,  '2026-10-25', 3,  12300.00, 'pending',   'Group of 3 — one vegetarian dietary requirement', '2026-06-10 14:22:00'],
        [13, 25, '2026-08-30', 4,  13800.00, 'pending',   'Family trip with two teenagers',                  '2026-06-18 09:40:00'],
        [14, 4,  '2026-11-14', 2,  3900.00,  'pending',   '',                                                 '2026-07-01 11:15:00'],
        [15, 10, '2026-09-07', 1,  649.00,   'pending',   'Solo traveller — any room upgrade available?',    '2026-07-15 16:30:00'],
        [16, 16, '2026-12-01', 2,  5600.00,  'pending',   'Christmas holiday',                               '2026-07-20 08:55:00'],
        [17, 9,  '2026-06-15', 2,  7200.00,  'cancelled', 'Change of travel dates',                         '2026-03-01 09:00:00'],
        [3,  12, '2026-07-10', 1,  720.00,   'cancelled', 'Personal reasons',                               '2026-04-05 14:30:00'],
        [11, 24, '2026-08-01', 2,  5300.00,  'cancelled', '',                                                '2026-05-20 11:00:00'],
    ] as $b) {
        $bookStmt->execute($b);
    }
}

/**
 * Add booking columns introduced after the first release. SQLite has no
 * "ADD COLUMN IF NOT EXISTS", so inspect the table first.
 */
function _dbEnsureBookingColumns(PDO $pdo): void
{
    $columns = [];
    foreach ($pdo->query('PRAGMA table_info(bookings)')->fetchAll() as $column) {
        $columns[] = $column['name'];
    }

    $required = [
        'addons_json'     => "TEXT NOT NULL DEFAULT '[]'",
        'group_surcharge' => 'REAL NOT NULL DEFAULT 0',
        'deposit_pct'     => 'INTEGER NOT NULL DEFAULT 10',
    ];

    foreach ($required as $name => $definition) {
        if (!in_array($name, $columns, true)) {
            $pdo->exec('ALTER TABLE bookings ADD COLUMN ' . $name . ' ' . $definition);
        }
    }
}

function _destinationsSeed(): array
{
    return [
        // ── Featured mid-range ────────────────────────────────────────────────
        ['id'=>1,'featured'=>1,'budget'=>0,'name'=>'Santorini Hideaway','slug'=>'santorini-hideaway',
         'country'=>'Greece','region'=>'Europe','price'=>2450,'duration_days'=>7,
         'description'=>"A week above the Aegean in a cliff-hewn cave suite. Private plunge pool, curated wine list, and a table at Lauda reserved for sunset on your last evening.",
         'highlights'=>"Private cave suite with plunge pool|Sunset dinner at Oia restaurant|Catamaran day trip around the caldera|Wine tasting at estate winery|Fast-track airport transfer",
         'image_url'=>'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>2,'featured'=>1,'budget'=>0,'name'=>'Maldives Overwater Escape','slug'=>'maldives-overwater-escape',
         'country'=>'Maldives','region'=>'Asia','price'=>5990,'duration_days'=>10,
         'description'=>"Drift between overwater bungalows, bioluminescent lagoons, and reefs alive with manta rays. Complete seclusion on your own stretch of Indian Ocean.",
         'highlights'=>"Overwater villa with glass floor|Guided manta ray snorkel|Sunset dolphin cruise|In-villa spa treatment|Private sandbank picnic",
         'image_url'=>'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>3,'featured'=>1,'budget'=>0,'name'=>'Japan in Bloom','slug'=>'japan-in-bloom',
         'country'=>'Japan','region'=>'Asia','price'=>3800,'duration_days'=>14,
         'description'=>"Cherry blossom season in Kyoto, a private dawn ascent of Fuji, and ryokans where hospitality is an art form and time deliberately slows.",
         'highlights'=>"Private dawn access to Fushimi Inari|Cherry blossom picnic in Maruyama Park|Guided tea ceremony in Uji|Night in a century-old Kyoto machiya|Bullet train pass across Honshu",
         'image_url'=>'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>4,'featured'=>1,'budget'=>0,'name'=>'Marrakech & the Atlas','slug'=>'marrakech-atlas',
         'country'=>'Morocco','region'=>'Africa','price'=>1950,'duration_days'=>8,
         'description'=>"Riad courtyards, souks that reward wandering, and starlit dinners in a Berber camp beneath the snow-capped High Atlas.",
         'highlights'=>"Riad rooftop breakfast in the Medina|Private souk tour with a local guide|Berber camp overnight in the Atlas|Camel trek at sunrise|Hammam & argan oil ritual",
         'image_url'=>'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>5,'featured'=>1,'budget'=>0,'name'=>'Iceland Northern Lights','slug'=>'iceland-northern-lights',
         'country'=>'Iceland','region'=>'Europe','price'=>3250,'duration_days'=>7,
         'description'=>"Geothermal springs, midnight glacier walks, and the aurora borealis overhead — best experienced from a heated outdoor tub in total silence.",
         'highlights'=>"Private aurora hunting tour|Blue Lagoon geothermal spa|Langjökull glacier snowmobile|Golden Circle in a small group|Lava tunnel exploration",
         'image_url'=>'https://images.unsplash.com/photo-1504829857797-ddff29c27927?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>6,'featured'=>1,'budget'=>0,'name'=>'Amalfi & the Islands','slug'=>'amalfi-islands',
         'country'=>'Italy','region'=>'Europe','price'=>2890,'duration_days'=>9,
         'description'=>"Cliffside lemon groves, private boat days to Positano, and tables so close to the sea the spray seasons your wine.",
         'highlights'=>"Private speedboat to Capri|Ravello villa garden tour|Limoncello tasting on the terraces|Positano sunset aperitivo|Pompeii with an archaeologist guide",
         'image_url'=>'https://images.unsplash.com/photo-1555881400-74d7acaacd8b?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>7,'featured'=>1,'budget'=>0,'name'=>'Peru & Machu Picchu','slug'=>'peru-machu-picchu',
         'country'=>'Peru','region'=>'Americas','price'=>4100,'duration_days'=>12,
         'description'=>"Private sunrise at Machu Picchu before the crowds arrive, the Sacred Valley at altitude, and Lima's world-class gastronomy as a final flourish.",
         'highlights'=>"Private sunrise slot at Machu Picchu|Sacred Valley local market tour|Belmond Hiram Bingham train|Cooking class with a Lima chef|Lake Titicaca floating islands",
         'image_url'=>'https://images.unsplash.com/photo-1526392060635-9d6019884377?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>8,'featured'=>1,'budget'=>0,'name'=>'Serengeti Safari','slug'=>'serengeti-safari',
         'country'=>'Tanzania','region'=>'Africa','price'=>6500,'duration_days'=>10,
         'description'=>"The Great Migration in full thundering motion, tented camps beneath acacia trees, and the Big Five viewed at distance close enough to feel.",
         'highlights'=>"Great Migration river crossing drive|Hot air balloon at dawn|Big Five game drives twice daily|Luxury tented camp with private deck|Ngorongoro Crater descent",
         'image_url'=>'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>9,'featured'=>0,'budget'=>0,'name'=>'Norway Fjord Journey','slug'=>'norway-fjord-journey',
         'country'=>'Norway','region'=>'Europe','price'=>3600,'duration_days'=>8,
         'description'=>"Dramatic fjords carved deep into the mountainside, fishing villages unchanged for centuries, and the midnight sun gilding everything gold.",
         'highlights'=>"Geirangerfjord private boat cruise|Flåm Railway scenic journey|Kayaking on Nærøyfjord|Bergen fish market breakfast|Northern lights hike (winter departures)",
         'image_url'=>'https://images.unsplash.com/photo-1520769945061-0a448c463865?auto=format&fit=crop&w=1600&q=85'],

        // ── Budget Breaks ─────────────────────────────────────────────────────
        ['id'=>10,'featured'=>0,'budget'=>1,'name'=>'Budapest City Break','slug'=>'budapest-city-break',
         'country'=>'Hungary','region'=>'Europe','price'=>649,'duration_days'=>5,
         'description'=>"Thermal baths, ruin bars, and a skyline that rewards every early riser. Budapest delivers culture and cuisine at exceptional value.",
         'highlights'=>"Private thermal bath session at Széchenyi|Ruin bar crawl with a local guide|Hungarian cooking class|Danube sunset river cruise|Buda Castle & Fisherman's Bastion tour",
         'image_url'=>'https://images.unsplash.com/photo-1647558584325-e4b923021d76?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>11,'featured'=>0,'budget'=>1,'name'=>'Porto Wine Trail','slug'=>'porto-wine-trail',
         'country'=>'Portugal','region'=>'Europe','price'=>795,'duration_days'=>6,
         'description'=>"Azulejo-tiled facades, ancient port wine cellars, and the Douro Valley glowing amber at harvest time. Portugal's most characterful city at a price that defies belief.",
         'highlights'=>"Private port wine cellar tour & tasting|Douro Valley vineyard day trip|Livraria Lello & Ribeira walking tour|Francesinha dinner at a local favourite|Day trip to Guimarães",
         'image_url'=>'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>12,'featured'=>0,'budget'=>1,'name'=>'Tbilisi & the Caucasus','slug'=>'tbilisi-caucasus',
         'country'=>'Georgia','region'=>'Europe','price'=>720,'duration_days'=>7,
         'description'=>"One of Europe's last undiscovered gems: ancient fortresses, natural wine bars in carved cellars, and the dramatic backdrop of the Greater Caucasus.",
         'highlights'=>"Old Tbilisi sulphur bath district|Mtskheta UNESCO heritage site|Kazbegi mountain day trip|Natural wine tasting in Kakheti|Narikala Fortress sunset walk",
         'image_url'=>'https://images.unsplash.com/photo-1565008576549-57569a49371d?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>13,'featured'=>0,'budget'=>1,'name'=>'Albania Riviera','slug'=>'albania-riviera',
         'country'=>'Albania','region'=>'Europe','price'=>580,'duration_days'=>7,
         'description'=>"Crystal Ionian coves, unspoilt hill villages, and a coastline that feels like the Med did fifty years ago — at a fraction of the neighbouring price.",
         'highlights'=>"Himara private boat trip to secret coves|Gjirokastër UNESCO old town|Ksamil island hop|Fresh seafood on the harbour at Sarandë|Llogara Pass mountain drive",
         'image_url'=>'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>14,'featured'=>0,'budget'=>1,'name'=>'Vietnam Highlights','slug'=>'vietnam-highlights',
         'country'=>'Vietnam','region'=>'Asia','price'=>1895,'duration_days'=>10,
         'description'=>"Halong Bay at dawn, Hoi An's lantern-lit streets at dusk, and street food that makes the food halls of the world feel redundant.",
         'highlights'=>"Halong Bay overnight junk cruise|Hoi An old town bicycle tour|Hanoi street food evening walk|My Son Sanctuary at sunrise|Cooking class in Hoi An",
         'image_url'=>'https://images.unsplash.com/photo-1528360983277-13d401cdc186?auto=format&fit=crop&w=1600&q=85'],

        // ── Legendary Journeys ────────────────────────────────────────────────
        ['id'=>15,'featured'=>1,'budget'=>0,'name'=>"Tolkien's Middle-earth: New Zealand",'slug'=>'tolkiens-middle-earth',
         'country'=>'New Zealand','region'=>'Oceania','price'=>6800,'duration_days'=>14,
         'description'=>"From Hobbiton's rolling green hills to Tongariro's volcanic Mordor — Middle-earth made real. For those who believe the world's greatest story deserves to be lived.",
         'highlights'=>"Private after-hours Hobbiton movie set tour|Tongariro Alpine Crossing (Mount Doom) with a guide|Weta Workshop behind-the-scenes experience|Milford Sound private sunrise cruise|Queenstown helicopter over the Southern Alps|Wellington film culture & craft beer evening",
         'image_url'=>'https://images.unsplash.com/photo-1507699622108-4be3abd695ad?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>16,'featured'=>0,'budget'=>0,'name'=>'Jordan: Petra & Wadi Rum','slug'=>'jordan-petra-wadi-rum',
         'country'=>'Jordan','region'=>'Middle East','price'=>2800,'duration_days'=>9,
         'description'=>"A Wonder of the World revealed at candlelight, a Bedouin camp under a billion stars, and the silence of Wadi Rum that every traveller carries home.",
         'highlights'=>"Petra by Night — candles lining the Siq to the Treasury|Private Bedouin camp in Wadi Rum|Dead Sea float at sunset|Wadi Mujib canyon wading trail|Jerash Roman ruins with a local historian|Aqaba Red Sea diving",
         'image_url'=>'https://images.unsplash.com/photo-1539768942893-daf53e448371?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>17,'featured'=>0,'budget'=>0,'name'=>'Bhutan: The Thunder Dragon Kingdom','slug'=>'bhutan-thunder-dragon',
         'country'=>'Bhutan','region'=>'Asia','price'=>5500,'duration_days'=>10,
         'description'=>"One of the last Himalayan kingdoms, with a constitutional visitor cap and a GDP measured in happiness. Tiger's Nest clings to a 3,000-metre cliff face.",
         'highlights'=>"Tiger's Nest Monastery dawn hike (Paro Taktsang)|Government visitor permit included|Punakha Dzong riverside fortress|Traditional archery ceremony|Bumthang valley monastery circuit|Private audience with a local monk",
         'image_url'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>18,'featured'=>1,'budget'=>0,'name'=>'Antarctica: The Last Wilderness','slug'=>'antarctica-expedition',
         'country'=>'Antarctica','region'=>'Oceania','price'=>14900,'duration_days'=>14,
         'description'=>"Fewer people visit Antarctica each year than attend a Premier League match. Continent-sized icebergs, 200,000-strong penguin colonies, and a sky so dark the Milky Way resolves into something you can barely believe.",
         'highlights'=>"Zodiac landings at penguin rookeries daily|Drake Passage crossing on expedition ship|Polar plunge in Antarctic waters|Glaciologist-led ice hike|Whale watching from the bow|Naturalist lectures every evening at sea",
         'image_url'=>'https://images.unsplash.com/photo-1529258283598-8d6fe60b27f4?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>19,'featured'=>0,'budget'=>0,'name'=>'India: Taj at Sunrise & the Golden Triangle','slug'=>'india-taj-golden-triangle',
         'country'=>'India','region'=>'Asia','price'=>2950,'duration_days'=>12,
         'description'=>"The Taj Mahal at first light — marble so luminous it seems lit from within. Jaipur's pink palaces and Delhi's 3,000 years of layered history complete one of the world's great itineraries.",
         'highlights'=>"Private Taj Mahal sunrise access before the crowds|Agra Fort & Mehtab Bagh at dusk|Jaipur City Palace rooftop dinner|Amber Fort at dawn|Old Delhi street food tour by rickshaw|Fatehpur Sikri abandoned Mughal city",
         'image_url'=>'https://images.unsplash.com/photo-1564507592333-c60657eea523?auto=format&fit=crop&w=1600&q=85'],

        // ── Americas ──────────────────────────────────────────────────────────
        ['id'=>20,'featured'=>1,'budget'=>0,'name'=>'Patagonia: Torres del Paine','slug'=>'patagonia-torres-del-paine',
         'country'=>'Chile','region'=>'Americas','price'=>5200,'duration_days'=>10,
         'description'=>"The granite towers at sunrise, Glacier Grey calving into turquoise water, condors drifting overhead. The earth at its most dramatic — and most humbling.",
         'highlights'=>"Private sunrise trek to Base Las Torres|Glacier Grey boat trip & ice trekking|Condor watching at Mirador del Condor|Perito Moreno glacier walkway (El Calafate)|Private estancia dinner under Patagonian stars|Torres del Paine W-trek with a mountain guide",
         'image_url'=>'https://images.unsplash.com/photo-1658312627438-30d1299683fd?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>21,'featured'=>0,'budget'=>0,'name'=>'Cuba: Havana & the Oriente','slug'=>'cuba-havana-oriente',
         'country'=>'Cuba','region'=>'Americas','price'=>1850,'duration_days'=>8,
         'description'=>"Faded grandeur and extraordinary life — Havana's pastel facades, revolutionary murals, and a music scene that bleeds through every open window. Catch it before it changes.",
         'highlights'=>"Classic car tour of Old Havana with a local historian|Private cigar rolling masterclass at a tobacco farm|Santiago de Cuba live son music evening|Trinidad colonial cobblestones at golden hour|Rum tasting at Havana Club with the master distiller|Bay of Pigs & Ciénaga de Zapata wetlands",
         'image_url'=>'https://images.unsplash.com/photo-1570299437488-d430e1e677c7?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>22,'featured'=>1,'budget'=>0,'name'=>'Galápagos Islands Expedition','slug'=>'galapagos-expedition',
         'country'=>'Ecuador','region'=>'Americas','price'=>7200,'duration_days'=>10,
         'description'=>"Charles Darwin changed his mind here. Sea lions that haven't learned fear, blue-footed boobies three feet from your boots, marine iguanas that ignore you entirely.",
         'highlights'=>"Snorkel with hammerhead sharks at Kicker Rock|Sea lion encounter on Española Island|Blue-footed booby nesting colony|Giant tortoise reserve in Santa Cruz highlands|Marine iguana colony on Fernandina|Live-aboard expedition yacht with a resident naturalist",
         'image_url'=>'https://images.unsplash.com/photo-1696772686504-09010046b1d3?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>23,'featured'=>0,'budget'=>0,'name'=>'Colombia: Cartagena & Coffee Country','slug'=>'colombia-cartagena-coffee',
         'country'=>'Colombia','region'=>'Americas','price'=>1750,'duration_days'=>9,
         'description'=>"Cartagena's walled city blazes with colour at dusk. The Zona Cafetera rewards those who venture inland with cloud-forest coffee farms, wax-palm valleys, and a warmth of welcome.",
         'highlights'=>"Walled city of Cartagena at golden hour with a local guide|Coffee farm stay in Salento (Zona Cafetera)|Cocora Valley wax palm hike|Sunset cocktails on Cartagena's city walls|Mompox — Colombia's most beautiful colonial town|Street food tour of Bogotá's La Candelaria",
         'image_url'=>'https://images.unsplash.com/photo-1534943441045-1009d7cb0bb9?auto=format&fit=crop&w=1600&q=85'],

        // ── North America ─────────────────────────────────────────────────────
        ['id'=>24,'featured'=>0,'budget'=>0,'name'=>'New York City: Icons & Neighbourhoods','slug'=>'new-york-city',
         'country'=>'USA','region'=>'Americas','price'=>2650,'duration_days'=>8,
         'description'=>"Eight nights isn't nearly enough, but it's a brilliant start. Central Park at dawn, the High Line over Chelsea, a proper New York slice, and a Broadway show on a Wednesday when the tourists are elsewhere.",
         'highlights'=>"Private guided walk through Brooklyn Bridge Park & DUMBO|Sunset drinks at 230 Fifth rooftop with Empire State views|Behind-the-scenes Broadway backstage tour|Harlem gospel Sunday brunch|Chelsea gallery district with an art-world guide|Staten Island Ferry at dusk — best skyline shot in the city",
         'image_url'=>'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>25,'featured'=>1,'budget'=>0,'name'=>'Canadian Rockies: Banff to Jasper','slug'=>'canadian-rockies-banff-jasper',
         'country'=>'Canada','region'=>'Americas','price'=>3450,'duration_days'=>10,
         'description'=>"The Icefields Parkway has been called the most spectacular road on earth. Moraine Lake at sunrise with nobody else there. Elk wandering through Jasper townsite at dusk.",
         'highlights'=>"Moraine Lake at sunrise — private vehicle, early access|Athabasca Glacier ice walk with a glaciologist guide|Banff Upper Hot Springs evening session|Wildlife safari along the Icefields Parkway (bear, elk, bighorn sheep)|Whitewater rafting on the Athabasca River|Helicopter flight over the Columbia Icefield",
         'image_url'=>'https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&w=1600&q=85'],

        ['id'=>26,'featured'=>0,'budget'=>0,'name'=>'American Southwest: The Big Five Parks','slug'=>'american-southwest-parks',
         'country'=>'USA','region'=>'Americas','price'=>2950,'duration_days'=>10,
         'description'=>"Zion, Bryce Canyon, Capitol Reef, Canyonlands, and Arches in ten days. We've timed each park so you arrive in the right light, every time.",
         'highlights'=>"Angels Landing summit trail at Zion (permit included)|Bryce Canyon hoodoos at golden hour from Inspiration Point|Horseshoe Bend overlook at sunrise|Delicate Arch at sunset with a local guide|Dead Horse Point State Park|Stargazing session at Capitol Reef (world-class dark skies)",
         'image_url'=>'https://images.unsplash.com/photo-1575408264798-b50b252663e6?auto=format&fit=crop&w=1600&q=85'],
    ];
}




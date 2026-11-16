<?php
declare(strict_types=1);

/**
 * TravelManager
 *
 * Central data-access layer for Meridian Horizons.
 * All methods use PDO prepared statements; no raw string interpolation.
 */
class TravelManager
{
    public function __construct(private PDO $db) {}

    // ───────────────────────── DESTINATIONS ─────────────────────────

    public function getFeaturedDestinations(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM destinations WHERE featured = 1 ORDER BY created_at DESC LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllDestinations(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM destinations ORDER BY featured DESC, name ASC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countDestinations(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM destinations')->fetchColumn();
    }

    public function searchDestinations(string $query, string $region = '', float $maxPrice = 0): array
    {
        $conditions = [];
        $params     = [];

        if ($query !== '') {
            $conditions[] = '(name LIKE :q OR country LIKE :q OR description LIKE :q)';
            $params[':q'] = '%' . $query . '%';
        }
        if ($region !== '') {
            $conditions[] = 'region = :region';
            $params[':region'] = $region;
        }
        if ($maxPrice > 0) {
            $conditions[] = 'price <= :max_price';
            $params[':max_price'] = $maxPrice;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $sql   = "SELECT * FROM destinations {$where} ORDER BY featured DESC, price ASC LIMIT 50";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDestinationBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM destinations WHERE slug = :slug');
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getDestinationById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM destinations WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function createDestination(array $d): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO destinations
                (name, slug, country, region, description, highlights, price, duration_days, image_url, featured)
             VALUES
                (:name, :slug, :country, :region, :description, :highlights, :price, :duration_days, :image_url, :featured)'
        );
        return $stmt->execute([
            ':name'          => $d['name'],
            ':slug'          => slugify($d['name']),
            ':country'       => $d['country'],
            ':region'        => $d['region']        ?? '',
            ':description'   => $d['description'],
            ':highlights'    => $d['highlights']    ?? null,
            ':price'         => (float) $d['price'],
            ':duration_days' => (int)   $d['duration_days'],
            ':image_url'     => $d['image_url']     ?? '',
            ':featured'      => (int)   ($d['featured'] ?? 0),
        ]);
    }

    public function updateDestination(int $id, array $d): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE destinations
             SET name=:name, country=:country, region=:region, description=:description,
                 highlights=:highlights, price=:price, duration_days=:duration_days,
                 image_url=:image_url, featured=:featured
             WHERE id=:id'
        );
        return $stmt->execute([
            ':name'          => $d['name'],
            ':country'       => $d['country'],
            ':region'        => $d['region']        ?? '',
            ':description'   => $d['description'],
            ':highlights'    => $d['highlights']    ?? null,
            ':price'         => (float) $d['price'],
            ':duration_days' => (int)   $d['duration_days'],
            ':image_url'     => $d['image_url']     ?? '',
            ':featured'      => (int)   ($d['featured'] ?? 0),
            ':id'            => $id,
        ]);
    }

    public function deleteDestination(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM destinations WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    // ───────────────────────── AUTH & USERS ─────────────────────────

    public function getUserByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getUserById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getAllUsers(): array
    {
        return $this->db->query(
            'SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC'
        )->fetchAll();
    }

    public function countUsers(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    /**
     * Registers a new user. Returns true on success, false if email already exists.
     */
    public function registerUser(string $name, string $email, string $password): bool
    {
        if ($this->getUserByEmail($email) !== null) {
            return false;
        }
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)'
        );
        return $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hash]);
    }

    /**
     * Verifies credentials and populates $_SESSION on success.
     */
    public function loginUser(string $email, string $password): bool
    {
        $user = $this->getUserByEmail($email);
        if ($user === null) {
            return false;
        }
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        // Regenerate session ID on privilege change
        session_regenerate_id(true);
        $_SESSION['user_id']    = (int)    $user['id'];
        $_SESSION['user_name']  = (string) $user['name'];
        $_SESSION['user_email'] = (string) $user['email'];
        $_SESSION['user_role']  = (string) $user['role'];
        return true;
    }

    public function updateUserRole(int $id, string $role): bool
    {
        if (!in_array($role, ['user', 'admin'], true)) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE users SET role = :role WHERE id = :id');
        return $stmt->execute([':role' => $role, ':id' => $id]);
    }

    public function deleteUser(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    // ───────────────────────── BASKET ──────────────────────────────

    public function getBasket(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT bi.*, d.name, d.slug, d.country, d.image_url, d.price, d.duration_days
             FROM basket_items bi
             JOIN destinations d ON d.id = bi.destination_id
             WHERE bi.user_id = :uid
             ORDER BY bi.added_at DESC'
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function addToBasket(int $userId, int $destinationId, int $passengers = 1): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO basket_items (user_id, destination_id, passengers)
             VALUES (:uid, :did, :pax)
             ON CONFLICT(user_id, destination_id) DO UPDATE SET passengers = excluded.passengers'
        );
        return $stmt->execute([':uid' => $userId, ':did' => $destinationId, ':pax' => $passengers]);
    }

    public function removeFromBasket(int $itemId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM basket_items WHERE id = :id AND user_id = :uid'
        );
        return $stmt->execute([':id' => $itemId, ':uid' => $userId]);
    }

    public function clearBasket(int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM basket_items WHERE user_id = :uid');
        return $stmt->execute([':uid' => $userId]);
    }

    public function getBasketTotal(int $userId): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(d.price * bi.passengers), 0)
             FROM basket_items bi
             JOIN destinations d ON d.id = bi.destination_id
             WHERE bi.user_id = :uid'
        );
        $stmt->execute([':uid' => $userId]);
        return (float) $stmt->fetchColumn();
    }

    // ───────────────────────── BOOKINGS ─────────────────────────

    public function createBooking(int $userId, int $destinationId, string $travelDate, int $passengers, string $notes = ''): bool
    {
        $dest = $this->getDestinationById($destinationId);
        if ($dest === null) {
            return false;
        }
        $total = (float) $dest['price'] * $passengers;
        $stmt  = $this->db->prepare(
            'INSERT INTO bookings (user_id, destination_id, travel_date, passengers, total_price, notes)
             VALUES (:uid, :did, :date, :pax, :total, :notes)'
        );
        return $stmt->execute([
            ':uid'   => $userId,
            ':did'   => $destinationId,
            ':date'  => $travelDate,
            ':pax'   => $passengers,
            ':total' => $total,
            ':notes' => $notes,
        ]);
    }

    public function getUserBookings(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*, d.name, d.country, d.image_url, d.slug
             FROM bookings b
             JOIN destinations d ON d.id = b.destination_id
             WHERE b.user_id = :uid
             ORDER BY b.created_at DESC'
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function getAllBookings(): array
    {
        return $this->db->query(
            'SELECT b.*, d.name AS destination_name, d.country, u.name AS user_name, u.email
             FROM bookings b
             JOIN destinations d ON d.id = b.destination_id
             JOIN users u ON u.id = b.user_id
             ORDER BY b.created_at DESC'
        )->fetchAll();
    }

    public function countBookings(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
    }

    public function updateBookingStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['pending', 'confirmed', 'cancelled'], true)) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE bookings SET status = :status WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    // ───────────────────────── ADMIN STATS ───────────────────────

    public function getDashboardStats(): array
    {
        $revenue = $this->db->query(
            "SELECT COALESCE(SUM(total_price),0) FROM bookings WHERE status != 'cancelled'"
        )->fetchColumn();

        return [
            'total_users'        => $this->countUsers(),
            'total_destinations' => $this->countDestinations(),
            'total_bookings'     => $this->countBookings(),
            'total_revenue'      => (float) $revenue,
        ];
    }

    /** Returns distinct region names for filter UI. */
    public function getRegions(): array
    {
        return $this->db->query(
            "SELECT DISTINCT region FROM destinations WHERE region != '' ORDER BY region"
        )->fetchAll(PDO::FETCH_COLUMN);
    }

    // ───────────────────────── BOOKING DETAIL ────────────────────────

    public function getBookingById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*,
                    d.name AS destination_name, d.country, d.image_url, d.slug,
                    d.price AS base_price, d.duration_days, d.highlights,
                    u.name AS user_name, u.email AS user_email
             FROM bookings b
             JOIN destinations d ON d.id = b.destination_id
             JOIN users        u ON u.id = b.user_id
             WHERE b.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // ───────────────────────── USER MANAGEMENT ───────────────────────

    /**
     * Update a user's name, email and role.
     * Returns false if the email is already taken by a different user.
     */
    public function updateUser(int $id, string $name, string $email, string $role): bool
    {
        if (!in_array($role, ['user', 'admin'], true)) {
            return false;
        }
        $existing = $this->getUserByEmail($email);
        if ($existing !== null && (int)$existing['id'] !== $id) {
            return false;
        }
        $stmt = $this->db->prepare(
            'UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id'
        );
        return $stmt->execute([':name' => $name, ':email' => $email, ':role' => $role, ':id' => $id]);
    }

    /** Set a new bcrypt password for a user. */
    public function resetUserPassword(int $id, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare('UPDATE users SET password = :password WHERE id = :id');
        return $stmt->execute([':password' => $hash, ':id' => $id]);
    }

    /**
     * Create a user account from the admin panel.
     * Returns false if the email is already registered.
     */
    public function adminCreateUser(string $name, string $email, string $password, string $role = 'user'): bool
    {
        if (!in_array($role, ['user', 'admin'], true)) {
            return false;
        }
        if ($this->getUserByEmail($email) !== null) {
            return false;
        }
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
        );
        return $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hash, ':role' => $role]);
    }
}

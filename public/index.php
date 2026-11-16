<?php
declare(strict_types=1);

// When the web server document root is this public/ directory, every request
// that is not a real static file is handled by the application front
// controller in the parent directory. Keeping the front controller and the
// database outside public/ is what prevents /database/meridian.db from being
// served as a static file.
require dirname(__DIR__) . '/index.php';

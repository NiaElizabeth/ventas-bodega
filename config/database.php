<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$environmentFile = $root . DIRECTORY_SEPARATOR . '.env';
$fileSettings = [];

if (is_file($environmentFile)) {
    $parsedSettings = parse_ini_file($environmentFile, false, INI_SCANNER_RAW);
    if ($parsedSettings === false) {
        throw new \RuntimeException('No se pudo leer el archivo .env.');
    }
    $fileSettings = $parsedSettings;
}

$setting = static function (string $key) use ($fileSettings): string {
    $environmentValue = getenv($key);
    if ($environmentValue !== false) {
        return $environmentValue;
    }

    if (!array_key_exists($key, $fileSettings)) {
        throw new \RuntimeException('Falta configurar ' . $key . ' en el entorno o en .env.');
    }

    return (string) $fileSettings[$key];
};

$host = $setting('DB_HOST');
$database = $setting('DB_NAME');
$username = $setting('DB_USER');
$password = $setting('DB_PASS');

return new \PDO(
    sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $database),
    $username,
    $password,
    [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false,
    ]
);

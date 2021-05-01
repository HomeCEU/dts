<?php


namespace HomeCEU\DTS\Db;


class Config {
  const DEFAULT_DRIVER          = 'mysql';
  const DEFAULT_MYSQL_PORT      = 3306;
  const DEFAULT_PG_PORT         = 5432;
  const DEFAULT_SQLITE_LOCATION = ':memory:';

  public string $driver='';
  public string $location = '';
  public string $host = '';
  public string $user = '';
  public string $pass = '';
  public string $name = '';
  public string $port = '';

  public function __construct(array $config) {
    foreach ($config as $k=>$v)
      $this->{$k} = $v;
  }

  public static function fromEnv(): self {
    return new self(
        [
            'driver' => getenv('DB_DRIVER') ?: self::DEFAULT_DRIVER,
            'host'   => getenv('DB_HOST'),
            'name'   => getenv('DB_NAME'),
            'user'   => getenv('DB_USER'),
            'pass'   => getenv('DB_PASS'),
            'port'   => getenv('DB_PORT') ?: self::DEFAULT_MYSQL_PORT
        ]
    );
  }

  public static function sqlite($location = self::DEFAULT_SQLITE_LOCATION): self {
    return new self(
        [
            'driver'   => 'sqlite',
            'location' => $location
        ]
    );
  }

  public static function mysql($host, $name, $user, $pass, $port = self::DEFAULT_MYSQL_PORT): self {
    return new self(
        [
            'driver' => 'mysql',
            'host'   => $host,
            'name'   => $name,
            'user'   => $user,
            'pass'   => $pass,
            'port'   => $port
        ]
    );
  }

  public function dsn(): string {
    if ($this->isSqlite()) {
      return $this->sqliteDsn();
    }
    return $this->otherDsn();
  }

  public function isSqlite(): bool {
    return substr($this->driver, 0, 6) == 'sqlite';
  }

  private function sqliteDsn(): string {
    return sprintf(
        "%s:%s",
        $this->driver,
        $this->location
    );
  }

  private function otherDsn(): string {
    return sprintf(
        "%s:host=%s;port=%s;dbname=%s",
        $this->driver,
        $this->host,
        $this->port,
        $this->name
    );
  }
}

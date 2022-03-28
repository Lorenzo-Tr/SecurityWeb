<?php
class Database
{
	public $pdo;
	public $iv;
	public $key;

	/**
	 * @param $db
	 * @param null $username
	 * @param null $password
	 * @param string $host
	 * @param int $port
	 * @param array $options
	 */
	public function __construct($db, $username = NULL, $password = NULL, $host = '127.0.0.1', $port = 3306, $options = [])
	{
		$default_options = [
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		];
		$options = array_replace($default_options, $options);
		$dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";

		try {
			$this->pdo = new \PDO($dsn, $username, $password, $options);
		} catch (\PDOException $e) {
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
	}

	/**
	 * @param $sql
	 * @param null $args
	 * @return false|PDOStatement
	 */
	public function run($sql, $args = NULL)
	{
		if (!$args)
		{
			return $this->pdo->query($sql);
		}
		$stmt = $this->pdo->prepare($sql);
		if($stmt->execute($args)){
			return true;
		}
		return false;
	}

	public function generate_encryption_key()
	{
		$iv_len = openssl_cipher_iv_length("aes256");
		$this->iv = openssl_random_pseudo_bytes($iv_len);
		$this->key = openssl_random_pseudo_bytes(256/4);
	}

	public function encrypt_data($text = ''){
		if(!is_array($text))
			return openssl_encrypt($text, "aes256", $this->key, $options=0, $this->iv);
		foreach ($text as $key => $val){
			$text[$key] = openssl_encrypt($val, "aes256", $this->key, $options=0, $this->iv);
		}
		return $text;
	}

	public function change_encrypt_key($key = null, $iv = null){
		if(!$key && !$iv){
			$this->generate_encryption_key();
		}else{
			$this->key = $key;
			$this->iv = $iv;
		}
	}
}
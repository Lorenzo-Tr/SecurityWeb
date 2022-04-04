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
	 * @param $args
	 * @return bool
	 */
	public function run($sql, $args = NULL): bool
	{
		return $this->pdo->prepare($sql)->execute($args);
	}


	/**
	 * @param $table
	 * @param $args
	 * @return bool
	 */
	public function create($table, $args = NULL): bool
	{
		$keys = array_keys($args);
		$fields = '`' . implode('`, `', $keys) . '`';

		$placeholder = substr(str_repeat('?,', count($keys)), 0, -1);

		return $this->pdo->prepare("INSERT INTO $table ($fields) VALUES($placeholder)")->execute(array_values($args));
	}


	/**
	 * @param $table
	 * @param $fields
	 * @param $where
	 * @return mixed
	 */
	public function get($table, $fields = NULL, $where = NULL, $order = [], $option = PDO::FETCH_OBJ)
	{
		if (!empty($fields)) {
			$S_fields = '`' . implode('`, `', $fields) . '`';
		} else {
			$S_fields = ' * ';
		}

		$keys = array_keys($where);
		$where_fields = implode('=? AND ', $keys) . '=?';

		$sql = "SELECT $S_fields FROM $table";
		if (!empty($where_fields)) {
			$sql .= " WHERE $where_fields";
		}

        if (!empty($order)){
            $sql .= " ORDER BY " . implode('', array_keys($order)) . ' ' . implode('', array_values($order));
        }

		$q = $this->pdo->prepare($sql);
		$q->execute(array_values($where));
		return $q->fetch($option);
	}

	/**
	 * @param $table
	 * @param $fields
	 * @param $where
	 * @return mixed
	 */
	public function update($table, $fields = NULL, $where = NULL)
	{
		$keys = array_keys($fields);
		$set_fields = implode('=?, ', $keys) . '=?';

		$keys = array_keys($where);
		$where_fields = implode('=? AND ', $keys) . '=?';

		var_dump("UPDATE $table SET $set_fields WHERE $where_fields");

		return $this->pdo->prepare("UPDATE $table SET $set_fields WHERE $where_fields")->execute(array_merge(array_values($fields), array_values($where)));
	}

	/**
	 * @param $table
	 * @param $fields
	 * @param $where
	 * @param int $option
	 * @return mixed
	 */
	public function replace($table, $args = NULL)
	{
		$keys = array_keys($args);
		$set_fields = implode('=?, ', $keys) . '=?';

		var_dump("REPLACE INTO $table SET $set_fields");
		return $this->pdo->prepare("REPLACE INTO $table SET $set_fields")->execute(array_values($args));
	}

	public function generate_encryption_key()
	{
		$iv_len = openssl_cipher_iv_length("aes256");
		$this->iv = openssl_random_pseudo_bytes($iv_len);
		$this->key = openssl_random_pseudo_bytes(256 / 4);
	}

	/**
	 * @param string|array $text
	 * @return array|false|string
	 */
	public function encrypt_data($text = '')
	{
		if (!is_array($text))
			return openssl_encrypt($text, "aes256", $this->key, $options = 0, $this->iv);
		foreach ($text as $key => $val) {
			$text[$key] = openssl_encrypt($val, "aes256", $this->key, $options = 0, $this->iv);
		}
		return $text;
	}

	/**
	 * @param string|array $text
	 * @return array|false|string
	 */
	public function decrypt_data($text = '')
	{
		if (!is_array($text))
			return openssl_decrypt($text, "aes256", $this->key, $options = 0, $this->iv);
		foreach ($text as $key => $val) {
			$text[$key] = openssl_decrypt($val, "aes256", $this->key, $options = 0, $this->iv);
		}
		return $text;
	}

	public function change_encrypt_key($key = null, $iv = null)
	{
		if (!$key && !$iv) {
			$this->generate_encryption_key();
		} else {
			$this->key = $key;
			$this->iv = $iv;
		}
	}
}
<?php

class Database 
{
	private
	private static $connection = null;

	private function __construct()
	{
		throw new \Exception('Not implemented');
	}

	public static function connect() {
		if (self::$connection === null) 
		{
			self::$connection = new PDO(
				"psql:host=localhost;dbname=metis"
			);
		}
	}
}
?>

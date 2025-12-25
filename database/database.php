<?php

class Database 
{

	private static $host = 'localhost';
	private static $dbname = 'metis';
	private static $user = 'metis_user';
	private static $password = 'metis123';

	private static ?PDO $connection = null;
	private function __construct()
	{
	}

	public static function connect() {
		if (self::$connection === null) 
		{
			self::$connection = new PDO(
				"pgsql:host=" . self::$host . ";dbname=" . self::$dbname,
				 self::$user,
				 self::$password,
				
				[
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
				]
			);
		}
		return self::$connection;
	}
}
?>

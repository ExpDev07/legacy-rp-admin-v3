<?php

use Illuminate\Support\Facades\DB;
use Dotenv\Dotenv;
use Illuminate\Support\Facades\Config;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

function runQuery(string $cluster, string $query)
{
	$dir = realpath(__DIR__ . '/../envs/' . $cluster);
	$env = $dir . '/.env';

	if (empty($env) || !file_exists($env)) {
		return [false, "Failed to read .env file"];
	}

	$contents = file_get_contents($env);

	$dotenv = Dotenv::createImmutable($dir, ".env");
	$envData = $dotenv->parse($contents);

	$dbName = 'cluster_' . $cluster;

	Config::set('database.connections.' . $dbName, [
		'driver' => $envData['DB_CONNECTION'],
		'host' => $envData['DB_HOST'],
		'port' => $envData['DB_PORT'],
		'database' => $envData['DB_DATABASE'],
		'username' => $envData['DB_USERNAME'],
		'password' => $envData['DB_PASSWORD']
	]);

	try {
        DB::connection($dbName)->getPdo();
    } catch (\Exception $e) {
        return [false, "Failed to connect to database: " . $e->getMessage()];
    }

	$affected = DB::connection($dbName)->statement($query);

	return [true, "Affected " . $affected . " rows"];
}

// UPDATE `inventories` SET `item_name` = 'weapon_addon_hk416' WHERE `item_name` = 'weapon_addon_m4'
Artisan::command('run-query', function() {
	$query = trim($this->ask("SQL> "));

	if (empty($query)) {
		$this->error('Query is empty');

		return;
	}

	$this->info('Iterating through all clusters...');

	$dir = __DIR__ . '/../envs';

	$clusters = array_diff(scandir($dir), ['.', '..']);

	chdir(__DIR__ . '/..');

	foreach ($clusters as $cluster) {
		$cluster = trim($cluster);

		$path = $dir . '/' . $cluster;

		if (empty($cluster) || !is_dir($path)) {
			continue;
		}

		$this->info('Running query on cluster `' . $cluster . '`...');

		$result = runQuery($cluster, $query);

		if (!$result[0]) {
			$this->error(" - " . $result[1]);
		} else {
			$this->comment(" - " . $result[1]);
		}
	}

	return;
})->describe('Runs a query on all clusters.');

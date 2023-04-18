<?php

use Illuminate\Support\Facades\DB;
use Dotenv\Dotenv;

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

	$dotenv = Dotenv::createImmutable($dir);
	$dotenv->load();

	config(['database.connections.mysql' => [
		'host' => getenv('DB_HOST'),
		'port' => getenv('DB_PORT'),
		'database' => getenv('DB_DATABASE'),
		'username' => getenv('DB_USERNAME'),
		'password' => getenv('DB_PASSWORD'),
	]]);

	try {
        DB::connection()->getPdo();
    } catch (\Exception $e) {
        return [false, "Failed to connect to database: " . $e->getMessage()];
    }

	$affected = DB::statement($query);

	return [true, "Affected " . $affected . " rows"];
}

// UPDATE `inventories` SET `item_name` = 'weapon_addon_hk416' WHERE `item_name` = 'weapon_addon_m4'
Artisan::command('run-query {query}', function(string $query) {
	$query = trim($query);

	if (empty($query)) {
		$this->error('Query is empty');

		return;
	}

	if (!defined("HAS_CLUSTER_ARG")) {
		$this->warn('No cluster argument defined, iterating through all clusters...');

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
				$this->error($result[1]);
			} else {
				$this->info($result[1]);
			}
		}

		return;
	} else {

	}
})->describe('Runs a query');

<?php

use Illuminate\Support\Facades\DB;

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

// UPDATE `inventories` SET `item_name` = 'weapon_addon_hk416' WHERE `item_name` = 'weapon_addon_m4'
Artisan::command('run-query {query}', function(string $query) {
	if (!defined("HAS_CLUSTER_ARG")) {
		$this->error('No cluster argument defined, iterating through all clusters...');

		$dir = __DIR__ . '/../envs';

		$clusters = array_diff(scandir($dir), ['.', '..']);

		foreach ($clusters as $cluster) {
			$cluster = trim($cluster);

			$path = $dir . '/' . $cluster;

			if (empty($cluster) || !is_dir($path)) {
				continue;
			}

			$this->info('Running query on cluster: ' . $cluster);

			$command = 'php ' . __DIR__ . '/../artisan run-query ' . json_encode($query) . ' --cluster=' . $cluster;

			$this->info(' - ' . $command);

			$output = shell_exec($command);

			$this->info('Output: ' . $output);
		}

		return;
	}

	$query = trim($query);

	if (empty($query)) {
		$this->error('Query is empty');

		return;
	}

	$this->info('Running query: `' . $query . '`...');

	$affected = DB::statement($query);

	$this->info('Query affected ' . $affected . ' rows.');
})->describe('Runs a query');

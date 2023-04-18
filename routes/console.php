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

			$command = 'php artisan run-query --cluster=' . $cluster . ' ' . json_encode($query);

			$this->comment(' - ' . $command);

			$output = shell_exec($command);

			$this->comment($output);
		}

		return;
	}

	$query = trim($query);

	if (empty($query)) {
		$this->error('Query is empty');

		return;
	}

	$this->info('Running query on cluster `' . CLUSTER . '`...');

	$affected = DB::statement($query);

	$this->info('Query affected ' . $affected . ' rows.');
})->describe('Runs a query');

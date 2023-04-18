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
	$this->info(defined("CLUSTER") ? CLUSTER : "NO CLUSTER DEFINED");

	$query = trim($query);

	if (empty($query)) {
		$this->error('Query is empty');

		return;
	}

	$this->info('Running query: `' . $query . '`...');

	$affected = DB::statement($query);

	$this->info('Query affected ' . $affected . ' rows.');
})->describe('Runs a query');

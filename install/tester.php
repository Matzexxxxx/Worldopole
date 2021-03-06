<?php

// Load Query Manager
// ###################

include_once __DIR__ . '/../core/process/queries/QueryManager.php';


########################################################################
// Test function
// This happend once to be sure database is still full of datas
// to avoid errors on website
########################################################################

function php_test()
{
	$lock_msg = '';

	if (version_compare(phpversion(), '5.4', '<')) {
		$lock_msg .= "Error: Sorry, your PHP version isn't supported. Please upgrade to PHP >= 5.4<br>";
	}

	return $lock_msg;
}

function db_test()
{
	$manager = QueryManager::current();

	$lock_msg = '';

	// Pokemon Test
	$result = $manager->testTotalPokemon();

	if ($result === 1) {
		$lock_msg .= "Error: No Pokémon database found<br>";
	} else if ($result === 2) {
		$lock_msg .= "Error: No Pokémon found is your database<br>";
	}

	// Gym Test
	$result = $manager->testTotalGyms();

	if ($result === 1) {
		$lock_msg .= "Error: No Gym database found<br>";
	} else if ($result === 2) {
		$lock_msg .= "Error: No Gym found is your database<br>";
	}


	// Pokéstop Test
	$result = $manager->testTotalPokestops();

	if ($result === 1) {
		$lock_msg .= "Error: No Pokestop database found<br>";
	} else if ($result === 2) {
		$lock_msg .= "Error: No Pokestop found in your database<br>";
	}

	return $lock_msg;
}

function permission_test()
{
	$lock_msg = '';

	// Can we write on install and core/json folder?
	if (!is_writable(SYS_PATH.'/install/') || !is_writeable(SYS_PATH.'/core/json/')) {
		$lock_msg .= "Error: Install can not be completed!<br>
                              Please fix install/ and core/json/ directory rights.<br>
                              Apache needs write access to both directories.<br>";
		// This is really bad exit immediately
		echo $lock_msg;
		exit();
	}

	return $lock_msg;
}

function run_tests()
{
	// Execute tests
	$errors = '';
	$errors .= php_test();
	$errors .= db_test();
	$errors .= permission_test();

	// Write lockfile on error
	$lock_file = SYS_PATH.'/install/website.lock';
	if (file_exists($lock_file)) {
		// delete old lockfile
		unlink($lock_file);
	}

	// create new file if there is an error
	if ($errors != '') {
		file_put_contents($lock_file, $errors);
	}
}

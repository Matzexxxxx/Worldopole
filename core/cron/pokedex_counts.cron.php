<?php

// get alltime pokemon counts for pokedex and pokemon pages

$maxpid = $config->system->max_pokemon;
if (is_null($pokecountdatas)) {
	$newpokecountdatas = array();
} else {
	$newpokecountdatas = $pokecountdatas;
}

$total_pokemon = 0;
$pokedex_counts = new stdClass();
for ($pid = 1; $pid <= $maxpid; $pid++) {

	if (!isset($newpokecountdatas[$pid])) {
		$emptyArray = array(
			"count" => 0,
			"last_update" => 0,
			"disappear_time" => null,
			"latitude" => null,
			"longitude" => null
		);
		$newpokecountdatas[$pid] = $emptyArray;
	}

	$last_update = $newpokecountdatas[$pid]['last_update'];

	$req = "SELECT COUNT(*) as count, UNIX_TIMESTAMP(MAX(disappear_time)) as last_timestamp, (CONVERT_TZ(MAX(disappear_time), '+00:00', '".$time_offset."')) AS disappear_time_real, latitude, longitude 
				FROM pokemon 
				WHERE pokemon_id = '".$pid."' && UNIX_TIMESTAMP(disappear_time) > '".$last_update."'";
	$result = $mysqli->query($req);
	$data = $result->fetch_object();

	if (isset($data)) {
		$count = $data->count;
	} else {
		$count = 0;
	}

	if ($count != 0) {
		$newpokecountdatas[$pid]['count'] += $count;
		$newpokecountdatas[$pid]['last_update'] = $data->last_timestamp;
		$newpokecountdatas[$pid]['disappear_time'] = $data->disappear_time_real;
		$newpokecountdatas[$pid]['latitude'] = $data->latitude;
		$newpokecountdatas[$pid]['longitude'] = $data->longitude;
		$total_pokemon += $count;
	}
}

if (!isset($newpokecountdatas["total"])) {
	$newpokecountdatas["total"] = 0;
}
$newpokecountdatas["total"] += $total_pokemon;

// Write to file
file_put_contents($pokedex_counts_file, json_encode($newpokecountdatas));

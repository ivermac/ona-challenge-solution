<?php

	function calculate($url) {
		$jsonData = file_get_contents($url);
		$jsonArray = json_decode($jsonData, true);

		$allWaterPoints = [];
		$brokenWaterPoints = [];

		foreach ($jsonArray as $row) {
			$communitiesVillages = $row['communities_villages']; 
			$waterFunctioning = $row['water_functioning'];

			if ($waterFunctioning != "yes")
				$brokenWaterPoints = increment($brokenWaterPoints, $communitiesVillages);

			$allWaterPoints = increment($allWaterPoints, $communitiesVillages);
		}

		/*
			The total of functional water points is calculated by the difference between the total of all water points and the total of
			broken water points. 'getCommunityRanking' function returns an array of all communities, each mapped to a value representing
			a percentage of the broken water points in that community and the array values are sorted from the communities with the largest
			percentage to the one with the least percentage.
		*/
		return [
			"numberAllWaterPoints" => array_sum(array_values($allWaterPoints)),
			"numberBrokenWaterPoints" => array_sum(array_values($brokenWaterPoints)),
			"numberFunctionalWaterPoints" => array_sum(array_values($allWaterPoints)) - array_sum(array_values($brokenWaterPoints)),
			"allWaterPoints" => $allWaterPoints,
			"communityRanking" => getCommunityRanking($allWaterPoints, $brokenWaterPoints)
		];
	}

	function increment($array, $key) {
		(empty($array[$key])) ? $array[$key] = 1 : $array[$key] += 1;
		return $array;
	}

	function getCommunityRanking($all, $broken) {
		$percentage = [];
		foreach ($all as $village => $total) {
			$percentage[$village] = (empty($broken[$village])) ? 0.0 : ($broken[$village] / $total ) * 100;
		}
		arsort($percentage);
		return $percentage;
	}

	var_dump(calculate("https://raw.githubusercontent.com/onaio/ona-tech/master/data/water_points.json"));
?>

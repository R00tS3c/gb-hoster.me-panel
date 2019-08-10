<?php

include("connect_db.php");

$serveri = mysql_query("SELECT * FROM `serveri` WHERE `igra` = '1'");

while($server_info = mysql_fetch_array($serveri)) {
	$port = $server_info['port'];
	$box_id = $server_info['box_id'];
	$server_ip = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$box_id'"));
	$IP = $server_ip['ip'].":".$port;

	$Rank = ServerInfo($IP, 'world_rank');

	if($Rank)
		echo "$IP | Rank : $Rank<br>";
}

function ServerInfo($ip, $type) {
	$api = file_get_contents("http://api.gametracker.rs/demo/json/server_info/$ip/");
	$data = json_decode($api, true);

	if($data['apiError']) {
		return false;
	}

	switch($type) {
		case 'name':
			return $data['name'];
		break;
		case 'game':
			return $data['gamename'];
		break;
		case 'mode':
			return $data['modname'];
		break;
		case 'world_rank':
			return $data['rank'];
		break;
		case 'balcan_rank':
			if($data['balcan'] == 1)
				return $data['rank_balcan'];
			else
				return 'Server nije lociran na Balkanu!';
		break;
		case 'players':
			return $data['players'];
		break;
		case 'slots':
			return $data['playersmax'];
		break;
		case 'status':
			switch($data['status']) {
				case 0:
					$server_status = 'Offline';
				break;
				case 1:
					$server_status = 'Online';
				break;
			}
			return $server_status;
		break;
		case 'last_update':
			return $data['last_refresh'];
		break;
		case 'players_list':
			$players = array();

			foreach($data['players_list'] as $player) {
				$pinfo['nick'] = $player['player']['name'];
				$pinfo['score'] = $player['player']['score'];
				$pinfo['time'] = $player['player']['time'];
				$pinfo['bot'] = $player['player']['is_bot'];

				$seconds = $pinfo['time'] % 60;
				$pinfo['time'] = ($pinfo['time'] - $seconds) / 60;
				$minutes = $pinfo['time'] % 60;
				$hours = ($pinfo['time'] - $minutes) / 60;

				if ($seconds<10) $seconds = '0'.$seconds;
				if ($minutes<10) $minutes = '0'.$minutes;
				if ($hours<10) $hours = '0'.$hours;
				$pinfo['time'] = $hours.':'.$minutes.":".$seconds;

				$players[] = $pinfo;
			}
			return $players;
		break;
		case 'server_added':
			return $data['timestamp_added'];
		break;
		case 'server_adder':
			return $data['adderusername'];
		break;
		case 'server_owner':
			return $data['ownerusername'];
		break;
		case 'server_owner_fname':
			return $data['ownerfirstname'];
		break;
		case 'server_owner_lname':
			return $data['ownerlastname'];
		break;
		case 'country':
			return $data['countryname'];
		break;
		case 'country_flag':
			$country_flag = 'http://static.gametracker.rs/flags/'.$data['iso2'].'.png';
			return $country_flag;
		break;
		case 'best_rank':
			return $data['rank_min'];
		break;
		case 'worst_rank':
			return $data['rank_max'];
		break;
		case 'map':
			return $data['map'];
		break;
		case 'last_map':
			return $data['lastMap'];
		break;
		case 'map_image':
			$image = 'http://banners.gametracker.rs/map/'.$data['gameshort'].'/'.$data['map'].'.jpg';
			return $image;
		break;
		case 'day_graph':
			$image = 'http://banners.gametracker.rs/'.$ip.'/graph-day/graph-day.jpg';
			return $image;
		break;
		case 'week_graph':
			$image = 'http://banners.gametracker.rs/'.$ip.'/graph-week/graph-week.jpg';
			return $image;
		break;
		case 'month_graph':
			$image = 'http://banners.gametracker.rs/'.$ip.'/graph-month/graph-month.jpg';
			return $image;
		break;
		case 'rank_graph':
			$image = 'http://banners.gametracker.rs/'.$ip.'/graph-rank/graph-rank.jpg';
			return $image;
		break;
		case 'daily_av_players':
			$data_day = explode(":", $data['players_day']);
			$average_day = array_sum($data_day)/count($data_day);
			return round($average_day, 2);
		break;
		case 'weekly_av_players':
			$data_week = explode(":", $data['players_week']);
			$average_week = array_sum($data_week)/count($data_week);
			return round($average_week, 2);
		break;
		case 'monthly_av_players':
			$data_month = explode(":", $data['players_month']);
			$average_month = array_sum($data_month)/count($data_month);
			return round($average_month, 2);
		break;
		case 'montly_av_rank':
			$rank_month = explode(":", $data['rank_month']);
			$average_rank = array_sum($rank_month)/count($rank_month);
			return round($average_rank, 0);
		break;
	}
}

?>
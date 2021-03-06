<h1>Actions Count</h1>
<table border="1">
<tr><th>Moderator</th><th>Count</th><th>Levels rated</th><th>Last time online</th></tr>
<?php
//error_reporting(0);
include "../../incl/lib/connection.php";
$query = $db->prepare("SELECT accountID, userName FROM accounts WHERE isAdmin = 1");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$mod){
	$query = $db->prepare("SELECT lastPlayed FROM users WHERE extID = :id");
	$query->execute([':id' => $mod["accountID"]]);
	$time = date("d/m/Y G:i:s", $query->fetchColumn());
	$query = $db->prepare("SELECT count(*) FROM modactions WHERE account = :id");
	$query->execute([':id' => $mod["accountID"]]);
	$actionscount = $query->fetchColumn();
	$query = $db->prepare("SELECT count(*) FROM modactions WHERE account = :id AND type = '1'");
	$query->execute([':id' => $mod["accountID"]]);
	$lvlcount = $query->fetchColumn();
	echo "<tr><td>".$mod["userName"]."</td><td>".$actionscount."</td><td>".$lvlcount."</td><td>".$time."</td></tr>";
}
?>
</table>
<h1>Actions Log</h1>
<table border="1"><tr><th>Moderator</th><th>Action</th><th>Value</th><th>Value2</th><th>LevelID</th><th>Time</th></tr>
<?php
$query = $db->prepare("SELECT * FROM modactions ORDER BY ID DESC");
$query->execute();
$result = $query->fetchAll();
foreach($result as &$action){
	//detecting mod
	$account = $action["account"];
	$query = $db->prepare("SELECT * FROM accounts WHERE accountID = :id");
	$query->execute([':id'=>$account]);
	$result2 = $query->fetchAll();
	$account = $result2[0]["userName"];
	//detecting action
	$value = $action["value"];
	$value2 = $action["value2"];
	switch($action["type"]){
		case 1:
			$actionname = "Rated a level";
			break;
		case 2:
			$actionname = "Featured change";
			break;
		case 3:
			$actionname = "Coins verification state";
			break;
		case 4:
			$actionname = "Epic change";
			break;
		case 5:
			$actionname = "Set as daily feature";
			if(is_numeric($value2)){
				$value2 = date("d/m/Y G:i:s", $value2);
			}
			break;
		case 6:
			$actionname = "Deleted a level";
			break;
		case 7:
			$actionname = "Creator change";
			break;
		case 8:
			$actionname = "Renamed a level";
			break;
		case 9:
			$actionname = "Changed level password";
			break;
		case 10:
			$actionname = "Changed demon difficulty";
			break;
		}
	if($action["type"] == 2 OR $action["type"] == 3 OR $action["type"] == 4){
		if($action["value"] == 1){
			$value = "True";
		}else{
			$value = "False";
		}
	}
	if($action["type"] == 5 OR $action["type"] == 6){
		$value = "";
	}
	$time = date("d/m/Y G:i:s", $action["timestamp"]);
	if($action["type"] == 5 AND $action["value2"] > time()){
		echo "<tr><td>".$account."</td><td>".$actionname."</td><td>".$value."</td><td>".$value2."</td><td>future</td><td>".$time."</td></tr>";
	}else{
		echo "<tr><td>".$account."</td><td>".$actionname."</td><td>".$value."</td><td>".$value2."</td><td>".$action["value3"]."</td><td>".$time."</td></tr>";
	}
	
}
?>
</table>
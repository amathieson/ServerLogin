<?php
include "vendor/autoload.php";
include "inc.php";
$host = getKeys(KEY_RCON_SERVER); // Server host name or IP
$port = 25575;                      // Port rcon is listening on
$password = getKeys(KEY_RCON); // rcon.password setting set in server.properties
$timeout = 3;                       // How long to timeout.

use Thedudeguy\Rcon;
if (isset($_GET['status'])) {
    $lim = ini_get('memory_limit');
    header("content-type: application/json");
    $rcon = new Rcon($host, $port, $password, $timeout);
    ini_set('memory_limit', '1024M');
    if ($rcon->connect()) {
        $tpssText = $rcon->sendCommand("forge tps");
        $playersText = $rcon->sendCommand("list uuids");
        $modsText = $rcon->sendCommand("forge mods");

        $maxPlayers = explode(" players", explode("max of ", $playersText)[1])[0];
        $mods = [];
        $players = [];
        $tpss = [];
        foreach (explode(", ", explode(" online: ", $playersText)[1]) as $item) {
            $matches = [];
            preg_match("{(.*) \((.*)\)}", $item, $matches);
            $name = isset($matches[1]) ? $matches[1] : '';
            $uuid = isset($matches[2]) ? $matches[2] : '';
            if ($name !== "")
                $players[] = [
                    "name" => $name,
                    "uuid" => $uuid,
                ];
        }
        usort($players, create_function(
            '$a, $b',
            'return strnatcmp($a["name"], $b["name"]);'
        ));
        foreach (explode("\n", $modsText) as $item) {
            $matches = [];
            preg_match("{mods_folder (.*) : (.*) \((.*)\)}", $item, $matches);
            $version = isset($matches[3]) ? $matches[3] : '';
            $jar = isset($matches[1]) ? $matches[1] : '';
            $name = isset($matches[2]) ? $matches[2] : '';
            if ($name !== "")
                $mods[] = [
                    "name" => $name,
                    "version" => $version,
                    "jar" => $jar
                ];
        }
        usort($mods, create_function(
            '$a, $b',
            'return strnatcmp($a["name"], $b["name"]);'
        ));
        foreach (explode("\n", $tpssText) as $item) {
            $matches = [];
            preg_match("{Dim (.*) \(.*time:(.*)ms.*TPS: (.*)}", $item, $matches);
            $tps = isset($matches[3]) ? $matches[3] : '';
            $region = isset($matches[1]) ? $matches[1] : '';
            $mtt = isset($matches[2]) ? $matches[2] : '';
            if (strpos($item, "Overall") !== false) {
                usort($tpss, create_function(
                    '$a, $b',
                    'return strnatcmp($a["region"], $b["region"]);'
                ));
                preg_match("{Overall.*time:(.*)ms.*TPS: (.*)}", $item, $matches);
                $tps = isset($matches[2]) ? $matches[2] : '';
                $region = 'Overall';
                $mtt = isset($matches[1]) ? $matches[1] : '';
            }
            if ($region !== "")
                $tpss[] = [
                    "region" => $region,
                    "mtt" => floatval($mtt),
                    "tps" => floatval($tps)
                ];
        }
        echo json_encode([
            "tpss" => $tpss,
            "maxPlayers" => $maxPlayers,
            "players" => $players,
            "mods" => $mods
        ], JSON_PRETTY_PRINT);
    }
    else {
        echo json_encode([
            "tpss" => [],
            "maxPlayers" => 0,
            "players" => [],
            "mods" => []
        ], JSON_PRETTY_PRINT);
    }
    ini_set('memory_limit', $lim);
}
<?php
include "vendor/autoload.php";
include "inc.php";
$host = getKeys(KEY_RCON_SERVER); // Server host name or IP
$port = 25575;                      // Port rcon is listening on
$password = getKeys(KEY_RCON); // rcon.password setting set in server.properties
$timeout = 3;                       // How long to timeout.

use Thedudeguy\Rcon;
$_GUILDID = getKeys(KEY_GUILD);
if (isset($_GET['code'], $_GET['state'])) {
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discord.com/api/v10/oauth2/token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            "client_id" => getKeys(KEY_CLIENT_ID),
            "client_secret" => getKeys(KEY_CLIENT_SECRET),
            "grant_type" => "authorization_code",
            "code" => $_GET['code'],
            "redirect_uri" => getKeys(KEY_REDIRECT),
        ]));  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'Cache-Control: no-cache',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $rtn = curl_exec($ch);
        $token = json_decode($rtn)->access_token;
        curl_close($ch);

        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, "https://discord.com/api/v10/users/@me/guilds");
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            'Cache-Control: no-cache',
            'Authorization: Bearer ' . $token
        ];
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);

        $guilds = json_decode(curl_exec($ch2));
        $member = false;
        foreach ($guilds as $guild) {
            if ($guild->id == $_GUILDID) {
                $member = true;
                break;
            }
        }
        curl_close($ch2);

        if ($member) {
            $user = json_decode(file_get_contents("https://api.minetools.eu/uuid/" . trim(explode(" ", $_GET['state'])[0])));
            if ($user->status === "OK") {
                $rcon = new Rcon($host, $port, $password, $timeout);

                if ($rcon->connect()) {
                    header("location: /?state=" . urlencode($rcon->sendCommand("whitelist add " . trim(explode(" ", $_GET['state'])[0]))));;
                }
            } else {
                header("location: /?state=" . urlencode("Unrecognised Username!"));;
            }

        } else {
            header("location: /?state=" . urlencode("You are not a Discord Member - You cannot join this server!"));;
        }
    } catch (Exception $exception) {
        header("location: /?state=" . urlencode("There was an error with that, please try again later!"));
    }

}
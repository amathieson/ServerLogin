<?php
const KEY_GUILD = 0;
const KEY_CLIENT_ID = 1;
const KEY_CLIENT_SECRET = 2;
const KEY_REDIRECT = 3;
const KEY_RCON = 4;
const KEY_RCON_SERVER = 5;
function getKeys($key) {
    return explode("\n",file_get_contents("_keys"))[$key];
}
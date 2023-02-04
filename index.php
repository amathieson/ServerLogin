<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Adam's Valelsia 3</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/fdb576115a.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Yanone+Kaffeesatz:wght@700&display=swap" rel="stylesheet">
</head>
<body>
<div class="backdrop">
    <video loop autoplay muted src="Minecraft%20Timelapse%20-%20Survival%20Island-te-JnGkQRYE.webm"></video>
</div>
<div class="backdrop-cover"></div>
<div class="container" id="app">
    <div class="card">
        <h1>Adam's Valelsia 3</h1>
        <p>Welcome to our new modpack! Simply download the profile and <a href="https://support.curseforge.com/en/support/solutions/articles/9000198501-exporting-and-importing-modpacks#Importing-an-exported-modpack">import it with the CurseForge launcher!</a></p>
        <?php
        if (!isset($_GET['state'])) {
        ?>
        <button class="btn" @click="getWhitelisted = !getWhitelisted">Get Whitelisted!</button>
        <button class="btn" @click="download()">Download the Profile</button>
        <div class="expandable whitelist" :class="{'expand':getWhitelisted}">
            <p>This server is whitelisted for the protection of its players and core system. Please enter your Minecraft username and connect your Discord account below to gain access to the server.</p>
            <sub>No Discord credentials are stored, they are simply checked against the members of the Discord server.</sub>
            <hr>
            <label class="input">
                Minecraft Username:
                <input v-model="username">
            </label>
            <button class="btn discord" @click="discord()"><i class="fa-brands fa-discord"></i> Connect With Discord</button>
        </div>
        <?php
        } else {
        ?>
            <b><?=$_GET['state']?></b>
        <?php }?>
    </div>

    <div class="card" :class="{'loading':reloading}">
        <div class="cover" :class="{'hidden-cover':loadingStatus}">Loading Status...</div>
        <button class="icon" @click="reloading = true; reload();"><i class="fa-solid fa-arrows-rotate"></i></button>
        <h2>System Status</h2>
        <hr>
        <h3>Tick Speed (Target 20TPS)</h3>
        <table class="table tps">
            <thead>
                <tr><th>Dimensions</th><th>Mean Tick Time (ms)</th><th>Mean TPS</th></tr>
            </thead>
            <tbody>
            <tr v-for="tps in tpss" :key="tps.region"><td>{{tps.region}}</td><td>{{tps.mtt.toFixed(3)}}</td><td>{{tps.tps.toFixed(3)}}</td></tr>
            </tbody>
        </table>
        <hr>
        <h3>Online Players ({{players.length}} / {{maxPlayers}})</h3>
        <div class="grid players">
            <div class="player" v-for="player in players" :key="player.uuid">
                <img :src="'https://mc-heads.net/head/' + player.uuid">
                <label>{{player.name}}</label>
            </div>
        </div>
        <hr>
        <h3>Installed Mods ({{mods.length}})</h3>
        <sub>Due to a bug in the mod detections, not all mods are displayed in this list.</sub>
        <a class="showMods" href="#" @click="showMods = !showMods"><i class="fa-solid" :class="{'fa-arrow-right':!showMods,'fa-arrow-down':showMods}"></i> Show All</a>
        <div class="expandable modList" :class="{'expand':showMods}">
            <div class="mod" v-for="mod in mods" :key="mod.jar">
                {{mod.name}} <span class="version">{{mod.version}}</span> <code>{{mod.jar}}</code>
            </div>
        </div>
    </div>
    <h6>Website by Adam Mathieson &copy; 2023 - Video <a href="https://www.youtube.com/watch?v=te-JnGkQRYE">Endavar</a></h6>
</div>
<script src="script.js"></script>
</body>
</html>
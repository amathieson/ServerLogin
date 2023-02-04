const { createApp } = Vue

const app = createApp({
    data() {
        return {
            getWhitelisted: false,
            loadingStatus:false,
            reloading: false,
            username: "",
            tpss:[],
            players:[],
            mods:[],
            maxPlayers:69,
            showMods: false
        }
    },
    created() {
        this.reload()
        setInterval(this.reload, 15000);
    },
    methods: {
        reload() {
            if (!this.isPageHidden()) {
                fetch("api.php?status").then((dta) => {
                    dta.json().then((data) => {
                        this.loadingStatus = true;
                        this.reloading = false;
                        this.tpss = data.tpss;
                        this.players = data.players;
                        this.mods = data.mods;
                        this.maxPlayers = data.maxPlayers;
                    })
                })
            }
        },
        isPageHidden(){
            return document.hidden || document.msHidden || document.webkitHidden || document.mozHidden;
        },
        discord() {
            const url = encodeURIComponent(window.location.href.includes('localhost:8000') ? "http://localhost:8000/discord.php" : "https://modded.genav.ch/discord.php");
            const state = encodeURIComponent(this.username);
            window.location.replace(`https://discord.com/api/oauth2/authorize?client_id=1071243292097912852&redirect_uri=${url}&response_type=code&scope=guilds%20identify&state=${state}`);
        }
    }
});

app.mount('#app');

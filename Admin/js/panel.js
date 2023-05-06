import sharedScripts from "./shared/sharedScripts.js";

Vue.createApp({
    data() {
        return {
            loginError: '',
            counterVal: 0
        };
    },
    methods: {
        loadingScreen: sharedScripts.loadingScreenShared,
        loadingScreenStart: sharedScripts.loadingScreenStartShared,
        checkPage: sharedScripts.checkPageShared
    },
    mounted() {
        this.loadingScreenStart();
        this.checkPage();
    },
}).mount('#panel');
import sharedScripts from "./shared/sharedScripts.js";

Vue.createApp({
    data() {
        return {
            logiError: ''
        };
    },
    methods: {
        async loginToAdminPanel() {
            try {
                this.loadingScreen('true');
                let response = await fetch("http://localhost/admin/api/adminPanel.php?type=login");
                let loginData = await response.json();
                if (loginData.loginError === 'true') {
                    this.logiError = loginData.loginErrorInfo;
                } else {
                    this.logiError = '';
                }
                this.loadingScreen('false');
            } catch (error) {
                console.log(error);
            }
        },
        loadingScreen: sharedScripts.loadingScreenShared,
        loadingScreenStart: sharedScripts.loadingScreenStartShared,
        checkIsUserLogin: sharedScripts.checkIsUserLoginShared
    },
    created() {
        Promise.all([
            this.loadingScreenStart,
            this.checkIsUserLogin
        ]);
    },
}).mount('#index');
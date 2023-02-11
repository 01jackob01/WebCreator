import sharedScripts from "./shared/sharedScripts.js";

Vue.createApp({
    data() {
        return {
            loginError: '',
            counterVal: 0
        };
    },
    methods: {
        async loginToAdminPanel() {
            try {
                this.loadingScreen('true');
                let response = await fetch("http://localhost/admin/api/adminPanel.php?type=login");
                let loginData = await response.json();
                if (loginData.loginError === 'true') {
                    this.loginError = loginData.loginErrorInfo;
                } else {
                    this.loginError = '';
                }
                this.loadingScreen('false');
            } catch (error) {
                console.log(error);
            }
        },
        async counterAdd() {
            this.counterVal = this.counterVal + 1;
            console.log(this.counterVal);
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
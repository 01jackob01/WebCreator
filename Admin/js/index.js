import sharedScripts from "./shared/sharedScripts.js";
import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/admin',
            name: 'Admin',
            component: AdminComponent
        }
    ]
});

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
                let dataToLogin = this.getDataToLogin();
                let response = await fetch("http://localhost/admin/api/adminPanel.php?type=login&dataToLogin=" + dataToLogin);
                let loginData = await response.json();
                if (loginData.loginError === 'true') {
                    this.loginError = loginData.loginErrorInfo;
                    router.push('/admin'); // przekierowanie na stronę /admin
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
        getDataToLogin() {
            let data = {};
            data.login = document.getElementById('login').value;
            data.password = document.getElementById('password').value;

            return JSON.stringify(data);
        },
        loadingScreen: sharedScripts.loadingScreenShared,
        loadingScreenStart: sharedScripts.loadingScreenStartShared,
        checkIsUserLogin: sharedScripts.checkIsUserLoginShared
    },
    mounted() {
        Promise.all([
            this.loadingScreenStart,
            this.checkIsUserLogin
        ]);
    },
}).use(router).mount('#index');

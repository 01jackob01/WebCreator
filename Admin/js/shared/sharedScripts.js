    export default {
        async checkPageShared() {
            try {
                this.loadingScreen('true');
                let response = await fetch("http://localhost/admin/api/adminPanel.php?type=checkPage");
                let pageCheck = await response.json();
                if (pageCheck.error === 'pageCheckFiled') {
                    window.location.replace("/admin/errorPage.html");
                }
                this.loadingScreen('false');
            } catch (error) {
                console.log(error);
            }
        },
        loadingScreenShared(show) {
            let loader = document.getElementById('loader');
            if (typeof loader !== 'undefined' && loader !== null) {
                if (show == 'true') {
                    document.getElementById('loader').style.display = 'block';
                } else {
                    document.getElementById('loader').style.display = 'none';
                }
            }
        },
        loadingScreenStartShared() {
            this.loadingScreen('false');
        }
    }
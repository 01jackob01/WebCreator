export default {
    checkIsUserLoginShared() {

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
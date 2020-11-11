class Utilities {

    /**
     * Fonction pour vérifier si l'admin est connécté
     */
    isAdminCheck() {


        var isAdmin = false
        var cookies = document.cookie.split(';')

        for (var i = 0; i < cookies.length; i++) {
            var cookiees = cookies[i].split('=')
            if (cookiees[i] === "isAdmin") {

                isAdmin = true
            }
        }
        return isAdmin
    }

    /**
     * Fonction pour vérifier si nous sommes dans la page adminSpace
     */
    isAdminSpace(){
        var urlCourante = document.location.href;
        var isAdminSpace = urlCourante.search('adminSpace')
 
        return isAdminSpace

    }

    /**
     * Fonction pour cacher la bannière si nous sommes dans la page adminSpace et si l'admin est connécté
     */

     hideBanner(checkAdmin){

        
        if(checkAdmin === true){
            if(isAdminSpace > 0){
                var banniere    = document.getElementById('logo')
                var header      = document.getElementsByTagName('header')
                var headerStyle = header[0].style
        
                banniere.classList.add('hidden')
                headerStyle.height = "fit-content"
            }
        }

     }
}
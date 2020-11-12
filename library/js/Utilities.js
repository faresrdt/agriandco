class Utilities {

    /**
     * Fonction pour vérifier si l'admin est connécté
     */
    isAdminCheck() {


        let isAdmin = false
        const cookies = document.cookie.split(';')

        for (let i = 0; i < cookies.length; i++) {
            let cookiees = cookies[i].split('=')
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
        const urlCourante = document.location.href;
        let isAdminSpace = urlCourante.search('adminSpace')
 
        return isAdminSpace

    }

    /**
     * Fonction pour cacher la bannière si nous sommes dans la page adminSpace et si l'admin est connécté
     */

     hideBanner(checkAdmin){

        
        if(checkAdmin === true){
            if(isAdminSpace > 0){
                let banniere    = document.getElementById('logo')
                let header      = document.getElementsByTagName('header')
                let headerStyle = header[0].style
        
                banniere.classList.add('hidden')
                headerStyle.height = "fit-content"
            }
        }

     }
}
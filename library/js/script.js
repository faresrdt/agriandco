/**
 * On instancie Utilities pour masquer la bannière lorsque l'admin est connecté et dans son espace d'administration
 */

const myUtilities = new Utilities

var checkAdmin = myUtilities.isAdminCheck()
var isAdminSpace = myUtilities.isAdminSpace()
myUtilities.hideBanner(checkAdmin)

/**
 * On utilise l'objet formValidator pour faire vérifier nos formulaire et afficher un message correspondant à l'erreur rencontrée si il y en a une
 */

// On cible les boutons avec la classe "btn_form" peu importe la page
var elements = document.getElementsByClassName('btn_form')

//On leur ajoute un event listener
for (let i = 0; i < elements.length; i++) {
    elements[i].addEventListener('click', function (event) {


        myForm = new FormValidator

        //On stock dans des variables les informations utiles pour vérifier le contentu et afficher les erreurs
        var id          = myForm.getIdForm(this)
        var formulaire  = myForm.getForm(id)
        console.log(formulaire)
        var inputs      = myForm.getInputsForm(formulaire)
        var areas       = myForm.getTextAreaForm(formulaire)

        inputs = Array.from(inputs)


        if (areas) {
            areas = Array.from(areas)
            for (var i = 0; i < areas.length; i++) {
                inputs.push(areas[i])
            }
        }

        //On vérifie si des champs du formulaire sont vides
        let check = myForm.checkIfEmpty(inputs)

        //Si des champs sont vide on stop l'execution, on créer la div error, on ajouter le message d'erreurs
        if (check != null) {
            event.preventDefault()

            //On recherche sur la page si la div d'erreur existe et on stock l'information dans une variable let
            let checkIfDivErrorExist = document.getElementsByClassName('alert_error')

            //Création de la div englobant le message d'erreur
            var divError = myForm.newElement("div", "alert_error")
            divError.classList.add(id)
            divError.id = divError.classList

            //Création de la div du message
            var divErrorP = myForm.newElement("p", "alert_error_p")
            divErrorP.id = "p_error"

            //On regarde si la variable est remplie ou non avec une condition
            if (checkIfDivErrorExist.length == 0) {

                //Si la div error n'existe pas, on l'insère avant le formulaire où il y a l'erreur
                formulaire.prepend(divError)

                //On insère dans la div d'erreur la div pour le message
                divError.append(divErrorP)

                //On insère le message dans sa div
                divErrorP.append(check)

            } else {

                //Si la div erreur existe alors on va la chercher et on la stock dans une variable
                divErr = document.querySelector(".alert_error")
                //On la supprime
                divErr.remove(divErr)
                
                //On recommence l'insertion des div et du message d'erreur
                formulaire.prepend(divError)
                divError.append(divErrorP)
                divErrorP.append(check)
            }
        }
    })
}
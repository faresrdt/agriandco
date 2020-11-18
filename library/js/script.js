/**
 * On instancie Utilities pour masquer la bannière lorsque l'admin est connécté et dans son espace d'administration
 */

const myUtilities = new Utilities

var checkAdmin = myUtilities.isAdminCheck()
var isAdminSpace = myUtilities.isAdminSpace()
myUtilities.hideBanner(checkAdmin)

/**
 * On utilise l'objet formValidator pour faire vérifier nos formulaire et afficher un message correspondant à l'erreur rencontrée si il y en a une
 */

// On cible les boutons avec la classe "btn_form" peu importe la page
var elements = document.getElementsByClassName('btn')

//On leur ajoute un event listener
for (let i = 0; i < elements.length; i++) {
    elements[i].addEventListener('click', function (event) {


        myForm = new FormValidator

        //On stock dans des variables les informations utiles pour vérifier le contentu et afficher les erreurs
        var id = myForm.getIdForm(this)
        var formulaire = myForm.getForm(id)
        var inputs = myForm.getInputsForm(formulaire)
        var areas = myForm.getTextAreaForm(formulaire)

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

            let checkIfDivErrorExist = document.getElementsByClassName('alert_error')




            if (checkIfDivErrorExist.length == 0) {


                var divError = myForm.newElement("div", "alert_error")
                divError.classList.add(id)
                divError.id = divError.classList

                var divErrorP = myForm.newElement("p", "alert_error_p")
                divErrorP.id = "p_error"

                formulaire.prepend(divError)

                divError.append(divErrorP)
                divErrorP.append(check)
            } else {

                divZebi = document.getElementsByClassName('alert_error')
                console.log(divZebi)
                document.removeChild(divZebi)

                // divError = document.getElementById(document.getElementsByClassName('alert_error').classList.replace(' ', ''))
                // console.log(divError)
                // console.log(checkIfDivErrorExist.parentNode)
                // formulaire.removeChild(checkIfDivErrorExist)


                //Si la div alert error existe déjà alors on la vide et on insère le nouveau message d'erreur
                divErrorP = document.getElementById('p_error')
                divErrorP.innerHTML = ""
                divErrorP.append(check)
            }
        }
    })
}
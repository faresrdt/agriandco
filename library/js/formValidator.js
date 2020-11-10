class FormValidator {
    constructor(form) {
        this.form = form
    }
    /**
     * Fonction pour récupérer l'id d'un formulaire
     * @param {*} form 
     */
    getIdForm(btn) {

        var id = btn.className.replace('btn ', '')
        return id

    }

    /**
     * Fonction pour récupérer les inputs d'un formulaire
     * @param {*} id 
     */
    getInputsForm(form) {

        var inputs = form.querySelectorAll('input')

        return inputs

    }

    /**
     * Fonction pour récupérer les textarea d'un formulaire
     * @param {*} id 
     */
    getTextAreaForm(form) {

        var textArea = form.querySelectorAll('textarea')

        return textArea

    }

    /**
     * Fonction pour récupérer le formulaire en entier selon son id
     * @param {*} id 
     */
    getForm(id) {

        var form = document.getElementById(id)
        return form

    }

    /**
     * Fonction pour vérifier si des inputs sont vides
     * @param {*} inputs 
     */
    checkIfEmpty(inputs) {

        var empty = null
        var fields = []

        for (var i = 0; i < inputs.length; i++) {


            if (inputs[i].value === '' || inputs[i].value === null) {


                if (inputs[i].name === "img") {

                } else {
                    fields.push(inputs[i].className)
                }

            }

        }

        if (fields.length > 1) {
            return ('Veuillez remplir les champs ' + fields.join(', '))
        } else if (fields.length == 1) {
            return ('Veuillez remplir le champ ' + fields.join(', '))
        } else {
            return null
        }

    }

}

// On cible les boutons peu importe la page
elements = document.getElementsByClassName('btn')

//On leur ajoute un event listener
for (var i = 0; i < elements.length; i++) {
    elements[i].addEventListener('click', function (event) {


        myForm = new FormValidator

        //On stock dans des variables les informations utiles pour vérifier le contentu et afficher les erreurs
        id = myForm.getIdForm(this)
        formulaire = myForm.getForm(id)
        inputs = myForm.getInputsForm(formulaire)
        areas = myForm.getTextAreaForm(formulaire)
        inputs = Array.from(inputs)


        if (areas) {
            areas = Array.from(areas)
            for (var i = 0; i < areas.length; i++) {
                inputs.push(areas[i])
            }
        }

        //On vérifie si des champs du formulaire sont vides
        check = myForm.checkIfEmpty(inputs)

        //Si des champs sont vide on stop l'execution, on créer la div error, on ajouter le message d'erreurs
        if (check != null) {
            event.preventDefault()

            checkIfDivErrorExist = document.getElementsByClassName('alert_error')

            if (checkIfDivErrorExist.length == 0) {
                divError = document.createElement('div')
                divError.classList.add('alert_error')
                divErrorP = document.createElement('p')
                divErrorP.id = "p_error"

                formulaire.prepend(divError)

                divError.append(divErrorP)
                divErrorP.append(check)
            } else {
                //Si la div alert error existe déjà alors on la vide et on insère le nouveau message d'erreur
                divErrorP.innerHTML = ""
                divErrorP.append(check)
            }
        }
    })
}
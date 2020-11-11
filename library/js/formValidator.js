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


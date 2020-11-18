class FormValidator {
    constructor(form) {
        this.form = form
    }
    /**
     * Fonction pour récupérer l'id d'un formulaire
     * @param {*} form 
     */
    getIdForm(btn) {

        let id = btn.className.replace('btn btn_form ', '')

        return id

    }

    /**
     * Fonction pour récupérer les inputs d'un formulaire
     * @param {*} id 
     */
    getInputsForm(form) {

        let inputs = form.querySelectorAll('input')

        return inputs

    }

    /**
     * Fonction pour récupérer les textarea d'un formulaire
     * @param {*} id 
     */
    getTextAreaForm(form) {

        let textArea = form.querySelectorAll('textarea')

        return textArea

    }

    /**
     * Fonction pour récupérer le formulaire en entier selon son id
     * @param {*} id 
     */
    getForm(id) {

        let form = document.getElementById(id)
        return form

    }

    /**
     * Fonction pour vérifier si des inputs sont vides
     * @param {*} inputs 
     */
    checkIfEmpty(inputs) {

        let fields = []

        for (let i = 0; i < inputs.length; i++) {


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

    /**
     * Fonction pour créer un élément avec une class définie
     * @param string element 
     * @param string classToAdd 
     */
    newElement(element, classToAdd) {

        let newElement = document.createElement(element)
        newElement.classList.add(classToAdd)

        return newElement

    }

}
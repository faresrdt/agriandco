//API METEO AVEC AJAX
var request = new XMLHttpRequest();
request.onreadystatechange = function () {
    if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
        var response    = JSON.parse(this.responseText);
        condition       = response.current_condition.condition
        iconCondition   = response.current_condition.icon
        


        //On créer la div pour afficher la météo de la ville choisi 
        divMeteo = document.createElement('div')
        divMeteo.classList.add('div_meteo')
        
        divMeteoP = document.createElement('p')
        divMeteoP.id = "p_meteo"
        divMeteoP.classList = "center"

        titleMeteo = document.createElement('h3')
        titleMeteo.append("Météo du jour : " + condition)

        divIconMeteo = document.createElement('div')
        imgIconMeteo = document.createElement('img')
        imgIconMeteo.src = iconCondition
        divIconMeteo.append(imgIconMeteo)

        document.querySelector('aside').prepend(divMeteo)
        divMeteo.append(divMeteoP)
        divMeteoP.append(titleMeteo)
        divMeteoP.append(divIconMeteo)
    }
};
request.open("GET", "https://www.prevision-meteo.ch/services/json/cergy");
request.send();
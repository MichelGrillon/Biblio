document.addEventListener('DOMContentLoaded', function () {

    // Sélectionne l'élément de champ de la date d'emprunt dans le formulaire
    const dateEmpruntInput = document.querySelector('#date_emprunt');

    // Sélectionne l'élément de champ de la date de retour prévue dans le formulaire
    const dateRetourPrevueInput = document.querySelector('#date_retour_prevue');

    // Champ caché pour la disponibilité
    const disponibiliteInput = document.querySelector('#disponibilite');

    // Ajoute un écouteur d'événement pour détecter les changements dans le champ de la date d'emprunt
    dateEmpruntInput.addEventListener('change', function () {

        // Crée un nouvel objet Date à partir de la valeur de la date d'emprunt sélectionnée
        const dateEmprunt = new Date(this.value);

        // Crée un nouvel objet Date pour la date de retour prévue en ajoutant 21 jours à la date d'emprunt
        const dateRetourPrevue = new Date(dateEmprunt);
        dateRetourPrevue.setDate(dateEmprunt.getDate() + 21);

        // Définit les options de formatage pour obtenir le jour, le mois et l'année complets
        //const options = { year: 'numeric', month: 'long', day: 'numeric' };

        // Formate la date de retour prévue en utilisant la méthode toLocaleDateString() avec les options spécifiées
        // La date est formatée dans le format "12 mai 2024" (jour mois année)
        //const formattedDateRetourPrevue = dateRetourPrevue.toLocaleDateString('fr-FR', options);
        const formattedDateRetourPrevue = encodeURIComponent(dateRetourPrevue.toISOString().split('T')[0]);

        // Définit la valeur du champ de la date de retour prévue avec la date formatée
        dateRetourPrevueInput.value = formattedDateRetourPrevue;

        // 0 pour "Indisponible" si date_emprunt est définie
        disponibiliteInput.value = dateEmprunt ? 0 : 1;
    });
});

//document.addEventListener('DOMContentLoaded', function () {

    // Sélectionne l'élément de champ de la date d'emprunt dans le formulaire
    //const dateEmpruntInput = document.querySelector('#date_emprunt');

    // Sélectionne l'élément de champ de la date de retour prévue dans le formulaire
    //const dateRetourPrevueInput = document.querySelector('#date_retour_prevue');

    // Sélectionne l'élément de champ de la disponibilité dans le formulaire
    //const disponibiliteInput = document.querySelector('#disponibilite');

    // Sélectionne l'élément de l'icône "trash" dans la table
    //const deleteButton = document.querySelector('.fa-trash-alt');

    // Définit une fonction pour mettre à jour la disponibilité et l'icône "trash" en fonction des dates d'emprunt et de retour prévue
    //function updateDisponibiliteAndDeleteButton() {

        // Récupère les valeurs des champs de date d'emprunt et de retour prévue
        //const dateEmprunt = encodeURIComponent(dateEmpruntInput.value);
        //const dateRetourPrevue = encodeURIComponent(dateRetourPrevueInput.value);

        // Si les deux champs de date ont une valeur, alors le livre est indisponible et on masque l'icône "trash"
       // if (dateEmprunt && dateRetourPrevue) {
            //disponibiliteInput.value = 0;
            //deleteButton.style.display = 'none';

        // Sinon, le livre est disponible et on affiche l'icône "trash"
        //} else {
           // disponibiliteInput.value = 1;
            //deleteButton.style.display = '';
       // }
  //  }

    // Ajoute un écouteur d'événement pour détecter les changements dans le champ de la date d'emprunt et de retour prévue et mettre à jour la disponibilité et l'icône "trash" en conséquence
    //dateEmpruntInput.addEventListener('change', updateDisponibiliteAndDeleteButton);
   // dateRetourPrevueInput.addEventListener('change', updateDisponibiliteAndDeleteButton);

    // Appelle la fonction une fois au chargement de la page pour afficher la disponibilité et l'icône "trash" correctement en fonction des dates d'emprunt et de retour prévue déjà renseignées
    //updateDisponibiliteAndDeleteButton();
//});

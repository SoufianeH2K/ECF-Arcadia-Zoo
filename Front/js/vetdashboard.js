document.getElementById("btnAddReport").addEventListener("click", function() {
  const reportDate = document.getElementById("report-date").value;
  const vetId = document.getElementById("vet-id").value;
  const animalId = document.getElementById("animal-id").value;
  const reportDetails = document.getElementById("report-details").value;
  const animalState = document.getElementById("animal-state").value;

  // Validate the input (optional step)
  if (!reportDate || !vetId || !animalId || !reportDetails || !animalState) {
    alert("Veuillez remplir tous les champs.");
    return;
  }

  // Create the veterinarian report
  const vetReportData = {
    date: reportDate,
    detail: reportDetails,
    utilisateur_id: vetId,
    animal_id: animalId
  };

  fetch(apiUrl + 'rapportVeterinaire', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(vetReportData)
  })
  .then(response => response.json())
  .then(data => {
    console.log("Rapport vétérinaire créé avec succès:", data);

    // Update the animal state after report creation
    const animalUpdateData = {
      etat: animalState
    };

    return fetch(apiUrl + `animal/${animalId}`, {
      method: 'PUT',  
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(animalUpdateData)
    });
  })
  .then(response => response.json())
  .then(data => {
    console.log("État de l'animal mis à jour avec succès:", data);
    alert("Le rapport et l'état de l'animal ont été mis à jour avec succès.");
  })
  .catch(error => {
    console.error("Erreur lors de la création du rapport ou de la mise à jour de l'animal:", error);
    alert("Une erreur s'est produite. Veuillez réessayer.");
  });
});

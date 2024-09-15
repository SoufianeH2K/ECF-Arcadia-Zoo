function addVetReport() {
  const reportDate = document.getElementById("report-date").value;
  const vetId = document.getElementById("vet-id").value;
  const animalId = document.getElementById("animal-id").value;
  const reportDetails = document.getElementById("report-details").value;
  const animalState = document.getElementById("animal-state").value;

  // Validate input
  if (!reportDate || !vetId || !animalId || !reportDetails || !animalState) {
    alert("Veuillez remplir tous les champs.");
    return;
  }

  const vetReportData = {
    date: reportDate,
    detail: reportDetails,
    utilisateur_id: vetId,
    animal_id: animalId
  };

  // Create the vet report and update the animal state
  fetch(`${apiUrl}rapportVeterinaire`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(vetReportData)
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Erreur lors de la création du rapport.');
    }

    // If the server responds with 204 No Content, return null instead of trying to parse JSON
    return response.status === 204 ? null : response.json();
  })
  .then(data => {
    console.log("Rapport vétérinaire créé:", data);

    // Update the animal state
    return fetch(`${apiUrl}animal/${animalId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ etat: animalState })
    });
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Erreur lors de la mise à jour de l\'état de l\'animal.');
    }

    // Handle 204 No Content
    return response.status === 204 ? null : response.json();
  })
  .then(data => {
    console.log("État de l'animal mis à jour:", data);
    alert("Le rapport et l'état de l'animal ont été mis à jour avec succès.");
  })
  .catch(error => {
    console.error("Erreur:", error);
    alert("Une erreur s'est produite. Veuillez réessayer.");
  });
}

// Event listener for adding a vet report
document.getElementById("btnAddReport").addEventListener("click", addVetReport);



// Function to submit habitat comment
function submitHabitatComment(event) {
  event.preventDefault();

  const habitatId = document.getElementById('habitatId').value;
  const habitatComment = document.getElementById('habitatComment').value;

  fetch(`${apiUrl}habitat/${habitatId}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ commentaire_habitat: habitatComment })
  })
  .then(response => {
    document.getElementById('statusMessage').innerText = response.ok 
      ? "Commentaire envoyé avec succès!" 
      : "Erreur lors de l'envoi du commentaire.";
  })
  .catch(error => {
    console.error("Error:", error);
    document.getElementById('statusMessage').innerText = "Erreur lors de l'envoi du commentaire.";
  });
}

// Event listener for habitat comment submission
document.getElementById('habitatCommentForm').addEventListener('submit', submitHabitatComment);

// Function to fetch animal food history
function fetchAnimalFoodHistory(event) {
  event.preventDefault();

  const animalId = document.getElementById('animalId').value;
  const apiNUrl = `${apiUrl}nouriture?animal_id=${animalId}`;
  const tableBody = document.querySelector('#foodTable tbody');
  
  // Clear existing table rows
  tableBody.innerHTML = '';

  fetch(apiNUrl)
  .then(response => response.json())
  .then(data => {
    if (data.length > 0) {
      data.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${formatDate(item.date)}</td>
          <td>${item.type}</td>
          <td>${item.quantite}</td>
        `;
        tableBody.appendChild(row);
      });
    } else {
      document.getElementById('statusMessage').innerText = "Aucun historique de nourriture trouvé.";
    }
  })
  .catch(error => {
    console.error("Error fetching food history:", error);
    document.getElementById('statusMessage').innerText = "Erreur lors du chargement de l'historique de nourriture.";
  });
}

// Event listener for fetching animal food history
document.getElementById('animalFoodForm').addEventListener('submit', fetchAnimalFoodHistory);

// Helper function to format the date to dd/mm/yyyy hh:mm
function formatDate(dateString) {
  const date = new Date(dateString);
  
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  
  return `${day}/${month}/${year} ${hours}:${minutes}`;
}

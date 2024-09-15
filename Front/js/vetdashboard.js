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


// ------------------


document.getElementById('habitatCommentForm').addEventListener('submit', function (event) {
  event.preventDefault(); // Prevent form from submitting the traditional way

  // Collecting form data
  const habitatId = document.getElementById('habitatId').value;
  const habitatComment = document.getElementById('habitatComment').value;

  // Constructing the JSON object
  const habitatData = {
      commentaire_habitat: habitatComment
  };

  // Send the data to the backend using Fetch API with the correct endpoint
  fetch(apiUrl + `habitat/${habitatId}`, {
      method: 'PUT',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify(habitatData),
  })
  .then(response => {
      if (response.ok) {
          document.getElementById('statusMessage').innerText = "Commentaire envoyé avec succès!";
      } else {
          document.getElementById('statusMessage').innerText = "Erreur lors de l'envoi du commentaire.";
      }
  })
  .catch(error => {
      console.error("Error:", error);
      document.getElementById('statusMessage').innerText = "Erreur lors de l'envoi du commentaire.";
  });
});


// ------------



document.getElementById('animalFoodForm').addEventListener('submit', function(event) {
  event.preventDefault(); // Prevent form submission

  const animalId = document.getElementById('animalId').value;
  const apiNUrl = apiUrl + 'nouriture?animal_id=' + animalId;

  // Clear any existing table rows
  document.querySelector('#foodTable tbody').innerHTML = '';

  fetch(apiNUrl, {
      method: 'GET',
  })
  .then(response => response.json())
  .then(data => {
      if (data.length > 0) {
          const tableBody = document.querySelector('#foodTable tbody');

          // Populate the table with API data
          data.forEach(item => {
              const formattedDate = formatDate(item.date);
              const row = document.createElement('tr');
              row.innerHTML = `
                  <td>${formattedDate}</td>
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
});

// Helper function to format the date to dd/mm/yyyy hh:mm
function formatDate(dateString) {
  const date = new Date(dateString);
  
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based in JavaScript
  const year = date.getFullYear();
  
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  
  return `${day}/${month}/${year} ${hours}:${minutes}`;
}

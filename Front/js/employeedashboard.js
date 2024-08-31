const inputCommentsDisplayFrom = document.getElementById(
  "inputCommentsDisplayFrom"
);
const inputCommentsDisplayTo = document.getElementById(
  "inputCommentsDisplayTo"
);
const displayComments = document.getElementById("displayComments");
const commentsTable = document.getElementById("commentsTable");

displayComments.addEventListener("click", function () {
  const fromId = parseInt(inputCommentsDisplayFrom.value, 10);
  const toId = parseInt(inputCommentsDisplayTo.value, 10);

  if (isNaN(fromId) || isNaN(toId) || fromId > toId) {
    console.error("Invalid input values");
    return;
  }

  // Clear existing table content except the header
  const tableBody = commentsTable.querySelector("tbody");
  tableBody.innerHTML = ""; // This clears the table body

  for (let i = fromId; i <= toId; i++) {
    getComments(i);
  }
});

function getComments(i) {
  fetch(apiUrl + `avis/${i}`, {
    method: "GET",
    headers: { "Content-Type": "application/json" },
  })
    .then((res) => res.json())
    .then((data) => {
      if (
        data.id === undefined ||
        typeof data.id !== "number" ||
        document.getElementById(`comment-row-${data.id}`)
      ) {
        console.log("Comment already displayed or invalid:", data);
        return; 
      }

      const dataVisibility = data.isVisible ? "Oui" : "Non";
      let template = `
                    <tr id="comment-row-${data.id}">
                        <th scope="row">${data.id}</th>
                          <td>${data.pseudo}</td>
                          <td>${dataVisibility}</td>
                          <td>
                            <select name="isVisible" id="isVisible-${data.id}"
                                onchange="updateVisibility(${data.id}, this)">
                                    <option value="true" ${
                                      data.isVisible ? "selected" : ""
                                    }>Visible</option>
                                    <option value="false" ${
                                      !data.isVisible ? "selected" : ""
                                    }>Non visible</option>
                            </select>
                          </td>
                          <td>${data.commentaire}</td>
                    </tr>`;
      commentsTable.insertAdjacentHTML("beforeend", template);
    })
    .catch((error) => console.error("Error fetching data:", error));
}

// Update visibility

function updateVisibility(commentId, selectElement) {
  // Fetch other details from the row for completeness
  const row = document.getElementById(`comment-row-${commentId}`);
  const pseudo = row.cells[1].textContent; // Assuming 'pseudo' is in the second cell
  const commentaire = row.cells[4].textContent; // Assuming 'commentaire' is in the fourth cell
  const isVisible = selectElement.value === "true";

  fetch(apiUrl + `avis/${commentId}`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      id: commentId.toString(),
      pseudo: pseudo,
      commentaire: commentaire,
      isVisible: isVisible,
    }),
  })
    .then((response) => {
      console.log("Raw response:", response);
      if (!response.ok) {
        throw new Error(`HTTP error, status = ${response.status}`);
      }

      if (
        response.status === 204 ||
        (response.status >= 200 && response.status < 300)
      ) {
        alert(
          "Mise à jour réussie. Veuillez actualiser la page pour voir les changements"
        );
        return response.text();
      }
      return response.json();
    })
    .then((data) => {
      if (data) {
        console.log("Successfully updated:", data);
      } else {
        console.log("No content returned, but update was successful.");
      }
    })
    .catch((error) => {
      console.error("Failed to update:", error);
      alert("Échec de la mise à jour des données");
    });
}


// Adding animal food to the database

function submitFeedingData() {
  const apiUrlFeeding = apiUrl + "nouriture"; // Adjust your API URL accordingly
  const form = document.getElementById("feedingForm");
  const data = {
    animal_id: form.animal_id.value,
    type: form.type.value,
    quantite: parseFloat(form.quantite.value),
    date: form.date.value,
    time: form.time.value, // Capture the time input
  };

  fetch(apiUrlFeeding, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      console.log("Success:", data);
      alert("Informations sur la nourriture envoyées.");
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Échec de l'envoi des informations sur la nourriture.");
    });
}


document.addEventListener('DOMContentLoaded', function() {
  const serviceTable = document.getElementById('serviceTable').getElementsByTagName('tbody')[0];
  const saveServiceButton = document.getElementById('saveService');
  const serviceModal = new bootstrap.Modal(document.getElementById('serviceModal'));

  // Function to fetch and display services
  async function fetchServices() {
      const response = await fetch('/api/services');
      const services = await response.json();

      serviceTable.innerHTML = ''; // Clear existing rows

      services.forEach(service => {
          const row = serviceTable.insertRow();
          row.innerHTML = `
              <td>${service.id}</td>
              <td>${service.nom}</td>
              <td>${service.description}</td>
              <td><img src="/path/to/image/${service.id}" alt="Service Image" class="img-thumbnail" style="width: 100px;"></td>
              <td>
                  <button class="btn btn-warning btn-sm editService" data-id="${service.id}">Edit</button>
                  <button class="btn btn-danger btn-sm deleteService" data-id="${service.id}">Delete</button>
              </td>
          `;
      });

      attachEventListeners();
  }

  // Event listener for Save Service button
  saveServiceButton.addEventListener('click', async function() {
      const id = document.getElementById('serviceId').value;
      const nom = document.getElementById('nom').value;
      const description = document.getElementById('description').value;
      const service_image = document.getElementById('service_image').files[0];

      const formData = new FormData();
      formData.append('nom', nom);
      formData.append('description', description);
      formData.append('service_image', service_image);

      let url = '/api/services';
      let method = 'POST';

      if (id) {
          url += `/${id}`;
          method = 'PUT';
      }

      await fetch(url, {
          method: method,
          body: formData,
      });

      serviceModal.hide();
      fetchServices();
  });

  // Event listeners for Edit and Delete buttons
  function attachEventListeners() {
      document.querySelectorAll('.editService').forEach(button => {
          button.addEventListener('click', async function() {
              const id = this.getAttribute('data-id');
              const response = await fetch(`/api/services/${id}`);
              const service = await response.json();

              document.getElementById('serviceId').value = service.id;
              document.getElementById('nom').value = service.nom;
              document.getElementById('description').value = service.description;
              // Note: service_image cannot be pre-populated for security reasons.

              document.getElementById('serviceModalLabel').textContent = 'Edit Service';
              serviceModal.show();
          });
      });

      document.querySelectorAll('.deleteService').forEach(button => {
          button.addEventListener('click', async function() {
              const id = this.getAttribute('data-id');

              await fetch(`/api/services/${id}`, {
                  method: 'DELETE',
              });

              fetchServices();
          });
      });
  }

  // Initial fetch of services
  fetchServices();
});

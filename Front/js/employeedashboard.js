const inputCommentsDisplayFrom = document.getElementById(
  "inputCommentsDisplayFrom"
);
const inputCommentsDisplayTo = document.getElementById(
  "inputCommentsDisplayTo"
);
const displayComments = document.getElementById("displayComments");
const commentsTable = document.getElementById("commentsTable");

//
// Ensure elements are available
const serviceTable = document.getElementById('serviceTable').getElementsByTagName('tbody')[0];
const addServiceButton = document.getElementById('addService');
const saveServiceButton = document.getElementById('saveService');
const serviceModal = new bootstrap.Modal(document.getElementById('serviceModal'));
const fetchServicesRangeButton = document.getElementById('fetchServicesRange');

let currentServiceId = null;  // Variable to track the current service being edited

// Function to fetch and display a specific service by ID
function fetchServiceById(serviceId) {
  const url = `https://127.0.0.1:8000/api/service/${serviceId}`;

  fetch(url)
      .then(response => {
          console.log('Response headers:', response.headers.get('content-type'));
          if (!response.ok) {
              return response.text().then(text => {
                  throw new Error(`Failed to fetch service with ID ${serviceId}: ${text}`);
              });
          }

          const contentType = response.headers.get("content-type");
          if (contentType && contentType.includes("application/json")) {
              return response.json();
          } else {
              return response.text().then(text => {
                  throw new Error(`Expected JSON, but received HTML. Response: ${text}`);
              });
          }
      })
      .then(service => {
          console.log('Service data:', service);
          const row = serviceTable.insertRow();
          row.innerHTML = `
              <td>${service.id}</td>
              <td>${service.nom}</td>
              <td>${service.description}</td>
              <td><img src="data:image/jpeg;base64,${service.service_image}" alt="Service Image" class="img-thumbnail" style="width: 100px;"></td>
              <td>
                  <button class="btn btn-warning btn-sm editService" data-id="${service.id}">Edit</button>
                  <button class="btn btn-danger btn-sm deleteService" data-id="${service.id}">Delete</button>
              </td>
          `;

          attachEventListeners();
      })
      .catch(error => {
          console.error(`Error fetching service with ID ${serviceId}:`, error);
      });
}

// Function to fetch services in a range of IDs
function fetchServicesInRange(fromId, toId) {
    serviceTable.innerHTML = ''; // Clear existing rows

    for (let id = fromId; id <= toId; id++) {
        fetchServiceById(id);
    }
}

// Event listener for fetching services within a range
fetchServicesRangeButton.addEventListener('click', function() {
    const fromId = parseInt(document.getElementById('serviceIdFrom').value, 10);
    const toId = parseInt(document.getElementById('serviceIdTo').value, 10);

    if (isNaN(fromId) || isNaN(toId) || fromId > toId) {
        console.error('Invalid input values');
        return;
    }

    fetchServicesInRange(fromId, toId);
});

// Event listener for Add Service button
addServiceButton.addEventListener('click', function() {
    // Clear the form fields for new entry
    currentServiceId = null;  // Reset currentServiceId for adding a new service
    document.getElementById('serviceId').value = '';
    document.getElementById('nom').value = '';
    document.getElementById('description').value = '';
    document.getElementById('service_image').value = '';
    document.getElementById('serviceModalLabel').textContent = 'Add Service';

    // Show the modal for adding a new service
    serviceModal.show();
});

// Event listener for Save Service button
saveServiceButton.addEventListener('click', async function() {
  const nom = document.getElementById('nom').value;
  const description = document.getElementById('description').value;
  const service_image = document.getElementById('service_image').files[0];

  const formData = new FormData();
  formData.append('nom', nom);
  formData.append('description', description);
  if (service_image) {
      formData.append('service_image', service_image);
  }

  let url = `https://127.0.0.1:8000/api/service`;  // Base URL for the API route
  let method = 'POST';

  if (currentServiceId) {  // If editing, use PUT method
      url += `/${currentServiceId}`;
      method = 'PUT';
  }

  try {
      const response = await fetch(url, {
          method: method,
          body: formData,
      });

      if (!response.ok) {
          throw new Error(`Failed to save service: ${response.statusText}`);
      }

      console.log('Service saved successfully');

      serviceModal.hide();
      fetchServicesInRange(fromId, toId); // Refresh the list of services after saving
  } catch (error) {
      console.error('Error saving service:', error);
      alert('Failed to save the service. Please try again.');
  }
});



// Event listeners for Edit and Delete buttons
function attachEventListeners() {
    document.querySelectorAll('.editService').forEach(button => {
        button.addEventListener('click', async function() {
            const id = this.getAttribute('data-id');
            currentServiceId = id;  // Set currentServiceId to the service being edited
            const response = await fetch(`https://127.0.0.1:8000/api/service/${id}`);  // Fetch the existing service data
            const service = await response.json();

            // Populate the modal with existing data
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

            try {
                const response = await fetch(`https://127.0.0.1:8000/api/service/${id}`, {  // Send DELETE request
                    method: 'DELETE',
                });

                if (!response.ok) {
                    throw new Error(`Failed to delete service with ID ${id}`);
                }

                console.log(`Service with ID ${id} deleted successfully`);
                const fromId = parseInt(document.getElementById('serviceIdFrom').value, 10);
                const toId = parseInt(document.getElementById('serviceIdTo').value, 10);
                fetchServicesInRange(fromId, toId); // Refresh the list of services after deletion
            } catch (error) {
                console.error(`Error deleting service with ID ${id}:`, error);
                alert('Failed to delete the service. Please try again.');
            }
        });
    });
}

// Initial fetch of services (optional)
// fetchServicesInRange(1, 5); // Example to fetch services in range of IDs 1 to 5


//

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
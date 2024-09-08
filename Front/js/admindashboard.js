const inputNom = document.getElementById("NomInput");
const inputPrenom = document.getElementById("PrenomInput");
const inputUsername = document.getElementById("UsernameInput");
const inputPassword = document.getElementById("PasswordInput");
const inputValidationPassword = document.getElementById("ValidatePasswordInput");
const inputAccountType = document.getElementById("AccountTypeInput");
const btnUserCreationForm = document.getElementById("btnCreationForm");
const formUserCreation = document.getElementById("account-creation-form");

btnUserCreationForm.addEventListener("click", inscrireUtilisateur);

[
  inputNom,
  inputPrenom,
  inputUsername,
  inputPassword,
  inputValidationPassword,
  inputAccountType,
].forEach((input) => {
  input.addEventListener("keyup", validateForm);
  input.addEventListener("change", validateForm);
});

function validateForm() {
  const nomOk = validateRequired(inputNom) && validateMaxLength(inputNom, 50);
  const prenomOk = validateRequired(inputPrenom) && validateMaxLength(inputPrenom, 50);
  const usernameOk = validateRequired(inputUsername) && validateMaxLength(inputUsername, 180);
  const passwordOk = validatePassword(inputPassword) && validateMaxLength(inputPassword, 50);
  const passwordConfirmOk = validatePasswordConfirmation(inputPassword, inputValidationPassword);
  const accountTypeOk = validateRequired(inputAccountType);

  if (nomOk && prenomOk && usernameOk && passwordOk && passwordConfirmOk && accountTypeOk) {
    btnUserCreationForm.disabled = false;
  } else {
    btnUserCreationForm.disabled = true;
  }
}

function validateRequired(input) {
  if (input.value.trim() !== "") {
    input.classList.add("is-valid");
    input.classList.remove("is-invalid");
    return true;
  } else {
    input.classList.remove("is-valid");
    input.classList.add("is-invalid");
    return false;
  }
}

function validateMaxLength(input, maxLength) {
  if (input.value.length <= maxLength) {
    input.classList.add("is-valid");
    input.classList.remove("is-invalid");
    return true;
  } else {
    input.classList.remove("is-valid");
    input.classList.add("is-invalid");
    return false;
  }
}

function validatePassword(input) {
  const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/;
  const passwordUser = input.value;
  if (passwordUser.match(passwordRegex)) {
    input.classList.add("is-valid");
    input.classList.remove("is-invalid");
    return true;
  } else {
    input.classList.remove("is-valid");
    input.classList.add("is-invalid");
    return false;
  }
}

function validatePasswordConfirmation(passwordInput, confirmPasswordInput) {
  if (passwordInput.value === confirmPasswordInput.value && confirmPasswordInput.value.trim() !== "") {
    confirmPasswordInput.classList.add("is-valid");
    confirmPasswordInput.classList.remove("is-invalid");
    return true;
  } else {
    confirmPasswordInput.classList.remove("is-valid");
    confirmPasswordInput.classList.add("is-invalid");
    return false;
  }
}

function inscrireUtilisateur() {
  const dataForm = {
    email: inputUsername.value,
    password: inputPassword.value,
    nom: inputNom.value,
    prenom: inputPrenom.value,
    accountType: inputAccountType.value,  // Add the account type to the data being sent
  };

  const requestOptions = {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(dataForm),
    redirect: "follow",
  };

  fetch(apiUrl + "registration", requestOptions)
    .then((response) => {
      if (response.ok) {
        return response.json();
      } else {
        return response.json().then((err) => {
          alert("Erreur lors de l'inscription: " + (err.error || "Unknown error"));
          throw new Error("Erreur lors de l'inscription");
        });
      }
    })
    .then((result) => {
      alert(
        "Utilisateur créé, son nom d'utilisateur: " +
          inputUsername.value +
          ", et son mot de passe :" +
          inputPassword.value +
          ", type de compte: " + inputAccountType.value
      );
    })
    .catch((error) => console.error("Erreur:", error));
}

validateForm();



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




//    --------------




let allReports = [];  // Store fetched reports here

// Fetch reports within the given range of IDs
function fetchReportsInRange() {
    const startId = document.getElementById("start-id").value;
    const endId = document.getElementById("end-id").value;

    allReports = [];  // Reset the reports array
    const promises = [];

    // Loop through the range of IDs and fetch each report
    for (let id = startId; id <= endId; id++) {
        promises.push(
            fetch(apiUrl + `rapportVeterinaire/${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Report ID ${id} not found.`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data) allReports.push(data);  // Only add valid reports
                })
                .catch(error => console.error(`Erreur lors de la récupération du rapport ID ${id}:`, error))
        );
    }

    // Once all reports are fetched, display them in the table
    Promise.all(promises).then(() => {
        displayReports(allReports);
    });
}

// Display reports in the table
function displayReports(reports) {
    const tableBody = document.getElementById("reportsTableBody");
    tableBody.innerHTML = ""; // Clear existing rows

    if (reports.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center">Aucun rapport trouvé</td></tr>`;
    } else {
        reports.forEach(report => {
            const reportDate = new Date(report.date);
            const formattedDate = reportDate.toLocaleDateString('fr-FR') + " - " + reportDate.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

            // Access nested 'utilisateur.id' and 'animal.id'
            const animalId = report.animal.id || 'N/A';  // Access animal ID from nested 'animal' object
            const vetId = report.utilisateur.id || 'N/A';  // Access vet ID from nested 'utilisateur' object

            const row = `<tr>
                <td>${report.id}</td>
                <td>${formattedDate}</td>
                <td>${animalId}</td>
                <td>${vetId}</td>
                <td>${report.detail || 'N/A'}</td>
            </tr>`;
            tableBody.innerHTML += row;
        });
    }
}

// Filter displayed reports based on animal ID or date
function filterReports() {
    const animalId = document.getElementById("filter-animal-id").value;
    const date = document.getElementById("filter-date").value;

    let filteredReports = allReports;

    // Apply animal ID filter
    if (animalId) {
        filteredReports = filteredReports.filter(report => report.animal.id == animalId);  // Access animal.id for comparison
    }

    // Apply date filter (format the input date to match the displayed date format)
    if (date) {
        filteredReports = filteredReports.filter(report => {
            const reportDate = new Date(report.date);
            const formattedDate = reportDate.toISOString().split('T')[0]; // Format date to YYYY-MM-DD
            return formattedDate === date;
        });
    }

    // Display the filtered reports
    displayReports(filteredReports);
}

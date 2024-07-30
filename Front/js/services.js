/*
const servicesContainer = document.getElementById("servicesContainer");
let myService = getService();

servicesContainer.innerHTML = myService;

// Function to fetch service data by ID
function fetchServiceById(serviceId) {
  fetch(apiUrl + `service/${serviceId}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((service) => {
      const serviceId = service.id;
      const serviceName = service.nom;
      const serviceDescription = service.description;
      console.log(service.service_image);
    })
    .catch((error) => {
      console.error("There was a problem with the fetch operation:", error);
    });
}

// Example usage
fetchServiceById(9); // Replace 123 with the actual service ID you want to fetch

function getService() {
  return `
  <div class="service">
  <img
    src="../img/zoo restaurant 2.jpg"
    alt="Service de restauration"
    class="service-image"
  />
  <div class="service-details">
    <h3 class="service-title">Restauration</h3>
    <p class="service-description">
      Profitez également de notre service de restauration au Zoo d'Arcadia, où
      vous pourrez savourer une délicieuse pause gastronomique au cœur de la
      nature. Nos options de restauration sont soigneusement sélectionnées
      pour satisfaire tous les goûts, allant des collations légères aux repas
      complets. Que vous souhaitiez vous détendre avec une boisson
      rafraîchissante ou déguster un délicieux repas en famille, notre équipe
      est là pour vous offrir une expérience culinaire inoubliable lors de
      votre visite
    </p>
  </div>
  <div class="edit-delete-buttons" data-show="admin">
    <button
      class="btn btn-primary btn-outline-light edit-service-btn"
      data-bs-toggle="modal"
      data-bs-target="#exampleModal"
    >
      <i class="bi bi-pencil-square"></i>
    </button>
    <button class="btn btn-danger delete-service-btn" data-bs-toggle="modal"
    data-bs-target="#deleteModal">
      <i class="bi bi-trash"></i>
    </button>
  </div>
</div>
    `;
}
*/
// Add a service

const addServiceForm = document.getElementById("service-form");

function postService() {
  const addServiceDataForm = new FormData(addServiceForm);

  fetch(apiUrl + `avis`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      nom: addServiceDataForm.get("service-name"),
      description: addServiceDataForm.get("service-description"),
      service_image: addServiceDataForm.get("service-image"),
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      console.log(data);
      alert("Le service à bien été envoyé");
    })
    .catch((error) => console.error("Error posting data:", error));
}

const btnAddService = document.getElementById("addServiceButton");
btnAddService.addEventListener("click", postService);

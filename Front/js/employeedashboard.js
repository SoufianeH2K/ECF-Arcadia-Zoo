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
        return; // Skip if already displayed or invalid
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

// Update of services
const servicesContainer = document.getElementById("servicesContainer");
/*
function getServices(i) {
  fetch(apiUrl + `service/${i}`, {
    method: "GET",
    headers: { "Content-Type": "application/json" },
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.id === undefined || typeof data.id !== "number") {
        console.log("Service already displayed or invalid:", data);
        return; // Skip if already displayed or invalid
      }
*/
let template = `
                  <div class="row row-cols-2 mt-2">
                    <div class="col">
                      <h1>*** title ***</h1>
                      <p>*** description ***</p>
                    </div>
                    <div class="col">
                      <img
                        src="https://images.unsplash.com/photo-1715787803917-d25f112866fe?q=80&w=2487&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                        alt="***"
                        class="img-fluid rounded"
                      />
                    </div>
                  </div>
                     `;
servicesContainer.insertAdjacentHTML("beforeend", template);
/*   })
    .catch((error) => console.error("Error fetching data:", error));
}*/

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

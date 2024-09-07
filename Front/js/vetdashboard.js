const vetReportForm = document.getElementById("vetReportForm");
const btnAddReport = document.getElementById("btnAddReport");

function postReport() {
  const date = document.getElementById("report-date").value;
  const detail = document.getElementById("report-details").value;
  const animalId = document.getElementById("animal-id").value;
  const vetId = document.getElementById("vet-id").value;

  fetch(apiUrl + `rapportVeterinaire`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      date: date,
      detail: detail,
      animal_id: animalId,
      utilisateur_id: vetId,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      console.log(data);
      alert("Votre rapport a bien été envoyé");
    })
    .catch((error) => console.error("Erreur d'envoie de commentaire:", error));
}
btnAddReport.addEventListener("click", postReport);
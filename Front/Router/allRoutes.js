import Route from "./Route.js";

//Définir ici vos routes
export const allRoutes = [
  new Route("/", "Accueil", "/pages/home.html", [], "/js/home.js"),
  new Route(
    "/services",
    "Nos services",
    "/pages/services.html",
    [],
    "/js/services.js"
  ),
  new Route("/habitats", "Nos habitats", "/pages/habitats.html", []),
  new Route(
    "/login",
    "Connexion",
    "/pages/login.html",
    ["disconnected"],
    "/js/login.js"
  ),
  new Route("/contact", "Contactez nous", "/pages/contact.html", []),
  new Route("/reservation", "Réservation", "/pages/reservation.html"),
  new Route(
    "/admindash",
    "Espace admin",
    "/pages/admindashboard.html",
    ["admin"],
    "/js/admindashboard.js"
  ),
  new Route(
    "/employeedash",
    "Espace employé",
    "/pages/employeedashboard.html",
    ["employee"],
    "/js/employeedashboard.js"
  ),
  new Route("/vetdash", "Espace veterinaire", "/pages/vetdashboard.html", [
    "vet",
  ]),
];

//Le titre s'affiche comme ceci : Route.titre - websitename
export const websiteName = "Arcadia";

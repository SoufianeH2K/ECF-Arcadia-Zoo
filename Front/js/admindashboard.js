const inputNom = document.getElementById("NomInput");
const inputPrenom = document.getElementById("PrenomInput");
const inputUsername = document.getElementById("UsernameInput");
const inputPassword = document.getElementById("PasswordInput");
const inputValidationPassword = document.getElementById(
  "ValidatePasswordInput"
);
const btnUserCreationForm = document.getElementById("btnCreationForm");
const formUserCreation = document.getElementById("account-creation-form");

btnUserCreationForm.addEventListener("click", incscrireUtilisateur);

[
  inputNom,
  inputPrenom,
  inputUsername,
  inputPassword,
  inputValidationPassword,
].forEach((input) => {
  input.addEventListener("keyup", validateForm);
  input.addEventListener("change", validateForm);
});

function validateForm() {
  const nomOk = validateRequired(inputNom) && validateMaxLength(inputNom, 50);
  const prenomOk =
    validateRequired(inputPrenom) && validateMaxLength(inputPrenom, 50);
  const usernameOk =
    validateRequired(inputUsername) && validateMaxLength(inputUsername, 50);
  const passwordOk =
    validatePassword(inputPassword) && validateMaxLength(inputPassword, 50);
  const passwordConfirmOk = validatePasswordConfirmation(
    inputPassword,
    inputValidationPassword
  );

  if (nomOk && prenomOk && usernameOk && passwordOk && passwordConfirmOk) {
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
  const passwordRegex =
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/;
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
  if (
    passwordInput.value === confirmPasswordInput.value &&
    confirmPasswordInput.value.trim() !== ""
  ) {
    confirmPasswordInput.classList.add("is-valid");
    confirmPasswordInput.classList.remove("is-invalid");
    return true;
  } else {
    confirmPasswordInput.classList.remove("is-valid");
    confirmPasswordInput.classList.add("is-invalid");
    return false;
  }
}

validateForm();

function incscrireUtilisateur() {
  let dataForm = new FormData(formUserCreation);

  const myHeaders = new Headers();
  myHeaders.append("Content-Type", "application/json");

  const raw = JSON.stringify({
    username: dataForm.get("username"),
    password: dataForm.get("password"),
    nom: dataForm.get("nom"),
    prenom: dataForm.get("prenom"),
  });

  const requestOptions = {
    method: "POST",
    headers: myHeaders,
    body: raw,
    redirect: "follow",
  };

  fetch(apiUrl + "registration", requestOptions)
    .then((response) => {
      if (response.ok) {
        return response.json();
      } else {
        alert("Erreur lors de l'inscription");
      }
    })

    .then((result) => {
      alert(
        "Utilisateur créé, son nom d'utilisateur: " +
          dataForm.get("username") +
          ", et son mot de passe :" +
          dataForm.get("password")
      );
    })
    .catch((error) => console.error(error));
}

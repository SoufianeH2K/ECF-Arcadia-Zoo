const inputUsername = document.getElementById("inputUsername");
const inputPassword = document.getElementById("inputPassword");
const btnLogin = document.getElementById("btnLogin");
const loginForm = document.getElementById("loginForm");

btnLogin.addEventListener("click", checkCredentials);

function checkCredentials() {
  let dataForm = new FormData(loginForm);

  const myHeaders = new Headers();
  myHeaders.append("Content-Type", "application/json");

  const raw = JSON.stringify({
    username: dataForm.get("username"),
    password: dataForm.get("password"),
  });

  const requestOptions = {
    method: "POST",
    headers: myHeaders,
    body: raw,
    redirect: "follow",
  };

  fetch(apiUrl + "login", requestOptions)
    .then((response) => {
      if (response.ok) {
        return response.json();
      } else {
        inputUsername.classList.add("is-invalid");
        inputPassword.classList.add("is-invalid");
      }
    })

    .then((result) => {
      // Recuperer le token
      const token = result.apiToken;
      setToken(token);
      // placer le token dans les cookies

      setCookie(roleCookieName, result.roles[0], 7);
      window.location.replace("/");
    })
    .catch((error) => console.error(error));
}

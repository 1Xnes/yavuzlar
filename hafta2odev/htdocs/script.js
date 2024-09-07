const homePageButton = document.getElementById("homePageButton");
const logoutButton = document.getElementById("logoutButton");
const profileButton = document.getElementById("profileButton");


function goToHomePage() {
  window.location.href = "index.php";
}

function loginFunc() {
  window.location.href = "index.php";
}

function logoutFunc() {
  window.location.href = "logout.php";
}

function goToProfile() {
  window.location.href = "profile.php";
}



logoutButton.addEventListener("click", logoutFunc);
profileButton.addEventListener("click", goToProfile);
homePageButton.addEventListener("click", goToHomePage);


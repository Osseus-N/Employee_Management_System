//ONLY FOR TEST(Hindi mismong code)
alert("AUTH.JS LOADED");

document.addEventListener("DOMContentLoaded", function () {
    alert("DOM READY");
    const loginForm = document.getElementById("loginForm");
    console.log(loginForm);
    if (!loginForm) {
        alert("FORM NOT FOUND");
        return;
    }
    alert("FORM FOUND");
    loginForm.addEventListener("submit", function (e) {
        e.preventDefault();
        alert("SUBMIT WORKS!");
    });
});

//TULOY KO BUKAS(ERROR)
async function login(event) {

    event.preventDefault();
    alert("STEP 1");
    const email = document.getElementById("emailInput").value;
    const password = document.getElementById("passwordInput").value;
    alert("STEP 2");
    try {
        const response = await fetch("../server/login/loginController.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                email: email,
                password: password
            })
        });
        alert("STEP 3");
        const text = await response.text();
        alert("STEP 4");
        console.log(text);
    } catch (e) {
        alert("FETCH ERROR");
        console.log(e);
    }
}
//FOR LOGOUT
async function logout() {
    try {
        await fetch(loginAPI, {
            method: "DELETE"
        });
    } catch (error) {
        console.log(error);
    }
    window.location.href = "../../login/login.html";
}
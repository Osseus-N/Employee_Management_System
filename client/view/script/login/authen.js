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

    // Pass the event directly to the standalone function
    loginForm.addEventListener("submit", login);
});
//TULOY KO BUKAS(ERROR)
async function login(event) {

    event.preventDefault();

    const email = document.getElementById("emailInput").value;
    const password = document.getElementById("passwordInput").value;

    try {
        const response = await fetch("../server/login/login.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                email: email,
                password: password
            })
        });

        const text = await response.text();
        console.log(text);
    } catch (e) {
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
    window.location.href = "../../../../login/login.html";
}

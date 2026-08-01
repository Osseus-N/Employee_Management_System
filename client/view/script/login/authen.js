//ONLY FOR TEST(Hindi mismong code)
document.addEventListener("DOMContentLoaded", function () {
        checkUserRole();
    });

    function checkUserRole() {

        const userRole =
            sessionStorage.getItem("role") ||
            localStorage.getItem("role");

        const adminBtn =
            document.getElementById("adminReturnBtn");

        if (
            userRole &&
            userRole.toLowerCase() === "admin"
        ) {

            adminBtn.classList.remove("d-none");

        }

    }
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

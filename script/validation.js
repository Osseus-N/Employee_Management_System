const form = document.getElementById("loginForm");

form.addEventListener("submit", async function(e){

    e.preventDefault();

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const response = await fetch("backend/login.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify({

            username:username,
            password:password

        })

    });

    const data = await response.json();

    if(data.success){

        if(data.role === "admin"){

            window.location.href = "../admin/admin_view.html";

        }
        else if(data.role === "employee"){

            window.location.href = "../employee/employee_view.html";

        }

    }else{

        alert(data.message);

    }

});
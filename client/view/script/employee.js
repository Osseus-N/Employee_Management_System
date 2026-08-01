const employeeAPI = "../../../server/router/admin.php";

let employees = [];
let selectedEmployee = null;

document.addEventListener("DOMContentLoaded", () => {

    bindEmployeeEvents();

    loadEmployees();

});

function bindEmployeeEvents(){

    btnAddEmployee.onclick = openEmployeeModal;

    btnCloseModal.onclick = closeEmployeeModal;

    btnCancel.onclick = closeEmployeeModal;

    employeeForm.addEventListener(
        "submit",
        saveEmployee
    );

    searchEmployee.addEventListener(
        "input",
        searchEmployees
    );

    statusFilter.addEventListener(
        "change",
        filterEmployees
    );

}

async function loadEmployees(){

    try{

        const response = await fetch(employeeAPI);

        const result = await response.json();

        employees = result.data || [];

        renderEmployees(employees);

    }

    catch(error){

        console.error(error);

    }

}

async function searchEmployees(){

    const keyword = searchEmployee.value.trim();

    if(keyword===""){

        renderEmployees(employees);

        return;

    }

    const response = await fetch(

        employeeAPI + "?search=" + encodeURIComponent(keyword)

    );

    const result = await response.json();

    renderEmployees(result.data || []);

}

function filterEmployees(){

    const status = statusFilter.value;

    if(status===""){

        renderEmployees(employees);

        return;

    }

    renderEmployees(

        employees.filter(emp=>emp.emp_status===status)

    );

}

async function saveEmployee(event){

    event.preventDefault();

    if(selectedEmployee){

        updateEmployee();

    }

    else{

        addEmployee();

    }

}

async function addEmployee(){

    const response = await fetch(employeeAPI,{

        method:"POST",

        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify(getEmployeeData())

    });

    const result = await response.json();

    alert(result.message);

    closeEmployeeModal();

    loadEmployees();

}

async function updateEmployee(){

    const response = await fetch(employeeAPI,{

        method:"PUT",

        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify({

            emp_id:selectedEmployee.emp_id,

            ...getEmployeeData()

        })

    });

    const result = await response.json();

    alert(result.message);

    closeEmployeeModal();

    loadEmployees();

}

async function deleteEmployee(id){

    if(!confirm("Delete employee?")){

        return;

    }

    const response = await fetch(employeeAPI,{

        method:"DELETE",

        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify({

            emp_id:id

        })

    });

    const result = await response.json();

    alert(result.message);

    loadEmployees();

}

document.addEventListener("click",e=>{

    const edit=e.target.closest(".btn-edit");

    if(edit){

        selectEmployee(edit.dataset.id);

    }

    const del=e.target.closest(".btn-delete");

    if(del){

        deleteEmployee(del.dataset.id);

    }

});
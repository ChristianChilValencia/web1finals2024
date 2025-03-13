function fetchData(type, tableId, rowTemplate) {
    axios.get(`fetch.php?type=${type}`)
        .then(response => {
            const data = response.data;
            const table = document.getElementById(tableId);
            if (table) {
                table.innerHTML = '';
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = rowTemplate(item);
                    table.appendChild(row);
                });
            }
        })
        .catch(error => console.error(`Error fetching ${type}:`, error));
}

function fetchStudents() {
    fetchData('students', 'studentsTable', student => `
        <td>${student.studid}</td>
        <td>${student.studfirstname}</td>
        <td>${student.studlastname}</td>
        <td>${student.studmidname || ''}</td>
        <td>${student.progfullname}</td>
        <td>${student.collshortname}</td>
        <td>${student.studyear}</td>
        <td class="action-buttons">
            <button onclick="window.location.href='update_students.php?id=${student.studid}'" class="btn-icon no-color"><img src="assets/update-icon.png" alt="Update"></button>
            <button onclick="deleteItem(${student.studid}, 'students')" class="btn-icon no-color"><img src="assets/delete-icon.png" alt="Delete"></button>
        </td>
    `);
}

function fetchPrograms() {
    fetchData('programs', 'programsTable', program => `
        <td>${program.progid}</td>
        <td>${program.progfullname}</td>
        <td>${program.progshortname || ''}</td>
        <td>${program.collshortname}</td>
        <td>${program.deptfullname}</td>
        <td class="action-buttons">
            <button onclick="window.location.href='update_programs.php?id=${program.progid}'" class="btn-icon no-color"><img src="assets/update-icon.png" alt="Update"></button>
            <button onclick="deleteItem(${program.progid}, 'programs')" class="btn-icon no-color"><img src="assets/delete-icon.png" alt="Delete"></button>
        </td>
    `);
}

function fetchDepartments() {
    fetchData('departments', 'departmentsTable', department => `
        <td>${department.deptid}</td>
        <td>${department.deptfullname}</td>
        <td>${department.deptshortname || ''}</td>
        <td>${department.collshortname}</td>
        <td class="action-buttons">
            <button onclick="window.location.href='update_departments.php?id=${department.deptid}'" class="btn-icon no-color"><img src="assets/update-icon.png" alt="Update"></button>
            <button onclick="deleteItem(${department.deptid}, 'departments')" class="btn-icon no-color"><img src="assets/delete-icon.png" alt="Delete"></button>
        </td>
    `);
}

function fetchColleges() {
    fetchData('colleges', 'collegesTable', college => `
        <td>${college.collid}</td>
        <td>${college.collfullname}</td>
        <td>${college.collshortname || ''}</td>
        <td class="action-buttons">
            <button onclick="window.location.href='update_colleges.php?id=${college.collid}'" class="btn-icon no-color"><img src="assets/update-icon.png" alt="Update"></button>
            <button onclick="deleteItem(${college.collid}, 'colleges')" class="btn-icon no-color"><img src="assets/delete-icon.png" alt="Delete"></button>
        </td>
    `);
}

function createItem(event, type) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    axios.post(`fetch.php?type=${type}`, formData)
        .then(() => {
            window.location.href = `read_${type}.php`;
        })
        .catch(error => {
            console.error(`Error creating ${type}`, error);
        });
}

function deleteItem(id, type) {
    showDeleteModal(id, type);
}

function showDeleteModal(id, type) {
    const modal = document.getElementById('deleteModal');
    const confirmButton = document.getElementById('confirmDeleteButton');
    const cancelButton = document.getElementById('cancelDeleteButton');

    modal.style.display = 'flex';

    confirmButton.onclick = function() {
        axios.delete(`fetch.php?type=${type}&id=${id}`)
            .then(() => {
                window.location.href = `read_${type}.php`;
            })
            .catch(error => {
                console.error(`Error deleting ${type}`, error);
            });
        modal.style.display = 'none';
    };

    cancelButton.onclick = function() {
        modal.style.display = 'none';
    };
}

function handleFormSubmission(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    axios.post(form.action, formData)
        .then(() => {
            window.location.href = form.action;
        })
        .catch(error => {
            console.error('Error submitting form', error);
        });
}

document.addEventListener('DOMContentLoaded', () => {
    fetchStudents();
    fetchPrograms();
    fetchDepartments();
    fetchColleges();

    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', handleFormSubmission);
    });

    const createForm = document.getElementById('createForm');
    if (createForm) {
        createForm.addEventListener('submit', event => createItem(event, createForm.getAttribute('data-type')));
    }
});

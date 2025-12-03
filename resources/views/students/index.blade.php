<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel </title>
   
  
  <style>
        table { 
            border-collapse: collapse; 
            width: 100%; 
            margin-top: 20px;
        }
        th, td { 
            padding: 8px; 
            text-align: left; 
            border: 1px solid #ddd; 
        }
        .pagination { 
            margin-top: 20px; 
            margin: 10px 10px 8px 8px;
            text-align: center; 
        }
        input, button {
            padding: 5px;
            margin: 2px;
        }
        button {
            cursor: pointer;
        }
        .search-row td {
            padding: 5px;
        }
        .add-row td {
            padding: 5px;
        }
    </style>
</head>
<body>
<div class="block-title">
                    <h2>Student Management System from Laravel</h2>
                </div>
    <table id="studentTable">
        <thead>
            <tr>
                <th onclick="sortTable(0)">ID</th>
                <th onclick="sortTable(1)">First Name</th>
                <th onclick="sortTable(2)">Last Name</th>
                <th onclick="sortTable(3)">Birth Place</th>
                <th onclick="sortTable(4)">Birth Date</th>
                <th>Actions</th>
            </tr>
            <tr class="search-row">
                <td><input type="number" id="search_sid" class="search-input" placeholder="ID"></td>
                <td><input type="text" id="search_fname" class="search-input" placeholder="First Name"></td>
                <td><input type="text" id="search_lname" class="search-input" placeholder="Last Name"></td>
                <td><input type="text" id="search_birthplace" class="search-input" placeholder="Birth Place"></td>
                <td><input type="date" id="search_birthDate" class="search-input"></td>
                <td><button onclick="searchStudents()" class="search-button">Search</button></td>
            </tr>
            <tr class="add-row">
                <th>Add New Student</th>
                <td><input type="text" id="fname" placeholder="First Name" required></td>
                <td><input type="text" id="lname" placeholder="Last Name" required></td>
                <td><input type="text" id="birthplace" placeholder="Birth Place" required></td>
                <td><input type="date" id="birthDate" required></td>
                <td><button onclick="createStudent()" class="search-button">Add</button></td>
            </tr>
        </thead>
        <tbody id="studentTableBody"></tbody>
    </table>
    
    <div id="pagination" class="pagination"></div>

    <script>
        let currentPage = 1;
        let sortColumn = 0;
        let sortDirection = 'asc';
        const RECORDS_PER_PAGE = 5;

        // Add CSRF token to all AJAX requests
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        document.addEventListener('DOMContentLoaded', function() {
            loadStudents();
        });

        function loadStudents() {
            fetch(`/students/list?page=${currentPage}&sort=${sortColumn}&direction=${sortDirection}`)
                .then(response => response.json())
                .then(data => {
                    displayStudents(data.students);
                    updatePagination(data.totalPages);
                });
        }

        function createStudent() {
            const data = {
                fname: document.getElementById('fname').value,
                lname: document.getElementById('lname').value,
                birthplace: document.getElementById('birthplace').value,
                birthDate: document.getElementById('birthDate').value
            };

            if (!data.fname || !data.lname || !data.birthplace || !data.birthDate) {
                alert('Please fill all fields');
                return;
            }

            fetch('/students', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    loadStudents();
                    document.getElementById('fname').value = '';
                    document.getElementById('lname').value = '';
                    document.getElementById('birthplace').value = '';
                    document.getElementById('birthDate').value = '';
                } else {
                    alert('Failed to create record!');
                }
            });
        }

        function toggleUpdate(sid) {
            const cells = document.querySelectorAll(`.editable-${sid}`);
            const updateBtn = event.target;
            
            if (updateBtn.textContent === 'Edit') {
                updateBtn.textContent = 'Save';
                cells.forEach(cell => {
                    const value = cell.textContent;
                    cell.innerHTML = `<input type="text" value="${value}">`;
                });
            } else {
                const updates = {
                    sid: sid,
                    fname: cells[0].querySelector('input').value,
                    lname: cells[1].querySelector('input').value,
                    birthplace: cells[2].querySelector('input').value,
                    birthDate: cells[3].querySelector('input').value
                };
                
                fetch(`/students/${sid}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(updates)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        updateBtn.textContent = 'Edit';
                        loadStudents();
                    } else {
                        alert('Failed to update record!');
                    }
                });
            }
        }

        function deleteStudent(sid) {
            if (confirm('Are you sure you want to delete this record?')) {
                fetch(`/students/${sid}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        loadStudents();
                    } else {
                        alert('Failed to delete record!');
                    }
                });
            }
        }

        function searchStudents() {
            const searchParams = new URLSearchParams({
                sid: document.getElementById('search_sid').value,
                fname: document.getElementById('search_fname').value,
                lname: document.getElementById('search_lname').value,
                birthplace: document.getElementById('search_birthplace').value,
                birthDate: document.getElementById('search_birthDate').value
            });

            fetch(`/students/search?${searchParams.toString()}`)
                .then(response => response.json())
                .then(data => {
                    displayStudents(data.students);
                    updatePagination(data.totalPages);
                });
        }

        function sortTable(column) {
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }
            loadStudents();
        }
        function displayStudents(students) {
            const tbody = document.getElementById('studentTableBody');
            tbody.innerHTML = '';
            
            students.forEach(student => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${student.sid}</td>
                    <td class="editable-${student.sid}">${student.fname}</td>
                    <td class="editable-${student.sid}">${student.lname}</td>
                    <td class="editable-${student.sid}">${student.birthplace}</td>
                    <td class="editable-${student.sid}">${student.birthDate}</td>
                    <td>
                        <button onclick="toggleUpdate(${student.sid})">Edit</button>
                        <button onclick="deleteStudent(${student.sid})">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        function updatePagination(totalPages) {
            const pagination = document.getElementById('pagination');
            if (totalPages <= 1) {
                pagination.innerHTML = '';
                return;
            }

            let html = '';
            
            if (currentPage > 1) {
                html += `<a href="#" onclick="gotoPage(1)">«</a> `;
                html += `<a href="#" onclick="gotoPage(${currentPage - 1})">‹</a> `;
            }
            
            let startPage, endPage;
            if (totalPages <= 7) {
                startPage = 1;
                endPage = totalPages;
            } else {
                if (currentPage <= 4) {
                    startPage = 1;
                    endPage = 7;
                } else if (currentPage >= totalPages - 3) {
                    startPage = totalPages - 6;
                    endPage = totalPages;
                } else {
                    startPage = currentPage - 3;
                    endPage = currentPage + 3;
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                html += `<a href="#" ${i === currentPage ? 'class="active"' : ''} onclick="gotoPage(${i})">${i}</a> `;
            }

            if (currentPage < totalPages) {
                html += `<a href="#" onclick="gotoPage(${currentPage + 1})">›</a> `;
                html += `<a href="#" onclick="gotoPage(${totalPages})">»</a>`;
            }

            pagination.innerHTML = html;
        }

        function gotoPage(page) {
            currentPage = page;
            loadStudents();
        }

      
    </script>
</body>
</html>


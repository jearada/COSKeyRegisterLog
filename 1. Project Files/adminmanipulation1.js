let currentEditingRow = null;
let editType = "";

// show tabs depending on button
function showTab(tabId) {
    document.querySelectorAll('.table-section').forEach(section => {
        section.classList.remove('active');
    });
    document.getElementById(tabId).classList.add('active');
    document.getElementById('addRoomForm').style.display = 'none';
}

function showTab(tabId) {
    var sections = document.getElementsByClassName("table-section");
    for (var i = 0; i < sections.length; i++) {
        sections[i].classList.remove("active");
    }
    document.getElementById(tabId).classList.add("active");

    if(tabId === 'viewLogs') {
        document.getElementById("addRoomForm").style.display = "none";
    }
    if(tabId === 'viewRooms' && !document.getElementById("radioAdd").checked) {
            document.getElementById("addRoomForm").style.display = "none";
    }
}

// to show or hide form for adding new rooms
function toggleRoomForm() {
    const form = document.getElementById('addRoomForm');
    document.querySelectorAll('.table-section').forEach(section => {
        section.classList.remove('active');
    });
    
    form.style.display = (form.style.display === 'block') ? 'none' : 'block';
    document.getElementById('radioAdd').checked = true;
}

// show viewRooms tab when adding new rooms
function prepareAddRoom() {
    showTab('viewRooms');
    document.getElementById("addRoomForm").style.display = "block";
}

// for new entry ng room
function addNewRoom() {
    const manualId = document.getElementById('newRoomID').value;
    const building = document.getElementById('building').value;
    const roomType = document.getElementById('newRoomType').value;
    const roomId = manualId.trim() !== "" ? manualId : "CS-" + (Math.floor(Math.random() * 900) + 100);
    const table = document.getElementById('roomsTable').getElementsByTagName('tbody')[0];
    const newRow = table.insertRow();
    newRow.innerHTML = `
        <td>${roomId}</td>
        <td>${building}</td>
        <td>${roomType}</td>
        <td style="color: #28a745; font-weight: bold;">Available</td>
        <td class="action-cell">
            <button class="btn-action edit-link" onclick="openEditRoomModal(this)">Edit</button>
            <button class="btn-action delete-link" onclick="deleteRow(this)">Delete</button>
        </td>
    `;
    document.getElementById('newRoomID').value = "";
    document.getElementById('building').value = "";
    document.getElementById('newRoomType').value = "Non-Airconditioned";

    showTab('viewRooms');
    document.getElementById('radioRooms').checked = true;
}

// delete a log entry
function deleteLog(uid) {
    if(confirm("Are you sure you want to delete this log entry?")) {
        window.location.href = `adminmanipulation.php?delete_uid=${uid}`;
    }
}

// delete a room entry
function deleteRoom(roomid) {
    if(confirm("Are you sure you want to delete this room?")) {
        window.location.href = `adminmanipulation.php?roomid=${roomid}`;
    }
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

// pop up for editing room entry 
function openEditRoomModal(btn) {
    currentEditingRow = btn.closest('tr');
    
    document.querySelector('input[name="action"]').value = "update_room";
    
    document.getElementById('modalTitle').innerText = "Edit Room Details";
    document.getElementById('modalInputs').innerHTML = `
        <label>Room ID (Read Only)</label>
        <input type="text" name="roomid" class="room-input" value="${currentEditingRow.cells[0].innerText}" readonly>
        
        <label>Building</label>
        <input type="text" name="building" class="room-input" value="${currentEditingRow.cells[1].innerText}">
        
        <label>Room Type</label>
        <select name="roomtype" class="room-input">
            <option value="Norm" ${currentEditingRow.cells[2].innerText === 'Non-Airconditioned' ? 'selected' : ''}>Non-Airconditioned</option>
            <option value="Airconditioned" ${currentEditingRow.cells[2].innerText === 'Airconditioned' ? 'selected' : ''}>Airconditioned</option>
        </select>
    `;
    document.getElementById('editModal').style.display = 'flex';
}

function openEditLogModal(btn) {
currentEditingRow = btn.closest('tr');
document.getElementById('modalTitle').innerText = "Edit Key Log Entry";

document.getElementById('modalInputs').innerHTML = `
    <label>UID (Read Only)</label>
    <input type="text" name="uid" class="room-input" value="${currentEditingRow.cells[0].innerText}" readonly>
    
    <label>Name</label>
    <input type="text" name="fullname" class="room-input" value="${currentEditingRow.cells[1].innerText}">
    
    <label>Student No.</label>
    <input type="text" name="studentno" class="room-input" value="${currentEditingRow.cells[2].innerText}">
    
    <label>Block</label>
    <input type="text" name="block" class="room-input" value="${currentEditingRow.cells[3].innerText}">
    
    <label>Email</label>
    <input type="text" name="email" class="room-input" value="${currentEditingRow.cells[4].innerText}">
    
    <label>Room ID</label>
    <input type="text" name="roomid" class="room-input" value="${currentEditingRow.cells[5].innerText}">
    
    <label>Purpose</label>
    <input type="text" name="purpose" class="room-input" value="${currentEditingRow.cells[6].innerText}">
    
    <label>Date</label>
    <input type="text" name="date" class="room-input" value="${currentEditingRow.cells[7].innerText}">
    
    <label>Time</label>
    <input type="text" name="time" class="room-input" value="${currentEditingRow.cells[8].innerText}">
    
    <label>Door Status</label>
    <input type="text" name="doorstatus" class="room-input" value="${currentEditingRow.cells[9].innerText}">
`;
document.getElementById('editModal').style.display = 'flex';
}

// for editing borrower log entry
function openEditLogModal(btn) {
    const row = btn.closest('tr');
    const cells = row.getElementsByTagName('td');
    const container = document.getElementById('modalInputs');
    
    document.getElementById('modalAction').value = "update_log";
    document.getElementById('modalTitle').innerText = "Edit Key Log Entry";

    const data = [
        { label: 'UID', name: 'uid', value: cells[0].innerText, readonly: true },
        { label: 'Full Name', name: 'fullname', value: cells[1].innerText },
        { label: 'Student No.', name: 'studentno', value: cells[2].innerText },
        { label: 'Block', name: 'block', value: cells[3].innerText },
        { label: 'Email', name: 'email', value: cells[4].innerText },
        { label: 'Room ID', name: 'roomid', value: cells[5].innerText },
        { label: 'Purpose', name: 'purpose', value: cells[6].innerText },
        { label: 'Date', name: 'date', value: cells[7].innerText },
        { label: 'Time', name: 'time', value: cells[8].innerText },
        { label: 'Status', name: 'doorstatus', value: cells[9].innerText }
    ];

    container.innerHTML = '';
    data.forEach(item => {
        const div = document.createElement('div');
        div.innerHTML = `<label>${item.label}</label>
                            <input type="text" name="${item.name}" value="${item.value}" ${item.readonly ? 'readonly' : ''}>`;
        container.appendChild(div);
    });

    document.getElementById('editModal').style.display = 'flex';
}

function openEditRoomModal(btn) {
    const row = btn.closest('tr');
    const cells = row.getElementsByTagName('td');
    const container = document.getElementById('modalInputs');
    
    document.getElementById('modalAction').value = "update_room";
    document.getElementById('modalTitle').innerText = "Edit Room Details";

    const data = [
        { label: 'Room ID', name: 'roomid', value: cells[0].innerText, readonly: true },
        { label: 'Building Name', name: 'building', value: cells[1].innerText },
        { label: 'Room Type', name: 'roomtype', value: cells[2].innerText, isSelect: true }
    ];

    container.innerHTML = '';
    data.forEach(item => {
        const div = document.createElement('div');
        if(item.isSelect) {
            div.innerHTML = `<label>${item.label}</label>
                            <select name="${item.name}">
                                <option value="Non-Airconditioned" ${item.value === 'Non-Airconditioned' ? 'selected' : ''}>Non-Airconditioned</option>
                                <option value="Airconditioned" ${item.value === 'Airconditioned' ? 'selected' : ''}>Airconditioned</option>
                            </select>`;
        } else {
            div.innerHTML = `<label>${item.label}</label>
                                <input type="text" name="${item.name}" value="${item.value}" ${item.readonly ? 'readonly' : ''}>`;
        }
        container.appendChild(div);
    });

    document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

function saveEdit() {
    if (editType === "room") {
        currentEditingRow.cells[0].innerText = document.getElementById('editRoomId').value;
        currentEditingRow.cells[1].innerText = document.getElementById('editRoomType').value;
    } else if (editType === "log") {
        currentEditingRow.cells[0].innerText = document.getElementById('editUid').value;
        currentEditingRow.cells[1].innerText = document.getElementById('editName').value;
        currentEditingRow.cells[2].innerText = document.getElementById('editSNo').value;
        currentEditingRow.cells[3].innerText = document.getElementById('editBlock').value;
        currentEditingRow.cells[4].innerText = document.getElementById('editEmail').value;
        currentEditingRow.cells[5].innerText = document.getElementById('editRoomIdLog').value;
        currentEditingRow.cells[6].innerText = document.getElementById('editPurpose').value;
        currentEditingRow.cells[7].innerText = document.getElementById('editDate').value;
        currentEditingRow.cells[8].innerText = document.getElementById('editTime').value;
        currentEditingRow.cells[9].innerText = document.getElementById('editDoor').value;
    }
    closeModal();
}
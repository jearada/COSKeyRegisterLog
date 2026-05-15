<?php
    include("database.php");

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // para hindi masira yung ui kapag may error
    set_exception_handler(function($e) {
        $errorMessage = addslashes($e->getMessage());
        echo "<script>
                alert('A system error occurred:\\n\\n' + '$errorMessage');
                window.location.href = 'adminmanipulation.php'; 
            </script>";
        exit();
    });

    // Default to viewLogs pag open
    $activeTab = 'viewLogs'; 

    // so the tab will stay sa tamang tab after buttons
    if (isset($_GET['tab'])) {
        $activeTab = ($_GET['tab'] === 'rooms') ? 'viewRooms' : 'viewLogs';
    }
    
    elseif (isset($_POST['action'])) {
        if (in_array($_POST['action'], ['search_rooms', 'update_room', 'add_room'])) {
            $activeTab = 'viewRooms';
        } else {
            $activeTab = 'viewLogs';
        }
    }
    // to stay in view rooms tab after deleting a room record
    elseif (isset($_GET['roomid'])) {
        $activeTab = 'viewRooms';
    }

    // to update logs
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_log') {
        $uid = $_POST['uid'] ?? '';
        $fullname = $_POST['fullname'] ?? '';
        $studentno = $_POST['studentno'] ?? '';
        $block = $_POST['block'] ?? '';
        $email = $_POST['email'] ?? '';
        $roomid = $_POST['roomid'] ?? '';
        $purpose = $_POST['purpose'] ?? '';
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $doorstatus = $_POST['doorstatus'] ?? '';

        if (!empty($uid) && !empty($fullname)) {
            $sqlEditLogs = "UPDATE tblborrowerlog SET FullName='$fullname', StudentNo='$studentno', Block='$block', Email='$email', RoomID='$roomid', Purpose='$purpose', Date='$date', Time='$time', DoorStatus='$doorstatus' WHERE UID='$uid'";
            mysqli_query($connection, $sqlEditLogs);
            header("Location: adminmanipulation.php");
            exit();
        }
    }

    // to update rooms
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_room') {
        $roomid = $_POST['roomid'] ?? '';
        $building = $_POST['building'] ?? '';
        $roomtype = $_POST['roomtype'] ?? '';

        if (!empty($roomid)) {
            $sql = "UPDATE tblrooms SET Building='$building', RoomType='$roomtype' WHERE RoomID='$roomid'";
            mysqli_query($connection, $sql);
            
            // para magstay sa room tab 
            header("Location: adminmanipulation.php?tab=rooms");
            exit();
        }
    }

    // delete borrower log
    if (isset($_GET["delete_uid"])) {
        $uid = $_GET["delete_uid"];
        $sql = "DELETE FROM tblborrowerlog WHERE UID='$uid'";
        mysqli_query($connection, $sql);
        header("Location: adminmanipulation.php");
        exit();
    }

    // delete room record 
    if (isset($_GET["roomid"])) {
        $roomid = $_GET["roomid"];
        $sql = "DELETE FROM tblrooms WHERE RoomID='$roomid'";
        mysqli_query($connection, $sql);
        // Stay on the rooms tab after deletion
        header("Location: adminmanipulation.php?tab=rooms");
        exit();
    }

    // SELECT AND SEARCH FOR BORROWER LOG (Display all records or search w textbox or date picker)
    $sqlSelectLogs = "SELECT * FROM tblborrowerlog WHERE 1=1";
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'search_logs'){

        $LogGeneralSearch = mysqli_real_escape_string($connection, $_POST['logGeneralSearch']);
        $DateSearch = mysqli_real_escape_string($connection, $_POST['DateSearch']);

        if (!empty($LogGeneralSearch)){
            $sqlSelectLogs .= " AND (FullName LIKE '%$LogGeneralSearch%' OR  StudentNo LIKE '%$LogGeneralSearch%'
                                OR Block LIKE '%$LogGeneralSearch%' OR Email LIKE '%$LogGeneralSearch%' OR RoomID LIKE '%$LogGeneralSearch%'
                                OR Purpose LIKE '%$LogGeneralSearch%' OR DoorStatus LIKE '%$LogGeneralSearch%')";
        }

        if (!empty($DateSearch)){
            $sqlSelectLogs .= " AND Date = '$DateSearch'";
        }
    }
    $sqlSelectLogs .= " ORDER BY UID DESC";
    $logsResult = $connection->query($sqlSelectLogs);

    
    $sqlSelectRooms = "SELECT * FROM tblrooms WHERE 1=1";

     if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'search_rooms'){
     $RoomSearch = mysqli_real_escape_string($connection, $_POST['roomSearch'] ?? '');

        if(!empty($RoomSearch)){
            $sqlSelectRooms .= " AND (RoomID LIKE '%$RoomSearch%' OR Building LIKE '%$RoomSearch%' OR RoomType LIKE '%$RoomSearch%')";
        }
     }
    $roomsResult = $connection->query($sqlSelectRooms);

    // add new room
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_room') {
    $roomid = $_POST['roomid'] ?? '';
    $building = $_POST['building'] ?? '';
    $roomtype = $_POST['roomtype'] ?? '';

    if (!empty($roomid) && !empty($building) && !empty($roomtype)) {
        $sql = "INSERT INTO tblrooms (RoomID, Building, RoomType) VALUES ('$roomid', '$building', '$roomtype')";
        $result = mysqli_query($connection, $sql);

        if ($result) {
            header("Location: adminmanipulation.php?tab=rooms&success=1");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="adminmanipulation.css" rel="stylesheet">
    <script src="adminmanipulation1.js"></script>
    <title>URS | Admin Manipulation</title>
</head>
<body>

    <header>University of Rizal System | College of Science</header>

    <div class="main-layout">
        <div class="container">
            <div id="viewLogs" class="table-section <?php echo ($activeTab == 'viewLogs') ? 'active' : ''; ?>">
                <div class="header-container">
                    <h1 class="page-title">Key Logs</h1>
                   <form class="search-controls" action="adminmanipulation.php" method="POST">
                        <input type="hidden" name="action" value="search_logs">

                        <input type="text" id="logSearch" name="logGeneralSearch" placeholder="Search records..."
                            value="<?php echo isset($_POST['logGeneralSearch']) ? htmlspecialchars($_POST['logGeneralSearch']) : ''; ?>">

                        <input type="date" id="dateSearch" name="DateSearch"
                            value="<?php echo isset($_POST['DateSearch']) ? htmlspecialchars($_POST['DateSearch']) : ''; ?>">

                        <button class="btn-search-exec" type="submit">Search</button>

                        <?php if(!empty($_POST['logGeneralSearch']) || !empty($_POST['DateSearch'])): ?>
                            <a href="adminmanipulation.php?tab=logs" style="color:red; text-decoration:none; font-size:12px; margin-left:10px; font-weight: bold;">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 80px;">UID</th>
                                <th>Name</th>
                                <th style="width: 120px;">Student No.</th>
                                <th style="width: 80px;">Block</th>
                                <th>Email</th>
                                <th style="width: 100px;">Room ID</th>
                                <th>Purpose</th>
                                <th style="width: 150px;">Date</th>
                                <th style="width: 150px;">Time</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="logsBody">
                            
                            <?php while($row = $logsResult->fetch_assoc()):?>
                            <tr>
                                <td><div class="cell-content"><?php echo $row['UID']; ?></div></td>
                                <td><div class="cell-content"><?php echo $row['FullName']; ?></div></td>
                                <td><?php echo $row['StudentNo']; ?></td>
                                <td><?php echo $row['Block']; ?></td>
                                <td><div class="cell-content"><?php echo $row['Email']; ?></div></td>
                                <td><?php echo $row['RoomID']; ?></td>
                                <td><div class="cell-content"><?php echo $row['Purpose']; ?></div></td>
                                <td class="date-cell"><?php echo $row['Date']; ?></td>
                                <td><?php echo $row['Time']; ?></td>
                                <td><?php echo $row['DoorStatus']; ?></td>
                                <td class="action-cell">
                                    <button class="btn-action edit-link" onclick="openEditLogModal(this)">Edit</button>
                                    <button class="btn-action delete-link" onclick="deleteLog('<?php echo $row['UID']; ?>')">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div id="viewRooms" class="table-section <?php echo ($activeTab == 'viewRooms') ? 'active' : ''; ?>">
                <div class="header-container">
                    <h2 style="margin: 0;">Registered Rooms</h2>
                    <form class="search-controls" action="adminmanipulation.php" method="POST">
                        <input type="hidden" name="action" value="search_rooms">
                        
                        <input type="text" name="roomSearch" id="roomSearch" placeholder="Search rooms..." 
                            value="<?php echo htmlspecialchars($_POST['roomSearch'] ?? ''); ?>">
                            
                        <button type="submit" class="btn-search-exec">Search</button>

                        <?php if(!empty($_POST['roomSearch'])): ?>
                            <a href="adminmanipulation.php?tab=rooms" style="color:red; text-decoration:none; font-size:12px; margin-left:10px;">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="table-wrapper">
                    <table id="roomsTable">
                        <thead>
                            <tr>
                                <th>Room ID</th>
                                <th>Building</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="roomsBody">
                            <?php while($row = $roomsResult->fetch_assoc()):?>
                            <tr>
                                <td><?php echo $row['RoomID']; ?></td>
                                <td><?php echo $row['Building']; ?></td>
                                <td><?php echo $row['RoomType']; ?></td>
                                <td class="action-cell">
                                    <button class="btn-action edit-link" onclick="openEditRoomModal(this)">Edit</button>
                                    <button class="btn-action delete-link" onclick="deleteRoom('<?php echo $row['RoomID']; ?>')">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <nav class="side-nav">
            <h4>Dashboard Menu</h4>
            <div class="filter-group">
                <label class="filter-option">
                    <input type="radio" name="menu" id="radioLogs" onclick="showTab('viewLogs')" 
                        <?php echo ($activeTab == 'viewLogs') ? 'checked' : ''; ?>>
                    <span class="filter-label">View Logs</span>
                </label>
                <label class="filter-option">
                    <input type="radio" name="menu" id="radioRooms" onclick="showTab('viewRooms')" 
                        <?php echo ($activeTab == 'viewRooms') ? 'checked' : ''; ?>>
                    <span class="filter-label">View Rooms</span>
                </label>
                <label class="filter-option">
                    <input type="radio" name="menu" id="radioAdd" onclick="prepareAddRoom()">
                    <span class="filter-label">Add Rooms/Keys</span>
                </label>
            </div>

            <div id="addRoomForm" style="display:none;">
                <h4 style="color: #064439; margin-bottom: 10px; margin-top: 15px;">New Room Entry</h4>
                <form method="POST" action="adminmanipulation.php">
                    <input type="hidden" name="action" value="add_room">
                    
                    <input type="text" name="roomid" class="room-input" placeholder="Room ID (e.g., CS-101)" required>
                    <input type="text" name="building" class="room-input" placeholder="Building Name" required>
                    
                    <select name="roomtype" class="room-input">
                        <option value="" disabled selected>Select Room Type</option>
                        <option value="Non-Airconditioned">Non-Airconditioned</option>
                        <option value="Airconditioned">Airconditioned</option>
                    </select>
                    
                    <button type="submit" class="submit-room-btn">Save to Database</button>
                </form>
            </div>
        </nav>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <form method="POST" action="adminmanipulation.php">
                <input type="hidden" name="action" id="modalAction" value="update_log">
                <h3 id="modalTitle">Edit Entry</h3>
                <div id="modalInputs" class="horizontal-grid"></div>
                <div class="modal-buttons">
                    <button type="submit" class="btn-save">Save Changes</button>
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <a href="index.php" class="back-home-btn">← Sign Out</a>
    <footer>&copy; 2026 URS College of Science</footer>
</body>
</html>
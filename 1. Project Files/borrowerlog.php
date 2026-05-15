<?php
    include("database.php");

    // to add new borrower record
     if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])){
        $name = $_POST['name'] ?? ''; 
        $SN = $_POST['SN'] ?? ''; 
        $block = $_POST['block'] ?? '';
        $email = $_POST['email'] ?? ''; 
        $RID = $_POST['RID'] ?? ''; 
        $purpose = $_POST['purpose'] ?? ''; 
        $date = $_POST['date'] ?? ''; 
        $time = $_POST['time'] ?? ''; 
        
        if ($purpose === 'Borrowing') {
            $DS = 'Open';
        } else {
            $DS = $_POST['DS'] ?? 'Locked';
        }

        if(!empty($name) && !empty($SN) && !empty($block) && !empty($email) && !empty($RID) && !empty($purpose) && !empty($date) && !empty($time) && !empty($DS)){
            $sql = "INSERT INTO tblborrowerlog (FullName, StudentNo, Block, Email, RoomID, Purpose, Date, Time, DoorStatus)
                    VALUES ('$name', '$SN', '$block', '$email', '$RID', '$purpose', '$date', '$time', '$DS')";
            
            if(mysqli_query($connection, $sql)){
                header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
                exit();
            }
        } else {
            echo "<script>alert('All fields are required');</script>";
        }
    }

    // select door status
    $sqlSelectDoorStatus = "SELECT rm.RoomID, brw.DoorStatus FROM tblrooms AS rm LEFT JOIN tblborrowerlog AS brw 
                            ON rm.RoomID = brw.RoomID 
                            AND brw.UID = (SELECT MAX(b2.UID) FROM tblborrowerlog as b2 WHERE rm.RoomID = b2.RoomID)";
    
    $resultSDS = $connection->query($sqlSelectDoorStatus);

     // select available keys
    $sqlAvailableKeys = "SELECT rm.RoomID FROM tblrooms AS rm LEFT JOIN tblborrowerlog AS brw 
                        ON rm.RoomID = brw.RoomID 
                        AND brw.UID = (SELECT MAX(b2.UID) FROM tblborrowerlog as b2 WHERE rm.RoomID = b2.RoomID) 
                        WHERE (brw.Purpose = 'Returning' or brw.Purpose IS Null)";

    $resultAK = $connection->query($sqlAvailableKeys);

    // select borrowed keys
    $sqlBorrowedKeys= "SELECT rm.RoomID FROM tblrooms AS rm LEFT JOIN tblborrowerlog AS brw 
                    ON rm.RoomID = brw.RoomID AND brw.UID = (SELECT MAX(b2.UID) FROM tblborrowerlog as b2 WHERE rm.RoomID = b2.RoomID) 
                    WHERE (brw.Purpose = 'Borrowing');";

    $resultBK = $connection->query($sqlBorrowedKeys);

    // if purpose is for borrowing
    $available = [];
    while($row = $resultAK->fetch_assoc()) { 
        $available[] = $row['RoomID']; 
    }

    // if purpose is for  returning
    $borrowed = [];
    while($row = $resultBK->fetch_assoc()) { 
        $borrowed[] = $row['RoomID']; 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URS | Borrower Portal</title>
    <link rel="stylesheet" href="borrowerlog1.css">
</head>
<body>
<header>University of Rizal System | Borrower Portal</header>

    <div class="main-layout">
        <div class="container">
            
            <div class="status-container">
                <div class="table-column">
                    <h2>Room & Door Status</h2>
                    <div class="table-wrapper">
                        <table id="statusTable">
                            <thead>
                                <tr>
                                    <th>Room ID</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($resultSDS) { $resultSDS->data_seek(0); } ?>
                                <?php while($rowSDS = $resultSDS->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $rowSDS['RoomID']; ?></td>
                                        <td>
                                            <span class="<?php echo ($rowSDS['DoorStatus'] == 'Locked') ? 'locked-status' : 'open-status'; ?>">
                                                <?php echo $rowSDS['DoorStatus'] ?? 'N/A'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-column">
                    <h2>Available Keys</h2>
                    <div class="table-wrapper">
                        <table id="availableKeysTable">
                            <thead>
                                <tr>
                                    <th>Key Designation</th>
                                </tr>
                            </thead>
                            <tbody id="keysBody">
                                <?php 
                                if($resultAK) { $resultAK->data_seek(0); }
                                while($rowAK = $resultAK->fetch_assoc()): ?>
                                <tr><td><?php echo $rowAK['RoomID']; ?></td></tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="form-container">
                <h2>Registration Form</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" placeholder="Juan Dela Cruz" name="name" required>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label>Student Number</label>
                            <input type="text" placeholder="M2024-XXXXX" name="SN" required>
                        </div>
                        <div class="input-group">
                            <label>Year & Block</label>
                            <input type="text" placeholder="II-E" name="block" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" placeholder="example@urs.edu.ph" name="email" required>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label>Purpose</label>
                            <select id="purposeSelect" onchange="toggleDoorStatus(); updateRoomOptions();" name="purpose" required>
                                <option value="" disabled selected>Select Purpose</option>
                                <option value="Borrowing">Borrowing</option>
                                <option value="Returning">Returning</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Room ID</label>
                            <select name="RID" required>
                                <option value="" disabled selected>Select Room</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label>Date</label>
                            <input type="date" id="logDate" name="date" required>
                        </div>
                        <div class="input-group">
                            <label>Time</label>
                            <input type="time" id="logTime" name="time" required>
                        </div>
                    </div>

                    <div class="input-group" id="doorStatusGroup">
                        <label>Door Status (Returning Only)</label>
                        <select id="doorStatus" name="DS">
                            <option value="Locked">Locked</option>
                            <option value="Open">Open</option>
                        </select>
                    </div>

                    <input type="submit" class="submit-btn" name="register" value="Register">
                </form>
            </div>
        </div>
    </div>

    <a href="index.php" class="back-home-btn">← Back</a>
    <footer>&copy; 2026 URS College of Science | Developed by Cenir Arada Encinares</footer>

    <script>

        // para makuha na agad current date and time after mag load ng webpage
        window.onload = function() {
            const now = new Date();
            document.getElementById('logDate').valueAsDate = now;
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('logTime').value = hours + ":" + minutes;
        };
        
        // depends sa purpose. if purpose = borrowing, door status section is hidden. if purpose = returning, section appears
        function toggleDoorStatus() {
            const purpose = document.getElementById('purposeSelect').value;
            const doorGroup = document.getElementById('doorStatusGroup');
            doorGroup.style.display = (purpose === "Returning") ? "block" : "none";
        }
        
        // for different values ng drop down
        const roomData = {
            "Borrowing": <?php echo json_encode($available); ?>, 
            "Returning": <?php echo json_encode($borrowed); ?>
        };

        // changes the values sa drop down depends sa purpose
        function updateRoomOptions() {
            const purpose = document.getElementById('purposeSelect').value;
            const roomSelect = document.getElementsByName('RID')[0];
            roomSelect.innerHTML = '<option value="" disabled selected>Select Room</option>';
            const options = roomData[purpose] || [];
            options.forEach(room => {
                const opt = document.createElement('option');
                opt.value = room;
                opt.textContent = room;
                roomSelect.appendChild(opt);
            });
        }
    </script>
</body>
</html>
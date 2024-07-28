<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['id']; // Fetch user ID from session

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$dbname = "dblms"; // Update with your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch one note per video for the logged-in user
$notesSql = "SELECT n.id, n.notes, DATE(n.created_at) as created_date, DATE(n.updated_at) as updated_date, v.judul_video
             FROM notes n
             JOIN videos v ON n.video_id = v.id_video
             WHERE n.user_id = ?
             GROUP BY n.video_id
             ORDER BY n.created_at DESC";
$stmt = $conn->prepare($notesSql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$notesResult = $stmt->get_result();

// Check if the query executed successfully
if (!$notesResult) {
    die("Error executing query: " . $conn->error);
}

// Handle note update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_note'])) {
    $noteId = $_POST['note_id'];
    $updatedNote = $_POST['note_text'];

    // Update note with timestamp
    $updateSql = "UPDATE notes SET notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sii", $updatedNote, $noteId, $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: notes.php?success=note_updated");
    } else {
        header("Location: notes.php?error=update_failed");
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Handle note deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_note'])) {
    $noteId = $_POST['note_id'];

    // Delete the note
    $deleteSql = "DELETE FROM notes WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("ii", $noteId, $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: notes.php?success=note_deleted");
    } else {
        header("Location: notes.php?error=delete_failed");
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Notes - Lab SIMI</title>
    <link href="../assets/img/jakarta-logo.png" rel="icon">
    <link href="../assets/img/jakarta-logo.png" rel="apple-touch-icon">
    <link rel="stylesheet" href="assets/css/styles.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.12/sweetalert2.min.css">
    <style>
        .note-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .note-actions .btn {
            margin: 0;
        }
        .note-actions .btn-danger {
            background-color: #dc3545; /* Red color */
            border-color: #dc3545; /* Red color */
        }
        .note-actions .btn-danger:hover {
            background-color: #c82333; /* Darker red on hover */
            border-color: #bd2130; /* Darker red on hover */
        }
        .note-actions .btn-primary {
            order: 2; /* Move edit button to the right */
        }
        .note-actions .btn-danger {
            order: 1; /* Move delete button to the left */
        }
    </style>
</head>

<body>
    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        <?php include("include/sidebar.php"); ?>
        <!-- Sidebar End -->

        <!-- Main wrapper -->
        <div class="body-wrapper">
            <!-- Header Start -->
            <!-- Header End -->

            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-4">My Notes</h5>
                        <div class="row gy-4">
                            <?php
                            if ($notesResult->num_rows > 0) {
                                // Output data for each note
                                while ($row = $notesResult->fetch_assoc()) {
                                    echo '<div class="col-lg-4">';
                                    echo '<div class="card">';
                                    echo '<div class="card-body">';
                                    echo '<h5 class="card-title">' . htmlspecialchars($row['judul_video']) . '</h5>';
                                    echo '<p class="card-text" id="note-' . $row['id'] . '">' . htmlspecialchars($row['notes']) . '</p>';
                                    echo '<p class="card-text text-muted">Last updated: ' . htmlspecialchars($row['updated_date']) . '</p>';
                                    echo '<div class="note-actions">';
                                    echo '<button class="btn btn-danger" onclick="deleteNote(' . $row['id'] . ')">Delete</button>';
                                    echo '<button class="btn btn-primary" onclick="editNote(' . $row['id'] . ', \'' . htmlspecialchars($row['notes']) . '\')">Edit</button>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p>No notes found.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.12/sweetalert2.all.min.js"></script>
    <script>
        function editNote(noteId, currentNote) {
            Swal.fire({
                title: 'Edit Note',
                input: 'textarea',
                inputLabel: 'Your Note',
                inputValue: currentNote,
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to write something!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form to send the updated note to the server
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'notes.php';
                    
                    const noteIdField = document.createElement('input');
                    noteIdField.type = 'hidden';
                    noteIdField.name = 'note_id';
                    noteIdField.value = noteId;
                    form.appendChild(noteIdField);

                    const noteTextField = document.createElement('input');
                    noteTextField.type = 'hidden';
                    noteTextField.name = 'note_text';
                    noteTextField.value = result.value;
                    form.appendChild(noteTextField);

                    const updateNoteField = document.createElement('input');
                    updateNoteField.type = 'hidden';
                    updateNoteField.name = 'update_note';
                    form.appendChild(updateNoteField);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function deleteNote(noteId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form to send the delete request to the server
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'notes.php';
                    
                    const noteIdField = document.createElement('input');
                    noteIdField.type = 'hidden';
                    noteIdField.name = 'note_id';
                    noteIdField.value = noteId;
                    form.appendChild(noteIdField);

                    const deleteNoteField = document.createElement('input');
                    deleteNoteField.type = 'hidden';
                    deleteNoteField.name = 'delete_note';
                    form.appendChild(deleteNoteField);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const error = urlParams.get('error');

            if (success === 'note_updated') {
                Swal.fire({
                    icon: 'success',
                    title: 'Note Updated',
                    text: 'Your note has been updated successfully!',
                });
            } else if (success === 'note_deleted') {
                Swal.fire({
                    icon: 'success',
                    title: 'Note Deleted',
                    text: 'Your note has been deleted successfully!',
                });
            } else if (error === 'update_failed') {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: 'Failed to update note. Please try again!',
                });
            } else if (error === 'delete_failed') {
                Swal.fire({
                    icon: 'error',
                    title: 'Delete Failed',
                    text: 'Failed to delete note. Please try again!',
                });
            }
        });
    </script>
</body>

</html>

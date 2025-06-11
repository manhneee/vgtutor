<?php
session_start();
if (!isset($_SESSION['adminid']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../login.php?error=Unauthorized access");
    exit;
}

if (!isset($_GET['tutorid'])) {
    header("Location: tutor.php?error=No tutor ID specified");
    exit;
}

include "../../DB_connection.php";
include "../data/tutor.php";

// Fetch tutor data
$tutorid = intval($_GET['tutorid']);
$tutor = getTutor($conn, $tutorid);

if (!$tutor) {
    header("Location: tutor.php?error=Tutor not found");
    exit;
}

// Handle form submission
$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gpa = $_POST['gpa'] ?? '';
    $description = $_POST['description'] ?? '';

    $update = $conn->prepare("UPDATE tutor_account SET gpa = ?, description = ? WHERE accountid = ?");
    if ($update->execute([$gpa, $description, $tutorid])) {
        $success = "Tutor updated successfully!";
        // Refresh tutor data
        $tutor = getTutor($conn, $tutorid);
    } else {
        $error = "Failed to update tutor.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tutor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="body-home">
    <?php include "../inc/navbar.php"; ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg" style="border: 2px solid #000;">
                    <div class="card-header text-white text-center" style="background-color: #f47119;">
                        <h3 class="mb-0">Edit Tutor</h3>
                    </div>
                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <?= $success ?>
                        </div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Tutor ID</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($tutor['accountid']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($tutor['email']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($tutor['name']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">GPA</label>
                                <input type="text" name="gpa" class="form-control" value="<?= htmlspecialchars($_POST['gpa'] ?? $tutor['gpa'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" maxlength="200" rows="4" required><?= htmlspecialchars($_POST['description'] ?? $tutor['description'] ?? '') ?></textarea>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary" style="background-color: #f47119; border-color: #f47119;">Update Tutor</button>
                                <a href="tutor.php" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
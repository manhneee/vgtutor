<?php
session_start();
if (isset($_SESSION['tutorid']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Tutor') {
    $tutorid = $_SESSION['tutorid'];
    
    // Get courseid from GET or POST (you may need to adjust this as per your navigation)
    $courseid = isset($_GET['courseid']) ? $_GET['courseid'] : (isset($_POST['courseid']) ? $_POST['courseid'] : '');
    //echo "<div style='background: #ffeeba; color: #856404; padding: 10px; margin: 10px 0; border: 1px solid #ffeeba; border-radius: 4px;'><strong>Debug:</strong> courseid = " . htmlspecialchars($courseid) . "</div>";
    // Include the data handler for course offering
    include "../data/course_offering.php";

    // Handle form submission
    $success = $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $grade = trim($_POST['grade'] ?? '');
        $price = intval($_POST['price'] ?? 0);
        $self_description = trim($_POST['self_intro'] ?? '');

        // Limit self_description to 1000 characters
        if (strlen($self_description) > 1000) {
            $self_description = substr($self_description, 0, 1000);
        }

        // Insert into pending_offering table using a function from course_offering.php
        $result = insertPendingOffering($tutorid, $courseid, $grade, $price, $self_description);

        if ($result === true) {
            $success = "Offer submitted and pending approval.";
        } else {
            $error = "Could not submit offer: " . htmlspecialchars($result);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Course</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="body-home">
    <?php include "navbar.php"; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header bg-orange text-white text-center">
                        <h3 class="mb-0">Offer</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <form action="" method="post">
                            <input type="hidden" name="courseid" value="<?= htmlspecialchars($courseid) ?>">
                            <div class="mb-3">
                                <label for="tutorid" class="form-label">Tutor ID</label>
                                <input type="text" class="form-control" id="tutorid" name="tutorid" value="<?= htmlspecialchars($tutorid) ?>" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="grade" class="form-label">Grade</label>
                                <input type="text" class="form-control" id="grade" name="grade" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price - per hour</label>
                                <input type="number" class="form-control" id="price" name="price" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label for="self_intro" class="form-label">Self Introduction (max 1000 characters)</label>
                                <textarea class="form-control" id="self_intro" name="self_intro" rows="6" maxlength="1000" required></textarea>
                                <div class="form-text">
                                    Maximum 1000 characters. Characters left: <span id="charsLeft">1000</span>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn bg-orange text-white">Submit Offer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    // Character count limit (1000 characters)
    const textarea = document.getElementById('self_intro');
    const charsLeft = document.getElementById('charsLeft');
    textarea.addEventListener('input', function() {
        const left = 1000 - this.value.length;
        charsLeft.textContent = left >= 0 ? left : 0;
        if (this.value.length > 1000) {
            this.value = this.value.substring(0, 1000);
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
} else {
    header("Location: ../login.php?error=Unauthorized access");
    exit;
}
?>
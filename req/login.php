<?php 

session_start();
if (isset($_POST['userid']) && 
    isset($_POST['password']))
{
    include "../DB_connection.php";

    $userid = trim($_POST['userid']); // Trim to avoid whitespace issues
    $pass = $_POST['password'];
    $role = $_POST['role'] ?? ''; // Use null coalescing operator to avoid undefined index

    if (empty($userid)) {
        $em = "User ID is required";
        header("Location: ../login.php?error=$em");
        exit;
    } elseif (empty($pass)) {
        $em = "Password is required";
        header("Location: ../login.php?error=$em");
        exit;
    } else {
        $sqladmincheck = "SELECT userid FROM `account`
                          INNER JOIN `admin_account` ON account.userid = admin_account.adminid
                          WHERE account.userid = ?";

        $stmt = $conn->prepare($sqladmincheck);
        $stmt->execute([$userid]);

        if ($stmt->rowCount() == 1) {
            $role = 'Admin';
            $sqladmin = "SELECT * FROM `account`
                         INNER JOIN `admin_account` ON account.userid = admin_account.adminid
                         WHERE account.userid = ?";
            $stmt = $conn->prepare($sqladmin);
            $stmt->execute([$userid]);

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC); // Explicit fetch mode for clarity
                $uid = $user['userid'];
                $password = $user['password'];

                // Normalize and trim values before comparison
                if (trim($uid) === trim($userid)) {
                    if (password_verify($pass, $password)) {
                        $_SESSION['role'] = $role;
                        $_SESSION['name'] = $user['name'];
                        if ($role === 'Admin') {
                            $admid = $user['adminid'];
                            $_SESSION['adminid'] = $admid;
                            header("Location: ../admin/index.php");
                            exit;
                        }
                    } else {
                        $em = "Incorrect password";
                        header("Location: ../login.php?error=$em");
                        exit;
                    }
                } else {
                    $em = "Incorrect userid";
                    header("Location: ../login.php?error=$em");
                    exit;
                }
            }
        } else {
            $em = "Incorrect userid or password";
            header("Location: ../login.php?error=$em");
            exit;
        }
    }
} else {
    header("Location: ../login.php");
    exit;
}

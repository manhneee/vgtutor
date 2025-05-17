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

        // Check if user is Admin
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
        } else {
            // Check if user is Tutor
            $sqltutorcheck = "SELECT userid FROM `account`
                    INNER JOIN `tutor_account` ON account.userid = tutor_account.accountid
                    WHERE account.userid = ?";
            $stmt = $conn->prepare($sqltutorcheck);
            $stmt->execute([$userid]);

            if ($stmt->rowCount() == 1) {
            $role = 'Tutor';
            $sqltutor = "SELECT * FROM `account`
                    INNER JOIN `tutor_account` ON account.userid = tutor_account.accountid
                    INNER JOIN `student_account` ON tutor_account.accountid = student_account.accountid
                    WHERE account.userid = ?";
            $stmt = $conn->prepare($sqltutor);
            $stmt->execute([$userid]);
            } else {
                // Check if user is Student
                $sqlstudentcheck = "SELECT userid FROM `account`
                            INNER JOIN `student_account` ON account.userid = student_account.accountid
                            WHERE account.userid = ?";
                $stmt = $conn->prepare($sqlstudentcheck);
                $stmt->execute([$userid]);

                if ($stmt->rowCount() == 1) {
                    $role = 'Student';
                    $sqlstudent = "SELECT * FROM `account`
                            INNER JOIN `student_account` ON account.userid = student_account.accountid
                            WHERE account.userid = ?";
                    $stmt = $conn->prepare($sqlstudent);
                    $stmt->execute([$userid]);
                    
                    // Check if Student is Tutor
                    $sqlIsTutor = "SELECT * FROM tutor_account WHERE accountid = ?";
                    $stmtIsTutor = $conn->prepare($sqlIsTutor);
                    $stmtIsTutor->execute([$userid]);
                    if ($stmtIsTutor->rowCount() == 1) {
                        $_SESSION['is_tutor'] = true;
                        $_SESSION['tutorid'] = $userid;
                    } else {
                        $_SESSION['is_tutor'] = false;
                        unset($_SESSION['tutorid']);
                    }
                
                }
            }
        }


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
                    else if ($role === 'Tutor') {
                        $tutorid = $user['accountid'];
                        $_SESSION['tutorid'] = $tutorid;
                        $_SESSION['studentid'] = $tutorid;
                        header("Location: ../tutor/index.php");
                        exit;
                    } else if ($role === 'Student') {
                        $studentid = $user['accountid'];
                        $_SESSION['studentid'] = $studentid;
                        if (isset($_SESSION['is_tutor']) && $_SESSION['is_tutor'] === true) {
                            $tutorid = $studentid;
                            $_SESSION['tutorid'] = $tutorid;
                        } else {
                            unset($_SESSION['tutorid']);
                        }
                        header("Location: ../student/index.php");
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
    }
    $em = "Incorrect userid or password";
    header("Location: ../login.php?error=$em");
    exit;
}



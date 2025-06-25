<?php
session_start();
include "../DB_connection.php";

// Kiểm tra input
if (!isset($_POST['userid']) || !isset($_POST['password'])) {
    header("Location: ../login.php?error=Missing required fields.");
    exit;
}
$userid = trim($_POST['userid']);
$password = $_POST['password'];

if (empty($userid)) {
    header("Location: ../login.php?error=User ID is required");
    exit;
}
if (empty($password)) {
    header("Location: ../login.php?error=Password is required");
    exit;
}

// 1. Kiểm tra Admin
$sql = "SELECT account.userid, account.password, aa.adminid, aa.name
        FROM account
        INNER JOIN admin_account aa ON account.userid = aa.adminid
        WHERE account.userid = ? AND account.is_verified = 1";
$stmt = $conn->prepare($sql);
$stmt->execute([$userid]);
if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['role'] = 'Admin';
        $_SESSION['adminid'] = $user['adminid'];
        $_SESSION['name'] = $user['name'];
        header("Location: ../admin/index.php");
        exit;
    }
}

// 2. Kiểm tra Tutor
$sql = "SELECT account.userid, account.password, ta.accountid, sa.email, sa.name, sa.major, sa.intake
        FROM account
        INNER JOIN tutor_account ta ON account.userid = ta.accountid
        INNER JOIN student_account sa ON ta.accountid = sa.accountid
        WHERE account.userid = ? AND account.is_verified = 1";
$stmt = $conn->prepare($sql);
$stmt->execute([$userid]);
if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['role'] = 'Tutor';
        $_SESSION['tutorid'] = $user['accountid'];
        $_SESSION['studentid'] = $user['accountid'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['major'] = $user['major'];
        $_SESSION['intake'] = $user['intake'];
        $_SESSION['is_tutor'] = true;
        header("Location: ../tutor/index.php");
        exit;
    }
}

// 3. Kiểm tra Student
$sql = "SELECT account.userid, account.password, sa.accountid, sa.email, sa.name, sa.major, sa.intake
        FROM account
        INNER JOIN student_account sa ON account.userid = sa.accountid
        WHERE account.userid = ? AND account.is_verified = 1";
$stmt = $conn->prepare($sql);
$stmt->execute([$userid]);
if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['role'] = 'Student';
        $_SESSION['studentid'] = $user['accountid'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['major'] = $user['major'];
        $_SESSION['intake'] = $user['intake'];
        // Kiểm tra Student cũng là Tutor?
        $sqlIsTutor = "SELECT 1 FROM tutor_account WHERE accountid = ?";
        $stmtIsTutor = $conn->prepare($sqlIsTutor);
        $stmtIsTutor->execute([$userid]);
        if ($stmtIsTutor->fetch()) {
            $_SESSION['is_tutor'] = true;
            $_SESSION['tutorid'] = $userid;
        } else {
            $_SESSION['is_tutor'] = false;
            unset($_SESSION['tutorid']);
        }
        header("Location: ../student/index.php");
        exit;
    }
}

// Nếu không có user nào trùng hoặc password sai
header("Location: ../login.php?error=Incorrect userid or password");
exit;

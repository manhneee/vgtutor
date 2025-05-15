<?php 

session_start();
if (isset($_POST['userid']) && 
    isset($_POST['password']))
{
    
    include "../DB_connection.php";
    $userid = $_POST['userid'];
    $pass = $_POST['password'];
    $role = $_POST['role'];
    if(empty($userid)) {
        $em = "Email is required";
        header("Location: ../login.php?error=$em");
        exit;
    }
    else if(empty($pass)) {
        $em = "Password is required";
        header("Location: ../login.php?error=$em");
        exit;
    }   
    else {
        $sql = "SELECT * FROM `account` 
                WHERE account.userid = ?";
        
        
        $sqladmincheck = "SELECT userid FROM `account`
                INNER JOIN `admin_account` ON account.userid = admin_account.adminid
                WHERE EXISTS account.userid = ?"; 
        
        // $sqlstudent = "SELECT * FROM `account`
        //         INNER JOIN `student` ON account.userid = student.accountid
        //         WHERE EXISTS account.userid = ?";
        
        if ($sqladmincheck != NULL) {
            $role = 'Admin';
            $sqladmin = "SELECT * FROM `account`
                INNER JOIN `admin_account` ON account.userid = admin_account.adminid
                WHERE account.userid = ?"; 
            $stmt = $conn->prepare($sqladmin);
            $stmt->execute([$userid]);
        }
        if($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            $uid = $user['userid'];
            $password = $user['password'];
            
            if ($uid === $userid) { // Compare $uid with $userid
                if (password_verify($pass, $password)) {
                    $_SESSION['role'] = $role;
                    $_SESSION['name'] = $user['name'];
                    if ($role == 'Admin') {
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

        } else {
            $em = "Incorrect userid or password";
            header("Location: ../login.php?error=$em");
            exit;
        }
    }
} else {
    header("Location: ../login.php");
}

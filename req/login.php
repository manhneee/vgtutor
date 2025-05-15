<?php 

session_start();
if (isset($_POST['email']) && 
    isset($_POST['password']) &&
    isset($_POST['role'])) {
    
    include "../DB_connection.php";
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $role = $_POST['role'];

    

    if(empty($email)) {
        $em = "Email is required";
        header("Location: ../login.php?error=$em");
        exit;
    }
    else if(empty($pass)) {
        $em = "Password is required";
        header("Location: ../login.php?error=$em");
        exit;
    }
    else if(empty($role)) {
        $em = "An error occurred";
        header("Location: ../login.php?error=$em");
        exit;
    } else {
        if ($role == '1') {
            
            $sql = "SELECT * FROM `admin` WHERE email = ?";
            $role = "Admin";
        } else if ($role == '2') {
            $sql = "SELECT * FROM `tutors` WHERE email = ?";
            $role = "Tutor";
        } else {
            $sql = "SELECT * FROM `students` WHERE email = ?";
            $role = "Student";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);

        if($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            $e = $user['email'];
            $password = $user['password'];

            if ($e === $email) {
                if (password_verify($pass, $password)) {
                    $_SESSION['role'] = $role;
                    $_SESSION['fname'] = $user['fname'];
                    $_SESSION['lname'] = $user['lname'];
                    if ($role == 'Admin') {
                        $id = $user['admin_id'];
                        $_SESSION['admin_id'] = $id;
                        header("Location: ../admin/index.php");
                        exit;
                    } 
                    // else if ($role == 'Tutor') {
                    //     header("Location: ../tutor/index.php");
                    // } else {
                    //     header("Location: ../student/index.php");
                    // }
                    
                    
                } else {
                    $em = "Incorrect password";
                    header("Location: ../login.php?error=$em");
                    exit;
                }
            } else {
                $em = "Incorrect email";
                header("Location: ../login.php?error=$em");
                exit;
            }

        } else {
            $em = "Incorrect email or password";
            header("Location: ../login.php?error=$em");
            exit;
        }
            
        
    }
    
} else {
    header("Location: ../login.php");
}
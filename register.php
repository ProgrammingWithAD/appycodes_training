<?php

include 'includes/header.php';
include 'includes/Config.php';
session_start();

// Handle form submission
if (isset($_POST['register_btn'])) {
    $name = mysqli_real_escape_string($config, $_POST['name']);
    $email = mysqli_real_escape_string($config, $_POST['email']);
    $password = mysqli_real_escape_string($config, sha1($_POST['password'])); // Hash the password for storage
    $role = 1; // Assign an integer value for the role

    // Check if the email is already registered
    $check_query = "SELECT * FROM user WHERE email='{$email}'";
    $check_result = mysqli_query($config, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Email already exists";
        header("location:register.php");
        exit();
    }

    // Insert user data into the database
    $insert_query = "INSERT INTO user (username, email, password, role) VALUES ('{$name}', '{$email}', '{$password}', '{$role}')";
    $insert_result = mysqli_query($config, $insert_query);

    if ($insert_result) {
        $_SESSION['success'] = "Registration successful. Please log in.";
        header("location:login.php"); // Redirect to login.php after successful registration
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("location:register.php");
        exit();
    }
}
ob_start();
?>

<div class="container">
    <div class="row">
        <div class="col-xl-5 col-md-4 m-auto p-5 mt-5 bg-info">
            <form action="" method="POST">
                <p class="text-center">Create an Account</p>
                <div class="mb-3">
                    <input type="text" name="name" placeholder="Name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" placeholder="Email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" placeholder="Password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <input type="hidden" name="role" value="1"> <!-- Hidden field for role -->
                </div>
                <div class="mb-3">
                    <input type="submit" name="register_btn" class="btn btn-primary" value="Register">
                </div>
                <?php
                if (isset($_SESSION['error'])) {
                    $error = $_SESSION['error'];
                    echo "<p class='bg-danger p-2 text-white'>" . $error . "</p>";
                    unset($_SESSION['error']);
                }
                ?>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php ob_end_flush(); ?>
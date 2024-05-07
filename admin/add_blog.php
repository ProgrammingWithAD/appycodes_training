<?php
// Start the session
session_start();

// Include header.php which should contain session_start()
include "header.php";

// Initialize $author_id with a default value
$author_id = null;

// Check if the user_data session variable is set and if it contains 'author_id'
if (isset($_SESSION['user_data']['author_id'])) {
	// Assign the 'author_id' from session to $author_id
	$author_id = $_SESSION['user_data']['author_id'];
} else {
	// Handle the case where 'author_id' is not set, you may want to redirect the user to login or set a default value
	// For example:
	// header("Location: login.php");
	// exit;
}

// Perform your database query to fetch categories
$sql = "SELECT * FROM categories";
$query = mysqli_query($config, $sql);
?>

<!-- HTML content continues here... -->
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Add Blog</title>
	<!-- Your CSS and other meta tags -->
</head>

<body>

	<div class="container">
		<h5 class="mb-2 text-gray-800">Blogs</h5>
		<div class="row">
			<div class="col-xl-8 col-lg-6">
				<div class="card">
					<div class="card-header">
						<h6 class="font-weight-bold text-primary mt-2">Publish blog/article</h6>
					</div>
					<div class="card-body">
						<form action="" method="POST" enctype="multipart/form-data">
							<div class="mb-3">
								<input type="text" name="blog_title" placeholder="Title" class="form-control" required>
							</div>
							<div class="mb-3">
								<label>Body/Description</label>
								<textarea required class="form-control" name="blog_body" rows="2" id="blog"></textarea>
							</div>
							<div class="mb-3">
								<input type="file" name="blog_image" class="form-control" required>
							</div>
							<div class="mb-3">
								<select class="form-control" name="category" required>
									<option value="">Select Category</option>
									<?php while ($cats = mysqli_fetch_assoc($query)) { ?>
										<option value="<?= $cats['cat_id'] ?>"><?= $cats['cat_name'] ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="mb-3">
								<input type="submit" name="add_blog" value="Add" class="btn btn-primary">
								<a href="index.php" class="btn btn-secondary">Back</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Include footer.php -->
	<?php include "footer.php"; ?>

	<?php
	// Handle the form submission
	if (isset($_POST['add_blog'])) {
		// Sanitize input data
		$title = mysqli_real_escape_string($config, $_POST['blog_title']);
		$body = mysqli_real_escape_string($config, $_POST['blog_body']);
		$filename = $_FILES['blog_image']['name'];
		$tmp_name = $_FILES['blog_image']['tmp_name'];
		$size = $_FILES['blog_image']['size'];
		$image_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		$allow_type = ['jpg', 'png', 'jpeg'];
		$destination = "upload/" . $filename;
		$category = mysqli_real_escape_string($config, $_POST['category']);

		// Check if 'author_id' is not empty
		if (!empty($author_id)) {
			// Check image file type and size
			if (in_array($image_ext, $allow_type) && $size <= 2000000) {
				// Move uploaded file to destination
				move_uploaded_file($tmp_name, $destination);

				// Insert blog data into database
				$sql2 = "INSERT INTO blog(blog_title, blog_body, blog_image, category, author_id) VALUES ('$title', '$body', '$filename', '$category', '$author_id')";
				$query2 = mysqli_query($config, $sql2);

				// Check if insertion was successful
				if ($query2) {
					$msg = ['Post published successfully', 'alert-success'];
					$_SESSION['msg'] = $msg;
					header("location:add_blog.php");
					exit; // Terminate script after redirection
				} else {
					$msg = ['Failed, please try again', 'alert-danger'];
					$_SESSION['msg'] = $msg;
					header("location:add_blog.php");
					exit; // Terminate script after redirection
				}
			} else {
				$msg = ['Image size should not be greater than 2mb', 'alert-danger'];
				$_SESSION['msg'] = $msg;
				header("location:add_blog.php");
				exit; // Terminate script after redirection
			}
		} else {
			$msg = ['Author ID is empty', 'alert-danger'];
			$_SESSION['msg'] = $msg;
			header("location:add_blog.php");
			exit; // Terminate script after redirection
		}
	}
	?>
</body>

</html>
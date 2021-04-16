<?php
session_start();
?>
<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Task 1 - Register</title>

    <meta charset='UTF-8'>
    <?php include 'style.php'; ?>
</head>

<body>
    <div class='container'>
        <form action="#" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">

                    <h2 class="h2 mb-3 fw-normal">Register</h2>
                    <p>Please fill in this form to create an account.</p>
                    <hr>
                    <div class="form-group">
                        <label for="userid"><b>User Id</b><sup>*</sup></label>
                        <input type="text" class="form-control" placeholder="Enter User Id" name="userid" required>
                    </div>
                    <div class="form-group">
                        <label for="username"><b>User name</b><sup>*</sup></label>
                        <input type="text" class="form-control" placeholder="Enter User Name" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><b>Password</b><sup>*</sup></label>
                        <input type="password" class="form-control" placeholder="Enter Password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="password-repeat"><b>Repeat Password</b><sup>*</sup></label>
                        <input type="password" class="form-control" placeholder="Repeat Password" name="password-repeat" required>
                    </div>
                    <div class="form-group">
                        <label for="imageUpload"><b>Upload Image</b></label>
                        <input type="file" class="form-control" placeholder="Upload Image" name="imageUpload">
                    </div>
                    <div class="clearfix">
                        <button type="reset" class="w-49 btn btn-lg btn-secondary" name='cancelBtn'>Cancel</button>
                        <button type="submit" class="w-49 btn btn-lg btn-primary" name='signupBtn'>Sign Up</button>
                    </div>
                    <div class="form-group">
                        <p>Already have an account? <a href="login.php">Login here</a>.</p>
                    </div>
                </div>
            </div>
        </form>

        <?php
        try {
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signupBtn'])) {
                if (!empty($_POST['userid']) && (!empty($_POST['username'])) && (!empty($_POST['password'])) && (!empty($_POST['password-repeat']))) {
                    $userid = $_POST['userid'];
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $repassword = $_POST['password-repeat'];
                    $image = $_FILES['imageUpload']['name'];
                    if (!isset($_FILES['imageUpload']) || $_FILES['imageUpload']['error'] == UPLOAD_ERR_NO_FILE) {
                        $image = "noimage.png";
                    }

                    if ($password == $repassword) {
                        $query = $datastore->gqlQuery('SELECT * FROM `User` WHERE id = @1 LIMIT 1', [
                            'bindings' => [
                                $userid
                            ],
                            'allowLiterals' => true
                        ]);
                        $results = $datastore->runQuery($query);

                        $match = 0;
                        foreach ($results as $entity) {
                            // echo $entity['id'] . '' . $entity['user_name'];
                            $match = 1;
                        }

                        $query = $datastore->gqlQuery('SELECT * FROM `User` WHERE user_name = @1 LIMIT 1', [
                            'bindings' => [
                                $username
                            ],
                            'allowLiterals' => true
                        ]);
                        $results = $datastore->runQuery($query);

                        // $match = 0;
                        foreach ($results as $entity) {
                            // echo $entity['id'] . '' . $entity['user_name'];
                            $match = 2;
                        }

                        if ($match == 0) {
                            //echo $root_path;
                            //echo "Filename:" . $image;
                            $allowed = array("image/jpeg", "image/png");
                            if (in_array($_FILES['imageUpload']['type'], $allowed)) {
                                //if ($_FILES['imageUpload']['type'] === 'image/jpeg') {
                                $name = 'image/' . $_FILES['imageUpload']['name'];
                                $path_from = $_FILES['imageUpload']['tmp_name'];
                                $file = fopen($path_from, 'r');

                                $object = $bucket->upload($file, [
                                    'name' => $name
                                ]);
                                //printf('Uploaded %s to gs://%s/%s' . PHP_EOL, basename($path_from), $bucketId, $name);
                            }
                            // Create an entity to insert into datastore.
                            $key = $datastore->key('User');
                            $entity = $datastore->entity($key, [
                                'id' => $userid,
                                'password' => $password,
                                'user_name' => $username,
                                'image' => $image,
                            ]);
                            $datastore->insert($entity);

                            //$_SESSION['user_id'] = $userid;
                            // header('Location: login.php?loginId=' . $userid);
                            header('Location: login.php');
                            die();
                        } else {
                            switch ($match) {
                                case 1:
                                    echo "<div class='alert alert-danger' role='alert'><ul><li>Registration Failed! The ID already exists!!!</li></ul></div>";
                                    break;
                                case 2:
                                    echo "<div class='alert alert-danger' role='alert'><ul><li>Registration Failed! The username already exists!!!</li></ul></div>";
                                    break;
                            }
                            // echo "<div class='alert alert-danger' role='alert'><ul><li>Registration Failed! The username already exists!!!</li></ul></div>";
                            # header('Location: register.php');
                        }
                    } else {
                        echo "<div class='alert alert-danger' role='alert'><ul><li>Please enter the same password!!</li></ul></div>";
                    }
                } else {
                    echo "<div class='alert alert-danger' role='alert'><ul><li>Please fill all required fields</li></ul></div>";
                }
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-danger' role='alert'><ul><li>",  $e->getMessage(), "</li></ul></div>";
        }
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>
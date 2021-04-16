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
    <title>Task 1 - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset='UTF-8'>
    <?php include 'style.php'; ?>
</head>

<body>

    <div class='container'>

        <form action="#" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="h2 mb-3 fw-normal">Login</h2>
                    <div class="form-group">
                        <label for="userid"><b>User ID</b><sup>*</sup></label>
                        <input type="text" class="form-control" placeholder="Enter Username" name="userid" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><b>Password</b><sup>*</sup></label>
                        <input type="password" class="form-control" placeholder="Enter Password" name="password" required>
                    </div>
                    <div class="clearfix">
                        <button type="reset" class="w-49 btn btn-lg btn-secondary" name='cancelBtn'>Cancel</button>
                        <button type="submit" class="w-49 btn btn-lg btn-primary" name='loginBtn'>Login</button>
                    </div>
                    <div class="form-group">
                        <p>Don't have an account? <a href="register.php">Register</a>.</p>
                    </div>
                </div>
            </div>
        </form>

        <?php
        try {
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loginBtn'])) {
                if (!empty($_POST['userid']) && (!empty($_POST['password']))) {
                    // echo "Entered:" . $_POST['userid'] . '-' . $_POST['password'];
                    $userid = $_POST['userid'];
                    $password = $_POST['password'];
                    // $key = $datastore->key('User');

                    $userQuery = $datastore->gqlQuery('SELECT * FROM `User` WHERE id = @id and password = @password LIMIT 1', [
                        'bindings' => [
                            'id' => $userid,
                            'password' => $password
                        ],
                        'allowLiterals' => true
                    ]);
                    $userResult = $datastore->runQuery($userQuery);

                    $match = 0;
                    foreach ($userResult as $entity) {
                        echo "Fetched:" . $entity['id'] . '' . $entity['user_name'];
                        $match++;
                    }
                    if ($match > 0) {
                        $_SESSION['user_id'] = $userid;
                        echo "Login Success";
                        header('Location: index.php?loginId=' . $userid);
                        die();
                    } else {
                        echo "<div class='alert alert-danger' role='alert'><ul><li>ID or password is invalid</li></ul></div>";
                        # header('Location: login.php');
                    }
                } else {
                    echo "<div class='alert alert-danger' role='alert'><ul><li>Please enter Username or Password</li></ul></div>";
                }
            } else {
                if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                    echo "Already logged in";
                    // header('Location: index.php?loginId=' . $_SESSION['user_id']);
                    // die();
                } else {
                    echo "Not logged in";
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
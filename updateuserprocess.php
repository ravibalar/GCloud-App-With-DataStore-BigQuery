<?php

session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateUserBtn'])) {
    if ((!empty($_POST['username'])) && (!empty($_POST['old_password'])) && (!empty($_POST['password'])) && (!empty($_POST['password-repeat']))) {
        $userid = $_POST['userid'];
        $username = $_POST['username'];
        $old_password = $_POST['old_password'];
        $password = $_POST['password'];
        $repassword = $_POST['password-repeat'];
        try {
            // Create an entity to insert into datastore.
            echo "Check type:" . is_int($_POST['id']);
            echo "Type Change:" . intval($_POST['id']);
            $key = $datastore->key('User', intval($_POST['id']));
            echo "Before change";

            //$datastore->delete($key);

            $entity = $datastore->lookup($key);
            $image = $entity['image'];
            print_r($entity);
            //$entity['user_name'] = $username;
            //$entity['password'] = $password;
            if ($password == $repassword) {
                if ($entity['password'] == $old_password) {
                    $entity = $datastore->entity($key, [
                        'id' => $userid,
                        'user_name' => $username,
                        'password' => $password,
                        'image' => $image,
                    ]);
                    echo "After change";
                    print_r($entity);
                    $datastore->update($entity, ['allowOverwrite' => true]);

                    echo "<div class='alert alert-success' role='alert'><ul><li>Password successfully updated!!!!!</li></ul></div>";
                    $_SESSION['user_id'] = null;
                    unset($_SESSION['user_id']);
                    header("location:login.php"); //?loginId=" . $_POST['loginId'] . "&id=" . $_POST['id'] . "&success=1");
                    die();
                } else {
                    echo "<div class='alert alert-danger' role='alert'><ul><li>The old password is incorrect</li></ul></div>";
                    header("location:updateuser.php?loginId=" . $_POST['loginId'] . "&id=" . $_POST['id'] . "&success=2");
                    die();
                }
            } else {
                echo "<div class='alert alert-danger' role='alert'><ul><li>Please enter same new and repeat password</li></ul></div>";
                header("location:updateuser.php?loginId=" . $_POST['loginId'] . "&id=" . $_POST['id'] . "&success=3");
                die();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    } else {
        echo "<div class='alert alert-danger' role='alert'><ul><li>Please fill all required fields after submission</li></ul></div>";
        header("location:updateuser.php?loginId=" . $_POST['loginId'] . "&id=" . $_POST['id'] . "&success=0");
        die();
    }
}

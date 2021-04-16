<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateMessageBtn'])) {
    if ((!empty($_POST['subject']))) {
        try {
            $messageid = $_POST['messageid'];
            $subject = $_POST['subject'];
            $message = $_POST['message'];
            $image = $_POST['image'];

            //echo $root_path;
            // echo "Filename:" . $image;
            $allowed = array("image/jpeg", "image/png");
            if (!file_exists($_FILES['imageUpload']['tmp_name']) || !is_uploaded_file($_FILES['imageUpload']['tmp_name'])) {
                echo 'No upload';
            } else {
                $image = $_FILES['imageUpload']['name'];
                if (in_array($_FILES['imageUpload']['type'], $allowed)) {
                    //if ($_FILES['imageUpload']['type'] === 'image/jpeg') {
                    $name = 'forum/' . $_FILES['imageUpload']['name'];
                    $path_from = $_FILES['imageUpload']['tmp_name'];
                    $file = fopen($path_from, 'r');

                    $object = $bucket->upload($file, [
                        'name' => $name
                    ]);
                    printf('Uploaded %s to gs://%s/%s' . PHP_EOL, basename($path_from), $bucketId, $name);
                }
            }

            $key = $datastore->key('Message', intval($_POST['id']));

            // $entity = $datastore->lookup($key);
            echo "before change:";
            //$datastore->delete($key);
            print_r($entity);

            $entity = $datastore->entity($key, [
                'message_id' => $messageid,
                'subject' => $subject,
                'message' => $message,
                'image' => $image,
                'posttime' => date("d/m/Y h:i:sa"),
                'creator_id' => $_POST['loginId'],
            ]);
            echo "After change:";
            print_r($entity);
            $datastore->update($entity, ['allowOverwrite' => true]);

            echo "<div class='alert alert-success' role='alert'><ul><li>Post successfully updated!!!!!</li></ul></div>";
            header("location:index.php?loginId=" . $_POST['loginId'] . "&success=1"); //. "&id=" . $_POST['id'] . "&success=1");
            die();
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    } else {
        echo "<div class='alert alert-danger' role='alert'><ul><li>Please fill all required fields after submission</li></ul></div>";
        header("location:updatemessage.php?loginId=" . $_POST['loginId'] . "&id=" . $_POST['id'] . "&success=0");
        die();
    }
}

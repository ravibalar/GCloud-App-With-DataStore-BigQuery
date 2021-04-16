<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addMessageBtn'])) {
    if (!empty($_POST['subject'])) {
        try {
            $subject = $_POST['subject'];
            $message = $_POST['message'];
            $image = $_FILES['imageUpload']['name'];
            if (!isset($_FILES['imageUpload']) || $_FILES['imageUpload']['error'] == UPLOAD_ERR_NO_FILE) {
                $image = "noimage.png";
            }

            //echo $root_path;
            //echo "Filename:" . $image;
            $allowed = array("image/jpeg", "image/png");
            if (in_array($_FILES['imageUpload']['type'], $allowed)) {
                //if ($_FILES['imageUpload']['type'] === 'image/jpeg') {
                $name = 'forum/' . $_FILES['imageUpload']['name'];
                $path_from = $_FILES['imageUpload']['tmp_name'];
                $file = fopen($path_from, 'r');

                $object = $bucket->upload($file, [
                    'name' => $name
                ]);
                //printf('Uploaded %s to gs://%s/%s' . PHP_EOL, basename($path_from), $bucketId, $name);
            }

            // Create an entity to insert into datastore.
            $key = $datastore->key('Message');
            $entity = $datastore->entity($key, [
                'message_id' => 'M-' . $_SESSION['user_id'],
                'subject' => $subject,
                'message' => $message,
                'image' => $image,
                'posttime' => date("d/m/Y h:i:sa"),
                'creator_id' => $_SESSION['user_id'],
            ]);
            // echo "New entity:";
            // print_r($entity);
            $datastore->insert($entity);

            //}
            echo "<div class='alert alert-success' role='alert'><ul><li>Post successfully added!!!!!</li></ul></div>";
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    } else {
        echo "<div class='alert alert-danger' role='alert'><ul><li>Please fill all required fields</li></ul></div>";
    }
}
?>
<form action="#" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">

            <h2 class="h2 mb-3 fw-normal">Add Post</h2>
            <hr>
            <div class="form-group">
                <label for="subject"><b>Subject</b><sup>*</sup></label>
                <input type="text" class="form-control" placeholder="Enter subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="message"><b>Message</b></label>
                <!-- <input type="text" class="form-control" placeholder="Enter message" name="message" required> -->
                <textarea type="text" class="form-control" placeholder="Enter message" name="message" rows="4" cols="50"></textarea>
            </div>

            <div class="form-group">
                <label for="imageUpload"><b>Upload Image</b></label>
                <input type="file" class="form-control" placeholder="Upload Image" name="imageUpload">
            </div>
            <div class="clearfix">
                <button type="reset" class="w-49 btn btn-lg btn-secondary" name='cancelBtn'>Cancel</button>
                <button type="submit" class="w-49 btn btn-lg btn-primary" name='addMessageBtn'>Add Message</button>
            </div>
        </div>
    </div>
</form>
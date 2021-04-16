<?php
session_start();
?>
<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
if (!isset($_GET['loginId']) || empty($_GET['loginId'])) {
    echo "User id is not set in SESSION";
    //echo $_SESSION['user_id'];
    header('Location: login.php');
    die();
} else {
    $_SESSION['user_id'] = $_GET['loginId'];
    //echo "User id is set in SESSION:" . $_SESSION['user_id'];
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Task 1 - Update Post</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset='UTF-8'>
    <?php include 'style.php'; ?>
</head>

<body>
    <div id='header'>
        <?php include 'menu.php'; ?>
    </div>

    <div class='container'>
        <div class="h3 header text-center">Task 1 - Update Post</div>
        <div class="row profile">
            <div class="user-info col-md-3" align="center">
                <?php include 'userinfo.php'; ?>
            </div>
            <div class="message-info col-md-9">
                <?php
                if (isset($_GET['success'])) {
                    switch ($_GET['success']) {
                        case 0:
                            echo "<div class='alert alert-danger' role='alert'><ul><li>Please fill all required fields after submission</li></ul></div>";
                            break;
                        case 1:
                            echo "<div class='alert alert-success' role='alert'><ul><li>Post successfully updated!!!!!</li></ul></div>";
                            break;
                    }
                }
                ?>
                <?php
                if (isset($_GET['id'])) {
                    //echo "Current message key:" . $_GET['id'];
                    try {
                        $messageKey = $datastore->key('Message', $_GET['id']);
                        $query = $datastore->gqlQuery('SELECT * FROM `Message` WHERE __key__ = @1 LIMIT 1', [
                            'bindings' => [
                                $messageKey
                            ],
                            'allowLiterals' => true
                        ]);
                        $messageResult = $datastore->runQuery($query);

                        // $messageKey = $datastore->key('Message', $_GET['id']);
                        // $transaction = $datastore->transaction();
                        // $messageResult = $transaction->lookup($messageKey);

                        foreach ($messageResult as $entity) {
                            $message_key = $entity->key()->pathEndIdentifier();
                            $message_id = $entity['message_id'];
                            $subject = $entity['subject'];
                            $message = $entity['message'];
                            $imageName = $entity['image'];
                            $imageURL =  "<img class='avatar pull-left img-responsive thumb margin10 img-thumbnail' src='https://storage.googleapis.com/${bucketId}/forum/${imageName}' />";
                        }
                    } catch (Exception $e) {
                        echo "<div class='alert alert-danger' role='alert'><ul><li>",  $e->getMessage(), "</li></ul></div>";
                    }
                ?>
                    <div class="message-content">
                        <form action="updatemessageprocess.php" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">

                                    <h2 class="h2 mb-3 fw-normal">Update Post</h2>
                                    <hr>
                                    <input type="hidden" id="id" name="id" value="<?= $message_key ?>">

                                    <input type="hidden" id="loginId" name="loginId" value="<?= $_SESSION['user_id'] ?>">

                                    <input type="hidden" id="messageid" name="messageid" value="<?= $message_id ?>">

                                    <input type="hidden" id="image" name="image" value="<?= $imageName ?>">

                                    <div class="form-group">
                                        <label for="subject"><b>New Subject</b><sup>*</sup></label>
                                        <input type="text" class="form-control" placeholder="Enter subject" name="subject" value="<?= $subject ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="message"><b>New Message</b></label>
                                        <textarea type="text" class="form-control" placeholder="Enter message" name="message" rows="4" cols="50"><?= $message ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="imageUpload"><b>Upload New Image</b></label>
                                        <div><?= $imageURL ?></div>
                                        <input type="file" class="form-control" placeholder="Upload Image" name="imageUpload">
                                    </div>
                                    <div class="clearfix">
                                        <button type="reset" class="w-49 btn btn-lg btn-secondary" name='cancelBtn'>Cancel</button>
                                        <button type="submit" class="w-49 btn btn-lg btn-primary" name='updateMessageBtn'>Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php } else {
                    echo "Not set!!!";
                } ?>
                    </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>
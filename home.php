<?php
session_start();
?>
<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
if (!isset($_GET['loginId']) || empty($_GET['loginId'])) {
    //echo "User id is not set in SESSION";
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
    <title>Task 1 - User area</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset='UTF-8'>
    <?php include 'style.php'; ?>
</head>

<body>
    <div id='header'>
        <?php include 'menu.php'; ?>
    </div>

    <div class='container'>
        <div class="h3 header text-center">Task 1 - User area</div>
        <div class="row profile">
            <div class="user-info col-md-3" align="center">
                <?php include 'userinfo.php'; ?>
            </div>

            <div class="message-info col-md-9">
                <div class="message-content">
                    <?php include 'addmessage.php' ?>
                </div>

                <div class="message-content">
                    <div class="h3 header text-center">User Forum</div>
                    <?php
                    try {
                        $messageQuery = $datastore->gqlQuery('SELECT * FROM `Message` WHERE creator_id = @creator ORDER BY posttime DESC LIMIT 10', [
                            'bindings' => [
                                'creator' => $_SESSION['user_id']
                            ],
                            'allowLiterals' => true
                        ]);
                        $messageResult = $datastore->runQuery($messageQuery);

                        $message = [];

                        $messageStr = "<div class='row mb-2'>";

                        foreach ($messageResult as $entity) {
                            // $message[] = sprintf(
                            //     'Message ID: %s Creator: %s',
                            //     $entity['message_id'],
                            //     $entity['creator_id']
                            // );
                            $userQuery = $datastore->gqlQuery('SELECT * FROM `User` WHERE id = @1 LIMIT 1', [
                                'bindings' => [
                                    $entity['creator_id']
                                ],
                                'allowLiterals' => true
                            ]);
                            $userResult = $datastore->runQuery($userQuery);


                            foreach ($userResult as $userEntity) {
                                $user_name = $userEntity['user_name'];
                                $userImageName = $userEntity['image'];
                                $userImageURL =  "<img class='img-responsive' src='https://storage.googleapis.com/${bucketId}/image/${userImageName}'  alt='' />";
                            }
                            $imageName = $entity['image'];
                            $imageURL =  "<img class='avatar pull-left img-responsive thumb margin10 img-thumbnail' src='https://storage.googleapis.com/${bucketId}/forum/${imageName}' />";

                            $messageStr .= "<div class='col-md-6 card border-0'>";
                            $messageStr .= "<div class='row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative'>";
                            $messageStr .= "<div class='col p-2 d-flex flex-column position-static'>";
                            // $messageStr .= "<strong class='d-inline-block mb-2 text-primary'>World</strong>";
                            $messageStr .= "<h3 class='card-header mb-0'>" . $entity['subject'] . "</h3>";
                            // $messageStr .= "<hr>";
                            $messageStr .= "<div class='col-auto d-none d-lg-block mt-1'>";
                            $messageStr .= "<div class='bd-placeholder-img text-center' width='200' height='250'>" . $imageURL . "</div>";
                            $messageStr .= "</div>";
                            $messageStr .= "<div class='card-body mb-auto'>" . $entity['message'] . "</div>";
                            // $messageStr .= "<hr>";
                            $messageStr .= "<div class='card-footer'>";
                            $messageStr .= "<div class='row'>";
                            $messageStr .= "<div class='col-md-4'><div class='small-profile'>" . $userImageURL . "</div></div>";
                            $messageStr .= "<div class='col-md-8'><div class='mb-1 text-muted'>Posted by: " . $user_name . " on " . $entity['posttime'] . "</div></div>";
                            $messageStr .= "<div class='col-md-12 mt-1 border-top'><a href='updatemessage.php?loginId=" . $_SESSION['user_id'] . "&id=" . $entity->key()->pathEndIdentifier()  . "' class='card-link'>Edit</a></div>";
                            $messageStr .= "</div>";
                            $messageStr .= "</div>";
                            $messageStr .= "</div>";
                            $messageStr .= "</div>";
                            $messageStr .= "</div>";
                        }
                        $messageStr .= '</div>';

                        echo $messageStr;
                    } catch (Exception $e) {
                        echo "<div class='alert alert-danger' role='alert'><ul><li>",  $e->getMessage(), "</li></ul></div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>
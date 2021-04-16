<?php
try {
    $userQuery = $datastore->gqlQuery('SELECT * FROM `User` WHERE id = @1 LIMIT 1', [
        'bindings' => [
            $_SESSION['user_id']
        ],
        'allowLiterals' => true
    ]);
    $userResult = $datastore->runQuery($userQuery);

    // $userQuery = $datastore->query()
    //     ->kind('User')
    //     ->filter('id', '=', $_SESSION['user_id'])
    //     ->order('id', 'DESCENDING')
    //     ->limit(1);
    // $userResult = $datastore->runQuery($userQuery);

    $userStr = "";
    $i = 0;
    foreach ($userResult as $entity) {
        $user_key = $entity->key()->pathEndIdentifier();
        $user_id = $entity['id'];
        $user_name = $entity['user_name'];
        $imageName = $entity['image'];
        $imageURL =  "<img class='img-responsive' src='https://storage.googleapis.com/${bucketId}/image/${imageName}' alt=''>";
    }
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
?>
<div class="profile-sidebar">
    <div class='profile-userpic'>
        <?= $imageURL ?>
    </div>
    <div class='profile-usertitle'>
        <div class='profile-usertitle-name'>
            <?= $user_name ?>(<a href="home.php?loginId=<?= $user_id ?>"><?= $user_id ?></a>)
        </div>
    </div>
    <div class='profile-usermenu'>
        <ul class='nav'>
            <li>
                <a href='updateuser.php?loginId=<?= $_SESSION["user_id"] ?>&id=<?= $user_key ?>'><i class='glyphicon glyphicon-user'></i>Account Settings </a>
            </li>
        </ul>
    </div>
</div>
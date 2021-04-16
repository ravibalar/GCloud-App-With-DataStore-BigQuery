<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="navbar-brand" href="#">GCloud App With DataStore & BigQuery</div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) { ?>
                <li class="nav-item active">
                    <a class="nav-link" href="index.php?loginId=<?= $_SESSION['user_id'] ?>">Task 1 <span class="sr-only">(current)</span></a>
                </li>
            <?php } ?>
            <!-- <li class="nav-item active">
                <a class="nav-link" href="addmessage.php">Add Message</a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link" href="query21.php">Query 2.1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="query22.php">Query 2.2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="query23.php">Query 2.3</a>
            </li>
            <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            <?php } ?>
        </ul>
    </div>
</nav>
<?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) { ?>
    <div class="login_user"> Welcome, <a href="home.php?loginId=<?= $_SESSION['user_id'] ?>"><?php echo $_SESSION['user_id']; ?></a></div>
<?php } ?>
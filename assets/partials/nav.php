<?php 

if (isset($_SESSION['user_id'])) {
    $userID = $_SESSION['user_id'];
}

if (isset($_SESSION['first_name'])) {
    $userFname = $_SESSION['first_name'];
}

?>
<div class="container-fluid">
    <nav role="navigation" class="row justify-content-md-center navbar navbar-expand-md navbar-light bg-faded" <?php if (!isset($_SESSION['group_id'])) { echo "style='height: 72px'";} ?>>
        <?php if (isset($_SESSION['group_id'])): ?>
            <div class="nav-inner ">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
            </span>
        </button>
            <span>Menu</span>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chores.php">Chores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="items.php">Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="visitors.php">Visitors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="groups.php">Groups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="preferences.php">Preferences</a>
                    </li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        <div id="userNameHeader">
            <h2 class="userNameTitle-<?php echo $userID?>" id="userFname">Hi, <strong><?php echo $userFname; ?></strong><a href="logout.php">Log Out</a></h2>
        </div>
    </nav>
</div>

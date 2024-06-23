<?php
session_start();
require 'db.php';

$is_logged_in = isset($_SESSION['user_id']);
$username = '';

if ($is_logged_in) {
    $stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $username = $user['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eWallet</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">eWallet</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">
                            Strona główna
                            
                        </a>
                    </li>
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Wyloguj</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Zaloguj</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="add_user.php">Dodaj użytkownika</a>
                    </li>
                    <?php endif; ?>  
                </ul>
                    <p>
                            <?php if ($is_logged_in): ?>
                                Witaj <?php echo htmlspecialchars($username); ?>
                            <?php endif; ?>
                            </p>
                    
            </div>
        </nav>
        <div class="alert alert-info" role="alert">
            Witamy w twoim eWallet!
        </div>

        <?php
        $accountBallance = 1000;
        echo '<div class="alert alert-primary">Początkowe saldo konta: '.$accountBallance.' PLN</div>';
        
        $transfersarray = array(1 => 100, 300, -1100, -1000);
        $transferBallance = 0;

        echo '<ul class="list-group">';
        foreach ($transfersarray as $transfer) {
            if ($transfer > 0) {
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">Przychodzący przelew: '.$transfer.' PLN <span class="badge badge-success">IN</span></li>';
            } else {
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">Wychodzący przelew: '.$transfer.' PLN <span class="badge badge-danger">OUT</span></li>';
            }
            $accountBallance += $transfer;
            $transferBallance += $transfer;
        }
        echo '</ul>';

        echo '<div class="alert alert-primary">Końcowe saldo konta: '.$accountBallance.' PLN</div>';

        if ($accountBallance < 0) {
            echo '<div class="alert alert-warning">Jesteś na debecie!</div>';
        }
        if ($transferBallance < 0) {
            echo '<div class="alert alert-danger">Masz niewystarczającą ilość środków!</div>';
        }
        ?>
    </div>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>

<?php
session_start();
require 'db.php';

$is_logged_in = isset($_SESSION['user_id']);
$username = '';
$accountBalance = 0;
$transfers = [];

if ($is_logged_in) {
    $stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $username = $user['username'];

    $stmt = $pdo->prepare('SELECT funts FROM finances WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $finance = $stmt->fetch();
    $accountBalance = $finance['funts'];

	$stmt = $pdo->prepare('SELECT amount FROM transfers WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $transfers = $stmt->fetchAll();
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
                            <a class="nav-link" href="transfer.php">Dodaj transakcję</a>
                        </li>
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

        <?php if ($is_logged_in): ?>
            <div class="alert alert-primary" role="alert">
                Stan konta: <?php echo htmlspecialchars($accountBalance); ?> PLN
            </div>
        <?php endif; ?>
            
             <?php if ($is_logged_in): ?>
            <ul class="list-group">
                <?php foreach ($transfers as $transfer): ?>
                    <?php if ($transfer['amount'] > 0): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Przychodzący przelew: <?php echo $transfer['amount']; ?> PLN 
                            <span class="badge badge-success">IN</span>
                        </li>
                    <?php else: ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Wychodzący przelew: <?php echo $transfer['amount']; ?> PLN 
                            <span class="badge badge-danger">OUT</span>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>

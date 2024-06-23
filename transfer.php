<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error_message = '';
$current_balance = 0;

// Pobierz nazwę użytkownika
$stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'];

$is_logged_in = isset($_SESSION['user_id']);

// Sprawdzenie aktualnego stanu konta
$stmt = $pdo->prepare('SELECT funts FROM finances WHERE user_id = ?');
$stmt->execute([$user_id]);
$finance = $stmt->fetch();
$current_balance = $finance['funts'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];

    if ($amount < 0 && $current_balance + $amount < 0) {
        $error_message = 'Niewystarczająca ilość środków.';
    } else {
        try {
            // Rozpoczęcie transakcji
            $pdo->beginTransaction();

            // Dodanie transferu do tabeli transfers
            $stmt = $pdo->prepare('INSERT INTO transfers (user_id, amount) VALUES (?, ?)');
            $stmt->execute([$user_id, $amount]);

            // Aktualizacja stanu konta
            $stmt = $pdo->prepare('UPDATE finances SET funts = funts + ? WHERE user_id = ?');
            $stmt->execute([$amount, $user_id]);

            // Zatwierdzenie transakcji
            $pdo->commit();

            header('Location: index.php');
            exit();
        } catch (Exception $e) {
            // Cofnięcie transakcji w przypadku błędu
            $pdo->rollBack();
            $error_message = 'Błąd podczas przetwarzania transakcji: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Transfer</title>
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

        <?php if ($is_logged_in): ?>
            <div class="alert alert-primary" role="alert">
                Stan konta: <?php echo htmlspecialchars($current_balance)?> PLN
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-body">
                        <h5 class="card-title">Dodaj transakcję</h5>
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <form action="transfer.php" method="POST">
                            <div class="form-group">
                                <label for="amount">Kwota (przelew wychodzący < 0 | przelew przychodzący > 0)</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Dodaj</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>

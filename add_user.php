<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj użytkownika</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">eWallet</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Strona główna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Zaloguj</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="add_user.php">Dodaj użytkownika</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-body">
                        <h5 class="card-title">Dodaj użytkownika</h5>
                        <form action="add_user.php" method="POST">
                            <div class="form-group">
                                <label for="username">Nazwa użytkownika</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Hasło</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Dodaj</button>
                        </form>
                        <?php
                        require 'db.php';

                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $username = $_POST['username'];
                            $password = $_POST['password'];
                            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                            $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                            if ($stmt->execute([$username, $passwordHash])) {
                                echo '<div class="alert alert-success mt-3">Użytkownik dodany!</div>';
                            } else {
                                echo '<div class="alert alert-danger mt-3">Nastąpił błąd, spróbuj ponownie później!</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
// End of POST handling
}

// Set message and user with explicit checks
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
} else {
    $message = '';
}

if (isLoggedIn()) {
    $user = getUser($conn, $_SESSION['user_id']);
} else {
    $user = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chama Savings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .navbar { background: linear-gradient(90deg, #28a745, #dc3545); }
        .card { transition: transform 0.3s; }
        .card:hover { transform: scale(1.02); }
        .btn-custom { background: linear-gradient(90deg, #28a745, #dc3545); color: white; }
        .btn-custom:hover { background: linear-gradient(90deg, #218838, #c82333); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark p-3">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Chama Savings</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item"><a class="nav-link" href="?view=members">Members</a></li>
                        <li class="nav-item"><a class="nav-link" href="?view=contributions">Contributions</a></li>
                        <li class="nav-item"><a class="nav-link" href="?view=groups">Groups</a></li>
                        <?php if ($user && $conn->query("SELECT id FROM users WHERE id = $user[id] AND group_id IS NULL")->num_rows > 0): ?>
                            <li class="nav-item"><a class="nav-link" href="?view=create_group">Create Group</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="?logout=1">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="?view=login">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="?view=register">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?php if ($message): ?>
        <div class="container mt-4">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <div class="container mt-5">
        <?php
        $view = isset($_GET['view']) ? $_GET['view'] : 'home';
        if ($view == 'home' && isLoggedIn()) {
            $group = $conn->query("SELECT * FROM groups WHERE id = $user[group_id]")->fetch_assoc();
            if ($group): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title">Your Group: <?php echo htmlspecialchars($group['name']); ?></h2>
                        <p class="card-text">Manage your group's members and contributions.</p>
                        <a href="?view=members" class="btn btn-custom">View Members</a>
                        <a href="?view=contributions" class="btn btn-custom">View Contributions</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title">No Group Yet</h2>
                        <p class="card-text">Create or join a group to get started.</p>
                        <a href="?view=create_group" class="btn btn-custom">Create Group</a>
                    </div>
                </div>
            <?php endif;
        } elseif ($view == 'login' && !isLoggedIn()): ?>
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Login</h2>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-custom">Login</button>
                    </form>
                </div>
            </div>
        <?php elseif ($view == 'register' && !isLoggedIn()): ?>
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Register</h2>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-custom">Register</button>
                    </form>
                </div>
            </div>
        <?php elseif ($view == 'create_group' && isLoggedIn() && !$user['group_id']): ?>
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Create Group</h2>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Group Name</label>
                            <input type="text" name="group_name" class="form-control" required>
                        </div>
                        <button type="submit" name="create_group" class="btn btn-custom">Create</button>
                    </form>
                </div>
            </div>
        <?php elseif ($view == 'members' && isLoggedIn()): ?>
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Members</h2>
                    <form method="POST" class="mb-4">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <input type="hidden" name="group_id" value="<?php echo $user['group_id']; ?>">
                        <button type="submit" name="add_member" class="btn btn-custom">Add Member</button>
                    </form>
                    <ul class="list-group">
                        <?php
                        $members = $conn->query("SELECT * FROM users WHERE group_id = $user[group_id]");
                        while ($member = $members->fetch_assoc()): ?>
                            <li class="list-group-item"><?php echo htmlspecialchars($member['username']); ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        <?php elseif ($view == 'contributions' && isLoggedIn()): ?>
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Contributions</h2>
                    <ul class="list-group">
                        <?php
                        $contributions = $conn->query("SELECT u.username, c.amount, c.date_added FROM contributions c JOIN users u ON c.user_id = u.id WHERE c.group_id = $user[group_id]");
                        while ($contribution = $contributions->fetch_assoc()): ?>
                            <li class="list-group-item"><?php echo htmlspecialchars($contribution['username']); ?>: <?php echo $contribution['amount']; ?> on <?php echo $contribution['date_added']; ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
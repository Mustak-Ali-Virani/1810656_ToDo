<!-- php -->
<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "list";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task = $_POST["task"];
    if (empty($task)) {
        $error_message = "Task name cannot be empty.";
    } else {
        $sql = "INSERT INTO tasks (task, is_favorite) VALUES ('$task', 0)";
        $conn->query($sql);
        header("Location: tasks.php");
        exit();
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM tasks WHERE id = $id";
    $conn->query($sql);
    header("Location: tasks.php");
    exit();
}

if (isset($_GET['favorite'])) {
    $id = $_GET['favorite'];
    $sql = "UPDATE tasks SET is_favorite = NOT is_favorite WHERE id = $id";
    $conn->query($sql);
    header("Location: tasks.php");
    exit();
}

$sql = "SELECT * FROM tasks";
$result = $conn->query($sql);

$tasks = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}


usort($tasks, function($a, $b) {
    return $b['is_favorite'] - $a['is_favorite'];
});



$conn->close();

?>
<!-- html/css -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
	<link rel="stylesheet" href="style.css"> 

</head>
<body>
    <div class="container">
        <h1 class="text-center mb-5">To-Do List</h1>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="form-group">
                <input type="text" class="form-control" name="task" placeholder="New Task">
            </div>
           <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i></button>

        </form>
<ul class="list-group mt-3">
    <?php foreach ($tasks as $task): ?>
        <?php if ($task['is_favorite']): ?>
           <li class="list-group-item d-flex justify-content-between align-items-center">
    <div>
        <span style="color: blue; margin-right: 10px;">★</span>
        <?php echo $task["task"]; ?>
    </div>
    <div>
        <a href="?delete=<?php echo $task['id']; ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a> <!-- Updated Delete button -->
        <a href="?favorite=<?php echo $task['id']; ?>" class="btn btn-sm" style="background-color: darkblue;"><i class="fas fa-star" style="color: white; font-size: 18px;"></i></a> <!-- Updated Favorite button -->
    </div>
</li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

<ul class="list-group mt-3">
    <?php foreach ($tasks as $task): ?>
        <?php if (!$task['is_favorite']): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <?php echo $task["task"]; ?>
                </div>
                <div>
                    <a href="?delete=<?php echo $task['id']; ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                    <a href="?favorite=<?php echo $task['id']; ?>" class="btn btn-sm" style="background-color: darkblue;"><i class="fas fa-star" style="color: white; font-size: 18px;"></i></a>
                </div>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

    </div>
</body>
</html>


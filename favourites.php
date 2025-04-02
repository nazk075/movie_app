<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    die("User not logged in. Please <a href='login.php'>login</a>.");
}

include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "add_movie") {
    $title = $_POST["title"];
    $year = $_POST["year"];
    $imdbRating = $_POST["imdbRating"];
    $genre = $_POST["genre"];
    $director = $_POST["director"];
    $poster = $_POST["poster"];

    $checkQuery = "SELECT * FROM moviez WHERE title = ? AND year = ? AND user_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ssi", $title, $year, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) { 
        $query = "INSERT INTO moviez (title, year, imdb_rating, genre, director, poster, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $title, $year, $imdbRating, $genre, $director, $poster, $_SESSION['user_id']);
        $stmt->execute();
        echo "Movie added successfully!";
        exit();
    } else {
        echo "Movie already exists!";
        exit();
    }
}

$stmt = $conn->prepare("SELECT * FROM moviez WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favourite Movies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
    <h2 class="text-center">Your Favourite Movies</h2>
    <h3 class="text-center">Welcome, <?php echo $_SESSION['username']; ?>!</h3>


        <a href="index.php" class="btn btn-primary mb-3">Back to Search</a>
        <a href="logout.php" class="btn btn-danger mb-3">Logout</a>

        
       
        
        <div class="row" id="moviesList">
            <?php while ($movie = $result->fetch_assoc()): ?>
                <div class="col-md-4 movie-item">
                    <div class="card mb-4">
                        <img src="<?= $movie['poster'] ?>" class="card-img-top" alt="Movie Poster">
                        <div class="card-body">
                            <h5 class="card-title"><?= $movie['title'] ?> (<?= $movie['year'] ?>)</h5>
                            <p class="card-text"><strong>IMDb Rating:</strong> <?= $movie['imdb_rating'] ?></p>
                            <p class="card-text"><strong>Genre:</strong> <?= $movie['genre'] ?></p>
                            <p class="card-text"><strong>Director:</strong> <?= $movie['director'] ?></p>
                            <button class="btn btn-danger delete-movie" data-id="<?= $movie['id'] ?>">Delete</button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            
            $("#addMovieForm").submit(function (e) {
                e.preventDefault();
                var formData = $(this).serialize() + "&action=add_movie";

                $.ajax({
                    type: "POST",
                    url: "favourites.php", 
                    data: formData,
                    success: function (response) {
                        $("#message").text(response);
                        location.reload(); 
                    }
                });
            });

            
            $(".delete-movie").click(function () {
                let movieId = $(this).data("id");
                if (confirm("Are you sure you want to delete this movie?")) {
                    window.location.href = "delete.php?id=" + movieId;
                }
            });
        });
    </script>
</body>
</html>

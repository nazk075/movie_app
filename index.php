<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$apiKey = "7fe835ce"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div style="text-align: right; padding: 10px;">
    <a href="favourites.php" class="btn btn-primary">My Favourites</a>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>

<div class="container">
    <div class="card shadow p-4">
        <h2 class="text-center">Search for a Movie</h2>
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="movie" class="form-control" placeholder="Enter movie title" required>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <?php 
        if (isset($_GET['movie'])) {
            $movieTitle = urlencode($_GET['movie']);
            $url = "https://www.omdbapi.com/?apikey=$apiKey&t=$movieTitle";
            $response = file_get_contents($url);
            $movie = json_decode($response, true);

            if ($movie && $movie["Response"] == "True") {
                $title = htmlspecialchars($movie['Title']);
                $year = htmlspecialchars($movie['Year']);
                $imdbRating = htmlspecialchars($movie['imdbRating']);
                $genre = htmlspecialchars($movie['Genre']);
                $director = htmlspecialchars($movie['Director']);
                $poster = htmlspecialchars($movie['Poster']);
                $plot = htmlspecialchars($movie['Plot']);
        ?>

        <div class="movie-card mt-4 p-3 shadow">
            <div class="row">
                <div class="col-md-4">
                    <img src="<?= $poster ?>" class="img-fluid" alt="Movie Poster">
                </div>
                <div class="col-md-8">
                    <h5><?= $title ?> (<?= $year ?>)</h5>
                    <p><strong>IMDb Rating:</strong> <?= $imdbRating ?></p>
                    <p><strong>Genre:</strong> <?= $genre ?></p>
                    <p><strong>Director:</strong> <?= $director ?></p>
                    <p><strong>Plot:</strong> <?= $plot ?></p>

                    
                    <form id="addMovieForm">
                        <input type="hidden" name="title" value="<?= $title ?>">
                        <input type="hidden" name="year" value="<?= $year ?>">
                        <input type="hidden" name="imdbRating" value="<?= $imdbRating ?>">
                        <input type="hidden" name="genre" value="<?= $genre ?>">
                        <input type="hidden" name="director" value="<?= $director ?>">
                        <input type="hidden" name="poster" value="<?= $poster ?>">
                        <input type="hidden" name="action" value="add_movie">
                        <button type="submit" class="btn btn-success mt-2">Save to Favourites</button>
                    </form>

                    
                    <p id="message" class="mt-2 text-success" style="display:none;">Movie saved successfully!</p>
                </div>
            </div>
        </div>

        <?php
            } else {
                echo '<p class="text-danger mt-3">Movie not found.</p>';
            }
        }
        ?>
    </div>
</div>

<script>
$(document).ready(function () {
    $("#addMovieForm").submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: "POST",
            url: "favourites.php", 
            data: formData,
            success: function (response) {
                if (response.trim() === "Movie added successfully!") {
                    $("#message").show();
                    setTimeout(function () {
                        $("#message").fadeOut();
                    }, 3000);
                } else {
                    $("#message").text(response).css("color", "red").show();
                }
            },
            error: function () {
                $("#message").text("Error saving movie.").css("color", "red").show();
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

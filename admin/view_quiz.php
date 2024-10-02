<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Details</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <!-- Quiz Details Card -->
        <div class="card mb-4">
            <div class="card-header">
                Quiz Details
            </div>
            <div class="card-body">
                <h5 class="card-title">Title: <?= $quizDetails['quiz_title'] ?></h5>
                <p class="card-text">Instructions: <?= $quizDetails['quiz_instructions'] ?></p>
                <p class="card-text">Total Marks: <?= $quizDetails['quiz_total_mark'] ?></p>
                <p class="card-text">Start Date: <?= $quizDetails['quiz_start_date'] ?></p>
                <p class="card-text">Start Time: <?= $quizDetails['quiz_start_time'] ?></p>
                <p class="card-text">Duration: <?= $quizDetails['quiz_duration'] ?></p>
                <p class="card-text">Status: <?= $quizDetails['quiz_status'] ?></p>
            </div>
        </div>

        <!-- Questions with Answers Card -->
        <div class="card">
            <div class="card-header">
                Questions with Answers
            </div>
            <div class="card-body">
                <?php foreach ($quizDetails['questions'] as $question): ?>
                    <div class="mb-4">
                        <h5><?= $question['question'] ?></h5>
                        <p>Type: <?= $question['type'] ?></p>
                        <p>Marks: <?= $question['marks'] ?></p>
                        <p>Answers:</p>
                        <ul>
                            <?php foreach ($question['answers'] as $answer): ?>
                                <li><?= $answer ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

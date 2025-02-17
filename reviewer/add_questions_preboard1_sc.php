<?php 
session_start();
include('../dbconnect.php');
$user_id = $_REQUEST['user_id'];
$s_id = $_REQUEST['s_id'];

if (isset($_POST['add_prelim_question'])) {

    $question = $_POST['question'];
    $answer1 = $_POST['answer1'];
    $answer2 = $_POST['answer2'];
    $answer3 = $_POST['answer3'];
    $answer4 = $_POST['answer4'];
    $final_answer = $_POST['final_answer'];

    if ($final_answer == "") {
        // Redirect to the page without alert
        header("Location: reviewer_test_questions.php?user_id=$user_id&s_id=$s_id");
        exit;
    } else {
        $query = "INSERT INTO question_answer (question, option1, option2, option3, option4, answer, subject_id, faculty_id, level) 
                  VALUES ('$question','$answer1','$answer2','$answer3','$answer4','$final_answer','$s_id','$user_id','PREBOARD1')";

        if (mysqli_query($conn, $query)) {
            // Redirect to the page after successful insertion
            header("Location: reviewer_test_questions.php?user_id=$user_id&s_id=$s_id");
            exit;
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
}
?>







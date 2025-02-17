<?php
include('../dbconnect.php');
$stud_id = $_REQUEST['user_id'];
$sub_id = $_REQUEST['sub_id'];
?>

<?php 
$query=mysqli_query($conn,"select * from subjects where id = '$sub_id'")or die(mysqli_error());
  if($row=mysqli_fetch_array($query))
  {
    $subs_id=$row['id'];
    $sub_description=$row['description'];
  }

?>
<?php 
$query=mysqli_query($conn,"select * from students where id = '$stud_id'")or die(mysqli_error());
  if($row=mysqli_fetch_array($query))
  {
    $level=$row['level'];
  }

?>

<?php
$host = 'localhost'; 
$username = 'root';  
$password = '';      
$database = 'smcc_befs'; 

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, question, option1, option2, option3, option4, answer FROM question_answer WHERE subject_id = $sub_id and level = '$level'";
$result = $conn->query($sql);

$questions = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = [
            'id' => $row['id'],
            'question' => $row['question'],
            'options' => [
                $row['option1'],
                $row['option2'],
                $row['option3'],
                $row['option4']
            ],
            'answer' => $row['answer']
        ];
    }
} else {
    echo "No questions for examination yet!";
    exit;
}

$score = 0;
$user_answers = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($questions as $question) {
        $user_answer = $_POST['answer'][$question['id']] ?? '';
        $user_answers[$question['id']] = $user_answer;

        if ($user_answer == $question['answer']) {
            $score++;
        }
    }
}


?>

<?php 
$query=mysqli_query($conn,"select * from subjects_timer where subjects_id = '$sub_id'")or die(mysqli_error());
  if($row=mysqli_fetch_array($query))
  {
    $timer=$row['timer'];
  }

?>


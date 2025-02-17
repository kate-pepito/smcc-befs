<?php 
$user_id = $_REQUEST['user_id'];
?>
<aside id="sidebar" class="sidebar">

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_home_page.php?user_id=<?php echo $user_id; ?>">
      <i class="bi bi-grid"></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->
 
  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_students_all.php?user_id=<?php echo $user_id; ?>">
      <i class="bi bi-person"></i>
      <span>Students</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_reviewers.php?user_id=<?php echo $user_id; ?>">
      <i class="bi bi-person"></i>
      <span>Reviewers</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_subjects.php?user_id=<?php echo $user_id; ?>">
      <i class="bi bi-book"></i><span>Subjects</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_recommended.php?user_id=<?php echo $user_id; ?>">
        <i class="bi bi-bar-chart"></i> <!-- A bar chart icon represents forecasting and analytics -->
        <span>Forecasting</span>
    </a>
</li>
</ul>

</aside>
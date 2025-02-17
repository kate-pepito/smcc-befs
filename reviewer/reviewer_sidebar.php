<?php 
$user_id = $_REQUEST['user_id'];
?>
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Dashboard Nav -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="reviewer_home.php?user_id=<?php echo htmlspecialchars($user_id); ?>">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <!-- End Dashboard Nav -->

    <!-- Sections and Subjects -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="reviewer_students.php?user_id=<?php echo htmlspecialchars($user_id); ?>">
        <i class="bi bi-person"></i>
        <span>Students</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="reviewer_subjects.php?user_id=<?php echo htmlspecialchars($user_id); ?>">
        <i class="bi bi-book"></i>
        <span>Subjects</span>
      </a>
    </li>
    <!-- End Sections and Subjects -->

  </ul>

</aside>

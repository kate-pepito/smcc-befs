
<aside id="sidebar" class="sidebar">

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_home_page">
      <i class="bi bi-grid"></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->
 
  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_students_all">
      <i class="bi bi-person"></i>
      <span>Students</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_reviewers">
      <i class="bi bi-person"></i>
      <span>Reviewers</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_subjects">
      <i class="bi bi-book"></i><span>Subjects</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_students_revalida">
      <i class="bi bi-book-half"></i><span>Revalida</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_students_gwa">
      <i class="bi bi-percent"></i><span>GWA (%)</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_students_board">
      <i class="bi bi-bookmark-check"></i><span>Board Exam Result</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="dean_forecasting">
        <i class="bi bi-bar-chart"></i> <!-- A bar chart icon represents forecasting and analytics -->
        <span>Forecast Recommendation</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#forecasting-nav" data-bs-toggle="collapse" href="#">
      <i class="ri-file-copy-2-line"></i><span>Manage Forecasting</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="forecasting-nav" class="nav-content collapse <?= is_nav_active("/dean/dean_datasets", "/dean/dean_train", "/dean/dean_models") ? "show" : "" ?>" data-bs-parent="#sidebar-nav">
      <li>
        <a href="dean_datasets" class="<?= is_nav_active("/dean/dean_datasets") ? "active" : "" ?>">
          <i class="bi bi-circle"></i><span>Dataset</span>
        </a>
      </li>
      <li>
        <a href="dean_train" class="<?= is_nav_active("/dean/dean_train") ? "active" : "" ?>">
          <i class="bi bi-circle"></i><span>Train</span>
        </a>
      </li>
      <li>
        <a href="dean_models" class="<?= is_nav_active("/dean/dean_models") ? "active" : "" ?>">
          <i class="bi bi-circle"></i><span>Models</span>
        </a>
      </li>
    </ul>
  </li>
</ul>

</aside>
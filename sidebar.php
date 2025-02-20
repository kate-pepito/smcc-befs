
<aside id="sidebar" class="sidebar">

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link collapsed" href="admin_home">
      <i class="bi bi-grid"></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->
  <li class="nav-heading">Interface</li>
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
      <i class="ri-user-3-line"></i><span>Users</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-nav" class="nav-content collapse <?= is_nav_active("/admin/admin_faculty", "/admin/admin_dean") ? "show" : "" ?>" data-bs-parent="#sidebar-nav">
      <!-- <li>
        <a href="admin_students">
          <i class="bi bi-circle"></i><span>Students</span>
        </a>
      </li> -->
      <li>
        <a href="admin_faculty" class="<?= is_nav_active("/admin/admin_faculty") ? "active" : "" ?>">
          <i class="bi bi-circle"></i><span>Reviewer</span>
        </a>
      </li>
      <li>
        <a href="admin_dean" class="<?= is_nav_active("/admin/admin_dean") ? "active" : "" ?>">
          <i class="bi bi-circle"></i><span>Dean</span>
        </a>
      </li>
    </ul>
  </li><!-- End Components Nav -->

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
      <i class="ri-file-copy-2-line"></i><span>Menu</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="forms-nav" class="nav-content collapse <?= is_nav_active("/admin/admin_school_year", "/admin/admin_course", "/admin/admin_section") ? "show" : "" ?>" data-bs-parent="#sidebar-nav">
      <li>
        <a href="admin_school_year" class="<?= is_nav_active("/admin/admin_school_year") ? "active" : "" ?>">
          <i class="bi bi-circle"></i><span>School Year</span>
        </a>
      </li>
      <li>
        <a href="admin_course" class="<?= is_nav_active("/admin/admin_course") ? "active" : "" ?>">
          <i class="bi bi-circle"></i><span>Course</span>
        </a>
      </li>
      <li>
        <a href="admin_section" class="<?= is_nav_active("/admin/admin_section") ? "active" : "" ?>">
          <i class="bi bi-circle"></i><span>Section</span>
        </a>
      </li>
    </ul>
  </li><!-- End Forms Nav -->

</ul>

</aside>
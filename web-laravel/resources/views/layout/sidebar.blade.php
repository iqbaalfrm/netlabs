<nav class="sidebar sidebar-offcanvas dynamic-active-class-disabled" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile not-navigation-link">
      <div class="nav-link">
        <div class="user-wrapper">
          <div class="profile-image">
            <img src="{{ url('assets/images/faces/face8.jpg') }}" alt="profile image">
          </div>
          <div class="text-wrapper">
            <p class="profile-name">{{ session('guru')->nama ?? 'Guru' }}</p>
            <div class="dropdown" data-display="static">
              <a href="#" class="nav-link d-flex user-switch-dropdown-toggler" id="UsersettingsDropdown" data-toggle="dropdown" aria-expanded="false">
                <small class="designation text-muted">Guru</small>
                <span class="status-indicator online"></span>
              </a>
              <div class="dropdown-menu" aria-labelledby="UsersettingsDropdown">
                <form action="/logout" method="POST">
                  @csrf
                  <button type="submit" class="dropdown-item">Keluar</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </li>
    <li class="nav-item {{ active_class(['/', '/dashboard']) }}">
      <a class="nav-link" href="{{ url('/dashboard') }}">
        <i class="menu-icon mdi mdi-television"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <li class="nav-item {{ active_class(['pertemuan*']) }}">
      <a class="nav-link" href="{{ url('/pertemuan') }}">
        <i class="menu-icon mdi mdi-book-open-page-variant"></i>
        <span class="menu-title">Pertemuan</span>
      </a>
    </li>
    <li class="nav-item {{ active_class(['siswa*']) }}">
      <a class="nav-link" href="{{ url('/siswa') }}">
        <i class="menu-icon mdi mdi-account-multiple"></i>
        <span class="menu-title">Siswa</span>
      </a>
    </li>
  </ul>
</nav>
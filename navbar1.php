<?php
session_start();

// Check login status
$is_logged_in = isset($_SESSION['user_id']);
$current_user_id = $is_logged_in ? intval($_SESSION['user_id']) : null;

// Fetch logged-in user's name and profile picture
$current_user_name = '';
$profile_picture = 'uploads/profile_pics/default.jpg'; // Default fallback

if ($is_logged_in && $current_user_id) {
    $stmt = $conn->prepare("SELECT firstname, middlename, lastname, profile_picture FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $user_row = $res->fetch_assoc();

        // Build full name
        $current_user_name = $user_row['firstname'];
        if (!empty($user_row['middlename'])) {
            $current_user_name .= ' ' . $user_row['middlename'];
        }
        $current_user_name .= ' ' . $user_row['lastname'];

        // Use profile picture if available
        if (!empty($user_row['profile_picture'])) {
            $profile_picture = $user_row['profile_picture'];
        }
    }
    $stmt->close();
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../styles/navbar1.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<title></title>
</head>
<body>

<!-- NAVIGATION BAR -->
<header>
  <div class="header-content">
    <div class="brand"><a href="index.php">Servify</a></div>
    <div class="menu-container">
      <nav class="wrapper-2" id="menu">
        <p><a href="view/browse.php">Services</a></p>
        <!-- <p><a href="#">Become a laborer</a></p> -->
        <p class="divider">|</p>

        <?php if ($is_logged_in): ?>
          <p class="profile-wrapper">
            <span class="profile-icon" onclick="toggleProfileMenu()">
              <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="icon">
            </span>
            <div id="profile-menu" class="profile-menu d-none">
              <a href="view/profile.php" class="user-info-link">
                <div class="user-info">
                  <span><?php echo htmlspecialchars($current_user_name); ?></span>
                  <i class="bi bi-pencil-square"></i>
                </div>
              </a>
              <a href="view/messages.php"><i class="bi bi-chat-dots"></i> Inbox</a>
              <!--<a href="#"><i class="bi bi-bell"></i> Notifications</a>
              <a href="#"><i class="bi bi-grid"></i> Dashboard</a>-->
              <a href="controls/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
          </p>
        <?php else: ?>
          <p class="login"><a href="view/login.php"><i class="bi bi-box-arrow-in-right"></i> Login / Signup</a></p>
        <?php endif; ?>
      </nav>
    </div>
  </div>
</header>


<!-- Bottom Navigation (Mobile/Tablet Only) -->
<div class="bottom-nav mobile-only">
  <a href="index.php">
    <div class="nav-item active" onclick="goToHome()">
      <i class="bi bi-house"></i>
      <span>Home</span>
    </div>
  </a>
  <a href="view/browse.php">
    <div class="nav-item" onclick="goToServices()">
      <i class="bi bi-search"></i>
      <span>Services</span>
    </div>
  </a>
  
  <div class="nav-item" onclick="toggleMoreMenu()">
    <i class="bi bi-three-dots"></i>
    <span>More</span>
  </div>
</div>


<!-- Fullscreen More Menu -->
<div id="more-menu" class="fullscreen-menu d-none">
  <div class="menu-panel">
    <div class="menu-header">
      <h1 class="menu-title">SERVIFY</h1>
      <span class="close-btn" onclick="toggleMoreMenu()">✕</span>
    </div>

    <?php if ($is_logged_in): ?>
    <!-- Logged-in User Menu -->
      <div class="user-section">
        <div class="profile-info" onclick="toggleProfileMenu()">
          <a href="view/profile.php" class="profile-link">
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="icon">
            <h3 class="user-name"><?php echo htmlspecialchars($current_user_name); ?></h3>
          </a>
        </div>
        <i class="bi bi-pencil-square edit-icon"></i>
      </div>

      <div class="section-divider"></div>

      <div class="menu-options">
        <a href="view/messages.php"><i class="bi bi-chat-dots"></i> Inbox</a>
        <a href="#"><i class="bi bi-bell"></i> Notifications</a>
        <a href="#"><i class="bi bi-grid"></i> Dashboard</a>
        <a href="controls/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
      </div>

    <?php else: ?>
      <!-- Non-User Menu -->
      <div class="menu-options">
        <a href="view/become-laborer.php"><i class="bi bi-person-workspace"></i> Become a laborer</a>
        <a href="view/login.php"><i class="bi bi-person-circle"></i> Signin / Signup</a>
      </div>
    <?php endif; ?>
  </div>
</div>



<script>
  function toggleMenu() {
    const menu = document.getElementById('menu');
    menu.classList.toggle('active');
  }
</script>

<script>
  // Toggle profile dropdown
  function toggleProfileMenu() {
    const menu = document.getElementById('profile-menu');
    menu.classList.toggle('d-none');
  }

  // Toggle fullscreen "More" menu
  function toggleMoreMenu() {
    const menu = document.getElementById('more-menu');
    menu.classList.toggle('d-none');
  }

  // Navigation actions
  function goToHome() {
    window.location.href = 'view/home.php';
  }

  function goToServices() {
    window.location.href = 'view/services.php';
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// session_start(); // Start the session at the beginning

include("config/connection.php");
include("include/navbar.php");

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, fname, lname, email, role FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $fname, $lname, $email, $role);
        $stmt->fetch();
        
        // Store user information in session
        $_SESSION['id'] = $id;
        $_SESSION['fname'] = $fname;
        $_SESSION['lname'] = $lname;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;

        // Redirect the user based on their role
        if ($role == 'ADMIN') {
            // Redirect to admin dashboard
            header("Location: admin/index.php");
            exit();
        } elseif ($role == 'USER') {
            // Redirect to user dashboard
            header("Location: dashboard/index.php");
            exit();
        } else {
            // Handle other roles or unexpected cases
            echo "Login Gagal: Role tidak dikenali.";
        }
    } else {
        echo "Login Gagal: Email atau kata sandi salah.";
    }

    $stmt->close();
}
?>


<!-- Section: Design Block -->
<section class="background-radial-gradient overflow-hidden">
  <style>
    .background-radial-gradient {
      background-color: hsl(218, 41%, 15%);
      background-image: radial-gradient(650px circle at 0% 0%,
          hsl(218, 41%, 35%) 15%,
          hsl(218, 41%, 30%) 35%,
          hsl(218, 41%, 20%) 75%,
          hsl(218, 41%, 19%) 80%,
          transparent 100%),
        radial-gradient(1250px circle at 100% 100%,
          hsl(218, 41%, 45%) 15%,
          hsl(218, 41%, 30%) 35%,
          hsl(218, 41%, 20%) 75%,
          hsl(218, 41%, 19%) 80%,
          transparent 100%);
    }
  </style>

  <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
    <div class="row gx-lg-5 align-items-center pt-5 mt-5 mb-5">
      <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
        <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
          Welcome Back <br />
          <span style="color: hsl(218, 81%, 75%)">Login to your account</span>
        </h1>
        <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
          Enter your email and password to access your account.
        </p>
      </div>

      <div class="col-lg-6 mb-5 mb-lg-0 mt-4 position-relative">
        <div class="card bg-glass">
          <div class="card-body px-4 py-5 px-md-5">
            <form method="post">
              <!-- Email input -->
              <div data-mdb-input-init class="form-outline mb-4">
                <input type="email" name="email" id="email" class="form-control" required />
                <label class="form-label" for="email">Alamat Email</label>
              </div>

              <!-- Password input -->
              <div data-mdb-input-init class="form-outline mb-4">
                <input type="password" name="password" id="password" class="form-control" required />
                <label class="form-label" for="password">Kata Sandi</label>
              </div>

              <!-- Submit button -->
              <div style="display: flex; justify-content: flex-end;">
                <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4" name="login">
                    Login
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Section: Design Block -->
<?php
include("include/footer.php");
?>

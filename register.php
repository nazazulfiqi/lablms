<?php
include("config/connection.php");
include("include/navbar.php");

// Initialize message variable
$message = '';

if (isset($_POST['register'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = 'USER';

    // Check if all fields are filled
    if (empty($fname) || empty($lname) || empty($email) || empty($password)) {
        $message = '<div class="alert alert-danger">All fields are required.</div>';
    } elseif (strlen($password) < 8) {
        $message = '<div class="alert alert-danger">Password must be at least 8 characters long.</div>';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = '<div class="alert alert-warning">Email is already registered. Please use a different email.</div>';
        } else {
            // Using prepared statements to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO users (fname, lname, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
            $stmt->bind_param("sssss", $fname, $lname, $email, $password, $role);

            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Account successfully registered.</div>';
                echo "<script>
                setTimeout(function() {
                    window.location = 'login.php';
                }, 1500); // Redirect after 1.5 seconds
                </script>";
            } else {
                $message = '<div class="alert alert-danger">Registration failed: ' . $stmt->error . '</div>';
            }
        }

        $stmt->close();
    }
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
    .card {
        margin: 0 auto; /* Center the card */
        max-width: 600px; /* Set a maximum width for larger screens */
        border-radius: 10px;
        overflow: hidden;
    }
    .card-body {
        padding: 2rem;
    }
    .alert {
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1rem;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
    }
  </style>

  <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
    <div class="row gx-lg-5 align-items-center pt-5 mt-5 mb-5">
      <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
        <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
          The best offer <br />
          <span style="color: hsl(218, 81%, 75%)">for your business</span>
        </h1>
        <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
          Lorem ipsum dolor sit amet, consectetur adipisicing elit.
          Temporibus, expedita iusto veniam atque, magni tempora mollitia
          dolorum consequatur nulla, neque debitis eos reprehenderit quasi
          ab ipsum nisi dolorem modi. Quos?
        </p>
      </div>

      <div class="col-lg-6 mb-5 mb-lg-0 mt-4 position-relative">
        <div class="card bg-glass">
          <div class="card-body px-4 py-5 px-md-5">
            <?php if ($message): ?>
            <div class="mb-4"><?php echo $message; ?></div>
            <?php endif; ?>
            <form method="post">
              <div class="row">
                <div class="col-md-6 mb-4">
                  <div data-mdb-input-init class="form-outline">
                    <input type="text" name="fname" id="fname" class="form-control" required />
                    <label class="form-label" for="fname">First Name</label>
                  </div>
                </div>
                <div class="col-md-6 mb-4">
                  <div data-mdb-input-init class="form-outline">
                    <input type="text" name="lname" id="lname" class="form-control" required />
                    <label class="form-label" for="lname">Last Name</label>
                  </div>
                </div>
              </div>
              <!-- Email input -->
              <div data-mdb-input-init class="form-outline mb-4">
                <input type="email" name="email" id="email" class="form-control" required />
                <label class="form-label" for="email">Email Address</label>
              </div>

              <!-- Password input -->
              <div data-mdb-input-init class="form-outline mb-4">
                <input type="password" name="password" id="password" class="form-control" required />
                <label class="form-label" for="password">Password</label>
              </div>

              <!-- Submit button -->
              <div style="display: flex; justify-content: flex-end;">
                <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4" name="register">
                    Register
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

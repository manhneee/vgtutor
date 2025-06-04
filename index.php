<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="icon" href="img/logo.png">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/logo.png" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
               <ul class="navbar-nav ms-auto d-flex gap-5">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero">
        <h1>Find the Right Tutor for You</h1>
        <p>Connecting VGU students with the best tutors for their success.</p>
        <div class="d-flex gap-3">
            <a href="login.php" class="btn btn-orange px-4 py-2">Get Started Today</a>
            <a href="#about" class="btn btn-outline-orange px-4 py-2">Learn More</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="bg-white text-dark py-5">
<div class="container text-center">
    <h2 class="mb-4 fw-bold">Why Choose VGtUtor?</h2>
    <p class="text-muted mb-5 ">We deliver better academic outcomes through expert tutors, tailored support, and future-ready tools.</p>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="p-4 shadow rounded bg-white h-100">
                <img src="img/stragies.png" alt="Innovation" width="100" class="mb-3">
                <h5>Strategic Innovation</h5>
                <p class="text-muted fw-semibold">We apply innovative teaching methods and tools to help students overcome challenges and unlock academic success.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 shadow rounded bg-white h-100">
                <img src="img/team.png" alt="Team" width="70" class="mb-3">
                <h5>About Team</h5>
                <p class="text-muted fw-semibold">Our tutors are students and experts from VGU who understand your needs and care about your progress.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 shadow rounded bg-white h-100">
                <img src="img/future.png" alt="Potential" width="70" class="mb-3">
                <h5>Future Potential</h5>
                <p class="text-muted fw-semibold">We support long-term academic development with personalized learning paths, mentoring, and career preparation.</p>
            </div>
        </div>
    </div>
</div>

    </section>
    <section class="py-5 text-center text-light" style="background-color:rgb(247, 125, 54);">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-3">
        <h2 class="fw-bolder display-6 text-white">Coming..</h2>
        <p class="mb-0 text-muted">Tutoring Hours Delivered</p>
      </div>
      <div class="col-md-3">
        <h2 class="fw-bolder display-6 text-white">Coming..</h2>
        <p class="mb-0 text-muted">Student Satisfaction</p>
      </div>
      <div class="col-md-3">
        <h2 class="fw-bolder display-6 text-white">Coming..</h2>
        <p class="mb-0 text-muted">Registered Tutors</p>
      </div>
      <div class="col-md-3">
        <h2 class="fw-bolder display-6 text-white">Coming...</h2>
        <p class="mb-0 text-muted">Courses Supported</p>
      </div>
    </div>
  </div>
</section>


  <section id="contact" class="bg-light text-dark py-5">
  <div class="container">
    <div class="row g-4 align-items-stretch">
      <!-- Left: Upcoming Features -->
        <div class="col-md-6">
          <div class="container shadow-lg rounded-4 p-4 h-100 bg-white">  
            <h2 class="upcoming-title">Upcoming <span class="highlight">features</span></h2>
            <div id="updateCarousel" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="img/test1.jpg" class="d-block w-100 rounded-3 carousel-img" alt="Update 1">
                </div>
                <div class="carousel-item">
                  <img src="img/test.jpg" class="d-block w-100 rounded-3 carousel-img" alt="Update 2">
                </div>
                <div class="carousel-item">
                  <img src="img/test3.jpg" class="d-block w-100 rounded-3 carousel-img" alt="Update 3">
                </div>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#updateCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#updateCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </button>
            </div>
          </div>
        </div>

        <!-- Right: Contact Form -->
        <div class="col-md-6">
          <div class="p-4 rounded-4 shadow-lg bg-white h-100">
            <h2 class="fw-bold text-center">Get in Touch</h2>
            <p class="text-center text-muted">We’d love to hear from you. Fill out the form below and we’ll respond shortly.</p>
            <form>
              <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" required>
              </div>
              <div class="mb-3">
                <label for="name" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="name" required>
              </div>
              <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" rows="4" required></textarea>
              </div>
              <button type="submit" class="btn btn-orange px-4 py-2 rounded-pill shadow-sm">Send</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

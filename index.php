<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>tittle</title>

  <!-- 
    - favicon
  -->
  <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">

  <!-- 
    - custom css link
  -->
  <link rel="stylesheet" href="css/home_page.css">

  <!-- 
    - google font link
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- 
    - preload images
  -->
  <link rel="preload" as="image" href="img/hero-banner.png">
  <link rel="preload" as="image" href="img/hero-abs-1.png" media="min-width(768px)">
  <link rel="preload" as="image" href="img/hero-abs-2.png" media="min-width(768px)">

</head>

<body id="top">

  <!-- 
    - #HEADER
  -->

<header class="header" data-header style="background-color: white; color: black;">
    <div class="container">

      <h1>
        <a href="#" class="logo">VGTUTOR</a>
      </h1>

      <nav class="navbar" data-navbar>

        <div class="navbar-top">
          <a href="#" class="logo">VGTUTOR</a>

          <button class="nav-close-btn" aria-label="Close menu" data-nav-toggler>
            <ion-icon name="close-outline"></ion-icon>
          </button>
        </div>

        <ul class="navbar-list">

          <li class="navbar-item">
            <a href="#home" class="navbar-link" data-nav-toggler>Home</a>
          </li>

          <li class="navbar-item">
            <a href="#about" class="navbar-link" data-nav-toggler>About</a>
          </li>

          <li class="navbar-item">
            <a href="#course" class="navbar-link" data-nav-toggler>Courses</a>
          </li>


          <li class="navbar-item">
            <a href="#contact" class="navbar-link" data-nav-toggler>Contact</a>
          </li>

        </ul>

      </nav>

      <div class="header-actions">



        <a href="login.php" class="header-action-btn login-btn">
          <ion-icon name="person-outline" aria-hidden="true"></ion-icon>

          <span class="span">Login </span>
        </a>
        
                <a href="signup.php" class="header-action-btn login-btn">
          <ion-icon name="person-outline" aria-hidden="true"></ion-icon>

          <span class="span">Register</span>
        </a>

        <button class="header-action-btn nav-open-btn" aria-label="Open menu" data-nav-toggler>
          <ion-icon name="menu-outline"></ion-icon>
        </button>

      </div>

      <div class="overlay" data-nav-toggler data-overlay></div>

    </div>
  </header>


  <main>
    <article>

      <!-- 
        - #HERO
      -->

      <section class="hero" id="home" aria-label="hero" style="background-image: url('img/hero-bg.jpg')">
        <div class="container">

          <div class="hero-content">

            <p class="section-subtitle">Better Learning Future With Us</p>

            <h2 class="h1 hero-title">Find the Right Tutor For You</h2>

            <p class="hero-text">
              Connecting VGU students with the best tutors for their success
            </p>

            <a href="signup.php" class="btn btn-primary">
              <span class="span">Get Started Today</span>

              <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
            </a>

          </div>

          <figure class="hero-banner">

            <img src="img/hero-banner.png" width="500" height="500" loading="lazy" alt="hero image"
              class="w-100">

          </figure>

        </div>
      </section>


 <!-- 
        - #ABOUT
      -->

      <section class="section about" id="about" aria-label="about">
        <div class="container">

          <figure class="about-banner">

            <img src="img/about-banner.jpg" width="450" height="590" loading="lazy" alt="about banner"
              class="w-100 about-img">

            <img src="img/about1.jpg" width="188" height="242" loading="lazy" aria-hidden="true"
              class="abs-img abs-img-1">

          </figure>

          <div class="about-content">

            <p class="section-subtitle">Who We Are ?</p>

            <h2 class="h2 section-title">We deliver better academic outcomes through expert tutors.</h2>

            <ul class="about-list">

              <li class="about-item">

                <div class="item-icon item-icon-1">
                  <img src="img/about-icon-1.png" width="30" height="30" loading="lazy" aria-hidden="true">
                </div>

                <div>
                  <h3 class="h3 item-title">The Expertise Tutors </h3>

                  <p class="item-text">
                    Our tutors are students and experts from VGU who understand your needs and care about your progress.
                  </p>
                </div>

              </li>

              <li class="about-item">

                <div class="item-icon item-icon-2">
                  <img src="img/about-icon-2.png" width="30" height="30" loading="lazy" aria-hidden="true">
                </div>

                <div>
                  <h3 class="h3 item-title">Our Goal</h3>

                  <p class="item-text">
                    We apply innovative teaching methods and tools to help students overcome challenges and unlock academic success.
                  </p>
                </div>

              </li>

              <li class="about-item">

                <div class="item-icon item-icon-3">
                  <img src="img/about-icon-3.png" width="30" height="30" loading="lazy" aria-hidden="true">
                </div>

                <div>
                  <h3 class="h3 item-title">Our Future Potential</h3>

                  <p class="item-text">
                    We support long-term academic development with personalized learning paths, mentoring, and career preparation.
                  </p>
                </div>

              </li>

            </ul>

            <a href="#" class="btn btn-primary">
              <span class="span">Know About Us</span>

              <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
            </a>

          </div>

        </div>
      </section>



      <!-- 
        - #CATEGORY
      -->

      <section class="section category" id="course" aria-label="category">
        <div class="container">

          <p class="section-subtitle">Course Offering till now</p>

          <h2 class="h2 section-title">Differen For you to pick</h2>

          <ul class="grid-list">

 <li>
              <div class="category-card">

                <div>
                  <ion-icon name="layers-outline"></ion-icon>
                </div>

                <div>
                  <h3 class="h3 card-title">
                    <a href="#">Statistic</a>
                  </h3>
                </div>

              </div>
            </li>
 <li>
              <div class="category-card">

                <div>
                  <ion-icon name="layers-outline"></ion-icon>
                </div>

                <div>
                  <h3 class="h3 card-title">
                    <a href="#">Discrete Math</a>
                  </h3>
                </div>

              </div>
            </li>

 <li>
              <div class="category-card">

                <div>
                  <ion-icon name="layers-outline"></ion-icon>
                </div>

                <div>
                  <h3 class="h3 card-title">
                    <a href="#">Database</a>
                  </h3>
                </div>

              </div>
            </li>

            <li>
              <div class="category-card">

                <div>
                  <ion-icon name="layers-outline"></ion-icon>
                </div>

                <div>
                  <h3 class="h3 card-title">
                    <a href="#">IT-security</a>
                  </h3>
                </div>

              </div>
            </li>

            <li>
              <div class="category-card">

                <div>
                  <ion-icon name="laptop-outline"></ion-icon>
                </div>

                <div>
                  <h3 class="h3 card-title">
                    <a href="#">Algebra</a>
                  </h3>
                </div>

              </div>
            </li>


          </ul>

        </div>
      </section>

      <!-- 
        - #CTA
      -->

      <section class="section cta" aria-label="">
        <div class="container">

          <figure class="cta-banner">
            <img src="img/cta.jpg" width="580" height="380" loading="lazy" alt="cta banner"
              class="img-cover">
          </figure>

          <div class="cta-content">


            <h2 class="h2 section-title">Our Mission</h2>

            <p class="section-text">
              our mission is to empower every learner with the tools, guidance, and support they need to succeed academically 
              and grow personally. We believe that education should be accessible, personalized, and transformative—built on the 
              principle that every student has the potential to excel when given the right environment and mentorship.
            </p>

            <a href="#" class="btn btn-secondary">
              <span class="span">Join us now</span>

              <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
            </a>

          </div>

        </div>
      </section>

      <!-- 
        - #NEWSLETTER
      -->

      <section class="section newsletter" aria-label="newsletter"
        style="background-image: url('img/newsletter-bg.jpg')">
        <div class="container">

          <p class="section-subtitle">Get in Touch</p>

          <h2 class="h2 section-title">Be a part of our community. Fill out the email below to receive news about us.</h2>

          <form action="" class="newsletter-form">

            <div class="input-wrapper">
              <input type="email" name="email_address" aria-label="email" placeholder="Enter your mail address" required
                class="email-field">

              <ion-icon name="mail-open-outline" aria-hidden="true"></ion-icon>
            </div>

            <button type="submit" class="btn btn-primary">
              <span class="span">Subscribe</span>

              <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
            </button>

          </form>

        </div>
      </section>

    </article>
  </main>





  <!-- 
    - #FOOTER
  -->

  <footer class="footer"> 
    <div class="container">

      <div class="footer-top">

        <div class="footer-brand">

          <a href="#" class="logo">VGTUTOR</a>

          <p class="section-text">
            Product of VGU's students.
          </p>

          <ul class="social-list">

            <li>
              <a href="#" class="social-link">
                <ion-icon name="logo-facebook"></ion-icon>
              </a>
            </li>

            <li>
              <a href="#" class="social-link">
                <ion-icon name="logo-twitter"></ion-icon>
              </a>
            </li>

            <li>
              <a href="#" class="social-link">
                <ion-icon name="logo-linkedin"></ion-icon>
              </a>
            </li>

            <li>
              <a href="#" class="social-link">
                <ion-icon name="logo-pinterest"></ion-icon>
              </a>
            </li>

          </ul>

        </div>

        <ul class="footer-list" id="contact">

          <li>
            <p class="footer-list-title">Explore</p>
          </li>

          <li>
            <a href="#" class="footer-link">
              <ion-icon name="chevron-forward" aria-hidden="true"></ion-icon>

              <span class="span">About Us</span>
            </a>
          </li>

          <li>
            <a href="#" class="footer-link">
              <ion-icon name="chevron-forward" aria-hidden="true"></ion-icon>

              <span class="span">Upcoming Events</span>
            </a>
          </li>

          <li>
            <a href="#" class="footer-link">
              <ion-icon name="chevron-forward" aria-hidden="true"></ion-icon>

              <span class="span">Blog & News</span>
            </a>
          </li>

          <li>
            <a href="#" class="footer-link">
              <ion-icon name="chevron-forward" aria-hidden="true"></ion-icon>

              <span class="span">FAQ Question</span>
            </a>
          </li>


        </ul>

        <ul class="footer-list">
        
        <ul class="footer-list">

          <li>
            <p class="footer-list-title">Contact Info</p>
          </li>

          <li class="footer-item">
            <ion-icon name="location-outline" aria-hidden="true"></ion-icon>

            <address class="footer-link">
              Đ. VĐ 4, Thới Hoà, Bến Cát, Bình Dương 75000
            </address>
          </li>

          <li class="footer-item">
            <ion-icon name="mail-outline" aria-hidden="true"></ion-icon>

            <a href="mailto:contact@eduhome.com" class="footer-link">10422052@student.vgu.edu.vn</a>
          </li>

                    <li class="footer-item">
            <ion-icon name="mail-outline" aria-hidden="true"></ion-icon>

            <a href="mailto:contact@eduhome.com" class="footer-link">10422044@student.vgu.edu.vn</a>
            
                      <li class="footer-item">
            <ion-icon name="mail-outline" aria-hidden="true"></ion-icon>

            <a href="mailto:contact@eduhome.com" class="footer-link">10422047@student.vgu.edu.vn</a>
          </li>          <li class="footer-item">
            <ion-icon name="mail-outline" aria-hidden="true"></ion-icon>

            <a href="mailto:contact@eduhome.com" class="footer-link">10422031@student.vgu.edu.vn</a>
          </li>
          <li class="footer-item">
            <ion-icon name="mail-outline" aria-hidden="true"></ion-icon>

            <a href="mailto:contact@eduhome.com" class="footer-link">10422118@student.vgu.edu.vn</a>
          </li>

          </li>

        </ul>

      </div>


    </div>
  </footer>


</body>

</html>
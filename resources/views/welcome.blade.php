<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Vardan India - Empower Your Financial Future</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    rel="stylesheet"
  />
  <style>
    /* General Body and Background */
    body {
      background: linear-gradient(135deg, #000713, #3267a5ba);
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #f0f4f8;
      scroll-behavior: smooth;
    }

    /* Glassmorphism containers */
    .glass {
      background: rgba(255, 255, 255, 0.12);
      border-radius: 15px;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .glass:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(255, 213, 79, 0.6);
    }

    /* Header */
    header {
      padding: 0.5rem 2rem;
      background: rgba(0, 0, 0, 0.3);
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.3);
    }
    .navbar-brand {
      font-weight: 700;
      font-size: 1.9rem;
      color: #ffd54f;
      text-shadow: 1px 1px 3px #000000aa;
    }
    .nav-link {
      color: #f0f4f8;
      font-weight: 500;
      transition: color 0.3s ease;
    }
    .nav-link:hover {
      color: #ffd54f;
    }

    /* Hero Section */
    .hero {
      min-height: 75vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 4rem 1rem;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }
    .hero h1 {
      font-size: 3.8rem;
      font-weight: 900;
      letter-spacing: 0.07em;
      text-shadow: 2px 2px 12px rgba(0, 0, 0, 0.75);
      max-width: 900px;
    }
    .hero p {
      font-size: 1.3rem;
      margin: 1.5rem 0 3rem;
      color: #dcdcdccc;
      max-width: 650px;
      line-height: 1.5;
    }
    .btn-primary {
      background: #ffd54f;
      border: none;
      color: #1e3c72;
      font-weight: 700;
      padding: 0.85rem 3rem;
      border-radius: 50px;
      box-shadow: 0 5px 20px #ffd54faa;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background: #fbc02d;
      box-shadow: 0 8px 30px #fbc02daa;
      color: #fff;
    }

    /* Animated background circles */
    .animated-bg {
      position: absolute;
      width: 550px;
      height: 550px;
      background: radial-gradient(circle at center, #ffd54f66, transparent 70%);
      border-radius: 50%;
      top: -200px;
      right: -200px;
      filter: blur(120px);
      animation: pulse 8s ease-in-out infinite alternate;
      z-index: 0;
    }
    @keyframes pulse {
      0% {
        transform: scale(1);
      }
      100% {
        transform: scale(1.3);
      }
    }

    /* Section Titles */
    h2.section-title {
      text-align: center;
      font-size: 2.5rem;
      font-weight: 800;
      color: #ffd54f;
      margin-bottom: 2rem;
      text-shadow: 1px 1px 6px rgba(0, 0, 0, 0.7);
    }

    /* Features Section */
    .features {
      padding: 4rem 2rem;
      max-width: 1200px;
      margin: 0 auto 4rem;
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      justify-content: center;
      z-index: 1;
      position: relative;
    }
    .feature-card {
      flex: 1 1 280px;
      padding: 2.5rem 2rem;
      text-align: center;
      cursor: default;
      transition: box-shadow 0.3s ease;
    }
    .feature-icon {
      font-size: 3.8rem;
      color: #ffd54f;
      margin-bottom: 1.3rem;
      filter: drop-shadow(0 0 3px #ffd54faa);
    }
    .feature-card h3 {
      margin-bottom: 0.7rem;
      font-weight: 700;
      font-size: 1.6rem;
      color: #fff;
    }
    .feature-card p {
      color: #ddd;
      font-size: 1.05rem;
      line-height: 1.5;
    }

    /* About Us Section */
    .about-us {
      background: rgba(0, 0, 0, 0.25);
      padding: 4rem 2rem;
      max-width: 900px;
      margin: 0 auto 4rem;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(255, 213, 79, 0.4);
      text-align: center;
    }
    .about-us p {
      font-size: 1.2rem;
      line-height: 1.7;
      color: #eee;
    }

    /* Services Section */
    .services {
      max-width: 1100px;
      margin: 0 auto 5rem;
      padding: 2rem 1rem;
      display: flex;
      flex-wrap: wrap;
      gap: 1.8rem;
      justify-content: center;
    }
    .service-card {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 2rem;
      flex: 1 1 300px;
      text-align: center;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: default;
    }
    .service-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px #ffd54faa;
    }
    .service-icon {
      font-size: 3.6rem;
      margin-bottom: 1rem;
      color: #ffd54f;
      filter: drop-shadow(0 0 4px #ffd54fbb);
    }
    .service-card h4 {
      font-weight: 700;
      color: #fff;
      margin-bottom: 0.5rem;
    }
    .service-card p {
      font-size: 1rem;
      color: #ddd;
      line-height: 1.5;
    }

    /* Testimonials Section */
    .testimonials {
      max-width: 900px;
      margin: 0 auto 5rem;
      color: #eee;
    }
    .carousel-item {
      text-align: center;
      padding: 2rem 3rem;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
      user-select: none;
    }
    .testimonial-text {
      font-style: italic;
      font-size: 1.2rem;
      margin-bottom: 1.5rem;
    }
    .testimonial-author {
      font-weight: 700;
      font-size: 1.1rem;
      color: #ffd54f;
    }
    .carousel-indicators [data-bs-target] {
      background-color: #ffd54f;
      opacity: 0.7;
    }
    .carousel-indicators .active {
      opacity: 1;
    }

    /* CTA Section */
    .cta {
      background: rgba(0, 0, 0, 0.3);
      padding: 3rem 1rem;
      text-align: center;
      border-radius: 25px;
      max-width: 700px;
      margin: 0 auto 5rem;
      box-shadow: 0 15px 40px rgba(255, 213, 79, 0.5);
    }
    .cta h2 {
      font-size: 2.8rem;
      font-weight: 800;
      margin-bottom: 1rem;
      color: #ffd54f;
      text-shadow: 1px 1px 6px rgba(0, 0, 0, 0.7);
    }
    .cta p {
      font-size: 1.2rem;
      margin-bottom: 2rem;
      color: #ddd;
      line-height: 1.6;
    }
    .form-control {
      border-radius: 50px;
      padding: 1rem 1.5rem;
      font-size: 1.1rem;
    }
    .btn-subscribe {
      background: #ffd54f;
      border: none;
      color: #1e3c72;
      font-weight: 700;
      padding: 0.85rem 2.5rem;
      border-radius: 50px;
      box-shadow: 0 5px 20px #ffd54faa;
      transition: all 0.3s ease;
    }
    .btn-subscribe:hover {
      background: #fbc02d;
      box-shadow: 0 8px 30px #fbc02daa;
      color: #fff;
    }

    /* Footer */
    footer {
      text-align: center;
      padding: 1rem 0;
      color: #ccc;
      font-size: 0.9rem;
      border-top: 1px solid #444;
      margin-top: 4rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.7rem;
      }
      .features,
      .services {
        flex-direction: column;
        padding: 0 1rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <nav class="d-flex justify-content-between align-items-center">
      <a class="navbar-brand" href="#">Vardan India</a>
      <ul class="nav">
        <li class="nav-item"><a href="#about" class="nav-link">About</a></li>
        <li class="nav-item"><a href="#services" class="nav-link">Services</a></li>

        <!-- Login Dropdown -->
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#"
            id="loginDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            Login
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="loginDropdown">
            <li><a class="dropdown-item" href="/admin/login">Admin Login</a></li>
            <li><a class="dropdown-item" href="/retailer/login">Retailer Login</a></li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <section class="hero">
    <div class="animated-bg"></div>
    <h1>Empower Your Financial Future with Vardan India</h1>
    <p>
      Unlock the potential of smart investing, expert financial planning, and wealth management.
      Join thousands achieving their goals with confidence and clarity.
    </p>
    <a href="#about" class="btn btn-primary">Get Started Today</a>
  </section>

  <section id="about" class="about-us glass">
    <h2 class="section-title">About Vardan India</h2>
    <p>
      At Vardan India, we believe everyone deserves financial freedom and security.
      Our mission is to provide transparent, innovative financial solutions tailored to your unique needs.
      From personalized investment strategies to real-time market insights, we empower you to take control of your wealth.
    </p>
  </section>

  <section id="services" class="services">
    <h2 class="section-title w-100">Our Services</h2>
    <div class="service-card glass">
      <div class="service-icon"><i class="fas fa-chart-line"></i></div>
      <h4>Investment Planning</h4>
      <p>Customized portfolio management to maximize growth while managing risk for short and long term goals.</p>
    </div>
    <div class="service-card glass">
      <div class="service-icon"><i class="fas fa-coins"></i></div>
      <h4>Wealth Management</h4>
      <p>Comprehensive wealth solutions including tax optimization, retirement planning, and estate management.</p>
    </div>
    <div class="service-card glass">
      <div class="service-icon"><i class="fas fa-lightbulb"></i></div>
      <h4>Financial Advisory</h4>
      <p>Expert advice on budgeting, debt reduction, and savings plans to secure your financial well-being.</p>
    </div>
    <div class="service-card glass">
      <div class="service-icon"><i class="fas fa-mobile-alt"></i></div>
      <h4>Real-time Market Insights</h4>
      <p>Stay ahead with live updates, news, and alerts customized to your investment interests.</p>
    </div>
  </section>

  <section id="testimonials" class="testimonials">
    <h2 class="section-title">What Our Clients Say</h2>
    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="7000">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <p class="testimonial-text">
            "Vardan India transformed how I manage my money. Their expert guidance gave me the confidence to invest wisely and grow my wealth steadily."
          </p>
          <p class="testimonial-author">– Kratika S., Entrepreneur</p>
        </div>
        <div class="carousel-item">
          <p class="testimonial-text">
            "The personalized financial plan helped me retire early and travel the world. Their team is responsive and truly cares about clients."
          </p>
          <p class="testimonial-author">– Harshvardhan D., Software Engineer</p>
        </div>
        <div class="carousel-item">
          <p class="testimonial-text">
            "Reliable, transparent, and innovative services. I recommend Vardan India to anyone serious about building wealth."
          </p>
          <p class="testimonial-author">– Ayushi B., Freelancer</p>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
      <div class="carousel-indicators mt-3">
        <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
        <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="2"></button>
      </div>
    </div>
  </section>

  <footer>
    &copy; 2025 Vardan India. All rights reserved.
  </footer>

  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
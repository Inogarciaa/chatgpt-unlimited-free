<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sugarlandia Barquillios | Premium Filipino Delicacies</title>
  
  <!-- Modern Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
  
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --primary: #d97706; /* Caramel/Gold */
      --primary-hover: #b45309;
      --secondary: #451a03; /* Deep Chocolate */
      --bg-light: #fefce8;
      --text-main: #1c1917;
      --text-muted: #57534e;
      --glass-bg: rgba(255, 255, 255, 0.85);
      --glass-border: rgba(255, 255, 255, 0.3);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; }

    body {
      font-family: 'Outfit', sans-serif;
      color: var(--text-main);
      background-color: var(--bg-light);
      line-height: 1.6;
      overflow-x: hidden;
    }

    h1, h2, h3 { font-family: 'Playfair Display', serif; }

    /* ============================================================
       NAVIGATION (Glassmorphism)
    ============================================================ */
    nav {
      position: fixed;
      top: 0; left: 0; right: 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem 5%;
      z-index: 1000;
      background: var(--glass-bg);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--glass-border);
      transition: all 0.3s ease;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--secondary);
      text-decoration: none;
      letter-spacing: -0.5px;
    }
    .logo span { color: var(--primary); }

    .nav-links {
      display: flex;
      gap: 2.5rem;
      align-items: center;
    }

    .nav-links a {
      text-decoration: none;
      color: var(--text-main);
      font-weight: 500;
      font-size: 1rem;
      transition: color 0.3s;
    }
    .nav-links a:hover { color: var(--primary); }

    .btn-order {
      background: var(--primary);
      color: #fff !important;
      padding: 0.8rem 1.8rem;
      border-radius: 50px;
      font-weight: 600;
      box-shadow: 0 4px 15px rgba(217, 119, 6, 0.3);
      transition: all 0.3s ease !important;
    }
    .btn-order:hover {
      background: var(--primary-hover);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
    }

    /* ============================================================
       HERO SECTION
    ============================================================ */
    .hero {
      min-height: 100vh;
      display: flex;
      align-items: center;
      padding: 0 5%;
      background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: -20%; right: -10%;
      width: 600px; height: 600px;
      background: radial-gradient(circle, rgba(217,119,6,0.15) 0%, rgba(255,255,255,0) 70%);
      border-radius: 50%;
    }

    .hero-content {
      flex: 1;
      max-width: 600px;
      z-index: 2;
    }

    .hero-badge {
      display: inline-block;
      background: rgba(217, 119, 6, 0.1);
      color: var(--primary);
      padding: 6px 16px;
      border-radius: 100px;
      font-size: 0.85rem;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 1.5rem;
    }

    .hero-content h1 {
      font-size: clamp(3rem, 5vw, 4.5rem);
      line-height: 1.1;
      color: var(--secondary);
      margin-bottom: 1.5rem;
    }

    .hero-content p {
      font-size: 1.1rem;
      color: var(--text-muted);
      margin-bottom: 2.5rem;
      max-width: 500px;
    }

    .hero-image {
      flex: 1;
      display: flex;
      justify-content: flex-end;
      z-index: 2;
      position: relative;
    }

    /* Beautiful Image Placeholder styling */
    .placeholder-img {
      width: 100%;
      max-width: 500px;
      height: 600px;
      background: linear-gradient(45deg, #e7e5e4, #f5f5f4);
      border-radius: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #a8a29e;
      font-size: 1.2rem;
      font-weight: 500;
      box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
      border: 8px solid white;
      position: relative;
    }
    
    .placeholder-img::after {
      content: '';
    }

    /* ============================================================
       ABOUT US
    ============================================================ */
    .section {
      padding: 6rem 5%;
    }

    .about {
      background: #fff;
      display: flex;
      align-items: center;
      gap: 4rem;
    }

    .about-img {
      flex: 1;
      height: 500px;
      background: linear-gradient(45deg, #f3f4f6, #e5e7eb);
      border-radius: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #9ca3af;
      font-weight: 500;
      position: relative;
    }
    .about-img::after { content: ; }

    .about-content {
      flex: 1;
    }

    .section-title {
      font-size: 2.5rem;
      color: var(--secondary);
      margin-bottom: 1.5rem;
      position: relative;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      left: 0; bottom: -10px;
      width: 60px; height: 4px;
      background: var(--primary);
      border-radius: 2px;
    }

    .about-content p {
      color: var(--text-muted);
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
    }

    /* ============================================================
       CONTACT US
    ============================================================ */
    .contact {
      background: var(--secondary);
      color: #fff;
      text-align: center;
      padding: 6rem 5%;
      position: relative;
      overflow: hidden;
    }

    .contact .section-title {
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .contact .section-title::after {
      left: 50%;
      transform: translateX(-50%);
    }

    .contact-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      margin-top: 4rem;
      max-width: 1000px;
      margin-left: auto;
      margin-right: auto;
    }

    .contact-card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 2.5rem;
      border-radius: 16px;
      backdrop-filter: blur(10px);
      transition: transform 0.3s;
    }
    .contact-card:hover { transform: translateY(-5px); }

    .contact-card i {
      font-size: 2.5rem;
      color: var(--primary);
      margin-bottom: 1.5rem;
    }

    .contact-card h3 {
      font-family: 'Outfit', sans-serif;
      font-size: 1.2rem;
      margin-bottom: 0.5rem;
    }
    .contact-card p {
      color: rgba(255, 255, 255, 0.7);
      font-weight: 300;
    }

    /* ============================================================
       FOOTER
    ============================================================ */
    footer {
      background: #290f02;
      color: rgba(255,255,255,0.5);
      text-align: center;
      padding: 2rem;
      font-size: 0.9rem;
    }
    footer a { color: var(--primary); text-decoration: none; }

    @media (max-width: 768px) {
      .hero, .about { flex-direction: column; text-align: center; padding-top: 6rem; }
      .hero-image { width: 100%; margin-top: 2rem; }
      .placeholder-img { height: 300px; font-size: 1rem; }
      .about-img { height: 300px; width: 100%; margin-bottom: 2rem; }
      .hero-content h1 { font-size: 2.5rem; }
      .section-title::after { left: 50%; transform: translateX(-50%); }
      
      .mobile-toggle { display: block; background: transparent; border: none; font-size: 1.8rem; color: var(--secondary); cursor: pointer; }
      .nav-links { 
         position: absolute; top: 100%; left: 0; right: 0; 
         background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px);
         flex-direction: column; padding: 1.5rem; border-bottom: 1px solid var(--glass-border);
         gap: 1.5rem; display: none; text-align: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
      }
      .nav-links.active { display: flex; }
      .btn-order { width: 100%; display: block; }
    }
    @media (min-width: 769px) {
      .mobile-toggle { display: none; }
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav>
    <a href="#" class="logo">Sugar<span>landia</span></a>
    <button class="mobile-toggle" onclick="document.querySelector('.nav-links').classList.toggle('active')">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="nav-links">
      <a href="#home" onclick="document.querySelector('.nav-links').classList.remove('active')">Home</a>
      <a href="#about" onclick="document.querySelector('.nav-links').classList.remove('active')">About Us</a>
      <a href="#contact" onclick="document.querySelector('.nav-links').classList.remove('active')">Contact</a>
      <!-- Link to the store folder -->
      <a href="store/index.php" class="btn-order"><i class="fa-solid fa-bag-shopping"></i> Order Now</a>
    </div>
  </nav>

  <!-- Hero Section -->
  <section id="home" class="hero">
    <div class="hero-content">
      <h1>The Pride of Bacolod City</h1>
      <p>Experience the original, crispiest, and most delicious Barquillos in town. Baked fresh daily with love, using our decades-old family recipe.</p>
      <a href="store/index.php" style="display:inline-block; background:var(--secondary); color:#fff; padding:1rem 2rem; border-radius:50px; text-decoration:none; font-weight:600; margin-top:1rem; transition:0.3s;">
        Browse Products &rarr;
      </a>
    </div>
    <div class="hero-image">
      <div class="placeholder-img" style="background: url(food.jpg) center/cover no-repeat; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3);"></div>
    </div>
  </section>

  <!-- About Us Section -->
  <section id="about" class="section about">
    <div class="about-img" style="background: url(store.jpg) center/cover no-repeat; border-radius: 24px; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.2);"></div>
    <div class="about-content"> 
      <h2 class="section-title">Our Story</h2>
      <p>In the 1930s, <strong>Damiana Mercado</strong> — lovingly known as <em>Tia Dami</em> — founded Sugarlandia. She sold her homemade delicacies everywhere, especially at the Central Market of Bacolod. The name "Sugarlandia" was a perfect fit, as Negros has always been known as the sugar capital of the Philippines. With her family's unwavering support, the business grew steadily, and Tia Dami continued to add more beloved products along the way.</p>
      <p>Most known for their iconic <strong>piaya</strong> and <strong>barquillos</strong>, the tradition of crafting Tia Dami's treasured recipes now rests proudly on the shoulders of her grandchildren — who continue to bake the old-school way, preserving every technique and flavor that made Sugarlandia a legend.</p>
      
      <div style="background: rgba(217, 119, 6, 0.05); border-left: 4px solid var(--primary); padding: 1.2rem; margin-top: 1.5rem; border-radius: 0 8px 8px 0;">
        <h4 style="color: var(--secondary); margin-bottom: 0.5rem; font-family: 'Outfit', sans-serif; font-size: 1.1rem;"><i class="fa-solid fa-lightbulb" style="color: var(--primary);"></i> Did you know?</h4>
        <p style="margin-bottom: 0; font-size: 0.95rem;">As the true pioneers of the Bacolod <em>pasalubong</em> industry, Sugarlandia was established decades before other famous brands like BongBong's and Merzci even existed!</p>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="contact">
    <h2 class="section-title">Get In Touch</h2>
    <p style="color: rgba(255,255,255,0.7); max-width: 600px; margin: 1rem auto 0;">Have a question about bulk orders or our ingredients? We'd love to hear from you.</p>
    
    <div class="contact-grid">
      <!-- Visit Us Card (Google Maps Link) -->
      <a href="https://www.google.com/maps/place/Sugarlandia/@10.6632336,122.9386866,19z/data=!4m6!3m5!1s0x33aed0354669ff95:0x97f2cedfde68174b!8m2!3d10.6633016!4d122.9390492!16s%2Fg%2F1pzr3_lqg" target="_blank" class="contact-card" style="text-decoration: none; color: inherit; display: block;">
        <i class="fa-solid fa-location-dot"></i>
        <h3>Visit Us</h3>
        <p>Bacolod City,<br>Negros Occidental<br><span style="color:var(--primary); font-size:0.85rem; font-weight:600; margin-top:0.5rem; display:inline-block;">Open in Google Maps &rarr;</span></p>
      </a>

      <!-- Facebook Card -->
      <a href="https://www.facebook.com/Sugarlandia" target="_blank" class="contact-card" style="text-decoration: none; color: inherit; display: block;">
        <i class="fa-brands fa-facebook"></i>
        <h3>Facebook</h3>
        <p>Follow us for updates<br><span style="color:var(--primary); font-size:0.85rem; font-weight:600; margin-top:0.5rem; display:inline-block;">Visit Page &rarr;</span></p>
      </a>

      <!-- Call Us Card -->
      <div class="contact-card">
        <i class="fa-solid fa-phone"></i>
        <h3>Call Us</h3>
        <p>Tel: 435-0053 &bull; 468-0270<br></p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; 2026 Sugarlandia Barquillios. All rights reserved. | <a href="homepage.php">Admin Portal</a></p>
  </footer>

</body>
</html>

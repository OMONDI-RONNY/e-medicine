<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Medicine | Home</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background-color: #f0f0f0; /* Light gray background for better contrast */
        }
        .navbar {
            background-color: #008CBA;
        }
        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }
        .navbar-nav a {
            color: white;
            margin-left: 20px;
            transition: color 0.3s;
        }
        .navbar-nav a:hover {
            color: #f1f1f1;
        }
        /* CSS to show dropdown on hover */
.nav-item.dropdown:hover .dropdown-menu {
    display: block;
}

/* Dropdown styles */
.dropdown-menu {
    background-color: #008CBA; /* Set a background color */
    color: white; /* Set text color */
}

.dropdown-item {
    color: white; /* Text color for dropdown items */
}

.dropdown-item:hover {
    background-color: #005f7f; /* Darker shade on hover */
}



        /* Hero Section */
        .hero-section {
            background-image: url('resources/images/doc.jpg'); /* Replace with your background */
            background-size: cover;
            background-position: center;
            color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            text-align: center;
            position: relative;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 140, 186, 0.7); /* Overlay */
            z-index: 0;
        }
        .hero-content {
            z-index: 1;
            max-width: 800px;
            margin: auto;
        }
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 40px;
            transition: opacity 0.5s ease;
        }
        .hero-content .btn-custom {
            padding: 15px 30px;
            background-color: #f1f1f1;
            color: #008CBA;
            font-weight: bold;
            border: none;
            border-radius: 25px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .hero-content .btn-custom:hover {
            background-color: #008CBA;
            color: white;
            transform: translateY(-3px);
        }

        /* Services Section */
        .services-section {
            padding: 60px 0;
            background-color: #f9f9f9;
        }
        .services-section h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
        }
        .service-box {
            text-align: center;
            margin-bottom: 30px;
        }
        .service-box img {
            width: 100%;
            height: auto;
            border-radius: 15px;
        }
        .service-box h3 {
            margin-top: 15px;
            font-size: 1.5rem;
        }

        /* About Section */
        .about-section {
            padding: 60px 0;
            background-color: #fff;
        }
        .about-section h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 40px;
        }
        .about-section img {
            width: 100%;
            border-radius: 15px;
        }
        .about-text {
            padding: 20px;
        }

        /* Testimonials */
        .testimonials-section {
            background-color: #f1f1f1;
            padding: 60px 0;
        }
        .testimonials-section h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
        }
        .testimonials-slider {
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 30px; /* Spacing between testimonial cards */
            flex-wrap: wrap;
        }
        .testimonial-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin: 0 15px;
            transition: transform 0.3s, box-shadow 0.3s;
            max-width: 300px; /* Set a maximum width for the cards */
        }
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        .testimonial-card p {
            font-style: italic;
            color: #555;
        }

        .footer {
            background-color: #008CBA;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        
        .footer .container {
            display: flex;
            justify-content: space-between; /* Space between the sections */
            align-items: center; /* Align items vertically */
        }
        
        .footer-contact,
        .footer-address,
        .footer-links {
            text-align: left; /* Align text to the left */
        }
        
        .footer-links a {
            color: white; /* Link color */
            text-decoration: none; /* Remove underline from links */
        }
        
        .footer-links a:hover {
            text-decoration: underline; /* Underline on hover */
        }
        

        /* Dot Navigation */
        .dot-navigation {
            margin-top: 20px;
        }
        .dot {
            height: 20px; /* Increased size */
            width: 20px; /* Increased size */
            margin: 0 5px;
            background-color: #FFD700; /* Changed color */
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .dot:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">E-Medicine</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#services">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#testimonials">Testimonials</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> Login
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="loginDropdown">
                        <li><a class="dropdown-item" href="admin/login.php"><i class="fas fa-user-shield"></i> Admin</a></li>
                        <li><a class="dropdown-item" href="doctor/login.php"><i class="fas fa-user-md"></i> Doctor</a></li>
                        <li><a class="dropdown-item" href="patient/login.php"><i class="fas fa-user"></i> Patient</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>


    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="hero-content">
            <h1>Your Health, Our Priority</h1>
            <p id="dynamicText">Access world-class medical consultations and treatments from the comfort of your home.</p>
            <a href="#services" class="btn btn-custom">Get Started</a>
            <div class="dot-navigation">
                <span class="dot" onclick="changeText(1)"></span>
                <span class="dot" onclick="changeText(2)"></span>
                <span class="dot" onclick="changeText(3)"></span>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section" id="services">
        <div class="container">
            <h2>Our Services</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="service-box">
                        <img src="resources/images/consult.jpg" alt="Consultation Service">
                        <h3>Consultation</h3>
                        <p>Get professional advice and treatment plans from licensed doctors via video chat.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-box">
                        <img src="resources/images/pharmacy.jpg" alt="Pharmacy Service">
                        <h3>Pharmacy</h3>
                        <p>Order prescribed medications and have them delivered to your doorstep.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-box">
                        <img src="resources/images/medicalreport.jpg" alt="Healthcare Service">
                        <h3>Healthcare</h3>
                        <p>Access comprehensive health packages tailored to your needs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section" id="about">
        <div class="container">
            <h2>About Us</h2>
            <div class="row">
                <div class="col-lg-6">
                    <img src="resources/images/about.jpg" alt="About Us">
                </div>
                <div class="col-lg-6 about-text">
                    <p>At E-Medicine, we believe in making healthcare accessible to everyone. Our platform connects patients with experienced healthcare professionals to ensure quality care anytime, anywhere.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section" id="testimonials">
        <div class="container">
            <h2>What Our Clients Say</h2>
            <div class="testimonials-slider">
                <div class="testimonial-card">
                    <p>"E-Medicine changed my life! I was able to consult with a doctor without leaving my home."</p>
                    <strong>- Kamau K.</strong>
                </div>
                <div class="testimonial-card">
                    <p>"The service is excellent, and the doctors are very professional. Highly recommend!"</p>
                    <strong>- Ronny O.</strong>
                </div>
                <div class="testimonial-card">
                    <p>"Fast service and great support! I got my prescriptions delivered in no time."</p>
                    <strong>- James T.</strong>
                </div>
            </div>
        </div>
    </section>

    
    <!-- Footer -->
<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-contact">
            <h3>Contact Us:</h3>
            <p>Email: omoron37@gmail.com</p>
            <p>Phone: 0796471436</p>
        </div>
        <div class="footer-address">
            <h3>Physical Address:</h3>
            <p>41733-00100 Ridgeways Rd</p>
            <p>Nairobi, Kenya</p>
        </div>
        <div class="footer-links">
            <h3>Quick Links:</h3>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#testimonials">Testimonials</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </div>
    </div>
</footer>


    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const texts = [
            "Access world-class medical consultations and treatments from the comfort of your home.",
            "Connect with healthcare professionals instantly.",
            "Your health is our priority."
        ];

        let currentIndex = 0;
        const dynamicText = document.getElementById('dynamicText');
        const dots = document.querySelectorAll('.dot');

        function changeText(index) {
            currentIndex = index - 1; // Adjust index to match array
            updateText();
            resetInterval();
        }

        function updateText() {
            dynamicText.textContent = texts[currentIndex];
            dots.forEach((dot, i) => {
                dot.style.backgroundColor = i === currentIndex ? '#FFD700' : '#ccc'; // Active dot color
            });
        }

        function autoSlide() {
            currentIndex = (currentIndex + 1) % texts.length;
            updateText();
        }

        let interval = setInterval(autoSlide, 5000); // Change text every 5 seconds

        function resetInterval() {
            clearInterval(interval);
            interval = setInterval(autoSlide, 5000); // Restart interval on manual change
        }

        // Initialize first text and dot color
        updateText();
    </script>
</body>
</html>

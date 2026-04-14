<?php
define('BASEPATH', true);
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | KGN ENTERPRISES | Leading Manpower Services Provider in Pune</title>
    <link rel="icon" type="image/x-icon" href="uploads/settings/logo.png">
    
    <!-- CDN CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Match Index.php -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&family=Montserrat:wght@600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        * { 
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #333333 100%);
        }
        
        /* Scrollbar Hidden like Index.php */
        body, html {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        
        body::-webkit-scrollbar, html::-webkit-scrollbar {
            display: none;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }
        
        .animate-fade-in-delay {
            animation: fadeIn 1s ease-out 0.3s both;
        }
        
        .animate-fade-in-delay-2 {
            animation: fadeIn 1s ease-out 0.6s both;
        }
        
        /* Card Hover Effects */
        .feature-card {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .team-card {
            transition: all 0.3s ease;
        }
        
        .team-card:hover {
            transform: translateY(-5px);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Stats Counter */
        .stat-counter {
            transition: all 0.5s ease;
        }
    </style>
    
    <!-- SEO Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="description" content="Learn about KGN ENTERPRISES - Pune's leading manpower services provider. 5+ years of excellence in staffing, facility management, and workforce solutions.">
    <meta name="keywords" content="manpower company Pune, staffing solutions, workforce management, about KGN ENTERPRISES, manpower services">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="About KGN ENTERPRISES | Premium Manpower Services">
    <meta property="og:description" content="Leading manpower services provider with 5+ years experience in Pune and Maharashtra">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://kgcenterprises.com/about.php">
    <meta property="og:image" content="https://kgcenterprises.com/uploads/settings/logo.png">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="About KGN ENTERPRISES">
    <meta name="twitter:description" content="Your trusted partner for premium manpower solutions">
    <meta name="twitter:image" content="https://kgcenterprises.com/uploads/settings/logo.png">
</head>
<body class="bg-gray-50">
    <!-- Navigation - Same as Index.php -->
    <nav class="bg-gradient-to-r from-black to-gray-900 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                <!-- Logo and Company Name -->
                <div class="flex items-center">
                    <a href="index.php" class="text-white text-2xl font-bold flex items-center">
                        <img src="uploads/settings/logo.png" alt="KGN ENTERPRISES" class="h-12 mr-3">
                        <span class="hidden md:block">KGN ENTERPRISES</span>
                        <span class="md:hidden flex flex-col items-start leading-tight">
                            <span class="text-lg">KGN</span><span class="text-lg">ENTERPRISES</span>
                        </span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex space-x-8">
                    <a href="index.php" class="text-white hover:text-orange-400 transition font-bold">
                        Home
                    </a>
                    <a href="about.php" class="text-white hover:text-orange-400 transition text-orange-400 font-bold">
                        About Us
                    </a>
                    <a href="services.php" class="text-white hover:text-orange-400 transition font-bold">
                        Services
                    </a>
                    <a href="careers.php" class="text-white hover:text-orange-400 transition font-bold">
                        Careers
                    </a>
                    <a href="clients.php" class="text-white hover:text-orange-400 transition font-bold">
                        Clients
                    </a>
                    <a href="gallery.php" class="text-white hover:text-orange-400 transition font-bold">
                        Gallery
                    </a>
                    <a href="testimonials.php" class="text-white hover:text-orange-400 transition font-bold">
                        Testimonials
                    </a>
                    <a href="contact.php" class="text-white hover:text-orange-400 transition font-bold">
                        Contact Us
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="lg:hidden text-white text-2xl focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-gray-900 pb-4">
            <div class="container mx-auto px-4 space-y-3">
                <a href="index.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-home mr-2"></i> Home
                </a>
                <a href="about.php" class="block text-white hover:text-orange-400 transition py-2 text-orange-400 font-bold">
                    <i class="fas fa-building mr-2"></i> About Us
                </a>
                <a href="services.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-briefcase mr-2"></i> Services
                </a>
                <a href="careers.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-user-tie mr-2"></i> Careers
                </a>
                <a href="clients.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-handshake mr-2"></i> Clients
                </a>
                <a href="gallery.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-images mr-2"></i> Gallery
                </a>
                <a href="testimonials.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-quote-left mr-2"></i> Testimonials
                </a>
                <a href="contact.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-envelope mr-2"></i> Contact Us
                </a>
            </div>
        </div>
    </nav>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>

    <!-- Hero Section -->
    <section class="relative hero-gradient text-white py-20 lg:py-32 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="grid grid-cols-4 md:grid-cols-8 gap-8 mt-16">
                <div class="col-span-1 bg-white rounded-lg h-24 transform rotate-12"></div>
                <div class="col-span-1 bg-orange-400 rounded-lg h-32 transform -rotate-12 mt-8"></div>
                <div class="col-span-1 bg-white rounded-lg h-20 transform rotate-6 mt-12"></div>
                <div class="col-span-1 bg-orange-400 rounded-lg h-28 transform -rotate-6 mt-4"></div>
                <div class="col-span-1 bg-white rounded-lg h-36 transform rotate-3 mt-6"></div>
                <div class="col-span-1 bg-orange-400 rounded-lg h-24 transform -rotate-3 mt-2"></div>
                <div class="col-span-1 bg-white rounded-lg h-32 transform rotate-6 mt-8"></div>
                <div class="col-span-1 bg-orange-400 rounded-lg h-20 transform -rotate-12 mt-4"></div>
            </div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-6xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl lg:text-7xl font-bold mb-6 animate-fade-in">
                    About <span class="gradient-text">KGN ENTERPRISES</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 animate-fade-in-delay">
                    Your Trusted Partner in Premium Manpower Solutions Since 2019
                </p>
                <p class="text-lg text-gray-300 max-w-3xl mx-auto mb-12 animate-fade-in-delay-2 font-medium">
                    Leading the way in providing quality staffing solutions with 5+ years of excellence and 1000+ successful deployments
                </p>
            </div>
        </div>
        
        <!-- Wave Divider -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 105C120 90 240 60 360 50C480 40 600 50 720 55C840 60 960 60 1080 65C1200 70 1320 80 1380 85L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="#F3F4F6"/>
            </svg>
        </div>
    </section>

    <!-- Company Story Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="animate-fade-in">
                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                            Our <span class="gradient-text">Journey</span>
                        </h2>
                        <div class="w-24 h-2 bg-orange-400 rounded-full mb-8"></div>
                        
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed font-medium">
                            Founded in 2019, <strong class="text-gray-900">KGN ENTERPRISES</strong> has emerged as a premier manpower services provider in Pune and across Maharashtra. What started as a small staffing agency has grown into a comprehensive workforce solutions partner for businesses across various industries.
                        </p>
                        
                        <p class="text-lg text-gray-600 mb-6 leading-relaxed font-medium">
                            Our journey is marked by consistent growth, client satisfaction, and an unwavering commitment to quality. We've successfully placed <strong class="text-gray-900">1000+ professionals</strong> in diverse roles, from industrial workers to IT professionals and facility management staff.
                        </p>
                        
                        <p class="text-lg text-gray-600 mb-8 leading-relaxed font-medium">
                            Today, we serve <strong class="text-gray-900">50+ corporate clients</strong> and have established ourselves as a trusted name in manpower services, known for our reliability, professionalism, and customer-centric approach.
                        </p>
                        
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                            <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-200">
                                <div class="text-3xl font-bold text-orange-500 mb-2" data-count="5+">0</div>
                                <div class="text-gray-700 font-bold">Years Experience</div>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-200">
                                <div class="text-3xl font-bold text-orange-500 mb-2" data-count="50+">0</div>
                                <div class="text-gray-700 font-bold">Happy Clients</div>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-200">
                                <div class="text-3xl font-bold text-orange-500 mb-2" data-count="1000+">0</div>
                                <div class="text-gray-700 font-bold">Workers Placed</div>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-200">
                                <div class="text-3xl font-bold text-orange-500 mb-2" data-count="24">0</div>
                                <div class="text-gray-700 font-bold">Hour Deployment</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="animate-fade-in-delay">
                        <div class="bg-gradient-to-br from-gray-900 to-black rounded-3xl p-8 shadow-2xl text-white">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center hover:bg-white/20 transition duration-300">
                                    <div class="w-16 h-16 bg-orange-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-bullseye text-2xl text-orange-400"></i>
                                    </div>
                                    <h3 class="text-xl font-bold mb-3">Our Mission</h3>
                                    <p class="text-gray-300 text-sm font-medium">
                                        To provide reliable, efficient, and premium manpower solutions that empower businesses to achieve operational excellence.
                                    </p>
                                </div>
                                
                                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center hover:bg-white/20 transition duration-300">
                                    <div class="w-16 h-16 bg-green-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-eye text-2xl text-green-400"></i>
                                    </div>
                                    <h3 class="text-xl font-bold mb-3">Our Vision</h3>
                                    <p class="text-gray-300 text-sm font-medium">
                                        To be the most trusted manpower services partner in India, recognized for excellence and client success.
                                    </p>
                                </div>
                                
                                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center hover:bg-white/20 transition duration-300">
                                    <div class="w-16 h-16 bg-purple-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-handshake text-2xl text-purple-400"></i>
                                    </div>
                                    <h3 class="text-xl font-bold mb-3">Our Values</h3>
                                    <p class="text-gray-300 text-sm font-medium">
                                        Integrity, Quality, Reliability, and Customer Satisfaction are our core values.
                                    </p>
                                </div>
                                
                                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center hover:bg-white/20 transition duration-300">
                                    <div class="w-16 h-16 bg-yellow-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-trophy text-2xl text-yellow-400"></i>
                                    </div>
                                    <h3 class="text-xl font-bold mb-3">Our Promise</h3>
                                    <p class="text-gray-300 text-sm font-medium">
                                        Quality manpower, timely delivery, and continuous support for smooth business operations.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Why <span class="gradient-text">Choose Us?</span>
                </h2>
                <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                    We stand out in the manpower industry through our commitment to excellence and customer-centric approach
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Feature 1 -->
                <div class="feature-card bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-200 animate-fade-in">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-gray-200 transition duration-300">
                        <i class="fas fa-user-check text-3xl text-gray-900"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Verified Workforce</h3>
                    <p class="text-gray-600 leading-relaxed font-medium">
                        All our manpower undergoes thorough background verification, skill assessment, and quality checks to ensure reliability.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-200 animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-gray-200 transition duration-300">
                        <i class="fas fa-clock text-3xl text-gray-900"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Quick Deployment</h3>
                    <p class="text-gray-600 leading-relaxed font-medium">
                        We understand urgency. Our streamlined process ensures quick deployment of manpower within 24-48 hours.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-200 animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-gray-200 transition duration-300">
                        <i class="fas fa-headset text-3xl text-gray-900"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">24/7 Support</h3>
                    <p class="text-gray-600 leading-relaxed font-medium">
                        Round-the-clock customer support to address your concerns and ensure smooth operations at all times.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-200 animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-gray-200 transition duration-300">
                        <i class="fas fa-rupee-sign text-3xl text-gray-900"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Cost-Effective</h3>
                    <p class="text-gray-600 leading-relaxed font-medium">
                        Competitive pricing without compromising on quality. Get the best value for your manpower investment.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-200 animate-fade-in" style="animation-delay: 0.4s;">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-gray-200 transition duration-300">
                        <i class="fas fa-shield-alt text-3xl text-gray-900"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Compliance Ready</h3>
                    <p class="text-gray-600 leading-relaxed font-medium">
                        Fully compliant with all labor laws and regulations. Complete documentation and legal support provided.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-200 animate-fade-in" style="animation-delay: 0.5s;">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-gray-200 transition duration-300">
                        <i class="fas fa-chart-line text-3xl text-gray-900"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Quality Assurance</h3>
                    <p class="text-gray-600 leading-relaxed font-medium">
                        Rigorous quality checks and performance monitoring to ensure consistent delivery of excellent services.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Leadership Team Section -->
    <section class="py-16 bg-white" id="team">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Our <span class="gradient-text">Leadership</span>
                </h2>
                <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                    Meet the experienced professionals driving KGN ENTERPRISES towards excellence in manpower services
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Team Member 1 - Javed Turuk -->
                <div class="team-card bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-200">
                    <div class="w-40 h-40 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center mx-auto mb-6 overflow-hidden">
                        <img src="uploads/settings/javed.png" alt="Mr. Javed Turuk" class="w-full h-full object-cover" 
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop'">
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Mr. Javed Turuk</h3>
                    <p class="text-orange-500 font-bold mb-4">Founder & Director</p>
                    <p class="text-gray-600 text-sm leading-relaxed font-medium mb-4">
                        With over 10 years of experience in the manpower industry, Mr. Javed leads KGN ENTERPRISES with vision and expertise in staffing solutions.
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a href="https://www.instagram.com/javed__9670" class="text-gray-400 hover:text-orange-500 transition duration-300" target="_blank">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                    </div>
                </div>

                <!-- Team Member 2 - Sakib Shaikh -->
                <div class="team-card bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-200">
                    <div class="w-40 h-40 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center mx-auto mb-6 overflow-hidden">
                        <img src="uploads/settings/sakib.png" alt="Mr. Sakib Shaikh" class="w-full h-full object-cover"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop'">
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Mr. Sakib Shaikh</h3>
                    <p class="text-orange-500 font-bold mb-4">Business Development Manager</p>
                    <p class="text-gray-600 text-sm leading-relaxed font-medium mb-4">
                        Expertise in operations management and client relations with 8+ years of experience in manpower deployment and facility management.
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a href="https://www.linkedin.com/in/sakib-shaikh-b5755b397" class="text-gray-400 hover:text-orange-500 transition duration-300" target="_blank">
                            <i class="fab fa-linkedin-in text-lg"></i>
                        </a>
                        <a href="https://www.instagram.com/sakib__.2006" class="text-gray-400 hover:text-orange-500 transition duration-300" target="_blank">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                    </div>
                </div>

                <!-- Team Member 3 - Swapnil Salunke -->
                <div class="team-card bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-200">
                    <div class="w-40 h-40 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center mx-auto mb-6 overflow-hidden">
                        <img src="uploads/settings/swapnil.png" alt="Mr. Swapnil Salunke" class="w-full h-full object-cover"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop'">
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Mr. Swapnil Salunke</h3>
                    <p class="text-orange-500 font-bold mb-4">Operations Manager</p>
                    <p class="text-gray-600 text-sm leading-relaxed font-medium mb-4">
                        Specialized in talent acquisition and workforce management with expertise in screening and deploying quality manpower.
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a href="https://www.instagram.com/swapnil__9004" class="text-gray-400 hover:text-orange-500 transition duration-300" target="_blank">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Certifications Section -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Our <span class="gradient-text">Certifications</span>
                </h2>
                <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                    Quality assurance through recognized certifications and registrations
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <!-- ISO Certification -->
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-200 transform hover:-translate-y-2 transition duration-300">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-award text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">ISO Certified</h3>
                    <p class="text-gray-600 font-medium">Quality Management System certified ensuring standardized processes and services.</p>
                </div>

                <!-- MSME Registered -->
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-200 transform hover:-translate-y-2 transition duration-300">
                    <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-building text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">MSME Registered</h3>
                    <p class="text-gray-600 font-medium">Recognized by Ministry of Micro, Small & Medium Enterprises, Government of India.</p>
                </div>

                <!-- Made in India -->
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-200 transform hover:-translate-y-2 transition duration-300">
                    <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-flag text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Made in India</h3>
                    <p class="text-gray-600 font-medium">Proudly supporting and promoting Indian workforce and local employment.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-gray-900 via-black to-gray-900 text-white relative overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">
                    Ready to Partner With Us?
                </h2>
                <p class="text-xl text-gray-300 mb-8 font-medium">
                    Join 50+ satisfied clients who trust KGN ENTERPRISES for their manpower needs
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <a href="contact.php" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition shadow-lg transform hover:scale-105 duration-300 inline-flex items-center">
                        <i class="fas fa-handshake mr-3"></i> Get Started Today
                    </a>
                    <a href="tel:+919881901568" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-gray-900 transition transform hover:scale-105 duration-300 inline-flex items-center">
                        <i class="fas fa-phone mr-3"></i> Call +91 9881901568
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Counter Animation Script -->
    <script>
    // Animate counter
    function animateCounter(element) {
        const target = element.getAttribute('data-count');
        const suffix = target.includes('+') ? '+' : '';
        const num = parseInt(target);
        
        let count = 0;
        const increment = num / 50;
        const timer = setInterval(() => {
            count += increment;
            if (count >= num) {
                element.textContent = num + suffix;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(count) + suffix;
            }
        }, 30);
    }

    // Trigger animation when element is in view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counters = entry.target.querySelectorAll('[data-count]');
                counters.forEach(counter => {
                    if (!counter.classList.contains('counted')) {
                        counter.classList.add('counted');
                        animateCounter(counter);
                    }
                });
            }
        });
    });

    // Observe stats section
    const statsSection = document.querySelector('section.bg-white');
    if (statsSection) {
        observer.observe(statsSection);
    }
    </script>

    <!-- Footer - Same as Index.php -->
    <footer class="bg-gray-900 text-white mt-20">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <h3 class="text-2xl font-bold mb-4 flex items-center">
                        <img src="uploads/settings/logo.png" alt="KGN ENTERPRISES" class="h-10 mr-3">
                        KGN ENTERPRISES
                    </h3>
                    <p class="text-gray-300 mb-4 font-medium">
                        Leading provider of premium manpower solutions across India. Trusted by 50+ clients.
                    </p>
                    <div class="flex space-x-4">
                        <a href="https://github.com/sakib92s" class="text-gray-300 hover:text-orange-400 transition" target="_blank"><i class="fab fa-github text-2xl"></i></a>
                        <a href="https://www.linkedin.com/in/sakib-shaikh-b5755b397" class="text-gray-300 hover:text-orange-400 transition" target="_blank"><i class="fab fa-linkedin text-2xl"></i></a>
                        <a href="https://www.instagram.com/sakib__.2006" class="text-gray-300 hover:text-orange-400 transition" target="_blank"><i class="fab fa-instagram text-2xl"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="index.php" class="hover:text-orange-400 transition font-medium"><i class="fas fa-chevron-right mr-2"></i> Home</a></li>
                        <li><a href="about.php" class="hover:text-orange-400 transition font-medium"><i class="fas fa-chevron-right mr-2"></i> About Us</a></li>
                        <li><a href="services.php" class="hover:text-orange-400 transition font-medium"><i class="fas fa-chevron-right mr-2"></i> Services</a></li>
                        <li><a href="careers.php" class="hover:text-orange-400 transition font-medium"><i class="fas fa-chevron-right mr-2"></i> Careers</a></li>
                        <li><a href="contact.php" class="hover:text-orange-400 transition font-medium"><i class="fas fa-chevron-right mr-2"></i> Contact Us</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Our Services</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><i class="fas fa-check-circle mr-2 text-orange-400"></i> Skilled Manpower</li>
                        <li><i class="fas fa-check-circle mr-2 text-orange-400"></i> Unskilled Manpower</li>
                        <li><i class="fas fa-check-circle mr-2 text-orange-400"></i> Facility Management</li>
                        <li><i class="fas fa-check-circle mr-2 text-orange-400"></i> IT Professionals</li>
                        <li><i class="fas fa-check-circle mr-2 text-orange-400"></i> Housekeeping</li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Contact Us</h3>
                    <div class="space-y-3 text-gray-300">
                        <p class="flex items-start font-medium">
                            <i class="fas fa-map-marker-alt mr-3 mt-1 text-orange-400"></i>
                            185 Ground, Javed Turuk, Shendewadi Phata,Turukwadi, Kadus, Khed, Pune - 412404, India
                        </p>
                        <p class="flex items-center font-medium">
                            <i class="fas fa-phone mr-3 text-orange-400"></i>
                            +91 9881901568<br>
                            +91 9423042591
                        </p>
                        <p class="flex items-center font-medium">
                            <i class="fas fa-envelope mr-3 text-orange-400"></i>
                            kgnenterprises9670@gmail.com
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-300">
                <p class="font-medium">&copy; <?php echo date('Y'); ?> KGN ENTERPRISES. All rights reserved.</p>
                <p class="mt-2 font-medium">ISO | MSME | Made in India Certified</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scroll-top" class="fixed bottom-8 right-8 bg-gray-900 text-white p-3 rounded-full shadow-lg hover:bg-black transition hidden border border-gray-700">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // Scroll to top functionality
        window.addEventListener('scroll', function() {
            const scrollTop = document.getElementById('scroll-top');
            if (window.pageYOffset > 300) {
                scrollTop.classList.remove('hidden');
            } else {
                scrollTop.classList.add('hidden');
            }
        });

        document.getElementById('scroll-top').addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Animate elements on scroll
        const animateOnScroll = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        // Observe all animated elements
        document.querySelectorAll('.animate-fade-in, .animate-fade-in-delay, .animate-fade-in-delay-2').forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            animateOnScroll.observe(element);
        });
    </script>
</body>
</html>
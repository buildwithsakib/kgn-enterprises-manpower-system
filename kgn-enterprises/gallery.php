<?php
define('BASEPATH', true);
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get gallery images from database
$gallery_items = [];
$categories = [];

try {
    // Get all active gallery items
    $stmt = $db->prepare("SELECT * FROM gallery WHERE is_active = TRUE ORDER BY created_at DESC");
    $stmt->execute();
    $gallery_items = $stmt->fetchAll();
    
    // Get unique categories
    $stmt = $db->prepare("SELECT DISTINCT category FROM gallery WHERE is_active = TRUE AND category IS NOT NULL");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch(PDOException $e) {
    error_log("Gallery data error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery | KGN ENTERPRISES | Premium Manpower Services in Pune</title>
    <link rel="icon" type="image/x-icon" href="uploads/settings/logo.png">
    
    <!-- CDN CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
    
    <!-- Google Fonts - Match About.php -->
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
        
        /* Scrollbar Hidden like About.php */
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
        .gallery-card {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: white;
        }
        
        .gallery-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        /* Filter Button Styles */
        .filter-btn {
            transition: all 0.3s ease;
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            color: white;
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Image Overlay Effects */
        .gallery-image-container {
            position: relative;
            overflow: hidden;
        }
        
        .gallery-image-container img {
            transition: transform 0.5s ease;
        }
        
        .gallery-image-container:hover img {
            transform: scale(1.1);
        }
        
        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .gallery-image-container:hover .gallery-overlay {
            opacity: 1;
        }
        
        /* Category Badge */
        .category-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: bold;
            z-index: 10;
        }
        
        /* Lightbox Customization */
        .lb-data .lb-caption {
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .lb-close {
            background: #f97316 !important;
        }
        
        .lb-nav a.lb-prev,
        .lb-nav a.lb-next {
            background: #f97316 !important;
        }
    </style>
    
    <!-- SEO Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="description" content="Explore KGN ENTERPRISES gallery showcasing our workforce deployments, team activities, and successful manpower projects in Pune and Maharashtra.">
    <meta name="keywords" content="manpower gallery Pune, workforce deployment, team activities gallery, project showcase, facility management gallery">
    
    <!-- Open Graph Meta -->
    <meta property="og:title" content="Gallery | KGN ENTERPRISES | Workforce & Project Showcase">
    <meta property="og:description" content="Explore our gallery showcasing KGN ENTERPRISES workforce deployments, team activities, and successful projects in manpower services.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="KGN ENTERPRISES">
    <meta property="og:image" content="uploads/settings/logo.png">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Gallery | KGN ENTERPRISES">
    <meta name="twitter:description" content="Visual showcase of our manpower solutions and successful projects">
    <meta name="twitter:image" content="uploads/settings/logo.png">
</head>
<body class="bg-gray-50">
    <!-- Navigation - Same as About.php -->
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
                    <a href="about.php" class="text-white hover:text-orange-400 transition font-bold">
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
                    <a href="gallery.php" class="text-white hover:text-orange-400 transition text-orange-400 font-bold">
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
                <a href="about.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
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
                <a href="gallery.php" class="block text-white hover:text-orange-400 transition py-2 text-orange-400 font-bold">
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
                    Our <span class="gradient-text">Gallery</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 animate-fade-in-delay">
                    Showcasing Excellence in Workforce Solutions
                </p>
                <p class="text-lg text-gray-300 max-w-3xl mx-auto mb-12 animate-fade-in-delay-2 font-medium">
                    Explore our journey through pictures - from successful deployments to team celebrations and client partnerships in Pune and Maharashtra
                </p>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-3xl mx-auto animate-fade-in-delay-2">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center">
                        <div class="text-3xl font-bold text-white mb-2">500+</div>
                        <div class="text-gray-300 font-medium">Successful Deployments</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center">
                        <div class="text-3xl font-bold text-white mb-2">50+</div>
                        <div class="text-gray-300 font-medium">Happy Clients</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center">
                        <div class="text-3xl font-bold text-white mb-2">1000+</div>
                        <div class="text-gray-300 font-medium">Workers Placed</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center">
                        <div class="text-3xl font-bold text-white mb-2">5+</div>
                        <div class="text-gray-300 font-medium">Years Experience</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Wave Divider -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 105C120 90 240 60 360 50C480 40 600 50 720 55C840 60 960 60 1080 65C1200 70 1320 80 1380 85L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="#F3F4F6"/>
            </svg>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16 animate-fade-in">
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                        Our <span class="gradient-text">Work & Activities</span>
                    </h2>
                    <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                    <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                        A visual journey through our successful projects, team events, and workforce deployments that define our commitment to excellence
                    </p>
                </div>

                <!-- Gallery Filter -->
                <div class="flex flex-wrap justify-center gap-4 mb-16 animate-fade-in-delay">
                    <button class="filter-btn active bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-full font-bold transition-all duration-300 shadow-lg hover:shadow-xl" data-filter="all">
                        <i class="fas fa-th-large mr-2"></i> All Photos
                    </button>
                    <button class="filter-btn bg-gray-100 text-gray-800 px-6 py-3 rounded-full font-bold transition-all duration-300 shadow-lg hover:shadow-xl hover:bg-gray-200" data-filter="workforce">
                        <i class="fas fa-users mr-2"></i> Workforce
                    </button>
                    <button class="filter-btn bg-gray-100 text-gray-800 px-6 py-3 rounded-full font-bold transition-all duration-300 shadow-lg hover:shadow-xl hover:bg-gray-200" data-filter="events">
                        <i class="fas fa-calendar-alt mr-2"></i> Events
                    </button>
                    <button class="filter-btn bg-gray-100 text-gray-800 px-6 py-3 rounded-full font-bold transition-all duration-300 shadow-lg hover:shadow-xl hover:bg-gray-200" data-filter="team">
                        <i class="fas fa-user-friends mr-2"></i> Team
                    </button>
                    <button class="filter-btn bg-gray-100 text-gray-800 px-6 py-3 rounded-full font-bold transition-all duration-300 shadow-lg hover:shadow-xl hover:bg-gray-200" data-filter="facility">
                        <i class="fas fa-building mr-2"></i> Facilities
                    </button>
                </div>

                <!-- Gallery Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto" id="gallery-grid">
                    <!-- Gallery Item 1 -->
                    <div class="gallery-card rounded-2xl shadow-lg overflow-hidden border border-gray-200 animate-fade-in" data-category="workforce">
                        <div class="gallery-image-container h-80 relative">
                            <!-- Image from Unsplash -->
                            <img src="https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                                 alt="Skilled Workforce Deployment" 
                                 class="w-full h-full object-cover">
                            
                            <!-- Category Badge -->
                            <div class="category-badge">Workforce</div>
                            
                            <!-- Overlay -->
                            <div class="gallery-overlay">
                                <a href="https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1600&q=80" 
                                   data-lightbox="gallery" 
                                   data-title="Skilled Workforce Deployment - Our skilled workforce at client manufacturing unit in Pune"
                                   class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-4 rounded-full hover:from-orange-600 hover:to-orange-700 transition transform hover:scale-110">
                                    <i class="fas fa-search-plus text-xl"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Skilled Workforce Deployment</h3>
                            <p class="text-gray-600 font-medium">Our skilled workforce at client manufacturing unit in Pune</p>
                            <div class="mt-4 flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>March 2024</span>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Item 2 -->
                    <div class="gallery-card rounded-2xl shadow-lg overflow-hidden border border-gray-200 animate-fade-in" data-category="events" style="animation-delay: 0.1s">
                        <div class="gallery-image-container h-80 relative">
                            <!-- Image from Unsplash -->
                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                                 alt="Annual Team Gathering" 
                                 class="w-full h-full object-cover">
                            
                            <!-- Category Badge -->
                            <div class="category-badge">Events</div>
                            
                            <!-- Overlay -->
                            <div class="gallery-overlay">
                                <a href="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1600&q=80" 
                                   data-lightbox="gallery" 
                                   data-title="Annual Team Gathering - Annual team meet and strategic planning session"
                                   class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-4 rounded-full hover:from-orange-600 hover:to-orange-700 transition transform hover:scale-110">
                                    <i class="fas fa-search-plus text-xl"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Annual Team Gathering</h3>
                            <p class="text-gray-600 font-medium">Annual team meet and strategic planning session for upcoming projects</p>
                            <div class="mt-4 flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>December 2023</span>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Item 3 -->
                    <div class="gallery-card rounded-2xl shadow-lg overflow-hidden border border-gray-200 animate-fade-in" data-category="facility" style="animation-delay: 0.2s">
                        <div class="gallery-image-container h-80 relative">
                            <!-- Image from Unsplash -->
                            <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                                 alt="Facility Management Team" 
                                 class="w-full h-full object-cover">
                            
                            <!-- Category Badge -->
                            <div class="category-badge">Facility</div>
                            
                            <!-- Overlay -->
                            <div class="gallery-overlay">
                                <a href="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1600&q=80" 
                                   data-lightbox="gallery" 
                                   data-title="Facility Management Team - Professional facility management staff on duty at corporate office"
                                   class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-4 rounded-full hover:from-orange-600 hover:to-orange-700 transition transform hover:scale-110">
                                    <i class="fas fa-search-plus text-xl"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Facility Management Team</h3>
                            <p class="text-gray-600 font-medium">Professional facility management staff on duty at corporate office</p>
                            <div class="mt-4 flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>February 2024</span>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Item 4 -->
                    <div class="gallery-card rounded-2xl shadow-lg overflow-hidden border border-gray-200 animate-fade-in" data-category="workforce" style="animation-delay: 0.3s">
                        <div class="gallery-image-container h-80 relative">
                            <!-- Image from Unsplash -->
                            <img src="https://images.unsplash.com/photo-1577962917302-cd874c4e31d2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                                 alt="Industrial Workforce" 
                                 class="w-full h-full object-cover">
                            
                            <!-- Category Badge -->
                            <div class="category-badge">Workforce</div>
                            
                            <!-- Overlay -->
                            <div class="gallery-overlay">
                                <a href="https://images.unsplash.com/photo-1577962917302-cd874c4e31d2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1600&q=80" 
                                   data-lightbox="gallery" 
                                   data-title="Industrial Workforce - Industrial workers at manufacturing plant in Maharashtra"
                                   class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-4 rounded-full hover:from-orange-600 hover:to-orange-700 transition transform hover:scale-110">
                                    <i class="fas fa-search-plus text-xl"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Industrial Workforce</h3>
                            <p class="text-gray-600 font-medium">Industrial workers at manufacturing plant in Maharashtra</p>
                            <div class="mt-4 flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>January 2024</span>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Item 5 -->
                    <div class="gallery-card rounded-2xl shadow-lg overflow-hidden border border-gray-200 animate-fade-in" data-category="team" style="animation-delay: 0.4s">
                        <div class="gallery-image-container h-80 relative">
                            <!-- Image from Unsplash -->
                            <img src="https://images.unsplash.com/photo-1559028012-481c04fa702d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                                 alt="Office Administration Team" 
                                 class="w-full h-full object-cover">
                            
                            <!-- Category Badge -->
                            <div class="category-badge">Team</div>
                            
                            <!-- Overlay -->
                            <div class="gallery-overlay">
                                <a href="https://images.unsplash.com/photo-1559028012-481c04fa702d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1600&q=80" 
                                   data-lightbox="gallery" 
                                   data-title="Office Administration Team - Our skilled office administration professionals at work"
                                   class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-4 rounded-full hover:from-orange-600 hover:to-orange-700 transition transform hover:scale-110">
                                    <i class="fas fa-search-plus text-xl"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Office Administration Team</h3>
                            <p class="text-gray-600 font-medium">Our skilled office administration professionals at KGN ENTERPRISES</p>
                            <div class="mt-4 flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>November 2023</span>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Item 6 -->
                    <div class="gallery-card rounded-2xl shadow-lg overflow-hidden border border-gray-200 animate-fade-in" data-category="events" style="animation-delay: 0.5s">
                        <div class="gallery-image-container h-80 relative">
                            <!-- Image from Unsplash -->
                            <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                                 alt="Training Session" 
                                 class="w-full h-full object-cover">
                            
                            <!-- Category Badge -->
                            <div class="category-badge">Events</div>
                            
                            <!-- Overlay -->
                            <div class="gallery-overlay">
                                <a href="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1600&q=80" 
                                   data-lightbox="gallery" 
                                   data-title="Training Session - Skill development training for workforce in Pune"
                                   class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-4 rounded-full hover:from-orange-600 hover:to-orange-700 transition transform hover:scale-110">
                                    <i class="fas fa-search-plus text-xl"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Training Session</h3>
                            <p class="text-gray-600 font-medium">Skill development training for workforce in Pune office</p>
                            <div class="mt-4 flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>October 2023</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Database Gallery Section -->
    <?php if (count($gallery_items) > 0): ?>
    <section class="py-16 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                        Database <span class="gradient-text">Gallery</span>
                    </h2>
                    <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                    <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                        Real photos from our projects and activities stored in our database
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($gallery_items as $item): ?>
                    <div class="gallery-card rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                        <div class="gallery-image-container h-64 relative">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-gray-900 to-black flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-image text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-gray-300 font-medium"><?php echo htmlspecialchars($item['title']); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($item['category'])): ?>
                                <div class="category-badge"><?php echo htmlspecialchars($item['category']); ?></div>
                            <?php endif; ?>
                            
                            <div class="gallery-overlay">
                                <a href="<?php echo !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : '#'; ?>" 
                                   data-lightbox="database-gallery" 
                                   data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                   class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-4 rounded-full hover:from-orange-600 hover:to-orange-700 transition transform hover:scale-110">
                                    <i class="fas fa-search-plus text-xl"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <?php if (!empty($item['description'])): ?>
                                <p class="text-gray-600 text-sm font-medium"><?php echo htmlspecialchars($item['description']); ?></p>
                            <?php endif; ?>
                            <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span><?php echo date('F Y', strtotime($item['created_at'])); ?></span>
                                </div>
                                <?php if (!empty($item['location'])): ?>
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        <span><?php echo htmlspecialchars($item['location']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-gray-900 via-black to-gray-900 text-white relative overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">Ready to Transform Your Workforce?</h2>
                <p class="text-xl text-gray-300 mb-8 font-medium">
                    Let's discuss how our manpower solutions can drive your business success in Pune
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <a href="contact.php?source=gallery" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition shadow-lg transform hover:scale-105 duration-300 inline-flex items-center">
                        <i class="fas fa-paper-plane mr-3"></i> Get Free Consultation
                    </a>
                    <a href="tel:+919881901568" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-gray-900 transition transform hover:scale-105 duration-300 inline-flex items-center">
                        <i class="fas fa-phone mr-3"></i> Call +91 9881901568
                    </a>
                </div>
                
                <!-- Trust Indicators -->
                <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-3xl mx-auto">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-400 mb-2">24/7</div>
                        <div class="text-gray-300 text-sm font-medium">Support</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-400 mb-2">50+</div>
                        <div class="text-gray-300 text-sm font-medium">Clients</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-400 mb-2">500+</div>
                        <div class="text-gray-300 text-sm font-medium">Deployments</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-400 mb-2">98%</div>
                        <div class="text-gray-300 text-sm font-medium">Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer - Same as About.php -->
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

    <!-- Lightbox JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

    <script>
        // Lightbox configuration
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'showImageNumberLabel': true,
            'alwaysShowNavOnTouchDevices': true,
            'fadeDuration': 300,
            'imageFadeDuration': 300
        });

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

        // Gallery filtering functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Gallery filtering
            const filters = document.querySelectorAll('.filter-btn');
            const galleryItems = document.querySelectorAll('.gallery-card');
            
            filters.forEach(filter => {
                filter.addEventListener('click', function() {
                    // Update active filter
                    filters.forEach(f => {
                        f.classList.remove('active', 'bg-gradient-to-r', 'from-orange-500', 'to-orange-600', 'text-white');
                        f.classList.add('bg-gray-100', 'text-gray-800');
                    });
                    this.classList.add('active', 'bg-gradient-to-r', 'from-orange-500', 'to-orange-600', 'text-white');
                    this.classList.remove('bg-gray-100', 'text-gray-800');
                    
                    const category = this.dataset.filter;
                    
                    // Filter items
                    galleryItems.forEach(item => {
                        if (category === 'all' || item.dataset.category === category) {
                            item.style.display = 'block';
                            setTimeout(() => {
                                item.style.opacity = '1';
                                item.style.transform = 'scale(1)';
                            }, 50);
                        } else {
                            item.style.opacity = '0';
                            item.style.transform = 'scale(0.8)';
                            setTimeout(() => {
                                item.style.display = 'none';
                            }, 300);
                        }
                    });
                });
            });

            // Auto play gallery images on hover
            const galleryImages = document.querySelectorAll('.gallery-image-container');
            galleryImages.forEach(container => {
                const img = container.querySelector('img');
                let originalSrc = img.src;
                
                container.addEventListener('mouseenter', function() {
                    // Add a subtle zoom effect
                    img.style.transition = 'transform 5s ease';
                    img.style.transform = 'scale(1.1)';
                });
                
                container.addEventListener('mouseleave', function() {
                    img.style.transition = 'transform 0.5s ease';
                    img.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>
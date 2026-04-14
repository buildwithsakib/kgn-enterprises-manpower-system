<?php
define('BASEPATH', true);
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get services from database
$services = [];
try {
    $stmt = $db->prepare("SELECT * FROM services WHERE is_active = TRUE ORDER BY display_order ASC");
    $stmt->execute();
    $services = $stmt->fetchAll();
} catch(PDOException $e) {
    error_log("Services page error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services | KGN ENTERPRISES | Comprehensive Manpower Solutions in Pune</title>
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
        
        /* Service Card Hover Effects */
        .service-card {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        /* Process Card Hover */
        .process-card {
            transition: all 0.3s ease;
        }
        
        .process-card:hover {
            transform: translateY(-5px);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Service Icon Animation */
        .service-icon {
            transition: transform 0.3s ease;
        }
        
        .service-card:hover .service-icon {
            transform: scale(1.1) rotate(5deg);
        }
    </style>
    
    <!-- SEO Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="description" content="KGN ENTERPRISES offers comprehensive manpower services including skilled & unskilled staffing, facility management, IT professionals, and housekeeping services in Pune.">
    <meta name="keywords" content="manpower services Pune, staffing solutions, facility management, housekeeping services, IT staffing, industrial manpower">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="Our Services | KGN ENTERPRISES | Manpower Solutions">
    <meta property="og:description" content="Comprehensive manpower services including skilled staffing, facility management, and IT professionals">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://kgcenterprises.com/services.php">
    <meta property="og:image" content="https://kgcenterprises.com/uploads/settings/logo.png">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Our Services | KGN ENTERPRISES">
    <meta name="twitter:description" content="Premium manpower solutions and staffing services">
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
                    <a href="about.php" class="text-white hover:text-orange-400 transition font-bold">
                        About Us
                    </a>
                    <a href="services.php" class="text-white hover:text-orange-400 transition text-orange-400 font-bold">
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
                <a href="about.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-building mr-2"></i> About Us
                </a>
                <a href="services.php" class="block text-white hover:text-orange-400 transition py-2 text-orange-400 font-bold">
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
                    Our <span class="gradient-text">Services</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 animate-fade-in-delay">
                    Comprehensive Premium Manpower Solutions for Every Business Need
                </p>
                <p class="text-lg text-gray-300 max-w-3xl mx-auto mb-12 animate-fade-in-delay-2 font-medium">
                    From skilled professionals to complete facility management, we provide end-to-end workforce solutions tailored to your requirements
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

    <!-- Services Overview -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Complete <span class="gradient-text">Workforce Solutions</span>
                </h2>
                <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                    We offer a wide range of premium manpower services designed to meet the diverse needs of modern businesses across industries
                </p>
            </div>

            <!-- Main Services Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-6xl mx-auto">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $index => $service): ?>
                    <div class="service-card bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <div class="flex items-start space-x-6">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center service-icon">
                                <i class="fas <?php echo htmlspecialchars($service['icon'] ?? 'fa-briefcase'); ?> text-2xl text-gray-900"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($service['title']); ?></h3>
                                <p class="text-gray-600 mb-4 leading-relaxed font-medium">
                                    <?php echo htmlspecialchars(substr($service['description'], 0, 180)) . '...'; ?>
                                </p>
                                <ul class="space-y-2 text-gray-600">
                                    <?php 
                                    $features = explode(',', $service['features'] ?? '');
                                    foreach (array_slice($features, 0, 4) as $feature):
                                        if (!empty(trim($feature))):
                                    ?>
                                    <li class="flex items-center font-medium">
                                        <i class="fas fa-check-circle mr-3 text-green-500"></i> 
                                        <?php echo htmlspecialchars(trim($feature)); ?>
                                    </li>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default Services -->
                    <div class="service-card bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in">
                        <div class="flex items-start space-x-6">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center service-icon">
                                <i class="fas fa-user-graduate text-2xl text-gray-900"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">Skilled Manpower</h3>
                                <p class="text-gray-600 mb-4 leading-relaxed font-medium">
                                    Highly trained and experienced professionals for specialized roles including technicians, engineers, supervisors, and skilled workers with technical expertise.
                                </p>
                                <ul class="space-y-2 text-gray-600">
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Technical Staff & Engineers</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> IT Professionals</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Administrative Staff</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Supervisory Roles</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="service-card bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.1s;">
                        <div class="flex items-start space-x-6">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center service-icon">
                                <i class="fas fa-users text-2xl text-gray-900"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">Unskilled Manpower</h3>
                                <p class="text-gray-600 mb-4 leading-relaxed font-medium">
                                    Reliable workforce for general labor, production, packaging, and other entry-level positions across various industries with quick deployment.
                                </p>
                                <ul class="space-y-2 text-gray-600">
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> General Laborers</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Production Workers</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Packaging Staff</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Warehouse Workers</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="service-card bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.2s;">
                        <div class="flex items-start space-x-6">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center service-icon">
                                <i class="fas fa-building text-2xl text-gray-900"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">Facility Management</h3>
                                <p class="text-gray-600 mb-4 leading-relaxed font-medium">
                                    Comprehensive facility management services including maintenance, security, and administrative support for smooth operations of your premises.
                                </p>
                                <ul class="space-y-2 text-gray-600">
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Housekeeping Staff</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Security Personnel</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Maintenance Crew</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Administrative Support</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="service-card bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.3s;">
                        <div class="flex items-start space-x-6">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center service-icon">
                                <i class="fas fa-laptop-code text-2xl text-gray-900"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">IT Professionals</h3>
                                <p class="text-gray-600 mb-4 leading-relaxed font-medium">
                                    Qualified IT professionals including developers, network engineers, support staff, and technical specialists for your technology needs and projects.
                                </p>
                                <ul class="space-y-2 text-gray-600">
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Software Developers</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> IT Support Staff</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Network Engineers</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check-circle mr-3 text-green-500"></i> Technical Specialists</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Detailed Services Section -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Service <span class="gradient-text">Details</span>
                </h2>
                <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                    Explore our comprehensive range of premium manpower services designed to meet your specific business requirements
                </p>
            </div>

            <div class="max-w-6xl mx-auto">
                <!-- Service 1: Skilled Manpower -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-12 border border-gray-200 animate-fade-in" id="skilled">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900 mb-4">Skilled Manpower Solutions</h3>
                            <div class="w-20 h-2 bg-orange-400 rounded-full mb-6"></div>
                            <p class="text-gray-600 mb-6 leading-relaxed font-medium">
                                Our skilled manpower services provide you with qualified professionals who bring expertise and experience to your organization. We carefully screen and select candidates to ensure they meet your specific technical requirements.
                            </p>
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-medium">Technical Experts</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-medium">Quality Assurance</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-medium">Industry Experience</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-medium">Certified Professionals</span>
                                </div>
                            </div>
                            <a href="contact.php" class="inline-flex items-center bg-gradient-to-r from-gray-900 to-black text-white px-6 py-3 rounded-full font-bold hover:from-gray-800 hover:to-gray-900 transition shadow-lg transform hover:scale-105 duration-300">
                                <i class="fas fa-user-tie mr-2"></i> Hire Skilled Staff
                            </a>
                        </div>
                        <div class="bg-gradient-to-br from-gray-900 to-black rounded-2xl p-8 text-white">
                            <i class="fas fa-user-graduate text-6xl text-orange-400 mb-4 service-icon"></i>
                            <h4 class="text-xl font-bold mb-2">Key Benefits</h4>
                            <ul class="text-left text-gray-300 space-y-2">
                                <li class="flex items-center font-medium"><i class="fas fa-star text-orange-400 mr-2"></i> Pre-screened candidates</li>
                                <li class="flex items-center font-medium"><i class="fas fa-star text-orange-400 mr-2"></i> Technical expertise</li>
                                <li class="flex items-center font-medium"><i class="fas fa-star text-orange-400 mr-2"></i> Quick deployment</li>
                                <li class="flex items-center font-medium"><i class="fas fa-star text-orange-400 mr-2"></i> Cost-effective solutions</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Service 2: Facility Management -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-12 border border-gray-200 animate-fade-in" style="animation-delay: 0.1s;" id="facility">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <div class="order-2 lg:order-1">
                            <div class="bg-gradient-to-br from-gray-900 to-black rounded-2xl p-8 text-white">
                                <i class="fas fa-building text-6xl text-orange-400 mb-4 service-icon"></i>
                                <h4 class="text-xl font-bold mb-2">Service Coverage</h4>
                                <ul class="text-left text-gray-300 space-y-2">
                                    <li class="flex items-center font-medium"><i class="fas fa-check text-orange-400 mr-2"></i> Complete facility management</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check text-orange-400 mr-2"></i> 24/7 support available</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check text-orange-400 mr-2"></i> Trained professionals</li>
                                    <li class="flex items-center font-medium"><i class="fas fa-check text-orange-400 mr-2"></i> Quality assurance</li>
                                </ul>
                            </div>
                        </div>
                        <div class="order-1 lg:order-2">
                            <h3 class="text-3xl font-bold text-gray-900 mb-4">Facility Management Services</h3>
                            <div class="w-20 h-2 bg-orange-400 rounded-full mb-6"></div>
                            <p class="text-gray-600 mb-6 leading-relaxed font-medium">
                                Our comprehensive facility management services ensure your premises are well-maintained, secure, and operational. We provide trained staff for housekeeping, security, maintenance, and administrative support.
                            </p>
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-medium">Housekeeping Staff</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-medium">Security Personnel</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-medium">Maintenance Crew</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-medium">Admin Support</span>
                                </div>
                            </div>
                            <a href="contact.php" class="inline-flex items-center bg-gradient-to-r from-gray-900 to-black text-white px-6 py-3 rounded-full font-bold hover:from-gray-800 hover:to-gray-900 transition shadow-lg transform hover:scale-105 duration-300">
                                <i class="fas fa-building mr-2"></i> Get Facility Staff
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Service 3: Industrial Manpower -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.2s;" id="unskilled">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900 mb-4">Industrial Manpower</h3>
                            <div class="w-20 h-2 bg-orange-400 rounded-full mb-6"></div>
                            <p class="text-gray-600 mb-6 leading-relaxed font-medium">
                                We provide reliable industrial manpower for manufacturing, production, warehouse, and logistics operations. Our workforce is trained to handle industrial environments and meet production targets efficiently.
                            </p>
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-medium">Factory Workers</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-medium">Warehouse Staff</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-medium">Production Line</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700 font-medium">Quality Control</span>
                                </div>
                            </div>
                            <a href="contact.php" class="inline-flex items-center bg-gradient-to-r from-gray-900 to-black text-white px-6 py-3 rounded-full font-bold hover:from-gray-800 hover:to-gray-900 transition shadow-lg transform hover:scale-105 duration-300">
                                <i class="fas fa-industry mr-2"></i> Industrial Staffing
                            </a>
                        </div>
                        <div class="bg-gradient-to-br from-gray-900 to-black rounded-2xl p-8 text-white">
                            <i class="fas fa-industry text-6xl text-orange-400 mb-4 service-icon"></i>
                            <h4 class="text-xl font-bold mb-2">Industrial Expertise</h4>
                            <ul class="text-left text-gray-300 space-y-2">
                                <li class="flex items-center font-medium"><i class="fas fa-cogs text-orange-400 mr-2"></i> Manufacturing units</li>
                                <li class="flex items-center font-medium"><i class="fas fa-warehouse text-orange-400 mr-2"></i> Warehouse operations</li>
                                <li class="flex items-center font-medium"><i class="fas fa-shipping-fast text-orange-400 mr-2"></i> Logistics support</li>
                                <li class="flex items-center font-medium"><i class="fas fa-tools text-orange-400 mr-2"></i> Production lines</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Our <span class="gradient-text">Process</span>
                </h2>
                <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                    Simple, efficient, and transparent process to get you the right manpower quickly
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
                <!-- Step 1 -->
                <div class="process-card text-center bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-clipboard-list text-2xl text-gray-900"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Requirement Analysis</h3>
                    <p class="text-gray-600 font-medium">
                        We understand your specific manpower needs, job roles, and skill requirements in detail.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="process-card text-center bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search text-2xl text-gray-900"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Candidate Screening</h3>
                    <p class="text-gray-600 font-medium">
                        Thorough screening, background verification, and skill assessment of potential candidates.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="process-card text-center bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-user-check text-2xl text-gray-900"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Selection & Deployment</h3>
                    <p class="text-gray-600 font-medium">
                        Final selection and quick deployment of qualified manpower to your location within 24-48 hours.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="process-card text-center bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-headset text-2xl text-gray-900"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Continuous Support</h3>
                    <p class="text-gray-600 font-medium">
                        Ongoing support, performance monitoring, and replacement if needed for complete satisfaction.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-gray-900 via-black to-gray-900 text-white relative overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">
                    Need Premium Manpower?
                </h2>
                <p class="text-xl text-gray-300 mb-8 font-medium">
                    Get the right workforce for your business with our comprehensive staffing solutions
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <a href="contact.php" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition shadow-lg transform hover:scale-105 duration-300 inline-flex items-center">
                        <i class="fas fa-paper-plane mr-3"></i> Get Free Consultation
                    </a>
                    <a href="tel:+919881901568" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-gray-900 transition transform hover:scale-105 duration-300 inline-flex items-center">
                        <i class="fas fa-phone mr-3"></i> Call +91 9881901568
                    </a>
                </div>
                
                <!-- Service Highlights -->
                <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-3xl mx-auto">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-400 mb-2">24-48</div>
                        <div class="text-gray-300 text-sm font-medium">Hours Deployment</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-400 mb-2">100%</div>
                        <div class="text-gray-300 text-sm font-medium">Verified Staff</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-400 mb-2">50+</div>
                        <div class="text-gray-300 text-sm font-medium">Industry Sectors</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-400 mb-2">24/7</div>
                        <div class="text-gray-300 text-sm font-medium">Customer Support</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
        document.querySelectorAll('.animate-fade-in').forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            animateOnScroll.observe(element);
        });

        // Add hover animation to service icons
        document.querySelectorAll('.service-icon').forEach(icon => {
            icon.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1) rotate(5deg)';
            });
            icon.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1) rotate(0deg)';
            });
        });
    </script>
</body>
</html>
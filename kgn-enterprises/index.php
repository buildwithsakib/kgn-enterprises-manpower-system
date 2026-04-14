<?php
define('BASEPATH', true);
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get featured data
$testimonials = [];
$clients = [];
$services = [];
$team = [];

try {
    $stmt = $db->prepare("SELECT * FROM testimonials WHERE is_approved = TRUE AND featured = TRUE ORDER BY created_at DESC LIMIT 4");
    $stmt->execute();
    $testimonials = $stmt->fetchAll();
    
    $stmt = $db->prepare("SELECT * FROM clients WHERE is_featured = TRUE ORDER BY created_at DESC LIMIT 8");
    $stmt->execute();
    $clients = $stmt->fetchAll();
    
    $stmt = $db->prepare("SELECT * FROM services WHERE is_active = TRUE ORDER BY display_order ASC LIMIT 6");
    $stmt->execute();
    $services = $stmt->fetchAll();
    
    $stmt = $db->prepare("SELECT * FROM team_members WHERE is_active = TRUE ORDER BY display_order ASC LIMIT 4");
    $stmt->execute();
    $team = $stmt->fetchAll();
    
} catch(PDOException $e) {
    error_log("Homepage data error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KGN ENTERPRISES | Premium Manpower Services, Job Placement & Contract Staffing Pune</title>
    <link rel="icon" type="image/x-icon" href="uploads/settings/logo.png">
    
    <!-- CDN CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Bold Fonts like Sainath -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&family=Montserrat:wght@600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
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
        
        .stat-gradient {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 50%, #fdba74 100%);
        }
        
        /* Scrollbar Hidden like Sainath */
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
        
        /* Service Card Hover Effect */
        .service-card {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        /* Team Card Hover */
        .team-card {
            transition: all 0.3s ease;
        }
        
        .team-card:hover {
            transform: translateY(-5px);
        }
        
        /* Certification Logo Animation */
        .cert-logo {
            transition: all 0.3s ease;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        /* Gallery Image Hover */
        .gallery-img {
            transition: transform 0.3s ease;
        }
        
        .gallery-img:hover {
            transform: scale(1.05);
        }
    </style>
    
    <!-- SEO Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="description" content="KGN ENTERPRISES provides premium manpower services, job placement, and contract staffing with 5+ years experience. We supply skilled and unskilled workforce, facility management, and industrial labour across Pune and Maharashtra.">
    <meta name="keywords" content="manpower services Pune, job placement, labour supply, contract staffing, workforce provider, recruitment agency, manpower consultancy, facility management services, staffing services in Pune, industrial labour supply">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="KGN ENTERPRISES | Premium Manpower & Job Placement Solutions">
    <meta property="og:description" content="KGN ENTERPRISES provides premium manpower services, job placement, and contract staffing with 5+ years experience. We supply skilled and unskilled workforce, facility management, and industrial labour across Pune and Maharashtra.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://kgcenterprises.com/">
    <meta property="og:image" content="https://kgcenterprises.com/uploads/settings/logo.png">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="KGN ENTERPRISES | Premium Manpower Solutions">
    <meta name="twitter:description" content="Premium manpower solutions & staffing services across India">
    <meta name="twitter:image" content="https://kgcenterprises.com/uploads/settings/logo.png">
    
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "KGN ENTERPRISES",
        "description": "KGN ENTERPRISES is a leading manpower services provider offering skilled and unskilled workforce, contract staffing, facility management, and job placement across Pune and Maharashtra.",
        "url": "https://kgcenterprises.com/",
        "telephone": "+91 9881901568",
        "email": "kgnenterprises9670@gmail.com",
        "image": "https://kgcenterprises.com/uploads/settings/logo.png",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "185 Ground, Javed Turuk, Shendewadi Phata",
            "addressLocality": "Kadus, Khed",
            "addressRegion": "Pune",
            "postalCode": "412404",
            "addressCountry": "IN"
        },
        "areaServed": ["Pune", "Maharashtra"],
        "makesOffer": [{
            "@type": "Offer",
            "itemOffered": [{
                "@type": "Service",
                "name": "Skilled Manpower Supply"
            },{
                "@type": "Service",
                "name": "Unskilled Labour Supply"
            },{
                "@type": "Service",
                "name": "Contract Staffing"
            },{
                "@type": "Service",
                "name": "Industrial Workers"
            },{
                "@type": "Service",
                "name": "Housekeeping Staff"
            },{
                "@type": "Service",
                "name": "Security & Facility Management"
            }]
        }]
    }
    </script>

</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-black to-gray-900 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                <!-- Logo and Company Name -->
                <div class="flex items-center">
                    <a href="index.php" class="text-white text-2xl font-bold flex items-center">
                        <img src="uploads/settings/logo.png" alt="KGN ENTERPRISES" class="h-12 mr-3">
                        <span class="hidden md:block font-extrabold">KGN ENTERPRISES</span>    
                        <span class="md:hidden flex flex-col items-start leading-tight">
                            <span class="text-lg">KGN</span><span class="text-lg">ENTERPRISES</span>
                        </span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex space-x-8">
                    <a href="index.php" class="text-white hover:text-orange-400 transition text-orange-400 font-bold">
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
                <a href="index.php" class="block text-white hover:text-orange-400 transition py-2 text-orange-400 font-bold">
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
        
        <!-- Certification Logos -->
        <div class="absolute top-8 left-0 right-0 z-20">
            <div class="container mx-auto px-4">
                <div class="flex justify-center space-x-6 md:space-x-12">
                    <div class="cert-logo bg-white p-3 rounded-lg shadow-lg">
                        <img src="uploads/settings/iso.png" alt="ISO Certified" class="w-12 h-12 md:w-16 md:h-16 object-contain">
                    </div>
                    <div class="cert-logo bg-white p-3 rounded-lg shadow-lg">
                        <img src="uploads/settings/msme.png" alt="MSME Registered" class="w-12 h-12 md:w-16 md:h-16 object-contain">
                    </div>
                    <div class="cert-logo bg-white p-3 rounded-lg shadow-lg">
                        <img src="uploads/settings/made-in-india.png" alt="Made in India" class="w-12 h-12 md:w-16 md:h-16 object-contain">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10 pt-16">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 animate-fade-in">
                    Premium <span class="text-orange-400">Manpower</span> Solutions for Every Sector
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 animate-fade-in-delay">
                    Trusted Manpower Services | 5+ Years Experience | PAN India Reach
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-delay-2">
                    <a href="contact.php" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-4 rounded-lg font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition shadow-lg transform hover:scale-105 duration-300">
                        <i class="fas fa-phone mr-2"></i> Hire Workforce
                    </a>
                    <a href="services.php" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-gray-900 transition transform hover:scale-105 duration-300">
                        <i class="fas fa-briefcase mr-2"></i> Our Services
                    </a>
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

    <!-- About Snippet -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">About KGN ENTERPRISES — Premium Manpower Services & Placement</h2>
                <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 border border-gray-200">
                    <p class="text-gray-700 text-lg leading-relaxed mb-8 font-medium">
                        <strong>KGN ENTERPRISES</strong> is a premier manpower services provider with <strong>5+ years</strong> of excellence in <strong>job placement</strong>, <strong>labour supply</strong>, and <strong>contract staffing</strong>. We deploy verified skilled and unskilled professionals including programmers, office clerks, data entry operators, administrative staff, housekeeping personnel, sanitation workers, sweepers, cleaning staff, and facility management workforce for government, industrial, commercial, and institutional establishments across Maharashtra.
                    </p>
                    <a href="about.php" class="inline-block bg-gradient-to-r from-gray-900 to-black text-white px-8 py-4 rounded-full font-bold text-lg hover:from-gray-800 hover:to-gray-900 transition shadow-lg transform hover:-translate-y-1 duration-300">
                        Read More <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-gradient-to-r from-gray-900 to-black text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                <div class="text-center bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 hover:bg-opacity-20 transition duration-300">
                    <div class="text-4xl md:text-5xl font-bold text-orange-400 mb-2" data-count="1000+">0</div>
                    <div class="text-lg md:text-xl font-bold">Workforce Deployed</div>
                </div>
                <div class="text-center bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 hover:bg-opacity-20 transition duration-300">
                    <div class="text-4xl md:text-5xl font-bold text-orange-400 mb-2" data-count="50+">0</div>
                    <div class="text-lg md:text-xl font-bold">Satisfied Clients</div>
                </div>
                <div class="text-center bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 hover:bg-opacity-20 transition duration-300">
                    <div class="text-4xl md:text-5xl font-bold text-orange-400 mb-2" data-count="5+">0</div>
                    <div class="text-lg md:text-xl font-bold">Years Experience</div>
                </div>
                <div class="text-center bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 hover:bg-opacity-20 transition duration-300">
                    <div class="text-4xl md:text-5xl font-bold text-orange-400 mb-2" data-count="24">0</div>
                    <div class="text-lg md:text-xl font-bold">Hour Deployment</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose KGN ENTERPRISES -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Why Choose KGN ENTERPRISES for Manpower & Contract Staffing</h2>
                <div class="w-24 h-1 bg-orange-400 mx-auto rounded-full"></div>
                <p class="text-gray-600 mt-4 max-w-3xl mx-auto font-medium">Over 5 years of excellence in providing premium manpower solutions</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-lg p-6 text-center border-t-4 border-gray-900 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-20 h-20 bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-user-check text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Verified Workers</h3>
                    <p class="text-gray-600 font-medium">All our workforce undergoes thorough background verification and skill assessment</p>
                </div>

                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-lg p-6 text-center border-t-4 border-orange-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-20 h-20 bg-orange-400 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-bolt text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Quick Deployment</h3>
                    <p class="text-gray-600 font-medium">Rapid workforce deployment within 24-48 hours to meet your urgent requirements</p>
                </div>

                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-lg p-6 text-center border-t-4 border-green-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-sync-alt text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Replacement Guarantee</h3>
                    <p class="text-gray-600 font-medium">Quick replacement of workforce if any issues arise during the service period</p>
                </div>

                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-lg p-6 text-center border-t-4 border-blue-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-globe-asia text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">PAN India Reach</h3>
                    <p class="text-gray-600 font-medium">We serve clients across all major cities and states throughout India</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Highlights -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Manpower Services & Staffing Solutions</h2>
                <div class="w-24 h-1 bg-orange-400 mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $index => $service): ?>
                    <div class="service-card bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-bold">
                                <?php echo htmlspecialchars($service['category'] ?? 'Service'); ?>
                            </span>
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas <?php echo htmlspecialchars($service['icon'] ?? 'fa-briefcase'); ?> text-gray-700"></i>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3"><?php echo htmlspecialchars($service['title']); ?></h3>
                        <p class="text-gray-600 mb-4 font-medium"><?php echo htmlspecialchars(substr($service['description'], 0, 120)) . '...'; ?></p>
                        <a href="services.php" class="text-gray-900 font-bold hover:text-orange-500 transition flex items-center">
                            Learn More <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default Services -->
                    <div class="service-card bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-bold">Skilled</span>
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-graduate text-gray-700"></i>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Skilled Professionals</h3>
                        <p class="text-gray-600 mb-4 font-medium">Highly trained technicians, engineers, IT professionals for your industry needs...</p>
                        <a href="services.php" class="text-gray-900 font-bold hover:text-orange-500 transition flex items-center">
                            Learn More <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>
                    
                    <div class="service-card bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-bold">General</span>
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-gray-700"></i>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">General Manpower</h3>
                        <p class="text-gray-600 mb-4 font-medium">Reliable general labor, production workers, packaging staff for various industries...</p>
                        <a href="services.php" class="text-gray-900 font-bold hover:text-orange-500 transition flex items-center">
                            Learn More <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>
                    
                    <div class="service-card bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">Facility</span>
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-building text-gray-700"></i>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Facility Management</h3>
                        <p class="text-gray-600 mb-4 font-medium">Complete facility management services including housekeeping, security, maintenance...</p>
                        <a href="services.php" class="text-gray-900 font-bold hover:text-orange-500 transition flex items-center">
                            Learn More <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-12">
                <a href="services.php" class="inline-block bg-gradient-to-r from-gray-900 to-black text-white px-8 py-4 rounded-full font-bold text-lg hover:from-gray-800 hover:to-gray-900 transition shadow-lg transform hover:-translate-y-1 duration-300">
                    View All Services <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Client Logos -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Trusted Clients & Employers</h2>
                <div class="w-24 h-1 bg-orange-400 mx-auto rounded-full"></div>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto font-medium">Leading organizations across India trust our manpower services</p>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-6 md:gap-8">
                <?php if (!empty($clients)): ?>
                    <?php foreach ($clients as $client): ?>
                    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-orange-400 transform hover:-translate-y-2 group" style="width: 220px; height: 250px;">
                        <div class="flex flex-col items-center h-full">
                            <div class="w-24 h-24 bg-gradient-to-br from-gray-50 to-gray-100 rounded-full flex items-center justify-center mb-4 overflow-hidden border-4 border-white shadow-md group-hover:shadow-lg transition-all duration-300 mx-auto">
                                <?php if(!empty($client['logo'])): ?>
                                    <img src="uploads/clients/<?php echo htmlspecialchars($client['logo']); ?>" alt="<?php echo htmlspecialchars($client['name']); ?>" class="w-full h-full object-cover rounded-full">
                                <?php else: ?>
                                    <i class="fas fa-building text-gray-600 text-4xl"></i>
                                <?php endif; ?>
                            </div>
                            <h3 class="font-bold text-gray-800 text-center text-lg flex-grow flex items-center"><?php echo htmlspecialchars($client['name']); ?></h3>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full mt-2 font-medium">
                                <?php echo htmlspecialchars($client['industry'] ?? 'Client'); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default Clients -->
                    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-orange-400 transform hover:-translate-y-2 group" style="width: 220px; height: 250px;">
                        <div class="flex flex-col items-center h-full">
                            <div class="w-24 h-24 bg-gradient-to-br from-gray-50 to-gray-100 rounded-full flex items-center justify-center mb-4 overflow-hidden border-4 border-white shadow-md group-hover:shadow-lg transition-all duration-300 mx-auto">
                                <i class="fas fa-industry text-gray-600 text-4xl"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 text-center text-lg flex-grow flex items-center">Manufacturing Company</h3>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full mt-2 font-medium">Industrial</span>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-orange-400 transform hover:-translate-y-2 group" style="width: 220px; height: 250px;">
                        <div class="flex flex-col items-center h-full">
                            <div class="w-24 h-24 bg-gradient-to-br from-gray-50 to-gray-100 rounded-full flex items-center justify-center mb-4 overflow-hidden border-4 border-white shadow-md group-hover:shadow-lg transition-all duration-300 mx-auto">
                                <i class="fas fa-laptop-code text-gray-600 text-4xl"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 text-center text-lg flex-grow flex items-center">IT Solutions Ltd</h3>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full mt-2 font-medium">Technology</span>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-orange-400 transform hover:-translate-y-2 group" style="width: 220px; height: 250px;">
                        <div class="flex flex-col items-center h-full">
                            <div class="w-24 h-24 bg-gradient-to-br from-gray-50 to-gray-100 rounded-full flex items-center justify-center mb-4 overflow-hidden border-4 border-white shadow-md group-hover:shadow-lg transition-all duration-300 mx-auto">
                                <i class="fas fa-hospital text-gray-600 text-4xl"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 text-center text-lg flex-grow flex items-center">Healthcare Group</h3>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full mt-2 font-medium">Healthcare</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-12">
                <a href="clients.php" class="inline-block bg-gradient-to-r from-gray-900 to-black text-white px-8 py-4 rounded-full font-bold text-lg hover:from-gray-800 hover:to-gray-900 transition shadow-lg transform hover:-translate-y-1 duration-300">
                    View All Clients <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">What Our Clients Say</h2>
                <div class="w-24 h-1 bg-orange-400 mx-auto rounded-full"></div>
            </div>
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php if (!empty($testimonials)): ?>
                        <?php foreach ($testimonials as $testimonial): ?>
                        <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition duration-300 border border-gray-200">
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center text-white text-xl font-bold">
                                    <?php echo strtoupper(substr($testimonial['client_name'], 0, 1)); ?>
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-bold text-gray-800"><?php echo htmlspecialchars($testimonial['client_name']); ?></h3>
                                    <p class="text-sm text-gray-600 font-medium"><?php echo htmlspecialchars($testimonial['client_designation']); ?></p>
                                </div>
                            </div>
                            <p class="text-gray-700 mb-3 italic font-medium">"<?php echo htmlspecialchars($testimonial['testimonial']); ?>"</p>
                            <div class="text-yellow-400">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Default Testimonials -->
                        <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition duration-300 border border-gray-200">
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center text-white text-xl font-bold">
                                    S
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-bold text-gray-800">OMKAR UGLE</h3>
                                    <p class="text-sm text-gray-600 font-medium">NIYATI PVT. LTD</p>
                                </div>
                            </div>
                            <p class="text-gray-700 mb-3 italic font-medium">"Provided excellent manpower support for our project. Professional and reliable service."</p>
                            <div class="text-yellow-400">
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition duration-300 border border-gray-200">
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center text-white text-xl font-bold">
                                    P
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-bold text-gray-800">TEJAS PAWALE</h3>
                                    <p class="text-sm text-gray-600 font-medium">HR Manager, Manufacturing Unit</p>
                                </div>
                            </div>
                            <p class="text-gray-700 mb-3 italic font-medium">"Quick deployment of skilled workers. Great replacement policy and professional team."</p>
                            <div class="text-yellow-400">
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star-half-alt text-yellow-400"></i>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition duration-300 border border-gray-200">
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center text-white text-xl font-bold">
                                    R
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-bold text-gray-800">ROHIT KINKER</h3>
                                    <p class="text-sm text-gray-600 font-medium">Operations Head, IT Company</p>
                                </div>
                            </div>
                            <p class="text-gray-700 mb-3 italic font-medium">"Best manpower service in Pune. They understand client requirements perfectly."</p>
                            <div class="text-yellow-400">
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                                <i class="fas fa-star text-yellow-400"></i>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="testimonials.php" class="inline-block bg-gradient-to-r from-gray-900 to-black text-white px-8 py-4 rounded-full font-bold text-lg hover:from-gray-800 hover:to-gray-900 transition shadow-lg transform hover:-translate-y-1 duration-300">
                    View All Reviews <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Our Team -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Leadership & Staffing Team</h2>
                <div class="w-24 h-1 bg-orange-400 mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <?php if (!empty($team)): ?>
                    <?php foreach ($team as $member): ?>
                    <div class="team-card bg-white rounded-2xl shadow-lg hover:shadow-xl transition duration-300 p-6 text-center border border-gray-200">
                        <div class="w-32 h-32 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center mx-auto mb-6 overflow-hidden">
                            <?php if(!empty($member['photo'])): ?>
                                <img src="uploads/team/<?php echo htmlspecialchars($member['photo']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white text-4xl font-bold">
                                    <?php echo strtoupper(substr($member['name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($member['name']); ?></h3>
                        <p class="text-orange-500 font-bold mb-4"><?php echo htmlspecialchars($member['designation']); ?></p>
                        <p class="text-gray-600 font-medium"><?php echo htmlspecialchars(substr($member['description'], 0, 120)) . '...'; ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Team Members -->
                    <div class="team-card bg-white rounded-2xl shadow-lg hover:shadow-xl transition duration-300 p-6 text-center border border-gray-200">
                        <div class="w-32 h-32 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center mx-auto mb-6 overflow-hidden">
                            <!-- You can replace with actual photo -->
                            <img src="uploads/settings/javed.png" alt="Mr. Javed Turuk" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop'">
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Mr. Javed Turuk</h3>
                        <p class="text-orange-500 font-bold mb-4">Founder & Director</p>
                        <p class="text-gray-600 font-medium">Expertise in managing large-scale workforce deployments and client relations...</p>
                    </div>
                    
                    <div class="team-card bg-white rounded-2xl shadow-lg hover:shadow-xl transition duration-300 p-6 text-center border border-gray-200">
                        <div class="w-32 h-32 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center mx-auto mb-6 overflow-hidden">
                            <!-- You can replace with actual photo -->
                            <img src="uploads/settings/sakib.png" alt="Sakib Shaikh" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop'">
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Sakib Shaikh</h3>
                        <p class="text-orange-500 font-bold mb-4">Business Development Manager</p>
                        <p class="text-gray-600 font-medium">Specialized in client acquisition and business strategy development...</p>
                    </div>
                    
                    <div class="team-card bg-white rounded-2xl shadow-lg hover:shadow-xl transition duration-300 p-6 text-center border border-gray-200">
                        <div class="w-32 h-32 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center mx-auto mb-6 overflow-hidden">
                            <!-- You can replace with actual photo -->
                            <img src="uploads/settings/swapnil.png" alt="Swapnil Salunke" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop'">
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Swapnil Salunke</h3>
                        <p class="text-orange-500 font-bold mb-4">Operations Manager</p>
                        <p class="text-gray-600 font-medium">Specialized in employee management and field operations...</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-12">
                <a href="about.php#team" class="inline-block bg-gradient-to-r from-gray-900 to-black text-white px-8 py-4 rounded-full font-bold text-lg hover:from-gray-800 hover:to-gray-900 transition shadow-lg transform hover:-translate-y-1 duration-300">
                    View Full Team <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Gallery -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Our Gallery</h2>
                <div class="w-24 h-1 bg-orange-400 mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
                <!-- Gallery Images from CDN -->
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden border border-gray-200 transform hover:-translate-y-2">
                    <div class="relative h-64 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop" alt="Team Meeting" class="w-full h-full object-cover gallery-img">
                        <div class="absolute top-4 right-4">
                            <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-bold">
                                Office
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-700 font-bold">Team Collaboration & Planning</p>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden border border-gray-200 transform hover:-translate-y-2">
                    <div class="relative h-64 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?w=600&h=400&fit=crop" alt="Manpower Training" class="w-full h-full object-cover gallery-img">
                        <div class="absolute top-4 right-4">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-bold">
                                Training
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-700 font-bold">Manpower Training Session</p>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden border border-gray-200 transform hover:-translate-y-2">
                    <div class="relative h-64 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600&h=400&fit=crop" alt="Industrial Staff" class="w-full h-full object-cover gallery-img">
                        <div class="absolute top-4 right-4">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">
                                Industrial
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-700 font-bold">Industrial Workforce Deployment</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-12">
                <a href="gallery.php" class="inline-block bg-gradient-to-r from-gray-900 to-black text-white px-8 py-4 rounded-full font-bold text-lg hover:from-gray-800 hover:to-gray-900 transition shadow-lg transform hover:-translate-y-1 duration-300">
                    View All Photos <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Manpower & Staffing FAQs</h2>
                <div class="w-24 h-1 bg-orange-400 mx-auto rounded-full"></div>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto font-bold">Find answers about manpower services, job placement, contract staffing, and facility management</p>
            </div>
            <div class="max-w-4xl mx-auto">
                <div class="space-y-4">
                    <!-- FAQ Item 1 -->
                    <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300">
                        <button class="flex justify-between items-center w-full p-6 text-left rounded-2xl focus:outline-none">
                            <span class="text-lg font-bold text-gray-800">What manpower services do you provide?</span>
                            <i class="fas fa-chevron-down text-gray-900 transition-transform duration-300"></i>
                        </button>
                        <div class="px-6 pb-6 hidden">
                            <p class="text-gray-600 font-medium">We provide skilled and unskilled manpower, contract staffing, industrial labour, housekeeping, security and facility management services across Pune and Maharashtra.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300">
                        <button class="flex justify-between items-center w-full p-6 text-left rounded-2xl focus:outline-none">
                            <span class="text-lg font-bold text-gray-800">How fast can you deploy workers?</span>
                            <i class="fas fa-chevron-down text-gray-900 transition-transform duration-300"></i>
                        </button>
                        <div class="px-6 pb-6 hidden">
                            <p class="text-gray-600 font-medium">For most roles we can deploy pre-verified workforce within 24–48 hours across PAN India, depending on project scope and location.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300">
                        <button class="flex justify-between items-center w-full p-6 text-left rounded-2xl focus:outline-none">
                            <span class="text-lg font-bold text-gray-800">Do you offer background verification?</span>
                            <i class="fas fa-chevron-down text-gray-900 transition-transform duration-300"></i>
                        </button>
                        <div class="px-6 pb-6 hidden">
                            <p class="text-gray-600 font-medium">Yes. All candidates undergo document validation, experience checks and reference verification before placement.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300">
                        <button class="flex justify-between items-center w-full p-6 text-left rounded-2xl focus:outline-none">
                            <span class="text-lg font-bold text-gray-800">What is your replacement policy?</span>
                            <i class="fas fa-chevron-down text-gray-900 transition-transform duration-300"></i>
                        </button>
                        <div class="px-6 pb-6 hidden">
                            <p class="text-gray-600 font-medium">We provide a quick replacement guarantee within 24–48 hours if any issue arises during the service period.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300">
                        <button class="flex justify-between items-center w-full p-6 text-left rounded-2xl focus:outline-none">
                            <span class="text-lg font-bold text-gray-800">Which industries do you serve?</span>
                            <i class="fas fa-chevron-down text-gray-900 transition-transform duration-300"></i>
                        </button>
                        <div class="px-6 pb-6 hidden">
                            <p class="text-gray-600 font-medium">We serve government, industrial, commercial and institutional establishments including IT, manufacturing, healthcare, hospitality and more.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    // FAQ accordion functionality
    document.querySelectorAll('.bg-white button').forEach(button => {
        button.addEventListener('click', () => {
            const icon = button.querySelector('i');
            const content = button.nextElementSibling;
            
            // Toggle icon rotation
            icon.classList.toggle('rotate-180');
            
            // Toggle content visibility
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                content.style.maxHeight = content.scrollHeight + 'px';
            } else {
                content.style.maxHeight = '0';
                setTimeout(() => {
                    content.classList.add('hidden');
                }, 300);
            }
        });
    });
    </script>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-gray-900 via-black to-gray-900 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Need Premium Workforce in Pune & Maharashtra? Contact Us Today!</h2>
            <p class="text-xl text-gray-300 mb-8 max-w-3xl mx-auto font-medium">
                We provide quality manpower solutions tailored to your business needs
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="contact.php" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition shadow-lg transform hover:-translate-y-1 duration-300">
                    <i class="fas fa-envelope mr-2"></i> Send Inquiry
                </a>
                <a href="tel:+919881901568" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-gray-900 transition transform hover:-translate-y-1 duration-300">
                    <i class="fas fa-phone mr-2"></i> Call Now
                </a>
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
                const counter = entry.target.querySelector('[data-count]');
                if (counter && !counter.classList.contains('counted')) {
                    counter.classList.add('counted');
                    animateCounter(counter);
                }
            }
        });
    });

    document.querySelectorAll('[data-count]').forEach(element => {
        observer.observe(element.parentElement);
    });
    </script>

    <!-- Footer -->
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
    </script>
</body>
</html>
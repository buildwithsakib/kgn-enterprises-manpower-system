<?php
define('BASEPATH', true);

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Fetch featured clients
$clients = [];
try {
    $clients_query = "SELECT * FROM clients WHERE is_featured = TRUE ORDER BY created_at DESC";
    $clients_stmt = $db->prepare($clients_query);
    $clients_stmt->execute();
    $clients = $clients_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching clients: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Clients | KGN ENTERPRISES | Trusted by 50+ Companies in Pune</title>
    <link rel="icon" type="image/x-icon" href="uploads/settings/logo.png">
    
    <!-- CDN CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
        .client-card {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: white;
        }
        
        .client-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .industry-card {
            transition: all 0.3s ease;
        }
        
        .industry-card:hover {
            transform: translateY(-5px);
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            color: white;
        }
        
        .industry-card:hover i,
        .industry-card:hover h3 {
            color: white;
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Logo Container */
        .logo-container {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }
        
        /* Client Logo Hover */
        .client-logo {
            transition: all 0.3s ease;
            filter: grayscale(100%);
            opacity: 0.8;
        }
        
        .client-logo:hover {
            filter: grayscale(0%);
            opacity: 1;
            transform: scale(1.05);
        }
    </style>
    
    <!-- SEO Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="description" content="KGN ENTERPRISES is trusted by 50+ leading companies across Pune and Maharashtra for premium manpower solutions. View our client portfolio.">
    <meta name="keywords" content="manpower clients Pune, staffing solutions clients, facility management clients, KGN ENTERPRISES clients, industrial manpower clients">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="Our Clients | KGN ENTERPRISES | Trusted by 50+ Companies">
    <meta property="og:description" content="Leading companies trust KGN ENTERPRISES for quality manpower solutions in Pune and Maharashtra">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://kgcenterprises.com/clients.php">
    <meta property="og:image" content="https://kgcenterprises.com/uploads/settings/logo.png">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="KGN ENTERPRISES Clients">
    <meta name="twitter:description" content="Trusted by 50+ leading companies for premium manpower solutions">
    <meta name="twitter:image" content="https://kgcenterprises.com/uploads/settings/logo.png">
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
                    <a href="clients.php" class="text-white hover:text-orange-400 transition text-orange-400 font-bold">
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
                <a href="services.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-briefcase mr-2"></i> Services
                </a>
                <a href="careers.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-user-tie mr-2"></i> Careers
                </a>
                <a href="clients.php" class="block text-white hover:text-orange-400 transition py-2 text-orange-400 font-bold">
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
                    Our <span class="gradient-text">Clients</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 animate-fade-in-delay">
                    Trusted by 50+ Leading Companies in Pune & Maharashtra
                </p>
                <p class="text-lg text-gray-300 max-w-3xl mx-auto mb-12 animate-fade-in-delay-2 font-medium">
                    We take pride in serving diverse industries with our quality manpower solutions and building long-term partnerships
                </p>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-3xl mx-auto animate-fade-in-delay-2">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-3xl font-bold text-white mb-2">50+</div>
                        <div class="text-gray-300 font-medium">Happy Clients</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-3xl font-bold text-white mb-2">5+</div>
                        <div class="text-gray-300 font-medium">Years Trust</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-3xl font-bold text-white mb-2">95%</div>
                        <div class="text-gray-300 font-medium">Retention Rate</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-3xl font-bold text-white mb-2">24/7</div>
                        <div class="text-gray-300 font-medium">Support</div>
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

    <!-- Featured Clients Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                        Featured <span class="gradient-text">Clients</span>
                    </h2>
                    <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                    <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                        We partner with businesses across various sectors to provide tailored manpower solutions
                    </p>
                </div>

                <?php if (count($clients) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($clients as $client): ?>
                            <div class="client-card rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in">
                                <!-- Client Logo/Icon -->
                                <div class="logo-container w-32 h-32 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                    <?php if (!empty($client['logo']) && file_exists($client['logo'])): ?>
                                        <img src="<?php echo htmlspecialchars($client['logo']); ?>" 
                                             alt="<?php echo htmlspecialchars($client['name']); ?>" 
                                             class="h-20 w-20 object-contain client-logo">
                                    <?php else: ?>
                                        <div class="text-center">
                                            <i class="fas fa-building text-5xl text-gray-600 mb-2"></i>
                                            <p class="text-gray-700 font-bold text-sm"><?php echo htmlspecialchars($client['name']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Client Info -->
                                <div class="text-center">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($client['name']); ?></h3>
                                    <p class="text-orange-600 font-bold mb-4"><?php echo htmlspecialchars($client['industry']); ?></p>
                                    
                                    <?php if (!empty($client['testimonial'])): ?>
                                        <div class="mb-6">
                                            <i class="fas fa-quote-left text-gray-300 text-xl mb-2"></i>
                                            <p class="text-gray-600 italic leading-relaxed font-medium">
                                                "<?php echo htmlspecialchars($client['testimonial']); ?>"
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Rating -->
                                    <?php if (!empty($client['rating'])): ?>
                                        <div class="flex justify-center items-center space-x-1 mb-6">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $client['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Since Date -->
                                    <?php if (!empty($client['since_date'])): ?>
                                        <p class="text-gray-500 text-sm font-medium mb-4">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            Partner since <?php echo date('Y', strtotime($client['since_date'])); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <!-- Website -->
                                    <?php if (!empty($client['website'])): ?>
                                        <a href="<?php echo htmlspecialchars($client['website']); ?>" 
                                           target="_blank" 
                                           class="inline-flex items-center text-orange-600 hover:text-orange-700 transition font-bold text-sm">
                                            <i class="fas fa-external-link-alt mr-2"></i>
                                            Visit Website
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16 animate-fade-in">
                        <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-building text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-600 mb-4">Client Portfolio Coming Soon</h3>
                        <p class="text-gray-500 max-w-md mx-auto mb-8 font-medium">
                            We're currently updating our client portfolio. Check back soon to see the leading companies we partner with.
                        </p>
                        <a href="contact.php" class="inline-flex items-center bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-full font-bold hover:from-orange-600 hover:to-orange-700 transition">
                            <i class="fas fa-handshake mr-2"></i> Become Our First Featured Client
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Industries We Serve -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Industries <span class="gradient-text">We Serve</span>
                </h2>
                <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                    We provide comprehensive manpower solutions across multiple industry verticals in Pune and Maharashtra
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 max-w-6xl mx-auto">
                <?php 
                $industries = [
                    ['icon' => 'fas fa-industry', 'title' => 'Manufacturing', 'color' => 'from-gray-900 to-black'],
                    ['icon' => 'fas fa-laptop-code', 'title' => 'IT & Tech', 'color' => 'from-blue-500 to-blue-600'],
                    ['icon' => 'fas fa-store', 'title' => 'Retail', 'color' => 'from-purple-500 to-purple-600'],
                    ['icon' => 'fas fa-heartbeat', 'title' => 'Healthcare', 'color' => 'from-red-500 to-red-600'],
                    ['icon' => 'fas fa-graduation-cap', 'title' => 'Education', 'color' => 'from-green-500 to-green-600'],
                    ['icon' => 'fas fa-hotel', 'title' => 'Hospitality', 'color' => 'from-yellow-500 to-yellow-600'],
                    ['icon' => 'fas fa-truck', 'title' => 'Logistics', 'color' => 'from-indigo-500 to-indigo-600'],
                    ['icon' => 'fas fa-building', 'title' => 'Real Estate', 'color' => 'from-teal-500 to-teal-600'],
                    ['icon' => 'fas fa-warehouse', 'title' => 'Warehousing', 'color' => 'from-orange-500 to-orange-600'],
                    ['icon' => 'fas fa-utensils', 'title' => 'Food Service', 'color' => 'from-pink-500 to-pink-600']
                ];
                
                foreach ($industries as $index => $industry): ?>
                    <div class="industry-card bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-200 animate-fade-in" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                        <div class="w-16 h-16 bg-gradient-to-br <?php echo $industry['color']; ?> rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="<?php echo $industry['icon']; ?> text-2xl text-white"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg"><?php echo $industry['title']; ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Client Testimonials -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                        Client <span class="gradient-text">Testimonials</span>
                    </h2>
                    <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                    <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                        Hear what our clients say about our manpower services
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200 animate-fade-in">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-bold text-gray-900 text-lg">Manufacturing Client</h4>
                                <p class="text-orange-600 font-medium">Automotive Industry</p>
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star text-yellow-400"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="text-gray-600 italic leading-relaxed font-medium">
                            "KGN ENTERPRISES has been our reliable manpower partner for 3 years. Their quick deployment and quality workers have significantly improved our production efficiency."
                        </p>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.2s;">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-tie text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-bold text-gray-900 text-lg">IT Company</h4>
                                <p class="text-orange-600 font-medium">Software Development</p>
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star text-yellow-400"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="text-gray-600 italic leading-relaxed font-medium">
                            "Their IT professionals are well-screened and qualified. We've hired multiple developers through them and have been impressed with the quality and retention rate."
                        </p>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.4s;">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-hospital text-2xl text-white"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-bold text-gray-900 text-lg">Hospital Chain</h4>
                                <p class="text-orange-600 font-medium">Healthcare Services</p>
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star text-yellow-400"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="text-gray-600 italic leading-relaxed font-medium">
                            "The housekeeping and facility management staff provided by KGN are well-trained and professional. They maintain high hygiene standards in our hospitals."
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-gray-900 via-black to-gray-900 text-white relative overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">
                    Join Our Growing Client Family
                </h2>
                <p class="text-xl text-gray-300 mb-8 font-medium">
                    Experience the KGN ENTERPRISES difference in manpower solutions
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <a href="contact.php" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition shadow-lg transform hover:scale-105 duration-300 inline-flex items-center">
                        <i class="fas fa-handshake mr-3"></i> Start Partnership
                    </a>
                    <a href="tel:+919881901568" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-gray-900 transition transform hover:scale-105 duration-300 inline-flex items-center">
                        <i class="fas fa-phone mr-3"></i> Call +91 9881901568
                    </a>
                </div>
                <p class="text-gray-400 mt-8 text-sm font-medium">
                    <i class="fas fa-star text-yellow-400 mr-2"></i>
                    50+ satisfied clients | 5+ years of trust | 95% client retention
                </p>
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
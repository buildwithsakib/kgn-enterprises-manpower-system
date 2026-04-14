<?php
define('BASEPATH', true);
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get source from URL parameter or default to 'direct'
$source = isset($_GET['source']) ? $_GET['source'] : 'direct';
$page_source = $source;

// Handle contact form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $service_type = $_POST['service_type'] ?? '';
    $form_source = $_POST['form_source'] ?? $source; // Get source from form
    
    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        try {
            // Insert into database with source
            $stmt = $db->prepare("INSERT INTO contact_submissions (name, email, phone, subject, message, service_type, source, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$name, $email, $phone, $subject, $message, $service_type, $form_source]);
            
            // Send email notification (you can implement this based on your server configuration)
            // $to = "kgnenterprises9670@gmail.com";
            // $email_subject = "New Contact Form Submission: " . $subject;
            // $email_body = "Name: $name\nEmail: $email\nPhone: $phone\nSubject: $subject\nService Type: $service_type\nSource: $form_source\n\nMessage:\n$message";
            // mail($to, $email_subject, $email_body);
            
            $success_message = "Thank you for contacting us! We'll get back to you within 24 hours.";
            
            // Clear form fields
            $_POST = [];
            $page_source = $form_source; // Keep the source for form reset
            
        } catch(PDOException $e) {
            error_log("Contact form error: " . $e->getMessage());
            $error_message = "Sorry, there was an error submitting your message. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | KGN ENTERPRISES | Leading Manpower Services Provider in Pune</title>
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
        
        /* Form Input Styling */
        .form-input {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }
        
        .form-input:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
            outline: none;
        }
        
        /* Card Hover Effects */
        .contact-card {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Map Container */
        .map-container {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
    
    <!-- SEO Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="description" content="Contact KGN ENTERPRISES for premium manpower solutions in Pune. Get skilled workforce, staffing services, and facility management support.">
    <meta name="keywords" content="contact manpower agency Pune, staffing solutions contact, workforce provider, get manpower quote, contact KGN ENTERPRISES">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="Contact KGN ENTERPRISES | Premium Manpower Solutions">
    <meta property="og:description" content="Get in touch with Pune's leading manpower services provider for skilled workforce solutions and staffing services">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://kgcenterprises.com/contact.php">
    <meta property="og:image" content="https://kgcenterprises.com/uploads/settings/logo.png">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Contact KGN ENTERPRISES">
    <meta name="twitter:description" content="Your trusted partner for premium manpower solutions in Pune">
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
                        <span class="hidden md:block font-extrabold">KGN ENTERPRISES</span>

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
                    <a href="gallery.php" class="text-white hover:text-orange-400 transition font-bold">
                        Gallery
                    </a>
                    <a href="testimonials.php" class="text-white hover:text-orange-400 transition font-bold">
                        Testimonials
                    </a>
                    <a href="contact.php" class="text-white hover:text-orange-400 transition text-orange-400 font-bold">
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
                <a href="gallery.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-images mr-2"></i> Gallery
                </a>
                <a href="testimonials.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-quote-left mr-2"></i> Testimonials
                </a>
                <a href="contact.php" class="block text-white hover:text-orange-400 transition py-2 text-orange-400 font-bold">
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
                    Contact <span class="gradient-text">Us</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 animate-fade-in-delay">
                    Get in Touch for Premium Manpower Solutions
                </p>
                <p class="text-lg text-gray-300 max-w-3xl mx-auto mb-12 animate-fade-in-delay-2 font-medium">
                    Reach out to discuss your workforce needs. We're here to provide the best staffing solutions for your business in Pune and Maharashtra.
                </p>
                
                <!-- Quick Contact Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-3xl mx-auto animate-fade-in-delay-2">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white mb-2">24/7</div>
                        <div class="text-gray-300 text-sm font-medium">Support Available</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white mb-2">2 Hrs</div>
                        <div class="text-gray-300 text-sm font-medium">Response Time</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white mb-2">100%</div>
                        <div class="text-gray-300 text-sm font-medium">Confidential</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white mb-2">Free</div>
                        <div class="text-gray-300 text-sm font-medium">Consultation</div>
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

    <!-- Contact Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Contact Form -->
                    <div class="animate-fade-in">
                        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                                Send Us a <span class="gradient-text">Message</span>
                            </h2>
                            <div class="w-24 h-2 bg-orange-400 rounded-full mb-8"></div>
                            
                            <?php if ($success_message): ?>
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-green-700 font-medium"><?php echo $success_message; ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($error_message): ?>
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-exclamation-circle text-red-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-red-700 font-medium"><?php echo $error_message; ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="" class="space-y-6">
                                <!-- Hidden field to track source -->
                                <input type="hidden" name="form_source" value="<?php echo htmlspecialchars($page_source); ?>">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-gray-700 font-bold mb-3">
                                            Full Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               id="name" 
                                               name="name" 
                                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                               class="w-full px-4 py-3 rounded-xl form-input font-medium"
                                               placeholder="Enter your full name"
                                               required>
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-gray-700 font-bold mb-3">
                                            Email Address <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" 
                                               id="email" 
                                               name="email" 
                                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                               class="w-full px-4 py-3 rounded-xl form-input font-medium"
                                               placeholder="Enter your email"
                                               required>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="phone" class="block text-gray-700 font-bold mb-3">
                                            Phone Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="tel" 
                                               id="phone" 
                                               name="phone" 
                                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                               class="w-full px-4 py-3 rounded-xl form-input font-medium"
                                               placeholder="Enter your phone number"
                                               required>
                                    </div>
                                    
                                    <div>
                                        <label for="service_type" class="block text-gray-700 font-bold mb-3">
                                            Service Needed
                                        </label>
                                        <select id="service_type" 
                                                name="service_type"
                                                class="w-full px-4 py-3 rounded-xl form-input font-medium">
                                            <option value="">Select a service</option>
                                            <option value="skilled" <?php echo ($_POST['service_type'] ?? '') == 'skilled' ? 'selected' : ''; ?>>Skilled Manpower</option>
                                            <option value="unskilled" <?php echo ($_POST['service_type'] ?? '') == 'unskilled' ? 'selected' : ''; ?>>Unskilled Manpower</option>
                                            <option value="facility" <?php echo ($_POST['service_type'] ?? '') == 'facility' ? 'selected' : ''; ?>>Facility Management</option>
                                            <option value="it" <?php echo ($_POST['service_type'] ?? '') == 'it' ? 'selected' : ''; ?>>IT Staffing</option>
                                            <option value="housekeeping" <?php echo ($_POST['service_type'] ?? '') == 'housekeeping' ? 'selected' : ''; ?>>Housekeeping</option>
                                            <option value="other" <?php echo ($_POST['service_type'] ?? '') == 'other' ? 'selected' : ''; ?>>Other Services</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="subject" class="block text-gray-700 font-bold mb-3">
                                        Subject
                                    </label>
                                    <input type="text" 
                                           id="subject" 
                                           name="subject" 
                                           value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>"
                                           class="w-full px-4 py-3 rounded-xl form-input font-medium"
                                           placeholder="Enter subject of your inquiry">
                                </div>
                                
                                <div>
                                    <label for="message" class="block text-gray-700 font-bold mb-3">
                                        Message <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="message" 
                                              name="message" 
                                              rows="6"
                                              class="w-full px-4 py-3 rounded-xl form-input font-medium resize-none"
                                              placeholder="Tell us about your manpower requirements, number of workers needed, duration, and any specific skills required..."
                                              required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                </div>
                                
                                <button type="submit" 
                                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 rounded-xl font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition shadow-lg transform hover:scale-[1.02] duration-300 inline-flex items-center justify-center">
                                    <i class="fas fa-paper-plane mr-3"></i> Send Message
                                </button>
                                
                                <p class="text-gray-500 text-sm text-center font-medium mt-4">
                                    <i class="fas fa-lock mr-2"></i> Your information is secure and will not be shared with third parties
                                </p>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="space-y-8 animate-fade-in-delay">
                        <!-- Contact Info Cards -->
                        <div class="bg-gradient-to-br from-gray-900 to-black rounded-2xl shadow-lg p-8 text-white">
                            <h2 class="text-3xl md:text-4xl font-bold mb-2">
                                Get in <span class="text-orange-400">Touch</span>
                            </h2>
                            <div class="w-24 h-2 bg-orange-400 rounded-full mb-8"></div>
                            
                            <div class="space-y-8">
                                <div class="flex items-start space-x-4">
                                    <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-orange-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xl mb-2">Office Address</h3>
                                        <p class="text-gray-300 font-medium">
                                            185 Ground, Javed Turuk, Shendewadi Phata,Turukwadi,<br>
                                            Kadus, Khed, Pune - 412404, Maharashtra
                                        </p>
                                        <a href="https://maps.google.com/?q=185 Ground, Javed Turuk, Shendewadi Phata, Kadus, Khed, Pune 412404" 
                                           target="_blank"
                                           class="inline-flex items-center text-orange-400 hover:text-orange-300 transition mt-3 font-medium">
                                            <i class="fas fa-directions mr-2"></i> Get Directions
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-4">
                                    <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-phone text-orange-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xl mb-2">Phone Numbers</h3>
                                        <div class="space-y-2">
                                            <a href="tel:+919881901568" class="block text-gray-300 hover:text-orange-400 transition font-medium text-lg">
                                                <i class="fas fa-mobile-alt mr-2"></i> +91 9881901568
                                            </a>
                                            <a href="tel:+919423042591" class="block text-gray-300 hover:text-orange-400 transition font-medium text-lg">
                                                <i class="fas fa-phone-alt mr-2"></i> +91 9423042591
                                            </a>
                                        </div>
                                        <p class="text-gray-400 text-sm mt-3 font-medium">
                                            <i class="far fa-clock mr-2"></i> Mon-Sat: 9:00 AM - 6:00 PM
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-4">
                                    <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-envelope text-orange-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xl mb-2">Email Address</h3>
                                        <a href="mailto:kgnenterprises9670@gmail.com" 
                                           class="block text-gray-300 hover:text-orange-400 transition font-medium text-lg mb-2">
                                            <i class="fas fa-envelope mr-2"></i> kgnenterprises9670@gmail.com
                                        </a>
                                        <p class="text-gray-400 text-sm font-medium">
                                            <i class="fas fa-headset mr-2"></i> 24/7 Email Support Available
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Emergency Contact -->
                        <div class="contact-card bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl shadow-lg p-8 text-white">
                            <div class="flex items-start space-x-4">
                                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-headset text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-2xl mb-3">Urgent Requirement?</h3>
                                    <p class="text-orange-100 mb-6 font-medium">
                                        Need immediate manpower for emergency projects? Call us now for quick deployment within 24 hours.
                                    </p>
                                    <a href="tel:+919881901568" 
                                       class="inline-flex items-center bg-white text-orange-600 px-8 py-4 rounded-full font-bold hover:bg-gray-100 transition duration-300 shadow-lg transform hover:scale-105">
                                        <i class="fas fa-phone mr-3"></i> Emergency Call: +91 9881901568
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Social Media -->
                        <div class="contact-card bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                            <h3 class="font-bold text-gray-900 text-2xl mb-6">Connect With Us</h3>
                            <div class="flex space-x-4">
                                <a href="https://www.linkedin.com/in/sakib-shaikh-b5755b397" 
                                   class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center hover:bg-blue-100 transition duration-300">
                                    <i class="fab fa-linkedin-in text-blue-700 text-xl"></i>
                                </a>
                                <a href="https://www.instagram.com/sakib__.2006" 
                                   class="w-14 h-14 bg-pink-100 rounded-2xl flex items-center justify-center hover:bg-pink-200 transition duration-300">
                                    <i class="fab fa-instagram text-pink-600 text-xl"></i>
                                </a>
                                <a href="https://wa.me/919881901568" 
                                   class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center hover:bg-green-200 transition duration-300">
                                    <i class="fab fa-whatsapp text-green-600 text-xl"></i>
                                </a>
                            </div>
                            <p class="text-gray-500 text-sm mt-6 font-medium">
                                Follow us for updates on job openings, industry news, and manpower solutions
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                        Find Our <span class="gradient-text">Location</span>
                    </h2>
                    <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                    <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                        Visit our office in Pune or get directions to discuss your manpower needs in person
                    </p>
                </div> 
                <div class="map-container animate-fade-in">
                    <!-- Google Map Embed -->
                    <div class="h-96 w-full">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3780.349493835528!2d73.85694967497264!3d18.651545382480434!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2c9c9b8c5a5a5%3A0x8b8c5a5a5a5a5a5!2sPune%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1647851234567!5m2!1sen!2sin"
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Inquiry Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="bg-gradient-to-r from-gray-900 to-black rounded-2xl shadow-lg p-12 text-white text-center">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Need Quick Manpower?</h2>
                    <p class="text-gray-300 text-lg mb-8 font-medium">
                        Submit a quick inquiry for immediate response. Our team will contact you within 2 hours.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                        <a href="tel:+919881901568" 
                           class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition shadow-lg transform hover:scale-105 duration-300 inline-flex items-center">
                            <i class="fas fa-phone mr-3"></i> Call Now
                        </a>
                        <a href="https://wa.me/919881901568?text=Hello%20KGN%20ENTERPRISES,%20I%20need%20information%20about%20manpower%20services" 
                           target="_blank"
                           class="bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-green-600 hover:to-green-700 transition shadow-lg transform hover:scale-105 duration-300 inline-flex items-center">
                            <i class="fab fa-whatsapp mr-3"></i> WhatsApp Us
                        </a>
                    </div>
                    <p class="text-gray-400 text-sm mt-8 font-medium">
                        <i class="fas fa-bolt mr-2"></i> Average response time: 30 minutes during business hours
                    </p>
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

        // Contact form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const phoneInput = document.getElementById('phone');
            
            // Phone number validation
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9+]/g, '');
            });
            
            // Form submission validation
            if (form) {
                form.addEventListener('submit', function(e) {
                    const phone = phoneInput.value;
                    const email = document.getElementById('email').value;
                    
                    // Validate phone number (Indian format)
                    const phoneRegex = /^(\+91[\-\s]?)?[0]?(91)?[789]\d{9}$/;
                    if (!phoneRegex.test(phone.replace(/\s+/g, ''))) {
                        e.preventDefault();
                        alert('Please enter a valid Indian phone number (10 digits starting with 7, 8, or 9).');
                        phoneInput.focus();
                        return false;
                    }
                    
                    // Validate email
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        e.preventDefault();
                        alert('Please enter a valid email address.');
                        document.getElementById('email').focus();
                        return false;
                    }
                    
                    return true;
                });
            }
            
            // Auto-detect source if not passed via URL
            const sourceInput = document.querySelector('input[name="form_source"]');
            if (sourceInput && sourceInput.value === 'direct') {
                // Try to get from URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const urlSource = urlParams.get('source');
                
                if (urlSource) {
                    sourceInput.value = urlSource;
                } else {
                    // Try to detect from referrer
                    const referrer = document.referrer;
                    if (referrer && referrer.includes(window.location.hostname)) {
                        // Extract page name from referrer
                        const referrerUrl = new URL(referrer);
                        const pageName = referrerUrl.pathname.split('/').pop().replace('.php', '') || 'index';
                        sourceInput.value = pageName;
                    }
                }
            }
        });
    </script>
</body>
</html>
<?php
define('BASEPATH', true);
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Initialize variables
$success_message = '';
$error_message = '';
$submitted = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_name = trim($_POST['client_name'] ?? '');
    $client_designation = trim($_POST['client_designation'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $testimonial = trim($_POST['testimonial'] ?? '');
    $rating = intval($_POST['rating'] ?? 5);
    $service_type = trim($_POST['service_type'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Validate input
    $errors = [];
    
    if (empty($client_name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($testimonial)) {
        $errors[] = "Testimonial text is required";
    }
    
    if (empty($email) && empty($phone)) {
        $errors[] = "Either email or phone is required for verification";
    }
    
    if ($rating < 1 || $rating > 5) {
        $errors[] = "Rating must be between 1 and 5";
    }
    
    // Handle image upload
    $client_image = '';
    if (isset($_FILES['client_image']) && $_FILES['client_image']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (in_array($_FILES['client_image']['type'], $allowed_types)) {
            if ($_FILES['client_image']['size'] <= $max_size) {
                $upload_dir = 'uploads/testimonials/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['client_image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . $file_extension;
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['client_image']['tmp_name'], $target_path)) {
                    $client_image = $filename;
                } else {
                    $errors[] = "Failed to upload image";
                }
            } else {
                $errors[] = "Image size should be less than 2MB";
            }
        } else {
            $errors[] = "Only JPG, JPEG, PNG, and GIF images are allowed";
        }
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
    INSERT INTO testimonials (
        client_name, client_designation, company, testimonial, rating, service_type,
        client_image, email, phone, is_approved, featured, display_order, created_at
    ) VALUES (
        :client_name, :client_designation, :company, :testimonial, :rating, :service_type,
        :client_image, :email, :phone, FALSE, FALSE, 0, NOW()
    )
");
            
            $stmt->execute([
                ':client_name' => $client_name,
                ':client_designation' => $client_designation,
                ':company' => $company,
                ':testimonial' => $testimonial,
                ':rating' => $rating,
                ':service_type' => $service_type,
                ':client_image' => $client_image,
                ':email' => $email,
                ':phone' => $phone
            ]);
            
            $submitted = true;
            $success_message = "Thank you for your testimonial! It has been submitted for review and will be published once approved.";
            
            // Clear form fields
            $_POST = [];
            
        } catch(PDOException $e) {
            error_log("Testimonial submission error: " . $e->getMessage());
            $error_message = "An error occurred while submitting your testimonial. Please try again.";
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// Get all testimonials
$testimonials = [];

try {
    // Get all approved testimonials
    $stmt = $db->prepare("SELECT * FROM testimonials WHERE is_approved = TRUE ORDER BY featured DESC, display_order ASC, created_at DESC");
    $stmt->execute();
    $testimonials = $stmt->fetchAll();
    
} catch(PDOException $e) {
    error_log("Testimonials data error: " . $e->getMessage());
}

// Get featured testimonials separately
$featured_testimonials = [];
try {
    $stmt = $db->prepare("SELECT * FROM testimonials WHERE is_approved = TRUE AND featured = TRUE ORDER BY display_order ASC, created_at DESC LIMIT 6");
    $stmt->execute();
    $featured_testimonials = $stmt->fetchAll();
} catch(PDOException $e) {
    error_log("Featured testimonials error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials | KGN ENTERPRISES | Client Reviews & Feedback</title>
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
        .testimonial-card {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: white;
        }
        
        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Quote Icon */
        .quote-icon {
            color: #f97316;
            opacity: 0.1;
            font-size: 5rem;
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
        
        /* Rating Stars */
        .rating-stars .active {
            color: #f97316;
        }
        
        /* Testimonial Image */
        .testimonial-image {
            width: 4rem;
            height: 4rem;
            object-fit: cover;
            border: 3px solid #f97316;
        }
        
        /* Stats Card */
        .stats-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        /* Form Styles */
        .form-input {
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }
        
        .required-field::after {
            content: " *";
            color: #ef4444;
        }
        
        /* Star Rating Input */
        .star-rating {
            display: inline-flex;
            flex-direction: row-reverse;
            font-size: 1.5rem;
            justify-content: space-around;
            padding: 0.5em 0;
            text-align: center;
            width: 10em;
        }
        
        .star-rating input {
            display: none;
        }
        
        .star-rating label {
            color: #ccc;
            cursor: pointer;
        }
        
        .star-rating :checked ~ label {
            color: #f97316;
        }
        
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #f97316;
        }
        
        /* File Upload */
        .file-upload {
            border: 2px dashed #d1d5db;
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-upload:hover {
            border-color: #f97316;
            background-color: #fffbeb;
        }
        
        .file-upload i {
            font-size: 2.5rem;
            color: #f97316;
            margin-bottom: 1rem;
        }
        
        /* Success/Error Messages */
        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-left: 4px solid #10b981;
            color: #065f46;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-left: 4px solid #ef4444;
            color: #991b1b;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 1rem;
            max-width: 90%;
            width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
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
                    <a href="gallery.php" class="text-white hover:text-orange-400 transition font-bold">
                        Gallery
                    </a>
                    <a href="testimonials.php" class="text-white hover:text-orange-400 transition text-orange-400 font-bold">
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
                <a href="gallery.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-images mr-2"></i> Gallery
                </a>
                <a href="testimonials.php" class="block text-white hover:text-orange-400 transition py-2 text-orange-400 font-bold">
                    <i class="fas fa-quote-left mr-2"></i> Testimonials
                </a>
                <a href="contact.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-envelope mr-2"></i> Contact Us
                </a>
            </div>
        </div>
    </nav>

    <!-- Success/Error Messages -->
    <?php if ($success_message): ?>
    <div class="container mx-auto px-4 pt-6">
        <div class="max-w-6xl mx-auto">
            <div class="alert-success animate-fade-in">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span class="font-bold"><?php echo $success_message; ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
    <div class="container mx-auto px-4 pt-6">
        <div class="max-w-6xl mx-auto">
            <div class="alert-error animate-fade-in">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <span class="font-bold"><?php echo $error_message; ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

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
                    Client <span class="gradient-text">Testimonials</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 animate-fade-in-delay">
                    Real Feedback from Our Valued Clients in Pune
                </p>
                <p class="text-lg text-gray-300 max-w-3xl mx-auto mb-12 animate-fade-in-delay-2 font-medium">
                    Discover what businesses across various industries say about our manpower services and staffing solutions
                </p>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-3xl mx-auto animate-fade-in-delay-2">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white mb-2"><?php echo count($testimonials); ?>+</div>
                        <div class="text-gray-300 text-sm font-medium">Testimonials</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white mb-2">4.8</div>
                        <div class="text-gray-300 text-sm font-medium">Average Rating</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white mb-2">95%</div>
                        <div class="text-gray-300 text-sm font-medium">Client Satisfaction</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white mb-2">80%</div>
                        <div class="text-gray-300 text-sm font-medium">Repeat Business</div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="mt-12 animate-fade-in-delay-2">
                    <button onclick="openSubmitForm()" class="inline-flex items-center bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition shadow-lg transform hover:scale-105">
                        <i class="fas fa-pencil-alt mr-3"></i> Share Your Experience
                    </button>
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

    <!-- Testimonials Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <!-- Section Header -->
                <div class="text-center mb-16 animate-fade-in">
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                        What Our <span class="gradient-text">Clients Say</span>
                    </h2>
                    <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                    <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                        Hear from businesses across Pune and Maharashtra who have experienced our premium manpower solutions
                    </p>
                </div>

                <?php if (!empty($testimonials)): ?>
                    <!-- Testimonials Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                        <?php foreach ($testimonials as $index => $testimonial): ?>
                            <div class="testimonial-card rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: <?php echo ($index % 3) * 0.1; ?>s">
                                <?php if ($testimonial['featured']): ?>
                                    <div class="absolute top-4 left-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-500 text-white">
                                            <i class="fas fa-star mr-1"></i> Featured
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Quote Icon -->
                                <i class="fas fa-quote-right quote-icon"></i>
                                
                                <!-- Rating -->
                                <div class="flex items-center mb-6">
                                    <div class="rating-stars flex space-x-1 mr-4">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star text-lg <?php echo $i <= $testimonial['rating'] ? 'text-yellow-400 active' : 'text-gray-300'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-gray-600 font-bold"><?php echo number_format($testimonial['rating'], 1); ?>/5</span>
                                </div>
                                
                                <!-- Testimonial Text -->
                                <div class="mb-8">
                                    <p class="text-gray-600 leading-relaxed font-medium italic">
                                        "<?php echo htmlspecialchars($testimonial['testimonial']); ?>"
                                    </p>
                                </div>
                                
                                <!-- Client Info -->
                                <div class="flex items-center pt-6 border-t border-gray-100">
                                    <div class="flex-shrink-0 mr-4">
                                        <?php if (!empty($testimonial['client_image'])): ?>
                                            <img src="uploads/testimonials/<?php echo htmlspecialchars($testimonial['client_image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($testimonial['client_name']); ?>"
                                                 class="testimonial-image rounded-full"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                                        <?php endif; ?>
                                        <div class="w-16 h-16 bg-gradient-to-br from-gray-900 to-black rounded-full flex items-center justify-center <?php if (!empty($testimonial['client_image'])) echo 'hidden'; ?>">
                                            <i class="fas fa-user text-2xl text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-lg"><?php echo htmlspecialchars($testimonial['client_name']); ?></h4>
                                        <p class="text-orange-600 font-bold"><?php echo htmlspecialchars($testimonial['client_designation']); ?></p>
                                        <?php if (!empty($testimonial['company'])): ?>
                                            <p class="text-gray-500 text-sm font-medium"><?php echo htmlspecialchars($testimonial['company']); ?></p>
                                        <?php endif; ?>
                                        <p class="text-gray-500 text-xs mt-2">
                                            <i class="far fa-calendar mr-1"></i>
                                            <?php echo date('F j, Y', strtotime($testimonial['created_at'])); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Service Type Badge -->
                                <?php if (!empty($testimonial['service_type'])): ?>
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-orange-100 text-orange-700">
                                            <i class="fas fa-tag mr-1"></i>
                                            <?php echo htmlspecialchars($testimonial['service_type']); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- No Testimonials -->
                    <div class="text-center py-16 animate-fade-in">
                        <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-comments text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-600 mb-4">No Testimonials Yet</h3>
                        <p class="text-gray-500 max-w-md mx-auto mb-8 font-medium">
                            Be the first to share your experience with KGN ENTERPRISES! Your feedback helps us improve our services.
                        </p>
                        <button onclick="openSubmitForm()" class="inline-flex items-center bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-full font-bold hover:from-orange-600 hover:to-orange-700 transition">
                            <i class="fas fa-pencil-alt mr-2"></i> Share Your Experience
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Featured Testimonials -->
    <?php if (!empty($featured_testimonials)): ?>
    <section class="py-16 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                        Featured <span class="gradient-text">Reviews</span>
                    </h2>
                    <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                    <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                        Highlighted testimonials from our most valued clients across different industries
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <?php foreach ($featured_testimonials as $index => $testimonial): ?>
                        <div class="stats-card rounded-2xl shadow-lg p-8 animate-fade-in" style="animation-delay: <?php echo $index * 0.2; ?>s">
                            <div class="flex items-start mb-6">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center mr-4 overflow-hidden">
                                    <?php if (!empty($testimonial['client_image'])): ?>
                                        <img src="uploads/testimonials/<?php echo htmlspecialchars($testimonial['client_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($testimonial['client_name']); ?>"
                                             class="w-full h-full object-cover"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                    <?php endif; ?>
                                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center <?php if (!empty($testimonial['client_image'])) echo 'hidden'; ?>">
                                        <i class="fas fa-user text-2xl text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($testimonial['company'] ?: $testimonial['service_type'] ?: 'Client'); ?></h3>
                                    <?php if (!empty($testimonial['service_type'])): ?>
                                        <p class="text-gray-300 font-medium"><?php echo htmlspecialchars($testimonial['service_type']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex mb-6">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'text-yellow-400' : 'text-gray-600'; ?> mr-1"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-gray-300 italic leading-relaxed mb-6 font-medium">
                                "<?php echo htmlspecialchars($testimonial['testimonial']); ?>"
                            </p>
                            <div class="pt-6 border-t border-gray-700">
                                <p class="text-white font-bold"><?php echo htmlspecialchars($testimonial['client_name']); ?></p>
                                <?php if (!empty($testimonial['client_designation'])): ?>
                                    <p class="text-gray-300"><?php echo htmlspecialchars($testimonial['client_designation']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($testimonial['company'])): ?>
                                    <p class="text-gray-300 text-sm"><?php echo htmlspecialchars($testimonial['company']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Submit Testimonial Form Modal -->
    <div id="submitTestimonialModal" class="modal">
        <div class="modal-content animate-fade-in">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-pencil-alt mr-2 text-orange-500"></i>
                        Share Your Experience
                    </h3>
                    <button onclick="closeSubmitForm()" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <!-- Form -->
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="form-label required-field">Your Name</label>
                            <input type="text" name="client_name" required 
                                   value="<?php echo htmlspecialchars($_POST['client_name'] ?? ''); ?>"
                                   class="form-input" placeholder="John Doe">
                        </div>
                        
                        <!-- Designation -->
                        <div>
                            <label class="form-label">Your Designation</label>
                            <input type="text" name="client_designation" 
                                   value="<?php echo htmlspecialchars($_POST['client_designation'] ?? ''); ?>"
                                   class="form-input" placeholder="HR Manager">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Company -->
                        <div>
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company" 
                                   value="<?php echo htmlspecialchars($_POST['company'] ?? ''); ?>"
                                   class="form-input" placeholder="ABC Corporation">
                        </div>
                        
                        <!-- Service Type -->
                        <div>
                            <label class="form-label">Service Type</label>
                            <select name="service_type" class="form-input">
                                <option value="">Select Service</option>
                                <option value="Skilled Manpower" <?php echo ($_POST['service_type'] ?? '') == 'Skilled Manpower' ? 'selected' : ''; ?>>Skilled Manpower</option>
                                <option value="Unskilled Manpower" <?php echo ($_POST['service_type'] ?? '') == 'Unskilled Manpower' ? 'selected' : ''; ?>>Unskilled Manpower</option>
                                <option value="Facility Management" <?php echo ($_POST['service_type'] ?? '') == 'Facility Management' ? 'selected' : ''; ?>>Facility Management</option>
                                <option value="IT Professionals" <?php echo ($_POST['service_type'] ?? '') == 'IT Professionals' ? 'selected' : ''; ?>>IT Professionals</option>
                                <option value="Housekeeping" <?php echo ($_POST['service_type'] ?? '') == 'Housekeeping' ? 'selected' : ''; ?>>Housekeeping</option>
                                <option value="Security Services" <?php echo ($_POST['service_type'] ?? '') == 'Security Services' ? 'selected' : ''; ?>>Security Services</option>
                                <option value="Other" <?php echo ($_POST['service_type'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email -->
                        <div>
                            <label class="form-label required-field">Email Address</label>
                            <input type="email" name="email" required 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   class="form-input" placeholder="john@example.com">
                            <p class="text-gray-500 text-sm mt-1">For verification purposes only</p>
                        </div>
                        
                        <!-- Phone -->
                        <div>
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" 
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                   class="form-input" placeholder="+91 9876543210">
                        </div>
                    </div>
                    
                    <!-- Rating -->
                    <div>
                        <label class="form-label required-field">Your Rating</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" <?php echo ($_POST['rating'] ?? 5) == 5 ? 'checked' : ''; ?>>
                            <label for="star5" title="Excellent">★</label>
                            <input type="radio" id="star4" name="rating" value="4" <?php echo ($_POST['rating'] ?? 5) == 4 ? 'checked' : ''; ?>>
                            <label for="star4" title="Good">★</label>
                            <input type="radio" id="star3" name="rating" value="3" <?php echo ($_POST['rating'] ?? 5) == 3 ? 'checked' : ''; ?>>
                            <label for="star3" title="Average">★</label>
                            <input type="radio" id="star2" name="rating" value="2" <?php echo ($_POST['rating'] ?? 5) == 2 ? 'checked' : ''; ?>>
                            <label for="star2" title="Poor">★</label>
                            <input type="radio" id="star1" name="rating" value="1" <?php echo ($_POST['rating'] ?? 5) == 1 ? 'checked' : ''; ?>>
                            <label for="star1" title="Very Poor">★</label>
                        </div>
                    </div>
                    
                    <!-- Testimonial Text -->
                    <div>
                        <label class="form-label required-field">Your Testimonial</label>
                        <textarea name="testimonial" required rows="5" 
                                  class="form-input" 
                                  placeholder="Share your experience with KGN ENTERPRISES..."><?php echo htmlspecialchars($_POST['testimonial'] ?? ''); ?></textarea>
                        <p class="text-gray-500 text-sm mt-1">Minimum 50 characters. Be honest and detailed about your experience.</p>
                    </div>
                    
                    <!-- Photo Upload -->
                    <div>
                        <label class="form-label">Your Photo (Optional)</label>
                        <div class="file-upload" onclick="document.getElementById('client_image').click()">
                            <input type="file" id="client_image" name="client_image" accept="image/*" class="hidden" 
                                   onchange="previewImage(this)">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p class="text-gray-600 font-bold mb-2">Click to upload your photo</p>
                            <p class="text-gray-500 text-sm">JPG, PNG or GIF (Max 2MB)</p>
                            <p class="text-gray-500 text-sm mt-2" id="file-name">No file chosen</p>
                        </div>
                        <div id="image-preview" class="mt-4 hidden">
                            <img id="preview" class="w-32 h-32 object-cover rounded-full border-4 border-orange-200">
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="closeSubmitForm()" 
                                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-full font-bold hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-full font-bold hover:from-orange-600 hover:to-orange-700 transition shadow-lg">
                                <i class="fas fa-paper-plane mr-2"></i> Submit Testimonial
                            </button>
                        </div>
                        <p class="text-gray-500 text-sm mt-4 text-center">
                            <i class="fas fa-info-circle mr-1"></i> All testimonials are reviewed and approved before publishing
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Trust Indicators -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="bg-gradient-to-r from-gray-900 to-black rounded-2xl shadow-lg p-12 text-white text-center">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">Trusted by Businesses Across Pune</h2>
                    <p class="text-gray-300 text-lg mb-8 font-medium">
                        Join 50+ satisfied clients who rely on KGN ENTERPRISES for their manpower needs
                    </p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
                        <div>
                            <div class="text-3xl font-bold text-orange-400 mb-2">50+</div>
                            <div class="text-gray-300 font-medium">Clients</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-orange-400 mb-2">95%</div>
                            <div class="text-gray-300 font-medium">Satisfaction</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-orange-400 mb-2">24/7</div>
                            <div class="text-gray-300 font-medium">Support</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-orange-400 mb-2">5+</div>
                            <div class="text-gray-300 font-medium">Years</div>
                        </div>
                    </div>
                    <button onclick="openSubmitForm()" 
                           class="inline-flex items-center bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition shadow-lg transform hover:scale-105">
                        <i class="fas fa-star mr-3"></i> Share Your Experience
                    </button>
                </div>
            </div>
        </div>
    </section>

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
                        <li><a href="testimonials.php" class="hover:text-orange-400 transition font-medium"><i class="fas fa-chevron-right mr-2"></i> Testimonials</a></li>
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

        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Modal functions
        function openSubmitForm() {
            document.getElementById('submitTestimonialModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSubmitForm() {
            document.getElementById('submitTestimonialModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSubmitForm();
            }
        });

        // Close modal when clicking outside
        document.getElementById('submitTestimonialModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSubmitForm();
            }
        });

        // Image preview for file upload
        function previewImage(input) {
            const file = input.files[0];
            const fileName = document.getElementById('file-name');
            const previewContainer = document.getElementById('image-preview');
            const preview = document.getElementById('preview');
            
            if (file) {
                fileName.textContent = file.name;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                fileName.textContent = 'No file chosen';
                previewContainer.classList.add('hidden');
            }
        }

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

        // Testimonial card hover effects
        document.addEventListener('DOMContentLoaded', function() {
            const testimonialCards = document.querySelectorAll('.testimonial-card');
            
            testimonialCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    const quoteIcon = this.querySelector('.quote-icon');
                    if (quoteIcon) {
                        quoteIcon.style.opacity = '0.2';
                        quoteIcon.style.transform = 'scale(1.1)';
                    }
                });
                
                card.addEventListener('mouseleave', function() {
                    const quoteIcon = this.querySelector('.quote-icon');
                    if (quoteIcon) {
                        quoteIcon.style.opacity = '0.1';
                        quoteIcon.style.transform = 'scale(1)';
                    }
                });
            });
            
            // Auto-open modal if form was submitted with errors
            <?php if ($error_message && $_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                openSubmitForm();
            <?php endif; ?>
        });
    </script>
</body>
</html>
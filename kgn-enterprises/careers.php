<?php
define('BASEPATH', true);
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Fetch active jobs with error handling
try {
    $jobs_query = "SELECT * FROM jobs WHERE is_active = TRUE AND (deadline >= CURDATE() OR deadline IS NULL) ORDER BY posted_at DESC";
    $jobs_stmt = $db->prepare($jobs_query);
    $jobs_stmt->execute();
    $jobs = $jobs_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $jobs = [];
    error_log("Error fetching jobs: " . $e->getMessage());
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job'])) {
    try {
        $full_name = sanitize_input($_POST['full_name']);
        $email = sanitize_input($_POST['email']);
        $phone = sanitize_input($_POST['phone']);
        $position = sanitize_input($_POST['position']);
        $experience = sanitize_input($_POST['experience']);
        $qualification = sanitize_input($_POST['qualification']);
        $current_ctc = sanitize_input($_POST['current_ctc'] ?? '');
        $expected_ctc = sanitize_input($_POST['expected_ctc']);
        $cover_letter = sanitize_input($_POST['cover_letter'] ?? '');
        
        // Validate required fields
        if (empty($full_name) || empty($email) || empty($phone) || empty($position) || 
            empty($experience) || empty($qualification) || empty($expected_ctc)) {
            throw new Exception('All required fields must be filled.');
        }
        
        if (!validate_email($email)) {
            throw new Exception('Please enter a valid email address.');
        }
        
        // Handle file upload
        $resume_path = '';
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === 0) {
            $upload_dir = 'uploads/resumes/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['pdf', 'doc', 'docx'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                throw new Exception('Please upload a PDF, DOC, or DOCX file only.');
            }
            
            // Check file size (2MB max)
            if ($_FILES['resume']['size'] > 2 * 1024 * 1024) {
                throw new Exception('File size should not exceed 2MB.');
            }
            
            $filename = 'resume_' . time() . '_' . uniqid() . '.' . $file_extension;
            $resume_path = $upload_dir . $filename;
            
            if (!move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path)) {
                throw new Exception('Failed to upload resume file. Please try again.');
            }
        } else {
            throw new Exception('Please upload your resume.');
        }

        $query = "INSERT INTO job_applications 
                  (full_name, email, phone, position, experience, qualification, 
                   current_ctc, expected_ctc, resume_path, cover_letter, applied_at)
                  VALUES (:full_name, :email, :phone, :position, :experience, :qualification,
                          :current_ctc, :expected_ctc, :resume_path, :cover_letter, NOW())";

        $stmt = $db->prepare($query);
        $stmt->bindParam(":full_name", $full_name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":position", $position);
        $stmt->bindParam(":experience", $experience);
        $stmt->bindParam(":qualification", $qualification);
        $stmt->bindParam(":current_ctc", $current_ctc);
        $stmt->bindParam(":expected_ctc", $expected_ctc);
        $stmt->bindParam(":resume_path", $resume_path);
        $stmt->bindParam(":cover_letter", $cover_letter);

        if ($stmt->execute()) {
            $success_message = "Thank you for your application! We will review your application and contact you soon.";
        } else {
            throw new Exception("Sorry, there was an error submitting your application. Please try again.");
        }
    } catch(Exception $exception) {
        $error_message = $exception->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers | KGN ENTERPRISES | Join Our Team & Job Opportunities</title>
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
        
        /* Job Card Hover Effects */
        .job-card {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .job-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        /* Benefit Card Hover */
        .benefit-card {
            transition: all 0.3s ease;
        }
        
        .benefit-card:hover {
            transform: translateY(-5px);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Modal Styles */
        .modal {
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }
        
        .modal.show {
            opacity: 1;
            pointer-events: all;
        }
        
        .modal-content {
            transform: translateY(-50px);
            transition: transform 0.3s ease;
        }
        
        .modal.show .modal-content {
            transform: translateY(0);
        }
    </style>
    
    <!-- SEO Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="description" content="Join KGN ENTERPRISES team. Explore career opportunities in manpower services, facility management, and staffing solutions in Pune.">
    <meta name="keywords" content="careers manpower Pune, job opportunities staffing, facility management jobs">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="Careers | KGN ENTERPRISES | Job Opportunities">
    <meta property="og:description" content="Join our team and explore career opportunities in manpower services">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://kgcenterprises.com/careers.php">
    <meta property="og:image" content="https://kgcenterprises.com/uploads/settings/logo.png">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Careers | KGN ENTERPRISES">
    <meta name="twitter:description" content="Join our growing team and build your career">
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
                    <a href="services.php" class="text-white hover:text-orange-400 transition font-bold">
                        Services
                    </a>
                    <a href="careers.php" class="text-white hover:text-orange-400 transition text-orange-400 font-bold">
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
                <a href="services.php" class="block text-white hover:text-orange-400 transition py-2 font-bold">
                    <i class="fas fa-briefcase mr-2"></i> Services
                </a>
                <a href="careers.php" class="block text-white hover:text-orange-400 transition py-2 text-orange-400 font-bold">
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
                    Join Our <span class="gradient-text">Team</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 animate-fade-in-delay">
                    Build Your Career with KGN ENTERPRISES
                </p>
                <p class="text-lg text-gray-300 max-w-3xl mx-auto mb-12 animate-fade-in-delay-2 font-medium">
                    Explore exciting career opportunities in manpower services and be part of our growing success story
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

    <!-- Current Openings -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Current <span class="gradient-text">Openings</span>
                </h2>
                <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                    Explore our current job opportunities and find the perfect role for your skills and career goals
                </p>
            </div>

            <div class="max-w-6xl mx-auto">
                <?php if (count($jobs) > 0): ?>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <?php foreach ($jobs as $index => $job): ?>
                            <div class="job-card bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($job['title']); ?></h3>
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">
                                        <?php echo ucfirst(str_replace('_', ' ', $job['job_type'])); ?>
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 mb-6 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2 text-gray-900"></i>
                                        <?php echo htmlspecialchars($job['location']); ?>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-briefcase mr-2 text-gray-900"></i>
                                        <?php echo htmlspecialchars($job['experience']); ?> experience
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-graduation-cap mr-2 text-gray-900"></i>
                                        <?php echo htmlspecialchars($job['qualification']); ?>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-rupee-sign mr-2 text-gray-900"></i>
                                        <?php echo htmlspecialchars($job['salary_range']); ?>
                                    </div>
                                </div>
                                
                                <p class="text-gray-600 mb-6 leading-relaxed font-medium">
                                    <?php echo substr(htmlspecialchars($job['description']), 0, 150) . '...'; ?>
                                </p>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?php if ($job['deadline']): ?>
                                            Apply before <?php echo date('M d, Y', strtotime($job['deadline'])); ?>
                                        <?php else: ?>
                                            Open until filled
                                        <?php endif; ?>
                                    </span>
                                    <button onclick="openApplicationModal('<?php echo htmlspecialchars($job['title'], ENT_QUOTES); ?>')" 
                                            class="inline-flex items-center bg-gradient-to-r from-gray-900 to-black text-white px-6 py-2 rounded-full text-sm font-bold hover:from-gray-800 hover:to-gray-900 transition shadow-lg transform hover:scale-105 duration-300">
                                        Apply Now
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 animate-fade-in">
                        <i class="fas fa-briefcase text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-600 mb-2">No Current Openings</h3>
                        <p class="text-gray-500 font-medium">We don't have any active job openings at the moment. Please check back later or send us your resume for future opportunities.</p>
                        <a href="contact.php" class="inline-flex items-center bg-gradient-to-r from-gray-900 to-black text-white px-6 py-3 rounded-full font-bold hover:from-gray-800 hover:to-gray-900 transition shadow-lg transform hover:scale-105 duration-300 mt-4">
                            <i class="fas fa-envelope mr-2"></i> Send General Application
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Application Form Modal -->
    <div id="applicationModal" class="modal fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90 p-4 hidden">
        <div class="modal-content relative bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <span class="close absolute top-4 right-4 text-gray-500 text-2xl cursor-pointer hover:text-gray-700 z-10">&times;</span>
            
            <div class="p-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Apply for <span id="modalJobTitle" class="text-orange-500"></span></h2>
                <div class="w-20 h-2 bg-orange-400 rounded-full mb-6"></div>
                
                <?php if ($success_message): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 font-medium">
                        <i class="fas fa-check-circle mr-2"></i><?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 font-medium">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form id="job-application-form" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="apply_job" value="1">
                    <input type="hidden" id="applicationPosition" name="position">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-gray-700 font-bold mb-2">Full Name *</label>
                            <input type="text" id="full_name" name="full_name" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300"
                                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                        </div>
                        <div>
                            <label for="email" class="block text-gray-700 font-bold mb-2">Email Address *</label>
                            <input type="email" id="email" name="email" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-gray-700 font-bold mb-2">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300"
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                        <div>
                            <label for="experience" class="block text-gray-700 font-bold mb-2">Experience *</label>
                            <input type="text" id="experience" name="experience" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300"
                                   placeholder="e.g., 3-5 years"
                                   value="<?php echo isset($_POST['experience']) ? htmlspecialchars($_POST['experience']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="qualification" class="block text-gray-700 font-bold mb-2">Qualification *</label>
                            <input type="text" id="qualification" name="qualification" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300"
                                   value="<?php echo isset($_POST['qualification']) ? htmlspecialchars($_POST['qualification']) : ''; ?>">
                        </div>
                        <div>
                            <label for="current_ctc" class="block text-gray-700 font-bold mb-2">Current CTC</label>
                            <input type="text" id="current_ctc" name="current_ctc" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300"
                                   value="<?php echo isset($_POST['current_ctc']) ? htmlspecialchars($_POST['current_ctc']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div>
                        <label for="expected_ctc" class="block text-gray-700 font-bold mb-2">Expected CTC *</label>
                        <input type="text" id="expected_ctc" name="expected_ctc" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300"
                               value="<?php echo isset($_POST['expected_ctc']) ? htmlspecialchars($_POST['expected_ctc']) : ''; ?>">
                    </div>
                    
                    <div>
                        <label for="resume" class="block text-gray-700 font-bold mb-2">Upload Resume *</label>
                        <input type="file" id="resume" name="resume" required accept=".pdf,.doc,.docx"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300">
                        <p class="text-sm text-gray-500 mt-1 font-medium">Accepted formats: PDF, DOC, DOCX (Max: 2MB)</p>
                    </div>
                    
                    <div>
                        <label for="cover_letter" class="block text-gray-700 font-bold mb-2">Cover Letter</label>
                        <textarea id="cover_letter" name="cover_letter" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300"
                                  placeholder="Tell us why you're interested in this position..."><?php echo isset($_POST['cover_letter']) ? htmlspecialchars($_POST['cover_letter']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-gray-900 to-black text-white py-4 px-6 rounded-lg font-bold text-lg hover:from-gray-800 hover:to-gray-900 transition shadow-lg transform hover:scale-105 duration-300">
                        <i class="fas fa-paper-plane mr-2"></i> Submit Application
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Why Work With Us Section -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Why <span class="gradient-text">Work With Us?</span>
                </h2>
                <div class="w-24 h-2 bg-orange-400 mx-auto rounded-full mb-6"></div>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto font-medium">
                    Join a team that values growth, collaboration, and excellence in the manpower services industry
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="benefit-card text-center bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-chart-line text-2xl text-gray-900"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Career Growth</h3>
                    <p class="text-gray-600 font-medium">Continuous learning opportunities and clear career progression paths</p>
                </div>

                <div class="benefit-card text-center bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-2xl text-gray-900"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Great Culture</h3>
                    <p class="text-gray-600 font-medium">Supportive work environment with team collaboration and work-life balance</p>
                </div>

                <div class="benefit-card text-center bg-white rounded-2xl shadow-lg p-8 border border-gray-200 animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-award text-2xl text-gray-900"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Competitive Benefits</h3>
                    <p class="text-gray-600 font-medium">Attractive compensation package with comprehensive benefits</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-gray-900 via-black to-gray-900 text-white relative overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">Can't Find Your Role?</h2>
                <p class="text-xl text-gray-300 mb-8 font-medium">
                    Send us your resume anyway! We're always looking for talented people.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <a href="contact.php" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition shadow-lg transform hover:scale-105 duration-300 inline-flex items-center">
                        <i class="fas fa-envelope mr-3"></i> Send General Application
                    </a>
                    <a href="tel:+919881901568" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-gray-900 transition transform hover:scale-105 duration-300 inline-flex items-center">
                        <i class="fas fa-phone mr-3"></i> Call for Inquiry
                    </a>
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

        function openApplicationModal(jobTitle) {
            document.getElementById('modalJobTitle').textContent = jobTitle;
            document.getElementById('applicationPosition').value = jobTitle;
            document.getElementById('applicationModal').classList.remove('hidden');
            document.getElementById('applicationModal').classList.add('show');
            document.body.style.overflow = 'hidden';
            
            // Clear previous messages when opening modal
            const successMessage = document.querySelector('.bg-green-100');
            const errorMessage = document.querySelector('.bg-red-100');
            if (successMessage) successMessage.style.display = 'none';
            if (errorMessage) errorMessage.style.display = 'none';
        }

        function closeApplicationModal() {
            document.getElementById('applicationModal').classList.remove('show');
            document.getElementById('applicationModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking close button
        document.querySelector('.close').addEventListener('click', closeApplicationModal);

        // Close modal when clicking outside
        document.getElementById('applicationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApplicationModal();
            }
        });

        // Handle form submission success
        <?php if ($success_message): ?>
            setTimeout(() => {
                closeApplicationModal();
                document.getElementById('job-application-form').reset();
            }, 3000);
        <?php endif; ?>

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

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeApplicationModal();
            }
        });
    </script>
</body>
</html>
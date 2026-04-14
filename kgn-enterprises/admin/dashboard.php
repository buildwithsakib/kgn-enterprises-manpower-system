<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get statistics
try {
    $stats = [];
    
    // Total services
    $stmt = $db->query("SELECT COUNT(*) as count FROM services WHERE is_active = 1");
    $stats['services'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total jobs
    $stmt = $db->query("SELECT COUNT(*) as count FROM jobs WHERE is_active = 1");
    $stats['jobs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total testimonials
    $stmt = $db->query("SELECT COUNT(*) as count FROM testimonials WHERE is_approved = 1");
    $stats['testimonials'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total clients
    $stmt = $db->query("SELECT COUNT(*) as count FROM clients WHERE is_featured = 1");
    $stats['clients'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending testimonials
    $stmt = $db->query("SELECT COUNT(*) as count FROM testimonials WHERE is_approved = 0");
    $stats['pending_testimonials'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending job applications
    $stmt = $db->query("SELECT COUNT(*) as count FROM job_applications WHERE status = 'pending'");
    $stats['pending_applications'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Recent contact submissions
    $stmt = $db->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'pending'");
    $stats['pending_contacts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Recent job applications
    $stmt = $db->query("SELECT ja.*, j.title as job_title FROM job_applications ja 
                       LEFT JOIN jobs j ON ja.job_id = j.id 
                       WHERE ja.status = 'pending' 
                       ORDER BY ja.applied_at DESC LIMIT 5");
    $recent_applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent contact submissions
    $stmt = $db->query("SELECT * FROM contact_submissions 
                       WHERE status = 'pending' 
                       ORDER BY submitted_at DESC LIMIT 5");
    $recent_contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    $stats = [];
    $recent_applications = [];
    $recent_contacts = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | KGN ENTERPRISES Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
        }
        .nav-link {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #f97316;
        }
        .stat-card {
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .table-row {
            transition: background-color 0.2s ease;
        }
        .table-row:hover {
            background-color: #f9fafb;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="sidebar fixed top-0 left-0 w-64 text-white shadow-lg">
        <!-- Logo -->
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                    <img src="../uploads/settings/logo.png" alt="Logo" class="w-8 h-8">
                </div>
                <div>
                    <h2 class="font-bold text-lg">KGN ENTERPRISES</h2>
                    <p class="text-xs text-gray-400">Admin Panel</p>
                </div>
            </div>
        </div>
        
        <!-- Admin Info -->
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white text-xl"></i>
                </div>
                <div>
                    <p class="font-bold"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
                    <p class="text-sm text-gray-400"><?php echo htmlspecialchars($_SESSION['admin_role']); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="p-4 space-y-2">
            <a href="dashboard.php" class="nav-link flex items-center space-x-3 p-3 rounded-lg active">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="modules/services.php" class="nav-link flex items-center space-x-3 p-3 rounded-lg">
                <i class="fas fa-briefcase w-5"></i>
                <span>Services</span>
            </a>
            
            <a href="modules/jobs.php" class="nav-link flex items-center space-x-3 p-3 rounded-lg">
                <i class="fas fa-user-tie w-5"></i>
                <span>Jobs & Applications</span>
                <?php if (($stats['pending_applications'] ?? 0) > 0): ?>

                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                    <?php echo $stats['pending_applications']; ?>
                </span>
                <?php endif; ?>
            </a>
            
            <a href="modules/testimonials.php" class="nav-link flex items-center space-x-3 p-3 rounded-lg">
                <i class="fas fa-quote-left w-5"></i>
                <span>Testimonials</span>
                <?php if (($stats['pending_testimonials'] ?? 0) > 0): ?>

                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                    <?php echo $stats['pending_testimonials']; ?>
                </span>
                <?php endif; ?>
            </a>
            
            <a href="modules/clients.php" class="nav-link flex items-center space-x-3 p-3 rounded-lg">
                <i class="fas fa-handshake w-5"></i>
                <span>Clients</span>
            </a>
            
            <a href="modules/gallery.php" class="nav-link flex items-center space-x-3 p-3 rounded-lg">
                <i class="fas fa-images w-5"></i>
                <span>Gallery</span>
            </a>
            
            <a href="modules/contacts.php" class="nav-link flex items-center space-x-3 p-3 rounded-lg">
                <i class="fas fa-envelope w-5"></i>
                <span>Contact Messages</span>
                <?php if (($stats['pending_contacts'] ?? 0) > 0): ?>
                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                    <?php echo $stats['pending_contacts']; ?>
                </span>
                <?php endif; ?>
            </a>
            
            <a href="modules/settings.php" class="nav-link flex items-center space-x-3 p-3 rounded-lg">
                <i class="fas fa-cog w-5"></i>
                <span>Settings</span>
            </a>
            
            <div class="pt-6 mt-6 border-t border-gray-700">
                <a href="logout.php" class="nav-link flex items-center space-x-3 p-3 rounded-lg text-red-400 hover:text-red-300">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">
                        <i class="far fa-clock mr-2"></i>
                        <?php echo date('l, F j, Y'); ?>
                    </span>
                    <a href="../" target="_blank" class="text-orange-600 hover:text-orange-700">
                        <i class="fas fa-external-link-alt"></i> View Site
                    </a>
                </div>
            </div>
        </header>
        
        <!-- Content -->
        <main class="p-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Active Services</p>
                            <p class="text-3xl font-bold text-gray-800"><?php echo $stats['services'] ?? 0; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-briefcase text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <a href="modules/services.php" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 mt-4">
                        Manage <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                
                <div class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Active Jobs</p>
                            <p class="text-3xl font-bold text-gray-800"><?php echo $stats['jobs'] ?? 0; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <a href="modules/jobs.php" class="inline-flex items-center text-sm text-green-600 hover:text-green-700 mt-4">
                        Manage <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                
                <div class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Featured Clients</p>
                            <p class="text-3xl font-bold text-gray-800"><?php echo $stats['clients'] ?? 0; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-handshake text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <a href="modules/clients.php" class="inline-flex items-center text-sm text-purple-600 hover:text-purple-700 mt-4">
                        Manage <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                
                <div class="stat-card bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Approved Testimonials</p>
                            <p class="text-3xl font-bold text-gray-800"><?php echo $stats['testimonials'] ?? 0; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-quote-left text-orange-600 text-xl"></i>
                        </div>
                    </div>
                    <a href="modules/testimonials.php" class="inline-flex items-center text-sm text-orange-600 hover:text-orange-700 mt-4">
                        Manage <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- Pending Items Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Job Applications -->
                <div class="bg-white rounded-xl shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800">Recent Job Applications</h2>
                        <p class="text-sm text-gray-600">Pending review: <?php echo $stats['pending_applications'] ?? 0; ?></p>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($recent_applications)): ?>
                            <div class="space-y-4">
                                <?php foreach ($recent_applications as $application): ?>
                                <div class="table-row p-4 rounded-lg border border-gray-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-bold text-gray-800"><?php echo htmlspecialchars($application['full_name']); ?></p>
                                            <p class="text-sm text-gray-600">
                                                For: <?php echo htmlspecialchars($application['position']); ?>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="far fa-clock mr-1"></i>
                                                <?php echo date('M d, Y', strtotime($application['applied_at'])); ?>
                                            </p>
                                        </div>
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">
                                            Pending
                                        </span>
                                    </div>
                                    <div class="mt-3 flex space-x-2">
                                        <a href="modules/jobs.php?view=applications&id=<?php echo $application['id']; ?>" 
                                           class="text-sm text-blue-600 hover:text-blue-700">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                        <a href="../uploads/resumes/<?php echo basename($application['resume_path']); ?>" 
                                           target="_blank"
                                           class="text-sm text-green-600 hover:text-green-700">
                                            <i class="fas fa-file-download mr-1"></i> Resume
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-4 text-center">
                                <a href="modules/jobs.php?view=applications" 
                                   class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium">
                                    View All Applications <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-check-circle text-3xl text-green-500 mb-3"></i>
                                <p class="text-gray-600 font-medium">No pending job applications</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Recent Contact Messages -->
                <div class="bg-white rounded-xl shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800">Recent Contact Messages</h2>
                        <p class="text-sm text-gray-600">Pending response: <?php echo $stats['pending_contacts'] ?? 0; ?></p>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($recent_contacts)): ?>
                            <div class="space-y-4">
                                <?php foreach ($recent_contacts as $contact): ?>
                                <div class="table-row p-4 rounded-lg border border-gray-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-bold text-gray-800"><?php echo htmlspecialchars($contact['name']); ?></p>
                                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($contact['email']); ?></p>
                                            <p class="text-sm text-gray-700 mt-2 truncate"><?php echo htmlspecialchars(substr($contact['message'], 0, 100)); ?>...</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="far fa-clock mr-1"></i>
                                                <?php echo date('M d, Y', strtotime($contact['submitted_at'])); ?>
                                            </p>
                                        </div>
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">
                                            Pending
                                        </span>
                                    </div>
                                    <div class="mt-3">
                                        <a href="modules/contacts.php?view=<?php echo $contact['id']; ?>" 
                                           class="text-sm text-blue-600 hover:text-blue-700">
                                            <i class="fas fa-eye mr-1"></i> View Details
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-4 text-center">
                                <a href="modules/contacts.php" 
                                   class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium">
                                    View All Messages <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-check-circle text-3xl text-green-500 mb-3"></i>
                                <p class="text-gray-600 font-medium">No pending contact messages</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="mt-8 bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="modules/services.php?action=add" 
                       class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition">
                        <i class="fas fa-plus-circle text-2xl text-green-600 mb-2"></i>
                        <p class="font-medium text-gray-800">Add Service</p>
                    </a>
                    <a href="modules/jobs.php?action=add" 
                       class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition">
                        <i class="fas fa-briefcase text-2xl text-blue-600 mb-2"></i>
                        <p class="font-medium text-gray-800">Post Job</p>
                    </a>
                    <a href="modules/clients.php?action=add" 
                       class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition">
                        <i class="fas fa-handshake text-2xl text-purple-600 mb-2"></i>
                        <p class="font-medium text-gray-800">Add Client</p>
                    </a>
                    <a href="modules/gallery.php?action=add" 
                       class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg text-center transition">
                        <i class="fas fa-image text-2xl text-orange-600 mb-2"></i>
                        <p class="font-medium text-gray-800">Add to Gallery</p>
                    </a>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 px-8 py-6">
            <div class="flex justify-between items-center text-sm text-gray-600">
                <p>&copy; <?php echo date('Y'); ?> KGN ENTERPRISES Admin Panel</p>
                <p>Logged in as: <?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
            </div>
        </footer>
    </div>
    
    <script>
        // Auto refresh dashboard every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000);
        
        // Confirm before logout
        document.querySelector('a[href="logout.php"]').addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to logout?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
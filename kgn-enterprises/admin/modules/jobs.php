<?php
require_once '../includes/auth.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';
$view = $_GET['view'] ?? 'jobs';
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle form submission for jobs
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid CSRF token";
    } else {
        $action = $_POST['action'];
        
        if ($action == 'add_job' || $action == 'edit_job') {
            $title = sanitize_input($_POST['title']);
            $description = sanitize_input($_POST['description']);
            $job_type = sanitize_input($_POST['job_type']);
            $location = sanitize_input($_POST['location']);
            $experience = sanitize_input($_POST['experience']);
            $qualification = sanitize_input($_POST['qualification']);
            $salary_range = sanitize_input($_POST['salary_range']);
            $deadline = $_POST['deadline'] ?: null;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            
            if ($action == 'add_job') {
                try {
                    $stmt = $db->prepare("INSERT INTO jobs (title, slug, description, job_type, location, experience, qualification, salary_range, deadline, is_active) 
                                         VALUES (:title, :slug, :description, :job_type, :location, :experience, :qualification, :salary_range, :deadline, :is_active)");
                    
                    $stmt->execute([
                        ':title' => $title,
                        ':slug' => $slug,
                        ':description' => $description,
                        ':job_type' => $job_type,
                        ':location' => $location,
                        ':experience' => $experience,
                        ':qualification' => $qualification,
                        ':salary_range' => $salary_range,
                        ':deadline' => $deadline,
                        ':is_active' => $is_active
                    ]);
                    
                    $message = "Job posted successfully!";
                    
                } catch(PDOException $e) {
                    $error = "Error adding job: " . $e->getMessage();
                }
            } else if ($action == 'edit_job') {
                $id = intval($_POST['id']);
                
                try {
                    $stmt = $db->prepare("UPDATE jobs SET 
                                         title = :title,
                                         slug = :slug,
                                         description = :description,
                                         job_type = :job_type,
                                         location = :location,
                                         experience = :experience,
                                         qualification = :qualification,
                                         salary_range = :salary_range,
                                         deadline = :deadline,
                                         is_active = :is_active,
                                         updated_at = NOW()
                                         WHERE id = :id");
                    
                    $stmt->execute([
                        ':id' => $id,
                        ':title' => $title,
                        ':slug' => $slug,
                        ':description' => $description,
                        ':job_type' => $job_type,
                        ':location' => $location,
                        ':experience' => $experience,
                        ':qualification' => $qualification,
                        ':salary_range' => $salary_range,
                        ':deadline' => $deadline,
                        ':is_active' => $is_active
                    ]);
                    
                    $message = "Job updated successfully!";
                    
                } catch(PDOException $e) {
                    $error = "Error updating job: " . $e->getMessage();
                }
            }
        } else if ($action == 'delete_job') {
            $id = intval($_POST['id']);
            
            try {
                $stmt = $db->prepare("DELETE FROM jobs WHERE id = :id");
                $stmt->execute([':id' => $id]);
                
                $message = "Job deleted successfully!";
                
            } catch(PDOException $e) {
                $error = "Error deleting job: " . $e->getMessage();
            }
        } else if ($action == 'update_application_status') {
            $id = intval($_POST['application_id']);
            $status = sanitize_input($_POST['status']);
            $notes = sanitize_input($_POST['notes'] ?? '');
            
            try {
                $stmt = $db->prepare("UPDATE job_applications SET status = :status, notes = :notes WHERE id = :id");
                $stmt->execute([
                    ':id' => $id,
                    ':status' => $status,
                    ':notes' => $notes
                ]);
                
                $message = "Application status updated!";
                
            } catch(PDOException $e) {
                $error = "Error updating application: " . $e->getMessage();
            }
        }
    }
}

// Get data based on view
$jobs = [];
$applications = [];
$current_job = null;
$current_application = null;

try {
    if ($view == 'jobs') {
        $stmt = $db->query("SELECT * FROM jobs ORDER BY posted_at DESC");
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else if ($view == 'applications') {
        $query = "SELECT ja.*, j.title as job_title FROM job_applications ja 
                  LEFT JOIN jobs j ON ja.job_id = j.id 
                  ORDER BY ja.applied_at DESC";
        
        if ($job_id > 0) {
            $query = "SELECT ja.*, j.title as job_title FROM job_applications ja 
                      LEFT JOIN jobs j ON ja.job_id = j.id 
                      WHERE ja.id = :id";
            $stmt = $db->prepare($query);
            $stmt->execute([':id' => $job_id]);
            $current_application = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $db->query($query);
            $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    // Get job for editing
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $id = intval($_GET['edit']);
        $stmt = $db->prepare("SELECT * FROM jobs WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $current_job = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
} catch(PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}

// Get statistics
$stats = [];
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM jobs WHERE is_active = 1");
    $stats['active_jobs'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM job_applications WHERE status = 'pending'");
    $stats['pending_applications'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM job_applications");
    $stats['total_applications'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
} catch(PDOException $e) {
    error_log("Stats error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs | KGN Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        <?php echo $view == 'applications' ? 'Job Applications' : 'Manage Jobs'; ?>
                    </h1>
                    <p class="text-gray-600">
                        <?php if ($view == 'applications'): ?>
                            <?php echo $stats['total_applications'] ?? 0; ?>


                        View and manage job applications (<?php echo $stats['pending_applications'] ?? 0; ?> pending)
                        <?php else: ?>
                        Post and manage job openings (<?php echo $stats['active_jobs']; ?> active)
                        <?php endif; ?>
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="../dashboard.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i> Dashboard
                    </a>
                    <?php if ($view == 'applications'): ?>
                    <a href="jobs.php?view=jobs" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-briefcase mr-2"></i> Manage Jobs
                    </a>
                    <?php else: ?>
                    <a href="jobs.php?view=applications" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-users mr-2"></i> View Applications
                        <?php if (($stats['pending_applications'] ?? 0) > 0): ?>

                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                            <?php echo $stats['pending_applications']; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Messages -->
            <?php if ($message): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <span class="font-medium text-green-800"><?php echo $message; ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                    <span class="font-medium text-red-800"><?php echo $error; ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($view == 'applications' && $current_application): ?>
            <!-- View Single Application -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Application Details</h2>
                        <p class="text-gray-600">Submitted on <?php echo date('F j, Y', strtotime($current_application['applied_at'])); ?></p>
                    </div>
                    <a href="jobs.php?view=applications" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-bold text-gray-700 mb-4">Applicant Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-500">Full Name</label>
                                <p class="font-medium"><?php echo htmlspecialchars($current_application['full_name']); ?></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Email</label>
                                    <p class="font-medium"><?php echo htmlspecialchars($current_application['email']); ?></p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Phone</label>
                                    <p class="font-medium"><?php echo htmlspecialchars($current_application['phone']); ?></p>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Position Applied</label>
                                <p class="font-medium"><?php echo htmlspecialchars($current_application['position']); ?></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Experience</label>
                                    <p class="font-medium"><?php echo htmlspecialchars($current_application['experience']); ?></p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Qualification</label>
                                    <p class="font-medium"><?php echo htmlspecialchars($current_application['qualification']); ?></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Current CTC</label>
                                    <p class="font-medium"><?php echo htmlspecialchars($current_application['current_ctc'] ?: 'Not specified'); ?></p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Expected CTC</label>
                                    <p class="font-medium"><?php echo htmlspecialchars($current_application['expected_ctc']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-bold text-gray-700 mb-4">Application Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-500">Status</label>
                                <div class="mt-1">
                                    <?php
                                    $status = $current_application['status'] ?? 'pending'; // agar undefined ho to default 'pending'
$status_colors = [
    'pending' => 'bg-yellow-100 text-yellow-800',
    'reviewed' => 'bg-blue-100 text-blue-800',
    'shortlisted' => 'bg-green-100 text-green-800',
    'rejected' => 'bg-red-100 text-red-800'
];
$status_class = isset($status_colors[$status]) ? $status_colors[$status] : 'bg-gray-100 text-gray-800';?>
                                    <span class="px-3 py-1 text-sm font-medium rounded-full">
                                    <?php echo $status_class;  
                                        $status = $current_application['status'] ?? 'pending';?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if ($current_application['cover_letter']): ?>
                            <div>
                                <label class="text-sm text-gray-500">Cover Letter</label>
                                <div class="mt-1 p-4 bg-gray-50 rounded-lg">
                                    <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($current_application['cover_letter'])); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div>
                                <label class="text-sm text-gray-500">Resume</label>
                                <div class="mt-1">
                                    <a href="../../uploads/resumes/<?php echo basename($current_application['resume_path']); ?>" 
                                       target="_blank"
                                       class="inline-flex items-center text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-file-pdf mr-2"></i> Download Resume
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Update Status Form -->
                        <form method="POST" action="" class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="action" value="update_application_status">
                            <input type="hidden" name="application_id" value="<?php echo $current_application['id']; ?>">
                            
                            <h4 class="font-bold text-gray-700 mb-3">Update Status</h4>
                            <div class="space-y-3">
                                <div>
                                    <select name="status" required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
    <?php $current_status = $current_application['status'] ?? 'pending'; ?>
    <option value="pending" <?php echo $current_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
    <option value="reviewed" <?php echo $current_status == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
    <option value="shortlisted" <?php echo $current_status == 'shortlisted' ? 'selected' : ''; ?>>Shortlisted</option>
    <option value="rejected" <?php echo $current_status == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
</select>

                                </div>
                                
                                <div>
                                    <textarea name="notes" 
                                              rows="3"
                                              placeholder="Add notes (optional)"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"><?php echo htmlspecialchars($current_application['notes'] ?? ''); ?></textarea>
                                </div>
                                
                                <button type="submit" 
                                        class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded-lg font-medium">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php elseif ($view == 'applications'): ?>
            <!-- Applications List -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">All Applications (<?php echo count($applications); ?>)</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Experience</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($applications)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-3 text-gray-300"></i>
                                    <p class="font-medium">No applications found</p>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($applications as $app): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($app['full_name']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($app['email']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($app['position']); ?></div>
                                        <?php if ($app['job_title']): ?>
                                        <div class="text-xs text-gray-500">Job: <?php echo htmlspecialchars($app['job_title']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                     <td class="px-6 py-4">
        <?php
        $status_colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'reviewed' => 'bg-blue-100 text-blue-800',
            'shortlisted' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800'
        ];
        $status = $app['status'] ?? 'pending';
        $status_class = $status_colors[$status] ?? 'bg-gray-100 text-gray-800';
        ?>
        <span class="px-3 py-1 text-xs font-medium rounded-full <?php echo $status_class; ?>">
            <?php echo ucfirst($status); ?>
        </span>
    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?php echo date('M d, Y', strtotime($app['applied_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="jobs.php?view=applications&id=<?php echo $app['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-900 mr-4">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="../../uploads/resumes/<?php echo basename($app['resume_path']); ?>" 
                                           target="_blank"
                                           class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Application Stats -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="grid grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-blue-600"><?php echo $stats['total_applications'] ?? 0; ?></div>
                            <div class="text-sm text-gray-600">Total</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-yellow-600"><?php echo $stats['pending_applications'] ?? 0; ?></div>
                            <div class="text-sm text-gray-600">Pending</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">
                                <?php 
                                $shortlisted = array_filter($applications, function($app) {
    return ($app['status'] ?? '') == 'shortlisted';
});
                                ?>
                            </div>
                            <div class="text-sm text-gray-600">Shortlisted</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600">
                                <?php 
                               $rejected = array_filter($applications, function($app) {
    return ($app['status'] ?? '') == 'rejected';
});
                                ?>
                            </div>
                            <div class="text-sm text-gray-600">Rejected</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php else: ?>
            <!-- Jobs Management -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Column -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">
                            <?php echo $current_job ? 'Edit Job' : 'Post New Job'; ?>
                        </h2>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <?php if ($current_job): ?>
                            <input type="hidden" name="action" value="edit_job">
                            <input type="hidden" name="id" value="<?php echo $current_job['id']; ?>">
                            <?php else: ?>
                            <input type="hidden" name="action" value="add_job">
                            <?php endif; ?>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                                    <input type="text" 
                                           name="title" 
                                           required
                                           value="<?php echo $current_job['title'] ?? ''; ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                           placeholder="e.g., Data Entry Operator">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Type</label>
                                    <select name="job_type" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                        <option value="full_time" <?php echo ($current_job['job_type'] ?? 'full_time') == 'full_time' ? 'selected' : ''; ?>>Full Time</option>
                                        <option value="part_time" <?php echo ($current_job['job_type'] ?? '') == 'part_time' ? 'selected' : ''; ?>>Part Time</option>
                                        <option value="contract" <?php echo ($current_job['job_type'] ?? '') == 'contract' ? 'selected' : ''; ?>>Contract</option>
                                        <option value="internship" <?php echo ($current_job['job_type'] ?? '') == 'internship' ? 'selected' : ''; ?>>Internship</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                                    <input type="text" 
                                           name="location" 
                                           required
                                           value="<?php echo $current_job['location'] ?? 'Pune'; ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                           placeholder="e.g., Pune">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Experience Required</label>
                                    <input type="text" 
                                           name="experience" 
                                           required
                                           value="<?php echo $current_job['experience'] ?? '0-1 years'; ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                           placeholder="e.g., 0-1 years">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Qualification *</label>
                                    <input type="text" 
                                           name="qualification" 
                                           required
                                           value="<?php echo $current_job['qualification'] ?? 'Any Graduate'; ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                           placeholder="e.g., Any Graduate">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Salary Range</label>
                                    <input type="text" 
                                           name="salary_range" 
                                           required
                                           value="<?php echo $current_job['salary_range'] ?? '12000-18000'; ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                           placeholder="e.g., 12000-18000">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Description *</label>
                                    <textarea name="description" 
                                              rows="6"
                                              required
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                              placeholder="Detailed job description..."><?php echo $current_job['description'] ?? ''; ?></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Application Deadline</label>
                                    <input type="date" 
                                           name="deadline" 
                                           value="<?php echo $current_job['deadline'] ?? ''; ?>"
                                           min="<?php echo date('Y-m-d'); ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           id="is_active"
                                           <?php echo ($current_job['is_active'] ?? 1) ? 'checked' : ''; ?>
                                           class="h-5 w-5 text-orange-600 rounded">
                                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active (visible on website)</label>
                                </div>
                                
                                <div class="pt-4">
                                    <button type="submit" 
                                            class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 px-4 rounded-lg font-medium transition">
                                        <i class="fas fa-save mr-2"></i>
                                        <?php echo $current_job ? 'Update Job' : 'Post Job'; ?>
                                    </button>
                                    
                                    <?php if ($current_job): ?>
                                    <a href="jobs.php" 
                                       class="block w-full text-center mt-3 text-gray-600 hover:text-gray-800 py-2">
                                        Cancel Edit
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- List Column -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-800">All Jobs (<?php echo count($jobs); ?>)</h2>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($jobs)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-briefcase text-3xl mb-3 text-gray-300"></i>
                                            <p class="font-medium">No jobs posted yet</p>
                                            <p class="text-sm mt-1">Post your first job using the form</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($jobs as $job): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($job['title']); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($job['salary_range']); ?></div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                    <?php echo ucfirst(str_replace('_', ' ', $job['job_type'])); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($job['location']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo $job['deadline'] ? date('M d, Y', strtotime($job['deadline'])) : 'Open'; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if ($job['is_active']): ?>
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i> Active
                                                </span>
                                                <?php else: ?>
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                    <i class="fas fa-times-circle mr-1"></i> Inactive
                                                </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="jobs.php?edit=<?php echo $job['id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-900 mr-4">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="jobs.php?view=applications&job_id=<?php echo $job['id']; ?>" 
                                                   class="text-purple-600 hover:text-purple-900 mr-4">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                                <form method="POST" action="" class="inline" onsubmit="return confirm('Delete this job?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="action" value="delete_job">
                                                    <input type="hidden" name="id" value="<?php echo $job['id']; ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto-set minimum date for deadline
        document.querySelector('input[name="deadline"]').min = new Date().toISOString().split('T')[0];
        
        // Auto-resize textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            if (textarea.value) textarea.dispatchEvent(new Event('input'));
        });
    </script>
</body>
</html>
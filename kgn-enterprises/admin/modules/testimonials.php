<?php
require_once '../includes/auth.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid CSRF token";
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action == 'add' || $action == 'edit') {
            $client_name = sanitize_input($_POST['client_name']);
            $client_designation = sanitize_input($_POST['client_designation']);
            $company = sanitize_input($_POST['company']);
            $testimonial = sanitize_input($_POST['testimonial']);
            $rating = intval($_POST['rating']);
            $service_type = sanitize_input($_POST['service_type']);
            $email = sanitize_input($_POST['email']);
            $phone = sanitize_input($_POST['phone']);
            $is_approved = isset($_POST['is_approved']) ? 1 : 0;
            $featured = isset($_POST['featured']) ? 1 : 0;
            $display_order = intval($_POST['display_order']);
            
            if ($action == 'add') {
                try {
                    $stmt = $db->prepare("INSERT INTO testimonials (client_name, client_designation, company, testimonial, rating, service_type, email, phone, is_approved, featured, display_order) 
                                         VALUES (:client_name, :client_designation, :company, :testimonial, :rating, :service_type, :email, :phone, :is_approved, :featured, :display_order)");
                    
                    $stmt->execute([
                        ':client_name' => $client_name,
                        ':client_designation' => $client_designation,
                        ':company' => $company,
                        ':testimonial' => $testimonial,
                        ':rating' => $rating,
                        ':service_type' => $service_type,
                        ':email' => $email,
                        ':phone' => $phone,
                        ':is_approved' => $is_approved,
                        ':featured' => $featured,
                        ':display_order' => $display_order
                    ]);
                    
                    $message = "Testimonial added successfully!";
                    
                } catch(PDOException $e) {
                    $error = "Error adding testimonial: " . $e->getMessage();
                }
            } else if ($action == 'edit') {
                $id = intval($_POST['id']);
                
                try {
                    $stmt = $db->prepare("UPDATE testimonials SET 
                                         client_name = :client_name,
                                         client_designation = :client_designation,
                                         company = :company,
                                         testimonial = :testimonial,
                                         rating = :rating,
                                         service_type = :service_type,
                                         email = :email,
                                         phone = :phone,
                                         is_approved = :is_approved,
                                         featured = :featured,
                                         display_order = :display_order,
                                         updated_at = NOW()
                                         WHERE id = :id");
                    
                    $stmt->execute([
                        ':id' => $id,
                        ':client_name' => $client_name,
                        ':client_designation' => $client_designation,
                        ':company' => $company,
                        ':testimonial' => $testimonial,
                        ':rating' => $rating,
                        ':service_type' => $service_type,
                        ':email' => $email,
                        ':phone' => $phone,
                        ':is_approved' => $is_approved,
                        ':featured' => $featured,
                        ':display_order' => $display_order
                    ]);
                    
                    $message = "Testimonial updated successfully!";
                    
                } catch(PDOException $e) {
                    $error = "Error updating testimonial: " . $e->getMessage();
                }
            }
        } else if ($action == 'delete') {
            $id = intval($_POST['id']);
            
            try {
                $stmt = $db->prepare("DELETE FROM testimonials WHERE id = :id");
                $stmt->execute([':id' => $id]);
                
                $message = "Testimonial deleted successfully!";
                
            } catch(PDOException $e) {
                $error = "Error deleting testimonial: " . $e->getMessage();
            }
        } else if ($action == 'approve') {
            $id = intval($_POST['id']);
            
            try {
                $stmt = $db->prepare("UPDATE testimonials SET is_approved = 1 WHERE id = :id");
                $stmt->execute([':id' => $id]);
                
                $message = "Testimonial approved!";
                
            } catch(PDOException $e) {
                $error = "Error approving testimonial: " . $e->getMessage();
            }
        } else if ($action == 'feature') {
            $id = intval($_POST['id']);
            $featured = intval($_POST['featured']);
            
            try {
                $stmt = $db->prepare("UPDATE testimonials SET featured = :featured WHERE id = :id");
                $stmt->execute([':id' => $id, ':featured' => $featured]);
                
                $message = $featured ? "Testimonial featured!" : "Testimonial unfeatured!";
                
            } catch(PDOException $e) {
                $error = "Error updating testimonial: " . $e->getMessage();
            }
        }
    }
}

// Get testimonials list
$testimonials = [];
try {
    $stmt = $db->query("SELECT * FROM testimonials ORDER BY 
                        CASE WHEN is_approved = 0 THEN 0 ELSE 1 END,
                        CASE WHEN featured = 1 THEN 0 ELSE 1 END,
                        display_order, created_at DESC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching testimonials: " . $e->getMessage();
}

// Get stats
$stats = [];
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM testimonials");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as pending FROM testimonials WHERE is_approved = 0");
    $stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];
    
    $stmt = $db->query("SELECT COUNT(*) as featured FROM testimonials WHERE featured = 1");
    $stats['featured'] = $stmt->fetch(PDO::FETCH_ASSOC)['featured'];
    
} catch(PDOException $e) {
    error_log("Stats error: " . $e->getMessage());
}

// Check if editing
$edit_testimonial = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    try {
        $stmt = $db->prepare("SELECT * FROM testimonials WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $edit_testimonial = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = "Error fetching testimonial: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials | KGN Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Manage Testimonials</h1>
                    <p class="text-gray-600">Manage client testimonials and reviews</p>
                </div>
                <a href="../dashboard.php" class="text-orange-600 hover:text-orange-700">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-comment text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Testimonials</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Pending Approval</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['pending']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-star text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Featured</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['featured']; ?></p>
                        </div>
                    </div>
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
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Column -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">
                            <?php echo $edit_testimonial ? 'Edit Testimonial' : 'Add New Testimonial'; ?>
                        </h2>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <?php if ($edit_testimonial): ?>
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $edit_testimonial['id']; ?>">
                            <?php else: ?>
                            <input type="hidden" name="action" value="add">
                            <?php endif; ?>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Client Name *</label>
                                    <input type="text" 
                                           name="client_name" 
                                           required
                                           value="<?php echo $edit_testimonial['client_name'] ?? ''; ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                           placeholder="e.g., John Smith">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                                        <input type="text" 
                                               name="client_designation" 
                                               value="<?php echo $edit_testimonial['client_designation'] ?? ''; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                               placeholder="e.g., HR Manager">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Company *</label>
                                        <input type="text" 
                                               name="company" 
                                               required
                                               value="<?php echo $edit_testimonial['company'] ?? ''; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                               placeholder="e.g., ABC Corporation">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Testimonial *</label>
                                    <textarea name="testimonial" 
                                              rows="5"
                                              required
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                              placeholder="Client's testimonial..."><?php echo $edit_testimonial['testimonial'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                        <select name="rating" 
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <option value="<?php echo $i; ?>" <?php echo ($edit_testimonial['rating'] ?? 5) == $i ? 'selected' : ''; ?>>
                                                <?php echo str_repeat('★', $i) . str_repeat('☆', 5 - $i) . " ($i/5)"; ?>
                                            </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                                        <input type="text" 
                                               name="service_type" 
                                               value="<?php echo $edit_testimonial['service_type'] ?? ''; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                               placeholder="e.g., Skilled Manpower">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input type="email" 
                                               name="email" 
                                               value="<?php echo $edit_testimonial['email'] ?? ''; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                               placeholder="client@example.com">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                        <input type="text" 
                                               name="phone" 
                                               value="<?php echo $edit_testimonial['phone'] ?? ''; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                               placeholder="+91 1234567890">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               name="is_approved" 
                                               id="is_approved"
                                               <?php echo ($edit_testimonial['is_approved'] ?? 1) ? 'checked' : ''; ?>
                                               class="h-5 w-5 text-green-600 rounded">
                                        <label for="is_approved" class="ml-2 text-sm text-gray-700">Approved</label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               name="featured" 
                                               id="featured"
                                               <?php echo ($edit_testimonial['featured'] ?? 0) ? 'checked' : ''; ?>
                                               class="h-5 w-5 text-yellow-600 rounded">
                                        <label for="featured" class="ml-2 text-sm text-gray-700">Featured</label>
                                    </div>
                                    
                                    <div>
                                        <input type="number" 
                                               name="display_order" 
                                               value="<?php echo $edit_testimonial['display_order'] ?? '0'; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                               placeholder="Order" min="0">
                                    </div>
                                </div>
                                
                                <div class="pt-4">
                                    <button type="submit" 
                                            class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 px-4 rounded-lg font-medium transition">
                                        <i class="fas fa-save mr-2"></i>
                                        <?php echo $edit_testimonial ? 'Update Testimonial' : 'Add Testimonial'; ?>
                                    </button>
                                    
                                    <?php if ($edit_testimonial): ?>
                                    <a href="testimonials.php" 
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
                            <h2 class="text-xl font-bold text-gray-800">All Testimonials (<?php echo count($testimonials); ?>)</h2>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Testimonial</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($testimonials)): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-comment text-3xl mb-3 text-gray-300"></i>
                                            <p class="font-medium">No testimonials found</p>
                                            <p class="text-sm mt-1">Add your first testimonial using the form</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($testimonials as $testimonial): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($testimonial['client_name']); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($testimonial['company']); ?></div>
                                                    <div class="text-xs text-yellow-500 mt-1">
                                                        <?php echo str_repeat('★', $testimonial['rating']) . str_repeat('☆', 5 - $testimonial['rating']); ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-800 line-clamp-3"><?php echo htmlspecialchars($testimonial['testimonial']); ?></div>
                                                <div class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($testimonial['service_type']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="space-y-1">
                                                    <?php if ($testimonial['is_approved']): ?>
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                    <?php else: ?>
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($testimonial['featured']): ?>
                                                    <span class="block px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                                        Featured
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="testimonials.php?edit=<?php echo $testimonial['id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <?php if (!$testimonial['is_approved']): ?>
                                                <form method="POST" action="" class="inline mr-3">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                                                    <button type="submit" class="text-green-600 hover:text-green-900" title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                
                                                <form method="POST" action="" class="inline mr-3">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="action" value="feature">
                                                    <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                                                    <input type="hidden" name="featured" value="<?php echo $testimonial['featured'] ? '0' : '1'; ?>">
                                                    <button type="submit" class="<?php echo $testimonial['featured'] ? 'text-yellow-600 hover:text-yellow-900' : 'text-gray-600 hover:text-gray-900'; ?>" 
                                                            title="<?php echo $testimonial['featured'] ? 'Remove from featured' : 'Mark as featured'; ?>">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="" class="inline" onsubmit="return confirm('Delete this testimonial?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
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
        </div>
    </div>
    
    <script>
        // Auto-resize textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            textarea.dispatchEvent(new Event('input'));
        });
    </script>
</body>
</html>
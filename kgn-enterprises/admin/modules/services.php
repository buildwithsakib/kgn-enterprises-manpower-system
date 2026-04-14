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
            $title = sanitize_input($_POST['title']);
            $description = sanitize_input($_POST['description']);
            $features = sanitize_input($_POST['features']);
            $icon = sanitize_input($_POST['icon']);
            $category = sanitize_input($_POST['category']);
            $display_order = intval($_POST['display_order']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            // Generate slug
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            
            if ($action == 'add') {
                try {
                    $stmt = $db->prepare("INSERT INTO services (title, slug, description, features, icon, category, display_order, is_active) 
                                         VALUES (:title, :slug, :description, :features, :icon, :category, :display_order, :is_active)");
                    
                    $stmt->execute([
                        ':title' => $title,
                        ':slug' => $slug,
                        ':description' => $description,
                        ':features' => $features,
                        ':icon' => $icon,
                        ':category' => $category,
                        ':display_order' => $display_order,
                        ':is_active' => $is_active
                    ]);
                    
                    $message = "Service added successfully!";
                    
                } catch(PDOException $e) {
                    $error = "Error adding service: " . $e->getMessage();
                }
            } else if ($action == 'edit') {
                $id = intval($_POST['id']);
                
                try {
                    $stmt = $db->prepare("UPDATE services SET 
                                         title = :title,
                                         slug = :slug,
                                         description = :description,
                                         features = :features,
                                         icon = :icon,
                                         category = :category,
                                         display_order = :display_order,
                                         is_active = :is_active,
                                         updated_at = NOW()
                                         WHERE id = :id");
                    
                    $stmt->execute([
                        ':id' => $id,
                        ':title' => $title,
                        ':slug' => $slug,
                        ':description' => $description,
                        ':features' => $features,
                        ':icon' => $icon,
                        ':category' => $category,
                        ':display_order' => $display_order,
                        ':is_active' => $is_active
                    ]);
                    
                    $message = "Service updated successfully!";
                    
                } catch(PDOException $e) {
                    $error = "Error updating service: " . $e->getMessage();
                }
            }
        } else if ($action == 'delete') {
            $id = intval($_POST['id']);
            
            try {
                $stmt = $db->prepare("DELETE FROM services WHERE id = :id");
                $stmt->execute([':id' => $id]);
                
                $message = "Service deleted successfully!";
                
            } catch(PDOException $e) {
                $error = "Error deleting service: " . $e->getMessage();
            }
        }
    }
}

// Get services list
$services = [];
try {
    $stmt = $db->query("SELECT * FROM services ORDER BY display_order, title");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching services: " . $e->getMessage();
}

// Check if editing
$edit_service = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    try {
        $stmt = $db->prepare("SELECT * FROM services WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $edit_service = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = "Error fetching service: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services | KGN Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Manage Services</h1>
                    <p class="text-gray-600">Add, edit, or delete services offered by KGN ENTERPRISES</p>
                </div>
                <a href="../dashboard.php" class="text-orange-600 hover:text-orange-700">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
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
                            <?php echo $edit_service ? 'Edit Service' : 'Add New Service'; ?>
                        </h2>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <?php if ($edit_service): ?>
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $edit_service['id']; ?>">
                            <?php else: ?>
                            <input type="hidden" name="action" value="add">
                            <?php endif; ?>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Title *</label>
                                    <input type="text" 
                                           name="title" 
                                           required
                                           value="<?php echo $edit_service['title'] ?? ''; ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                           placeholder="e.g., Skilled Manpower">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                    <textarea name="description" 
                                              rows="4"
                                              required
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                              placeholder="Describe the service..."><?php echo $edit_service['description'] ?? ''; ?></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Features (comma separated)</label>
                                    <textarea name="features" 
                                              rows="3"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                              placeholder="Feature 1, Feature 2, Feature 3"><?php echo $edit_service['features'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
                                        <input type="text" 
                                               name="icon" 
                                               value="<?php echo $edit_service['icon'] ?? 'fas fa-briefcase'; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                               placeholder="fas fa-briefcase">
                                        <p class="text-xs text-gray-500 mt-1">Font Awesome class</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                        <input type="text" 
                                               name="category" 
                                               value="<?php echo $edit_service['category'] ?? 'General'; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                               placeholder="e.g., Skilled">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                                        <input type="number" 
                                               name="display_order" 
                                               value="<?php echo $edit_service['display_order'] ?? '0'; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                               min="0">
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               id="is_active"
                                               <?php echo ($edit_service['is_active'] ?? 1) ? 'checked' : ''; ?>
                                               class="h-5 w-5 text-orange-600 rounded">
                                        <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                                    </div>
                                </div>
                                
                                <div class="pt-4">
                                    <button type="submit" 
                                            class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 px-4 rounded-lg font-medium transition">
                                        <i class="fas fa-save mr-2"></i>
                                        <?php echo $edit_service ? 'Update Service' : 'Add Service'; ?>
                                    </button>
                                    
                                    <?php if ($edit_service): ?>
                                    <a href="services.php" 
                                       class="block w-full text-center mt-3 text-gray-600 hover:text-gray-800 py-2">
                                        Cancel Edit
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Icon Reference -->
                    <div class="mt-6 bg-white rounded-xl shadow-md p-6">
                        <h3 class="font-bold text-gray-800 mb-4">Common Icons</h3>
                        <div class="grid grid-cols-3 gap-3 text-sm">
                            <div class="text-center p-2 bg-gray-50 rounded">
                                <i class="fas fa-user-graduate mb-1 text-gray-600"></i>
                                <p class="text-xs">fa-user-graduate</p>
                            </div>
                            <div class="text-center p-2 bg-gray-50 rounded">
                                <i class="fas fa-users mb-1 text-gray-600"></i>
                                <p class="text-xs">fa-users</p>
                            </div>
                            <div class="text-center p-2 bg-gray-50 rounded">
                                <i class="fas fa-building mb-1 text-gray-600"></i>
                                <p class="text-xs">fa-building</p>
                            </div>
                            <div class="text-center p-2 bg-gray-50 rounded">
                                <i class="fas fa-laptop-code mb-1 text-gray-600"></i>
                                <p class="text-xs">fa-laptop-code</p>
                            </div>
                            <div class="text-center p-2 bg-gray-50 rounded">
                                <i class="fas fa-industry mb-1 text-gray-600"></i>
                                <p class="text-xs">fa-industry</p>
                            </div>
                            <div class="text-center p-2 bg-gray-50 rounded">
                                <i class="fas fa-broom mb-1 text-gray-600"></i>
                                <p class="text-xs">fa-broom</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- List Column -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-800">All Services (<?php echo count($services); ?>)</h2>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($services)): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-3xl mb-3 text-gray-300"></i>
                                            <p class="font-medium">No services found</p>
                                            <p class="text-sm mt-1">Add your first service using the form</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($services as $service): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-medium text-gray-900"><?php echo $service['display_order']; ?></span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 flex-shrink-0 mr-3">
                                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                            <i class="<?php echo htmlspecialchars($service['icon']); ?> text-gray-600"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($service['title']); ?></div>
                                                        <div class="text-sm text-gray-500 truncate max-w-xs"><?php echo substr(htmlspecialchars($service['description']), 0, 50); ?>...</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                    <?php echo htmlspecialchars($service['category']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if ($service['is_active']): ?>
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
                                                <a href="services.php?edit=<?php echo $service['id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-900 mr-4">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="" class="inline" onsubmit="return confirm('Delete this service?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
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
                    
                    <!-- Instructions -->
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <h3 class="font-bold text-blue-800 mb-2 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i> Instructions
                        </h3>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li><i class="fas fa-circle text-xs mr-2"></i> Services will appear on the Services page in the order specified</li>
                            <li><i class="fas fa-circle text-xs mr-2"></i> Use Font Awesome icons for visual representation</li>
                            <li><i class="fas fa-circle text-xs mr-2"></i> Inactive services won't be displayed on the website</li>
                            <li><i class="fas fa-circle text-xs mr-2"></i> Keep descriptions concise and feature-rich</li>
                        </ul>
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
        
        // Confirm before delete
        document.querySelectorAll('form[onsubmit]').forEach(form => {
            form.onsubmit = function() {
                return confirm('Are you sure you want to delete this item?');
            };
        });
    </script>
</body>
</html>
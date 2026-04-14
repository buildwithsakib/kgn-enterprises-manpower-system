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
            $name = sanitize_input($_POST['name']);
            $industry = sanitize_input($_POST['industry']);
            $testimonial = sanitize_input($_POST['testimonial']);
            $rating = floatval($_POST['rating']);
            $website = sanitize_input($_POST['website']);
            $since_date = $_POST['since_date'] ?: null;
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $display_order = intval($_POST['display_order']);
            
            // Handle file upload
            $logo = '';
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../uploads/clients/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array(strtolower($file_extension), $allowed_extensions)) {
                    $file_name = 'client_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $file_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $file_path)) {
                        $logo = 'uploads/clients/' . $file_name;
                    } else {
                        $error = "Error uploading logo file.";
                    }
                } else {
                    $error = "Invalid file type. Allowed: JPG, PNG, GIF, WebP";
                }
            } elseif ($action == 'edit' && isset($_POST['current_logo'])) {
                $logo = $_POST['current_logo'];
            }
            
            if (!$error) {
                if ($action == 'add') {
                    try {
                        $stmt = $db->prepare("INSERT INTO clients (name, logo, industry, testimonial, rating, website, since_date, is_featured, display_order) 
                                             VALUES (:name, :logo, :industry, :testimonial, :rating, :website, :since_date, :is_featured, :display_order)");
                        
                        $stmt->execute([
                            ':name' => $name,
                            ':logo' => $logo,
                            ':industry' => $industry,
                            ':testimonial' => $testimonial,
                            ':rating' => $rating,
                            ':website' => $website,
                            ':since_date' => $since_date,
                            ':is_featured' => $is_featured,
                            ':display_order' => $display_order
                        ]);
                        
                        $message = "Client added successfully!";
                        
                    } catch(PDOException $e) {
                        $error = "Error adding client: " . $e->getMessage();
                    }
                } else if ($action == 'edit') {
                    $id = intval($_POST['id']);
                    
                    try {
                        $stmt = $db->prepare("UPDATE clients SET 
                                             name = :name,
                                             logo = :logo,
                                             industry = :industry,
                                             testimonial = :testimonial,
                                             rating = :rating,
                                             website = :website,
                                             since_date = :since_date,
                                             is_featured = :is_featured,
                                             display_order = :display_order,
                                             updated_at = NOW()
                                             WHERE id = :id");
                        
                        $stmt->execute([
                            ':id' => $id,
                            ':name' => $name,
                            ':logo' => $logo,
                            ':industry' => $industry,
                            ':testimonial' => $testimonial,
                            ':rating' => $rating,
                            ':website' => $website,
                            ':since_date' => $since_date,
                            ':is_featured' => $is_featured,
                            ':display_order' => $display_order
                        ]);
                        
                        $message = "Client updated successfully!";
                        
                    } catch(PDOException $e) {
                        $error = "Error updating client: " . $e->getMessage();
                    }
                }
            }
        } else if ($action == 'delete') {
            $id = intval($_POST['id']);
            
            try {
                // Get logo path to delete file
                $stmt = $db->prepare("SELECT logo FROM clients WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $client = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($client['logo'] && file_exists('../../' . $client['logo'])) {
                    unlink('../../' . $client['logo']);
                }
                
                $stmt = $db->prepare("DELETE FROM clients WHERE id = :id");
                $stmt->execute([':id' => $id]);
                
                $message = "Client deleted successfully!";
                
            } catch(PDOException $e) {
                $error = "Error deleting client: " . $e->getMessage();
            }
        } else if ($action == 'feature') {
            $id = intval($_POST['id']);
            $is_featured = intval($_POST['is_featured']);
            
            try {
                $stmt = $db->prepare("UPDATE clients SET is_featured = :is_featured WHERE id = :id");
                $stmt->execute([':id' => $id, ':is_featured' => $is_featured]);
                
                $message = $is_featured ? "Client marked as featured!" : "Client removed from featured!";
                
            } catch(PDOException $e) {
                $error = "Error updating client: " . $e->getMessage();
            }
        }
    }
}

// Get clients list
$clients = [];
try {
    $stmt = $db->query("SELECT * FROM clients ORDER BY 
                        CASE WHEN is_featured = 1 THEN 0 ELSE 1 END,
                        display_order, name");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching clients: " . $e->getMessage();
}

// Get stats
$stats = [];
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM clients");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as featured FROM clients WHERE is_featured = 1");
    $stats['featured'] = $stmt->fetch(PDO::FETCH_ASSOC)['featured'];
    
} catch(PDOException $e) {
    error_log("Stats error: " . $e->getMessage());
}

// Check if editing
$edit_client = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    try {
        $stmt = $db->prepare("SELECT * FROM clients WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $edit_client = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = "Error fetching client: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients | KGN Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Manage Clients</h1>
                    <p class="text-gray-600">Manage client companies and testimonials</p>
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
                            <i class="fas fa-building text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Clients</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-star text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Featured Clients</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['featured']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-chart-line text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Avg. Rating</p>
                            <p class="text-2xl font-bold text-gray-800">
                                <?php 
                                try {
                                    $stmt = $db->query("SELECT AVG(rating) as avg_rating FROM clients");
                                    $avg = $stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'];
                                    echo number_format($avg, 1);
                                } catch(Exception $e) {
                                    echo "0.0";
                                }
                                ?>
                            </p>
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
                            <?php echo $edit_client ? 'Edit Client' : 'Add New Client'; ?>
                        </h2>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <?php if ($edit_client): ?>
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $edit_client['id']; ?>">
                            <input type="hidden" name="current_logo" value="<?php echo $edit_client['logo'] ?? ''; ?>">
                            <?php else: ?>
                            <input type="hidden" name="action" value="add">
                            <?php endif; ?>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Client Name *</label>
                                    <input type="text" 
                                           name="name" 
                                           required
                                           value="<?php echo $edit_client['name'] ?? ''; ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                           placeholder="e.g., NIYATI PVT. LTD">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                                    <div class="flex items-center space-x-4">
                                        <?php if (isset($edit_client['logo']) && $edit_client['logo']): ?>
                                        <div class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200">
                                            <img src="../../<?php echo htmlspecialchars($edit_client['logo']); ?>" 
                                                 alt="Current logo" class="w-full h-full object-contain">
                                        </div>
                                        <?php endif; ?>
                                        <div class="flex-1">
                                            <input type="file" 
                                                   name="logo" 
                                                   accept="image/*"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                            <p class="text-xs text-gray-500 mt-1">Recommended: 200x200px PNG or JPG</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                                        <input type="text" 
                                               name="industry" 
                                               value="<?php echo $edit_client['industry'] ?? ''; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                               placeholder="e.g., Manufacturing">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating (0-5)</label>
                                        <input type="number" 
                                               name="rating" 
                                               step="0.1"
                                               min="0"
                                               max="5"
                                               value="<?php echo $edit_client['rating'] ?? '5.0'; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                               placeholder="5.0">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                                    <input type="url" 
                                           name="website" 
                                           value="<?php echo $edit_client['website'] ?? ''; ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                           placeholder="https://example.com">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Testimonial</label>
                                    <textarea name="testimonial" 
                                              rows="4"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                              placeholder="Client's testimonial..."><?php echo $edit_client['testimonial'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Since Date</label>
                                        <input type="date" 
                                               name="since_date" 
                                               value="<?php echo $edit_client['since_date'] ?? ''; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                                        <input type="number" 
                                               name="display_order" 
                                               value="<?php echo $edit_client['display_order'] ?? '0'; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                               min="0">
                                    </div>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           name="is_featured" 
                                           id="is_featured"
                                           <?php echo ($edit_client['is_featured'] ?? 0) ? 'checked' : ''; ?>
                                           class="h-5 w-5 text-yellow-600 rounded">
                                    <label for="is_featured" class="ml-2 text-sm text-gray-700">Featured Client</label>
                                </div>
                                
                                <div class="pt-4">
                                    <button type="submit" 
                                            class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 px-4 rounded-lg font-medium transition">
                                        <i class="fas fa-save mr-2"></i>
                                        <?php echo $edit_client ? 'Update Client' : 'Add Client'; ?>
                                    </button>
                                    
                                    <?php if ($edit_client): ?>
                                    <a href="clients.php" 
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
                            <h2 class="text-xl font-bold text-gray-800">All Clients (<?php echo count($clients); ?>)</h2>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Industry</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Since</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($clients)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-building text-3xl mb-3 text-gray-300"></i>
                                            <p class="font-medium">No clients found</p>
                                            <p class="text-sm mt-1">Add your first client using the form</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($clients as $client): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <?php if ($client['logo']): ?>
                                                    <div class="w-10 h-10 flex-shrink-0 mr-3">
                                                        <div class="w-10 h-10 rounded-lg overflow-hidden border border-gray-200">
                                                            <img src="../../<?php echo htmlspecialchars($client['logo']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($client['name']); ?>" 
                                                                 class="w-full h-full object-contain">
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($client['name']); ?></div>
                                                        <?php if ($client['website']): ?>
                                                        <div class="text-xs text-blue-600">
                                                            <a href="<?php echo htmlspecialchars($client['website']); ?>" 
                                                               target="_blank" class="hover:underline">
                                                                <?php echo parse_url($client['website'], PHP_URL_HOST); ?>
                                                            </a>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                    <?php echo htmlspecialchars($client['industry']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-yellow-500 mr-2">
                                                        <?php echo str_repeat('★', floor($client['rating'])) . (fmod($client['rating'], 1) >= 0.5 ? '½' : '') . str_repeat('☆', floor(5 - $client['rating'])); ?>
                                                    </div>
                                                    <span class="text-sm font-medium"><?php echo number_format($client['rating'], 1); ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo $client['since_date'] ? date('Y', strtotime($client['since_date'])) : '-'; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if ($client['is_featured']): ?>
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-star mr-1"></i> Featured
                                                </span>
                                                <?php else: ?>
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                    Regular
                                                </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="clients.php?edit=<?php echo $client['id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form method="POST" action="" class="inline mr-3">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="action" value="feature">
                                                    <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                                                    <input type="hidden" name="is_featured" value="<?php echo $client['is_featured'] ? '0' : '1'; ?>">
                                                    <button type="submit" class="<?php echo $client['is_featured'] ? 'text-yellow-600 hover:text-yellow-900' : 'text-gray-600 hover:text-gray-900'; ?>" 
                                                            title="<?php echo $client['is_featured'] ? 'Remove from featured' : 'Mark as featured'; ?>">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="" class="inline" onsubmit="return confirm('Delete this client?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
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
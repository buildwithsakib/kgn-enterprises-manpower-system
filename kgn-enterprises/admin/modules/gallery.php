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
            $category = sanitize_input($_POST['category']);
            $location = sanitize_input($_POST['location']);
            $display_order = intval($_POST['display_order']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            // Handle image upload
            $image_url = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../uploads/gallery/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array(strtolower($file_extension), $allowed_extensions)) {
                    $file_name = 'gallery_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $file_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                        // Create thumbnail
                        createThumbnail($file_path, $upload_dir . 'thumb_' . $file_name, 300, 200);
                        $image_url = 'uploads/gallery/' . $file_name;
                    } else {
                        $error = "Error uploading image file.";
                    }
                } else {
                    $error = "Invalid file type. Allowed: JPG, PNG, GIF, WebP";
                }
            } elseif ($action == 'edit' && isset($_POST['current_image'])) {
                $image_url = $_POST['current_image'];
            }
            
            if (!$error) {
                if ($action == 'add') {
                    try {
                        $stmt = $db->prepare("INSERT INTO gallery (title, description, image_url, category, location, display_order, is_active) 
                                             VALUES (:title, :description, :image_url, :category, :location, :display_order, :is_active)");
                        
                        $stmt->execute([
                            ':title' => $title,
                            ':description' => $description,
                            ':image_url' => $image_url,
                            ':category' => $category,
                            ':location' => $location,
                            ':display_order' => $display_order,
                            ':is_active' => $is_active
                        ]);
                        
                        $message = "Gallery item added successfully!";
                        
                    } catch(PDOException $e) {
                        $error = "Error adding gallery item: " . $e->getMessage();
                    }
                } else if ($action == 'edit') {
                    $id = intval($_POST['id']);
                    
                    try {
                        $stmt = $db->prepare("UPDATE gallery SET 
                                             title = :title,
                                             description = :description,
                                             image_url = :image_url,
                                             category = :category,
                                             location = :location,
                                             display_order = :display_order,
                                             is_active = :is_active,
                                             updated_at = NOW()
                                             WHERE id = :id");
                        
                        $stmt->execute([
                            ':id' => $id,
                            ':title' => $title,
                            ':description' => $description,
                            ':image_url' => $image_url,
                            ':category' => $category,
                            ':location' => $location,
                            ':display_order' => $display_order,
                            ':is_active' => $is_active
                        ]);
                        
                        $message = "Gallery item updated successfully!";
                        
                    } catch(PDOException $e) {
                        $error = "Error updating gallery item: " . $e->getMessage();
                    }
                }
            }
        } else if ($action == 'delete') {
            $id = intval($_POST['id']);
            
            try {
                // Get image path to delete file
                $stmt = $db->prepare("SELECT image_url FROM gallery WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($item['image_url']) {
                    $image_path = '../../' . $item['image_url'];
                    $thumb_path = '../../' . dirname($item['image_url']) . '/thumb_' . basename($item['image_url']);
                    
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                    if (file_exists($thumb_path)) {
                        unlink($thumb_path);
                    }
                }
                
                $stmt = $db->prepare("DELETE FROM gallery WHERE id = :id");
                $stmt->execute([':id' => $id]);
                
                $message = "Gallery item deleted successfully!";
                
            } catch(PDOException $e) {
                $error = "Error deleting gallery item: " . $e->getMessage();
            }
        }
    }
}

// Function to create thumbnail
function createThumbnail($source, $destination, $width, $height) {
    $info = getimagesize($source);
    $mime = $info['mime'];
    
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }
    
    $src_width = imagesx($image);
    $src_height = imagesy($image);
    
    $thumbnail = imagecreatetruecolor($width, $height);
    
    // Preserve transparency for PNG and GIF
    if ($mime == 'image/png' || $mime == 'image/gif') {
        imagecolortransparent($thumbnail, imagecolorallocatealpha($thumbnail, 0, 0, 0, 127));
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
    }
    
    // Calculate aspect ratio
    $src_ratio = $src_width / $src_height;
    $dst_ratio = $width / $height;
    
    if ($dst_ratio > $src_ratio) {
        $new_height = $height;
        $new_width = $height * $src_ratio;
    } else {
        $new_width = $width;
        $new_height = $width / $src_ratio;
    }
    
    $dst_x = ($width - $new_width) / 2;
    $dst_y = ($height - $new_height) / 2;
    
    imagecopyresampled($thumbnail, $image, $dst_x, $dst_y, 0, 0, $new_width, $new_height, $src_width, $src_height);
    
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($thumbnail, $destination, 85);
            break;
        case 'image/png':
            imagepng($thumbnail, $destination, 9);
            break;
        case 'image/gif':
            imagegif($thumbnail, $destination);
            break;
        case 'image/webp':
            imagewebp($thumbnail, $destination, 85);
            break;
    }
    
    imagedestroy($image);
    imagedestroy($thumbnail);
    
    return true;
}

// Get gallery items
$gallery_items = [];
try {
    $stmt = $db->query("SELECT * FROM gallery ORDER BY category, display_order, created_at DESC");
    $gallery_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching gallery items: " . $e->getMessage();
}

// Get categories
$categories = [];
try {
    $stmt = $db->query("SELECT DISTINCT category FROM gallery ORDER BY category");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) {
    error_log("Categories error: " . $e->getMessage());
}

// Get stats
$stats = [];
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM gallery");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as active FROM gallery WHERE is_active = 1");
    $stats['active'] = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
    
    $stmt = $db->query("SELECT COUNT(DISTINCT category) as categories FROM gallery");
    $stats['categories'] = $stmt->fetch(PDO::FETCH_ASSOC)['categories'];
    
} catch(PDOException $e) {
    error_log("Stats error: " . $e->getMessage());
}

// Check if editing
$edit_item = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    try {
        $stmt = $db->prepare("SELECT * FROM gallery WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $edit_item = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = "Error fetching gallery item: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery | KGN Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Manage Gallery</h1>
                    <p class="text-gray-600">Manage gallery images and categories</p>
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
                            <i class="fas fa-images text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Images</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-eye text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Active Images</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['active']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-folder text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Categories</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['categories']; ?></p>
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
                            <?php echo $edit_item ? 'Edit Gallery Item' : 'Add New Gallery Item'; ?>
                        </h2>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <?php if ($edit_item): ?>
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                            <input type="hidden" name="current_image" value="<?php echo $edit_item['image_url'] ?? ''; ?>">
                            <?php else: ?>
                            <input type="hidden" name="action" value="add">
                            <?php endif; ?>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                                    <input type="text" 
                                           name="title" 
                                           required
                                           value="<?php echo $edit_item['title'] ?? ''; ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                           placeholder="e.g., Team Meeting">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Image *</label>
                                    <div class="space-y-3">
                                        <?php if (isset($edit_item['image_url']) && $edit_item['image_url']): ?>
                                        <div class="rounded-lg overflow-hidden border border-gray-200">
                                            <img src="../../<?php echo htmlspecialchars($edit_item['image_url']); ?>" 
                                                 alt="Current image" class="w-full h-48 object-cover">
                                        </div>
                                        <?php endif; ?>
                                        <input type="file" 
                                               name="image" 
                                               accept="image/*"
                                               <?php echo !$edit_item ? 'required' : ''; ?>
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                        <p class="text-xs text-gray-500">Recommended: 1200x800px JPG or PNG</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea name="description" 
                                              rows="3"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                              placeholder="Image description..."><?php echo $edit_item['description'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                        <select name="category" 
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                            <option value="general" <?php echo ($edit_item['category'] ?? 'general') == 'general' ? 'selected' : ''; ?>>General</option>
                                            <option value="events" <?php echo ($edit_item['category'] ?? '') == 'events' ? 'selected' : ''; ?>>Events</option>
                                            <option value="workforce" <?php echo ($edit_item['category'] ?? '') == 'workforce' ? 'selected' : ''; ?>>Workforce</option>
                                            <option value="facility" <?php echo ($edit_item['category'] ?? '') == 'facility' ? 'selected' : ''; ?>>Facility</option>
                                            <option value="team" <?php echo ($edit_item['category'] ?? '') == 'team' ? 'selected' : ''; ?>>Team</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                        <input type="text" 
                                               name="location" 
                                               value="<?php echo $edit_item['location'] ?? ''; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                               placeholder="e.g., Pune">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                                        <input type="number" 
                                               name="display_order" 
                                               value="<?php echo $edit_item['display_order'] ?? '0'; ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                               min="0">
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               id="is_active"
                                               <?php echo ($edit_item['is_active'] ?? 1) ? 'checked' : ''; ?>
                                               class="h-5 w-5 text-orange-600 rounded">
                                        <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                                    </div>
                                </div>
                                
                                <div class="pt-4">
                                    <button type="submit" 
                                            class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 px-4 rounded-lg font-medium transition">
                                        <i class="fas fa-save mr-2"></i>
                                        <?php echo $edit_item ? 'Update Item' : 'Add Item'; ?>
                                    </button>
                                    
                                    <?php if ($edit_item): ?>
                                    <a href="gallery.php" 
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
                            <h2 class="text-xl font-bold text-gray-800">Gallery Items (<?php echo count($gallery_items); ?>)</h2>
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                            <div class="flex flex-wrap gap-2">
                                <button onclick="filterCategory('all')" 
                                        class="px-3 py-1 text-sm font-medium rounded-full bg-orange-600 text-white">
                                    All
                                </button>
                                <?php foreach (array_unique($categories) as $category): ?>
                                <button onclick="filterCategory('<?php echo $category; ?>')" 
                                        class="px-3 py-1 text-sm font-medium rounded-full bg-gray-200 text-gray-800 hover:bg-gray-300">
                                    <?php echo ucfirst($category); ?>
                                </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-6" id="galleryGrid">
                            <?php if (empty($gallery_items)): ?>
                            <div class="col-span-3 text-center py-8 text-gray-500">
                                <i class="fas fa-images text-3xl mb-3 text-gray-300"></i>
                                <p class="font-medium">No gallery items found</p>
                                <p class="text-sm mt-1">Add your first gallery item using the form</p>
                            </div>
                            <?php else: ?>
                                <?php foreach ($gallery_items as $item): ?>
                                <div class="bg-gray-50 rounded-xl overflow-hidden border border-gray-200 gallery-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                                    <div class="relative h-48">
                                        <img src="../../<?php echo htmlspecialchars($item['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                             class="w-full h-full object-cover">
                                        <?php if (!$item['is_active']): ?>
                                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                            <span class="text-white font-medium">Inactive</span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="absolute top-2 right-2 flex space-x-1">
                                            <a href="gallery.php?edit=<?php echo $item['id']; ?>" 
                                               class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            <form method="POST" action="" 
                                                  onsubmit="return confirm('Delete this item?');" 
                                                  class="inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" 
                                                        class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($item['title']); ?></h3>
                                            <span class="text-xs px-2 py-1 rounded-full bg-gray-200 text-gray-800">
                                                <?php echo htmlspecialchars($item['category']); ?>
                                            </span>
                                        </div>
                                        <?php if ($item['description']): ?>
                                        <p class="text-sm text-gray-600 mb-2 line-clamp-2"><?php echo htmlspecialchars($item['description']); ?></p>
                                        <?php endif; ?>
                                        <div class="flex justify-between items-center text-xs text-gray-500">
                                            <span>Order: <?php echo $item['display_order']; ?></span>
                                            <span><?php echo date('M d, Y', strtotime($item['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Category filter
        function filterCategory(category) {
            const items = document.querySelectorAll('.gallery-item');
            items.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Update active button
            document.querySelectorAll('.bg-gray-50 button').forEach(btn => {
                btn.classList.remove('bg-orange-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-800', 'hover:bg-gray-300');
            });
            
            if (category === 'all') {
                event.target.classList.remove('bg-gray-200', 'text-gray-800', 'hover:bg-gray-300');
                event.target.classList.add('bg-orange-600', 'text-white');
            } else {
                event.target.classList.remove('bg-gray-200', 'text-gray-800', 'hover:bg-gray-300');
                event.target.classList.add('bg-orange-600', 'text-white');
            }
        }
        
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
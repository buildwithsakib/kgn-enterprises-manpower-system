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
        
        if ($action == 'save_settings') {
            $settings = $_POST['settings'] ?? [];
            $files = $_FILES['files'] ?? [];
            
            try {
                $db->beginTransaction();
                
                foreach ($settings as $key => $value) {
                    $stmt = $db->prepare("UPDATE website_settings SET setting_value = :value WHERE setting_key = :key");
                    $stmt->execute([':value' => $value, ':key' => $key]);
                }
                
                // Handle file uploads
                foreach ($files['name'] as $key => $name) {
                    if ($files['error'][$key] === UPLOAD_ERR_OK) {
                        $upload_dir = '../../uploads/settings/';
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_extension = pathinfo($name, PATHINFO_EXTENSION);
                        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'ico', 'svg'];
                        
                        if (in_array(strtolower($file_extension), $allowed_extensions)) {
                            $file_name = $key . '_' . time() . '.' . $file_extension;
                            $file_path = $upload_dir . $file_name;
                            
                            if (move_uploaded_file($files['tmp_name'][$key], $file_path)) {
                                $relative_path = 'uploads/settings/' . $file_name;
                                
                                // Delete old file if exists
                                $stmt = $db->prepare("SELECT setting_value FROM website_settings WHERE setting_key = :key");
                                $stmt->execute([':key' => $key]);
                                $old_value = $stmt->fetch(PDO::FETCH_COLUMN);
                                
                                if ($old_value && file_exists('../../' . $old_value)) {
                                    unlink('../../' . $old_value);
                                }
                                
                                $stmt = $db->prepare("UPDATE website_settings SET setting_value = :value WHERE setting_key = :key");
                                $stmt->execute([':value' => $relative_path, ':key' => $key]);
                            }
                        }
                    }
                }
                
                $db->commit();
                $message = "Settings saved successfully!";
                
            } catch(PDOException $e) {
                $db->rollBack();
                $error = "Error saving settings: " . $e->getMessage();
            }
        } else if ($action == 'add_setting') {
            $key = sanitize_input($_POST['key']);
            $value = sanitize_input($_POST['value']);
            $type = sanitize_input($_POST['type']);
            $category = sanitize_input($_POST['category']);
            $display_order = intval($_POST['display_order']);
            
            try {
                $stmt = $db->prepare("INSERT INTO website_settings (setting_key, setting_value, setting_type, category, display_order) 
                                     VALUES (:key, :value, :type, :category, :display_order)");
                
                $stmt->execute([
                    ':key' => $key,
                    ':value' => $value,
                    ':type' => $type,
                    ':category' => $category,
                    ':display_order' => $display_order
                ]);
                
                $message = "Setting added successfully!";
                
            } catch(PDOException $e) {
                $error = "Error adding setting: " . $e->getMessage();
            }
        } else if ($action == 'delete_setting') {
            $id = intval($_POST['id']);
            
            try {
                // Check if it's an image setting and delete file
                $stmt = $db->prepare("SELECT setting_key, setting_value, setting_type FROM website_settings WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $setting = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($setting && $setting['setting_type'] == 'image' && $setting['setting_value'] && file_exists('../../' . $setting['setting_value'])) {
                    unlink('../../' . $setting['setting_value']);
                }
                
                $stmt = $db->prepare("DELETE FROM website_settings WHERE id = :id");
                $stmt->execute([':id' => $id]);
                
                $message = "Setting deleted successfully!";
                
            } catch(PDOException $e) {
                $error = "Error deleting setting: " . $e->getMessage();
            }
        } else if ($action == 'clear_cache') {
            // Clear cache directory
            $cache_dir = '../../cache/';
            if (file_exists($cache_dir)) {
                array_map('unlink', glob($cache_dir . '*'));
            }
            $message = "Cache cleared successfully!";
        }
    }
}

// Get settings by category
$settings_by_category = [];
try {
    $stmt = $db->query("SELECT * FROM website_settings ORDER BY category, display_order, setting_key");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($settings as $setting) {
        $category = $setting['category'];
        if (!isset($settings_by_category[$category])) {
            $settings_by_category[$category] = [];
        }
        $settings_by_category[$category][] = $setting;
    }
} catch(PDOException $e) {
    $error = "Error fetching settings: " . $e->getMessage();
}

// Get all categories
$categories = array_keys($settings_by_category);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Settings | KGN Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Website Settings</h1>
                    <p class="text-gray-600">Configure website settings and preferences</p>
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
            
            <!-- Category Tabs -->
            <div class="mb-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto">
                        <?php foreach ($categories as $index => $category): ?>
                        <button onclick="showCategory('<?php echo $category; ?>')" 
                                class="<?php echo $index === 0 ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> 
                                       whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            <?php echo ucfirst(str_replace('_', ' ', $category)); ?>
                        </button>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </div>
            
            <!-- Settings Form -->
            <form method="POST" action="" enctype="multipart/form-data" id="settingsForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="save_settings">
                
                <?php foreach ($settings_by_category as $category => $settings): ?>
                <div class="category-content mb-8" id="category-<?php echo $category; ?>" style="<?php echo $category !== $categories[0] ? 'display: none;' : ''; ?>">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-xl font-bold text-gray-800">
                                <?php echo ucfirst(str_replace('_', ' ', $category)); ?> Settings
                            </h2>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <?php foreach ($settings as $setting): ?>
                                <div class="setting-item">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <?php echo ucfirst(str_replace('_', ' ', $setting['setting_key'])); ?>
                                        <span class="text-xs text-gray-500 ml-1">(<?php echo $setting['setting_type']; ?>)</span>
                                    </label>
                                    
                                    <?php if ($setting['setting_type'] == 'text'): ?>
                                    <input type="text" 
                                           name="settings[<?php echo $setting['setting_key']; ?>]"
                                           value="<?php echo htmlspecialchars($setting['setting_value'] ?? ''); ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    
                                    <?php elseif ($setting['setting_type'] == 'textarea'): ?>
                                    <textarea name="settings[<?php echo $setting['setting_key']; ?>]"
                                              rows="4"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"><?php echo htmlspecialchars($setting['setting_value'] ?? ''); ?></textarea>
                                    
                                    <?php elseif ($setting['setting_type'] == 'image'): ?>
                                    <div class="space-y-3">
                                        <?php if ($setting['setting_value'] && file_exists('../../' . $setting['setting_value'])): ?>
                                        <div class="flex items-center space-x-4">
                                            <div class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200">
                                                <img src="../../<?php echo htmlspecialchars($setting['setting_value']); ?>" 
                                                     alt="Current image" class="w-full h-full object-contain">
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                Current: <?php echo basename($setting['setting_value']); ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <input type="file" 
                                               name="files[<?php echo $setting['setting_key']; ?>]"
                                               accept="image/*"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                        <input type="hidden" 
                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                               value="<?php echo htmlspecialchars($setting['setting_value'] ?? ''); ?>">
                                    </div>
                                    
                                    <?php elseif ($setting['setting_type'] == 'boolean'): ?>
                                    <div class="flex items-center">
                                        <input type="hidden" 
                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                               value="0">
                                        <input type="checkbox" 
                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                               value="1"
                                               <?php echo ($setting['setting_value'] ?? '0') == '1' ? 'checked' : ''; ?>
                                               class="h-5 w-5 text-orange-600 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Enabled</span>
                                    </div>
                                    
                                    <?php elseif ($setting['setting_type'] == 'number'): ?>
                                    <input type="number" 
                                           name="settings[<?php echo $setting['setting_key']; ?>]"
                                           value="<?php echo htmlspecialchars($setting['setting_value'] ?? '0'); ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    
                                    <?php endif; ?>
                                    
                                    <div class="mt-1 text-xs text-gray-500">
                                        Key: <code><?php echo $setting['setting_key']; ?></code>
                                        <?php if ($setting['display_order'] > 0): ?>
                                        • Order: <?php echo $setting['display_order']; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Save Button -->
                <div class="flex justify-end space-x-4 mt-8">
                    <button type="button" 
                            onclick="clearCache()"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium">
                        <i class="fas fa-broom mr-2"></i> Clear Cache
                    </button>
                    <button type="submit" 
                            class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-3 rounded-lg font-medium">
                        <i class="fas fa-save mr-2"></i> Save All Settings
                    </button>
                </div>
            </form>
            
            <!-- Add New Setting -->
            <div class="mt-12 bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Add New Setting</h2>
                
                <form method="POST" action="" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="add_setting">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Setting Key *</label>
                            <input type="text" 
                                   name="key" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="e.g., facebook_url">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select name="category" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="general">General</option>
                                <option value="contact">Contact</option>
                                <option value="social">Social</option>
                                <option value="seo">SEO</option>
                                <option value="about">About</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                            <select name="type" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="image">Image</option>
                                <option value="boolean">Boolean</option>
                                <option value="number">Number</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                            <input type="number" 
                                   name="display_order" 
                                   value="0"
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Setting Value</label>
                        <textarea name="value" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                  placeholder="Enter setting value"></textarea>
                    </div>
                    
                    <div class="pt-2">
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium">
                            <i class="fas fa-plus mr-2"></i> Add Setting
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- All Settings Table -->
            <div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">All Settings (<?php echo count($settings); ?>)</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($settings as $setting): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($setting['setting_key']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate" title="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                        <?php 
                                        if ($setting['setting_type'] == 'image' && $setting['setting_value']) {
                                            echo '<span class="text-blue-600">' . htmlspecialchars(basename($setting['setting_value'])) . '</span>';
                                        } elseif ($setting['setting_type'] == 'boolean') {
                                            echo $setting['setting_value'] == '1' ? 'Yes' : 'No';
                                        } else {
                                            echo htmlspecialchars(substr($setting['setting_value'], 0, 100));
                                            if (strlen($setting['setting_value']) > 100) echo '...';
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        <?php echo htmlspecialchars($setting['setting_type']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($setting['category']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form method="POST" action="" class="inline" onsubmit="return confirm('Delete this setting?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="action" value="delete_setting">
                                        <input type="hidden" name="id" value="<?php echo $setting['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Category tabs
        function showCategory(category) {
            // Hide all category content
            document.querySelectorAll('.category-content').forEach(el => {
                el.style.display = 'none';
            });
            
            // Show selected category
            document.getElementById('category-' + category).style.display = 'block';
            
            // Update active tab
            document.querySelectorAll('nav button').forEach(btn => {
                btn.classList.remove('border-orange-500', 'text-orange-600');
                btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });
            
            event.target.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            event.target.classList.add('border-orange-500', 'text-orange-600');
        }
        
        // Clear cache
        function clearCache() {
            if (confirm('Clear website cache?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = 'csrf_token';
                csrf.value = '<?php echo $_SESSION['csrf_token']; ?>';
                form.appendChild(csrf);
                
                const action = document.createElement('input');
                action.type = 'hidden';
                action.name = 'action';
                action.value = 'clear_cache';
                form.appendChild(action);
                
                document.body.appendChild(form);
                form.submit();
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
        
        // Preview image before upload
        document.querySelectorAll('input[type="file"][accept="image/*"]').forEach(input => {
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = input.parentElement.querySelector('img');
                        if (preview) {
                            preview.src = e.target.result;
                        } else {
                            const div = document.createElement('div');
                            div.className = 'w-16 h-16 rounded-lg overflow-hidden border border-gray-200';
                            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-contain">`;
                            input.parentElement.prepend(div);
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</body>
</html>
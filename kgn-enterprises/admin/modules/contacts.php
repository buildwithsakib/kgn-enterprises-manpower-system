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
        
        if ($action == 'update_status') {
            $id = intval($_POST['id']);
            $status = sanitize_input($_POST['status']);
            $notes = sanitize_input($_POST['notes'] ?? '');
            
            try {
                $stmt = $db->prepare("UPDATE contact_submissions SET status = :status, notes = :notes WHERE id = :id");
                $stmt->execute([
                    ':id' => $id,
                    ':status' => $status,
                    ':notes' => $notes
                ]);
                
                $message = "Contact status updated!";
                
            } catch(PDOException $e) {
                $error = "Error updating contact: " . $e->getMessage();
            }
        } else if ($action == 'delete') {
            $id = intval($_POST['id']);
            
            try {
                $stmt = $db->prepare("DELETE FROM contact_submissions WHERE id = :id");
                $stmt->execute([':id' => $id]);
                
                $message = "Contact submission deleted!";
                
            } catch(PDOException $e) {
                $error = "Error deleting contact: " . $e->getMessage();
            }
        } else if ($action == 'bulk_delete') {
            $ids = $_POST['ids'] ?? [];
            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                try {
                    $stmt = $db->prepare("DELETE FROM contact_submissions WHERE id IN ($placeholders)");
                    $stmt->execute($ids);
                    
                    $message = "Selected contacts deleted!";
                    
                } catch(PDOException $e) {
                    $error = "Error deleting contacts: " . $e->getMessage();
                }
            }
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT * FROM contact_submissions WHERE 1=1";
$params = [];

if ($status_filter != 'all') {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

if ($date_from) {
    $query .= " AND DATE(submitted_at) >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $query .= " AND DATE(submitted_at) <= ?";
    $params[] = $date_to;
}

if ($search) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ? OR subject LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$query .= " ORDER BY 
            CASE WHEN status = 'pending' THEN 0 
                 WHEN status = 'contacted' THEN 1
                 WHEN status = 'resolved' THEN 2
                 ELSE 3 END,
            submitted_at DESC";

// Get contacts list
$contacts = [];
try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching contacts: " . $e->getMessage();
}

// Get stats
$stats = [];
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM contact_submissions");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as pending FROM contact_submissions WHERE status = 'pending'");
    $stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];
    
    $stmt = $db->query("SELECT COUNT(*) as contacted FROM contact_submissions WHERE status = 'contacted'");
    $stats['contacted'] = $stmt->fetch(PDO::FETCH_ASSOC)['contacted'];
    
    $stmt = $db->query("SELECT COUNT(*) as resolved FROM contact_submissions WHERE status = 'resolved'");
    $stats['resolved'] = $stmt->fetch(PDO::FETCH_ASSOC)['resolved'];
    
    // Monthly stats
    $stmt = $db->query("SELECT DATE_FORMAT(submitted_at, '%Y-%m') as month, COUNT(*) as count 
                        FROM contact_submissions 
                        WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                        GROUP BY DATE_FORMAT(submitted_at, '%Y-%m')
                        ORDER BY month DESC");
    $stats['monthly'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    error_log("Stats error: " . $e->getMessage());
}

// Check if viewing single contact
$view_contact = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $id = intval($_GET['view']);
    try {
        $stmt = $db->prepare("SELECT * FROM contact_submissions WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $view_contact = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Mark as contacted if still pending
        if ($view_contact && $view_contact['status'] == 'pending') {
            $stmt = $db->prepare("UPDATE contact_submissions SET status = 'contacted' WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
    } catch(PDOException $e) {
        $error = "Error fetching contact: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contacts | KGN Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Contact Submissions</h1>
                    <p class="text-gray-600">Manage and respond to contact form submissions</p>
                </div>
                <a href="../dashboard.php" class="text-orange-600 hover:text-orange-700">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-inbox text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Submissions</p>
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
                            <p class="text-sm text-gray-600">Pending</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['pending']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-phone text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Contacted</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['contacted']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Resolved</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $stats['resolved']; ?></p>
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
            
            <?php if ($view_contact): ?>
            <!-- View Single Contact -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Contact Details</h2>
                        <p class="text-gray-600">Submitted on <?php echo date('F j, Y g:i A', strtotime($view_contact['submitted_at'])); ?></p>
                    </div>
                    <a href="contacts.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-bold text-gray-700 mb-4">Contact Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-500">Full Name</label>
                                <p class="font-medium text-lg"><?php echo htmlspecialchars($view_contact['name']); ?></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Email</label>
                                    <p class="font-medium">
                                        <a href="mailto:<?php echo htmlspecialchars($view_contact['email']); ?>" 
                                           class="text-blue-600 hover:underline">
                                            <?php echo htmlspecialchars($view_contact['email']); ?>
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Phone</label>
                                    <p class="font-medium">
                                        <a href="tel:<?php echo htmlspecialchars($view_contact['phone']); ?>" 
                                           class="text-blue-600 hover:underline">
                                            <?php echo htmlspecialchars($view_contact['phone']); ?>
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Subject</label>
                                <p class="font-medium"><?php echo htmlspecialchars($view_contact['subject'] ?? 'No subject'); ?></p>
                            </div>
                            <?php if ($view_contact['service_type']): ?>
                            <div>
                                <label class="text-sm text-gray-500">Service Type</label>
                                <p class="font-medium"><?php echo htmlspecialchars($view_contact['service_type']); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if ($view_contact['source']): ?>
                            <div>
                                <label class="text-sm text-gray-500">Source</label>
                                <p class="font-medium">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        <?php echo htmlspecialchars($view_contact['source']); ?>
                                    </span>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-bold text-gray-700 mb-4">Message</h3>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <p class="text-gray-700 whitespace-pre-line"><?php echo htmlspecialchars($view_contact['message']); ?></p>
                        </div>
                        
                        <!-- Status Update Form -->
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="id" value="<?php echo $view_contact['id']; ?>">
                            
                            <h4 class="font-bold text-gray-700 mb-3">Update Status</h4>
                            <div class="space-y-3">
                                <div>
                                    <select name="status" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                        <option value="pending" <?php echo $view_contact['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="contacted" <?php echo $view_contact['status'] == 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                        <option value="resolved" <?php echo $view_contact['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                        <option value="spam" <?php echo $view_contact['status'] == 'spam' ? 'selected' : ''; ?>>Spam</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <textarea name="notes" 
                                              rows="3"
                                              placeholder="Add notes (optional)"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"><?php echo htmlspecialchars($view_contact['notes'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="flex space-x-3">
                                    <button type="submit" 
                                            class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded-lg font-medium">
                                        Update Status
                                    </button>
                                    <a href="mailto:<?php echo htmlspecialchars($view_contact['email']); ?>?subject=Re: <?php echo urlencode($view_contact['subject'] ?? 'Your Inquiry'); ?>" 
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium text-center">
                                        <i class="fas fa-reply mr-2"></i> Reply
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php else: ?>
            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <form method="GET" action="" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Status</option>
                                <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="contacted" <?php echo $status_filter == 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                <option value="resolved" <?php echo $status_filter == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                <option value="spam" <?php echo $status_filter == 'spam' ? 'selected' : ''; ?>>Spam</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                            <input type="date" 
                                   name="date_from" 
                                   value="<?php echo $date_from; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                            <input type="date" 
                                   name="date_to" 
                                   value="<?php echo $date_to; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" 
                                   name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   placeholder="Name, email, or phone"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="submit" 
                                class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg font-medium">
                            <i class="fas fa-filter mr-2"></i> Apply Filters
                        </button>
                        <a href="contacts.php" 
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg font-medium">
                            <i class="fas fa-times mr-2"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Bulk Actions -->
            <form method="POST" action="" id="bulkForm" class="mb-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="bulk_delete">
                <input type="hidden" name="ids" id="bulkIds">
                
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            onclick="selectAll()"
                            class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-check-square mr-1"></i> Select All
                    </button>
                    <button type="button" 
                            onclick="deselectAll()"
                            class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-square mr-1"></i> Deselect All
                    </button>
                    <button type="submit" 
                            onclick="return confirm('Delete selected contacts?')"
                            class="text-sm text-red-600 hover:text-red-800">
                        <i class="fas fa-trash mr-1"></i> Delete Selected
                    </button>
                </div>
            </form>
            
            <!-- Contacts List -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">All Submissions (<?php echo count($contacts); ?>)</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">
                                    <input type="checkbox" id="selectAll" class="rounded">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($contacts)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-3 text-gray-300"></i>
                                    <p class="font-medium">No contact submissions found</p>
                                    <?php if ($status_filter != 'all' || $date_from || $date_to || $search): ?>
                                    <p class="text-sm mt-1">Try adjusting your filters</p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($contacts as $contact): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" 
                                               name="contact_ids[]" 
                                               value="<?php echo $contact['id']; ?>" 
                                               class="contact-checkbox rounded">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($contact['name']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($contact['email']); ?></div>
                                            <div class="text-xs text-gray-400"><?php echo htmlspecialchars($contact['phone']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($contact['subject'] ?: 'No subject'); ?></div>
                                        <?php if ($contact['service_type']): ?>
                                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars($contact['service_type']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php
                                        $status_colors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'contacted' => 'bg-blue-100 text-blue-800',
                                            'resolved' => 'bg-green-100 text-green-800',
                                            'spam' => 'bg-red-100 text-red-800'
                                        ];
                                        $status_class = $status_colors[$contact['status']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full <?php echo $status_class; ?>">
                                            <?php echo ucfirst($contact['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?php echo date('M d, Y', strtotime($contact['submitted_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="contacts.php?view=<?php echo $contact['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>?subject=Re: <?php echo urlencode($contact['subject'] ?: 'Your Inquiry'); ?>" 
                                           class="text-green-600 hover:text-green-900 mr-3">
                                            <i class="fas fa-reply"></i>
                                        </a>
                                        <form method="POST" action="" class="inline" onsubmit="return confirm('Delete this contact?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
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
                
                <!-- Monthly Stats -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <h3 class="font-bold text-gray-700 mb-3">Monthly Submissions (Last 6 months)</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        <?php if (empty($stats['monthly'])): ?>
                        <div class="text-center text-gray-500">
                            <p class="text-sm">No data available</p>
                        </div>
                        <?php else: ?>
                            <?php foreach ($stats['monthly'] as $month): ?>
                            <div class="text-center">
                                <div class="text-lg font-bold text-orange-600"><?php echo $month['count']; ?></div>
                                <div class="text-xs text-gray-600"><?php echo date('M Y', strtotime($month['month'] . '-01')); ?></div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Bulk selection
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.contact-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        function selectAll() {
            const checkboxes = document.querySelectorAll('.contact-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            document.getElementById('selectAll').checked = true;
        }
        
        function deselectAll() {
            const checkboxes = document.querySelectorAll('.contact-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('selectAll').checked = false;
        }
        
        // Bulk form submission
        document.getElementById('bulkForm').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.contact-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            document.getElementById('bulkIds').value = JSON.stringify(ids);
            
            if (ids.length === 0) {
                e.preventDefault();
                alert('Please select at least one contact.');
                return false;
            }
        });
        
        // Update select all when individual checkboxes change
        document.querySelectorAll('.contact-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allCheckboxes = document.querySelectorAll('.contact-checkbox');
                const selectAll = document.getElementById('selectAll');
                selectAll.checked = Array.from(allCheckboxes).every(cb => cb.checked);
            });
        });
    </script>
</body>
</html>
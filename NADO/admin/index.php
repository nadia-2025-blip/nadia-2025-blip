<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Database configuration
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "sweets_course";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    // Get total count
    $countStmt = $conn->query("SELECT COUNT(*) FROM registrations");
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);
    
    // Get registrations with pagination
    $stmt = $conn->prepare("SELECT * FROM registrations ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$limit, $offset]);
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $todayCount = $conn->query("SELECT COUNT(*) FROM registrations WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    $weekCount = $conn->query("SELECT COUNT(*) FROM registrations WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
    $monthCount = $conn->query("SELECT COUNT(*) FROM registrations WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=registrations_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // CSV headers
    fputcsv($output, ['ID', 'الاسم الكامل', 'رقم الهاتف', 'البريد الإلكتروني', 'البلدية', 'تاريخ التسجيل']);
    
    // Get all registrations for export
    $exportStmt = $conn->query("SELECT * FROM registrations ORDER BY created_at DESC");
    while ($row = $exportStmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['id'],
            $row['full_name'],
            $row['phone'],
            $row['email'] ?: 'غير مُحدد',
            $row['city'],
            date('d/m/Y H:i', strtotime($row['created_at']))
        ]);
    }
    
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - دورة الأستاذة نادية</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #e94560 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #f38ba8;
        }
        
        .admin-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.1);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 600;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        
        .dashboard {
            padding: 40px 0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.total { border-top: 4px solid #28a745; }
        .stat-card.today { border-top: 4px solid #17a2b8; }
        .stat-card.week { border-top: 4px solid #ffc107; }
        .stat-card.month { border-top: 4px solid #e94560; }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 10px;
        }
        
        .stat-card.total .stat-number { color: #28a745; }
        .stat-card.today .stat-number { color: #17a2b8; }
        .stat-card.week .stat-number { color: #ffc107; }
        .stat-card.month .stat-number { color: #e94560; }
        
        .stat-label {
            font-size: 1.1rem;
            color: #666;
            font-weight: 600;
        }
        
        .registrations-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .section-header {
            background: linear-gradient(135deg, #e94560, #f38ba8);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .export-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .export-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: right;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 700;
            color: #1a1a2e;
            position: sticky;
            top: 0;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .pagination {
            padding: 25px 30px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        .page-btn {
            padding: 10px 15px;
            border: 1px solid #ddd;
            background: white;
            color: #666;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .page-btn:hover,
        .page-btn.active {
            background: #e94560;
            color: white;
            border-color: #e94560;
        }
        
        .no-data {
            text-align: center;
            padding: 50px;
            color: #666;
            font-size: 1.2rem;
        }
        
        .phone-number {
            font-weight: 600;
            color: #e94560;
        }
        
        .email {
            color: #17a2b8;
        }
        
        .city {
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .date {
            font-size: 0.9rem;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            table {
                font-size: 0.9rem;
            }
            
            th, td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1><i class="fas fa-crown"></i> لوحة تحكم دورة الأستاذة نادية</h1>
                </div>
                <div class="admin-info">
                    <span><i class="fas fa-user-shield"></i> مرحباً، المدير</span>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="dashboard">
        <div class="container">
            <?php if (isset($error)): ?>
                <div class="error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card total">
                    <div class="stat-number"><?php echo number_format($totalRecords); ?></div>
                    <div class="stat-label">إجمالي التسجيلات</div>
                </div>
                <div class="stat-card today">
                    <div class="stat-number"><?php echo number_format($todayCount); ?></div>
                    <div class="stat-label">تسجيلات اليوم</div>
                </div>
                <div class="stat-card week">
                    <div class="stat-number"><?php echo number_format($weekCount); ?></div>
                    <div class="stat-label">تسجيلات الأسبوع</div>
                </div>
                <div class="stat-card month">
                    <div class="stat-number"><?php echo number_format($monthCount); ?></div>
                    <div class="stat-label">تسجيلات الشهر</div>
                </div>
            </div>

            <div class="registrations-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-users"></i> قائمة المسجلين
                    </h2>
                    <a href="?export=csv" class="export-btn">
                        <i class="fas fa-download"></i> تصدير CSV
                    </a>
                </div>

                <?php if (empty($registrations)): ?>
                    <div class="no-data">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 20px; color: #ddd;"></i>
                        <p>لا توجد تسجيلات حتى الآن</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم الكامل</th>
                                    <th>رقم الهاتف</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>البلدية</th>
                                    <th>تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registrations as $registration): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($registration['id']); ?></td>
                                        <td><strong><?php echo htmlspecialchars($registration['full_name']); ?></strong></td>
                                        <td class="phone-number"><?php echo htmlspecialchars($registration['phone']); ?></td>
                                        <td class="email"><?php echo $registration['email'] ? htmlspecialchars($registration['email']) : 'غير مُحدد'; ?></td>
                                        <td><span class="city"><?php echo htmlspecialchars($registration['city']); ?></span></td>
                                        <td class="date"><?php echo date('d/m/Y H:i', strtotime($registration['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>" class="page-btn">
                                    <i class="fas fa-chevron-right"></i> السابق
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>" class="page-btn <?php echo $i == $page ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?>" class="page-btn">
                                    التالي <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
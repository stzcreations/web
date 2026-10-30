<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$perPage = 9;
$search = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$filetype = trim($_GET['filetype'] ?? '');
$sort = $_GET['sort'] ?? 'latest';
$page = max(1, (int)($_GET['page'] ?? 1));

$where = ["p.status='published'"];
$params = [];
if ($search !== '') {
    $where[] = "(p.title LIKE ? OR p.short_description LIKE ? OR p.description LIKE ?)";
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like);
}
if ($category !== '') {
    $where[] = "c.slug = ?";
    $params[] = $category;
}
if ($filetype !== '') {
    $where[] = "p.file_type = ?";
    $params[] = $filetype;
}
$order = "p.created_at DESC";
if ($sort === 'popular') $order = "p.download_count DESC";
elseif ($sort === 'updated') $order = "p.updated_at DESC";

$sqlWhere = implode(' AND ', $where);
$countStmt = db()->prepare("SELECT COUNT(*) FROM projects p LEFT JOIN categories c ON p.category_id=c.id WHERE $sqlWhere");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

$pg = paginate($total, $perPage, $page);
$stmt = db()->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM projects p LEFT JOIN categories c ON p.category_id=c.id WHERE $sqlWhere ORDER BY $order LIMIT ? OFFSET ?");
$i = 1;
foreach ($params as $v) { $stmt->bindValue($i++, $v); }
$stmt->bindValue($i++, $pg['perPage'], PDO::PARAM_INT);
$stmt->bindValue($i++, $pg['offset'], PDO::PARAM_INT);
$stmt->execute();
$projects = $stmt->fetchAll();

$cats = db()->query("SELECT * FROM categories WHERE status='active' ORDER BY name")->fetchAll();
$types = db()->query("SELECT DISTINCT file_type FROM projects WHERE status='published' ORDER BY file_type")->fetchAll(PDO::FETCH_COLUMN);

if (!empty($_GET['ajax'])) {
    ob_start();
    foreach ($projects as $p) include __DIR__ . '/includes/project_card.php';
    $html = ob_get_clean();
    header('Content-Type: application/json');
    echo json_encode(['html'=>$html,'total'=>$total,'pages'=>$pg['pages'],'current'=>$pg['current']]);
    exit;
}

$pageTitle = 'Browse Projects';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container">
  <h2 class="section-title mt-4">Browse <span>Projects</span></h2>

  <form id="filterForm" class="row g-2 mb-4" method="get" action="<?php echo base_url('browse'); ?>">
    <div class="col-md-3">
      <input type="text" name="q" class="form-control" placeholder="Search..." value="<?php echo e($search); ?>">
    </div>
    <div class="col-md-2">
      <select name="category" class="form-select">
        <option value="">All Categories</option>
        <?php foreach ($cats as $c): ?>
        <option value="<?php echo e($c['slug']); ?>" <?php if ($category===$c['slug']) echo 'selected'; ?>><?php echo e($c['name']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="filetype" class="form-select">
        <option value="">All Types</option>
        <?php foreach ($types as $t): ?>
        <option value="<?php echo e($t); ?>" <?php if ($filetype===$t) echo 'selected'; ?>><?php echo e($t); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="sort" class="form-select">
        <option value="latest" <?php if ($sort==='latest') echo 'selected'; ?>>Latest first</option>
        <option value="popular" <?php if ($sort==='popular') echo 'selected'; ?>>Most popular</option>
        <option value="updated" <?php if ($sort==='updated') echo 'selected'; ?>>Recently updated</option>
      </select>
    </div>
    <div class="col-md-3">
      <button class="btn btn-glow w-100" type="submit">Apply Filters</button>
    </div>
  </form>

  <div id="projectGrid" class="row">
    <?php foreach ($projects as $p): include __DIR__ . '/includes/project_card.php'; endforeach; ?>
    <?php if (empty($projects)): ?><p class="text-muted">No projects found.</p><?php endif; ?>
  </div>

  <nav class="mt-4">
    <ul class="pagination justify-content-center">
      <?php for ($i=1; $i<=$pg['pages']; $i++): ?>
      <li class="page-item <?php if ($i==$pg['current']) echo 'active'; ?>">
        <a class="page-link" href="<?php echo base_url('browse?' . http_build_query(array_merge($_GET, ['page'=>$i]))); ?>"><?php echo $i; ?></a>
      </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

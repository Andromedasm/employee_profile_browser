<?php
// 导入数据库连接信息
include 'conn.php';

// 生成分支选项标签
function fetchBranchOptions(PDOStatement $stmtBranches, string $selectedBranch): string
{
    $stmtBranches->execute();
    $branches = $stmtBranches->fetchAll(PDO::FETCH_ASSOC);
    $options = '';
    foreach ($branches as $branch) {
        $selected = ($branch['branch'] == $selectedBranch) ? 'selected' : '';
        $branchName = htmlspecialchars($branch['branch'], ENT_QUOTES, 'UTF-8');
        $options .= "<option value=\"{$branchName}\" {$selected}>{$branchName}</option>";
    }
    return $options;
}

// 定义一个函数，用于绑定 SQL 查询中的参数
function bindParameters($stmt, $parameters)
{
    // 遍历所有参数，绑定到 SQL 查询中
    foreach ($parameters as $key => $value) {
        // PDO 参数索引从1开始，数组索引从0开始，所以我们需要把索引值加1
        $stmt->bindValue($key + 1, $value);
    }
}

// 定义一个函数，用于根据用户的选择建立 WHERE 子句
function buildWhereClause(&$parameters, $selected_company, $selected_branch, $user_role, $user_branch)
{
    $where_clause = '';

    // 如果用户选择了特定的分支，将其添加到 WHERE 子句中
    if ($selected_branch !== '' && $selected_branch !== 'All') {
        $where_clause = "WHERE branch = ?";
        $parameters[] = $selected_branch;
    } else {
        // 如果用户没有选择特定的分支，或者选择的是 "All"，那么再看 selected_company
        if ($selected_company !== '' && $selected_company !== 'All') {
            $where_clause = "WHERE company = ?";
            $parameters[] = $selected_company;
        }

        // 如果用户角色不是管理员（即他们只能看到他们自己分支的信息），将其分支添加到 WHERE 子句中
        if ($user_role !== 'admin') {
            $branch_placeholders = implode(',', array_fill(0, count($user_branch), '?'));
            $where_clause .= $where_clause === '' ? "WHERE branch IN ($branch_placeholders)" : " AND branch IN ($branch_placeholders)";
            $parameters = array_merge($parameters, $user_branch);
        }
    }

    return $where_clause;
}

// 获取用户角色，公司，和分支
$user_role = $_SESSION['role'];
$user_company = $_SESSION['company'];
$user_branch = $_SESSION['branch'];

// 初始化 SQL 查询参数
$parameters = [];

// 获取用户选择的公司和分支，如果用户没有选择，就默认为所有公司和分支
$selected_company = ($user_role === 'admin') ? ($_GET['company'] ?? '') : $user_company;

// 检查 $_GET['company'] 是否设置并且不等于 ''
if (isset($_GET['company']) && $_GET['company'] !== '') {
    // 判断是否发生了公司变化
    $companyChanged = !isset($_SESSION['selected_company']) || $_GET['company'] !== $_SESSION['selected_company'];

    $_SESSION['selected_company'] = $_GET['company'];

    if ($companyChanged) {
        // 当公司变化时，清空 selected_branch
        $selected_branch = '';
        $_SESSION['selected_branch'] = $selected_branch;
    } else {
        $selected_branch = $_GET['branch'] ?? $_SESSION['selected_branch'] ?? '';
        $_SESSION['selected_branch'] = $selected_branch;
    }
} else {
    $selected_branch = $_GET['branch'] ?? $_SESSION['selected_branch'] ?? '';
    $_SESSION['selected_branch'] = $selected_branch;
}

// Debugging code
/*echo "Session company: " . $_SESSION['selected_company'];
echo "GET company: " . $_GET['company'];
echo "Selected branch: " . $selected_branch;*/


if (is_array($selected_branch)) {
    $selected_branch = $selected_branch[0];
}


// 获取用户选择的页面和每页显示的记录数，如果用户没有选择，就使用默认值
$limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT, ['options' => ['default' => 5, 'min_range' => 1, 'max_range' => 100]]);
$current_page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$offset = ($current_page - 1) * $limit;

// 建立 WHERE 子句
$where_clause = buildWhereClause($parameters, $selected_company, $selected_branch, $user_role, $user_branch);

try {
    // 准备 SQL 查询，获取总的记录数
    $stmt = $connection->prepare("SELECT COUNT(*) as total_rows FROM employee $where_clause");
    // 绑定 SQL 查询中的参数
    bindParameters($stmt, $parameters);
    // 执行 SQL 查询
    $stmt->execute();
    // 获取总的记录数
    $total_rows = $stmt->fetch(PDO::FETCH_ASSOC)['total_rows'];

    // 计算总的页面数
    $total_pages = ceil($total_rows / $limit);

    // 准备 SQL 查询，获取当前页面的记录
    // 准备 SQL 查询，获取当前页面的记录
    $stmt = $connection->prepare("SELECT * FROM employee $where_clause ORDER BY position_rank ASC, start_date ASC, employee_id ASC LIMIT ? OFFSET ?");
    // 添加额外的参数：每页显示的记录数和偏移量
    $parameters[] = $limit;
    $parameters[] = $offset;
    // 绑定 SQL 查询中的参数
    bindParameters($stmt, $parameters);
    // 执行 SQL 查询
    $stmt->execute();
    // 获取当前页面的记录
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // 如果出现异常，适当地处理它
    die("Database error: " . $e->getMessage());
}

// 如果用户是管理员，获取他们选择的公司的所有分支
if ($user_role === 'admin') {
    // 创建 SQL 查询，获取所有分支
    $queryBranches = "SELECT branch_name as branch FROM branches WHERE company = :company";
} else {
    // 如果用户不是管理员，获取他们所属的分支
    $queryBranches = "SELECT branch FROM user_branches WHERE user_id = :user_id";
}

// 准备 SQL 查询
$stmtBranches = $connection->prepare($queryBranches);

if ($user_role === 'admin') {
    // 如果用户是管理员，绑定他们选择的公司
    $stmtBranches->bindParam(':company', $selected_company);
} else {
    // 如果用户不是管理员，绑定他们的用户 ID
    $stmtBranches->bindParam(':user_id', $_SESSION['user_id']);
}

// 执行 SQL 查询
$stmtBranches->execute();
// 获取分支信息
$branches = $stmtBranches->fetchAll(PDO::FETCH_ASSOC);

// Debugging code
//echo "<pre>";
//echo "Selected Branch: " . $selected_branch . PHP_EOL;
//echo "Where Clause: " . $where_clause . PHP_EOL;
//echo "Parameters: " . print_r($parameters, true) . PHP_EOL;
//echo "Rows: " . print_r($rows, true) . PHP_EOL;
//echo "</pre>";


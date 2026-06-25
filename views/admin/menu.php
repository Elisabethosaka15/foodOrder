<?php

require_once 'helpers/Auth.php';
require_once 'controllers/MenuController.php';

Auth::admin();

$menuController = new MenuController();
$message = null;
$messageClass = 'alert-success';
$oldInput = [];

function oldValue($key)
{
    global $oldInput, $editMenu;

    if (isset($oldInput[$key])) {
        return htmlspecialchars($oldInput[$key]);
    }

    return htmlspecialchars($editMenu[$key] ?? '');
}

function selectedOld($key, $value)
{
    global $oldInput, $editMenu;

    if (isset($oldInput[$key])) {
        return $oldInput[$key] === $value ? 'selected' : '';
    }

    return isset($editMenu[$key]) && $editMenu[$key] === $value ? 'selected' : '';
}

function uploadMenuPhoto($inputName)
{
    if (!isset($_FILES[$inputName]) || empty($_FILES[$inputName]['name'])) {
        return null;
    }

    if ($_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $allowedMime = [
        'image/jpeg',
        'image/jpg',
        'image/png',
    ];

    if (!in_array($_FILES[$inputName]['type'], $allowedMime)) {
        return false;
    }

    if ($_FILES[$inputName]['size'] > 2 * 1024 * 1024) {
        return false;
    }

    $uploadDir = __DIR__ . '/../../uploads/menu';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $extension = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
    $filename = uniqid('menu_', true) . '.' . $extension;
    $destination = $uploadDir . '/' . $filename;

    if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $destination)) {
        return $filename;
    }

    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldInput = $_POST;
}

if (isset($_POST['create_menu'])) {
    $fotoUpload = uploadMenuPhoto('foto');

    if ($fotoUpload === false || $fotoUpload === null) {
        $message = 'Silakan unggah foto menu dalam format JPEG atau PNG (maks. 2MB).';
        $messageClass = 'alert-error';
    } else {
        $data = [
            'nama_menu' => trim($_POST['nama_menu']),
            'deskripsi' => trim($_POST['deskripsi']),
            'kategori' => trim($_POST['kategori']),
            'harga' => (float) $_POST['harga'],
            'stok' => (int) ($_POST['stok'] ?? 0),
            'foto' => $fotoUpload,
        ];

        if ($menuController->createMenu($data)) {
            $message = 'Menu berhasil ditambahkan.';
            $messageClass = 'alert-success';
            $oldInput = [];
        } else {
            $message = 'Gagal menambahkan menu. Periksa kembali data Anda.';
            $messageClass = 'alert-error';
            $uploadedFile = __DIR__ . '/../../uploads/menu/' . $fotoUpload;
            if (file_exists($uploadedFile)) {
                unlink($uploadedFile);
            }
        }
    }
}

if (isset($_POST['update_menu'])) {
    $menuId = (int) $_POST['menu_id'];
    $currentMenu = $menuController->getMenuById($menuId);
    $fotoUpload = uploadMenuPhoto('foto');
    $newFoto = $fotoUpload !== null ? $fotoUpload : ($currentMenu['foto'] ?? null);

    if ($fotoUpload === false) {
        $message = 'Gagal mengunggah foto. Pastikan file berformat gambar dan maksimal 2MB.';
        $messageClass = 'alert-error';
    } else {
        $data = [
            'nama_menu' => trim($_POST['nama_menu']),
            'deskripsi' => trim($_POST['deskripsi']),
            'kategori' => trim($_POST['kategori']),
            'harga' => (float) $_POST['harga'],
            'stok' => (int) ($_POST['stok'] ?? 0),
            'foto' => $newFoto,
        ];

        if ($menuController->updateMenu($menuId, $data)) {
            if ($fotoUpload !== null && !empty($currentMenu['foto'])) {
                $oldFile = __DIR__ . '/../../uploads/menu/' . $currentMenu['foto'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $message = 'Menu berhasil diperbarui.';
            $messageClass = 'alert-success';
            $oldInput = [];
            $editMenu = $menuController->getMenuById($menuId);
        } else {
            if ($fotoUpload !== null) {
                $newFile = __DIR__ . '/../../uploads/menu/' . $fotoUpload;
                if (file_exists($newFile)) {
                    unlink($newFile);
                }
            }

            $message = 'Gagal memperbarui menu. Periksa kembali data Anda.';
            $messageClass = 'alert-error';
        }
    }
}

if (isset($_POST['delete_menu'])) {
    $menuId = (int) $_POST['menu_id'];
    $currentMenu = $menuController->getMenuById($menuId);

    if ($menuController->deleteMenu($menuId)) {
        if (!empty($currentMenu['foto'])) {
            $oldFile = __DIR__ . '/../../uploads/menu/' . $currentMenu['foto'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        $message = 'Menu berhasil dihapus.';
        $messageClass = 'alert-success';
    } else {
        $message = 'Gagal menghapus menu.';
        $messageClass = 'alert-error';
    }
}

$menuList = $menuController->getMenu();
$editMenu = null;

if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $editMenu = $menuController->getMenuById($editId);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: #f8fafc;
            color: #334155;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .container {
            width: min(1200px, calc(100% - 40px));
            margin: 0 auto;
            padding: 20px 0;
        }

        .page-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 1.9rem;
            margin: 0;
        }

        .btn-primary {
            background: #e11d48;
            color: #fff;
            border: 0;
            padding: 12px 18px;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 10px 30px -20px rgba(15, 23, 42, 0.15);
            padding: 24px;
        }

        .form-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #0f172a;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 14px 16px;
            background: #f8fafc;
            color: #0f172a;
            width: 100%;
            font-size: 0.95rem;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .alert {
            border-radius: 16px;
            padding: 16px 18px;
            border: 1px solid #dbeafe;
            background: #eff6ff;
            color: #1d4ed8;
            margin-bottom: 18px;
        }

        .alert-error {
            background: #fbe9e7;
            border-color: #f9c2bd;
            color: #b91c1c;
        }

        .admin-layout {
            display: grid;
            gap: 24px;
            grid-template-columns: 1fr;
        }

        @media (min-width: 1100px) {
            .admin-layout {
                grid-template-columns: 420px minmax(0, 1fr);
            }
        }

        .form-panel {
            padding: 24px;
        }

        .list-panel {
            padding: 0;
        }

        .table-responsive {
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        th,
        td {
            padding: 16px 14px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
        }

        td>form {
            display: inline-flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn-sm {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 12px;
            border: 0;
            cursor: pointer;
        }

        .btn-edit {
            background: #0284c7;
            color: #fff;
        }

        .btn-delete {
            background: #d94675;
            color: #fff;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
        }

        .badge-category {
            background: #eef2ff;
            color: #4338ca;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert <?= htmlspecialchars($messageClass) ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="page-header">
            <div>
                <h1>Kelola Menu</h1>
            </div>
            <a href="index.php?page=admin" class="btn-primary" style="background:#334155;"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>

        <div class="admin-layout">
            <div class="card form-panel">
                <h2 style="margin-top:0;"><?php echo $editMenu ? 'Sunting Menu' : 'Tambah Menu Baru'; ?></h2>
                <form method="post" enctype="multipart/form-data" class="form-grid">
                    <div class="form-group">
                        <label>Nama Menu</label>
                        <input type="text" name="nama_menu" value="<?= oldValue('nama_menu') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" required>
                            <option value="Makanan" <?= selectedOld('kategori', 'Makanan') ?>>Makanan</option>
                            <option value="Minuman" <?= selectedOld('kategori', 'Minuman') ?>>Minuman</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" min="0" step="1000" value="<?= oldValue('harga') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" min="0" step="1" value="<?= oldValue('stok') ?: 0 ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Foto Menu</label>
                        <input type="file" name="foto" accept="image/*">
                    </div>
                    <?php if (!empty($editMenu['foto'])): ?>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Preview Foto Saat Ini</label>
                            <img src="uploads/menu/<?= htmlspecialchars($editMenu['foto']) ?>" alt="Foto Menu" style="max-width: 220px; border-radius: 16px; border: 1px solid #cbd5e1;">
                        </div>
                    <?php endif; ?>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" required><?= oldValue('deskripsi') ?></textarea>
                    </div>
                    <?php if ($editMenu): ?>
                        <input type="hidden" name="menu_id" value="<?= htmlspecialchars($editMenu['id']) ?>">

                        <div style="grid-column: span 2; text-align: center;">
                            <button type="submit" name="update_menu" class="btn-primary"
                                style=" width: 200px; display: flex; justify-content: center; align-items: center; ">
                                Perbarui Menu
                            </button>
                        </div>

                    <?php else: ?>
                        <button type="submit" name="create_menu" class="btn-primary"
                                style=" width: 200px; display: flex; justify-content: center; align-items: center; ">
                                Tambahkan menu
                            </button>
                    <?php endif; ?>
                </form>
            </div>

            <div class="card list-panel">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nama Menu</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1 ?>
                            <?php while ($row = $menuList->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $no++ ?></td>
                                    <td>
                                        <?php if (!empty($row['foto'])): ?>
                                            <img src="uploads/menu/<?= htmlspecialchars($row['foto']) ?>" alt="Foto <?= htmlspecialchars($row['nama_menu']) ?>" style="width:75px; height:75px; object-fit:cover; border-radius:12px; border:1px solid #e2e8f0;">
                                        <?php else: ?>
                                            <span style="color:#94a3b8;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['nama_menu']) ?></td>
                                    <td><span class="badge badge-category"><?= htmlspecialchars($row['kategori']) ?></span></td>
                                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($row['stok']) ?></td>
                                    <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                                    <td>
                                        <a href="index.php?page=admin_menu&edit=<?= htmlspecialchars($row['id']) ?>" class="btn-sm btn-edit"><i class="fa-solid fa-pen"></i> Edit</a>
                                        <form method="post" onsubmit="return confirm('Hapus menu ini?');">
                                            <input type="hidden" name="menu_id" value="<?= htmlspecialchars($row['id']) ?>">
                                            <button type="submit" name="delete_menu" class="btn-sm btn-delete"><i class="fa-solid fa-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
</body>

</html>
<?php
require_once __DIR__ . "/../init.php";
ob_start();
$categories = fetchAllFromTable($conn, 'category');
$modules = fetchAllFromTable($conn, 'module');

$user = checkAuth('Admin');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/dist/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Modules and Categories</title>
</head>

<body class="pt-24">
    <div>
        <?php include "templates/navbar.php"; ?>
    </div>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Category list</h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Category Number</th>
                        <th class="px-4 py-2 text-left">Category</th>
                        <th class="px-4 py-2 text-left">Category Description</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <?php foreach ($categories as $cat): ?>
                    <tbody class="divide-y divide-gray-200">
                        <tr class="border-b hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 font-mono text-blue-600 uppercase">
                                <?= htmlspecialchars($cat['cat_id']) ?>
                            </td>
                            <td class="px-4 py-2 font-mono text-blue-600 uppercase">
                                <?= htmlspecialchars($cat['category']) ?>
                            </td>

                            <td class="px-4 py-2 font-mono text-blue-600 uppercase">
                                <?= htmlspecialchars($cat['cat_desc']) ?>
                            </td>
                            <div class="flex items-center justify-center gap-4">
                                <td class="px-4 py-2 text-left">
                                    <button
                                        onclick="openEditCategoryModal('<?= $cat['cat_id'] ?>', '<?= addslashes($cat['category']) ?>', '<?= addslashes($cat['cat_desc'] ?? '') ?>')"
                                        class="text-xs font-bold uppercase tracking-wider text-cyan-600 hover:text-cyan-800 transition-colors">
                                        Edit
                                    </button>
                                </td>
                            </div>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Module list</h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Module Number</th>
                        <th class="px-4 py-2 text-left">Module</th>
                        <th class="px-4 py-2 text-left">Module Description</th>
                        <th class="px-4 py-2 text-left">Action</th>

                    </tr>
                </thead>
                <?php foreach ($modules as $mod): ?>
                    <tbody class="divide-y divide-gray-200">
                        <tr class="border-b hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 font-mono text-blue-600 uppercase">
                                <?= htmlspecialchars($mod['mod_id']) ?>
                            </td>
                            <td class="px-4 py-2 font-mono text-blue-600 uppercase">
                                <?= htmlspecialchars($mod['module']) ?>
                            </td>

                            <td class="px-4 py-2 font-mono text-blue-600 uppercase">
                                <?= htmlspecialchars($mod['mod_desc']) ?>
                            </td>
                            <div class="flex items-center justify-center gap-4">
                                <td class="px-4 py-2 text-left">
                                    <button
                                        onclick="openEditModuleModal('<?= $mod['mod_id'] ?>', '<?= addslashes($mod['module']) ?>', '<?= addslashes($mod['mod_desc'] ?? '') ?>')"
                                        class="text-xs font-bold uppercase tracking-wider text-cyan-600 hover:text-cyan-800 transition-colors">
                                        Edit
                                    </button>
                                </td>
                            </div>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <!-- Add new Category Form -->
    <div class="container mx-auto p-6">
        <h1>Add new Category</h1>
        <div>
            <form action="../controllers/add_category.php" method="POST" class="space-y-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category Code</label>
                    <input type="text" name="category" id="category" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                </div>
                <div>
                    <label for="cat_desc" class="block text-sm font-medium text-gray-700">Category Description</label>
                    <textarea name="cat_desc" id="cat_desc" rows="3" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"></textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-black rounded-md hover:bg-blue-700">Add
                    Category</button>
            </form>
        </div>
    </div>
    <!-- Add new Module Form -->
    <div class="container mx-auto p-6">
        <h1>Add new Module</h1>
        <div>
            <form action="../controllers/add_module.php" method="POST" class="space-y-4">
                <div>
                    <label for="module" class="block text-sm font-medium text-gray-700">Module Code</label>
                    <input type="text" name="module" id="module" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                </div>
                <div>
                    <label for="mod_desc" class="block text-sm font-medium text-gray-700">Module Description</label>
                    <textarea name="mod_desc" id="mod_desc" rows="3" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"></textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-red rounded-md hover:bg-blue-700">Add
                    Module</button>
            </form>
        </div>
    </div>

    <!-- EDIT CATEGORY -->
    <div id="editCategoryModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div id="editModalContent"
            class="bg-white p-6 rounded-lg w-96 transform transition-all duration-300 opacity-0 scale-95">
            <h2 class="text-xl mb-4 font-bold">Edit Category</h2>

            <form id="editForm" action="../controllers/edit_category.php" method="POST">
                <input type="hidden" name="cat_id" id="edit_cat_id">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" name="edit_category" id="edit_category_input" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <input type="text" name="edit_cat_desc" id="edit_cat_desc_input" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeEditCategoryModal()"
                        class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded transition">Cancel</button>
                    <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-black px-4 py-2 rounded transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODULE -->
    <div id="editModuleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div id="editModalContent"
            class="bg-white p-6 rounded-lg w-96 transform transition-all duration-300 opacity-0 scale-95">
            <h2 class="text-xl mb-4 font-bold">Edit Module</h2>

            <form id="editForm" action="../controllers/edit_module.php" method="POST">
                <input type="hidden" name="module_id" id="edit_module_id">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Module Name</label>
                    <input type="text" name="edit_module_name" id="edit_module_name_input" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <input type="text" name="edit_module_desc" id="edit_module_desc_input" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeEditModuleModal()"
                        class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded transition">Cancel</button>
                    <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-black px-4 py-2 rounded transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
<script src="js/categories_module.js"></script>

</html>
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

<body class="pt-24 bg-gray-50 text-slate-900">
    <div>
        <?php include "templates/navbar.php"; ?>
        <div id="validationBlock" class="fixed top-28 right-5 z-[100] flex flex-col gap-3 pointer-events-none">
            <div class="pointer-events-auto">
                <?= showValidation() ?>
            </div>
        </div>
    </div>

    <div class="container mx-auto p-6">

        <div class="container mx-auto p-6">


            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">

                    <!-- ✅ UPDATED CATEGORY HEADER -->
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Categories</h2>
                        <p class="text-slate-500 text-sm">Monitor and manage Categories.</p>
                    </div>

                    <div class="relative w-full md:w-72 lg:w-80" data-tooltip="Search categories">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>

                        <input type="text" id="categorySearch" onkeyup="filterTable('categorySearch', 'categoryTable')"
                            placeholder="Search category..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm
    focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none
    shadow-sm hover:shadow-md transition-all">


                    </div>
                </div>

                <button onclick="openGenericModal('addCategoryModal', 'addCategoryContainer')"
                    data-tooltip="Add new category"
                    class="hidden md:flex items-center justify-center gap-x-2 whitespace-nowrap flex-shrink-0 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-5 h-10 rounded-xl font-semibold text-sm transition-all shadow-lg shadow-blue-600/20">
                    <i class="fa-solid fa-plus"></i>
                    <span>New Category</span>
                </button>
            </div>


            <div class="overflow-hidden bg-white rounded-2xl shadow-md border border-slate-100 mb-12">
                <table id="categoryTable" class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-blue-600 text-white border-b border-blue-700">
                            <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">Code
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">
                                Description</th>
                            <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($categories as $cat): ?>
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4 text-sm font-semibold text-slate-700">
                                    <span
                                        class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded text-sm uppercase"
                                        data-tooltip="Category code">
                                        <?= htmlspecialchars($cat['cat_id']) ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm font-semibold text-slate-700">
                                    <?= htmlspecialchars($cat['category']) ?>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-500 max-w-[250px] truncate">
                                    <?= htmlspecialchars($cat['cat_desc']) ?>
                                </td>

                                <td class="px-4 py-3">
                                    <button
                                        onclick="openEditCategoryModal('<?= $cat['cat_id'] ?>', '<?= addslashes($cat['category']) ?>', '<?= addslashes($cat['cat_desc'] ?? '') ?>')"
                                        data-tooltip="Edit category"
                                        class="hidden md:flex items-center gap-x-1.5 bg-blue-600 hover:bg-blue-700 text-white px-3 h-8 rounded-lg font-medium text-xs transition-all shadow-sm">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Edit</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">

                    <!-- ✅ UPDATED MODULE HEADER -->
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Modules</h2>
                        <p class="text-slate-500 text-sm">Monitor and manage Modules.</p>
                    </div>

                    <div class="relative w-full md:w-72" data-tooltip="Search modules">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="moduleSearch" onkeyup="filterTable('moduleSearch', 'moduleTable')"
                            placeholder="Search module code or name..."
                            class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:border-blue-500 outline-none transition-all shadow-sm">
                    </div>
                </div>

                <button onclick="openGenericModal('addModuleModal', 'addModuleContainer')" data-tooltip="Add new module"
                    class="hidden md:flex items-center justify-center gap-x-2 whitespace-nowrap flex-shrink-0 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-5 h-10 rounded-xl font-semibold text-sm transition-all shadow-lg shadow-blue-600/20">
                    <i class="fa-solid fa-plus"></i>
                    <span>New Module</span>
                </button>
            </div>

            <div class="overflow-hidden bg-white rounded-2xl shadow-md border border-slate-100 mb-12">
                <table id="moduleTable" class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-blue-600 text-white border-b border-blue-700">
                            <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">Code
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">Module
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">
                                Description</th>
                            <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($modules as $mod): ?>
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <span
                                        class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded text-sm uppercase"
                                        data-tooltip="Module code">
                                        <?= htmlspecialchars($mod['mod_id']) ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm font-semibold text-slate-700">
                                    <?= htmlspecialchars($mod['module']) ?>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-500 max-w-[250px] truncate">
                                    <?= htmlspecialchars($mod['mod_desc']) ?>
                                </td>

                                <td class="px-4 py-3">
                                    <button
                                        onclick="openEditModuleModal('<?= $mod['mod_id'] ?>', '<?= addslashes($mod['module']) ?>', '<?= addslashes($mod['mod_desc'] ?? '') ?>')"
                                        data-tooltip="Edit module"
                                        class="hidden md:flex items-center gap-x-1.5 bg-blue-600 hover:bg-blue-700 text-white px-3 h-8 rounded-lg font-medium text-xs transition-all shadow-sm">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Edit</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


    </div>

</body>

<div id="addCategoryModal"
    class="hidden fixed inset-0 z-[250] flex items-center justify-center p-4 backdrop-blur-md transition-all duration-300">
    <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"
        onclick="closeGenericModal('addCategoryModal', 'addCategoryContainer')"></div>
    <div id="addCategoryContainer"
        class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden z-10 transform scale-95 opacity-0 transition-all duration-300 ease-out">
        <div class="bg-blue-600 px-6 py-5 flex justify-between items-center text-white">
            <h2 class="text-xl font-bold">New Category</h2>
            <button onclick="closeGenericModal('addCategoryModal', 'addCategoryContainer')"
                class="hover:text-gray-200"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="../controllers/add_category.php" method="POST" class="p-6 space-y-4">
            <div>
                <label class="text-[13px] font-semibold text-slate-600 ml-1">Category Code</label>
                <input type="text" name="category" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-all">
            </div>
            <div>
                <label class="text-[13px] font-semibold text-slate-600 ml-1">Description</label>
                <textarea name="cat_desc" rows="3" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-all resize-none"></textarea>
            </div>
            <div style="display: flex; gap: 12px; padding-top: 8px;">
                <button type="button" onclick="closeGenericModal('addCategoryModal', 'addCategoryContainer')"
                    style="flex: 1; padding: 12px 0; background-color: #10b981; color: white; font-weight: bold; border: none; border-radius: 16px; cursor: pointer; transition: background-color 0.2s;"
                    onmouseover="this.style.backgroundColor='#059669'"
                    onmouseout="this.style.backgroundColor='#10b981'">
                    Close
                </button>

                <button type="submit"
                    style="flex: 2; padding: 12px 0; background-color: #2563eb; color: white; font-weight: bold; border: none; border-radius: 16px; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'"
                    onmousedown="this.style.transform='scale(0.95)'" onmouseup="this.style.transform='scale(1)'">
                    Add Category
                </button>
            </div>
        </form>
    </div>
</div>

<div id="addModuleModal"
    class="hidden fixed inset-0 z-[250] flex items-center justify-center p-4 backdrop-blur-md transition-all duration-300">
    <div class="absolute inset-0 bg-slate-900/60" onclick="closeGenericModal('addModuleModal', 'addModuleContainer')">
    </div>
    <div id="addModuleContainer"
        class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden z-10 transform scale-95 opacity-0 transition-all duration-300 ease-out">
        <div class="bg-blue-600 px-6 py-5 flex justify-between items-center text-white">
            <h2 class="text-xl font-bold">New Module</h2>
            <button onclick="closeGenericModal('addModuleModal', 'addModuleContainer')"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="../controllers/add_module.php" method="POST" class="p-6 space-y-4">
            <div>
                <label class="text-[13px] font-semibold text-slate-600 ml-1">Module Code</label>
                <input type="text" name="module" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-all">
            </div>
            <div>
                <label class="text-[13px] font-semibold text-slate-600 ml-1">Description</label>
                <textarea name="mod_desc" rows="3" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-all resize-none"></textarea>
            </div>
            <div style="display: flex; gap: 12px; padding-top: 8px;">
                <button type="button" onclick="closeGenericModal('addModuleModal', 'addModuleContainer')"
                    style="flex: 1; padding: 12px 0; background-color: #10b981; color: white; font-weight: bold; border: none; border-radius: 16px; cursor: pointer; transition: background-color 0.2s;"
                    onmouseover="this.style.backgroundColor='#059669'"
                    onmouseout="this.style.backgroundColor='#10b981'">
                    Close
                </button>

                <button type="submit"
                    style="flex: 2; padding: 12px 0; background-color: #2563eb; color: white; font-weight: bold; border: none; border-radius: 16px; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'"
                    onmousedown="this.style.transform='scale(0.95)'" onmouseup="this.style.transform='scale(1)'">
                    Add Module
                </button>
            </div>
        </form>
    </div>
</div>

<div id="editCategoryModal"
    class="hidden fixed inset-0 z-[250] flex items-center justify-center p-4 backdrop-blur-md transition-all duration-300">
    <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300" onclick="closeEditCategoryModal()">
    </div>
    <div id="categoryModalContent"
        class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden z-10 transform scale-95 opacity-0 transition-all duration-300 ease-out">
        <div class="bg-blue-600 px-6 py-5 flex justify-between items-center text-white">
            <h2 class="text-xl font-bold">Edit Category</h2>
            <button onclick="closeEditCategoryModal()" class="hover:text-gray-200"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="editForm" action="../controllers/edit_category.php" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="cat_id" id="edit_cat_id">
            <div>
                <label class="text-[13px] font-semibold text-slate-600 ml-1">Category Name</label>
                <input type="text" name="edit_category" id="edit_category_input" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-all">
            </div>
            <div>
                <label class="text-[13px] font-semibold text-slate-600 ml-1">Description</label>
                <input type="text" name="edit_cat_desc" id="edit_cat_desc_input" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-all">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditCategoryModal()"
                    class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-xl transition">Cancel</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition">Save
                    Changes</button>
            </div>
        </form>
    </div>
</div>

<div id="editModuleModal"
    class="hidden fixed inset-0 z-[250] flex items-center justify-center p-4 backdrop-blur-md transition-all duration-300">
    <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300" onclick="closeEditModuleModal()">
    </div>
    <div id="moduleModalContent"
        class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden z-10 transform scale-95 opacity-0 transition-all duration-300 ease-out">
        <div class="bg-blue-600 px-6 py-5 flex justify-between items-center text-white">
            <h2 class="text-xl font-bold">Edit Module</h2>
            <button onclick="closeEditModuleModal()" class="hover:text-gray-200"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="editForm" action="../controllers/edit_module.php" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="module_id" id="edit_module_id">
            <div>
                <label class="text-[13px] font-semibold text-slate-600 ml-1">Module Name</label>
                <input type="text" name="edit_module_name" id="edit_module_name_input" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-all">
            </div>
            <div>
                <label class="text-[13px] font-semibold text-slate-600 ml-1">Description</label>
                <input type="text" name="edit_module_desc" id="edit_module_desc_input" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-all">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModuleModal()"
                    class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-xl transition">Cancel</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition">Save
                    Changes</button>
            </div>
        </form>
    </div>
</div>
<div id="tooltip"
    class="fixed pointer-events-none opacity-0 transition-opacity duration-200 z-50 px-3 py-1.5 text-sm font-medium text-white bg-slate-900 rounded shadow-lg whitespace-nowrap">
</div>
</body>
<?php ob_end_flush(); ?>
<script src="js/removeNotification.js" defer></script>
<script src="js/categories_module.js"></script>
<script src="js/tooltip.js"></script>

</html>
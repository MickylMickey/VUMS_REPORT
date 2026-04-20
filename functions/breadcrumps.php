<?php
/**
 * Breadcrumb Component
 * Usage:
 * include 'breadcrumb.php';
 * renderBreadcrumb(['Home' => 'index.php', 'Users' => 'users.php', 'Edit User' => 'edit.php']);
 */

function renderBreadcrumb(array $items)
{
    if (empty($items))
        return;

    echo '<nav class="flex text-sm text-gray-600 space-x-2">';

    $lastKey = array_key_last($items);
    foreach ($items as $label => $url) {
        if ($url === '#' || $url === '' || $label === $lastKey) {
            // Last item = active
            echo '<span class="font-semibold text-gray-900">' . htmlspecialchars($label) . '</span>';
        } else {
            echo '<a href="' . htmlspecialchars($url) . '" class="hover:underline">' . htmlspecialchars($label) . '</a>';
            echo '<span>/</span>';
        }
    }

    echo '</nav>';
}
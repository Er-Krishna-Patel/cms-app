<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItems;
use Illuminate\Support\Facades\DB;

class MenuItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('menu_items')->truncate();

        $menuItems = [
            // Dashboard
            [
                'title' => 'Dashboard',
                'icon' => '🏠',
                'route' => 'dashboard',
                'position' => 1,
                'type' => 'system',
                'is_active' => true,
            ],

            // Content Section
            [
                'title' => 'Posts',
                'icon' => '📝',
                'route' => 'admin.posts.index',
                'position' => 10,
                'type' => 'content',
                'is_active' => true,
            ],
            [
                'title' => 'Categories',
                'icon' => '📂',
                'route' => 'admin.categories.index',
                'position' => 20,
                'type' => 'content',
                'is_active' => true,
            ],
            [
                'title' => 'Tags',
                'icon' => '🏷️',
                'route' => 'admin.tags.index',
                'position' => 30,
                'type' => 'content',
                'is_active' => true,
            ],
            [
                'title' => 'Media Library',
                'icon' => '🖼️',
                'route' => 'admin.media.index',
                'position' => 40,
                'type' => 'content',
                'is_active' => true,
            ],

            // Admin Section
            [
                'title' => 'Users',
                'icon' => '👤',
                'route' => 'admin.users.index',
                'position' => 50,
                'type' => 'admin',
                'permission' => 'admin',
                'is_active' => true,
            ],
            [
                'title' => 'Menu Manager',
                'icon' => '☰',
                'url' => '#',
                'position' => 60,
                'type' => 'admin',
                'permission' => 'admin',
                'is_active' => false,
            ],
            [
                'title' => 'Settings',
                'icon' => '⚙️',
                'route' => 'admin.settings.index',
                'position' => 70,
                'type' => 'admin',
                'permission' => 'admin',
                'is_active' => true,
            ],
        ];

        foreach ($menuItems as $item) {
            MenuItems::create($item);
        }
    }
}


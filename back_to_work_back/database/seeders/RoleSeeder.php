<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $roleAdmin = Role::create(['name' => 'admin']);
        $roleUser = Role::create(['name' => 'user']);

        // Ads
        $createAdPermission = Permission::create(['name' => 'create adds']);
        $readAdPermission = Permission::create(['name' => 'read adds']);
        $updateAdPermission = Permission::create(['name' => 'update adds']);
        $deleteAdPermission = Permission::create(['name' => 'delete adds']);

        // Chats
        $createChatPermission = Permission::create(['name' => 'create chats']);
        $readChatPermission = Permission::create(['name' => 'read chats']);
        $updateChatPermission = Permission::create(['name' => 'update chats']);
        $deleteChatPermission = Permission::create(['name' => 'delete chats']);
        $deleteFullChatPermission = Permission::create(['name' => 'delete full chats']);

        // Users
        $createUserPermission = Permission::create(['name' => 'create users']);
        $readUserPermission = Permission::create(['name' => 'read users']);
        $updateUserPermission = Permission::create(['name' => 'update users']);
        $deleteUserPermission = Permission::create(['name' => 'delete users']);

        // Offers
        $createOfferPermission = Permission::create(['name' => 'create offers']);
        $readOfferPermission = Permission::create(['name' => 'read offers']);
        $updateOfferPermission = Permission::create(['name' => 'update offers']);
        $deleteOfferPermission = Permission::create(['name' => 'delete offers']);

        // Categories
        $createCategoryPermission = Permission::create(['name' => 'create categories']);
        $readCategoryPermission = Permission::create(['name' => 'read categories']);
        $updateCategoryPermission = Permission::create(['name' => 'update categories']);
        $deleteCategoryPermission = Permission::create(['name' => 'delete categories']);

        $roleAdmin->givePermissionTo(
            $createAdPermission, $readAdPermission, $updateAdPermission, $deleteAdPermission,
            $createUserPermission, $readUserPermission, $updateUserPermission, $deleteUserPermission,
            $createOfferPermission, $readOfferPermission, $updateOfferPermission, $deleteOfferPermission,
            $createCategoryPermission, $readCategoryPermission, $updateCategoryPermission, $deleteCategoryPermission,
            $createChatPermission, $readChatPermission, $updateChatPermission, $deleteChatPermission, $deleteFullChatPermission
        );

        $roleUser->givePermissionTo(
            $readUserPermission, $readCategoryPermission,
            $createAddPermission, $readAddPermission, $updateAddPermission, $deleteAddPermission,
            $createOfferPermission, $readOfferPermission, $updateOfferPermission, $deleteOfferPermission,
            $createChatPermission, $readChatPermission, $updateChatPermission, $deleteChatPermission,
        );
    }
}

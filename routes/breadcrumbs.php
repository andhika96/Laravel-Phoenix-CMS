<?php // routes/breadcrumbs.php

use App\Models\Awesome_Admin\Account;

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Awesome Admin
Breadcrumbs::for('awesome_admin', function (BreadcrumbTrail $trail) 
{
	$trail->push(t('Awesome Admin'), route('cms.admin.awesome_admin'));
});

// Awesome Admin > Config
Breadcrumbs::for('awesome_admin.config', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Site Settings'), route('cms.admin.awesome_admin.config'));
});

// Awesome Admin > Modules
Breadcrumbs::for('awesome_admin.modules', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Manage Users'), route('cms.admin.awesome_admin.user'));
});

// Awesome Admin > Menus
Breadcrumbs::for('awesome_admin.menu', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Menus'), route('cms.admin.awesome_admin.menu'));
});

// Awesome Admin > Menus > Backend
Breadcrumbs::for('awesome_admin.menu.be', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Menus'), route('cms.admin.awesome_admin.menu'));
	$trail->push(t('Backend'), route('cms.admin.awesome_admin.menu.be'));
});

// Awesome Admin > Menus > Backend > Category Menu
Breadcrumbs::for('awesome_admin.menu.be.category_menu', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Menus'), route('cms.admin.awesome_admin.menu'));
	$trail->push(t('Backend'), route('cms.admin.awesome_admin.menu.be'));
	$trail->push(t('Category Menus'), route('cms.admin.awesome_admin.menu.be.category_menu'));
});

// Awesome Admin > Menus > Backend > Parent Menus
Breadcrumbs::for('awesome_admin.menu.be.parent_menu', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Menus'), route('cms.admin.awesome_admin.menu'));
	$trail->push(t('Backend'), route('cms.admin.awesome_admin.menu.be'));
	$trail->push(t('Parent Menus'), route('cms.admin.awesome_admin.menu.be.parent_menu'));
});

// Awesome Admin > Menus > Backend > Submenus
Breadcrumbs::for('awesome_admin.menu.be.submenu', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Menus'), route('cms.admin.awesome_admin.menu'));
	$trail->push(t('Backend'), route('cms.admin.awesome_admin.menu.be'));
	$trail->push(t('Submenus'), route('cms.admin.awesome_admin.menu.be.submenu'));
});

// Awesome Admin > Menus > Backend > Submenus > Detail
Breadcrumbs::for('awesome_admin.menu.be.submenu.detail', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Menus'), route('cms.admin.awesome_admin.menu'));
	$trail->push(t('Backend'), route('cms.admin.awesome_admin.menu.be'));
	$trail->push(t('Submenus'), route('cms.admin.awesome_admin.menu.be.submenu'));
	$trail->push(t('Detail'), route('cms.admin.awesome_admin.menu.be.submenu.detail', 'idOrSlug'));
});

// Awesome Admin > Menus > Frontend
Breadcrumbs::for('awesome_admin.menu.fe', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Menus'), route('cms.admin.awesome_admin.menu'));
	$trail->push(t('Frontend'), route('cms.admin.awesome_admin.menu.fe'));
});

// Awesome Admin > Menus > Frontend > Category Menu
Breadcrumbs::for('awesome_admin.menu.fe.category_menu', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Menus'), route('cms.admin.awesome_admin.menu'));
	$trail->push(t('Frontend'), route('cms.admin.awesome_admin.menu.fe'));
	$trail->push(t('Category Menus'), route('cms.admin.awesome_admin.menu.fe.category_menu'));
});

// Awesome Admin > Menus > Frontend > Parent Menu
Breadcrumbs::for('awesome_admin.menu.fe.parent_menu', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Menus'), route('cms.admin.awesome_admin.menu'));
	$trail->push(t('Frontend'), route('cms.admin.awesome_admin.menu.fe'));
	$trail->push(t('Parent Menus'), route('cms.admin.awesome_admin.menu.fe.parent_menu'));
});

// Awesome Admin > Menus > Frontend > Submenu
Breadcrumbs::for('awesome_admin.menu.fe.submenu', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Menus'), route('cms.admin.awesome_admin.menu'));
	$trail->push(t('Frontend'), route('cms.admin.awesome_admin.menu.fe'));
	$trail->push(t('Submenus'), route('cms.admin.awesome_admin.menu.fe.submenu'));
});

// Awesome Admin > Menus > Frontend > Submenu > Detail
Breadcrumbs::for('awesome_admin.menu.fe.submenu.detail', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Menus'), route('cms.admin.awesome_admin.menu'));
	$trail->push(t('Frontend'), route('cms.admin.awesome_admin.menu.fe'));
	$trail->push(t('Submenus'), route('cms.admin.awesome_admin.menu.fe.submenu'));
	$trail->push(t('Detail'), route('cms.admin.awesome_admin.menu.fe.submenu.detail', 'idOrSlug'));
});


// Awesome Admin > User
Breadcrumbs::for('awesome_admin.user', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Manage Users'), route('cms.admin.awesome_admin.user'));
});

// Awesome Admin > User > User Edit
Breadcrumbs::for('awesome_admin.user.edit', function (BreadcrumbTrail $trail, Account $account) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Manage Users'), route('cms.admin.awesome_admin.user'));
	$trail->push(t('Edit'), route('cms.admin.awesome_admin.user.edit', 'idOrSlug'));
});

// Awesome Admin > Role > Create
Breadcrumbs::for('awesome_admin.role.create', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Manage Roles'), route('cms.admin.awesome_admin.role'));
	$trail->push(t('Create'), route('cms.admin.awesome_admin.role.createV2'));
});

// Awesome Admin > Role > Edit
Breadcrumbs::for('awesome_admin.role.edit', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Manage Roles'), route('cms.admin.awesome_admin.role'));
	$trail->push(t('Edit'), route('cms.admin.awesome_admin.role.editV2', 'idOrSlug'));
});

// Awesome Admin > Role
Breadcrumbs::for('awesome_admin.role', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Manage Roles'), route('cms.admin.awesome_admin.role'));
});

// Awesome Admin > Permission
Breadcrumbs::for('awesome_admin.permission', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Manage Permissions'), route('cms.admin.awesome_admin.permission'));
});

// Awesome Admin > SMTP
Breadcrumbs::for('awesome_admin.smtp', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('STMP Settings'), route('cms.admin.awesome_admin.smtp'));
});

// Awesome Admin > Language
Breadcrumbs::for('awesome_admin.language', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Manage Languages'), route('cms.admin.awesome_admin.language'));
});

// Awesome Admin > Language > Unstranslated
Breadcrumbs::for('awesome_admin.language.untranslated', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Manage Languages'), route('cms.admin.awesome_admin.language'));
	$trail->push(t('Untranslated'), route('cms.admin.awesome_admin.language.untranslated'));
});

// Awesome Admin > Language > Translated
Breadcrumbs::for('awesome_admin.language.translated', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Manage Languages'), route('cms.admin.awesome_admin.language'));
	$trail->push(t('Translated'), route('cms.admin.awesome_admin.language.translated'));
});

// Awesome Admin > Appearance
Breadcrumbs::for('awesome_admin.appearance', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Manage Appearance'), route('cms.admin.awesome_admin.appearance'));
});

// Awesome Admin > Themes
Breadcrumbs::for('awesome_admin.themes', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('Manage Themes'), route('cms.admin.awesome_admin.themes'));
});

// Awesome Admin > File Manager
Breadcrumbs::for('awesome_admin.file_manager', function (BreadcrumbTrail $trail) 
{
	$trail->parent('awesome_admin');
	$trail->push(t('File Manager'), route('cms.admin.awesome_admin.file_manager'));
});

// Blog
Breadcrumbs::for('manage_article', function (BreadcrumbTrail $trail) 
{
	$trail->push(t('Manage Article'), route('cms.core.manage_article'));
});

// Blog > Add
Breadcrumbs::for('manage_article.add', function (BreadcrumbTrail $trail) 
{
	$trail->parent('manage_article');
	$trail->push(t('Add Post'), route('cms.core.manage_article.add'));
});

// Awesome Admin > Role > Edit
Breadcrumbs::for('manage_article.edit', function (BreadcrumbTrail $trail) 
{
	$trail->parent('manage_article');
	$trail->push(t('Edit Post'), route('cms.core.manage_article.edit', 'idOrSlug'));
});

// Blog
Breadcrumbs::for('manage_coverimage', function (BreadcrumbTrail $trail) 
{
	$trail->push(t('Manage Cover Image'), route('cms.core.manage_coverimage'));
});

// Blog > Add
Breadcrumbs::for('manage_coverimage.add', function (BreadcrumbTrail $trail) 
{
	$trail->parent('manage_coverimage');
	$trail->push(t('Add Cover Image'), route('cms.core.manage_coverimage.add'));
});

// Awesome Admin > Role > Edit
Breadcrumbs::for('manage_coverimage.edit', function (BreadcrumbTrail $trail) 
{
	$trail->parent('manage_coverimage');
	$trail->push(t('Edit Cover Image'), route('cms.core.manage_coverimage.edit', 'idOrSlug'));
});

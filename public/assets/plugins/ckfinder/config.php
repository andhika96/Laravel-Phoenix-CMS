<?php

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

$ckfinderAllowedRoles = ['Super Admin', 'Administrator', 'General Member'];
$ckfinderUserScope = isset($_SESSION['CKFinder_UserRole_UUID'])
    && is_string($_SESSION['CKFinder_UserRole_UUID'])
    && preg_match('/\A[A-Za-z0-9_-]+\z/D', $_SESSION['CKFinder_UserRole_UUID'])
        ? $_SESSION['CKFinder_UserRole_UUID']
        : '__unauthenticated__';

/*
 * CKFinder Configuration File
 *
 * For the official documentation visit https://ckeditor.com/docs/ckfinder/ckfinder3-php/
 */

/*============================ PHP Error Reporting ====================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/debugging.html

// Production
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// ini_set('display_errors', 0);

// Development
error_reporting(E_ALL & ~E_DEPRECATED);

ini_set('display_errors', 0);

/*============================ General Settings =======================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html

$config = array();

/**
 * Important for environments without symlink support (e.g. exFAT portable SSD):
 * force CKFinder local backend root to Laravel storage/app/public/ckfinder
 * so it does not depend on public/storage symlink.
 */
$ckfinderStorageRoot = rtrim(str_replace('public\\assets\\plugins\\ckfinder', '', __DIR__), "\\/")
    . DIRECTORY_SEPARATOR . 'storage'
    . DIRECTORY_SEPARATOR . 'app'
    . DIRECTORY_SEPARATOR . 'public'
    . DIRECTORY_SEPARATOR . 'ckfinder';

/*============================ Enable PHP Connector HERE ==============================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_authentication

$config['authentication'] = static function () use ($ckfinderAllowedRoles): bool {
    $userId = $_SESSION['CKFinder_UserId'] ?? null;
    $scope = $_SESSION['CKFinder_UserRole_UUID'] ?? null;
    $role = $_SESSION['CKFinder_UserRole'] ?? null;
    $expiresAt = $_SESSION['CKFinder_AuthExpiresAt'] ?? null;

    return is_scalar($userId)
        && trim((string) $userId) !== ''
        && is_string($scope)
        && preg_match('/\A[A-Za-z0-9_-]+\z/D', $scope) === 1
        && is_string($role)
        && in_array($role, $ckfinderAllowedRoles, true)
        && is_int($expiresAt)
        && $expiresAt >= time();
};

/*============================ License Key ============================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_licenseKey

$config['licenseName'] = 'localhost';
$config['licenseKey']  = '*7?9-*1**-S**J-*S**-*2**-4*G*-2**D';

/*============================ Images and Thumbnails ==================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_images

$config['images'] = array(
    'maxWidth'  => 1600,
    'maxHeight' => 1200,
    'quality'   => 80,
    'sizes' => array(
        'small'  => array('width' => 480, 'height' => 320, 'quality' => 85),
        'medium' => array('width' => 600, 'height' => 480, 'quality' => 85),
        'large'  => array('width' => 1024, 'height' => 720, 'quality' => 80)
    )
);

/*=================================== Backends ========================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_backends

$config['defaultBackend'] = 'default';

// Local
$config['backends'][] = array(
    'name'                  => 'default',
    'adapter'               => 'local',
    'baseUrl'               => '/storage/ckfinder/',
    'root'                  => $ckfinderStorageRoot,
    'chmodFiles'            => 0644,
    'chmodFolders'          => 0755,
    'filesystemEncoding'    => 'UTF-8',
);

/*============================ CKFinder Internal Directory ============================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_privateDir

$config['privateDir'] = array(
    'backend'   => $config['defaultBackend'],
    'tags'      => '.ckfinder/tags',
    'logs'      => '.ckfinder/logs',
    'cache'     => '.ckfinder/cache',
    'thumbs'    => '.ckfinder/cache/thumbs',
);

/*================================ Resource Types =====================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_resourceTypes

$config['defaultResourceTypes'] = '';

$config['resourceTypes'][] = array(
    'name'              => 'All User Files', // Single quotes not allowed.
    'directory'         => 'userfiles',
    'maxSize'           => 0,
    'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,webp,wma,wmv,xls,xlsx,zip',
    'deniedExtensions'  => '',
    'backend'           => $config['defaultBackend']
);

$config['resourceTypes'][] = array(
    'name'              => 'User Files', // Single quotes not allowed.
    'directory'         => 'userfiles/'.$ckfinderUserScope,
    'maxSize'           => 0,
    'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,webp,wma,wmv,xls,xlsx,zip',
    'deniedExtensions'  => '',
    'backend'           => $config['defaultBackend']
);

$config['resourceTypes'][] = array(
    'name'              => 'Articles', // Single quotes not allowed.
    'directory'         => 'articles',
    'maxSize'           => 0,
    'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,webp,wma,wmv,xls,xlsx,zip',
    'deniedExtensions'  => '',
    'backend'           => $config['defaultBackend']
);

$config['resourceTypes'][] = array(
    'name'              => 'Events', // Single quotes not allowed.
    'directory'         => 'events',
    'maxSize'           => 0,
    'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,webp,wma,wmv,xls,xlsx,zip',
    'deniedExtensions'  => '',
    'backend'           => $config['defaultBackend']
);

$config['resourceTypes'][] = array(
    'name'              => 'Promotions', // Single quotes not allowed.
    'directory'         => 'promotions',
    'maxSize'           => 0,
    'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,webp,wma,wmv,xls,xlsx,zip',
    'deniedExtensions'  => '',
    'backend'           => $config['defaultBackend']
);

/*================================ Access Control =====================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_roleSessionVar
$config['roleSessionVar'] = 'CKFinder_UserRole';

// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_accessControl
$config['accessControl'][] = array(
    'role'                => 'Super Admin',
    'resourceType'        => 'All User Files',
    'folder'              => '/',

    'FOLDER_VIEW'         => true,
    'FOLDER_CREATE'       => true,
    'FOLDER_RENAME'       => true,
    'FOLDER_DELETE'       => true,

    'FILE_VIEW'           => true,
    'FILE_CREATE'         => true,
    'FILE_RENAME'         => true,
    'FILE_DELETE'         => true,

    'IMAGE_RESIZE'        => true,
    'IMAGE_RESIZE_CUSTOM' => true
);

$config['accessControl'][] = array(
    'role'                => 'Super Admin',
    'resourceType'        => 'Articles',
    'folder'              => '/',

    'FOLDER_VIEW'         => true,
    'FOLDER_CREATE'       => true,
    'FOLDER_RENAME'       => true,
    'FOLDER_DELETE'       => true,

    'FILE_VIEW'           => true,
    'FILE_CREATE'         => true,
    'FILE_RENAME'         => true,
    'FILE_DELETE'         => true,

    'IMAGE_RESIZE'        => true,
    'IMAGE_RESIZE_CUSTOM' => true
);

$config['accessControl'][] = array(
    'role'                => 'Super Admin',
    'resourceType'        => 'Events',
    'folder'              => '/',

    'FOLDER_VIEW'         => true,
    'FOLDER_CREATE'       => true,
    'FOLDER_RENAME'       => true,
    'FOLDER_DELETE'       => true,

    'FILE_VIEW'           => true,
    'FILE_CREATE'         => true,
    'FILE_RENAME'         => true,
    'FILE_DELETE'         => true,

    'IMAGE_RESIZE'        => true,
    'IMAGE_RESIZE_CUSTOM' => true
);

$config['accessControl'][] = array(
    'role'                => 'Super Admin',
    'resourceType'        => 'Promotions',
    'folder'              => '/',

    'FOLDER_VIEW'         => true,
    'FOLDER_CREATE'       => true,
    'FOLDER_RENAME'       => true,
    'FOLDER_DELETE'       => true,

    'FILE_VIEW'           => true,
    'FILE_CREATE'         => true,
    'FILE_RENAME'         => true,
    'FILE_DELETE'         => true,

    'IMAGE_RESIZE'        => true,
    'IMAGE_RESIZE_CUSTOM' => true
);

$config['accessControl'][] = array(
    'role'                => 'Administrator',
    'resourceType'        => 'All User Files',
    'folder'              => '/',

    'FOLDER_VIEW'         => true,
    'FOLDER_CREATE'       => true,
    'FOLDER_RENAME'       => true,
    'FOLDER_DELETE'       => true,

    'FILE_VIEW'           => true,
    'FILE_CREATE'         => true,
    'FILE_RENAME'         => true,
    'FILE_DELETE'         => true,

    'IMAGE_RESIZE'        => true,
    'IMAGE_RESIZE_CUSTOM' => true
);

$config['accessControl'][] = array(
    'role'                => 'Administrator',
    'resourceType'        => 'Articles',
    'folder'              => '/',

    'FOLDER_VIEW'         => true,
    'FOLDER_CREATE'       => true,
    'FOLDER_RENAME'       => true,
    'FOLDER_DELETE'       => true,

    'FILE_VIEW'           => true,
    'FILE_CREATE'         => true,
    'FILE_RENAME'         => true,
    'FILE_DELETE'         => true,

    'IMAGE_RESIZE'        => true,
    'IMAGE_RESIZE_CUSTOM' => true
);

$config['accessControl'][] = array(
    'role'                => 'Administrator',
    'resourceType'        => 'Events',
    'folder'              => '/',

    'FOLDER_VIEW'         => true,
    'FOLDER_CREATE'       => true,
    'FOLDER_RENAME'       => true,
    'FOLDER_DELETE'       => true,

    'FILE_VIEW'           => true,
    'FILE_CREATE'         => true,
    'FILE_RENAME'         => true,
    'FILE_DELETE'         => true,

    'IMAGE_RESIZE'        => true,
    'IMAGE_RESIZE_CUSTOM' => true
);

$config['accessControl'][] = array(
    'role'                => 'Administrator',
    'resourceType'        => 'Promotions',
    'folder'              => '/',

    'FOLDER_VIEW'         => true,
    'FOLDER_CREATE'       => true,
    'FOLDER_RENAME'       => true,
    'FOLDER_DELETE'       => true,

    'FILE_VIEW'           => true,
    'FILE_CREATE'         => true,
    'FILE_RENAME'         => true,
    'FILE_DELETE'         => true,

    'IMAGE_RESIZE'        => true,
    'IMAGE_RESIZE_CUSTOM' => true
);

$config['accessControl'][] = array(
    'role'                => 'General Member',
    'resourceType'        => 'User Files',
    'folder'              => '/',

    'FOLDER_VIEW'         => true,
    'FOLDER_CREATE'       => true,
    'FOLDER_RENAME'       => true,
    'FOLDER_DELETE'       => true,

    'FILE_VIEW'           => true,
    'FILE_CREATE'         => true,
    'FILE_RENAME'         => true,
    'FILE_DELETE'         => true,

    'IMAGE_RESIZE'        => true,
    'IMAGE_RESIZE_CUSTOM' => true
);

/*================================ Other Settings =====================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html

$config['overwriteOnUpload'] = false;
$config['checkDoubleExtension'] = true;
$config['disallowUnsafeCharacters'] = true;
$config['secureImageUploads'] = true;
$config['checkSizeAfterScaling'] = true;
$config['htmlExtensions'] = array('html', 'htm', 'xml', 'js');
$config['hideFolders'] = array('.*', 'CVS', '__thumbs');
$config['hideFiles'] = array('.*');
$config['forceAscii'] = false;
$config['xSendfile'] = false;

// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_debug
$config['debug'] = false;

/*==================================== Plugins ========================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_plugins

$config['pluginsDirectory'] = __DIR__ . '/plugins';
$config['plugins'] = array();

/*================================ Cache settings =====================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_cache

$config['cache'] = array(
    'imagePreview' => 24 * 3600,
    'thumbnails'   => 24 * 3600 * 365,
    'proxyCommand' => 3600
);

/*============================ Temp Directory settings ================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_tempDirectory

$config['tempDirectory'] = sys_get_temp_dir();

/*============================ Session Cause Performance Issues =======================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_sessionWriteClose

$config['sessionWriteClose'] = true;

/*================================= CSRF protection ===================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_csrfProtection

$config['csrfProtection'] = true;

/*===================================== Headers =======================================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_headers

$config['headers'] = array();

/*============================== End of Configuration =================================*/

// Config must be returned - do not change it.
return $config;

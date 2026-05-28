<?php

function getLaravelUser()
{
    define('LARAVEL_START', microtime(true));

    $getDir = str_replace("public\assets\plugins\ckfinder", "", __DIR__);

    // Register the Composer autoloader...
    require $getDir.'/vendor/autoload.php';

    // Bootstrap Laravel and handle the request...
    $app = require_once $getDir.'/bootstrap/app.php';
    $app->handle(Illuminate\Http\Request::capture());

    $id = $app['encrypter']->decryptString($_COOKIE[$app['config']['session.cookie']]);
    $app['session']->driver()->setId($id);
    $app['session']->driver()->start();

    // return $app;
    return $app['auth']->user();
    // return $app['session']->get('CKFinder_UserRole');
}

session_start();

// use Illuminate\Support\Facades\DB;

// $_SESSION['CKFinder_UserRole'] = getLaravelUser()->roles[0]['name'];

// print_r(getLaravelUser()->roles[0]['name']);
// print_r(getLaravelUser()->uuid);

// print_r(getLaravelUser()['db']->table('accounts')->get());

// print_r(getLaravelUser()['filesystem']->allFiles('public/fonts'));

// foreach (getLaravelUser()['filesystem']->disk('local')->allFiles('storage') as $key => $value) 
// {
//  echo $value;
// }

// exit;

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

/*============================ Enable PHP Connector HERE ==============================*/
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_authentication

$config['authentication'] = function() 
{
    return true;
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
    // 'root'               => '', // Can be used to explicitly set the CKFinder user files directory.
    'chmodFiles'            => 0777,
    'chmodFolders'          => 0755,
    'filesystemEncoding'    => 'UTF-8',
);

// AWS
$config['backends'][] = array(
    'name'          => 'aws0',
    'adapter'       => 's3',
    'bucket'        => 'mgmotorid-assets',
    'visibility'    => 'public',
    'region'        => 'ap-southeast-3',
    'key'           => 'AKIAZJDUNPZ7CYOS6Y7O',
    'secret'        => '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',
    'root'          => 'contents/ckfinder',
    'baseUrl'       => 'https://mgmotorid-assets.s3.ap-southeast-3.amazonaws.com/contents/ckfinder/',
);

// IDCloudHost
$config['backends'][] = array(
    'name'              => 'aws',
    'adapter'           => 's3compatible',
    'bucket'            => 'arunadevs3',
    'visibility'        => 'public',
    'region'            => 'ap-southeast-3',
    'key'               => '7HB226KS01NTA8W0TKI1',
    'secret'            => 'zknPppPnPNSoaUumuWNIEIlOwAa2OOwcdMdq5V9m',
    'root'              => 'ckfinder',
    'baseUrl'           => 'https://is3.cloudhost.id/arunadevs3/ckfinder/',
    'endPoint'          => 'https://is3.cloudhost.id/',
    'useProxyCommand'   => false
);

// DigitalOcean Spaces
$config['backends'][] = array(
    'name'              => 'digitalocean',
    'adapter'           => 's3compatible',
    'bucket'            => 'arunadevs3',
    'visibility'        => 'public',
    'region'            => 'ap-southeast-3',
    'key'               => 'DO00AWE2NHGLR9YXVK9E',
    'secret'            => 'in8U5UvQqpWT9J9XDGEsQSMWTN2myaV0XpTxT2F+aK4',
    'root'              => 'ckfinder',
    'baseUrl'           => 'https://sgp1.digitaloceanspaces.com/arunadevs3/ckfinder/',
    'endPoint'          => 'https://sgp1.digitaloceanspaces.com',
    'useProxyCommand'   => false
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
    'directory'         => 'userfiles/'.$_SESSION['CKFinder_UserRole_UUID'],
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

$config['accessControl'][] = array(
    'role'                => 'General Member',
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
    'role'                => 'General Member',
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
$config['debug'] = true;

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
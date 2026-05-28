<?php

function replace_string_in_file($filename, $string_to_replace, $replace_with)
{
	$content=file_get_contents($filename);
	$content_chunks=explode($string_to_replace, $content);
	$content=implode($replace_with, $content_chunks);
	file_put_contents($filename, $content);
}

$oldCode = 
"Route::name('cms.core.')
	->prefix('account')
	->namespace('App\Http\Controllers\Web\Account')
	->group(function() 
	{
		Route::controller(\Account_Controller::class)->group(function()
		{
			Route::get('/', 'index')->name('account')->middleware('auth', 'checkSuspended', 'permission:read data');
			Route::get('/general', 'general')->name('account.general')->middleware('auth', 'checkSuspended', 'permission:edit data');
			Route::post('/general2', 'update')->name('account.update2')->middleware('auth', 'checkSuspended', 'permission:edit data');
			Route::post('/', 'update')->name('account.update')->middleware('auth', 'checkSuspended', 'permission:edit data');
		});
	});";


$prefix = 'account_2_account_2';

$newCode =
"Route::name('cms.core.')
	->prefix('".$prefix."')
	->namespace('App\Http\Controllers\Web3\KWKWKWKWKWKWKWK')
	->group(function() 
	{
		Route::controller(\Account_Controller::class)->group(function()
		{
			Route::get('/', 'index')->name('account')->middleware('auth', 'checkSuspended', 'permission:read data');
			Route::get('/general', 'general')->name('account.general')->middleware('auth', 'checkSuspended', 'permission:edit data');
			Route::post('/general2', 'update')->name('account.update2')->middleware('auth', 'checkSuspended', 'permission:edit data');
			Route::post('/', 'update')->name('account.update')->middleware('auth', 'checkSuspended', 'permission:edit data');
		});
	});";

// replace_string_in_file('testing.php', "echo 'kwkwkwkwkwkwk';", "echo 'hahahahahaha';");
replace_string_in_file('testing.php', $oldCode, $newCode);
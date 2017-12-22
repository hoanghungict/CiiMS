<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* This is the main configuration file for CiiMS. Options for CiiMS extend from this base
* configuration file, and should be written to protected/config/main.php. They will override _any_
* configuration variable listed in here via CMap::mergeArray()
*
* The reason for doing this is to reduce the number of options written directly to the main.php file,
* so that we're only writing out what is _needed_ in that config file. Additionally, if we want to
* introduce new functionality in the future, we can safely add it to this config file without having
* to make sure the end user adds it to their config file. The point is to make the main.php config file
* a write once file so the end user never has to deal with it after the install.
*
* @package    CiiMS Content Management System
* @author     Charles R. Portwood II <charlesportwoodii@ethreal.net>
* @copyright  Charles R. Portwood II <https://www.erianna.com> 2012-2014
* @license    http://opensource.org/licenses/MIT  MIT LICENSE
* @link       https://github.com/charlesportwoodii/CiiMS
*/
$import = function($default=false) {

	$modules = (require __DIR__ . DS . 'modules.php');

	if ($default === true)
		return $modules;

	$m = array(
		'application.models.*',
		'application.models.forms.*',
		'application.models.settings.*'
	);

	foreach ($modules as $k=>$v) {
		$m[] = 'application.modules.'.$v.'.*';
	}

	return $m;
};

$ciimsCoreConfig = array(
	'basePath' => __DIR__.DS.'..',
	'name' => NULL,
	'sourceLanguage' => 'en_US',
	'preload' => array(
		'cii',
		'analytics'
	),
	'import' => $import(),
	'modules' => $import(true),
	'behaviors' => array(
		'onBeginRequest' => array(
			'class' => 'vendor.charlesportwoodii.yii-newrelic.behaviors.YiiNewRelicWebAppBehavior',
		),
	),
	'components' => array(
		'themeManager' => array(
			'basePath' => (__DIR__ . DS . '..' . DS . '..' . DS . 'themes')
		),
		'messages' => array(
			'class' => 'vendor.charlesportwoodii.cii.components.CiiPHPMessageSource'
		),
			'newRelic' => array(
			'class' => 'vendor.charlesportwoodii.yii-newrelic.YiiNewRelic',
			'setAppNameToYiiName' => false
		),
		'cii' => array(
			'class' => 'vendor.charlesportwoodii.cii.components.CiiBase'
		),
		'analytics' => array(
			'class' => 'vendor.charlesportwoodii.cii.components.CiiAnalytics',
			'lowerBounceRate' => true,
			'options' => array(),
		),
		'assetManager' => array(
			'class' => 'vendor.charlesportwoodii.cii.components.CiiAssetManager',
		),
		'clientScript' => array(
			'class' => 'vendor.charlesportwoodii.cii.components.CiiClientScript',
		),
		'errorHandler' => array(
			'errorAction' => 'site/error',
		),
		'session' => array(
			'autoStart'     => true,
			'sessionName'   => '_ciims',
			'cookieMode'    => 'only',
			'cookieParams'  => array(
				'httponly' => true,
				'secure' => (
					(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
					(!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || 
					(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
				)
			)
		),
		'urlManager' => array(
			'class'          => 'vendor.charlesportwoodii.cii.components.CiiURLManager',
			'urlFormat'      => 'path',
			'showScriptName' => false
		),
		'user' => array(
			'authTimeout'    		=> 900,
			'absoluteAuthTimeout' 	=> 1900,
			'autoRenewCookie' 		=> true
		),
		'db' => array(
			'class'                 => 'CDbConnection',
			'connectionString'      => NULL,
			'emulatePrepare'        => true,
			'username'              => NULL,
			'password'              => NULL,
			'charset'               => 'utf8',
			'schemaCachingDuration' => 3600,
			'enableProfiling'       => false,
			'enableParamLogging'    => false
		),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				)
			)
		),
		'cache' => array(
			'class' => 'CFileCache',
		)
	),
	'params' => array(
		'encryptionKey'       => NULL,
		'debug'               => false,
		'trace'               => 0,
		'NewRelicAppName'     => null,
		'max_fileupload_size' => (10 * 1024 * 1024),
		'cards' => 'https://cards.ciims.io/1.0.0',
	)
);

// CLI specific data
if (php_sapi_name() == "cli")
{
	$ciimsCoreConfig['behaviors'] = array(
		'onBeginRequest' => array(
			'class' => 'vendor.charlesportwoodii.yii-newrelic.behaviors.YiiNewRelicConsoleAppBehavior',
		),
		'onEndRequest' => array(
			'class' => 'vendor.charlesportwoodii.yii-newrelic.behaviors.YiiNewRelicConsoleAppBehavior',
		)
	);
}

if (php_sapi_name() != "cli" && YII_DEBUG)
{
	$ciimsCoreConfig['preload'][] = 'debug';
	$ciimsCoreConfig['components']['debug'] = array(
		'class' => 'vendor.zhuravljov.yii2-debug.Yii2Debug',
		'enabled' => YII_DEBUG,
		'allowedIPs' => array('*')
	);
}

return $ciimsCoreConfig;

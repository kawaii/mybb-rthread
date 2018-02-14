<?php

require_once MYBB_ROOT . 'inc/plugins/rthread/core.php';
require_once MYBB_ROOT . 'inc/plugins/rthread/hooks_frontend.php';

define('rthread\DEVELOPMENT_MODE', 0);

\rthread\addHooksNamespace('rthread\Hooks');

function rthread_info()
{
    global $lang;
    $lang->load('rthread');
    return [
        'name'          => 'Random Threads',
        'description'   => $lang->rthread_description,
        'website'       => 'https://cute.im/',
        'author'        => 'Kane \'kawaii\' Valentine',
        'authorsite'    => 'https://cute.im/',
        'version'       => '1.0',
        'compatibility' => '18*',
    ];
}

function rthread_install()
{
    global $db, $cache, $PL;
    rthread_admin_load_pluginlibrary();
    $settings = [
        'rthread_forums' => [
            'title'       => 'RThread Forum IDs',
            'description' => 'Forums in which the random thread button is displayed.',
            'optionscode' => 'forumselect',
            'value'       => '2',
        ],
        'rthread_days' => [
            'title'       => 'RThread Days',
            'description' => 'The number of past days we should select a random thread from.',
            'optionscode' => 'numeric',
            'value'       => '30',
        ],
        'rthread_discriminator' => [
            'title'       => 'RThread Discriminator',
            'description' => 'Select between dateline or lastpost random selection discrimination.',
            'optionscode' => 'select
dateline=dateline
lastpost=lastpost',
            'value'       => 'dateline',
        ],
    ];

    $PL->settings(
        'rthread',
        'Random Threads',
        'Settings for the Random Threads plugin.',
        $settings
    );
}

function rthread_uninstall()
{
    global $db, $cache, $PL;
    rthread_admin_load_pluginlibrary();
    $PL->settings_delete('rthread', true);
}

function rthread_is_installed()
{
    global $db;
    $query = $db->simple_select('settinggroups', 'gid', "name='rthread'");
    return (bool)$db->num_rows($query);
}

function rthread_activate()
{
    global $PL;
    rthread_admin_load_pluginlibrary();
    $templates = [];
    $directory = new DirectoryIterator(MYBB_ROOT . 'inc/plugins/rthread/templates');
    foreach ($directory as $file) {
        if (!$file->isDot() && !$file->isDir()) {
            $templateName = $file->getPathname();
            $templateName = basename($templateName, '.tpl');
            $templates[$templateName] = file_get_contents($file->getPathname());
        }
    }
    $PL->templates('rthread', 'Random Threads', $templates);
}

function rthread_deactivate()
{
    global $PL;
    rthread_admin_load_pluginlibrary();
    $PL->templates_delete('rthread', true);
}

function rthread_admin_load_pluginlibrary()
{
    global $lang;
    if (!defined('PLUGINLIBRARY')) {
        define('PLUGINLIBRARY', MYBB_ROOT . 'inc/plugins/pluginlibrary.php');
    }
    if (!file_exists(PLUGINLIBRARY)) {
        $lang->load('rthread');
        flash_message($lang->rthread_admin_pluginlibrary_missing, 'error');
        admin_redirect('index.php?module=config-plugins');
    } elseif (!$PL) {
        require_once PLUGINLIBRARY;
    }
}

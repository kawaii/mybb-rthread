<?php

namespace rthread\Hooks;

function global_start()
{
    \rthread\loadTemplates([
        'button',
    ], 'rthread_');
}

function misc_start()
{
    global $mybb, $db, $lang;

    $lang->load('rthread');

    if($mybb->input['action'] == 'rthread')
    {
        $fid = $mybb->get_input('fid', \MyBB::INPUT_INT);

        $rthread_forums = \rthread\getCsvSettingValues('rthread_forums');

        if(!in_array($fid, $rthread_forums))
        {
            error_no_permission();
        }

        $rthread_discriminator = \rthread\getSettingValue('rthread_discriminator');
        $rthread_days = \rthread\getSettingValue('rthread_days') * 86400;
        $greaterThan = TIME_NOW - $rthread_days;

        switch($db->type)
        {
            case "mysqli":
            case "sqlite":
               $query = $db->query("SELECT tid FROM " . TABLE_PREFIX . "threads WHERE fid={$fid} AND visible=1 AND {$rthread_discriminator} > {$greaterThan} ORDER BY RAND() LIMIT 1;");
               break;
            case "pgsql":
               $query = $db->query("SELECT tid FROM " . TABLE_PREFIX . "threads WHERE fid={$fid} AND visible=1 AND {$rthread_discriminator} > {$greaterThan} ORDER BY RANDOM() LIMIT 1;");
               break;
        }

        $tid = $db->fetch_field($query, 'tid');

        if($tid > 0)
        {
                redirect($mybb->settings['bburl'].'/'.get_thread_link($tid));
        }
        else
        {
                error($lang->rthread_not_found);
        }
    }
}

function forumdisplay_start()
{
    global $mybb, $lang, $rthread_button;

    $lang->load('rthread');

    $fid = $mybb->get_input('fid', \MyBB::INPUT_INT);

    $rthread_forums = \rthread\getCsvSettingValues('rthread_forums');

    if(in_array($fid, $rthread_forums))
    {
            eval('$rthread_button = "' . \rthread\tpl('button') . '";');
    }
    else
    {
            $rthread_button = null;
    }
}

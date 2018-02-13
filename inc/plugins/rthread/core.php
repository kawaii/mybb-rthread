<?php

function addHooks(array $hooks, string $namespace = null)
{
    global $plugins;

    if ($namespace) {
        $prefix = $namespace . '\\';
    } else {
        $prefix = null;
    }

    foreach ($hooks as $hook) {
        $plugins->add_hook($hook, $prefix . $hook);
    }
}

function addHooksNamespace(string $namespace)
{
    global $plugins;

    $namespaceFunctions = \rthread\getNamespaceFunctions($namespace);

    foreach ($namespaceFunctions as $functionName) {
        $plugins->add_hook($functionName, $namespace . '\\' . $functionName);
    }
}

function getNamespaceFunctions($namespace): array
{
    $units = get_defined_functions()['user'];

    return \rthread\filterUnitsByNamespace($units, $namespace, true);
}

function filterUnitsByNamespace(array $units, string $namespace, $lowercaseNamespace = false): array
{
    $names = [];

    if ($lowercaseNamespace) {
        $namespaceMatch = strtolower($namespace);
    } else {
        $namespaceMatch = $namespace;
    }

    $namespaceWithPrefixLength = strlen($namespaceMatch) + 1;

    foreach ($units as $unitName) {
        if (substr($unitName, 0, $namespaceWithPrefixLength) == $namespaceMatch . '\\') {
            $names[] = substr_replace($unitName, null, 0, $namespaceWithPrefixLength);
        }
    }

    return $names;
}

function getSettingValue(string $name)
{
    global $mybb;
    return $mybb->settings['rthread_' . $name] ?? null;
}

function getCsvSettingValues(string $name): array
{
    global $mybb;

    return array_filter(explode(',', getSettingValue($name)));
}

function loadTemplates(array $templates, string $prefix = null)
{
    global $templatelist;

    if (!empty($templatelist)) {
        $templatelist .= ',';
    }
    if ($prefix) {
        $templates = preg_filter('/^/', $prefix, $templates);
    }

    $templatelist .= implode(',', $templates);
}

function tpl(string $name)
{
    global $templates;

    $templateName = 'rthread_' . $name;
    $directory = MYBB_ROOT . 'inc/plugins/rthread/templates/';

    if (DEVELOPMENT_MODE) {
        return str_replace(
            "\\'",
            "'",
            addslashes(
                file_get_contents($directory . $name . '.tpl')
            )
        );
    } else {
        return $templates->get($templateName);
    }
}

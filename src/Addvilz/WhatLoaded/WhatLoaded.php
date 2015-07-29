<?php

namespace Addvilz\WhatLoaded;

/**
 * WhatLoaded is essentially a autoloader hook that listens for autoload calls and registers
 * all classes that is loaded via autoloader.
 *
 * It is later possible to analyze this data to get insight on dependencies and whatever other
 * data you might extract from this.
 *
 * @package Addvilz\WhatLoaded
 */
class WhatLoaded
{
    /**
     * @var array
     */
    private static $classes;

    /**
     * @param string $class
     * @return bool
     */
    public static function autoloadHook($class)
    {
        self::$classes[] = $class;

        return false;
    }

    /**
     * Register autoloader.
     */
    public static function start()
    {
        spl_autoload_register('WhatLoaded::autoloadHook', false, true);
    }

    /**
     * Register rendering on shutdown
     */
    public static function renderOnShutdown()
    {
        register_shutdown_function('WhatLoaded::render');
    }

    /**
     * Get collected data
     *
     * @return array
     *
     *      vendor name => [
     *          'count' => {number of packages}
     *          'packages' => [package name]
     *                          => {number of classes}
     *          'classes' => [class, class, class]
     *      ]
     */
    public static function collect()
    {
        $vendors = [];

        foreach (self::$classes as $className) {
            $class = str_replace('_', '\\', $className);

            if (substr_count($class, '\\') >= 1) {
                list($vendor, $package) = explode('\\', $class, 3);

                if (!isset($vendors[$vendor])) {
                    $vendors[$vendor] = [
                        'count' => 0,
                        'packages' => [],
                        'classes' => [],
                    ];
                }

                if (!isset($vendors[$vendor]['packages'][$package])) {
                    $vendors[$vendor]['packages'][$package] = 0;
                }

                $vendors[$vendor]['count']++;
                $vendors[$vendor]['packages'][$package]++;
                $vendors[$vendor]['classes'][] = $className;

                continue;
            }

            if (!isset($packages[$class])) {
                $vendors[$class] = [
                    'count' => 0,
                    'packages' => [],
                    'classes' => [],
                ];
            }

            $vendors[$class]['count']++;
            $vendors[$class]['classes'][] = $className;
        }

        return $vendors;
    }

    /**
     * Render the list of vendors and packages loaded via autoloader
     * @param bool $showClasses Render classes?
     */
    public static function render($showClasses = false)
    {
        $vendors = self::collect();

        $report = PHP_EOL.'============================================='.PHP_EOL;
        foreach ($vendors as $vendor => $packages) {
            $report .= sprintf('> %s (%d classes)', $vendor, $packages['count']).PHP_EOL;
            foreach ($packages['packages'] as $package => $count) {
                $report .= sprintf('    - %s (%d classes)', $package, $count).PHP_EOL;
            }

            if ($showClasses) {
                foreach ($packages['classes'] as $class) {
                    $report .= sprintf('    -: %s', $class).PHP_EOL;
                }
            }
        }
        $report .= PHP_EOL.'============================================='.PHP_EOL;

        echo $report;
    }
}

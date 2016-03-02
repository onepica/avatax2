<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category  OnePica
 * @package   OnePica_AvaTax16
 * @copyright Copyright (c) 2016 One Pica, Inc. (http://www.onepica.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Register autoload function
 */
spl_autoload_register(
    /**
     * Defines class loading search path
     *
     * @param string $className
     */
    function ($className) {
        $classPath = explode('\\', $className);
        if ($classPath[0] != 'OnePica') {
            return;
        }
        // Drop 'OnePica'
        $classPath = array_slice($classPath, 1);
        $filePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $classPath) . '.php';
        if (file_exists($filePath)) {
            require_once($filePath);
        }
    }
);

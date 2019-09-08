<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\App\Router;

use Magento\Framework\Module\Dir\Reader as ModuleReader;

class ActionList
{
    /**
     * Not allowed string in route's action path to avoid disclosing admin url
     */
    const NOT_ALLOWED_IN_NAMESPACE_PATH = 'adminhtml';

    /**
     * List of application actions
     *
     * @var array
     */
    protected $actions;

    /**
     * @var array
     */
    protected $reservedWords = [
        'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const',
        'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare',
        'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final',
        'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'instanceof',
        'insteadof','interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected',
        'public', 'require', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var',
        'while', 'xor',
    ];

    /**
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param ModuleReader $moduleReader
     * @param string $actionInterface
     * @param string $cacheKey
     * @param array $reservedWords
     */
    public function __construct(
        \Magento\Framework\Config\CacheInterface $cache,
        ModuleReader $moduleReader,
        $actionInterface = '\Magento\Framework\App\ActionInterface',
        $cacheKey = 'app_action_list',
        $reservedWords = []
    ) {
        $this->reservedWords = array_merge($reservedWords, $this->reservedWords);
        $this->actionInterface = $actionInterface;
        $data = $cache->load($cacheKey);
        if (!$data) {
            $this->actions = $moduleReader->getActionFiles();
            $cache->save(serialize($this->actions), $cacheKey);
        } else {
            $this->actions = unserialize($data);
        }
    }

    /**
     * Retrieve action class
     *
     * @param string $module
     * @param string $area
     * @param string $namespace
     * @param string $action
     * @return null|string
     */
    public function get($module, $area, $namespace, $action)
    {
        if ($area) {
            $area = '\\' . $area;
        }
        if (strpos($namespace, self::NOT_ALLOWED_IN_NAMESPACE_PATH) !== false) {
            return null;
        }
        if (in_array(strtolower($action), $this->reservedWords)) {
            $action .= 'action';
        }
        $fullPath = str_replace(
            '_',
            '\\',
            strtolower(
                $module . '\\controller' . $area . '\\' . $namespace . '\\' . $action
            )
        );
        if (isset($this->actions[$fullPath])) {
            return is_subclass_of($this->actions[$fullPath], $this->actionInterface) ? $this->actions[$fullPath] : null;
        }
        return null;
    }
}

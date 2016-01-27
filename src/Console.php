<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2015
 * @version $Id$
 * @since 1.0.0
 */

namespace skoro\stardict;

/**
 * Console helper.
 */
class Console
{
    
    /**
     * @var Console
     */
    protected static $instance = null;
    
    /**
     * @var string console script file name.
     */
    protected $scipt;
    
    /**
     * @var array
     */
    protected $argv;
    
    public function __construct()
    {
        if (static::$instance) {
            throw new \LogicException('Console already initialized.');
        }
        global $argv;
        $this->script = array_shift($argv);
        $this->argv = $argv;
    }
    
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function abort($message = null)
    {
        if ($message) {
            printf('%s: error: %s\n', $this->script, $message);
        }
        exit(1);
    }

    public function getOptionParam($name)
    {
        if (!($param = array_shift($this->argv))) {
            $this->abort(sprintf('option "%s" requires a value', $name));
        }
        return $param;
    }
    
    public function getCommandArg()
    {
        $arg = array_shift($this->argv);
        if (strpos($arg, '--') === 0 && strpos($arg, '=') !== false) {
            list($arg, $value) = explode('=', $arg, 2);
            array_unshift($this->argv, $value);
        }
        return $arg;
    }
    
    public function checkArg($arg)
    {
        return in_array($arg, $this->argv);
    }
    
    public function getScriptName()
    {
        return $this->script;
    }
    
}


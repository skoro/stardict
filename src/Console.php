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
    
    /**
     * Constructor.
     *
     * @throws \LogicException when instance already created.
     */
    public function __construct()
    {
        if (static::$instance) {
            throw new \LogicException('Console already initialized.');
        }
        global $argv;
        $this->script = array_shift($argv);
        $this->argv = $argv;
    }
    
    /**
     * Get console instance.
     *
     * @return Console
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Bail out.
     *
     * @param string $message
     */
    public function abort($message = null)
    {
        if ($message) {
            printf("%s: error: %s\n", $this->script, $message);
        }
        exit(1);
    }

    /**
     * Get option parameter.
     *
     * @param string $name
     * @return string
     */
    public function getOptionParam($name)
    {
        if (!($param = array_shift($this->argv))) {
            $this->abort(sprintf('option "%s" requires a value', $name));
        }
        return $param;
    }
    
    /**
     * Get next command line argument.
     *
     * @return string|null
     */
    public function getCommandArg()
    {
        $arg = array_shift($this->argv);
        if (strpos($arg, '--') === 0 && strpos($arg, '=') !== false) {
            list($arg, $value) = explode('=', $arg, 2);
            array_unshift($this->argv, $value);
        }
        return $arg;
    }
    
    /**
     * Is exists argument in command line arguments ?
     *
     * @return boolean
     */
    public function checkArg($arg)
    {
        return in_array($arg, $this->argv);
    }

    /**
     * @return string
     */    
    public function getScriptName()
    {
        return $this->script;
    }
    
}


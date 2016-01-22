<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2015
 * @version $Id$
 * @since 1.0.0
 */

namespace skoro\stardict;

/**
 * Generic StarDict exception.
 */
class Exception extends \Exception
{

    /**
     * @var Informative
     */
    protected $info;

    /**
     * @param Informative $info
     * @param string $message
     */
    public function __construct(Informative $info, $message)
    {
        $this->info = $info;
        parent::__construct($message);
    }
    
    /**
     * @return Informative
     */
    public function getInfo()
    {
        return $this->info;
    }

}


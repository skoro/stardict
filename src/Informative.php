<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2015
 * @version $Id$
 * @since 1.0.0
 */

namespace skoro\stardict;

/**
 * Informative
 *
 * Retrieve information.
 */
interface Informative
{

    /**
     * @return Info
     */
    public function getInfo();
    
    /**
     * @return string
     */
    public function getFilename();

}

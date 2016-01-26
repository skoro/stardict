<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2015
 * @version $Id$
 * @since 1.0.0
 */

namespace skoro\stardict;

/**
 * Print converter.
 *
 * Converts dictionary to comma separated pairs.
 */
class Converter
{
    
    /**
     * @var Info
     */
    protected $info;
    
    /**
     * @var Index
     */
    protected $index;
    
    /**
     * @var Dict
     */
    protected $dict;
    
    /**
     * @var array
     */
    protected $params = [];
    
    /**
     * @param string $infofile
     */
    public function __construct($infofile)
    {
        $this->info = new Info($infofile);
        $this->index = new Index($this->info);
        $this->dict = new Dict($this->index);
    }
    
    /**
     * Initialize before conversion starts.
     */
    public function init()
    {
    
    }
    
    /**
     * Invokes after conversion is done.
     */
    public function done()
    {
    
    }
    
    /**
     * Convert dictionary.
     *
     * @param array $params
     */
    public function convert(array $params = [])
    {
        $this->params = array_merge($this->params, $params);
        try {
            $this->init();
            foreach ($this->index as $word => $index) {
                $data = $this->dict->getData($index[0], $index[1]);
                $this->process($word, $data);
            }
            $this->done();
        }
        catch (\Exception $e) {
            $this->catchException($e);
            throw $e;
        }
    }
    
    /**
     * Process word data.
     *
     * @param string $word
     * @param string $data
     */
    public function process($word, $data)
    {
        print $word . ',"' . addslashes($data) .  '"' . PHP_EOL;
    }
    
    /**
     * Finish conversion on exception.
     *
     * @param \Exception $e
     */
    protected function catchException(\Exception $e)
    {
        // Does nothing.    
    }
    
}


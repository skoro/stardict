<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2015
 * @version $Id$
 * @since 1.0.0
 */

namespace skoro\stardict;

/**
 * Implements dictionary reader.
 *
 * @author skoro
 */
class Dict
{

    /**
     * @var resource
     */
    protected $handle;
    
    /**
     * @var string
     */
    protected $filename;
    
    /**
     * @var bool
     */
    protected $isCompressed;
    
    /**
     * @var Index
     */
    protected $index;

    /**
     * @param Index $index
     */
    public function __construct(Index $index)
    {
        $this->index = $index;
        $this->filename = $index->getInfo()->getDictFilename();
        $this->isCompressed = Info::isCompressedFile($this->filename);
        $this->open();
    }
    
    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->close();
    }
    
    /**
     * Open dictionary file.
     *
     * @throws DictException
     */
    protected function open()
    {
        if ($this->isCompressed) {
            $this->handle = gzopen($this->filename, 'r');
        } else {
            $this->handle = fopen($this->filename);
        }
        
        if (!$this->handle) {
            throw new DictException('Couldn\'t open file: ' . $this->filename);
        }
    }
    
    /**
     * Close dictionary file.
     */
    protected function close()
    {
        if (is_resource($this->handle)) {
            if ($this->isCompressed) {
                gzclose($this->handle);
            } else {
                fclose($this->handle);
            }
        }
    }
    
    /**
     * Get dictionary file name.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
    
    /**
     * Get dictionary info instance.
     *
     * @return Info
     */
    public function getInfo()
    {
        return $this->index->getInfo();
    }
    
    /**
     * Get dictionary index instance.
     *
     * @return Index
     */
    public function getIndex()
    {
        return $this->index;
    }
    
    /**
     * Read data from dictionary.
     *
     * @param int $offset
     * @param int $size
     * @return string
     * @throws DictException When seek operation to offset failed.
     */
    public function getData($offset, $size)
    {
        if ($this->isCompressed) {
            if (($seek = gzseek($this->handle, $offset)) !== -1) {
                $data = gzread($this->handle, $size);
            }
        } else {
            if (($seek = fseek($this->handle, $offset)) !== -1) {
                $data = fread($this->handle, $offset);
            }
        }
        
        if ($seek === -1) {
            throw new DictException('File seek error.');
        }
        
        return $data;
    }
    
    /**
     * Lookup word data in dictionary.
     *
     * @param string $word
     * @see Index::lookup()
     * @return array|false
     * @throws DictException
     */
    public function lookup($word)
    {
        if (!($matched = $this->index->lookup($word))) {
            return false;
        }
        
        $result = [];
        foreach ($matched as $match => $info) {
            $result[$match] = $this->getData($info[0], $info[1]);
        }
        
        return $result;
    }
    
}

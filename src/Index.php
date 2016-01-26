<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2015
 * @version $Id$
 * @since 1.0.0
 */

namespace skoro\stardict;

/**
 * Implements dictionary index reader.
 *
 * @author skoro
 */
class Index implements \IteratorAggregate, Informative
{

    /**
     * @var Info
     */
    protected $info;    
    
    /**
     * @var array
     */
    protected $words = [];

    /**
     * @param Info $info Info reader instance.
     */
    public function __construct(Info $info)
    {
        $this->info = $info;
        $this->readFile();
    }
    
    /**
     * Get dictionary info instance.
     *
     * @return Info
     */
    public function getInfo()
    {
        return $this->info;
    }
    
    /**
     * Get index file name.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->info->getIdxFilename();
    }
    
    /**
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->words);
    }
    
    /**
     * @return integer
     */
    public function getWordCount()
    {
        return count($this->words);
    }
    
    /**
     * Lookup a word.
     *
     * @param string $word exact word or wild card.
     * @return array|false
     */
    public function lookup($word)
    {
        if (substr($word, -1) === '*') {
            $word = substr($word, 0, -1);
            $ln = mb_strlen($word);
            $matched = [];
            foreach ($this->words as $w => $data) {
                if (strncmp($word, $w, $ln) === 0) {
                    $matched[$w] = $data;
                }
            }
            return $matched ? $matched : false;
        }
        else if (isset($this->words[$word])) {
            return [
                $word => $this->words[$word],
            ];
        }
        return false;
    }
    
    /**
     * Reads index data.
     *
     * @throws IndexException
     */
    protected function readFile()
    {
        $filename = $this->getFilename();
        $data = file_get_contents($filename);
        $fsize = filesize($filename);
        
        if ($data && Info::isCompressedFile($filename)) {
            $data = gzdecode($data);
            $fsize = strlen($data);
        }
        
        if (!$data) {
            throw new IndexException($this, 'Read data error.');
        }
        if ($fsize != $this->info->idxfilesize) {
            throw new IndexException($this, 'Index file size mismatch.');
        }
        
        $pos = 0;
        
        while ($pos < $fsize) {
            $chars = [];
            while (true) {
                $x = unpack("@{$pos}/Cch", $data);
                $pos++;
                if ($x['ch'] === 0) {
                    break;
                }
                $chars[] = $x['ch'];
            }
            $word = call_user_func_array('pack', array_merge(['C*'], $chars));
            $x = unpack("@{$pos}/Noffset/Nsize", $data);
            $this->words[$word] = [$x['offset'], $x['size']];
            $pos += 8;
        }
        
    }

}


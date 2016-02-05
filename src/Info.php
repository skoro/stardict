<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2015
 * @version $Id$
 * @since 1.0.0
 */

namespace skoro\stardict;

/**
 * Info
 *
 * Implements StarDict .ifo reader.
 *
 * @author skoro
 */
class Info implements Informative
{

    const HEADER = "StarDict's dict ifo file";

    /**
     * @var array Info options and its values.
     */
    protected $options = [
        'bookname' => ['req' => true, 'val' => ''],
        'wordcount' => ['req' => true, 'val' => ''],
        'idxfilesize' => ['req' => true, 'val' => ''],
        'version' => ['req' => true, 'val' => ''],
        'author' => ['req' => false, 'val' => ''],
        'email' => ['req' => false, 'val' => ''],
        'website' => ['req' => false, 'val' => ''],
        'description' => ['req' => false, 'val' => ''],
        'date' => ['req' => false, 'val' => ''],
        'sametypesequence' => ['req' => true, 'val' => ''],
    ];
    
    /**
     * @var string Info file name.
     */
    protected $filename;

    /**
     * Construct reader instance.
     *
     * @param string $filename (with or without extension .ifo)
     */
    public function __construct($filename)
    {
        if (substr($filename, -4) !== '.ifo') {
            $filename .= '.ifo';
        }
        $this->filename = $filename;
        $this->readFile();
    }
    
    /**
     * Get info option value.
     *
     * @throws InfoException when options is not defined.
     * @return string
     */
    public function __get($name)
    {
        if (!isset($this->options[$name])) {
            throw new InfoException($this, 'Info option "' . $name . '" not defined.');
        }
        return $this->options[$name]['val'];
    }
    
    /**
     * @throws \LogicException
     */
    public function __set($name, $value)
    {
        if (isset($this->options[$name])) {
            throw new \LogicException('Cannot set read only property: "' . $name . '"');
        }
    }
    
    /**
     * Get info file name.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
    
    /**
     * Informative::getInfo()
     */
    public function getInfo()
    {
        return $this;
    }
    
    /**
     * Get file name for dictionary.
     *
     * @param string $ext file extension (.idx or .dict)
     * @return string|false
     */
    protected function getFilenameFor($ext)
    {
        $base = substr(basename($this->filename), 0, -4); // Except extension.
        $file = dirname($this->filename) . DIRECTORY_SEPARATOR . $base . $ext;
        if (file_exists($file)) {
            return $file;
        }
        $file .= '.dz'; // Compressed.
        return file_exists($file) ? $file : false;
    }
    
    /**
     * Get dictionary index file.
     *
     * @throws InfoException When file not found.
     * @return string
     */
    public function getIdxFilename()
    {
        if (!($idx = $this->getFilenameFor('.idx'))) {
            throw new InfoException($this, 'No .idx index file found.');
        }
        return $idx;
    }
    
    /**
     * Get dictionary data file.
     *
     * @throws InfoException When file not found.
     * @return string
     */
    public function getDictFilename()
    {
        if (!($dict = $this->getFilenameFor('.dict'))) {
            throw new InfoException($this, 'No .dict dictionary file found.');
        }
        return $dict;
    }
    
    /**
     * Whether filename has compressed data.
     *
     * @param string $filename
     * @return bool
     */
    public static function isCompressedFile($filename)
    {
        return substr($filename, -3) === '.dz';
    }
    
    /**
     * Read file contents.
     *
     * @throws InfoException
     */
    protected function readFile()
    {
        if (!($data = @file_get_contents($this->filename))) {
            throw new InfoException($this, 'Couldn\'t read file.');
        }
        
        $lines = explode("\n", $data);
        
        // Validate file header.
        $header = array_shift($lines);
        $this->validateHeader($header);
        
        foreach ($lines as $line) {
            // Skip empty lines.
            if (empty($line = trim($line))) {
                continue;
            }
            list($k, $v) = explode('=', $line, 2);
            if (isset($this->options[$k])) {
                switch ($k) {
                    case 'wordcount':
                    case 'idxfilesize':
                        $v = (int) $v;
                        break;
                    default:
                        $v = trim($v);
                }
                $this->options[$k]['val'] = $v;
            } else {
                throw new InfoException($this, 'Unknown info option: ' . $k);
            }
        }
        
        $this->validateRequired();
        $this->validateVersion();
    }
    
    /**
     * Validate ifo header.
     *
     * @param string $header
     * @throws InfoException When header does not match.
     */
    protected function validateHeader($header)
    {
        if ($header !== self::HEADER) {
            throw new InfoException($this, 'StarDict .ifo has invalid format header.');
        }
    }
    
    /**
     * Validate required options.
     *
     * @throws InfoException When required options are exists.
     */
    protected function validateRequired()
    {
        $missed = array_filter($this->options, function ($opt) {
            return $opt['req'] && empty($opt['val']);
        });
        if ($missed) {
            throw new InfoException($this, 'Options "' . implode(',', array_keys($missed)) . '" are required.');
        }
    }
    
    /**
     * Validate StarDict version.
     *
     * @throws InfoException When version does not equal 2.4.2
     */
    protected function validateVersion()
    {
        if ($this->options['version']['val'] !== '2.4.2') {
            throw new InfoException($this, 'Only 2.4.2 version is supported.');
        }
    }
    
}


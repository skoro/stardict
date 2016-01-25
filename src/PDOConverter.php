<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2015
 * @version $Id$
 * @since 1.0.0
 */

namespace skoro\stardict;

use PDO;

/**
 * Converts dictionary to database.
 */
class PDOConverter extends Converter
{

    /**
     * @var PDO
     */
    protected $pdo;
    
    /**
     * @var array
     */
    protected $params = [
        'initSchema' => false,
        'tableDict' => 'dict',
        'tableWord' => 'word',
        'tableDictWords' => 'dict_words',
        'transaction' => false,
    ];

    /**
     * @param PDO $pdo
     * @return PDOConverter
     */
    public function setPDO(PDO $pdo)
    {
        $this->pdo = $pdo;
        return $this;
    }
    
    /**
     * @return PDO
     */
    public function getPDO()
    {
        return $this->pdo;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->pdo instanceof PDO) {
            throw new \RuntimeException('PDO not initialized.');
        }
        
        if ($this->params['transaction'] && !$this->pdo->beginTransaction()) {
            throw new \RuntimeException('PDO cannot begin transaction.');
        }

        if ($this->params['initSchema']) {
            $this->initSchema();
        }
    }
    
    /**
     * Initialize database schema.
     */
    public function initSchema()
    {
        $sql = <<<EOF
CREATE TABLE {$this->params['tableDict']} (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    dict VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);
CREATE TABLE {$this->params['tableWord']} (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    word VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);
CREATE TABLE {$this->params['tableDictWords']} (
    dict_id INT UNSIGNED NOT NULL,
    word_id INT UNSIGNED NOT NULL,
    UNIQUE `idx_dict_words` (`dict_id`, `word_id`)
);
EOF;
        $this->pdo->exec($sql);
    }
    
    public function process($word, $data)
    {
    
    }
    
}


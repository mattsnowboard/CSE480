<?php

namespace Wws\Mapper;

use Wws\Model\Dictionary;

/**
 * This class can look up Dictionary words
 * 
 * @author Matt Durak <durakmat@msu.edu>
 */
class DictionaryMapper
{
    /**
     * @var Doctrine\DBAL\Connection 
     */
    protected $db;
    
    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        $this->db = $db;
    }
    
    /**
     * Find a random word
     * 
     * @return \Wws\Model\Dictionary|null
     */
	public function FindRandom()
    {
        $dictionaryArr = $this->db->fetchAssoc('SELECT * FROM dictionary ORDER BY rand() LIMIT 1');
        return $this->returnDictionary($dictionaryArr);
    }
	
	/**
     * Return a Dictionary object for an associative array result set
     * Also checks for empty/no result
     * @param mixed $sqlResult
     * @return \Wws\Model\Dictionary|null
     */
    protected function returnDictionary($sqlResult)
    {
        if (!is_null($sqlResult) && $sqlResult !== false && !empty($sqlResult)) {
            $game = new Dictionary($sqlResult);
            return $game;
        }
        return null;
    }
}
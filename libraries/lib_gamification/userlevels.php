<?php
/**
 * @package		 Gamification Platform
 * @subpackage	 Gamification Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification Library is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('JPATH_PLATFORM') or die;

class GamificationUserLevels {

    /**
     * Users ID
     * @var integer
     */
    public $userId;
    
    public $levels = array();
    
    /**
     * Database driver
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    public function __construct($id) {
        
        $this->db = JFactory::getDbo();
        if(!empty($id)) {
            $this->load($id);
        }
        
    }
    
    /**
     * Load all user levels and set them to group index.
     * Every user can have only one level for a group.
     * 
     * @param array $id  User Id
     */
    public function load($id) {
        
        if(!$id)  {
            return null;
        }
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->select("a.level_id, a.user_id, a.group_id")
            ->select("b.title, b.points, b.value, b.published, b.points_id, b.rank_id, b.group_id")
            ->from($this->db->quoteName("#__gfy_userlevels") . ' AS a')
            ->innerJoin($this->db->quoteName("#__gfy_levels") . ' AS b ON a.level_id = b.id')
            ->where("a.user_id  = ". (int)$id);
        
        $this->db->setQuery($query);
        $results = $this->db->loadAssoc();
        
        if(!empty($results)) {
            
            $this->userId = $id;
            
            foreach($results as $result) {
                $level = new GamificationUserLevel();
                $level->bind($result);
                
                $this->levels[$result["group_id"]] = $level;
            }
            
        } 
        
    }

    public function getLevels() {
        return $this->levels;
    }
    
    /**
     * Get level by group ID
     * @param integer $groupId
     * 
     * @return mixed
     */
    public function getLevel($groupId) {
        return (!isset($this->levels[$groupId])) ? null : $this->levels[$groupId];
    }
}

<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class GamificationViewNotifications extends JViewLegacy {
    
    protected $state;
    protected $items;
    protected $pagination;
    
    protected $option;
    
    public function __construct($config){
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null){
        
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        
        JHtml::addIncludePath(JPATH_COMPONENT_SITE.'/helpers/html');
        
        JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
        
        // Add submenu
        GamificationHelper::addSubmenu($this->getName());
        
        // Prepare sorting data
        $this->prepareSorting();
        
        // Prepare actions
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();
        
        parent::display($tpl);
    }
    
    /**
     * Prepare sortable fields, sort values and filters.
     */
    protected function prepareSorting() {
    
        // Prepare filters
        $this->listOrder  = $this->escape($this->state->get('list.ordering'));
        $this->listDirn   = $this->escape($this->state->get('list.direction'));
        $this->saveOrder  = (strcmp($this->listOrder, 'a.ordering') != 0 ) ? false : true;
    
        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option='.$this->option.'&task='.$this->getName().'.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName().'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }
    
        $this->sortFields = array(
            'b.name'      => JText::_('COM_GAMIFICATION_USER'),
            'a.read'      => JText::_('COM_GAMIFICATION_READ'),
            'a.created'   => JText::_('COM_GAMIFICATION_CREATED'),
            'a.id'        => JText::_('JGRID_HEADING_ID')
        );
    
    }
    
    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar() {
    
        JHtmlSidebar::setAction('index.php?option='.$this->option.'&view='.$this->getName());
    
        $states = array(
            JHtml::_("select.option", "0", JText::_("COM_GAMIFICATION_NOT_READ")),
            JHtml::_("select.option", "1", JText::_("COM_GAMIFICATION_READ"))
        );
        
        JHtmlSidebar::addFilter(
            JText::_('COM_GAMIFICATION_SELECT_STATE'),
            'filter_state',
            JHtml::_('select.options', $states, 'value', 'text', $this->state->get('filter.state'), true)
        );
    
        $this->sidebar = JHtmlSidebar::render();
    
    }
    
    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar(){
        
        // Set toolbar items for the page
        JToolbarHelper::title(JText::_('COM_GAMIFICATION_NOTIFICATIONS_MANAGER'));
        JToolbarHelper::editList('notification.edit');
        JToolbarHelper::divider();
        JToolbarHelper::deleteList(JText::_("COM_GAMIFICATION_DELETE_ITEMS_QUESTION"), "notifications.delete");
        JToolbarHelper::divider();
        JToolbarHelper::custom('notifications.backToDashboard', "dashboard", "", JText::_("COM_GAMIFICATION_DASHBOARD"), false);
        
    }
    
	/**
	 * Method to set up the document properties
	 * @return void
	 */
	protected function setDocument() {
	    
		$this->document->setTitle(JText::_('COM_GAMIFICATION_NOTIFICATIONS_MANAGER'));
		
		// Scripts
		JHtml::_('behavior.multiselect');
		JHtml::_('formbehavior.chosen', 'select');
		JHtml::_('bootstrap.tooltip');
		
		$this->document->addScript('../media/'.$this->option.'/js/admin/list.js');
	}
    
}
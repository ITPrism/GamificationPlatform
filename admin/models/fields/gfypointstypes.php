<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package      Gamification Platform
 * @subpackage   Components
 * @since        1.6
 */
class JFormFieldGfyPointsTypes extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'gfypointstypes';

    /**
     * Method to get the field options.
     *
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.id AS value, CONCAT(a.title, " [", a.abbr, "] ") AS text')
            ->from($db->quoteName('#__gfy_points', 'a'))
            ->where('a.published = 1')
            ->order("a.title ASC");

        // Get the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

    protected function getInput()
    {
        // Initialize variables.
        $html = array();

        // Get the field options.
        $options = (array)$this->getOptions();

        $pointsTypes = array();
        if (!empty($this->value)) {
            $pointsTypes_ = (array)json_decode($this->value);
            if (!empty($pointsTypes_)) {
                foreach ($pointsTypes_ as $type) {
                    $pointsTypes[$type->id] = $type->value;
                }
            }
        }

        if (!empty($options)) {

            $html[] = '<div id="points-elements">';

            foreach ($options as $option) {

                $attr = ' class="points-type';

                // Initialize some field attributes.
                $attr .= $this->element['class'] ? ' ' . (string)$this->element['class'] . '"' : '"';

                $attr .= $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';

                // Initialize JavaScript field attributes.
                $attr .= $this->element['onchange'] ? ' onchange="' . (string)$this->element['onchange'] . '"' : '';


                $elementId = substr(md5(uniqid(time() * rand(), true)), 0, 10);

                $value = JArrayHelper::getValue($pointsTypes, $option->value);

                $htmlInput = '<label for="' . $elementId . '">' . $option->text . '</label>';
                $htmlInput .= '<input type="text" value="' . $value . '" id="' . $elementId . '" data-id="' . $option->value . '" ' . $attr . '/>';

                $html[] = $htmlInput;

            }

            $html[] = '</div>';

        }

        $html[] = '<input type="hidden" name="' . $this->name . '" value=\'' . $this->value . '\' id="' . $this->id . '" />';

        // Scripts
        JHtml::_("behavior.framework");
        $doc = JFactory::getDocument();
        $doc->addScript(JURI::root() . "media/com_gamification/js/admin/fields/pointstypes.js");

        return implode($html);

    }
}

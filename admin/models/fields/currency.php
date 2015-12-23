<?php
/*----------------------------------------------------------------------------------|  www.giz.de  |----/
	Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb 
/-------------------------------------------------------------------------------------------------------/

	@version		3.1.0
	@build			23rd December, 2015
	@created		15th June, 2012
	@package		Cost Benefit Projection
	@subpackage		currency.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>	
	@owner			Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
	
/-------------------------------------------------------------------------------------------------------/
	Cost Benefit Projection Tool.
/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Currency Form Field class for the Costbenefitprojection component
 */
class JFormFieldCurrency extends JFormFieldList
{
	/**
	 * The currency field type.
	 *
	 * @var		string
	 */
	public $type = 'currency'; 
	/**
	 * Override to add new button
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   3.2
	 */
	protected function getInput()
	{
		// [7943] see if we should add buttons
		$setButton = $this->getAttribute('button');
		// [7945] get html
		$html = parent::getInput();
		// [7947] if true set button
		if ($setButton === 'true')
		{
			$user = JFactory::getUser();
			// [7951] only add if user allowed to create currency
			if ($user->authorise('currency.create', 'com_costbenefitprojection'))
			{
				// [7969] get the input from url
				$jinput = JFactory::getApplication()->input;
				// [7971] get the view name & id
				$values = $jinput->getArray(array(
					'id' => 'int',
					'view' => 'word'
				));
				// [7976] check if new item
				$ref = '';
				if (!is_null($values['id']) && strlen($values['view']))
				{
					// [7980] only load referal if not new item.
					$ref = '&amp;ref=' . $values['view'] . '&amp;refid=' . $values['id'];
				}
				// [7983] build the button
				$button = '<a class="btn btn-small btn-success"
					href="index.php?option=com_costbenefitprojection&amp;view=currency&amp;layout=edit'.$ref.'" >
					<span class="icon-new icon-white"></span>' . JText::_('COM_COSTBENEFITPROJECTION_NEW') . '</a>';
				// [7987] return the button attached to input field
				return $html . $button;
			}
		}
		return $html;
	}

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	public function getOptions()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('a.codethree','a.name'),array('codethree','currency_name')));
		$query->from($db->quoteName('#__costbenefitprojection_currency', 'a'));
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order('a.name ASC');
		$db->setQuery((string)$query);
		$items = $db->loadObjectList();
		$options = array();
		if ($items)
		{
			foreach($items as $item)
			{
				$options[] = JHtml::_('select.option', $item->codethree, $item->currency_name);
			}
		}
		return $options;
	}
}
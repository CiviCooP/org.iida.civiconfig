<?php
/**
 * Class for ActivityType configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 3 Feb 2016
 * @license AGPL-3.0
 */
class CRM_Civiconfig_Entity_ActivityType extends CRM_Civiconfig_Entity_OptionValue {
  /**
   * Manipulate $params before entity creation.
   *
   * @param array $params params that will be used for entity creation
   * @param array $existing existing entity (if available)
   */
  protected function prepareParams(array &$params, array $existing = []) {
    $params['option_group_id'] = $this->getOptionGroupId();
    parent::prepareParams($params, $existing);
  }

  /**
   * Function to find an existing entity based on the entity's parameters.
   *
   * This default implementation searches on the name, but you can override it.
   *
   * @param array $params
   * @return array|bool
   * @access public
   * @static
   */
  public function getExisting(array $params) {
    $params['option_group_id'] = $this->getOptionGroupId();
    return parent::getExisting($params); // TODO: Change the autogenerated stub
  }

  /**
   * Method to get option group id for activity type
   *
   * @return array
   * @throws CiviCRM_API3_Exception
   */
  public function getOptionGroupId() {
    return civicrm_api3('OptionGroup', 'Getvalue', array('name' => 'activity_type', 'return' => 'id'));
  }
}
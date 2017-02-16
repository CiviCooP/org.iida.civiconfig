<?php
/**
 * Class for CustomGroup configuration
 * 
 * This class creates the custom fields as well.
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 *         Johan Vervloet (Chirojeugd-Vlaanderen vzw) <helpdesk@chiro.be>
 * @date 16 Feb 2017
 * @license AGPL-3.0
 */
class CRM_Civiconfig_Entity_CustomGroup extends CRM_Civiconfig_Entity {
  /**
   * CRM_Civiconfig_CustomGroup constructor.
   */
  public function __construct() {
    parent::__construct('CustomGroup');
  }

  /**
   * Method to create custom group with custom fields.
   *
   * @param array $params
   * @return array
   * @throws Exception when error from API CustomGroup Create
   */
  public function create(array $params) {
    $fieldParamsArray = $params['fields'];
    $id = parent::create($params);

    $customFieldCreator = new CRM_Civiconfig_Entity_CustomField();
    foreach ($fieldParamsArray as $customFieldData) {
      $customFieldData['custom_group_id'] = $id;
      $customFieldCreator->create($customFieldData);
    }
    // remove custom fields that are still on install but no longer in config
    CRM_Civiconfig_Entity_CustomField::removeUnwantedCustomFields($id, $params);

    return $id;
  }

  /**
   * Manipulate $params before entity creation.
   *
   * @param array $params params that will be used for entity creation
   * @param array $existing existing entity (if available)
   */
  protected function prepareParams(array &$params, array $existing = []) {
    if (empty($params['title'])) {
      $params['title'] = CRM_Civiconfig_Utils::buildLabelFromName($params['name']);
    }
    parent::prepareParams($params, $existing);

    unset($params['fields']);

    if (empty($params['extends_entity_column_value'])) {
      return;
    }

    // FIXME: This is rather hacky.
    $entityType = $params['extends'];
    switch ($entityType) {
      case 'Contribution':
        $entitySubType = 'FinancialType';
        break;
      case 'ParticipantEventType':
        $entitySubType = 'EventType';
        break;
      default:
        $entitySubType = $entityType . "Type";
        break;
    }
    $entitySubTypeKey = in_array($entityType, [
      'Activity',
      'Event'
    ]) ? 'value' : 'id';

    $values = $params['extends_entity_column_value'];
    if (!is_array($values)) {
      $values = [$values];
    }

    $className = "CRM_Civiconfig_Entity_$entitySubType";
    $entityConfig = new $className();

    $params['extends_entity_column_value'] = [];
    foreach ($values as $extendsValue) {
      $found = $entityConfig->getExisting(['name' => $extendsValue]);
      if (isset($found[$entitySubTypeKey])) {
        $params['extends_entity_column_value'][] = $found[$entitySubTypeKey];
      }
    }
  }
}

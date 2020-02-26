<?php


class CRM_Civiconfig_Entity_CiviRuleRuleCondition extends CRM_Civiconfig_Entity
{
  public function __construct()
  {
    parent::__construct('CiviRuleRuleCondition');
  }

  public function validateCreateParams($params)
  {
    if (empty($params['rule_id']) || empty($params['condition_id'])){
      throw new \CRM_Civiconfig_EntityException("Missing mandatory parameter 'rule_id', 'action_id' or 'action_params' in class " . get_class() . ".");
    }
  }

  public function getExisting(array $params)
  {
    try {
      $result = civicrm_api3('civiRuleRuleCondition', 'getSingle', [
        'rule_id' => $params['rule_id'],
        'condition_id' => $params['condition_id'],
        'condition_params' => $params['condition_params']
      ]);
      return $result;
    } catch (CiviCRM_API3_Exception $ex) {
      return [];
    }
  }

  public function removeUnwantedConditions($ruleId, $wantedConditions){
    $conditions = civicrm_api3('CiviRuleRuleCondition', 'get', [
      'rule_id' => $ruleId
    ])['values'];

    $wantedConditions = array_values($wantedConditions);
    foreach ($conditions as $condition){
      $index = $this::findCondition($condition, $wantedConditions);
      if ($index == -1){
        civicrm_api3('CiviRuleRuleCondition', 'delete', [
          'id' => $condition['id']
        ]);
      }
    }
  }

  public function findCondition($condition, $conditions):int {
    $out = -1;
    for ($i=0; $i<count($conditions) && $out==-1; $i++ ){
      $a = $conditions[$i];
      if($condition['condition_id'] == $a['condition_id'] && $condition['condition_params'] == $a['condition_params']){
        $out = $i;
      }
    }
    return $out;
  }


}

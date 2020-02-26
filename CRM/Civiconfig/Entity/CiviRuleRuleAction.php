<?php


class CRM_Civiconfig_Entity_CiviRuleRuleAction extends CRM_Civiconfig_Entity
{
  public function __construct()
  {
    parent::__construct('CiviRuleRuleAction');
  }

  public function validateCreateParams($params)
  {
    if (empty($params['rule_id']) || empty($params['action_id']) || empty($params['action_params'])){
      throw new \CRM_Civiconfig_EntityException("Missing mandatory parameter 'rule_id', 'action_id' or 'action_params' in class " . get_class() . ".");
    }
  }

  public function getExisting(array $params)
  {
    try {
      $result = civicrm_api3('civiRuleRuleAction', 'getSingle', [
        'rule_id' => $params['rule_id'],
        'action_id' => $params['action_id'],
        'action_params' => $params['action_params']
      ]);
      return $result;
    } catch (CiviCRM_API3_Exception $ex) {
      return [];
    }
  }

  public function removeUnwantedActions($ruleId, $wantedActions){
    $actions = civicrm_api3('CiviRuleRuleAction', 'get', [
      'rule_id' => $ruleId
    ])['values'];

    $wantedActions = array_values($wantedActions);
    foreach ($actions as $action){
      $index = $this::findAction($action, $wantedActions);
      if ($index == -1){
        civicrm_api3('CiviRuleRuleAction', 'delete', [
          'id' => $action['id']
        ]);
      }
    }
  }

  public function findAction($action, $actions):int {
    $out = -1;
    for ($i=0;$i<count($actions) && $out==-1; $i++ ){
      $a = $actions[$i];
      if($action['action_id'] == $a['action_id'] && $action['action_params'] == $a['action_params']){
        $out = $i;
      }
    }
    return $out;
  }

}

<?php


class CRM_Civiconfig_Entity_CiviRuleRule extends CRM_Civiconfig_Entity
{
  public function __construct()
  {
    parent::__construct('civiRuleRule');
  }

  public function create(array $params)
  {
    $actionsParams = $params['actions'];
    $conditionsParams = $params['conditions'];
    $id = parent::create($params);

    $actionCreater = new CRM_Civiconfig_Entity_CiviRuleRuleAction();
    foreach ($actionsParams as $actionparams){
      $actionparams['rule_id'] = $id;
      $actionCreater->create($actionparams);
    }
    $actionCreater->removeUnwantedActions($id, $actionsParams);

    $conditionCreator = new CRM_Civiconfig_Entity_CiviRuleRuleCondition();
    foreach ($conditionsParams as $conditionParams){
      $conditionParams['rule_id'] = $id;
      $conditionCreator->create($conditionParams);
    }
    $conditionCreator->removeUnwantedConditions($id, $conditionsParams);

    return $id;
  }
}

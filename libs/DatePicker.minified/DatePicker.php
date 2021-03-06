<?php
/**
 * Addons and code snippets for Nette Framework. (unofficial)
 *
 * @author   Jan Tvrdík
 * @license  MIT
 */
namespace JanTvrdik\Components{use
Nette;use
Nette\Forms;use
DateTime;class
DatePicker
extends
Forms\Controls\BaseControl{const
W3C_DATE_FORMAT='Y-m-d';protected$value;protected$rawValue;private$className='date';function
__construct($label=NULL){parent::__construct($label);$this->control->type='date';}function
getClassName(){return$this->className;}function
setClassName($className){$this->className=$className;return$this;}function
getControl(){$control=parent::getControl();$control->addClass($this->className);list($min,$max)=$this->extractRangeRule($this->getRules());if($min!==NULL)$control->min=$min->format(self::W3C_DATE_FORMAT);if($max!==NULL)$control->max=$max->format(self::W3C_DATE_FORMAT);if($this->value)$control->value=$this->value->format(self::W3C_DATE_FORMAT);return$control;}function
setValue($value){if($value
instanceof
DateTime){}elseif(is_int($value)){}elseif(empty($value)){$rawValue=$value;$value=NULL;}elseif(is_string($value)){$rawValue=$value;if(preg_match('#^(?P<dd>\d{1,2})[. -] *(?P<mm>\d{1,2})([. -] *(?P<yyyy>\d{4})?)?$#',$value,$matches)){$dd=$matches['dd'];$mm=$matches['mm'];$yyyy=isset($matches['yyyy'])?$matches['yyyy']:date('Y');if(checkdate($mm,$dd,$yyyy)){$value="$yyyy-$mm-$dd";}else{$value=NULL;}}}else{throw
new\InvalidArgumentException();}if($value!==NULL){try{$value=Nette\DateTime::from($value);}catch(\Exception$e){$value=NULL;}}if(!isset($rawValue)&&isset($value)){$rawValue=$value->format(self::W3C_DATE_FORMAT);}$this->value=$value;$this->rawValue=$rawValue;return$this;}function
getRawValue(){return$this->rawValue;}static
function
validateFilled(Forms\IControl$control){if(!$control
instanceof
self)throw
new
Nette\InvalidStateException('Unable to validate '.get_class($control).' instance.');$rawValue=$control->rawValue;return!empty($rawValue);}static
function
validateValid(Forms\IControl$control){if(!$control
instanceof
self)throw
new
Nette\InvalidStateException('Unable to validate '.get_class($control).' instance.');$value=$control->value;return(empty($control->rawValue)||$value
instanceof
DateTime);}static
function
validateRange(Forms\IControl$control,$range){return
Nette\Utils\Validators::isInRange($control->getValue(),$range);}private
function
extractRangeRule(Forms\Rules$rules){$controlMin=$controlMax=NULL;foreach($rules
as$rule){if($rule->type===Forms\Rule::VALIDATOR){if($rule->operation===Forms\Form::RANGE&&!$rule->isNegative){$ruleMinMax=$rule->arg;}}elseif($rule->type===Forms\Rule::CONDITION){if($rule->operation===Forms\Form::FILLED&&!$rule->isNegative&&$rule->control===$this){$ruleMinMax=$this->extractRangeRule($rule->subRules);}}if(isset($ruleMinMax)){list($ruleMin,$ruleMax)=$ruleMinMax;if($ruleMin!==NULL&&($controlMin===NULL||$ruleMin>$controlMin))$controlMin=$ruleMin;if($ruleMax!==NULL&&($controlMax===NULL||$ruleMax<$controlMax))$controlMax=$ruleMax;$ruleMinMax=NULL;}}return
array($controlMin,$controlMax);}}}
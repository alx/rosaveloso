<?php
/**
 * @package ReusableOptions
 */
 

/**
 * The UpdateOptionVisitor:: is responsible for updating visited options. It 
 * typically visits objects after form submission.
 *
 * @author  M. Flury
 * @package ReusableOptions
 */
class UpdateOptionVisitor extends OptionVisitor
{
	
	/**
	 * Abstract implementation of the visitTextField() method called whenever a
	 * TextFieldOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object TextFieldOption &$textField	Reference to visited option.
	 */
	 function visitTextFieldOptionBefore(&$textField)
	 {
	 	if(isset($_POST[$textField->getPOSTName()]))
	 		$textField->setValue(attribute_escape($_POST[$textField->getPOSTName()]));
	 }
	 
	 
	 function visitStrictValidationTextFieldOptionBefore(&$textField)
	 {
	 	$oldValue = $textField->getValue();
	 	$this->visitTextField($textField);
	 	//check whether we pass validation if not put back the old value
	 	$errMsgs = $textField->validate();
	 	if(!empty($errMsgs))
	 		$textField->setValue($oldValue);	
	 }
	
	/**
	 * Abstract implementation of the visitTextField() method called whenever a
	 * TextFieldOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object TextFieldOption &$textField	Reference to visited option.
	 */
	 function visitPasswordTextFieldOptionBefore(&$textField)
	 {
	 	$this->visitTextFieldOptionBefore($textField);
	 }
	 
	 /**
	 * Abstract implementation of the visitTextArea() method called whenever a
	 * TextAreaOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object TextAreaOption &$textArea	Reference to visited option.
	 */
	 function visitTextAreaOptionBefore(&$textArea)
	 {
	 	if(isset($_POST[$textArea->getPOSTName()]))
	 		$textArea->setValue(attribute_escape($_POST[$textArea->getPOSTName()]));
	 }
	 
	 /**
	 * Abstract implementation of the visitHiddenInputField() method called whenever a
	 * HiddenInputField is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object HiddenInputField &$hiddenInputField	Reference to visited option.
	 */
	 function visitHiddenInputFieldOptionBefore(&$hiddenInputField)
	 {
	 	$hiddenInputField->setValue(attribute_escape($_POST[$hiddenInputField->getPOSTName()]));
	 }
	
	/**
	 * Abstract implementation of the visitCheckBox() method called whenever a
	 * CheckBoxOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object CheckBoxOption &$checkBox	Reference to visited option.
	 */
	 function visitCheckBoxOptionBefore(&$checkBox)
	 {
	 	$checkBox->setValue(isset($_POST[$checkBox->getPOSTName()]) ? '1' : '0');
	 }

	/**
	 * Abstract implementation of the visitCheckBox() method called whenever a
	 * CheckBoxOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object CheckBoxOption &$checkBox	Reference to visited option.
	 */
	 function visitRadioButtonListBefore(&$radioButtonList)
	 {	
	 	$radioButtonList->setValue($_POST[$radioButtonList->getPOSTName()]);
	 }
	 
	 function visitRO_CheckBoxListBefore(&$checkBoxList)
	 {	
	 	$checkBoxList->setValue(isset($_POST[$checkBoxList->getPOSTName()]) ? $_POST[$checkBoxList->getPOSTName()] : NULL);
	 }
	 
	 
	 /**
	 * Abstract implementation of the visitDropDownListBefore() method called whenever a
	 * CheckBoxOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object DropDownList &$dropDownList	Reference to visited option.
	 */
	 function visitDropDownListBefore(&$dropDownList)
	 {	
	 	$dropDownList->setValue($_POST[$dropDownList->getPOSTName()]);
	 }
	 

	 function visitRO_ReorderableListBefore(&$reorderableList){
	 	$reorderableList->setValue($_POST[$reorderableList->getFieldName()]);
	 }

	 
	 /**
	  * Method called whenever any option is visited.
	  *
	  * @param object ReusableOption &$option	Reference to visited option.
	  */
	 function visitDefaultBefore(&$option)
	 {
	 	$option->storeOldValues();
	 }

	 /**
	  * Method called whenever any option is visited.
	  *
	  * @param object ReusableOption &$option	Reference to visited option.
	  */
	 function visitDefaultAfter(&$option)
	 {
	 	$option->updateChangedStatus();
	 }


}

?>
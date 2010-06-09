<?php

class Tx_HBook_ViewHelpers_Form_PropertyRowViewHelper extends Tx_Fluid_Core_ViewHelper_TagBasedViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'tr';

		/**
		 * @param string $property
		 * @param string $errorClass
		 * @return string
		 */
	public function render($property, $errorClass = 'tx_hbook-error') {
		if($this->hasErrorsForProperty($property)) $this->tag->addAttribute("class", $errorClass);
		$this->tag->setContent($this->renderChildren());
		return $this->tag->render();
	}

	protected function hasErrorsForProperty($propertyName) {
		$errors = $this->controllerContext->getRequest()->getErrors();
		$formName = $this->viewHelperVariableContainer->get('Tx_Fluid_ViewHelpers_FormViewHelper', 'formName');
		$formErrors = array();
		foreach ($errors as $error) {
			if ($error instanceof Tx_Extbase_Validation_PropertyError && $error->getPropertyName() === $formName) {
				$formErrors = $error->getErrors();
				foreach ($formErrors as $formError) {
					if ($formError instanceof Tx_Extbase_Validation_PropertyError && $formError->getPropertyName() === $propertyName) {
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}

}

?>
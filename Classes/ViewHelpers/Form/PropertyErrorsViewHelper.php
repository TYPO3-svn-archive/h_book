<?php

class Tx_HBook_ViewHelpers_Form_PropertyErrorsViewHelper extends Tx_Fluid_ViewHelpers_Form_ErrorsViewHelper {

		/**
		 * @param string $property
		 * @param string $form
		 * @param string $as
		 * @return string
		 */
	public function render($property, $form, $as = 'error') {
		$errors = $this->controllerContext->getRequest()->getErrors();
		$errors = $this->getErrorsForProperty($form, $errors);
		$errors = $this->getErrorsForProperty($property, $errors);
		$output = '';
		foreach ($errors as $errorKey => $error) {
			$this->templateVariableContainer->add($as, $error);
			$output .= $this->renderChildren();
			$this->templateVariableContainer->remove($as);
		}
		return $output;
	}

}

?>

"use strict";

(function() {

    function handleGoToStepClick() {
        const steps = document.querySelectorAll(".go-to-step");
        steps.forEach(step => {
            step.addEventListener("click", function(e) {
                e.preventDefault();
        
                // Assume validation is required unless explicitly set to false
                const shouldValidate = this.getAttribute("data-validate") !== "false";
                const currentStep = document.querySelector(".step.active");
                const targetStepSelector = this.getAttribute("data-target-step");
                const targetStep = document.querySelector(targetStepSelector);
        
                // Perform SWV validation for the current step before proceeding if needed
                if (!shouldValidate || (shouldValidate && validateCurrentStepFields(currentStep))) {
                // Proceed if validation is not needed or if validation passes
                document.querySelectorAll(".step").forEach(step => step.classList.remove("active"));
                targetStep.classList.add("active");
                } else {
                console.log("Validation failed for current step. Correct errors to proceed.");
                // Handle validation failure (e.g., display error messages)
                }
            });
        });
    }
  
    function getFormIdAndSchema(formElement) {
        // Find the hidden input field containing the form ID
        const formIdInput = formElement.querySelector('input[name="_wpcf7"]');
        const formId = formIdInput ? parseInt(formIdInput.value, 10) : null;
    
        // Retrieve the validation schema using the form ID
        const validationSchema = formId ? wpcf7.schemas.get(formId) : null;
    
        return {
        formId,
        validationSchema
        };
    }

    function validateCurrentStepFields(stepElement) {
        let isValid = true;
    
        const formElement = stepElement.closest('.wpcf7-form');
        if (!formElement) {
            console.error('Form element not found for the current step.');
            return false;
        }
    
        stepElement.querySelectorAll('.validation-error').forEach(el => el.remove());
    
        const { validationSchema } = getFormIdAndSchema(formElement);
        if (!validationSchema) {
            console.error('Validation schema not found.');
            return false;
        }
    
        const inputGroups = groupInputsByName(stepElement.querySelectorAll('input, select, textarea'));
    
        Object.entries(inputGroups).forEach(([name, inputs]) => {
            // Handling grouped fields (checkboxes, radios) separately
            if (inputs[0].type === 'checkbox' || inputs[0].type === 'radio') {
                if (!handleGroupValidation(inputs, validationSchema) && isValid) {
                    isValid = false;
                }
            } else {
                // Handling other types of fields
                const applicableRules = validationSchema.rules.filter(rule => rule.field === name);
                applicableRules.forEach(rule => {
                    if (!validateFieldGroup(inputs, rule) && isValid) {
                        isValid = false;
                    }
                });
            }
        });
    
        return isValid;
    }
    
    function handleGroupValidation(inputs, validationSchema) {
        // Extract rules for the group
        const groupName = inputs[0].name;
        const rule = validationSchema.rules.find(r => r.field === groupName && r.rule === 'required');
    
        // Only proceed if a 'required' rule is found for this group
        if (rule) {
            const isAnyChecked = inputs.some(input => input.checked);
            if (!isAnyChecked) {
                appendValidationError(inputs[0].closest('.wpcf7-form-control-wrap'), groupName, rule.error || "Please select an option.");
                return false;
            }
        }
        return true;
    }
     
    function validateFieldGroup(inputs, rule) {
        let fieldIsValid = true;
        let errorText = rule.error || "This field is not correctly filled.";
    
        inputs.forEach(input => {
            if (fieldIsValid) { // Only proceed if still valid to avoid overwriting previous errors
                switch (rule.rule) {
                    case 'required':
                        if (input.value.trim() === "") {
                            fieldIsValid = false;
                            errorText = rule.error || "This field is required.";
                        }
                        break;
                    case 'email':
                        // Email validation should only happen if input is not empty or if it's required
                        if (input.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value.trim())) {
                            fieldIsValid = false;
                            errorText = rule.error || "Invalid email format.";
                        }
                        break;
                    case 'date':
                        // Date validation should only occur if input is not empty or if it's required
                        if (input.value.trim() && (!/^\d{4}-\d{2}-\d{2}$/.test(input.value) || isNaN(new Date(input.value).getTime()))) {
                            fieldIsValid = false;
                            errorText = rule.error || "Invalid date format.";
                        }
                        break;
                    case 'enum':
                        if (input.value.trim() && !rule.accept.includes(input.value)) {
                            fieldIsValid = false;
                            errorText = rule.error || "Invalid selection.";
                        }
                        break;
                    // Include additional validation rules as needed
                }
            }
        });
    
        if (!fieldIsValid) {
            appendValidationError(inputs[0].closest('.wpcf7-form-control-wrap'), rule.field, errorText);
        }
    
        return fieldIsValid;
    }
    

    
    function groupInputsByName(inputs) {
        return Array.from(inputs).reduce((acc, input) => {  
            (acc[input.name] = acc[input.name] || []).push(input);
            return acc;
        }, {});
    }
    
    function appendValidationError(wrapper, fieldName, errorText) {
        if (!wrapper.querySelector(`.validation-error[data-for="${fieldName}"]`)) {
            const errorMessage = document.createElement('div');
            errorMessage.textContent = errorText;
            errorMessage.className = 'validation-error';
            errorMessage.setAttribute('data-for', fieldName);
            wrapper.appendChild(errorMessage);
        }
    }

    function setupFinalStepOnSuccess() {
        document.addEventListener('wpcf7mailsent', moveCf7ResponseToFinalStep, false);
    }

    function moveCf7ResponseToFinalStep() {
        
        // Remove 'active' class from all step elements to hide them
        document.querySelectorAll('.step').forEach(step => {
            step.classList.remove('active');
        });

        // Display the final step
        const finalStep = document.getElementById('final-step');
        if (finalStep) {
            finalStep.classList.add('active');
        }
        
    }

    document.addEventListener('DOMContentLoaded', function() {
        handleGoToStepClick();
        setupFinalStepOnSuccess();
    });

})();

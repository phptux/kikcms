<?php

namespace KikCMS\Classes\Frontend\Extendables;


use KikCMS\Classes\Frontend\WebsiteExtendable;
use KikCMS\Classes\WebForm\DataForm\DataForm;
use KikCMS\Classes\WebForm\Field;

/**
 * Contains methods to add template fields to a Form in a Pages DataTable
 */
class TemplateFieldsBase extends WebsiteExtendable
{
    /**
     * @param string $variable
     * @param DataForm $form
     * @return null|Field
     */
    public function getFormField(string $variable, DataForm $form)
    {
        $methodName = 'field' . ucfirst($variable);

        if( ! method_exists($this, $methodName)){
            return null;
        }

        return $this->$methodName($form);
    }
}
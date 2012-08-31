<?php
/**
 * EvaEngine
 *
 * @link      https://github.com/AlloVince/eva-engine
 * @copyright Copyright (c) 2012 AlloVince (http://avnpc.com/)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Eva_Api.php
 * @author    AlloVince
 */

namespace Eva\Form\View\Helper;

use Zend\Form\FormInterface;
use Zend\Form\ElementInterface;

/**
 * Core Form Input helper
 * This helper will call other Zend official helpers to create Form Element
 * 
 * @category   Eva
 * @package    Eva_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Input extends \Zend\Form\View\Helper\AbstractHelper
{

    protected function translateElement(ElementInterface $element)
    {
        if(!$this->translator){
            return $element;
        }
        $attributes = $element->getAttributes();
        foreach($attributes as $key => $value){
            if(isset($attributes['label'])){
                $attributes['label'] = $this->translator->translate($attributes['label']);
            }

            if($attributes['type'] == 'select' && isset($attributes['options'])){
                $elementOptions = $attributes['options'];
                foreach($elementOptions as $elementOptionsKey => $elementSubOption){
                    if(!isset($elementSubOption['label'])){
                        continue;
                    }
                    $attributes['options'][$elementOptionsKey]['label'] = $this->translator->translate($elementSubOption['label']);
                }
            }

            if($attributes['type'] == 'radio' && isset($attributes['options'])){
                $elementOptions = $attributes['options'];
                $translatedOptions = array();
                foreach($elementOptions as $elementOptionsKey => $elementSubOption){
                    $translatedKey = $this->translator->translate($elementOptionsKey);
                    $translatedOptions[$translatedKey] = $elementSubOption;
                }
                $attributes['options'] = $translatedOptions;
            }

            if(isset($attributes['placeholder'])){
                $attributes['placeholder'] = $this->translator->translate($attributes['placeholder']);
            }
        }
        if($messages = $element->getMessages()){
            foreach($messages as $key => $message){
                $messages[$key] = $this->translator->translate($message);
            }
            $element->setMessages($messages);
        }
        $element->setAttributes($attributes);

        return $element;
    }

    /**
    * Invoke helper as functor
    *
    * Proxies to {@link render()}.
    * 
    * @param  ElementInterface $element 
    * @return string
    */
    public function __invoke(ElementInterface $element, array $options = array())
    {
        $defaultOptions = array(
            'type' => 'formInput',
            'args' => array(),
            'i18n' => true,
        );

        $options = array_merge($defaultOptions, $options);
        $elementType = $options['type'];
        unset($options['type']);

        //Support Subform
        if($subFormName = $element->getAttribute('data-subform-name')){
            $name = $element->getName();
            if($name){
                $element->setName($subFormName . '[' . $name . ']');
                $attributes = $element->getAttributes();
                //Reset attributes to make sure re-name just once;
                unset($attributes['data-subform-name']);
                $element->clearAttributes();
                $element->setAttributes($attributes);
            }
        }

        $args = array();
        if(isset($option['args'])){
            if($option['args'] && is_array($option['args'])){
                foreach($args as $key => $value){
                    $args[] = $value; 
                }
            }
            unset($option['args']);
        }


        if($options){
            //NOTE: clone element not effect to form original element
            $element = clone $element;
            foreach($options as $key => $value){
                $element->setAttribute($key, $value);
            }
            if(true === $options['i18n']){
                $element = $this->translateElement($element);
            }
        }

        //put element clone into view helper
        array_unshift($args, $element);

        $view = $this->getView();
        return call_user_func_array(array(
            &$view,
            $elementType,
        ), $args);
    }
}

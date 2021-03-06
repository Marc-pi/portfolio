<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\Portfolio\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class ProjectForm extends BaseForm
{

    public function __construct($name = null, $option = array())
    {
        $this->option = $option;
        //$this->thumbUrl = (isset($option['thumbUrl'])) ? $option['thumbUrl'] : '';
        //$this->removeUrl = (isset($option['removeUrl'])) ? $option['removeUrl'] : '';
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new ProjectFilter($this->option);
        }
        return $this->filter;
    }

    public function init()
    {
        // id
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        // title
        $this->add(array(
            'name' => 'title',
            'options' => array(
                'label' => __('Title'),
            ),
            'attributes' => array(
                'type' => 'text',
                'required' => true,
            )
        ));
        // slug
        $this->add(array(
            'name' => 'slug',
            'options' => array(
                'label' => __('Slug'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // type
        $this->add(array(
            'name' => 'type',
            'type' => 'Module\Portfolio\Form\Element\Type',
            'options' => array(
                'label' => __('Project type'),
                'category' => '',
            ),
            'attributes' => array(
                'required' => true,
            )
        ));
        // Main image
        $this->add(array(
            'name' => 'main_image',
            'type' => 'Module\Media\Form\Element\Media',
            'options' => array(
                'label' => __('Main image'),
                'required' => true,
            ),
        ));
        // Additional images'
        $this->add(array(
            'name' => 'additional_images',
            'type' => 'Module\Media\Form\Element\Media',
            'options' => array(
                'label' => __('Additional images'),
                'media_gallery' => true,
            ),
        ));
        // text_description
        $this->add(array(
            'name' => 'text_description',
            'options' => array(
                'label' => __('Description'),
                'editor' => 'html',
            ),
            'attributes' => array(
                'type' => 'editor',
                'description' => '',
            )
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'type' => 'select',
            'options' => array(
                'label' => __('Status'),
                'value_options' => array(
                    1 => __('Published'),
                    2 => __('Pending review'),
                    3 => __('Draft'),
                    4 => __('Private'),
                    5 => __('Remove'),
                ),
            ),
        ));
        // service
        $this->add(array(
            'name' => 'service',
            'options' => array(
                'label' => __('Service'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // Technology
        $this->add(array(
            'name' => 'technology',
            'options' => array(
                'label' => __('Technology'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // Website
        $this->add(array(
            'name' => 'website',
            'options' => array(
                'label' => __('Website'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // website_view
        $this->add(array(
            'name'          => 'website_view',
            'type'          => 'checkbox',
            'options'       => array(
                'label' => __('View website link'),
            ),
            'attributes'    => array(
                'value'     => '1',
            )
        ));
        // phone
        $this->add(array(
            'name' => 'phone',
            'options' => array(
                'label' => __('Phone'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // phone_view
        $this->add(array(
            'name'          => 'phone_view',
            'type'          => 'checkbox',
            'options'       => array(
                'label' => __('View phone'),
            ),
            'attributes'    => array(
                'value'     => '1',
            )
        ));
        // extra_seo
        $this->add(array(
            'name' => 'extra_seo',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('SEO options'),
            ),
        ));
        // seo_title
        $this->add(array(
            'name' => 'seo_title',
            'options' => array(
                'label' => __('SEO Title'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '2',
                'cols' => '40',
                'description' => '',
            )
        ));
        // seo_keywords
        $this->add(array(
            'name' => 'seo_keywords',
            'options' => array(
                'label' => __('SEO Keywords'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '2',
                'cols' => '40',
                'description' => '',
            )
        ));
        // seo_description
        $this->add(array(
            'name' => 'seo_description',
            'options' => array(
                'label' => __('SEO Description'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '3',
                'cols' => '40',
                'description' => '',
            )
        ));
        // tag
        if (Pi::service('module')->isActive('tag')) {
            $this->add(array(
                'name' => 'tag',
                'type' => 'tag',
                'options' => array(
                    'label' => __('Tags'),
                ),
                'attributes' => array(
                    'id' => 'tag',
                    'description' => __('Use `|` as delimiter to separate tag terms'),
                )
            ));
        }
        /* // Version
        $this->add(array(
            'name' => 'version',
            'options' => array(
                'label' => __('Version'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // Size
        $this->add(array(
            'name' => 'size',
            'options' => array(
                'label' => __('Size'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // extra_comment
        $this->add(array(
            'name' => 'extra_comment',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('Customer comment'),
            ),
        ));
        // Customer
        $this->add(array(
            'name' => 'customer',
            'options' => array(
                'label' => __('Customer'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // Comment
        $this->add(array(
            'name' => 'comment',
            'options' => array(
                'label' => __('Comment'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '5',
                'cols' => '40',
            )
        ));
        // comment by
        $this->add(array(
            'name' => 'commentby',
            'options' => array(
                'label' => __('Comment By'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // extra_link
        $this->add(array(
            'name' => 'extra_link',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('Extra links'),
            ),
        ));
        // link_1
        $this->add(array(
            'name' => 'link_1',
            'options' => array(
                'label' => __('Link 1'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // link_2
        $this->add(array(
            'name' => 'link_2',
            'options' => array(
                'label' => __('Link 2'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // link_3
        $this->add(array(
            'name' => 'link_3',
            'options' => array(
                'label' => __('Link 3'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // link_4
        $this->add(array(
            'name' => 'link_4',
            'options' => array(
                'label' => __('Link 4'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // link_5
        $this->add(array(
            'name' => 'link_5',
            'options' => array(
                'label' => __('Link 5'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // extra_image
        $this->add(array(
            'name' => 'extra_image',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('Extra images'),
            ),
        ));
        // image_1
        $this->add(array(
            'name' => 'image_1',
            'options' => array(
                'label' => __('Image 1'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // image_2
        $this->add(array(
            'name' => 'image_2',
            'options' => array(
                'label' => __('Image 2'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // image_3
        $this->add(array(
            'name' => 'image_3',
            'options' => array(
                'label' => __('Image 3'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // image_4
        $this->add(array(
            'name' => 'image_4',
            'options' => array(
                'label' => __('Image 4'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        ));
        // image_5
        $this->add(array(
            'name' => 'image_5',
            'options' => array(
                'label' => __('Image 5'),
            ),
            'attributes' => array(
                'type' => 'text',
            )
        )); */
        // Save
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => __('Submit'),
            )
        ));
    }
}   
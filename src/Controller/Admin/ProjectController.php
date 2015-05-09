<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\Portfolio\Controller\Admin;

use Pi;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Pi\File\Transfer\Upload;
use Module\Portfolio\Form\ProjectForm;
use Module\Portfolio\Form\ProjectFilter;

class ProjectController extends ActionController
{
    protected $ImagePrefix = 'project_';

    protected $projectColumns = array(
        'id', 'title', 'slug', 'service', 'technology', 'website', 'website_view', 
        'information', 'seo_title', 'seo_keywords', 'seo_description', 'time_create', 
        'time_update', 'uid', 'hits', 'image', 'path', 'status', 'point', 'count', 
        'favourite', 'commentby', 'comment', 'customer', 'version', 'size', 
        'link_1', 'link_2', 'link_3', 'link_4', 'link_5',
        'image_1', 'image_2', 'image_3', 'image_4', 'image_5'
    );

    public function indexAction()
    {
        // Get page
        $page = $this->params('page', 1);
        $module = $this->params('module');
        // Get info
        $list = array();
        $order = array('id DESC', 'time_create DESC');
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $limit = intval($this->config('admin_perpage'));
        $select = $this->getModel('project')->select()->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('project')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = Pi::api('project', 'portfolio')->canonizeProject($row);
        }
        // Set paginator
        $columns = array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)'));
        $select = $this->getModel('project')->select()->columns($columns);
        $count = $this->getModel('project')->selectWith($select)->current()->count;
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            'router'    => $this->getEvent()->getRouter(),
            'route'     => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params'    => array_filter(array(
                'module'        => $this->getModule(),
                'controller'    => 'project',
                'action'        => 'index',
            )),
        ));
        // Set view
        $this->view()->setTemplate('project_index');
        $this->view()->assign('projects', $list);
        $this->view()->assign('paginator', $paginator);
    }

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        $module = $this->params('module');
        $option = array();
        // Find Product
        if ($id) {
            $project = $this->getModel('project')->find($id)->toArray();
            if ($project['image']) {
                $thumbUrl = sprintf('upload/%s/thumb/%s/%s', $this->config('image_path'), $project['path'], $project['image']);
                $option['thumbUrl'] = Pi::url($thumbUrl);
                $option['removeUrl'] = $this->url('', array('action' => 'remove', 'id' => $project['id']));
            }
        }
        // Set form
        $form = new ProjectForm('project', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            // Set slug
            $slug = ($data['slug']) ? $data['slug'] : $data['title'];
            $filter = new Filter\Slug;
            $data['slug'] = $filter($slug);
            // Form filter
            $form->setInputFilter(new ProjectFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Tag
                if (!empty($values['tag'])) {
                    $tag = explode('|', $values['tag']);
                }
                // upload image
                if (!empty($file['image']['name'])) {
                    // Set upload path
                    $values['path'] = sprintf('%s/%s', date('Y'), date('m'));
                    $originalPath = Pi::path(sprintf('upload/%s/original/%s', $this->config('image_path'), $values['path']));
                    // Image name
                    $imageName = Pi::api('image', 'portfolio')->rename($file['image']['name'], $this->ImageCategoryPrefix, $values['path']);
                    // Upload
                    $uploader = new Upload;
                    $uploader->setDestination($originalPath);
                    $uploader->setRename($imageName);
                    $uploader->setExtension($this->config('image_extension'));
                    $uploader->setSize($this->config('image_size'));
                    if ($uploader->isValid()) {
                        $uploader->receive();
                        // Get image name
                        $values['image'] = $uploader->getUploaded('image');
                        // process image
                        Pi::api('image', 'portfolio')->process($values['image'], $values['path']);
                    } else {
                        $this->jump(array('action' => 'update'), __('Problem in upload image. please try again'));
                    }
                } elseif (!isset($values['image'])) {
                    $values['image'] = '';  
                }
                // Set just project fields
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->projectColumns)) {
                        unset($values[$key]);
                    }
                }
                // Set seo_title
                $title = ($values['seo_title']) ? $values['seo_title'] : $values['title'];
                $filter = new Filter\HeadTitle;
                $values['seo_title'] = $filter($title);
                // Set seo_keywords
                $keywords = ($values['seo_keywords']) ? $values['seo_keywords'] : $values['title'];
                $filter = new Filter\HeadKeywords;
                $filter->setOptions(array(
                    'force_replace_space' => (bool) $this->config('force_replace_space'),
                ));
                $values['seo_keywords'] = $filter($keywords);
                // Set seo_description
                $description = ($values['seo_description']) ? $values['seo_description'] : $values['title'];
                $filter = new Filter\HeadDescription;
                $values['seo_description'] = $filter($description);
                // Set time
                if (empty($values['id'])) {
                    $values['time_create'] = time();
                    $values['uid'] = Pi::user()->getId();
                }
                $values['time_update'] = time();
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('project')->find($values['id']);
                } else {
                    $row = $this->getModel('project')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Tag
                if (isset($tag) && is_array($tag) && Pi::service('module')->isActive('tag')) {
                    if (empty($values['id'])) {
                        Pi::service('tag')->add($module, $row->id, '', $tag);
                    } else {
                        Pi::service('tag')->update($module, $row->id, '', $tag);
                    }
                }
                // Add / Edit sitemap
                if (Pi::service('module')->isActive('sitemap')) {
                    // Set loc
                    $loc = Pi::url($this->url('portfolio', array(
                        'module'      => $module, 
                        'controller'  => 'project', 
                        'slug'        => $values['slug']
                    )));
                    // Update sitemap
                    Pi::api('sitemap', 'sitemap')->singleLink($loc, $row->status, $module, 'category', $row->id);            
                }
                // Jump
                $message = __('Project data saved successfully.');
                $url = array('action' => 'index');
                $this->jump($url, $message);
            }
        } else {
            if ($id) {
                $values = $this->getModel('project')->find($id)->toArray();
                // Get tag list
                if (Pi::service('module')->isActive('tag')) {
                    $tag = Pi::service('tag')->get($module, $values['id'], '');
                    if (is_array($tag)) {
                        $values['tag'] = implode('|', $tag);
                    }
                }
                // Set data
                $form->setData($values);
            }
        }
        $this->view()->setTemplate('project_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Project'));
    }

    public function removeAction()
    {
        // Get id and status
        $id = $this->params('id');
        // set project
        $project = $this->getModel('project')->find($id);
        // Check
        if ($project && !empty($id)) {
            // remove file
            /* $files = array(
                Pi::path('upload/' . $this->config('image_path') . '/original/' . $project->path . '/' . $project->image),
                Pi::path('upload/' . $this->config('image_path') . '/large/' . $project->path . '/' . $project->image),
                Pi::path('upload/' . $this->config('image_path') . '/medium/' . $project->path . '/' . $project->image),
                Pi::path('upload/' . $this->config('image_path') . '/thumb/' . $project->path . '/' . $project->image),
            );
            Pi::service('file')->remove($files); */
            // clear DB
            $project->image = '';
            $project->path = '';
            // Save
            if ($project->save()) {
                $message = sprintf(__('Image of %s removed'), $project->title);
                $status = 1;
            } else {
                $message = __('Image not remove');
                $status = 0;
            }
        } else {
            $message = __('Please select project');
            $status = 0;
        }
        return array(
            'status' => $status,
            'message' => $message,
        );
    }

    /* public function deleteAction()
    {
        // Get information
        $this->view()->setTemplate(false);
        $id = $this->params('id');
        $row = $this->getModel('project')->find($id);
        if ($row) {
            // Remove sitemap
            if (Pi::service('module')->isActive('sitemap')) {
                $loc = Pi::url($this->url('portfolio', array(
                        'module'      => $module, 
                        'controller'  => 'project',
                        'action'      => 'index',
                        'slug'        => $row->slug
                    )));
                Pi::api('sitemap', 'sitemap')->remove($loc);
            } 
            // Remove page
            $row->delete();
            $this->jump(array('action' => 'index'), __('This project deleted'));
        }
        $this->jump(array('action' => 'index'), __('Please select project'));
    } */
}
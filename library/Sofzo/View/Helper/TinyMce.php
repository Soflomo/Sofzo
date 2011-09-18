<?php

/**
 * This is free and unencumbered software released into the public domain.
 * 
 * Anyone is free to copy, modify, publish, use, compile, sell, or
 * distribute this software, either in source code form or as a compiled
 * binary, for any purpose, commercial or non-commercial, and by any
 * means.
 * 
 * In jurisdictions that recognize copyright laws, the author or authors
 * of this software dedicate any and all copyright interest in the
 * software to the public domain. We make this dedication for the benefit
 * of the public at large and to the detriment of our heirs and
 * successors. We intend this dedication to be an overt act of
 * relinquishment in perpetuity of all present and future rights to this
 * software under copyright law.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
 * OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * 
 * For more information, please refer to <http://unlicense.org/>
 * 
 * @category   Soflomo
 * @package    Soflomo_View
 * @copyright  Copyright (c) 2009-2011 Soflomo (http://www.soflomo.com)
 * @license    http://unlicense.org Unlicense
 */

/**
 * Soflomo_View_Helper_TinyMce
 * 
 * View helper for TinyMce initialization code
 * 
 * @category   Soflomo
 * @package    Soflomo_View
 * @author     Jurian Sluiman <jurian@soflomo.com>
 * @copyright  Copyright (c) 2009-2011 Soflomo (http://www.soflomo.com)
 * @license    http://unlicense.org Unlicense
 */
class Soflomo_View_Helper_TinyMce extends Zend_View_Helper_Abstract
{
    protected $_enabled = false;

    protected $_supported = array(
        'mode'      => array('textareas', 'specific_textareas', 'exact', 'none'),
        'theme'     => array('simple', 'advanced'),
        'format'    => array('html', 'xhtml'),
        'languages' => array('en'),
        'plugins'   => array('style', 'layer', 'table', 'save',
                             'advhr', 'advimage', 'advlink', 'emotions',
                             'iespell', 'insertdatetime', 'preview', 'media',
                             'searchreplace', 'print', 'contextmenu', 'paste',
                             'directionality', 'fullscreen', 'noneditable', 'visualchars',
                             'nonbreaking', 'xhtmlxtras', 'imagemanager', 'filemanager','template'));

    protected $_config = array('mode'  =>'textareas',
                               'theme' => 'simple',
                               'element_format' => 'html');
    protected $_scriptPath;
    protected $_scriptFile;
    protected $_useCompressor = false;

    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (!method_exists($this, $method)) {
            throw new Zend_View_Exception('Invalid tinyMce property');
        }
        $this->$method($value);
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        if (!method_exists($this, $method)) {
            throw new Zend_View_Exception('Invalid tinyMce property');
        }
        return $this->$method();
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            } else {
                $this->_config[$key] = $value;
            }
        }
        return $this;
    }

    public function tinyMce ()
    {
        return $this;
    }

    public function setScriptPath ($path)
    {
        $this->_scriptPath = rtrim($path,'/');
        return $this;
    }

    public function setScriptFile ($file)
    {
        $this->_scriptFile = (string) $file;
    }

    public function setCompressor ($switch)
    {
        $this->_useCompressor = (bool) $switch;
        return $this;
    }

    public function render()
    {
        if (false === $this->_enabled) {
            $this->_renderScript();
            $this->_renderCompressor();
            $this->_renderEditor();
        }
        $this->_enabled = true;
    }

    protected function _renderScript ()
    {
        $this->view->headScript()->appendFile($this->_scriptPath . '/' . $this->_scriptFile);
        return $this;
    }

    protected function _renderCompressor ()
    {
        if (false === $this->_useCompressor) {
            return;
        }

        if (isset($this->_config['plugins']) && is_array($this->_config['plugins'])) {
            $plugins = $this->_config['plugins'];
        } else {
            $plugins = $this->_supported['plugins'];
        }

        $script = 'tinyMCE_GZ.init({' . PHP_EOL
                . 'themes: "' . implode(',', $this->_supported['theme']) . '",' . PHP_EOL
                . 'plugins: "'. implode(',', $plugins) . '",' . PHP_EOL
                . 'languages: "' . implode(',', $this->_supported['languages']) . '",' . PHP_EOL
                . 'disk_cache: true,' . PHP_EOL
                . 'debug: false' . PHP_EOL
                . '});';

        $this->view->headScript()->appendScript($script);
        return $this;
    }

    protected function _renderEditor ()
    {
        $script = 'tinyMCE.init({' . PHP_EOL;

        $params = array();
        foreach ($this->_config as $name => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            
            if ($value !== "false" && $value !== "true") {
                $value = '"' . $value . '"';
            }
            $params[] = $name . ': ' . $value;
        }
        $script .= implode(',' . PHP_EOL, $params) . PHP_EOL;
        $script .= '});';

        $this->view->headScript()->appendScript($script);
        return $this;
    }
}
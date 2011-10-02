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
 * @package    Soflomo_Controller
 * @copyright  Copyright (c) 2009-2011 Soflomo (http://www.soflomo.com)
 * @license    http://unlicense.org Unlicense
 */

/**
 * Soflomo_Controller_Action_Helper_Https
 * 
 * Action helper to redirect a browser to http or https version of
 * the requested url. Actions marked as forcedHttp or forcedHttps
 * will be redirected. For all other actions, no redirect happens. 
 * 
 * Usage example:
 * <code>
 * public function init ()
 * {
 *   $this->_helper->getHelper('https')
 *                 ->forceHttps(array('foo', 'bar'))
 *                 ->forceHttp(array('baz', 'bat'));
 * }
 * </code>
 * 
 * @example    http://juriansluiman.nl/en/article/110/in-control-of-https-for-action-controllers
 * @category   Soflomo
 * @package    Soflomo_Controller
 * @author     Jurian Sluiman <jurian@soflomo.com>
 * @copyright  Copyright (c) 2009-2011 Soflomo (http://www.soflomo.com)
 * @license    http://unlicense.org Unlicense
 */
class Soflomo_Controller_Action_Helper_Https extends Zend_Controller_Action_Helper_Abstract
{
    protected $_https = array();
    protected $_http = array();
    
    public function forceHttps (array $actions)
    {
        $this->_https = $this->_filter($actions);
        return $this;
    }
 
    public function forceHttp (array $actions)
    {
        $this->_http = $this->_filter($actions);
        return $this;
    }

    public function preDispatch ()
    {
        $action = $this->getRequest()->getActionName();
        $scheme = $this->getRequest()->getScheme();
        $url    = $this->getFrontController()->getParam('domainName')
                . $this->getRequest()->getRequestUri();

        if (in_array($action, $this->_https) && $scheme === 'http') {
            $this->getActionController()
                 ->getHelper('redirector')
                 ->gotoUrlAndExit('https://' . $uri);
        } elseif (in_array($action, $this->_http) && $scheme === 'https') {
            $this->getActionController()
                 ->getHelper('redirector')
                 ->gotoUrlAndExit('http://' . $url);
        }
    }

    protected function _filter (array $actions)
    {
        $methods   = get_class_methods($this->getActionController());
        $actionKey = ucfirst($this->getRequest()->getActionKey());
        
        foreach ($actions as $key => $action) {
            $action = $action . $actionKey;
            if (!in_array($action, $methods)) {
                unset($actions[$key]);
            }
        }

        return $actions;
    }
}

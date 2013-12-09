<?php
/**
 * Classe responsável por encurtar a URL
 * @todo Classe responsável por encurtar a URL
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage DTUXBase\View\Helper
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 05/11/13 11:10
 */

namespace DTUXBase\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;
class AbsoluteUrl extends AbstractHelper
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function __invoke()
    {
        return $this->request->getUri();
    }
}
<?php
namespace DTUXBase\Document\Interfaces;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Interface reponsavél por obter a conectividade via SM e repassar para o controller
 * @todo Interface reponsavél por obter a conectividade via SM e repassar para o controller
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage Document\Interface
 * @url http://www.chrisgahlert.com/blog/2012-09-20/zend-framework-2-injecting-mongodb-odm-into-controllers-using-initializers/
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 07/11/13 09:49
 * @example
 */
interface DocumentManagerAwareInterface
{
    public function setDocumentManager(DocumentManager $dm);
    /**
     * @example
     *
      class IndexController extends AbstractActionController implements \DTUXBase\Document\Interfaces\DocumentManagerAwareInterface
    {

        private $dm
        public function setDocumentManager(DocumentManager $dm)
        {
            $this->dm = $dm
        }
    }
     */
}
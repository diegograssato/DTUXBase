<?PHP
namespace DTUXBase\Form\Filter;

use Zend\Filter\FilterInterface;

class DataTransformer implements FilterInterface
{

    protected $manager;
    protected $entity;

    public function __construct($manager, string $entity = null){
        $this->manager = $manager;
        $this->entity = $entity;

    }

    public function filter($id)
    {
        $obj = null;
        if(! is_null( $id ) )
            $obj = $this->getManager()->getReference($this->entity, $id);
           // $obj = $this->getManager()->find($this->getEntity(), $id);

        return $obj;
    }


    /**
     * Gets the value of manager.
     *
     * @return mixed
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Sets the value of manager.
     *
     * @param mixed $manager the manager
     *
     * @return self
     */
    public function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Gets the value of entity.
     *
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Sets the value of entity.
     *
     * @param mixed $entity the entity
     *
     * @return self
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }
}
# Object Creator
The Object Creator can instantiate and initialize objects dynamically. It can be used to instantiate and initialize objects from classes which represent a data structure. It is possible to add instantiators which know how to instantiate objects of a specific class. In this way it can be ensured that all objects are getting instantiated properly. 

### Usage

```php
use Drieschel\ObjectCreator\ObjectCreator;
use Drieschel\ObjectCreator\Instantiator\DateTimeInstantiator;

// Example data from various source
$data = [
    'int' => 42,
    'float' => 0.7,
    'string' => 'yeeeehaaa',
    'bool' => true,
    'datetime' => 'Sat Jan 30 1988 12:22:22 GMT+0100'
];

// Example data structure class
class Entity {
    protected int $int;
    
    protected float $float;
    
    protected string $string;
    
    protected bool $bool;
    
    protected \DateTimeImmutable $datetime;
    
    public function __construct(int $int, string $string) {
        $this->int = $int;
        $this->string = $string;
    }
    
    public function setFloat(float $float): self
    {
        $this->float = $float;
        return $this;
    }
        
    public function setBool(bool $bool): self
    {
        $this->bool = $bool;
        return $this;
    }
        
    public function setDatetime(\DateTimeImmutable $datetime): self
    {
        $this->datetime = $datetime;
        return $this;
    }
}

// Instantiate the creator
$creator = new ObjectCreator();

// Registering a DateTime instantiator which knows how to
// instantiate an object that implements the DateTimeInterface 
$creator->registerInstantiator(new DateTimeInstantiator());

// Instantiate an object
$entity = $creator->instantiate(Entity::class, $data);

// Initialize an object
$creator->initialize($entity, $data);

// Or instantiate and initialize an object together 
$entity = $creator->instantiateAndInitialize(Entity::class, $data);
```
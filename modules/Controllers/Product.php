<?php
header('Content-Type: text/html; charset=utf-8');
class Product
{
    public $name, $age;

    function __construct($name, $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    function getInfo()
    {
        echo "Имя: $this->name ; Возраст: $this->age <br>";
    }
}

$user1 = new Product("Andrew", 30);
$user1->getInfo();
?>
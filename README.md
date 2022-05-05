# Formgen4Symfony - A Typesdcript Library
![php version](https://img.shields.io/badge/php-8.0-blue.svg)
![license](https://img.shields.io/badge/license-MIT-green.svg)
## About
An easy way to create forms with symfony entities.

Gr.O.M.
## Requirements
php >= 8.0
## Installation
```bash
  composer require mudde/formgen4symfony
```
## Examples
### Tax
**Entity:**
```php
<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Mudde\Formgen4Symfony\Annotation\FormField;
use Mudde\Formgen4Symfony\Annotation\Formgen;
use Mudde\Formgen4Symfony\Annotation\FormIgnore;
use App\Repository\TaxRepository;

#[ORM\Entity(repositoryClass=TaxRepository::class)]]
#[Formgen('tax',
  [
    'data'=> ['_type'=>'Api', 'url'=>'api/taxes'  ], 
    'languages'=>['nl'], 
    'buttons'=>[["_type"=> "Submit","label"=> "Opslaan"]], 'builders'=>[["_type"=> "TabBuilder"]]])
  ]
#[ApiResource('Tax')]
class Tax
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type="integer")]
    #[FormField('id', 'id', [], ['readonly' => true])]
    private ?int $id;

    #[ORM\Column(type="string", length=255)]
    #[FormField('name', 'name', [], [])]
    private ?string $name;

    #[ORM\Column(type="string", precision=2, scale=2)]
    #[FormField('percentage', 'Tax percentage', [], ['_type' => 'Number'])]
    private float $percentage;
    
    #[ORM\OneToMany(targetEntity=Product::class, mappedBy="tax")]
    #[FormIgnore()]
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPercentage(): ?string
    {
        return $this->percentage;
    }

    public function setPercentage(string $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setTax($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            if ($product->getTax() === $this) {
                $product->setTax(null);
            }
        }

        return $this;
    }
}
```
**PHP:**

To generate JSON form an entity:
```php
FormgenHelper::generateForm('App\\Entity\\Tax');
```
**Output:**

Generates this form in javascript
![Generated form](readme.md/tax-form.png?raw=true)
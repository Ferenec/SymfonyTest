<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductDataRepository")
 */
class ProductData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $intProductDataId;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $strProductName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $strProductDesc;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     */
    private $strProductCode;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dtmAdded;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dtmDiscontinued;

    /**
     * @ORM\Column(type="datetime")
     */
    private $stmTimestamp;

    public function __construct($data = [])
    {
        $this->strProductName = $data['name'];
        $this->strProductCode = $data['code'];
        $this->strProductDesc = $data['desc'];
        if ($data['disc']){
            $this->setDtmDiscontinued();
        }
        $this->setDtmAdded();
        $this->setStmTimestamp();
    }

    public function setDtmAdded()
    {
        $this->dtmAdded = new \DateTime();
    }

    public function setDtmDiscontinued()
    {
        $this->dtmDiscontinued = new \DateTime();
    }

    public function setStmTimestamp()
    {
        $this->stmTimestamp = new \DateTime();
    }
}

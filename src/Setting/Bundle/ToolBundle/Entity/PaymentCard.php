<?php

namespace Setting\Bundle\ToolBundle\Entity;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentCard
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PaymentCard
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="paymentCard")
	 */
	protected $sales;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="status", type="boolean")
	 */
	private $status;


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return PaymentCard
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set status
	 *
	 * @param boolean $status
	 *
	 * @return PaymentCard
	 */
	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * Get status
	 *
	 * @return boolean
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return Sales
	 */
	public function getSales()
	{
		return $this->sales;
	}


	/**
	 * @return Invoice
	 */
	public function getHmsInvoices()
	{
		return $this->hmsInvoices;
	}

	/**
	 * @return MedicineSales
	 */
	public function getMedicineSales()
	{
		return $this->medicineSales;
	}

	/**
	 * @return DpsInvoice
	 */
	public function getDpsInvoices()
	{
		return $this->dpsInvoices;
	}

	/**
	 * @return DpsTreatmentPlan
	 */
	public function getDpsTreatmentPlans()
	{
		return $this->dpsTreatmentPlans;
	}

	/**
	 * @return BusinessInvoice
	 */
	public function getBusinessInvoice()
	{
		return $this->businessInvoice;
	}

	/**
	 * @return HotelInvoice
	 */
	public function getHotelInvoice() {
		return $this->hotelInvoice;
	}

}


<?php
namespace Lists\Entity;

class Domain
{
	/**
	 * @var string
	 */
	protected $resource;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @param string $resource
	 * @return Domain
	 */
	public function setResource($resource)
	{
		$this->resource = $resource;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @param string $description
	 * @return Domain
	 */
	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
}
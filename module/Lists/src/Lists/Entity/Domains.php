<?php
namespace Lists\Entity;

class Domains
{
	/**
	 * @var string
	 */
	protected $domains;

	/**
	 * @param string $domains
	 * @return Domain
	 */
	public function setDomains($domains)
	{
		$this->domains = $domains;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDomains()
	{
		return $this->domains;
	}

}
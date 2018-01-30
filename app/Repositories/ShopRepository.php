<?php

namespace App\Repositories;

use App\Shop;


class ShopRepository
{

    protected $shop;

    public function __construct(shop $shop)
	{
		$this->shop = $shop;
	}



public function all()
	{
		return $this->shop->all();
	}


	public function getById($id)
	{
		return $this->shop->findOrFail($id);
	}

}
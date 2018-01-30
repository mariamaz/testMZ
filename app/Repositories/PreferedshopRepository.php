<?php

namespace App\Repositories;

use app\Preferedshop;

Class PreferedshopRepository
{

   protected $PreferedshopPreferedshop;

    public function __construct(Preferedshop $Preferedshop)
	{
		$this->prefered_shop = $prefered_shop;
	}

	private function save(Preferedshop $Preferedshop)
	{
		$Prefered_shop->created_by = Auth::user()->id;
		$Prefered_shop->shop_id = $inputs['shop_id'];	
		$Prefered_shop->save();
	}

	
	public function all()
	{
		return $this->Preferedshop->all();
	}

	public function store(Array $inputs)
	{
		$Preferedshop = new $this->Preferedshop;		

		$this->save($Preferedshop, $inputs);

		return $Preferedshop;
	}


	public function getById($id)
	{
		return $this->Preferedshop->findOrFail($id);
	}

	public function update($id, Array $inputs)
	{
		$this->save($this->getById($id), $inputs);
	}

	public function destroy($id)
	{
		$this->getById($id)->delete();
	}

}
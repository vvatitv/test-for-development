<?php

namespace App\Filters;

use App\Filters\QueryFilter;
use Illuminate\Support\Str;
use App\Models\Organization;

class OrganizationFilters extends QueryFilter
{
	public function filter($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}

		if( is_array($param) )
		{
			foreach ($param as $type => $value)
			{
				if( method_exists($this, $type) )
				{
					call_user_func_array([$this, $type], [$value]);
				}
			}
		}
	}

	public function search($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}

		if( !empty($param['text']) )
		{
			$this->builder->search(
				$search = $param['text'],
				$restriction = ( isset($param['restriction']) && !empty($param['restriction']) ? $param['restriction'] : 8 ),
				$restriction = 0,
				$threshold = true,
				$entireText = ( isset($param['strict']) && $param['strict'] == true ? true : false ),
				$entireTextOnly = ( isset($param['strict']) && $param['strict'] == true ? true : false )
			);
		}
	}

	public function id($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}

		if( is_array($param) )
		{
			if( !empty($param['text']) )
			{
				if( empty($param['like']) )
				{
					$param['like'] = '%{text}%';
				}

				if( !isset($param['or']) || isset($param['or']) && !$param['or'] )
				{
					$this->builder->where('id', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
				}else{
					$this->builder->orWhere('id', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
				}
			}

		}else{

			if( !empty( (int) $param))
			{
				$this->builder->where('id', (int) $param);
			}

		}
	}

	public function name($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}

		if( is_array($param) )
		{
			if( !empty($param['text']) )
			{
				if( empty($param['like']) )
				{
					$param['like'] = '%{text}%';
				}

				if( !isset($param['or']) || isset($param['or']) && !$param['or'] )
				{
					$this->builder->where('name', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
				}else{
					$this->builder->orWhere('name', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->where('name', $param);
			}

		}
	}

	public function abbreviation($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}

		if( is_array($param) )
		{
			if( !empty($param['text']) )
			{
				if( empty($param['like']) )
				{
					$param['like'] = '%{text}%';
				}

				if( !isset($param['or']) || isset($param['or']) && !$param['or'] )
				{
					$this->builder->where('abbreviation', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
				}else{
					$this->builder->orWhere('abbreviation', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->where('abbreviation', $param);
			}

		}
	}

    public function withCount($param)
    {
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}

		$this->builder->withCount($param);
    }
	
	public function orderBy($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}

		if( is_array($param) )
		{
			if( !empty($param['field']) )
			{
				$this->builder->orderBy($param['field'], ( !empty($param['type']) ? $param['type'] : 'asc' ));
			}

		}else{

			$this->builder->orderBy('id', 'asc');

		}
	}
	
	public function limit($param)
	{
		$param = (int) $param;

		if( !empty($param) && is_int($param) )
		{
			$this->builder->limit($param);
		}
	}
}
<?php

namespace App\Filters;

use App\Filters\QueryFilter;
use Illuminate\Support\Str;

class UserQuizFilters extends QueryFilter
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
				}
				else
				{
					$this->builder->orWhere('id', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
				}
			}

		}
		else
		{

			if( !empty( (int) $param))
			{
				$this->builder->where('id', (int) $param);
			}

		}
	}

	public function status_id($param)
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
					$this->builder->where('status_id', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
				}
				else
				{
					$this->builder->orWhere('status_id', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
				}
			}

		}
		else
		{

			if( !empty( (int) $param))
			{
				$this->builder->where('status_id', (int) $param);
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
				}
				else
				{
					$this->builder->orWhere('name', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
				}
			}

		}
		else
		{

			if( !empty($param) )
			{
				$this->builder->where('name', $param);
			}

		}
	}

	public function hash($param)
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
					$this->builder->whereHas(
						'quizze',
						function($query) use ($param)
						{
							return $query->where('hash', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
						}
					);
				}
				else
				{
					$this->builder->whereHas(
						'quizze',
						function($query) use ($param)
						{
							return $query->orWhere('hash', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
						}
					);
				}
			}

		}
		else
		{

			if( !empty($param) )
			{
				$this->builder->whereHas(
					'quizze',
					function($query) use ($param)
					{
						return $query->where('hash', $param);
					}
				);
			}

		}
	}

	public function organization($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}

		if( is_array($param) )
		{
			if( !empty($param['text']) )
			{
				if( !isset($param['or']) || isset($param['or']) && !$param['or'] )
				{

					$this->builder->whereHas('user.organization', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						return $query
									->where(function($q) use ($param){
										$q
											->where('name', 'like', (string) Str::replace('{text}', $param['text'], $param['like']))
											->orWhere('abbreviation', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}else{

					$this->builder->orWhereHas('user.organization', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						return $query
									->where(function($q) use ($param){
										$q
											->where('name', 'like', (string) Str::replace('{text}', $param['text'], $param['like']))
											->orWhere('abbreviation', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('user.organization', function($query) use ($param){
					return $query
								->where(function($q) use ($param){
									$q
										->where('name', $param)
										->orWhere('abbreviation', $param);
								});
				});
			}

		}
	}

	public function full_name($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}

		if( is_array($param) )
		{
			if( !empty($param['text']) )
			{
				if( !isset($param['or']) || isset($param['or']) && !$param['or'] )
				{
					$this->builder->whereHas('user.fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
						
						return $query
									->where(function($q) use($param){
										$Names = explode(' ', $param['text']);
										$NamesArr = [
											'first_name',
											'last_name',
											'middle_name'
										];
										foreach ($NamesArr as $key => $tagvalue)
										{
											if( $key == 0 )
											{
	
												$q
													->where(function($qu) use($Names, $param, $tagvalue, $key){
														
														foreach ($Names as $Nkey => $nameValue)
														{
															if( $Nkey == 0 )
															{
																$qu
																	->where(function($qnu) use($Names, $param, $tagvalue, $key, $Nkey, $nameValue){
	
																		if( !empty($param['like']) )
																		{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', 'like', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}else{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}
																	});
															}else{
																$qu
																	->where(function($qnu) use($Names, $param, $tagvalue, $key, $Nkey, $nameValue){
	
																		if( !empty($param['like']) )
																		{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', 'like', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}else{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}
																	});
															}
														}
	
													});
	
											}else{
	
												$q
													->orWhere(function($qu) use($Names, $param, $tagvalue, $key){
														
														foreach ($Names as $Nkey => $nameValue)
														{
															if( $Nkey == 0 )
															{
																$qu
																	->where(function($qnu) use($Names, $param, $tagvalue, $key, $Nkey, $nameValue){
	
																		if( !empty($param['like']) )
																		{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', 'like', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}else{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}
																	});
															}else{
																$qu
																	->orWhere(function($qnu) use($Names, $param, $tagvalue, $key, $Nkey, $nameValue){
	
																		if( !empty($param['like']) )
																		{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', 'like', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}else{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}
																	});
															}
														}
	
													});
	
											}
										}
									});
					});

				}else{

					$this->builder->orWhereHas('user.fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
						
						return $query
									->where(function($q) use($param){
										$Names = explode(' ', $param['text']);
										$NamesArr = [
											'first_name',
											'last_name',
											'middle_name'
										];
										foreach ($NamesArr as $key => $tagvalue)
										{
											if( $key == 0 )
											{
	
												$q
													->where(function($qu) use($Names, $param, $tagvalue, $key){
														
														foreach ($Names as $Nkey => $nameValue)
														{
															if( $Nkey == 0 )
															{
																$qu
																	->where(function($qnu) use($Names, $param, $tagvalue, $key, $Nkey, $nameValue){
	
																		if( !empty($param['like']) )
																		{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', 'like', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}else{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}
																	});
															}else{
																$qu
																	->where(function($qnu) use($Names, $param, $tagvalue, $key, $Nkey, $nameValue){
	
																		if( !empty($param['like']) )
																		{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', 'like', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}else{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}
																	});
															}
														}
	
													});
	
											}else{
	
												$q
													->orWhere(function($qu) use($Names, $param, $tagvalue, $key){
														
														foreach ($Names as $Nkey => $nameValue)
														{
															if( $Nkey == 0 )
															{
																$qu
																	->where(function($qnu) use($Names, $param, $tagvalue, $key, $Nkey, $nameValue){
	
																		if( !empty($param['like']) )
																		{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', 'like', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}else{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}
																	});
															}else{
																$qu
																	->orWhere(function($qnu) use($Names, $param, $tagvalue, $key, $Nkey, $nameValue){
	
																		if( !empty($param['like']) )
																		{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', 'like', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}else{
																			$qnu
																				->where('slug', $tagvalue)
																				->where('value', (string) Str::replace('{text}', $nameValue, $param['like']));
																		}
																	});
															}
														}
	
													});
	
											}
										}
									});
					});
				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('user.fields', function($query) use ($param){
					return $query
								->where(function($q) use($param){
									$q
										->whereIn('slug', ['first_name', 'last_name', 'middle_name'])
										->where('value', $param);
								});
				});
			}

		}
	}

	public function team($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}

		if( is_array($param) )
		{
			if( !empty($param['text']) )
			{
				if( !isset($param['or']) || isset($param['or']) && !$param['or'] )
				{

					$this->builder->whereHas('user.teams', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						if( empty($param['field']) )
						{
							$param['field'] = 'name';
						}
	
						return $query
									->where($param['field'], 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
					});

				}else{

					$this->builder->orWhereHas('user.teams', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						if( empty($param['field']) )
						{
							$param['field'] = 'name';
						}
	
						return $query
									->where($param['field'], 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
					});

				}
				
			}elseif( !empty($param['id']) ){

				$this->builder->whereHas('user.teams', function($query) use ($param){
					return $query
								->whereIn('id', $param['id']);
				});

			}

		}else{

			if( $param )
			{
				$this->builder->whereHas('user.teams', function($query) use ($param){
					return $query
								->where('name', $param);
				});
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

	public function status($param)
	{
		switch ($param)
		{
			case 1:
				$this->builder
							->onlyTrashed();
			break;
			case 2:
				$this->builder
							->withTrashed();
			break;
            default:
			break;
		}
	}
}
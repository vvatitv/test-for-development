<?php

namespace App\Filters;

use App\Filters\QueryFilter;
use Illuminate\Support\Str;
use App\Models\Team;

class TeamFilters extends QueryFilter
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
				$restriction = ( isset($param['restriction']) && !empty($param['restriction']) ? $param['restriction'] : 0 ),
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
					if( is_array($param['text']) )
					{
						$this->builder->whereIn('id', $param['text']);
					}else{
						$this->builder->where('id', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
					}
				}else{
					if( is_array($param['text']) )
					{
						$this->builder->orWhereIn('id', $param['text']);
					}else{
						$this->builder->orWhere('id', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
					}
				}
				
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->where('id', $param);
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

					$this->builder->whereHas('organization', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						return $query
									->where(function($query) use ($param){
										if( is_array($param['text']) )
										{
											$query
												->whereIn('name', $param['text'])
												->orWhereIn('abbreviation', $param['text']);
										}else{
											$query
												->where('name', 'like', (string) Str::replace('{text}', $param['text'], $param['like']))
												->orWhere('abbreviation', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
										}
									});
					});

				}else{

					$this->builder->orWhereHas('organization', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						return $query
									->where(function($query) use ($param){

										if( is_array($param['text']) )
										{
											$query
												->whereIn('name', $param['text'])
												->orWhereIn('abbreviation', $param['text']);
										}else{
											$query
												->where('name', 'like', (string) Str::replace('{text}', $param['text'], $param['like']))
												->orWhere('abbreviation', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
										}
									});
					});

				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('organization', function($query) use ($param){
					return $query
								->where(function($query) use ($param){
									$query
										->where('name', $param)
										->orWhere('abbreviation', $param);
								});
				});
			}

		}
	}

	public function organization_id($param)
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

					$this->builder->whereHas('organization', function($query) use ($param){
						$query->where('id', $param['text']);

						return $query;
					});

				}
				else
				{

					$this->builder->orWhereHas('organization', function($query) use ($param){
						$query->where('id', $param['text']);

						return $query;
					});

				}
			}

		}
		else
		{

			if( !empty($param) )
			{
				$this->builder->whereHas('organization', function($query) use ($param){
					$query->where('id', $param);

					return $query;
				});
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
					if( is_array($param['text']) )
					{
						$this->builder->whereIn('name', $param['text']);
					}else{
						$this->builder->where('name', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
					}
				}else{
					if( is_array($param['text']) )
					{
						$this->builder->orWhereIn('name', $param['text']);
					}else{
						$this->builder->orWhere('name', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
					}
				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->where('name', $param);
			}
		}
	}

	public function task($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}
		
		if( !empty($param) )
		{
			$this->builder->whereHas('tasks', function($query) use ($param){
				return $query
							->where('slug', $param);
			});
		}

	}

	public function track($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}
		
		if( !empty($param) )
		{
			$this->builder->whereHas('tracks', function($query) use ($param){
				return $query
							->where('hash', $param);
			});
		}

	}

	public function lead($param)
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
					
					$this->builder->whereHas('leads', function($q) use ($param){

						$q->whereHas('leadIn')->whereHas('fields', function($query) use ($param){

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

					});

				}else{

					$this->builder->orWhereHas('leads', function($qul) use ($param){

						$qul->whereHas('leadIn')->orWhereHas('fields', function($query) use ($param){

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

					});

				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('leads', function($query) use ($param){
					return $query
								->with(['fields'])
								->where('id', $param);
				});
			}

		}
	}

	public function step($param)
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
					
					$this->builder->whereHas('steps', function($query) use ($param){

						if( is_array($param['text']) )
						{
							return $query
										->whereIn('id', $param['text']);
						}else{
							return $query
										->where('id', $param['text']);
						}

					});

				}else{

					$this->builder->orWhereHas('steps', function($query) use ($param){

						if( is_array($param['text']) )
						{
							return $query
										->whereIn('id', $param['text']);
						}else{
							return $query
										->where('id', $param['text']);
						}
						
					});

				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('steps', function($query) use ($param){
					return $query
								->where('id', $param);
				});
			}

		}
	}

	public function description($param)
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
					if( is_array($param['text']) )
					{
						$this->builder->whereIn('description', $param['text']);
					}else{
						$this->builder->where('description', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
					}
				}else{
					if( is_array($param['text']) )
					{
						$this->builder->orWhereIn('description', $param['text']);
					}else{
						$this->builder->orWhere('description', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
					}
				}				
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->where('description', $param);
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
	
	// public function limit($param)
	// {
	// 	$param = (int) $param;

	// 	if( !empty($param) && is_int($param) )
	// 	{
	// 		$this->builder->limit($param);
	// 	}
	// }
}
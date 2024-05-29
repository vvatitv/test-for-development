<?php

namespace App\Filters;

use App\Filters\QueryFilter;
use Illuminate\Support\Str;
use App\Models\User;

class UserFilters extends QueryFilter
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
				$restriction = ( isset($param['restriction']) && !empty($param['restriction']) ? $param['restriction'] : 700 ),
				$threshold = true,
				$entireText = ( isset($param['strict']) && $param['strict'] == true ? true : false ),
				$entireTextOnly = ( isset($param['strict']) && $param['strict'] == true ? true : false )
			);
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
									->where(function($q) use ($param){
										$q
											->where('name', 'like', (string) Str::replace('{text}', $param['text'], $param['like']))
											->orWhere('abbreviation', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}else{

					$this->builder->orWhereHas('organization', function($query) use ($param){

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
				$this->builder->whereHas('organization', function($query) use ($param){
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

		}
		else
		{

			if( !empty((int) $param) )
			{
				$this->builder->where('id', (int) $param);
			}

		}
	}

	public function sex($param)
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

					$this->builder->whereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						return $query
									->where(function($q) use ($param){
										$q
											->whereIn('slug', ['sex'])
											->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}else{

					$this->builder->orWhereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						return $query
									->where(function($q) use ($param){
										$q
											->whereIn('slug', ['sex'])
											->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('fields', function($query) use ($param){
					return $query
								->where(function($q) use ($param){
									$q
										->whereIn('slug', ['sex'])
										->where('value', $param);
								});
				});
			}

		}
	}

	public function birth_date($param)
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

					$this->builder->whereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
						
						return $query
									->where(function($q) use ($param){
										$q
											->whereIn('slug', ['birth_date'])
											->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}else{

					$this->builder->orWhereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
						
						return $query
									->where(function($q) use ($param){
										$q
											->whereIn('slug', ['birth_date'])
											->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('fields', function($query) use ($param){
					return $query
								->where(function($q) use ($param){
									$q
										->whereIn('slug', ['birth_date'])
										->where('value', $param);
								});
				});
			}

		}
	}

	public function job_position($param)
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

					$this->builder->whereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
						
						return $query
									->where(function($q) use($param){
										$q
											->whereIn('slug', ['job_position'])
											->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}else{

					$this->builder->orWhereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
						
						return $query
									->where(function($q) use($param){
										$q
											->whereIn('slug', ['job_position'])
											->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('fields', function($query) use ($param){
					return $query
								->where(function($q) use($param){
									$q
										->whereIn('slug', ['job_position'])
										->where('value', $param);
								});
				});
			}

		}
	}

	public function job_experience($param)
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

					$this->builder->whereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						
						return $query
									->where(function($q) use ($param){
										$q
											->whereIn('slug', ['job_experience'])
											->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}else{

					$this->builder->orWhereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						
						return $query
									->where(function($q) use ($param){
										$q
											->whereIn('slug', ['job_experience'])
											->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('fields', function($query) use ($param){
					return $query
								->where(function($q) use($param){
									$q
										->whereIn('slug', ['job_experience'])
										->where('value', $param);
								});
				});
			}

		}
	}

	public function telephone($param)
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

					$this->builder->whereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						return $query
									->where(function($q) use($param){
										$q
											->whereIn('slug', ['telephone'])
											->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}else{

					$this->builder->orWhereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						return $query
									->where(function($q) use($param){
										$q
											->whereIn('slug', ['telephone'])
											->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
					});

				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('fields', function($query) use ($param){
					return $query
								->where(function($q) use($param){
									$q
										->whereIn('slug', ['telephone'])
										->where('value', $param);
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
					$this->builder->whereHas('fields', function($query) use ($param){

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

					$this->builder->orWhereHas('fields', function($query) use ($param){

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
				$this->builder->whereHas('fields', function($query) use ($param){
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

	public function first_name($param)
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

					$this->builder->whereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
						
						return $query
									->where(function($q) use($param){
										$q
											->whereIn('slug', ['first_name'])
											->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
	
					});

				}else{

					$this->builder->orWhereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
						
						return $query
									->where(function($q) use($param){
										$q
											->whereIn('slug', ['first_name'])
											->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
	
					});

				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('fields', function($query) use ($param){
					return $query
								->where(function($q) use($param){
									$q
										->whereIn('slug', ['first_name'])
										->where('value', $param);
								});
				});
			}
		}
	}

	public function last_name($param)
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

					$this->builder->whereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						return $query
									->where(function($q) use($param){
										$q
										->whereIn('slug', ['last_name'])
										->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
	
					});

				}else{

					$this->builder->orWhereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						return $query
									->where(function($q) use($param){
										$q
										->whereIn('slug', ['last_name'])
										->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
	
					});

				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('fields', function($query) use ($param){
					return $query
								->where(function($q) use($param){
									$q
									->whereIn('slug', ['last_name'])
									->where('value', $param);
								});
				});
			}

		}
	}

	public function middle_name($param)
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

					$this->builder->whereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						return $query
									->where(function($q) use($param){
										$q
										->whereIn('slug', ['middle_name'])
										->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
	
					});

				}else{

					$this->builder->orWhereHas('fields', function($query) use ($param){

						if( empty($param['like']) )
						{
							$param['like'] = '%{text}%';
						}
	
						return $query
									->where(function($q) use($param){
										$q
										->whereIn('slug', ['middle_name'])
										->where('value', 'like', (string) Str::replace('{text}', $param['text'], $param['like']));
									});
	
					});
				}
			}

		}else{

			if( !empty($param) )
			{
				$this->builder->whereHas('fields', function($query) use ($param){
					return $query
								->where(function($q) use($param){
									$q
									->whereIn('slug', ['middle_name'])
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

					$this->builder->whereHas('teams', function($query) use ($param){

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

					$this->builder->orWhereHas('teams', function($query) use ($param){

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

				$this->builder->whereHas('teams', function($query) use ($param){
					return $query
								->whereIn('id', $param['id']);
				});

			}

		}else{

			if( $param )
			{
				$this->builder->whereHas('teams', function($query) use ($param){
					return $query
								->where('name', $param);
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

		if( !empty($param) )
		{
			$this->builder->whereHas(
				'teams',
				function($query) use ($param)
				{
					return $query
								->whereHas(
									'steps',
									function($q) use ($param)
									{
										return $q->where('id', $param);
									}
								);
				}
			);
		}
	}

	public function isLead($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}

		switch ($param)
		{
			case true:

				$this->builder->whereHas('leadIn');

			break;
			case false:

				$this->builder->doesntHave('leadIn');

			break;
			default:
			break;
		}

	}

	public function lead($param)
	{
		if( \App\Libraries\Helper\JsonHelper::isJSON($param) )
		{
			$param = json_decode($param, true);
		}

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
					$this->builder->whereHas('leadIn')->whereHas('fields', function($query) use ($param){

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

					$this->builder->whereHas('leadIn')->orWhereHas('fields', function($query) use ($param){

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
				$this->builder->whereHas('leadIn')->whereHas('fields', function($query) use ($param){
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

	public function status($param)
	{
		switch ($param)
		{
			case -2:
				$this->builder
							->onlyTrashed();
			break;
			case -1:
				$this->builder
							->withTrashed();
			break;
			case 1:
				$this->builder
							->whereHas('roles', function($query) use ($param){
								return $query->where('name', 'member');
							})
							->whereNotNull('email_verified_at');
			break;
			case 2:
				$this->builder
							->onlyTrashed()
							->whereHas('roles', function($query) use ($param){
								return $query->where('name', 'member');
							});
			break;
			case 3:
				$this->builder
							->whereHas('roles', function($query) use ($param){
								return $query->where('name', 'member');
							})
							->whereNull('email_verified_at');
			break;
			case 0:
			default:
				$this->builder
							->withTrashed()
							->whereHas('roles', function($query) use ($param){
								return $query->where('name', 'member');
							});
			break;
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
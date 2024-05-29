<?php

namespace App\Traits\Users;

use Illuminate\Support\Facades\Cache;

trait FieldTrait
{
    protected $noFields = true;
    protected $fieldsLists = [];

    public function getFirstNameRawAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'first_name')->count() ? null : $rows->where('slug', 'first_name')->first();
    }

    public function getFirstNameAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'first_name')->count() ? null : $rows->where('slug', 'first_name')->first()->pivot->value;
    }

    public function getLastNameRawAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'last_name')->count() ? null : $rows->where('slug', 'last_name')->first();
    }

    public function getLastNameAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'last_name')->count() ? null : $rows->where('slug', 'last_name')->first()->pivot->value;
    }

    public function getMiddleNameRawAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'middle_name')->count() ? null : $rows->where('slug', 'middle_name')->first();
    }

    public function getMiddleNameAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'middle_name')->count() ? null : $rows->where('slug', 'middle_name')->first()->pivot->value;
    }

    public function getFullNameAttribute()
    {
        $arr = collect([]);
        $rows = $this->fields;
        $farrs = [
            'last_name',
            'first_name',
            'middle_name',
        ];

        if( empty($farrs) )
        {
            return null;
        }

        foreach ($farrs as $key => $field)
        {
            switch ($field)
            {
                default:
                
                    if( $rows->where('slug', $field)->count() )
                    {
                        $arr->push($rows->where('slug', $field)->first()->pivot->value);
                    }

                break;
            }

        }

        return $arr->count() ? $arr->implode(' ') : null;
    }

    public function getFullNameShortAttribute()
    {
        $arr = collect([]);
        $rows = $this->fields;
        $farrs = [
            'last_name',
            'first_name',
            'middle_name',
        ];

        if( empty($farrs) )
        {
            return null;
        }

        foreach ($farrs as $key => $field)
        {
            switch ($field)
            {
                case 'last_name':

                    if( $rows->where('slug', $field)->count() )
                    {
                        $arr->push($rows->where('slug', $field)->first()->pivot->value);
                    }

                break;
                case 'first_name':

                    if( $rows->where('slug', $field)->count() )
                    {
                        $arr->push(
                            \Illuminate\Support\Str::of(
                                $rows->where('slug', $field)->first()->pivot->value
                            )->substr(0, 1)->finish('.')
                        );
                    }

                break;
                case 'middle_name':

                    if( $rows->where('slug', $field)->count() )
                    {
                        $arr->push(
                            \Illuminate\Support\Str::of(
                                $rows->where('slug', $field)->first()->pivot->value
                            )->substr(0, 1)->finish('.')
                        );
                    }

                break;
                default:
                
                    if( $rows->where('slug', $field)->count() )
                    {
                        $arr->push($rows->where('slug', $field)->first()->pivot->value);
                    }

                break;
            }

        }

        return $arr->count() ? $arr->implode(' ') : null;
    }

    public function getBirthDateRawAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'birth_date')->count() ? null : $rows->where('slug', 'birth_date')->first();
    }

    public function getBirthDateAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'birth_date')->count() ? null : $rows->where('slug', 'birth_date')->first()->pivot->value;
    }

    public function getJobPositionRawAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'job_position')->count() ? null : $rows->where('slug', 'job_position')->first();
    }

    public function getJobPositionAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'job_position')->count() ? null : $rows->where('slug', 'job_position')->first()->pivot->value;
    }

    public function getJobExperienceRawAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'job_experience')->count() ? null : $rows->where('slug', 'job_experience')->first();
    }

    public function getJobExperienceAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'job_experience')->count() ? null : $rows->where('slug', 'job_experience')->first()->pivot->value;
    }

    public function getTelephoneRawAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'telephone')->count() ? null : $rows->where('slug', 'telephone')->first();
    }

    public function getTelephoneAttribute()
    {
        $rows = $this->fields;
        return !$rows->where('slug', 'telephone')->count() ? null : $rows->where('slug', 'telephone')->first()->pivot->value;
    }

    public function toArray()
    {
        $data = parent::toArray();

        if( $this->noFields )
        {
            return $data;
        }

        $fields = $this->fields;
        
        if( !empty($fields) )
        {
            foreach($fields as $key => $field)
            {
                if( empty($this->fieldsLists) )
                {
                    if( empty($field->options) )
                    {
                        $data[$field->slug] = $field;
                    }else{

                        if( !empty($field->options['relation']) )
                        {
                            $model = call_user_func($field->options['relation']['model'] . '::get');
                            $data[$field->slug] = $model->where($field->options['relation']['identifier'], $field->pivot->value)->first();
                        }else{
                            $data[$field->slug] = $field;
                        }
                    }
                }else{

                    if( in_array($field->slug, $this->fieldsLists) )
                    {
                        if( empty($field->options) )
                        {
                            $data[$field->slug] = $field;
                        }else{

                            if( !empty($field->options['relation']) )
                            {
                                $model = call_user_func($field->options['relation']['model'] . '::get');
                                $data[$field->slug] = $model->where($field->options['relation']['identifier'], $field->pivot->value)->first();
                            }else{
                                $data[$field->slug] = $field;
                            }

                        }
                    }
                }
                
            }

            $full_name = collect([]);

            if( !empty($data['last_name']) )
            {
                $full_name->push($data['last_name']->pivot->value);
            }

            if( !empty($data['first_name']) )
            {
                $full_name->push($data['first_name']->pivot->value);
            }

            if( !empty($data['middle_name']) )
            {
                $full_name->push($data['middle_name']->pivot->value);
            }

            if( $full_name->count() )
            {
                $data['full_name'] = $full_name->implode(' ');
            }

            $short_full_name = collect([]);

            if( !empty($data['last_name']) )
            {
                $short_full_name->push($data['last_name']->pivot->value . ' ');
            }

            if( !empty($data['first_name']) )
            {
                $short_full_name->push(\Illuminate\Support\Str::of($data['first_name']->pivot->value)->substr(0, 1)->finish('.'));
            }

            if( !empty($data['middle_name']) )
            {
                $short_full_name->push(\Illuminate\Support\Str::of($data['middle_name']->pivot->value)->substr(0, 1)->finish('.'));
            }

            if( $short_full_name->count() )
            {
                $data['full_name_short'] = $short_full_name->implode('');
            }
        }

        return $data;
    }

    public function withoutFiedls()
    {
        $this->noFields = true;
        return $this;
    }

    public function withFields($lists = [])
    {
        $this->noFields = false;
        $this->fieldsLists = $lists;
        return $this;
    }
}
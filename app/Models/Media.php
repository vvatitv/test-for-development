<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;
    use Sluggable;

	protected $table = 'media';
    
    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'name',
        'parent_id',
        'type',
        'thumbnails',
        'ordering',
        'mediatable_type',
        'mediatable_id',
        'size',
        'extension',
        'collection',
        'tag',
        'width',
        'height',
        'user_id',
        'src',
        'mimes',
        'disk',
        'folder',
        'storage',
        'storage_data',
        'slug'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'storage_data' => \Illuminate\Database\Eloquent\Casts\AsArrayObject::class
    ];

    protected $appends = [
        'cloud',
        'url',
        'webp',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'generate_unique_uid'
            ]
        ];
    }

    public function getGenerateUniqueUidAttribute()
    {
		do{
		    $uid = Str::uuid();
		}while( Media::where('slug', $uid)->first() instanceof Media );
        return $uid;
    }
    
    public function getWebpAttribute()
    {
        return ( Storage::disk($this->disk)->exists($this->folder . pathinfo($this->src, PATHINFO_FILENAME) . '.webp') ? pathinfo($this->src, PATHINFO_FILENAME) . '.webp' : null );
    }

    public function getCloudAttribute()
    {
        return $this->storage_data;
    }

    public function getUrlAttribute()
    {
        $picture = $this;
        
        if( !collect(['public', 'local'])->contains($picture->disk) )
        {
            return null;
        }
        
        if( !Storage::disk($picture->disk)->exists($picture->folder . $picture->src) )
        {
            return [];
        }

        $newArray = [
            'src' => Storage::disk($picture->disk)->url($picture->folder . $picture->src),
        ];
        
        $thumbnails = $picture->thumbnails;

        if( !empty($thumbnails) )
        {
            $newArray['thumbnails'] = [];

            foreach($thumbnails as $pkey => $pick):
                if( Storage::disk($pick->disk)->exists($pick->folder . $pick->src) )
                {
                    $newArray['thumbnails'][$pick->tag] = (object) [
                        'src' => Storage::disk($pick->disk)->url($pick->folder . $pick->src),
                        'retina' => ( !empty($pick->retina) ? Storage::disk($pick->retina->disk)->url($pick->retina->folder . $pick->retina->src) : null )
                    ];
                }
            endforeach;
        }
        return $newArray;
    }
    public function getSizeHumanAttribute()
    {
        return \App\Libraries\Size\SizeHuman::parse($this->size);
    }

    public function getThumbnailsAttribute($value)
    {
        return collect(json_decode($value));
    }

    public function mediatable()
    {
        return $this->morphTo();
    }

    public function user()
    {
		return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Media', 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany('App\Models\Media', 'parent_id')->with('children');
    }

    public function childrens()
    {
        return $this->hasMany('App\Models\Media', 'parent_id')->with('childrens');
    }

    public function getRetinaAttribute()
    {
        return $this->children()->first();
    }
}

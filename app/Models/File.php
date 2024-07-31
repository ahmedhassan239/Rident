<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['name', 'folder_id', 'file_path'];

    public function folder() {
        return $this->belongsTo(Folder::class);
    }

    public function blogs()
    {
        return $this->morphedByMany(Blog::class, 'model', 'model_has_files');
    }

    public function aboutus()
    {
        return $this->morphedByMany(Aboutus::class, 'model', 'model_has_files');
    }

    public function categories()
    {
        return $this->morphedByMany(Category::class, 'model', 'model_has_files');
    }

    public function programs()
    {
        return $this->morphedByMany(Program::class, 'model', 'model_has_files');
    }
    public function teams()
    {
        return $this->morphedByMany(Team::class, 'model', 'model_has_files');
    }
    public function sliders()
    {
        return $this->morphedByMany(Slider::class, 'model', 'model_has_files');
    }

    public function courses()
    {
        return $this->morphedByMany(Course::class, 'model', 'model_has_files');
    }

    public function popups()
    {
        return $this->morphedByMany(Popup::class, 'model', 'model_has_files');
    }
    public function templates()
    {
        return $this->morphedByMany(Template::class, 'model', 'model_has_files');
    }
    public function globalseos()
    {
        return $this->morphedByMany(GlobalSeo::class, 'model', 'model_has_files');
    }
    public function galleries()
    {
        return $this->morphedByMany(Gallery::class, 'model', 'model_has_files');
    }

    public function getFolderFullPath(Folder $folder)
    {
        $path = $folder->name;
        $parent_id = $folder->parent_id;

        do {

            $parent_folder = Folder::where('id', $parent_id)->first();

            if ($parent_folder) {
                $path = $parent_folder->name . '/' . $path;
                $parent_id = $parent_folder->parent_id;
            } else {
                $parent_id = 0;
            }
        } while ($parent_id != 0 && $parent_id != null);

        return $path;
    }

    protected $appends = ['file_url'];


    public function getFileURLAttribute()
    {
        return asset('storage/' . $this->getFolderFullPath($this->folder) .'/'. $this->name);
    }
}

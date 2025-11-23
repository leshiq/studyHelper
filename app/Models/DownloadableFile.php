<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadableFile extends Model
{
    protected $fillable = [
        'title',
        'description',
        'filename',
        'file_path',
        'file_size',
        'mime_type',
        'max_downloads',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function fileAccesses()
    {
        return $this->hasMany(FileAccess::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'file_accesses')
            ->withPivot('expires_at')
            ->withTimestamps();
    }

    public function downloadLogs()
    {
        return $this->hasMany(DownloadLog::class);
    }

    public function courseLessons()
    {
        return $this->hasMany(CourseLesson::class);
    }
}

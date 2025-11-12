<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StudentInvitation extends Model
{
    protected $fillable = [
        'token',
        'email',
        'expires_at',
        'used_at',
        'created_by',
        'student_id',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    /**
     * Generate a unique invitation token
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Check if invitation is valid
     */
    public function isValid(): bool
    {
        return is_null($this->used_at) && $this->expires_at->isFuture();
    }

    /**
     * Mark invitation as used
     */
    public function markAsUsed(Student $student): void
    {
        $this->update([
            'used_at' => now(),
            'student_id' => $student->id,
        ]);
    }

    /**
     * Get the admin who created this invitation
     */
    public function creator()
    {
        return $this->belongsTo(Student::class, 'created_by');
    }

    /**
     * Get the student who used this invitation
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'employee_profiles';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'surname',
        'first_name',
        'middle_name',
        'suffix',
        'birth_date',
        'sex',
        'marital_status',
        'phone_number',
        'address',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_relationship',
        'health_card_path',
        'nbi_clearance_path',
        'police_clearance_path',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Get the user that owns the employee profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the employee's full name.
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name . ' ' . $this->surname;

        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }

        if ($this->suffix) {
            $name .= ' ' . $this->suffix;
        }

        return $name;
    }

    /**
     * Get formatted phone number with +63 prefix.
     */
    public function getFormattedPhoneNumberAttribute(): string
    {
        $number = ltrim($this->phone_number, '+63');
        return '+63 ' . substr($number, 0, 3) . ' ' . substr($number, 3, 3) . ' ' . substr($number, 6);
    }

    /**
     * Get age from birth date.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }

        return $this->birth_date->age;
    }
}

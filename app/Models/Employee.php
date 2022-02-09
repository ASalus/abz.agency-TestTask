<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';
    protected $fillable = ['name', 'position_id', 'employment_date', 'phone_number', 'email', 'salary', 'head_id', 'image'];

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'id')->withDefault(['position_id' => 'No position']);
    }

    // headRelation defines who is employee's supervisor//
    public function headRelation()
    {
        return $this->hasOne(Hierarchies::class, 'subordinate_id', 'id');
    }
    // subordinateRelation defines who is employee's subordinate//
    public function subordinateRelation()
    {
        return $this->hasOne(Hierarchies::class, 'head_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function($employee){
            if($employee->subordinateRelation!==null)
            {
                //deleting the supervisor relation
                $employee->subordinateRelation->delete();
            }
            if($employee->headRelation !== null)
            {
                //deleting the subordinate relation

                $employee->headRelation->delete();
            }
        });
    }
}
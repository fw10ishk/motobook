<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutiveBranch extends Model
{
    protected $table = 'executive_branches';

    protected $fillable = ['name'];

    public $timestamps = false;

    public static function search($query)
    {
        return self::where('name', 'LIKE', "%{$query}%")
            ->select('name')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'text' => $item->name,
                    'value' => $item->name
                ];
            });
    }

    public static function findOrCreateByName($name)
    {
        $executive_branch = self::where('name', $name)->first();
        if (!$executive_branch) {
            $executive_branch = self::create(['name' => $name]);
        }
        return $executive_branch;
    }
}

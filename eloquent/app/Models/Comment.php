<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @Property integer id
 * @Property string email
 * @Property string title
 * @Property string comment
 * @Property $timestamps
 */
class Comment extends Model
{
    protected $table = 'comments';
    protected $primaryKey = "id";
    protected $keyType = "integer";

    public $incrementing = true;
    public $timestamps = true; // default true

    protected $attributes = [
        "title" => "Default title",
        "comment" => "Default Comment"
    ];
}

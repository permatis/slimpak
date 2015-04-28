<?php namespace App\Models;

class Post extends \Model {
	protected $table = 'posts';
	protected $fillable = ['title', 'slug'];
}
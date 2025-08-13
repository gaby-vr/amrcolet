<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dotlogics\Grapesjs\App\Editor\Config;
use Dotlogics\Grapesjs\App\Contracts\Editable;
use Dotlogics\Grapesjs\App\Traits\EditableTrait;

class Page extends Model implements Editable
{
    use EditableTrait;
    
    protected $fillable = [
        'title',
        'slug',
        'main',         // 0: custom (can be deleted) / 1: default (cannot be deleted, ex: home, contact)
        'gjs_data',
    ];

    protected $table = 'pages';

    public function getAllClassesAttribute()
    {
        return $this->extractClasses($this->gjs_data['html']);
    }

    public function getEditorConfigAttribute()
    {
        return app(Config::class)->initialize($this);
    }

    public function getCssFileClassesAttribute()
    {
        $classes = '';
        foreach($this->editor_config->getStyles() as $style) {
            if($css = $this->extractClasses(file_get_contents($style), 'css', $this->all_classes)) {
                $classes .= $css;
            }
        }
        return $classes;
    }

    public function extractClasses(string $origin, $type = 'html', $classes = [])
    {
        if(in_array($type, ['html', 'css'])) {
            $result = [];
            preg_match_all(self::{$type.'_string_pattern'}($classes), $origin, $result);
            if(isset($result[1]) && is_array($result[1])) {
                if($type == 'html') {
                    // return unique classes
                    return array_values(array_unique(explode(' ', implode(' ', $result[1]))));
                } else {
                    return implode('', $result[0]);
                }
            }
        }
        return null;
    }

    public function html_string_pattern(array $classes = [])
    {
        return '/class="([^"]*)"/';
    }

    public function css_string_pattern(array $classes = [])
    {
        $particular = count($classes) ? '(?:\.'.addcslashes(implode('|.', $classes), '-/\\[]:.').')' : '';
        return '/'.$particular.'\s*[^{]*{([^{}}]*)}/';
    }
}

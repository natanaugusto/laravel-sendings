<?php

namespace App\Http\Requests\Traits;

use Exception;

/**
 * @method public function query($key = null, $default = null): string|array|null
 *
 * @see \Illuminate\Http\Request
 * @see \Illuminate\Http\Concerns\InteractsWithInput
 */
trait Sort
{

    /**
     * @return array order|direction Ex.: name|desc
     */
    public function sort(): array
    {
        $args = explode(
            '|',
            $this->query('sort', 'id|asc')
        );
        if (empty($args[1])) {
            $args[1] = 'asc';
        } elseif (!in_array($args[1], ['asc', 'desc'])) {
            throw new Exception("{$args[1]} is not an acceptable direction for ordering. Try asc or desc");
        }
        return $args;
    }
}

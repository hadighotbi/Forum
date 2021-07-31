<?php
function create($class, $attributes = [])
{
    return $class::factory()->create($attributes);
}


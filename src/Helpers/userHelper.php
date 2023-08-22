<?php

/* GR
* V1.1.0
* 19.05.02
*  
*/

function getUserMetadata($user, $key)
{

    $metadata = new \stdClass();

    foreach ($user->metadata as $meta) {
        $metadata->{$meta->key} = $meta->value;
    }

    if (property_exists($metadata, $key)) {
        return $metadata->{$key};
    } else {
        return null;
    }
}

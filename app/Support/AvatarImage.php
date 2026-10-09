<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarImage
{
    /**
     * Lado del cuadrado que se guarda. El sitio muestra el avatar a 64 px;
     * 256 cubre pantallas de alta densidad sin cargar la foto original.
     */
    public const SIZE = 256;

    /**
     * Recorta el centro de la foto en cuadrado —lo mismo que deja ver el
     * círculo—, la reduce y la guarda como JPEG en el disco público.
     */
    public static function store(UploadedFile $file): string
    {
        $source = imagecreatefromstring($file->get());
        $width = imagesx($source);
        $height = imagesy($source);
        $side = min($width, $height);

        $avatar = imagecreatetruecolor(self::SIZE, self::SIZE);
        imagecopyresampled(
            $avatar,
            $source,
            0,
            0,
            intdiv($width - $side, 2),
            intdiv($height - $side, 2),
            self::SIZE,
            self::SIZE,
            $side,
            $side,
        );

        ob_start();
        imagejpeg($avatar, null, 85);
        $jpeg = ob_get_clean();

        $path = 'avatars/'.Str::random(40).'.jpg';
        Storage::disk('public')->put($path, $jpeg);

        return $path;
    }
}

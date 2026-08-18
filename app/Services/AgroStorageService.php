<?php

namespace App\Services;

use App\Models\User;
use App\Models\Conversacion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AgroStorageService
{
    /**
     * Almacena archivos de Usuario (Perfil, Portada, Terrenos, Cultivos, etc.)
     * Estructura: users/{email_hash}/{tipo_archivo}/{categoria}/...
     *
     * @param UploadedFile $file
     * @param User $user
     * @param string $category 'perfil', 'portada', 'terreno', 'cultivo', 'labor'
     * @return array
     */
    public static function storeUserFile(UploadedFile $file, User $user, string $category): array
    {
        $userHash = md5($user->email);
        $mime = $file->getMimeType();

        // Clasificación Técnica por Tipo
        $mime = $file->getMimeType();
        $fileType = 'otros';
        if (str_contains($mime, 'image')) $fileType = 'img';
        elseif (str_contains($mime, 'video')) $fileType = 'video';
        elseif (str_contains($mime, 'pdf') || str_contains($mime, 'word') || str_contains($mime, 'excel')) $fileType = 'doc';

        // Ruta: users/{hash}/{tipo_archivo}/{category}/
        $path = "users/{$userHash}/{$fileType}/{$category}";

        return self::executeUpload($file, $path, $user->id);
    }

    /**
     * Almacena archivos de Chat (Individual, Privado o Grupal)
     * Estructura: chats/{tipo_chat}/{hash_identificador}/{tipo_archivo}/...
     */
    public static function storeChatFile(UploadedFile $file, User $sender, Conversacion $conv): array
    {
        $path = "chats";

        if ($conv->tipo_conversacion === 'individual') {
            // Chat con uno mismo: chats/individual/{tu_hash}
            $path .= "/individual/" . md5($sender->email);
        }
        elseif ($conv->tipo_conversacion === 'privada') {
            // Chat privado (2 personas): chats/private/{hash_alfabetico_de_correos}
            $participantes = $conv->participantes()->with('usuario')->get()->pluck('usuario.email')->toArray();
            sort($participantes); // Ordenar alfabéticamente para carpeta única
            $combinedEmails = implode('', $participantes);
            $path .= "/private/" . md5($combinedEmails);
        }
        else {
            // Chat grupal: chats/groups/{md5(organizacion_id)}
            $path .= "/groups/" . md5($conv->organizacion_id);
        }

        // Clasificación por tipo de archivo
        $mime = $file->getMimeType();
        if (str_contains($mime, 'image')) $path .= "/img";
        elseif (str_contains($mime, 'video')) $path .= "/video";
        elseif (str_contains($mime, 'pdf') || str_contains($mime, 'word') || str_contains($mime, 'excel')) $path .= "/doc";
        else $path .= "/otros";

        return self::executeUpload($file, $path, $sender->id);
    }

    /**
     * Ejecuta el proceso físico de subida y normalización de nombres
     */
    private static function executeUpload(UploadedFile $file, string $path, int $userId): array
    {
        // Nomenclatura AgroSys: dia-mes-año_horaminseg_nombre
        $now = Carbon::now()->format('d-m-Y_His');
        $originalName = $file->getClientOriginalName();
        $cleanName = str_replace(' ', '_', $originalName);
        $fileName = "{$now}_{$cleanName}";

        // Almacenamiento físico en disco público
        $fullPath = $file->storeAs($path, $fileName, 'public');

        return [
            'usuario_id' => $userId,
            'nombre_original' => $originalName,
            'nombre_archivo_unique' => $fileName,
            'ruta_completa' => $fullPath,
            'tipo_mime' => $file->getMimeType(),
            'tamano_bytes' => $file->getSize(),
            'url_publica' => Storage::url($fullPath)
        ];
    }
}

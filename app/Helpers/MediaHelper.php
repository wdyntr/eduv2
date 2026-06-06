<?php

if (!function_exists('youtubeEmbed')) {
    function youtubeEmbed(string $url): string
    {
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
        $id = $matches[1] ?? '';
        if (!$id) return $url;
        return "https://www.youtube.com/embed/{$id}?rel=0&modestbranding=1";
    }
}

if (!function_exists('driveEmbed')) {
    function driveEmbed(string $url, string $tipe = 'pdf'): string
    {
        // Coba pattern /file/d/{id}
        preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches);

        // Fallback: coba pattern ?id= atau &id=
        if (empty($matches[1])) {
            preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $url, $matches);
        }

        $id = $matches[1] ?? '';
        if (!$id) return $url;

        if ($tipe === 'ppt') {
            return "https://docs.google.com/presentation/d/{$id}/embed?start=false&loop=false&delayms=3000";
        }

        return "https://drive.google.com/file/d/{$id}/preview";
    }
}
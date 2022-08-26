<?php
return array(
    'ffmpeg' => [
        'binaries' => env('FFMPEG_BINARIES', 'C:\FFmpeg\bin\ffmpeg.exe'),
        'threads'  => 12,
    ],
    'ffprobe' => [
        'binaries' => env('FFPROBE_BINARIES', 'C:\FFmpeg\bin\ffprobe.exe'),
    ],
);
?>
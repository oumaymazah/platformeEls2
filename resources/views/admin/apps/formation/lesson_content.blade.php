<div class="lesson-description mb-4">
    {!! $lesson->description !!}
</div>

@if($lesson->link)
    <div class="video-container mb-4">
        @if(str_contains($lesson->link, 'youtube.com') || str_contains($lesson->link, 'youtu.be'))
            @php
                $videoId = null;
                if (str_contains($lesson->link, 'youtube.com/watch?v=')) {
                    $videoId = explode('v=', $lesson->link)[1];
                } elseif (str_contains($lesson->link, 'youtu.be/')) {
                    $videoId = explode('youtu.be/', $lesson->link)[1];
                }

                if (str_contains($videoId, '&')) {
                    $videoId = explode('&', $videoId)[0];
                }
            @endphp

            @if($videoId)
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}" title="{{ $lesson->title }}" allowfullscreen></iframe>
                </div>
            @else
                <div class="alert alert-warning">Le format du lien YouTube n'est pas valide.</div>
            @endif
        @elseif(str_contains($lesson->link, 'vimeo.com'))
            @php
                $videoId = null;
                if (preg_match('/vimeo\.com\/([0-9]+)/', $lesson->link, $matches)) {
                    $videoId = $matches[1];
                }
            @endphp

            @if($videoId)
                <div class="ratio ratio-16x9">
                    <iframe src="https://player.vimeo.com/video/{{ $videoId }}" title="{{ $lesson->title }}" allowfullscreen></iframe>
                </div>
            @else
                <div class="alert alert-warning">Le format du lien Vimeo n'est pas valide.</div>
            @endif
        @else
            <div class="embed-responsive embed-responsive-16by9">
                <video controls class="embed-responsive-item w-100">
                    <source src="{{ $lesson->link }}" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture de vidéos.
                </video>
            </div>
        @endif
    </div>
@endif

@if(count($lesson->files) > 0)
    <div class="lesson-files mt-4">
        <h4>Ressources de la leçon</h4>
        <div class="list-group">
            @foreach($lesson->files as $file)
                <a href="{{ asset('storage/' . $file->path) }}" class="list-group-item list-group-item-action d-flex align-items-center" target="_blank">
                    @php
                        $extension = pathinfo($file->path, PATHINFO_EXTENSION);
                        $icon = 'icon-file';

                        switch($extension) {
                            case 'pdf':
                                $icon = 'icon-file-pdf';
                                break;
                            case 'doc':
                            case 'docx':
                                $icon = 'icon-file-word';
                                break;
                            case 'xls':
                            case 'xlsx':
                                $icon = 'icon-file-excel';
                                break;
                            case 'ppt':
                            case 'pptx':
                                $icon = 'icon-file-powerpoint';
                                break;
                            case 'jpg':
                            case 'jpeg':
                            case 'png':
                            case 'gif':
                                $icon = 'icon-image';
                                break;
                            case 'zip':
                            case 'rar':
                                $icon = 'icon-file-archive';
                                break;
                        }
                    @endphp

                    <i class="{{ $icon }} me-2"></i>
                    <span>{{ $file->name }}</span>
                    <span class="ms-auto badge bg-secondary">{{ strtoupper($extension) }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif

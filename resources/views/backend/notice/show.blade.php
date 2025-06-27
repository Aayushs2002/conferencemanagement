<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h4 class="text-center mb-4 " style="background: white;">View Data</h4>
        <div class="row">
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Title</p>
                <span>{{ $notice->title }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Date</p>
                <span>{{ \Carbon\Carbon::parse($notice->date)->format('d M, Y') }}
                    {{ !empty($notice->end_date) ? ' - ' . \Carbon\Carbon::parse($notice->end_date)->format('d M, Y') : '' }}
                    </span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Image/Logo</p><span><a
                        href="{{ asset('storage/notice/image/' . $notice->image) }}" target="_blank"><img
                            src="{{ asset('storage/notice/image/' . $notice->image) }}" height="100" width="100"
                            alt="image"></a></span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Attachment</p><span>
                    <a href="{{ asset('storage/notice/attachment/' . $notice->attachment) }}" target="_blank">
                        @php
                            $fileName = explode('.', $notice->attachment);
                        @endphp
                        @if ($notice->attachment)
                            @if ($fileName[1] == 'pdf')
                                <img src="{{ asset('default-image/pdf.png') }}" height="50" alt="pdf">
                            @elseif ($fileName[1] == 'doc' || $fileName[1] == 'docx')
                                <img src="{{ asset('default-image/word.png') }}" height="50" alt="word file">
                            @else
                                <img src="{{ asset('storage/notice/attachment/' . $notice->attachment) }}"
                                    height="50" alt="image">
                            @endif
                        @endif
                    </a>
                </span>
            </div>
        </div>
        @if (!empty($notice->description))
            <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Description</p>
            <p>{!! $notice->description !!}</p>
        @endif
    </div>
</div>

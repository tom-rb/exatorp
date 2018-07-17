<a class="{{$classes or ''}} @active('inicio')" href="{{ route('member.home') }}">
    <span class="icon"><i class="fa fa-home"></i></span><span class="name">Início</span>
</a>


@if (! Auth::guest())
    @if (Auth::user()->isActive())
        <a class="{{$classes or ''}} @active('membros*')" href="{{ route('member.index') }}">
            <span class="icon"><i class="fa fa-users"></i></span><span class="name">Membros</span>
        </a>
        @can('show', \App\SelectionProcess\MemberApplication::class)
            <a class="{{$classes or ''}} @active('processo-seletivo*')" href="{{ route('selection-process.index') }}">
                <span class="icon"><i class="fa fa-handshake-o"></i></span><span class="name">Processo Seletivo</span>
            </a>
        @endcan
    @endif
@endif
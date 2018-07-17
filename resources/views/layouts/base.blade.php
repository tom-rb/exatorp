<!DOCTYPE html>
<html lang="pt-BR">
    @include('layouts.head')

    <body>
        <div id="exato-app">
            @yield('navbar')

            <div class="content-wrapper">
                @yield('main-content')
            </div>

            @include('layouts.footer')
        </div>

        @include('layouts.scripts')

        {{-- Calls a flash notification from session data --}}
        <script>
            @foreach(session('flash_notification', collect())->toArray() as $message)
                snackbar({ message: '{{ $message['message'] }}', type: 'is-{{ $message['level'] }}',
                           {{ $message['important'] ? 'indefinite: true' : 'actionText: null' }},
                           position: 'is-bottom-right'});
            @endforeach
        </script>

        @stack('scripts')
    </body>
</html>

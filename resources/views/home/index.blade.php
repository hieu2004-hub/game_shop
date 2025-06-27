<!DOCTYPE html>
<html lang="en">
    <head>
        @include('home.homecss')
    </head>
    <body>

    <header>
        @include('home.header')
    </header>

    <main>
        @include('home.slider')
        @include('home.mainproduct')
    </main>

    <header>
        @include('home.youtubeSlider')
    </header>


    <footer>
        @include('home.footer')
    </footer>

    <script src="{{ asset('./assets/js/script.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    </body>
</html>

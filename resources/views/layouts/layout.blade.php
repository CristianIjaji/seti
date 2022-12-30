<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    {{-- <title>{{ Auth::user()->title }}</title> --}}

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    {{-- <script src="{{ asset('js/ConectorPlugin.js') }}"></script> --}}

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" defer rel="stylesheet">

    <link rel="icon" href="/images/icon.png">
    <script type="application/javascript">
        window.updateThemeColor = () => {
            (() => {
                'use strict'

                const storedTheme = localStorage.getItem('theme')

                const getPreferredTheme = () => {
                    if (storedTheme) {
                        return storedTheme
                    }

                    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
                }

                const setTheme = function (theme) {
                    if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        document.documentElement.setAttribute('data-bs-theme', 'dark')
                    } else {
                        document.documentElement.setAttribute('data-bs-theme', theme)
                    }

                    let btn_remove_class = (theme === 'dark' ? 'btn-outline-primary' : 'btn-outline-light');
                    let btn_add_class = (theme === 'dark' ? 'btn-outline-light' : 'btn-outline-primary');

                    document.querySelectorAll(`.${btn_remove_class}`).forEach(element => {
                        element.classList.remove(btn_remove_class);
                        element.classList.add(btn_add_class);
                    });
                }

                setTheme(getPreferredTheme())

                const showActiveTheme = theme => {
                    const activeThemeIcon = document.querySelector('.theme-icon-active use')
                    const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
                    try {
                    const svgOfActiveBtn = btnToActive.querySelector('svg use').getAttribute('href')

                    document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
                        element.classList.remove('active')
                    })

                    btnToActive.classList.add('active')
                    if (activeThemeIcon !== null) {
                        activeThemeIcon.setAttribute('href', svgOfActiveBtn)
                    }
                    } catch {
                        // Using different theme names. Not just dark and light.
                    }
                }

                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                    if (storedTheme !== 'light' || storedTheme !== 'dark') {
                        setTheme(getPreferredTheme())
                    }
                })

                window.addEventListener('load', () => {
                    showActiveTheme(getPreferredTheme())

                    document.querySelectorAll('[data-bs-theme-value]').
                        forEach(toggle => {
                            toggle.addEventListener('click', () => {
                            const theme = toggle.getAttribute('data-bs-theme-value')
                            localStorage.setItem('theme', theme)
                            setTheme(theme)
                            showActiveTheme(theme)

                            // After changing the theme, hide offcanvas
                            const oc = document.querySelector('[aria-modal][class*=show][class*=offcanvas-]')
                            if (oc !== null) {
                                bootstrap.Offcanvas.getInstance(oc).hide()
                            }
                        })
                    })
                })
            })()
        }

        updateThemeColor();
    </script>
</head>
<body id="body-pd">
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="moon-stars-fill" viewBox="0 0 16 16">
            <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"></path>
            <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"></path>
        </symbol>
        <symbol id="sun-fill" viewBox="0 0 16 16">
            <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"></path>
        </symbol>
        <symbol id="check2" viewBox="0 0 16 16">
            <title>Check</title>
            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"></path>
        </symbol>
    </svg>
    <div id="app">
        @include('partials.loader')

        <main class="pt-4">
            @include('layouts.nav')
        </main>
    </div>
    <div id="sound"></div>
</body>
<script type="application/javascript">
    document.addEventListener("DOMContentLoaded", function(event) {
        let user_id = "<?php echo Auth::user()->id_usuario; ?>";
        listener(user_id);
    });

    updateThemeColor();

    // (() => {
    //     'use strict'

    //     const storedTheme = localStorage.getItem('theme')

    //     const getPreferredTheme = () => {
    //         if (storedTheme) {
    //             return storedTheme
    //         }

    //         return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    //     }

    //     const setTheme = function (theme) {
    //         if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    //             document.documentElement.setAttribute('data-bs-theme', 'dark')
    //         } else {
    //             document.documentElement.setAttribute('data-bs-theme', theme)
    //         }

    //         let btn_remove_class = (theme === 'dark' ? 'btn-outline-primary' : 'btn-outline-light');
    //         let btn_add_class = (theme === 'dark' ? 'btn-outline-light' : 'btn-outline-primary');

    //         let backgroundimage = (theme === 'dark' ? 'none' : 'linear-gradient(195deg, var(--bs-secondary) 0%, var(--bs-primary) 100%)');
    //         let backgroundColor = (theme === 'dark' ? '#2c2f32' : 'var(--bs-primary)');

    //         document.querySelectorAll(`.${btn_remove_class}`).forEach(element => {
    //             element.classList.remove(btn_remove_class);
    //             element.classList.add(btn_add_class);
    //         });

    //         let element = document.getElementById('nav-bar');

    //         element.style.backgroundImage = backgroundimage;
    //         element.style.backgroundColor = backgroundColor;
    //     }

    //     setTheme(getPreferredTheme())

    //     const showActiveTheme = theme => {
    //         const activeThemeIcon = document.querySelector('.theme-icon-active use')
    //         const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
    //         try {
    //         const svgOfActiveBtn = btnToActive.querySelector('svg use').getAttribute('href')

    //         document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
    //             element.classList.remove('active')
    //         })

    //         btnToActive.classList.add('active')
    //         if (activeThemeIcon !== null) {
    //             activeThemeIcon.setAttribute('href', svgOfActiveBtn)
    //         }
    //         } catch {
    //             // Using different theme names. Not just dark and light.
    //         }
    //     }

    //     window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    //         if (storedTheme !== 'light' || storedTheme !== 'dark') {
    //             setTheme(getPreferredTheme())
    //         }
    //     })

    //     window.addEventListener('load', () => {
    //         showActiveTheme(getPreferredTheme())

    //         document.querySelectorAll('[data-bs-theme-value]').
    //             forEach(toggle => {
    //                 toggle.addEventListener('click', () => {
    //                 const theme = toggle.getAttribute('data-bs-theme-value')
    //                 localStorage.setItem('theme', theme)
    //                 setTheme(theme)
    //                 showActiveTheme(theme)

    //                 // After changing the theme, hide offcanvas
    //                 const oc = document.querySelector('[aria-modal][class*=show][class*=offcanvas-]')
    //                 if (oc !== null) {
    //                     bootstrap.Offcanvas.getInstance(oc).hide()
    //                 }
    //             })
    //         })
    //     })
    // })()

    // let theme = localStorage.getItem('theme');
    // let backgroundimage = (theme === 'dark' ? 'none' : 'linear-gradient(195deg, var(--bs-secondary) 0%, var(--bs-primary) 100%)');
    // let backgroundColor = (theme === 'dark' ? '#2c2f32' : 'var(--bs-primary)');

    // let element = document.getElementById('nav-bar');

    // element.style.backgroundImage = backgroundimage;
    // element.style.backgroundColor = backgroundColor;
</script>
</html>
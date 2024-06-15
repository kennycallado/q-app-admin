import { themeSwitcher } from '/public/assets/@siemens/ix/dist/index.js';

// (async () => {
//     await window.customElements.whenDefined('ix-dropdown');

//     // const darkSwitch = document.querySelector('ix-dropdown-item[icon="moon"]');
//     // const lightSwitch = document.querySelector('ix-dropdown-item[icon="sun"]');

//     // darkSwitch?.addEventListener('click', () => {
//     //     themeSwitcher.setTheme('theme-classic-dark');
//     //     localStorage.setItem('theme', 'dark');

//     //     document.body.classList.remove('bg-dark', 'bg-light');
//     //     document.body.classList.add('bg-dark');
//     // });

//     // lightSwitch?.addEventListener('click', () => {
//     //     themeSwitcher.setTheme('theme-classic-light');
//     //     localStorage.setItem('theme', 'light');

//     //     document.body.classList.remove('bg-dark', 'bg-light');
//     //     document.body.classList.add('bg-light');
//     // });

// })();

(() => {
    'use strict'
    // const themes = ['theme-classic-light', 'theme-classic-dark'];

    const getStoredTheme = () => localStorage.getItem('theme')
    // const setStoredTheme = (theme: string) => localStorage.setItem('theme', theme)

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        const storedTheme = getStoredTheme()
        if (storedTheme !== 'light' && storedTheme !== 'dark') {
            setTheme(getPreferredTheme())
        }
    })

    const getPreferredTheme = () => {
        const storedTheme = getStoredTheme()
        if (storedTheme) {
            return storedTheme
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }

    const setTheme = (theme) => {
        if (theme === 'auto') {
            let preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
            themeSwitcher.setTheme(preferredTheme === 'dark' ? 'theme-classic-dark' : 'theme-classic-light')
            
            document.body.classList.remove('bg-dark', 'bg-light');
            document.body.classList.add(`bg-${preferredTheme}`);
        } else {
            themeSwitcher.setTheme(theme === 'dark' ? 'theme-classic-dark' : 'theme-classic-light')
            
            document.body.classList.remove('bg-dark', 'bg-light');
            document.body.classList.add(`bg-${theme}`);
        }
    }

    setTheme(getPreferredTheme())

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        let preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
        setTheme(preferredTheme)
    })
})(); // <-- semi-colon is important here

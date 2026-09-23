/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig',
    './src/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        // Dispatch / Upsun design system
        ink: {
          DEFAULT: '#0C0F10', // near-black surfaces (header)
          950: '#09090B',     // deepest text / footer
          900: '#0F1115',
          800: '#16181D',
        },
        night: {
          // deep indigo-violet used in the hero gradient
          DEFAULT: '#100D29',
          deep: '#0B0A1F',
        },
        brand: {
          blue: '#0050FF',  // primary accent
          lime: '#E6FB66',  // signature highlight
          green: '#22C55E',
          violet: '#6D5BFF',
          pink: '#FF4D8D',
        },
        slatey: {
          50: '#F7F8FA',
          100: '#EFF1F4',
          200: '#E3E6EB',
          300: '#CBD0D8',
          400: '#9AA2AF',
          500: '#6B7280',
          600: '#4B515C',
          700: '#33373F',
        },
      },
      fontFamily: {
        display: ['"Space Grotesk"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        mono: ['"JetBrains Mono"', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'monospace'],
      },
      maxWidth: {
        prose: '46rem',
      },
      backgroundImage: {
        'hero-glow':
          'radial-gradient(70% 120% at 50% 0%, #211a4d 0%, #14102f 38%, #0c0a22 64%, #0b0b0e 100%)',
      },
      boxShadow: {
        card: '0 1px 2px rgba(9,9,11,0.04), 0 8px 24px -12px rgba(9,9,11,0.12)',
        'card-hover': '0 2px 4px rgba(9,9,11,0.06), 0 16px 40px -16px rgba(9,9,11,0.22)',
      },
    },
  },
  plugins: [require('@tailwindcss/typography')],
};

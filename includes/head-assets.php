<?php
// Sécurité : éviter l'accès direct si besoin
?>

<link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/theme.css">

<script>
    tailwind = {
        config: {
            theme: {
                extend: {
                    colors: {
                        primary: "rgb(var(--color-primary) / <alpha-value>)",
                        "primary-dark": "rgb(var(--color-primary-dark) / <alpha-value>)",
                        "on-primary": "rgb(var(--color-on-primary) / <alpha-value>)",

                        secondary: "rgb(var(--color-secondary) / <alpha-value>)",
                        danger: "rgb(var(--color-danger) / <alpha-value>)",
                        success: "rgb(var(--color-success) / <alpha-value>)",
                        warning: "rgb(var(--color-warning) / <alpha-value>)",

                        background: "rgb(var(--color-background) / <alpha-value>)",
                        surface: "rgb(var(--color-surface) / <alpha-value>)",
                        border: "rgb(var(--color-border) / <alpha-value>)",

                        text: "rgb(var(--color-text) / <alpha-value>)",
                        muted: "rgb(var(--color-muted) / <alpha-value>)"
                    }
                }
            }
        }
    };
</script>

<script src="https://cdn.tailwindcss.com"></script>
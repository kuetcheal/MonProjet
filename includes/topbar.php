<?php
$baseUrl = $baseUrl ?? ((strpos($_SERVER['SCRIPT_NAME'], '/MonProjet/') !== false) ? '/MonProjet/' : '/');
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="notranslate fixed top-0 left-0 right-0 z-[1600] w-full h-[48px] bg-[#06182c] text-white hidden max-[992px]:flex items-center">
    <div class="w-full px-5 flex items-center justify-end gap-3">
        <button 
            type="button"  
            id="gvMobileLangFr" 
            class="gv-mobile-lang-btn is-active text-white text-base font-bold underline underline-offset-4 cursor-pointer bg-transparent border-0 focus:outline-none"
        >
            FR
        </button>

        <span class="text-white/70">|</span>

        <button 
            type="button" 
            id="gvMobileLangEn" 
            class="gv-mobile-lang-btn text-white text-base font-bold cursor-pointer bg-transparent border-0 focus:outline-none"
        >
            EN
        </button>
    </div>
</div>

<style>
    @media (max-width: 992px) {
        body {
            padding-top: 48px;
        }

        .gv-mobile-lang-btn.is-active {
            font-weight: 800 !important;
            text-decoration: underline !important;
            text-underline-offset: 6px !important;
        }

        .gv-mobile-lang-btn:not(.is-active) {
            font-weight: 500 !important;
            text-decoration: none !important;
        }
    }
</style>
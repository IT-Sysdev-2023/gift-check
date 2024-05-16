<script setup lang="ts">

import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div>

        <div class="flex justify-center items-center" style="height: 100vh;">
            <div class="card bg-white" style="width: 1000px; border-radius: 15px;">
                <div class="flex ">
                    <div class="w-1/2 flex justify-center items-center " style="height: 500px;" id="bg-image">
                    </div>

                    <div class="flex flex-col w-1/2 ">
                        <div class="mx-2 flex h-full sm:justify-center items-center pt-6 sm:pt-0">
                            <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden">
                                <slot />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="text-end"><small class="text-white">GC&copy2024 version: 2.0</small></div>
    </div>
    <div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
    </div>


</template>





<style>
#bg-image {
    background-image: url('http://172.16.161.48/gc/assets/images/gcicon.png');
    background-position: center;
    background-size: cover;
    background-repeat: no-repeat;
}

body {
    margin: auto;
    font-family: -apple-system, BlinkMacSystemFont, sans-serif;
    overflow: auto;
    background: linear-gradient(315deg, rgba(101, 0, 94, 1) 3%, rgba(60, 132, 206, 1) 38%, rgba(48, 238, 226, 1) 68%, rgba(255, 25, 25, 1) 98%);
    animation: gradient 15s ease infinite;
    background-size: 400% 400%;
    background-attachment: fixed;
}

@keyframes gradient {
    0% {
        background-position: 0% 0%;
    }

    50% {
        background-position: 100% 100%;
    }

    100% {
        background-position: 0% 0%;
    }
}

.wave {
    background: rgb(255 255 255 / 25%);
    border-radius: 1000% 1000% 0 0;
    position: fixed;
    width: 200%;
    height: 12em;
    animation: wave 10s -3s linear infinite;
    transform: translate3d(0, 0, 0);
    opacity: 0.8;
    bottom: 0;
    left: 0;
    z-index: -1;
}

.wave:nth-of-type(2) {
    bottom: -1.25em;
    animation: wave 18s linear reverse infinite;
    opacity: 0.8;
}

.wave:nth-of-type(3) {
    bottom: -2.5em;
    animation: wave 20s -1s reverse infinite;
    opacity: 0.9;
}

@keyframes wave {
    2% {
        transform: translateX(1);
    }

    25% {
        transform: translateX(-25%);
    }

    50% {
        transform: translateX(-50%);
    }

    75% {
        transform: translateX(-25%);
    }

    100% {
        transform: translateX(1);
    }
}
</style>

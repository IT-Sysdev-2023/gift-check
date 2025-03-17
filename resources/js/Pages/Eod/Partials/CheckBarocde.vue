<script setup lang="ts">
import { onMounted, ref, nextTick } from "vue";
import lottie from "lottie-web";

const container = ref<HTMLElement | null>(null);

onMounted(() => {
    nextTick(() => {
        if (!container.value) {
            console.error("Lottie container is not available");
            return;
        }

        fetch("/images/searching.json")
            .then((response) => response.json())
            .then((animationData) => {
                const animation = lottie.loadAnimation({
                    container: container.value!,
                    renderer: "svg",
                    loop: true,
                    autoplay: true,
                    animationData,
                });

                // Ensure previous instance is destroyed when reloading
                return () => animation.destroy();
            })
            .catch((error) => console.error("Error loading animation:", error));
    });
});
</script>

<template>
    <div ref="container" class="lottie-container"></div>
</template>

<style scoped>
.lottie-container {
    width: 100%;
    height: 300px;
}
</style>

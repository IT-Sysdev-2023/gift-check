<template>
    <Vue3Lottie class="lottie" :animationData="animationData" :loop="true" :autoplay="true" />
    <a-result :status="false" :title="title" sub-title="Please check and modify the following information.">
        <template #extra>
            <a :href="download">
                <a-button key="buy" size="large" type="primary">DownLoad Result</a-button>
            </a>
            <a-button key="buy" size="large" @click="() => router.get(route(routeTo, id))">Return Home</a-button>

        </template>

        <div class="desc">
            <p style="font-size: 16px">
                <strong>Result has been successfully generated:</strong>
            </p>
            <div>
                <iframe :src="'data:application/pdf;base64,' + stream" width="100%" height="600px"></iframe>
            </div>

        </div>
    </a-result>
</template>

<script lang="ts" setup>
import { Vue3Lottie } from 'vue3-lottie';
import animationData from '../../../../public/json/success.json'; // Import your JSON animation
import { router } from '@inertiajs/core';

defineProps<{
    title: string,
    routeTo: string,
    download: string,
    stream: string,
    id: number,
}>();
</script>


<style scoped>
.desc p {
    margin-bottom: 1em;
}

.lottie {
    width: 100%;
    height: 400px;
}
</style>

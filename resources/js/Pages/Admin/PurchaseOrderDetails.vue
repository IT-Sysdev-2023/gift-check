<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8" v-for="item in record">
                <a-card  class="card"  @click="setup(item)">
                  <div class="flex justify-between">
                    <div style="cursor: pointer;">
                        <span  style="cursor: pointer; font-weight: bold;" > <FileTextOutlined /> &nbsp; {{ item }}</span>
                    </div>
                    <div>
                        <ArrowRightOutlined  style="cursor: pointer;"/>
                    </div>
                  </div>
                </a-card>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script setup>
import { router } from '@inertiajs/core';

defineProps({
    record: Object,
});

const setup = (name) => {
    router.get(route('admin.setup', name));
}
</script>

<style scoped>
.card {
    cursor: pointer;
}
.card:hover{
    color: #0D92F4;
}
</style>

<template>
    <a-card v-if="budget">
        <div class="flex justify-between">
            <div>
                <a-space>
                    <a-avatar :size="30">
                        <template #icon>
                            <UserOutlined />
                        </template>
                    </a-avatar>
                </a-space>
            </div>
            <div class="ml-2 mr-2">
                {{ budget?.br_remarks }} - {{ budget?.datereq }}
            </div>
            <div class="animate-pulse">
                <a-button @click="approve(budget?.br_id)">
                    <template #icon>
                        <LikeOutlined />
                    </template>
                </a-button>
            </div>
        </div>
    </a-card>

    <a-modal v-model:open="open" title="Basic Modal" style="width: 60%;">
        <iframe :src="stream" frameborder="2" style="width: 100%; height: 400px;" ></iframe>
    </a-modal>

</template>

<script setup>
import { router } from '@inertiajs/core';
import { ref } from 'vue';

const props = defineProps({
    budget: Object
});
const stream = ref();
const open = ref(false);

const approve = (id) => {
    router.put(route('iad.approve', id), {
    }, {
        onSuccess: (res) => {
            stream.value = `data:application/pdf;base64,${res.props.flash.stream}`;
            open.value = true;
        }
    })
}
</script>

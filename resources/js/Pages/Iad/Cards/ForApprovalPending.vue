<template>
    <a-card v-if="budget" style="border: 1px solid #73EC8B;">
        <div class="flex justify-between">
            <div class="ml-2 mr-2 mt-1">
                <span >
                    Requested Budget:
                </span> &nbsp; <span class="font-bold">₱{{ budget?.br_request.toLocaleString() }}</span>
            </div>
            <div class="animate-pulse">
                <a-button @click="openDrawer(budget.br_id)">
                    <template #icon>
                        <LikeOutlined />
                    </template>
                    Approve
                </a-button>
            </div>
        </div>
    </a-card>

    <a-modal v-model:open="open" title="Basic Modal" style="width: 60%;">
        <iframe :src="stream" frameborder="2" style="width: 100%; height: 400px;"></iframe>
    </a-modal>

    <a-drawer :width="500" title="Approve Budget Request" :placement="'right'" :open="drawer" @close="onClose">
        <a-descriptions layout="horizontal" size="small" bordered>
            <a-descriptions-item style="width: 50%;" label="Budget Requested"> ₱ {{ data.br_request.toLocaleString()
                }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions layout="horizontal" size="small" bordered>
            <a-descriptions-item style="width: 50%;" label="Requested At">{{ data.requested }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions layout="horizontal" size="small" bordered>
            <a-descriptions-item style="width: 50%;" label="Remarks">{{ data.br_remarks }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions layout="horizontal" size="small" bordered>
            <a-descriptions-item style="width: 50%;" label="Requested By">{{ data.reqby }}</a-descriptions-item>
        </a-descriptions>
        <a-button class="mt-4" block @click="approve(budget.br_id)">
            <LikeOutlined />Approve Budget Request
        </a-button>
    </a-drawer>

</template>

<script setup>
import { router } from '@inertiajs/core';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    budget: Object
});
const stream = ref();
const open = ref(false);
const data = ref({});
const drawer = ref(false);

const approve = (id) => {
    router.put(route('iad.approve', id), {
    }, {
        onSuccess: (res) => {
            notification[res.props.flash.status]({
                message: res.props.flash.title,
                description: res.props.flash.msg,
            });
            stream.value = `data:application/pdf;base64,${res.props.flash.stream}`;
            open.value = true;
            drawer.value = false;
        },
        preserveState: true,
    })
}

const openDrawer = (id) => {
    axios.get(route('iad.details', id)).then(res => {
        data.value = res.data.record;
        drawer.value = true;
    })
}

const onClose = () => {
    drawer.value = false;
}

</script>

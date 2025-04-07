<template>
    <a-modal :footer="null" v-model:open="open" title="Basic Modal" style="width: 60%;">
        <iframe :src="stream" frameborder="2" style="width: 100%; height: 400px;"></iframe>
    </a-modal>

    <a-descriptions class="mt-5" layout="horizontal" size="small" bordered>
        <a-descriptions-item style="width: 50%;" label="Br No">{{ budget.br_no
        }}</a-descriptions-item>
    </a-descriptions>

    <a-descriptions layout="horizontal" size="small" bordered>
        <a-descriptions-item style="width: 50%;" label="Budget Requested"> ₱ {{ budget.br_request
        }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions layout="horizontal" size="small" bordered>
        <a-descriptions-item style="width: 50%;" label="Requested At">{{ budget.datereq }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions layout="horizontal" size="small" bordered>
        <a-descriptions-item style="width: 50%;" label="Remarks">{{ budget.br_remarks }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions layout="horizontal" size="small" bordered>
        <a-descriptions-item style="width: 50%;" label="Requested By">{{ budget.reqby }}</a-descriptions-item>
    </a-descriptions>
    <a-card size="small" title="Documents" class="mt-1 text-center">
        <a-image class="rounded-xl" :src="'/storage/budgetRequestScanCopy/' + budget.br_file_docno" />
    </a-card>
    <a-button class="mt-4" size="large" type="primary" block @click="approve(budget)">
        <LikeOutlined />Review Budget Request
    </a-button>

</template>

<script setup>
import { router } from '@inertiajs/core';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';

const emit = defineEmits(['close']);

const props = defineProps({
    budget: Object
});
const stream = ref();
const open = ref(false);

const approve = (data) => {
    router.put(route('iad.approve', data.br_id), {
        data
    }, {
        onSuccess: (res) => {
            notification[res.props.flash.status]({
                message: res.props.flash.title,
                description: res.props.flash.msg,
            });
            stream.value = `data:application/pdf;base64,${res.props.flash.stream}`;
            open.value = true;
            emit('close');
        },
        preserveState: true,
    })
}


</script>
